<?php

use App\Models\User;
use App\Models\Action;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Facture;
use App\Models\Magasin;
use App\Models\Produit;
use Mockery\Matcher\Not;
use App\Models\ActionUser;
use App\Models\AgenceUser;
use App\Models\Fournisseur;
use App\Models\Maintenance;
use App\Models\groupeAction;
use App\Models\TotalFacture;
use App\Models\StockHistories;
use App\Models\CategorieProduit;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationApprovs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// Fonction qui retourne l'ID de l'agence utilisé par l'utilisateur lors de sa connexion
if (!function_exists('getIdAgenceByUser')) {
    function getIdAgenceByUser()
    {
        if (Session::get('site_id') != null) {
            return Session::get('site_id');
        } else {
            // Auth::logout();
            abort(to_route('verif-access'));
        }
    }
}

// Fonction qui retourne le nom de l'agence par son ID
if (!function_exists('getAgenceById')) {
    function getAgenceById()
    {
        $idSite = getIdAgenceByUser();
        if (is_array($idSite)) {
            return 'Siège';
        } else {

            if ($idSite == null) {
                Auth::logout();
            } else {
                return Agence::find($idSite)->NomAgence;
            }
        }
    }
}

// Voir si une action est affectée ou non a un utilisateur
if (!function_exists('isActionAffectedToGroupe')) {
    function isActionAffectedToGroupe($actionId, $groupeId)
    {
        $groupeAction = groupeAction::where('groupe_id', $groupeId)->where('action_id', $actionId)->first();

        if ($groupeAction !== null) {
            return 'checked';
        } else {
            return '';
        }
    }
}
// Voir si une action est affectée ou non a un utilisateur
if (!function_exists('isActionAffectedToUser')) {
    function isActionAffectedToUser($actionId, $userId, $agenceId)
    {
        $userAction = ActionUser::where('user_id', $userId)->where('action_id', $actionId)->where('agence_id', $agenceId)->first();

        // dd($userAction);

        if ($userAction !== null) {
            return 'checked';
        } else {
            return '';
        }
    }
}

// Voir si l'ensemble des actions d'un module est affecté a un groupe ou non
if (!function_exists('isAllActionAffectedToGroupe')) {
    function isAllActionAffectedToGroupe($module_id, $groupe_id)
    {
        $actionIds = Action::where('module_id', $module_id)->pluck('id')->toArray();

        if ($module_id == null) {
            return '';
        } else {
            foreach ($actionIds as $actionId) {
                $checked = groupeAction::where('groupe_id', $groupe_id)->where('action_id', $actionId)->first();

                if (!$checked) {
                    return '';
                }
            }
            return 'checked';
        }
    }
}

// Voir si l'ensemble des actions d'un module est affecté a un utilisateur ou non
if (!function_exists('isAllActionAffectedToUser')) {
    function isAllActionAffectedToUser($module_id, $user_id, $agence_id)
    {
        $actionIds = Action::where('module_id', $module_id)->pluck('id')->toArray();

        if ($module_id == null) {
            return '';
        } else {
            foreach ($actionIds as $actionId) {
                $checked = ActionUser::where('user_id', $user_id)->where('action_id', $actionId)->where('agence_id', $agence_id)->first();

                if (!$checked) {
                    return '';
                }
            }
            return 'checked';
        }
    }
}

// Attribution de droit
if (!function_exists('authorizeAccess')) {
    function authorizeAccess($action_id)
    {
        $action = Action::where('id', $action_id)->first();

        if($action && $action->module_id == 9 && PrefixeReference::first()->caisse == 0){
            return false;
        } elseif ($action && $action->module_id == 8 && PrefixeReference::first()->emballage == 0) {
            return false;
        } else {
            return ActionUser::where('user_id', auth()->user()->id)->where('agence_id', getIdAgenceByUser())->where('action_id', $action_id)->exists();
        }
    }
}

// Maintenance ou non du site
if (!function_exists('inMaintenance')) {

    function inMaintenance()
    {
        if (Auth::check() && !in_array(Auth::id(), [1, 3])) {
            if (Maintenance::first() && Maintenance::first()->maintenance) {
                header('Location: /site-en-maintenance');
                exit;
            }
        }
    }
}

if (!function_exists('notInMaintenance')) {

    function notInMaintenance()
    {
        if (!Maintenance::first()->maintenance) {
            if (Auth::check()) {
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();
            }
            header('Location: /login');
            exit;
        }
    }
}



// Verifie si un  utilisateur a déjà droit a une action via un autre groupe au moment de lui affecter un nouuveau groupe
if (!function_exists('actionByOtherGroupe')) {

    function actionByOtherGroupe($groupe_id, $action_id)
    {
        if (count($groupe_id) == 0) {
            return true;
        } elseif (count($groupe_id) == 1) {
            $groupeAction = GroupeAction::where('groupe_id', $groupe_id)->where('action_id', $action_id)->exists();
            if ($groupeAction) {
                return false;
            } else {
                return true;
            }
        } else {
            $groupeAction = GroupeAction::whereIn('groupe_id', $groupe_id)->where('action_id', $action_id)->exists();
            if ($groupeAction) {
                return false;
            } else {
                return true;
            }
        }
    }
}

// Verifie si l'utilisateur connecté a ou non le droit d'attribuer un groupe a un utilisateur
if (!function_exists('attributeAccessGroupeInFunctionAuthUser')) {
    function attributeAccessGroupeInFunctionAuthUser($groupe_id)
    {
        if (in_array(auth()->user()->id, [1, 3])) {
            return true;
        } elseif (auth()->user()->id == 2) {
            if ($groupe_id == 1) {
                return false;
            } else {
                return true;
            }
        } else {
            if (in_array($groupe_id, [1, 2])) {
                return false;
            } else {
                return true;
            }
        }
    }
}

// Renvoie une erreur 403 quand l'utilisateur essaye d'acceder a travers l'url a une page des acces de droit a un groupe auquel il n'a pas autorisation
if (!function_exists('AccessPageGroupeInFunctionAuthUser')) {
    function AccessPageGroupeInFunctionAuthUser($groupe_id, $inputGroupeId, $module_id)
    {
        if ($inputGroupeId) {
            // On recupere les ids des modules
            $module_ids = Module::pluck('id')->toArray();

            if (!in_array($module_id, $module_ids)) {
                abort('403');
            }

            $groupes = Groupe::pluck('id')->toArray();
            if (!in_array($inputGroupeId, $groupes)) {
                abort('403', 'Action non autorisée');
            }
            if (in_array(auth()->user()->id, [1, 3])) {
                return true;
            } elseif (auth()->user()->id == 2) {
                if ($groupe_id == 1) {
                    abort('403', 'Action Non Autorisée !');
                } else {
                    return true;
                }
            } else {
                if (in_array($groupe_id, [1, 2])) {
                    abort('403', 'Action Non Autorisée !');
                } else {
                    return true;
                }
            }
        }
    }
}

// Verifie si l'utilisateur connecté a ou non le droit d'attribuer un acces a un utilisateur specifique
if (!function_exists('attributeAccessUserInFunctionAuthUser')) {
    function attributeAccessUserInFunctionAuthUser($user_id, $agence_id)
    {
        // On recupere les agences de l'utilisateurs choisi auquel on veut affecter l'acces
        $agencesUser = AgenceUser::where('user_id', $user_id)->pluck('agence_id')->toArray();

        if (in_array(auth()->user()->id, [1, 3])) {
            if (in_array($agence_id, $agencesUser)) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => "L'utilisateur n'est pas affecté a cette agence"];
            }
        } elseif (auth()->user()->id == 2) {
            if (!in_array($user_id, [1, 3])) {
                if (in_array($agence_id, $agencesUser)) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'message' => "L'utilisateur n'est pas affecté a cette agence"];
                }
            } else {
                return ['success' => false, "message" => "Attention ! Vous essayez de faire une action dangereuse !"];
            }
        } else {
            if (!in_array($user_id, [1, 2, 3])) {
                if (in_array($agence_id, $agencesUser)) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'message' => "L'utilisateur n'est pas affecté a cette agence"];
                }
            } else {
                return ['success' => false, "message" => "Attention ! Vous essayez de faire une action dangereuse !"];
            }
        }
    }
}

// Renvoie une erreur 403 quand l'utilisateur essaye d'acceder a travers l'url a une page des acces de droit a un groupe auquel il n'a pas autorisation
if (!function_exists('AccessPageUserInFunctionAuthUser')) {
    function AccessPageUserInFunctionAuthUser($user_id, $agence_id, $module_id)
    {
        if ($user_id && $agence_id && $module_id) {

            // On recupere les ids des modules
            $module_ids = Module::pluck('id')->toArray();

            // On recupere les agences de l'utilisateurs choisi auquel on veut affecter l'acces
            $agencesUser = AgenceUser::where('user_id', $user_id)->pluck('agence_id')->toArray();

            if (!in_array($module_id, $module_ids)) {
                abort('403');
            }

            if (!in_array($agence_id, $agencesUser)) {
                abort('403');
            }

            if (in_array(auth()->user()->id, [1, 3])) {
                return ['success' => true];
            } elseif (auth()->user()->id == 2) {
                if (!in_array($user_id, [1, 3])) {
                    return ['success' => true];
                } else {
                    abort('403', 'Action Non Autorisée !');
                }
            } else {
                if (!in_array($user_id, [1, 2, 3, auth()->user()->id])) {
                    return ['success' => true];
                } else {
                    abort('403', 'Action Non Autorisée !');
                }
            }
        }
    }
}

// Retourne l'id agence pour le filtre
if (!function_exists('getIdAgenceFilter')) {
    function getIdAgenceFilter($filter)
    {
        if ($filter != null) {

            $idsAgence_User = auth()->user()->agences->pluck('agence_id')->toArray();

            if ($filter === '0') {
                return $idFilter = $idsAgence_User;
            } else {
                if (in_array($filter, $idsAgence_User)) {
                    return $idFilter = $filter;
                } else {
                    abort('403', 'ACTION NON AUTORISEE');
                }
            }
        } else {
            $idFilter = getIdAgenceByUser();
        }
        return $idFilter;
    }
}

// Affiche la reference de la facture tout en verifiant si c'est une facture d'avoir ou non
if (!function_exists('refFacture')) {
    function refFacture($justificatif)
    {

        $operation = StockHistories::where('Justificatif', $justificatif)->first()->operation;

        if ($operation != 'SORTIE' || preg_match('/SP/', $justificatif)) {
            return $justificatif;
        } else {
            $id_factureOrigine = Facture::where('Reference_facture', $justificatif)->first()->id;
            $refFactureOrigine = Facture::where('idFacture_originale', $id_factureOrigine)->first();
            if (($id_factureOrigine != null) && ($refFactureOrigine != null)) {
                return $refFactureOrigine->Reference_facture;
            } else {
                return $justificatif;
            }
        }
    }
}

// Affiche une erreur 403 lorqu'on essaye d'accéder a travers l'url aux pages de gestion d'emballage
if (!function_exists('accessEmballage')) {
    function accessEmballage()
    {
        if (PrefixeReference::first()->emballage == 0) {
            abort('403');
        }
    }
}

if(!function_exists('verifAccessAgenceUser')){
    function verifAccessAgenceUser(){

        $userAgences = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();

        if(session('site_id') == null || !in_array(session('site_id'), $userAgences)){
            Auth::logout();
            session()->invalidate();
        }
    }
}

if (!function_exists('agences')) {
    function agences()
    {
        return Agence::all();
    }
}

if (!function_exists('sessionsBdd')) {
    function sessionsBdd()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $currentSessionId = session()->getId();
            // Vérifie s'il y a d'autres sessions pour cet utilisateur
            $otherSessions = DB::table('sessions')
                ->where('user_id', $userId)
                ->where('id', '<>', $currentSessionId)
                ->get();

            return  $otherSessions;
        }
    }
}

if (!function_exists('categories')) {
    function categories()
    {
        return CategorieProduit::all();
    }
}

if (!function_exists('produits')) {
    function produits()
    {
        return Produit::all();
    }
}

if (!function_exists('produits_par_categorie')) {
    function produits_par_categorie($id_categorie)
    {
        return Produit::where('Id_Categorie', $id_categorie)->get();
    }
}

if (!function_exists('clients')) {
    function clients()
    {
        return Client::all();
    }
}

if (!function_exists('users')) {
    function users()
    {
       return User::whereNotIn('id', [1, 2])->get();
        // return User::all();
    }
}

if (!function_exists('notification_approvs')) {

    function notification_approvs()
    {

        $site_id = session()->get('site_id');

        $notification_approvs = DB::table('notification_approvs')
        ->join('agences', 'notification_approvs.Id_Agence_Source', '=', 'agences.id')
        ->join('agences as agences2', 'notification_approvs.Id_Agence_Destination', '=', 'agences2.id')
        ->join('approvisionnements', 'notification_approvs.Id_Approvisionnement', '=', 'approvisionnements.id')
        ->select('notification_approvs.*', 'agences.NomAgence as NomAgenceSource', 'agences2.NomAgence as NomAgenceDestination', 'approvisionnements.Reference_Approvisionnement as Reference_Approv', )
        ->orderBy('created_at', 'desc')
        ->where('notification_approvs.Id_Agence_Destination', '=', $site_id)
        ->where('notification_approvs.Statut', '=', 1)
        ->get();

        return $notification_approvs;
    }
}
if (!function_exists('notification_approv_emballages')) {

    function notification_approv_emballages()
    {

        $site_id = session()->get('site_id');

        $notification_approv_emballages = DB::table('notification_approv_emballages')
        ->join('agences', 'notification_approv_emballages.Id_Agence_Source', '=', 'agences.id')
        ->join('agences as agences2', 'notification_approv_emballages.Id_Agence_Destination', '=', 'agences2.id')
        ->join('appro_emballages', 'notification_approv_emballages.Id_Appro_Emballage', '=', 'appro_emballages.id')
        ->select('notification_approv_emballages.*', 'agences.NomAgence as NomAgenceSource', 'agences2.NomAgence as NomAgenceDestination', 'appro_emballages.Reference_Appro_Emballage as Reference_Approv', )
        ->orderBy('created_at', 'desc')
        ->where('notification_approv_emballages.Id_Agence_Destination', '=', $site_id)
        ->where('notification_approv_emballages.Statut', '=', 1)
        ->get();

        return $notification_approv_emballages;
    }
}

if (!function_exists('notification_achemis')) {
    function notification_achemis()
    {

        $site_id = session()->get('site_id');


        $notification_achemis = DB::table('notification_achemis')
        ->join('agences', 'notification_achemis.Id_Agence_Source', '=', 'agences.id')
        ->join('agences as agences2', 'notification_achemis.Id_Agence_Destination', '=', 'agences2.id')
        ->join('acheminements', 'notification_achemis.Id_Acheminement', '=', 'acheminements.id')
        ->select('notification_achemis.*', 'agences.NomAgence as NomAgenceSource', 'agences2.NomAgence as NomAgenceDestination', 'acheminements.Reference_acheminement as Reference_achemis', )
        ->orderBy('created_at', 'desc')
        ->where('notification_achemis.Id_Agence_Destination', $site_id)
        ->where('notification_achemis.Statut', '=', 1)

        ->get();
       // dd($notification_achemis);

        return $notification_achemis;
    }
}
if (!function_exists('notification_achemi_emballages')) {
    function notification_achemi_emballages()
    {

        $site_id = session()->get('site_id');


        $notification_achemi_emballages = DB::table('notification_achemi_emballages')
        ->join('agences', 'notification_achemi_emballages.Id_Agence_Source', '=', 'agences.id')
        ->join('agences as agences2', 'notification_achemi_emballages.Id_Agence_Destination', '=', 'agences2.id')
        ->join('acheminement_emballages', 'notification_achemi_emballages.Id_Acheminement', '=', 'acheminement_emballages.id')
        ->select('notification_achemi_emballages.*', 'agences.NomAgence as NomAgenceSource', 'agences2.NomAgence as NomAgenceDestination', 'acheminement_emballages.Reference_acheminement as Reference_achemis', )
        ->orderBy('created_at', 'desc')
        ->where('notification_achemi_emballages.Id_Agence_Destination', $site_id)
        ->where('notification_achemi_emballages.Statut', '=', 1)
        ->get();
       // dd($notification_achemi_emballages);

        return $notification_achemi_emballages;
    }
}

if (!function_exists('magasins')) {

    function magasins()
    {
        $site_id = session()->get('site_id');

        $magasins = Magasin::where('agence_id', $site_id)->get();

        return $magasins;
    }
}

if (!function_exists('categorie_approvs')) {

    function categorie_approvs()
    {
        $site_id = session()->get('site_id');

        $categorie = DB::table('receptionner_appros')
        ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', '=', 'approvisionners.id')
        ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
        ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
        ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
        ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
        ->select('categorie_produits.id', 'categorie_produits.Libelle')
        ->distinct()
        ->get();

        return $categorie;
    }
}

if (!function_exists('categorie_appros')) {

    function categorie_appros()
    {
        $site_id = session()->get('site_id');

        $categorie = DB::table('approvisionners')
        ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
        ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
        ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
        ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
        ->select('categorie_produits.id', 'categorie_produits.Libelle')
        ->distinct()
        ->get();

        return $categorie;
    }
}

if (!function_exists('categorie_emballage_appros')) {

    function categorie_emballage_appros()
    {
        $site_id = session()->get('site_id');

        $categorie = DB::table('ligne_appro_emballages')
        ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
        ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
        ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
        ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
        ->select('categorie_emballages.id', 'categorie_emballages.Libelle')
        ->distinct()
        ->get();

        return $categorie;
    }
}
/*
if (!function_exists('nombre_notification_approvs')) {
    function nombre_notification_approvs()
    {

        $site_id = session()->get('site_id');

        $nombre_notification_approvs = NotificationApprovs::where('Id_Agence_Destination', '=', $site_id)->count();


        // dd($nombre_notification_approvs);
        // if(!$nombre_notification_approvs){
        //     $nombre_notification_approvs = 0;
        // }else{
        //     $nombre_notification_approvs = count($nombre_notification_approvs);
        // }

        return $nombre_notification_approvs;
    }
} */

if (!function_exists('fournisseurs')) {
    function fournisseurs()
    {
        return Fournisseur::all();
    }
}

if (!function_exists('userAffectedSiege')) {
    function userAffectedSiege()
    {
        $user_idaffected = AgenceUser::where('user_id', auth()->user()->id)->where('agence_id', 1)->first();

        if ($user_idaffected) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('emballageActiver')) {

    function emballageActiver()
    {
        $emballageActiver = PrefixeReference::where('emballage', 1)->first();

        if ($emballageActiver) {
            return true;
        } else {
            return false;
        }
    }
}




if (!function_exists('total_facture')) {
    function total_facture($id_facture)
    {
        $total_facture = TotalFacture::where('facture_id', $id_facture)->first();
        $total_HT = $total_facture->TotalExoneree + $total_facture->TotalHT_B + $total_facture->TotalHT_C + $total_facture->TotalHT_D + $total_facture->TotalHT_E +  $total_facture->TotalHT_F;
        $total_TVA = $total_facture->TotalTVA_B + $total_facture->TotalTVA_D;

        return json_encode([
            'total_HT' => $total_HT,
            'total_TVA' => $total_TVA,
            'total_TTC' => $total_HT + $total_TVA + $total_facture->Aib_facturee,
            'total_Aib_deductible' => $total_facture->Aib_deductible,
            'total_Aib_facturee' => $total_facture->Aib_facturee
        ]);
    }
}

if (!function_exists('nettoyerPhrase')) {
    function nettoyerPhrase($phrase)
    {
        return preg_replace('/\s+/', ' ', trim($phrase));
    }
}

if (!function_exists('aibPrecocher')) {

    function aibPrecocher()
    {
        $aibPrecocher = PrefixeReference::where('id',1)->first();

        if ($aibPrecocher) {
            return $aibPrecocher->pre_cocher_aib;
        } else {
            return false;
        }
    }
}

