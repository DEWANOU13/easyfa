<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Action;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Creation des widgets dashboard
        DB::table('widgets')->insert([
            ['id' => 1, 'name' => 'Total produits et catégories produit', 'description' => 'Total produits et catégories produit', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Total fournisseurs', 'description' => 'Total fournisseurs', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Total magasins', 'description' => 'Total magasins', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Total stocks, entrées et sorties', 'description' => 'Total stocks, entrées et sorties', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Total clients et catégories clients', 'description' => 'Total clients et catégories clients', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Total factures FV, FA et proformas', 'description' => 'Total factures FV, FA et proformas', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Etat des reglements', 'description' => 'Etat des reglements', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Chiffre d\'affaire', 'description' => 'Chiffre d\'affaire', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Top des 10 meilleurs clients', 'description' => 'Top des 10 meilleurs clients', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Top des 10 meilleurs produits', 'description' => 'Top des 10 meilleurs produits', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Historique de vos 5 dernières connexions', 'description' => 'Historique de vos 5 dernières connexions', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Creation super admin et admin
        DB::table('users')->insert([
            ['name' => 'Super Admin', 'email' => 'superadmin@easyfac.net', 'password' => Hash::make('123456'), 'actif' => 1, 'created_at' => now()],
            ['name' => 'Admin', 'email' => 'admin@easyfac.net', 'password' => Hash::make('123456'), 'actif' => 1, 'created_at' => now()],
            ['name' => 'Lin', 'email' => 'constantlin@gmail.com', 'password' => Hash::make('123456'), 'actif' => 1, 'created_at' => now()],
        ]);

        $users = DB::table('users')->select('id')->get();
        foreach ($users as $user) {
            DB::table('user_widgets')->insert([
                ['user_id' => $user->id, 'widget_id' => 11, 'created_at' => now()],
                ['user_id' => $user->id, 'widget_id' => 7, 'created_at' => now()],
                ['user_id' => $user->id, 'widget_id' => 10, 'created_at' => now()],
                ['user_id' => $user->id, 'widget_id' => 9, 'created_at' => now()],
                ['user_id' => $user->id, 'widget_id' => 8, 'created_at' => now()],
                ['user_id' => $user->id, 'widget_id' => 4, 'created_at' => now()],
                ['user_id' => $user->id, 'widget_id' => 6, 'created_at' => now()]
            ]);
        }

        // Creation super admin et admin
        DB::table('categorie_produits')->insert([
            ['Libelle' => 'PRESTATION', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Creation de l'agence principale
        DB::table('agences')->insert([
            ['id' => 1, 'NomAgence' => 'Siège', 'EnActivite' => 1, 'create_user_id' => 1, 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Affecter l'agence principale au super admin et a l'admin
        DB::table('agence_users')->insert([
            ['agence_id' => 1, 'user_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['agence_id' => 1, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['agence_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Creattion des modules
        DB::table('modules')->insert([
            ['id' => 1, 'nom_module' => 'FACTURATION', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nom_module' => 'PARAMETRES', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nom_module' => 'PRODUITS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nom_module' => 'REGLEMENTS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nom_module' => 'STOCKS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nom_module' => 'TIERS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nom_module' => 'STATISTIQUES', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nom_module' => 'EMBALLAGES', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'nom_module' => 'Caisses', 'created_at' => now(), 'updated_at' => now()],
        ]);



        // Creation des libellé type operations
        DB::table('libelle_type_operations')->insert([
            ['id' => 1, 'Libelle_Operation' => 'CARTE BANCAIRE', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'Libelle_Operation' => 'CHEQUE', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'Libelle_Operation' => 'ESPECE', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'Libelle_Operation' => 'MOBILE MONEY', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'Libelle_Operation' => 'VIREMENT', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insertion de données dans la table 'actions' à l'aide de la classe DB
        DB::table('actions')->insert([
            ['id' => 1, 'nom_action' => 'Consulter les proformas', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nom_action' => 'Creer un proforma', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nom_action' => 'Modifier une proforma', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nom_action' => 'Consulter les factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nom_action' => 'Creer une facture', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nom_action' => 'Annuler une facture', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nom_action' => 'Normaliser une facture', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nom_action' => 'Consulter les avoirs', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'nom_action' => 'Creer un avoirs', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'nom_action' => 'Normaliser un avoir', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'nom_action' => 'Consulter la liste des agences', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'nom_action' => 'Creer une agence', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'nom_action' => 'Modifier une agence', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'nom_action' => 'Consulter la liste des utilisateurs', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'nom_action' => 'Creer un utilisateur', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'nom_action' => 'Modifier un utilisateur', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'nom_action' => 'Consulter la liste des groupes', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'nom_action' => 'Creer un groupe', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'nom_action' => 'Modifier un groupe', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'nom_action' => 'Attribuer un droit d\'acces', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'nom_action' => 'Attribuer une agence', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'nom_action' => 'factures et lignes fatures', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'nom_action' => 'Consulter la liste des categories de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'nom_action' => 'Creer une catégorie de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'nom_action' => 'Modifier une catégorie de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'nom_action' => 'Consulter une unité de comptage', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'nom_action' => 'Creer une unité de comptage', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'nom_action' => 'Modifier une unité de comptage', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'nom_action' => 'Consulter la liste des produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'nom_action' => 'Creer un produit', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'nom_action' => 'Modifier un produit', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'nom_action' => 'Consulter les prix de vente', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'nom_action' => 'Creer un prix de vente', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'nom_action' => 'Modifier un prix de vente', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'nom_action' => 'Consulter les règlements', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'nom_action' => 'Enregistrer un reglement', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'nom_action' => 'Annuler un reglement', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'nom_action' => 'Consulter la liste des magasins', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 39, 'nom_action' => 'Creer un magasins', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'nom_action' => 'Modifier un magasin', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'nom_action' => 'Consulter le stock de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 42, 'nom_action' => 'Consulter les entrées de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 43, 'nom_action' => 'Effectuer une entrée de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 44, 'nom_action' => 'Consulter les sorties de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 45, 'nom_action' => 'Effectuer une sortie de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 46, 'nom_action' => 'Consulter les transferts de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 47, 'nom_action' => 'Effectuer un transfert de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 48, 'nom_action' => 'Consulter les inventaires', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 49, 'nom_action' => 'Effectuer un inventaire', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 50, 'nom_action' => 'Boucler un inventaire', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 51, 'nom_action' => 'Consulter la liste des approvisionnements', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 52, 'nom_action' => 'Effectuer un approvisionnement', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 53, 'nom_action' => 'Consulter la liste de réception des approvisionnements', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 54, 'nom_action' => 'Réceptionner un approvisonnement', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 55, 'nom_action' => 'Consulter la liste des fournisseurs', 'module_id' => 6, 'created_at' => now(), 'unow()pdated_at' => now()],
            ['id' => 56, 'nom_action' => 'Creer un fournisseur', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 57, 'nom_action' => 'Modifier le fournisseur', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 58, 'nom_action' => 'Consulter la liste des clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 59, 'nom_action' => 'Creer un client', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 60, 'nom_action' => 'Modifier les clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 61, 'nom_action' => 'Consulter la liste des catégories de clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 62, 'nom_action' => 'Creer une catégorie de client', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 63, 'nom_action' => 'Modifier une catégorie de client', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 64, 'nom_action' => 'Consulter les statistiques achats', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 65, 'nom_action' => 'Consulter les statistiques ventes', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 66, 'nom_action' => 'Consulter les statistiques stocks', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 67, 'nom_action' => 'Consulter les statistiques marges', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            // Ajouter Ensuite
            ['id' => 68, 'nom_action' => 'Parametres', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 69, 'nom_action' => 'Importation', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 70, 'nom_action' => 'Exporter la liste des fournisseurs', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 71, 'nom_action' => 'Exporter la liste des clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 72, 'nom_action' => 'Importer le compte clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 73, 'nom_action' => 'Exporter les produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 74, 'nom_action' => 'Importer les produits actifs', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 75, 'nom_action' => 'Importer les produits inactifs', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 76, 'nom_action' => 'Importer les prestation', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 77, 'nom_action' => 'Dupliquer un proforma', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 78, 'nom_action' => 'Convertir proforma en facture', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 79, 'nom_action' => 'Imprimer proforma', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 80, 'nom_action' => 'Imprimer facture en A4', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 81, 'nom_action' => 'Imprimer facture en A5', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 82, 'nom_action' => 'Imprimer facture en A8', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 83, 'nom_action' => 'Imprimer Bordereau de livraison', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 84, 'nom_action' => 'Invalider un avoir', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 85, 'nom_action' => 'Imprimer avoir en A4', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 86, 'nom_action' => 'Imprimer avoir en A5', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 87, 'nom_action' => 'Imprimer avoir en A8', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 88, 'nom_action' => 'Imprimer reglement', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 89, 'nom_action' => 'Imprimer la liste des transfert', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 90, 'nom_action' => 'Imprimer les entrees de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 91, 'nom_action' => 'Imprimer les sorties de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            // Ajouter de nouveau encore
            ['id' => 92, 'nom_action' => 'Voir les taxes', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 93, 'nom_action' => 'Créer une taxe', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 94, 'nom_action' => 'Modifier une taxe', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 95, 'nom_action' => 'Voir la liste des groupes de catégorie de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 96, 'nom_action' => 'Creer un groupe categorie de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 97, 'nom_action' => 'Modifier un groupe categorie de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 98, 'nom_action' => 'Associer une catégorie à un groupe categorie de produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 99, 'nom_action' => 'Révoquer une association d\'une catégorie et d\'un groupe de catégorie de produit', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 100, 'nom_action' => 'Voir les raports de statistique', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 101, 'nom_action' => 'Exporter en pdf les raports de statistique', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 102, 'nom_action' => 'Exporter en excel les raports de statistique', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 103, 'nom_action' => 'Voir les points de vente', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 104, 'nom_action' => 'Exporter en pdf les points de vente', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 105, 'nom_action' => 'Exporter en excel les les points de vente', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            // Les actions pour les emballages
            ['id' => 106, 'nom_action' => 'Voir la liste des catégories d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 107, 'nom_action' => 'Créer une categorie d\'emballages', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 108, 'nom_action' => 'Modifier une catégorie d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 109, 'nom_action' => 'Voir la liste des emballages', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 110, 'nom_action' => 'Créer un emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 111, 'nom_action' => 'Modifier un emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            // Les actions inventaires emballages
            ['id' => 112, 'nom_action' => 'Voir inventaire stock emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 113, 'nom_action' => 'Exporter en pdf l\'inventaire des stocks d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 114, 'nom_action' => 'exporter en excel l\'inventaire des stocks d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 115, 'nom_action' => 'Voir liste des entrées d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 116, 'nom_action' => 'Faire un entré d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 117, 'nom_action' => 'Exporter en pdf les entrées d\emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 118, 'nom_action' => 'Exporter en excel les entrées d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 119, 'nom_action' => 'Voir liste des sorties d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 120, 'nom_action' => 'Faire une sortie d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 121, 'nom_action' => 'Imprimer en pdf les sorties d\emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 122, 'nom_action' => 'Exporter en excel les sorties d\emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 123, 'nom_action' => 'Voir la liste des inventaires', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 124, 'nom_action' => 'Faire un inventaire d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 125, 'nom_action' => 'Imprimer en pdf les inventaire d\emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 126, 'nom_action' => 'Exporter en excel les inventaires d\emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 127, 'nom_action' => 'Voir les transfert d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 128, 'nom_action' => 'Faire un transfert d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 129, 'nom_action' => 'Exporter en pdf les transfert d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 130, 'nom_action' => 'Exporter en excel les transferts  d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 131, 'nom_action' => 'Voir les consignations', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            // seuil stock
            ['id' => 132, 'nom_action' => 'Voir le seuil stock', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            // Produits
            ['id' => 133, 'nom_action' => 'Importer le prix des produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Achememinement
            ['id' => 134, 'nom_action' => 'Consulter la liste des acheminenments', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 135, 'nom_action' => 'Effectuer un acheminement', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 136, 'nom_action' => 'Consulter la liste des réceptions d\'acheminement', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 137, 'nom_action' => 'Effectuer une réception d\'acheminement', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            // Acheminement et reApprovisionnement emballage
            ['id' => 138, 'nom_action' => 'Consulter la liste des approvisionnements emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 139, 'nom_action' => 'Effectuer un approvisionnement emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 140, 'nom_action' => 'Consulter la liste des réceptions d\'approvisionnement emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 141, 'nom_action' => 'Effectuer une réception d\'approvisionnement emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 142, 'nom_action' => 'Consulter la liste des acheminenments emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 143, 'nom_action' => 'Effectuer un acheminement emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 144, 'nom_action' => 'Consulter la liste des réceptions d\'acheminement emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 145, 'nom_action' => 'Effectuer une réception d\'acheminement emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            // Consignations
            ['id' => 146, 'nom_action' => 'Voir les entrées de consignation', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            // Produits relevé sortie
            ['id' => 147, 'nom_action' => 'Relevé des sorties de produits', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            // Les actions de la caisse
            ['id' => 148, 'nom_action' => 'Voir la liste des catégories de dépense', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 149, 'nom_action' => 'Créer une catégorie de dépense', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 150, 'nom_action' => 'Modifier une catégorie de dépense', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 151, 'nom_action' => 'Voir la liste des dépenses', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 152, 'nom_action' => 'Effectuer une nouvelle dépense', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 153, 'nom_action' => 'Voir la liste des catégories de recette', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 154, 'nom_action' => 'Créer une catégorie de recette', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 155, 'nom_action' => 'Modifier une catégorie de recette', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 156, 'nom_action' => 'Voir la liste des recettes', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 157, 'nom_action' => 'Effectuer une nouvelle recette', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 158, 'nom_action' => 'Voir la caisse', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 159, 'nom_action' => 'Ouvrir une nouvelle caisse', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 160, 'nom_action' => 'Fermer la caisse', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 161, 'nom_action' => 'Statistique rapport de la caisee', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 162, 'nom_action' => 'Imprimer stock en pdf par catégorie', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            // Les tous derniers droits
            ['id' => 163, 'nom_action' => 'Exporter en excel la liste des magasins', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 164, 'nom_action' => 'Imprimer la liste des magasins', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 165, 'nom_action' => 'Imprimer la liste des fournisseurs', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 166, 'nom_action' => 'Exporter en excel la liste des categorie client', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 167, 'nom_action' => 'Imprimer la liste des categories clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 168, 'nom_action' => 'Imprimer la liste des clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 169, 'nom_action' => 'Voir le compte des clients', 'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 170, 'nom_action' => 'Exporter en excel les unité de comptage', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 171, 'nom_action' => 'Imprimer en pdf les unités de comptage', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 172, 'nom_action' => 'Historique de fixation des prix', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 173, 'nom_action' => 'Exporter en excel les prix des produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 174, 'nom_action' => 'Exporter au format d\'importation les prix des produits', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 175, 'nom_action' => 'Imprimer en pdf les prix', 'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 176, 'nom_action' => 'Importer la liste des emballages (excel)', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 177, 'nom_action' => 'Voir les impressions des proformas', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 178, 'nom_action' => 'Exporter en excel la liste des proformas', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 179, 'nom_action' => 'Imprimer en pdf la liste des proformas', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 180, 'nom_action' => 'Voir les impressions des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 181, 'nom_action' => 'Exporter en excel la liste des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 182, 'nom_action' => 'Imprimer en pdf la liste des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 183, 'nom_action' => 'Voir les statistiques globales des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 184, 'nom_action' => 'Exporter en excel les statistiques globales des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 185, 'nom_action' => 'Imprimer en pdf les statistiques globales des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 186, 'nom_action' => 'Voir les statistiques détaillees des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 187, 'nom_action' => 'Exporter en excel les statistiques détaillees des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 188, 'nom_action' => 'Imprimer en pdf les statistiques détaillees des factures', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 189, 'nom_action' => 'Voir les impressions des avoirs', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 190, 'nom_action' => 'Exporter en excel la liste des avoirs', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 191, 'nom_action' => 'Imprimer en pdf la liste des avoirs', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 192, 'nom_action' => 'Consignation sur la situation des clients', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 193, 'nom_action' => 'Regler une consignation', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 194, 'nom_action' => 'Regler une consignation entrée', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 195, 'nom_action' => 'Exporter reglement en excel', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 196, 'nom_action' => 'Voir les impressions des reglements', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 197, 'nom_action' => 'Exporter en excel la liste des reglements', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 198, 'nom_action' => 'Imprimer en pdf la liste des reglements', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 199, 'nom_action' => 'Voir les impressions des reglements sur periode', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 200, 'nom_action' => 'Exporter en excel la liste des reglements sur periode', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 201, 'nom_action' => 'Imprimer en pdf la liste des reglements sur periode', 'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 202, 'nom_action' => 'Voir les impressions des depenses', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 203, 'nom_action' => 'Voir les impressions des recettes', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 204, 'nom_action' => 'Exporter caisse en excel', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 205, 'nom_action' => 'Exporter caisee en pdf', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 206, 'nom_action' => 'Voir les impressions de dépense', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 207, 'nom_action' => 'Voir les impressions de recette', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 208, 'nom_action' => 'Voir les impressions de caisse', 'module_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 209, 'nom_action' => 'Voir les statistiques achats cumulees par mois', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 210, 'nom_action' => 'Imprimer le pdf de statistiques achats cumulees par mois', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 211, 'nom_action' => 'Exporter en excel de statistiques achats cumulees par mois', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 212, 'nom_action' => 'Voir les statistiques achats cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 213, 'nom_action' => 'Imprimer le pdf de statistiques achats cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 214, 'nom_action' => 'Exporter en excel de statistiques achats cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 215, 'nom_action' => 'Voir les statistiques achats cumulees par fournisseur', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 216, 'nom_action' => 'Imprimer le pdf de statistiques achats cumulees par fournisseur', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 217, 'nom_action' => 'Exporter en excel de statistiques achats cumulees par fournisseur', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 218, 'nom_action' => 'Voir les statistiques achats cumulees par agence', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 219, 'nom_action' => 'Imprimer le pdf de statistiques achats cumulees par agence', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 220, 'nom_action' => 'Exporter en excel de statistiques achats cumulees par agence', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 221, 'nom_action' => 'Voir les statistiques ventes par valeur cumulees par jour', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 222, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur cumulees par jour', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 223, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur cumulees par jour', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 224, 'nom_action' => 'Voir les statistiques ventes par valeur cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 225, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 226, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 227, 'nom_action' => 'Voir les statistiques ventes par valeur cumulees par client', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 228, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur cumulees par client', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 229, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur cumulees par client', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 230, 'nom_action' => 'Voir les statistiques ventes par valeur cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 231, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 232, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 233, 'nom_action' => 'Voir les statistiques ventes par valeur cumulees par agence', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 234, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur cumulees par agence', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 235, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur cumulees par agence', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 236, 'nom_action' => 'Voir les statistiques ventes par valeur du journal des ventes', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 237, 'nom_action' => 'Exporter en excel les statistiques ventes par valeur des journaux des ventes', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 238, 'nom_action' => 'Imprimer les statistiques ventes par valeur des journaux des ventes', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 239, 'nom_action' => 'Voir les statistiques ventes par valeur des factures annulées et leur avoir', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 240, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur des factures annulées et leur avoir', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 241, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur des factures annulées et leur avoir', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 242, 'nom_action' => 'Voir les statistiques ventes par valeur des ventes par utilisateurs', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 243, 'nom_action' => 'Exporter en excel de statistiques ventes par valeur ventes par utilisateurs', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 244, 'nom_action' => 'Imprimer le pdf de statistiques ventes par valeur ventes par utilisateurs', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 245, 'nom_action' => 'Voir les statistiques des ventes par valeur', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 246, 'nom_action' => 'Voir les statistiques des ventes par quantité', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 247, 'nom_action' => 'Voir les statistiques ventes par quantité cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 248, 'nom_action' => 'Exporter en excel de statistiques ventes par quantité cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 249, 'nom_action' => 'Imprimer le pdf de statistiques ventes par quantité cumulees par categorie', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 250, 'nom_action' => 'Voir les statistiques ventes par quantité cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 251, 'nom_action' => 'Exporter en excel de statistiques ventes par quantité cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 252, 'nom_action' => 'Imprimer le pdf de statistiques ventes par quantité cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 253, 'nom_action' => 'Voir les statistiques stocks du magasin', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 254, 'nom_action' => 'Exporter en excel de statistiques stock du magasin', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 255, 'nom_action' => 'Imprimer le pdf de statistiques stock du magasin', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 256, 'nom_action' => 'Voir les statistiques stocks consolidé', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 257, 'nom_action' => 'Exporter en excel de statistiques stock consolidé', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 258, 'nom_action' => 'Imprimer le pdf de statistiques stock consolidé', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 259, 'nom_action' => 'Voir les statistiques stocks de la fiche stock consolide', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 260, 'nom_action' => 'Exporter en excel de statistiques stock fiche stock consolide', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 261, 'nom_action' => 'Imprimer le pdf de statistiques stock fiche stock consolide', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 262, 'nom_action' => 'Voir les statistiques marge par valeur cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 263, 'nom_action' => 'Exporter en excel de statistiques marge par valeur cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 264, 'nom_action' => 'Imprimer le pdf de statistiques marge par valeur cumulees par produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 265, 'nom_action' => 'Voir les statistiques marge par valeur cumulees par jour', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 266, 'nom_action' => 'Exporter en excel de statistiques marge par valeur cumulees par jour', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 267, 'nom_action' => 'Imprimer le pdf de statistiques marge par valeur cumulees par jour', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 268, 'nom_action' => 'Voir les statistiques marge par valeur cumulees par mois', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 269, 'nom_action' => 'Exporter en excel de statistiques marge par valeur cumulees par mois', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 270, 'nom_action' => 'Imprimer le pdf de statistiques marge par valeur cumulees par mois', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 272, 'nom_action' => 'Voir les statistiques marge par valeur cumulees par client', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 273, 'nom_action' => 'Exporter en excel de statistiques marge par valeur cumulees par client', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 274, 'nom_action' => 'Imprimer le pdf de statistiques marge par valeur cumulees par client', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 275, 'nom_action' => 'Voir les statistiques rapport vente produit sans marge', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 276, 'nom_action' => 'Voir les statistiques rapport vente produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 277, 'nom_action' => 'Exporter en excel les statistiques rapport vente produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 278, 'nom_action' => 'Imprimer les statistiques rapport vente produit', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 279, 'nom_action' => 'Voir les statistiques rapport vente prestation', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 280, 'nom_action' => 'Exporter en excel les statistiques rapport vente prestation', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 281, 'nom_action' => 'Imprimer les statistiques rapport vente prestation', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 282, 'nom_action' => 'Exporter en excel les statistiques de releve sorti', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 283, 'nom_action' => 'Imprimer en pdf les statistiques rapport vente prestation', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 284, 'nom_action' => 'Exporter en excel les statistiques de caisse', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 285, 'nom_action' => 'Imprimer les statistiques de caisse', 'module_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 286, 'nom_action' => 'Exporter en excel le stock produit', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 287, 'nom_action' => 'Exporter en excel le stock par emballage', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 288, 'nom_action' => 'Exporter en excel le stock au format d\'importation', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 289, 'nom_action' => 'Imprimer en pdf le stock', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 290, 'nom_action' => 'Imprimer en pdf le stock par emballage', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 291, 'nom_action' => 'Importer le stock produit', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 292, 'nom_action' => 'Historique du stock produit', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 293, 'nom_action' => 'Historique mise a jour import du stock produit', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 294, 'nom_action' => 'Voir les produits sous seul du stock produit', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 295, 'nom_action' => 'Exporter en excel les entrees de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 296, 'nom_action' => 'Importer les entrees de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 297, 'nom_action' => 'Voir les impression d\entree de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 298, 'nom_action' => 'Exporter en excel les entrees de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 299, 'nom_action' => 'Imprimer en pdf les entrees de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 300, 'nom_action' => 'Exporter en excel la liste des sorties de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 301, 'nom_action' => 'Exporter en excel la liste des impressions de sorties de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 302, 'nom_action' => 'Imprimer en pdf la liste des impressions de sorties de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 303, 'nom_action' => 'Exporter en excel la liste des impressions des inventaires de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 304, 'nom_action' => 'Imprimer en pdf la liste des impressions des inventaires de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 305, 'nom_action' => 'Exporter en excel la liste des transferts de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 306, 'nom_action' => 'Exporter en excel la liste des impressions de transferts de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 307, 'nom_action' => 'Imprimer en pdf la liste des impressions de transferts de produits', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 308, 'nom_action' => 'Exporter au format d\'importation le stock emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 309, 'nom_action' => 'Importer le stock emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 310, 'nom_action' => 'Historique du stock emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 311, 'nom_action' => 'Historique mise a jour du stock emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 312, 'nom_action' => 'Voir la liste des impressions de stock emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 313, 'nom_action' => 'Voir les impressions de sortie d\'emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 314, 'nom_action' => 'Voir les impressions d\'inventaire emballage', 'module_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 315, 'nom_action' => 'Affectation de droit par utilisateur', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 316, 'nom_action' => 'Affectation de groupe aux utilisateurs', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 317, 'nom_action' => 'Voir et modifier les entete et pied de page excel', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 318, 'nom_action' => 'Voir et modifier les entete et pied de page pdf', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Creation du groupe Super Admin et Admin
        DB::table('groupes')->insert([
            ['id' => 1, 'nom_groupe' => 'Super Admin', 'description' => 'Super Administrateur', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nom_groupe' => 'Admin', 'description' => 'Administrateur', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Affecter Le super admin au groupe super admin et l'admin au groupe admin
        DB::table('groupe_users')->insert([
            ['id' => 1, 'groupe_id' => 1, 'user_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'groupe_id' => 2, 'user_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'groupe_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);


        // Affecter Droit

        // Recuperer les ids des actions
        $idsAction = Action::pluck('id')->toArray();

        //Afecter les droits au super admin et Monsieur Lin
        foreach ($idsAction as $idAction) {

            // On affecte tous les droits au groupe super admin
            DB::table('groupe_actions')->insert([
                ['groupe_id' => 1, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
            ]);

            // On affecte tous less droits aux utilisateurs super admin  Lin
            DB::table('action_users')->insert([
                ['user_id' => 1, 'agence_id' => 1, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => 3, 'agence_id' => 1, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Affectation des droits initiaux a l'admin
        foreach ($idsAction as $idAction) {

            // Droit a ne pas lui accorder
            $droitPasAccorder = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 68];

            if (!in_array($idAction, $droitPasAccorder)) {

                // On affecte les droits initiaux a l'admin
                DB::table('groupe_actions')->insert([
                    ['groupe_id' => 2, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
                ]);

                // On affecte les droits initiaux a l'admin
                DB::table('action_users')->insert([
                    ['user_id' => 2, 'agence_id' => 1, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
        }


        // Creation des groupe de taxation
        DB::table('groupe_taxations')->insert([
            ['id' => 1, 'Code_lettre' => 'A', 'Etiquette' => 'EXONERES', 'valeur_taxe' => 0, 'Date_Synchro' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'Code_lettre' => 'B', 'Etiquette' => 'TVA HT[B] 18%', 'valeur_taxe' => 18, 'Date_Synchro' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'Code_lettre' => 'C', 'Etiquette' => 'TVA HT[C] 0%', 'valeur_taxe' => 0, 'Date_Synchro' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'Code_lettre' => 'D', 'Etiquette' => 'TVA HT[D] 18%', 'valeur_taxe' => 18, 'Date_Synchro' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'Code_lettre' => 'E', 'Etiquette' => 'REGIME TPS [E]', 'valeur_taxe' => 0, 'Date_Synchro' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'Code_lettre' => 'F', 'Etiquette' => 'RESERVES TPS [F]', 'valeur_taxe' => 0, 'Date_Synchro' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insertion des prefixe facture
        DB::table('prefixe_references')->insert([
            ['id' => 1, 'entre_produit' => 'EP', 'sortie_produit' => 'SP', 'transfert_produit' => 'TP', 'inventaire' => 'IN', 'proforma' => 'PR', 'facture' => 'FV', 'avoir' => 'FA', 'reglement' => 'REG', 'libelle_reserves' => 'RESERVES', 'approvisionnement' => 'AP', 'reception_approvisionnement' => 'RE', 'acheminement' => 'IS', 'reception_acheminement' => 'RA', 'mode_impression' => 1, 'prise_en_compte_reglement' => 1, 'surplus_reglement' => 1, 'type_normalisation' => 1, 'emballage' => 0, 'caisse' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
