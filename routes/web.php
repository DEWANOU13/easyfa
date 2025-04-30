<?php

use App\Models\Stock;
use App\Models\Maintenance;
use GuzzleHttp\TransferStats;
use App\Models\InventaireProduit;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReceptionAcheminement;
use App\Http\Controllers\AcheminementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ArcheminementController;
use App\Http\Controllers\caisse\CaisseController;
use App\Http\Controllers\Produit\StockController;
use App\Http\Controllers\accueil\agenceController;
use App\Http\Controllers\accueil\clientController;
use App\Http\Controllers\caisse\DepenseController;
use App\Http\Controllers\caisse\RecetteController;
use App\Http\Controllers\GestionDesPrixController;
use App\Http\Controllers\Produit\EntreeController;
use App\Http\Controllers\Produit\SortieController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\accueil\magasinController;
use App\Http\Controllers\Produit\NouveauController;
use App\Http\Controllers\Produit\ProduitController;

use App\Http\Controllers\accueil\dashboardController;
use App\Http\Controllers\ApprovisionnementController;
use App\Http\Controllers\facturation\avoirController;
use App\Http\Controllers\Produit\CategorieController;
use App\Http\Controllers\Produit\TransfertController;
use App\Http\Controllers\statistique\margeController;
use App\Http\Controllers\statistique\venteController;
use App\Http\Controllers\ImportExport\ExcelController;
use App\Http\Controllers\Produit\InventaireController;
use App\Http\Controllers\Produit\seuilStockController;
use App\Http\Controllers\ReceptionnerApprovController;
use App\Http\Controllers\statistique\stocksController;
use App\Http\Controllers\statistique\venteqController;
use App\Http\Controllers\accueil\fournisseurController;
use App\Http\Controllers\Auth\changePasswordController;
use App\Http\Controllers\emballage\emballageController;
use App\Http\Controllers\facturation\factureController;
use App\Http\Controllers\Produit\GestionPrixController;
use App\Http\Controllers\reglement\reglementController;
use App\Http\Controllers\Auth\SMSVerificationController;
use App\Http\Controllers\facturation\documentController;
use App\Http\Controllers\facturation\proformaController;
use App\Http\Controllers\AcheminementEmballageController;
use App\Http\Controllers\Produit\UniteComptageController;
use App\Http\Controllers\ReceptionAcheminementController;
use App\Http\Controllers\emballage\consignationController;
use App\Http\Controllers\statistique\pointVenteController;
use App\Http\Controllers\accueil\categorieclientController;
use App\Http\Controllers\caisse\categorieDepenseController;
use App\Http\Controllers\Maintenance\MaintenanceController;
use App\Http\Controllers\Produit\groupeCategorieController;
use App\Http\Controllers\statistique\statistiqueController;
use App\Http\Controllers\emballage\StockEmballageController;
use App\Http\Controllers\facturation\factureAvoirController;
use App\Http\Controllers\statistique\rapportVenteController;
use App\Http\Controllers\statistique\releveSortieController;
use App\Http\Controllers\Auth\IdentityVerificationController;
use App\Http\Controllers\emballage\EntreeEmballageController;
use App\Http\Controllers\emballage\SortieEmballageController;
use App\Http\Controllers\facturation\normalisationController;
use App\Http\Controllers\statistique\RapportCaisseController;
use App\Http\Controllers\EmballageApprovisionnementController;
use App\Http\Controllers\EmballageReceptionnerApprovController;
use App\Http\Controllers\emballage\ConsignationEntreeController;
use App\Http\Controllers\emballage\TransfertEmballageController;
use App\Http\Controllers\emballage\InventaireEmballageController;
use App\Http\Controllers\ReceptionAcheminementEmballageController;
use App\Http\Controllers\parametre_administration\groupeController;
use App\Http\Controllers\parametre_administration\parametreController;
use App\Http\Controllers\parametre_administration\agenceUserController;
use App\Http\Controllers\parametre_administration\UtilisateurController;
use App\Http\Controllers\parametre_administration\SurveillanceController;
use App\Http\Controllers\parametre_administration\Facture_FF_FLFController;
use App\Http\Controllers\parametre_administration\affectationdroitController;
use App\Http\Controllers\parametre_administration\FactureFFController;

Route::get('/', function () {
    return to_route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/accueil', [dashboardController::class, 'eleHmentsdashbord'])->name('home');

    Route::get('/verif-access', function () {
        return view('verif-access');
    })->name('verif-access');

    // Route::get('/dash', [dashboardController::class, 'elementsdashbord'])->name('home');
    Route::get('/importation-fichiers', [dashboardController::class, 'importation'])->name('importation');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/send-sms-code', [SMSVerificationController::class, 'sendVerificationCode'])->name('sms.send');
    Route::post('/verify-sms-code', [SMSVerificationController::class, 'verifyCode'])->name('sms.verify');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/verif-access-force', function () {
        return view('verif-access-force');
    })->name('verif-access-force');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/trigger-identity-verification', [LoginController::class, 'identityTriggerVerification'])->name('identity.trigger.verification');
    Route::get('/identity/verify/{token}', [LoginController::class, 'identityShow'])->name('identity.verify.notice');
    Route::post('/identity-verify-code', [LoginController::class, 'identityVerify'])->name('identity.verify');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/trigger-email-verification', [LoginController::class, 'emailTriggerVerification'])->name('email.trigger.verification');
    Route::get('/email/verify/{token}', [LoginController::class, 'emailShow'])->name('email.verify.notice');
    Route::post('/email-verify-code', [LoginController::class, 'emailVerify'])->name('email.verify');
});
Route::post('switchAgence', [LoginController::class, 'switchAgence'])->middleware('auth')->name('switchAgence');
Route::get('deconnexion', [LoginController::class, 'deconnexion'])->middleware('auth')->name('deconnexion');
Route::post('/password-change', [LoginController::class, 'passwordCharge'])->middleware('auth')->name('first.password.update');

/* Les routes pour l'authentification */
Route::prefix('/')->group(function () {
    Route::get('/choix-agence', [LoginController::class, 'choixAgence'])->middleware('guest')->name('choix-agence');
    // Route::get('connexion', [LoginController::class, 'login'])->middleware('guest')->name('login');
    Route::get('choixAgence', [LoginController::class, 'choixAgence'])->middleware('auth')->name('choixAgence');
    Route::post('choixAgence', [LoginController::class, 'doLoginChoixAgence'])->middleware('auth')->name('choixAgenceLogin');
    Route::get('changer/mot-de-passe', [changePasswordController::class, 'changePassword'])->name('changePassword');
    Route::post('changer/mot-de-passe', [changePasswordController::class, 'doChangePassword']);
});
/* Fin routes pour authentification */


//route htmx
Route::get('/get-produit}', [EntreeController::class, 'showDesignation'])->middleware('auth')->name('get-produit');

//Dossier produit--------------------------------------------------------------------------------------------------------------
Route::get('/categorie', [CategorieController::class, 'index'])->middleware('auth')->name('page.produit.categorie');
Route::post('/export-categorie', [CategorieController::class, 'export'])->middleware('auth')->name('exportCategorieProduit');
Route::post('/imprimer-categorie', [CategorieController::class, 'imprimer'])->middleware('auth')->name('imprimerCategorieProduit');
Route::post('/store-categorie', [CategorieController::class, 'store'])->middleware('auth')->name('store.categorie');
Route::post('/store-produit-categorie', [CategorieController::class, 'storeProduit'])->middleware('auth')->name('store.produit_categorie');
Route::get('/ajout-categorie', [CategorieController::class, 'create'])->middleware('auth')->name('page.produit.categorie_nouveau');
Route::get('/edit-categorie/{id}', [CategorieController::class, 'edit'])->middleware('auth')->name('edit.categorie');
Route::put('/update-categorie/{id}', [CategorieController::class, 'update'])->middleware('auth')->name('update.categorie');

Route::post('/export-unite-comptage', [UniteComptageController::class, 'export'])->middleware('auth')->name('exportUniteComptage');
Route::post('/imprimer-unite-comptage', [UniteComptageController::class, 'imprimer'])->middleware('auth')->name('imprimerUniteComptage');
Route::get('/unite-comptage', [UniteComptageController::class, 'index'])->middleware('auth')->name('page.produit.unite_comptage');
Route::get('/nouvelle-unite-comptage', [UniteComptageController::class, 'create'])->middleware('auth')->name('page.unite_comptage_nouveau');
Route::post('/store-unite-comptage', [UniteComptageController::class, 'store'])->middleware('auth')->name('store.unite_comptage');
Route::post('/store-produit-unite-comptage', [UniteComptageController::class, 'storeProduit'])->middleware('auth')->name('store.produit_unite_comptage');
Route::get('/edit-unite-comptage/{id}', [UniteComptageController::class, 'edit'])->middleware('auth')->name('edit.unite_comptage');
Route::put('/update-unite-comptage/{id}', [UniteComptageController::class, 'update'])->middleware('auth')->name('update.unite_comptage');

Route::get('/produit', [ProduitController::class, 'index'])->middleware('auth')->name('page.produit.produit');
Route::post('/export-produit', [ProduitController::class, 'export'])->middleware('auth')->name('exportProduit');
Route::post('/imprimer-produit', [ProduitController::class, 'imprimerProduit'])->middleware('auth')->name('imprimer-produit');
Route::get('/store-produit', [NouveauController::class, 'store'])->middleware('auth')->name('store.produit');
Route::get('/edit-produit/{id}', [NouveauController::class, 'edit'])->middleware('auth')->name('edit.produit');
Route::get('/update-produit/{id}', [NouveauController::class, 'update'])->middleware('auth')->name('update.produit');

Route::get('/entree-emballage', [EntreeEmballageController::class, 'index'])->middleware('auth')->name('entree_emballage');
//entree embalage
Route::get('/entree-emballage', [EntreeEmballageController::class, 'index'])->middleware('auth')->name('entree_emballage');
Route::post('/store-entree-emballage', [EntreeEmballageController::class, 'store'])->middleware('auth')->name('store_entree_emballage');
Route::get('/ajout-entree-emballage', [EntreeEmballageController::class, 'create'])->middleware('auth')->name('entree_nouveau_emballage');
Route::get('/get-entrer-emballage/{id}', [EntreeEmballageController::class, 'getEntrerEmballage'])->middleware('auth')->name('getEntrerEmballage');
Route::post('/imprimer-entree-emballage', [EntreeEmballageController::class, 'imprimer_entree_emballage'])->middleware('auth')->name('imprimer_entree_emballage');
Route::post('/imprimer-liste-entree-emballage', [EntreeEmballageController::class, 'imprimerEntreeEmballagePeriode'])->middleware('auth')->name('imprimerEntreeEmballagePeriode');

Route::get('/entree', [EntreeController::class, 'index'])->middleware('auth')->name('page.entree.entree');
Route::post('/store-entree', [EntreeController::class, 'store'])->middleware('auth')->name('store.entree');
Route::get('/get-entrer-produit/{id}', [EntreeController::class, 'getEntrerProduit'])->middleware('auth')->name('get.entrer_produit_for_entree_produit');
Route::post('get-detail-import-stock', [StockController::class, 'getDetailsImportStock'])->middleware('auth')->name('get.detail_import_stock');
Route::get('/ajout-entree', [EntreeController::class, 'create'])->middleware('auth')->name('page.produit.entree_nouveau');
Route::post('/imprimer-entree', [EntreeController::class, 'imprimerEntree'])->middleware('auth')->name('imprimer-entree');
Route::post('nouveau-fournisseur-entree', [EntreeController::class, 'storeFournisseur'])->middleware('auth')->name('storeFournisseur-entree');
Route::post('nouveau-magasin-entree', [EntreeController::class, 'storeMagasinentree'])->middleware('auth')->name('storeMagasin-entree');
Route::post('/imprimer-entree-action', [EntreeController::class, 'entreeImprimer'])->middleware('auth')->name('imprimer-entree-action');
Route::post('/fiche-importation-entree-action', [EntreeController::class, 'entreeImporter'])->middleware('auth')->name('fiche_importation_entree_action');

Route::get('/ajout-sortie-emballage', [SortieEmballageController::class, 'create'])->middleware('auth')->name('sortie_nouveau');
Route::post('/store-sortie-emballage', [SortieEmballageController::class, 'store'])->middleware('auth')->name('store_sortie_emballage');
Route::get('/get-sortie-emballage/{id}', [SortieEmballageController::class, 'getSortirEmballage'])->middleware('auth')->name('getSortirEmballage');
Route::post('/imprimer-sortie-emballage', [SortieEmballageController::class, 'imprimer_sortie_emballage'])->middleware('auth')->name('imprimer_sortie_emballage');
Route::post('/imprimer-liste-sortie-emballage', [SortieEmballageController::class, 'imprimerlisteSortieEmballage'])->middleware('auth')->name('imprimerlisteSortieEmballage');

Route::get('/sortie', [SortieController::class, 'index'])->middleware('auth')->name('page.sortie.sortie');
Route::get('/ajout-sortie', [SortieController::class, 'create'])->middleware('auth')->name('page.produit.sortie_nouveau');
Route::get('/get-sortie-produit/{id}', [SortieController::class, 'getSortirProduit'])->middleware('auth')->name('get.sortir_produit_for_sortie_produit');
Route::post('/store-sortie', [SortieController::class, 'store'])->middleware('auth')->name('store.sortie');
Route::post('/imprimer-sortie', [SortieController::class, 'imprimerSortie'])->middleware('auth')->name('imprimer-sortie');
Route::post('/imprimer-sortie-action', [SortieController::class, 'sortirImprimer'])->middleware('auth')->name('imprimer-sortie-action');

Route::get('/gestion-prix', [GestionPrixController::class, 'index'])->middleware('auth')->name('page.gestion_prix.gestion_prix');
Route::post('/export-prix', [GestionPrixController::class, 'exportPrix'])->middleware('auth')->name('export-prix');
Route::post('/imprimer-prix', [GestionPrixController::class, 'imprimerPrix'])->middleware('auth')->name('imprimer-prix');
Route::post('/export-prix-history', [GestionPrixController::class, 'exportPrixHistory'])->middleware('auth')->name('export-prix-history');
Route::post('/imprimer-prix-history', [GestionPrixController::class, 'imprimerPrixHistory'])->middleware('auth')->name('imprimer-prix-history');


Route::get('/gestion-des-prix-produits', [GestionDesPrixController::class, 'gestion_des_prix'])->middleware('auth')->name('gestion_des_prix');
Route::get('/filter-des-prix-produits', [GestionDesPrixController::class, 'filterProduits'])->middleware('auth')->name('filter.produits');
Route::get('/filter-des-listes-prix-produits', [GestionDesPrixController::class, 'filterListeProduits'])->middleware('auth')->name('filter.prix.produits');
Route::get('/historique-des-prix-produits', [GestionDesPrixController::class, 'historiquePrixProduits'])->middleware('auth')->name('historique.prix.produits');
Route::post('/update-prix-produits', [GestionDesPrixController::class, 'updatePrixProduits'])->middleware('auth')->name('update.prix.produits');
Route::post('/importPrixProduitsViaGestion', [GestionDesPrixController::class, 'importPrixProduitsViaGestion'])->middleware('auth')->name('importPrixProduitsViaGestion');


Route::post('/store-gestion-prix', [GestionPrixController::class, 'store'])->middleware('auth')->name('store.gestion-prix');
Route::post('/filter-liste-gestion-prix', [GestionPrixController::class, 'filterListeGestionPrix'])->middleware('auth')->name('filterListe.gestion-prix');

Route::get('/inventaire-emballage', [InventaireEmballageController::class, 'index'])->middleware('auth')->name('inventaire_emballage');
Route::get('/ajout-inventaire-emballage', [InventaireEmballageController::class, 'create'])->middleware('auth')->name('inventaire_emballage_nouveau');
Route::get('/store-inventaire-emballage', [InventaireEmballageController::class, 'store'])->middleware('auth')->name('store.inventaire_emballage');
Route::post('/modifier-inventorier-emballage', [InventaireEmballageController::class, 'modifier_inventorier_emballage'])->middleware('auth')->name('modifier_inventorier_emballage');
Route::post('/imprimer-inventorier-emballage', [InventaireEmballageController::class, 'impimer_inventaire_emballage'])->middleware('auth')->name('impimer_inventaire_emballage');
Route::get('/modifier-inventaire-emballage', [InventaireEmballageController::class, 'modifier_statut_inventaire_emballage'])->middleware('auth')->name('modifier_statut_inventaire_emballage');
Route::get('/get-inventaire-magasin-emballage/{id}', [InventaireEmballageController::class, 'getInventaireEmballageMagasin'])->middleware('auth')->name('get.inventaire_magasin_for_inventaire_emballage');
Route::get('/get-products-by-category-inventaire-emballage/{id}', [InventaireEmballageController::class, 'getProductsByCategoryEmballage'])->middleware('auth');
Route::get('/get-category-by-magasin-inventaire-emballage/{id}', [InventaireEmballageController::class, 'getCategorysByMagasinEmballage'])->middleware('auth');

Route::get('/inventaire', [InventaireController::class, 'index'])->middleware('auth')->name('page.inventaire.inventaire');
Route::get('/ajout-inventaire', [InventaireController::class, 'create'])->middleware('auth')->name('page.inventaire.inventaire_nouveau');
Route::get('/store-inventaire', [InventaireController::class, 'store'])->middleware('auth')->name('store.inventaire');
Route::get('/get-inventaire-magasin/{id}', [InventaireController::class, 'getInventaireMagasin'])->middleware('auth')->name('get.inventaire_magasin_for_inventaire_produit');
Route::post('/filter-magasin-inventorier', [InventaireController::class, 'filterMagasinInventorier'])->middleware('auth')->name('filter.magasin_inventorier');
Route::get('/modifier-inventaire-produit', [InventaireController::class, 'modifierStatutInventaireProduit'])->middleware('auth')->name('modifier_statut_inventaire_produit');
Route::post('/modifier-inventorier', [InventaireController::class, 'modifierInventorier'])->middleware('auth')->name('modifier_inventorier');
Route::post('/imprimer-inventorier', [InventaireController::class, 'imprimerInventaire'])->middleware('auth')->name('impimer-inventaire');
Route::get('/get-products-by-category-inventaire/{id}', [InventaireController::class, 'getProductsByCategory'])->middleware('auth');
Route::get('/get-category-by-magasin-inventaire/{id}', [InventaireController::class, 'getCategorysByMagasin'])->middleware('auth');

//transfert emballage
Route::get('/transfert-emballage', [TransfertEmballageController::class, 'index'])->middleware('auth')->name('transfert_emballage');
Route::get('/transfert-emballage-nouveau', [TransfertEmballageController::class, 'create'])->middleware('auth')->name('transfert_nouveau_emballage');
Route::post('/store-transfert-emballage', [TransfertEmballageController::class, 'store'])->middleware('auth')->name('store_transfert_emballage');
Route::get('/get-tranferer-emballage/{id}', [TransfertEmballageController::class, 'getTransfertEmballage'])->middleware('auth')->name('getTransfertEmballage');
Route::post('/transfert-emballage-imprimer', [TransfertEmballageController::class, 'ImprimerTransfertEmballage'])->middleware('auth')->name('ImprimerTransfertEmballage');
Route::post('/transfert-emballage-periode', [TransfertEmballageController::class, 'transfertEmballagePeriode'])->middleware('auth')->name('transfertEmballagePeriode');
Route::post('/transfert_emballage-imprimer', [TransfertEmballageController::class, 'transfertEmballageImprimer'])->middleware('auth')->name('transfert_emballage-imprimer');


Route::get('/nouveau', [NouveauController::class, 'index'])->middleware('auth')->name('page.nouveau.nouveau');
Route::get('/transfert-emballage', [TransfertEmballageController::class, 'index'])->middleware('auth')->name('transfert_emballage');
Route::get('/get-tranferer-produit/{id}', [TransfertController::class, 'getTransfertProduit'])->middleware('auth')->name('get.transferer_for_transfert_produit');
Route::get('/ajout-transfert', [TransfertController::class, 'create'])->middleware('auth')->name('page.produit.transfert_nouveau');
Route::get('/transfert', [TransfertController::class, 'index'])->middleware('auth')->name('page.transfert.transfert');
Route::post('/store-transfert', [TransfertController::class, 'store'])->middleware('auth')->name('store.transfert');
Route::post('/imprimer-transfert', [TransfertController::class, 'imprimerTransfert'])->middleware('auth')->name('imprimer-transfert');
Route::post('/transfert-imprimer', [TransfertController::class, 'transfertImprimer'])->middleware('auth')->name('transfert-imprimer');


Route::post('/produits/importFixationPrixx', [ProduitController::class, 'importFixationPrixx'])->name('importFixationPrixx');


Route::get('/stock-emballage', [StockEmballageController::class, 'index'])->middleware('auth')->name('stock_emballage');
Route::post('get-detail-import-stock-emballage', [StockEmballageController::class, 'getDetailsImportStockEmballage'])->middleware('auth')->name('get.detail_import_stock_emballage');

//stock emballage
Route::get('/stock-emballage', [StockEmballageController::class, 'index'])->middleware('auth')->name('stock_emballage');
Route::match(['get', 'post'], '/filter-stock-emballage', [StockEmballageController::class, 'filterStockEmballage'])->middleware('auth')->name('filter_stock_emballage');
Route::post('/filter-stock-emballage-historique', [StockEmballageController::class, 'filter_stock_emballage_historique'])->middleware('auth')->name('filter_stock_emballage_historique');
Route::post('/imprimerStockEmballage', [StockEmballageController::class, 'imprimerStockEmballage'])->middleware('auth')->name('imprimerStockEmballage');
Route::post('/imprimerStockEmballageHistorique', [StockEmballageController::class, 'imprimerStockEmballageHistorique'])->middleware('auth')->name('imprimerStockEmballageHistorique');
Route::post('/exporterStockEmballageHistorique', [StockEmballageController::class, 'exporterStockEmballageHistorique'])->middleware('auth')->name('exporterStockEmballageHistorique');
Route::match(['get', 'post'], '/filter-stock-emballage-historique', [StockEmballageController::class, 'filter_stock_emballage_historique'])->middleware('auth')->name('filter.stock_emballage_historique');

Route::get('/stock', [StockController::class, 'index'])->middleware('auth')->name('page.stock.stock');
// Route::get('/filter-stock-produit/{agence}/{magasin}/{categorie}/{produit}', [StockController::class, 'filterStockProduit'])->name('filter.stock_produit');
// Route::post('/filter-stock-produit', [StockController::class, 'filterStockProduit'])->name('filter.stock_produit');
// Route::post('/filter-stock-produit-historique', [StockController::class, 'filterStockProduitHistorique'])->name('filter.stock_produit_historique');
// Route::post('/filter-stock-produit', [StockController::class, 'filterStockProduit'])->middleware('auth')->name('filter.stock_produit');
Route::match(['get', 'post'], '/filter-stock-produit-historique', [StockController::class, 'filterStockProduitHistorique'])->middleware('auth')->name('filter.stock_produit_historique');
Route::match(['get', 'post'], '/filter-stock-produit', [StockController::class, 'filterStockProduit'])->middleware('auth')->name('filter.stock_produit');
Route::post('/imprimerStock', [StockController::class, 'imprimerStock'])->middleware('auth')->name('imprimerStock');
Route::post('/imprimer-stock-historique', [StockController::class, 'imprimerStockHistorique'])->middleware('auth')->name('imprimer-stock-historique');
Route::post('/exporter-stock-historique', [StockController::class, 'exporterStockHistorique'])->middleware('auth')->name('exporter-stock-historique');

Route::get('/sortie-emballage', [SortieEmballageController::class, 'index'])->middleware('auth')->name('sortie_emballage');
Route::get('/sortie/filter', [SortieController::class, 'filterSortie'])->middleware('auth')->name('filterSortie');
Route::get('/entree/filter', [EntreeController::class, 'filterEntree'])->middleware('auth')->name('filterEntree');
Route::get('/inventaire/filter', [InventaireController::class, 'filterInventaire'])->middleware('auth')->name('filterInventaire');
Route::get('/transfert/filter', [TransfertController::class, 'filterTransfert'])->middleware('auth')->name('filterTransfert');
Route::get('/reglement/filter', [reglementController::class, 'filterReglement'])->middleware('auth')->name('filterReglement');
Route::get('/reglement_surplus', [reglementController::class, 'reglement_surplus'])->middleware('auth')->name('reglement_surplus');

Route::get('/sortieEmballage/filter', [SortieEmballageController::class, 'filterSortieEmballage'])->middleware('auth')->name('filterSortieEmballage');
Route::get('/transfertEmballage/filter', [TransfertEmballageController::class, 'filterTransfertEmballage'])->middleware('auth')->name('filterTransfertEmballage');
Route::get('/inventaireEmballage/filter', [InventaireEmballageController::class, 'filterInventaireEmballage'])->middleware('auth')->name('filterInventaireEmballage');
Route::get('/entreeEmballage/filter', [EntreeEmballageController::class, 'filterEntreeEmballage'])->middleware('auth')->name('filterEntreeEmballage');


Route::get('/get-products-by-category/{id}', [StockController::class, 'getProductsByCategory'])->middleware('auth');
Route::get('/get-products-by-category-emballage/{id}', [StockEmballageController::class, 'getProductsByCategoryEmballage'])->middleware('auth');
Route::post('/get-approvisionner-reception', [ReceptionnerApprovController::class, 'getApprov'])->middleware('auth');
Route::post('/get-approvisionner-reception-emballage', [EmballageReceptionnerApprovController::class, 'getApprovEmballage'])->middleware('auth');

//------------------------------Groupe categorie
Route::get('/groupes-categorie', [groupeCategorieController::class, 'index'])->middleware('auth')->name('groupeCategorie');
Route::post('/store-groupe-categorie', [groupeCategorieController::class, 'storeGroupeCategorie'])->middleware('auth')->name('storeGroupeCategorie');
Route::post('/update-groupe-categorie', [groupeCategorieController::class, 'updateGroupeCategorie'])->middleware('auth')->name('updateGroupeCategorie');
Route::post('/association-categorie', [groupeCategorieController::class, 'storeAssociationGroupeCategorieProduit'])->middleware('auth')->name('storeAssociationGroupeCategorieProduit');
Route::post('/delete-selected', [groupeCategorieController::class, 'deleteSelected'])->middleware('auth')->name('deleteSelectedAssociations');

// route approvisionnement
Route::get('/approvisionner', [ApprovisionnementController::class, 'index'])->middleware('auth')->name('approvisionner');
Route::get('/nouveau-approvissionner', [ApprovisionnementController::class, 'create'])->middleware('auth')->name('nouveau_approvissionner');
Route::post('/store-approvissionner', [ApprovisionnementController::class, 'store'])->middleware('auth')->name('store.approvisionnement');
Route::get('/get-approvisionner/{id}', [ApprovisionnementController::class, 'getApprovisionner'])->middleware('auth')->name('get.approvisionner_by_approvisionnement');
Route::post('/approvisionnement-imprimer-action', [ApprovisionnementController::class, 'ApprovisinnementImprimerAction'])->middleware('auth')->name('approvisionnement_imprimer_action');
Route::post('/approvisionnement-imprimer', [ApprovisionnementController::class, 'ReceptionApprovOngletImpression'])->middleware('auth')->name('imprimer-approv');
Route::post('/get-produit-approvisionnement', [ApprovisionnementController::class, 'getProductsByCategoryApprov'])->middleware('auth');
Route::get('/approvisionnement/filter', [ApprovisionnementController::class, 'filterApprovisionnement'])->middleware('auth')->name('filterApprovisionnement');

Route::get('/changeStatutnotificationApprov/{id}', [EmballageApprovisionnementController::class, 'changeStatutnotificationApprov'])->middleware('auth')->name('changeStatutnotificationApprov');
Route::get('/changeStatutnotificationApprovEmb/{id}', [EmballageApprovisionnementController::class, 'changeStatutnotificationApprovEnb'])->middleware('auth')->name('changeStatutnotificationApprovEnb');
Route::get('/changeStatutnotificationAchemi/{id}', [EmballageApprovisionnementController::class, 'changeStatutnotificationAchemi'])->middleware('auth')->name('changeStatutnotificationAchemi');
Route::get('/changeStatutnotificationAchemiEmb/{id}', [EmballageApprovisionnementController::class, 'changeStatutnotificationAchemiEmb'])->middleware('auth')->name('changeStatutnotificationAchemiEmb');


// approvisionnement emballage
Route::get('/approvisionner-emballage', [EmballageApprovisionnementController::class, 'index'])->middleware('auth')->name('approvisionner_emballage');
Route::get('/approvisionnement-emballage/filter', [EmballageApprovisionnementController::class, 'filterAproEmballage'])->middleware('auth')->name('filterAproEmballage');

Route::get('/nouveau-approvissionner-emballage', [EmballageApprovisionnementController::class, 'create'])->middleware('auth')->name('nouveau_approvissionner_emballage');
Route::post('/store-approvissionner-emballage', [EmballageApprovisionnementController::class, 'store'])->middleware('auth')->name('store.approvisionnement_emballage');
Route::get('/get-approvisionner-emballage/{id}', [EmballageApprovisionnementController::class, 'getApprovisionnerEmballage'])->middleware('auth')->name('get.approvisionner_by_approvisionnement_emballage');
Route::post('/approvisionnement-emballage-imprimer-action', [EmballageApprovisionnementController::class, 'ApprovisinnementImprimerActionEmballage'])->middleware('auth')->name('approvisionnement_imprimer_action_emballage');
Route::post('/approvisionnement-emballage-imprimer', [EmballageApprovisionnementController::class, 'ReceptionApprovOngletImpressionEmballage'])->middleware('auth')->name('imprimer-approv-emballage');
Route::post('/get-produit-approvisionnement-emballage', [EmballageApprovisionnementController::class, 'getProductsByCategoryApprovEmballage'])->middleware('auth');

// reception approvisionnement
Route::get('/reception-approv', [ReceptionnerApprovController::class, 'index'])->middleware('auth')->name('reception_approvisionnement');
Route::get('/nouveau-reception-approv', [ReceptionnerApprovController::class, 'create'])->middleware('auth')->name('nouveau_reception_approvissionner');
Route::post('/sotre-reception-approv', [ReceptionnerApprovController::class, 'store'])->middleware('auth')->name('store.reception_approv_nouveau');
Route::get('/get-reception-approvisionner/{id}', [ReceptionnerApprovController::class, 'getReceptionnerApprov'])->middleware('auth')->name('get.receptionner_by_reception');
Route::post('/reception-approv-imprimer-action', [ReceptionnerApprovController::class, 'ReceptionApprovImprimerAction'])->middleware('auth')->name('reception_approv_imprimer_action');
Route::post('/reception-approv-impression', [ReceptionnerApprovController::class, 'ReceptionApprovOngletImpression'])->middleware('auth')->name('reception_approv_impression');
Route::post('/get-reception-approv', [ReceptionnerApprovController::class, 'getProductsByCategoryReception'])->middleware('auth');
Route::get('/reception/filter', [ReceptionnerApprovController::class, 'filterReceptionAppro'])->middleware('auth')->name('filterReceptionAppro');


// reception approvisionnement emballage
Route::get('/reception-approv-emballage', [EmballageReceptionnerApprovController::class, 'index'])->middleware('auth')->name('reception_approvisionnement_emballage');
Route::get('/reception-approv-emballage/filter', [EmballageReceptionnerApprovController::class, 'filterReceptionApproEmballage'])->middleware('auth')->name('filterReceptionApproEmballage');


Route::get('/nouveau-reception-approv-emballage', [EmballageReceptionnerApprovController::class, 'create'])->middleware('auth')->name('nouveau_reception_approvissionner_emballage');
Route::post('/sotre-reception-approv-emballage', [EmballageReceptionnerApprovController::class, 'store'])->middleware('auth')->name('store.reception_approv_nouveau_emballage');
Route::get('/get-reception-approvisionner-emballage/{id}', [EmballageReceptionnerApprovController::class, 'getReceptionnerApprov'])->middleware('auth')->name('get.receptionner_by_reception_emballage');
Route::post('/reception-approv-imprimer-action-emballage', [EmballageReceptionnerApprovController::class, 'ReceptionApprovImprimerAction'])->middleware('auth')->name('reception_approv_imprimer_action_emballage');
Route::post('/reception-approv-impression-emballage', [EmballageReceptionnerApprovController::class, 'ReceptionApprovOngletImpression'])->middleware('auth')->name('reception_approv_impression_emballage');
Route::post('/get-reception-approv-emballage', [EmballageReceptionnerApprovController::class, 'getProductsByCategoryReception'])->middleware('auth');

// route archeminement
Route::get('/acheminer', [AcheminementController::class, 'index'])->middleware('auth')->name('acheminer');
Route::get('/nouveau-acheminer', [AcheminementController::class, 'create'])->middleware('auth')->name('nouveau_acheminer');
Route::post('/store-acheminer', [AcheminementController::class, 'store'])->middleware('auth')->name('store.acheminer');
Route::get('/get-acheminer/{id}', [AcheminementController::class, 'getAcheminer'])->middleware('auth')->name('get.acheminer_by_acheminement');
Route::post('/acheminement-imprimer-action', [AcheminementController::class, 'acheminement_imprimer_action'])->middleware('auth')->name('acheminement_imprimer_action');
Route::post('/acheminement-imprimer', [AcheminementController::class, 'imprimer_acheminement'])->middleware('auth')->name('imprimer_acheminement');
Route::get('/acheminement/filter', [AcheminementController::class, 'filterAcheminement'])->middleware('auth')->name('filterAcheminement');

Route::get('/reception-acheminement', [ReceptionAcheminementController::class, 'index'])->middleware('auth')->name('reception_acheminement');
Route::get('/nouveau-reception-acheminement', [ReceptionAcheminementController::class, 'create'])->middleware('auth')->name('nouveau_reception_acheminement');
Route::post('/store-reception-acheminement', [ReceptionAcheminementController::class, 'store'])->middleware('auth')->name('store_reception_acheminement');
Route::post('/get-acheminement-reception', [ReceptionAcheminementController::class, 'getAcheminement'])->middleware('auth')->name('getAcheminement');
Route::get('/getReceptionLigne/{id}', [ReceptionAcheminementController::class, 'getReceptionLigne'])->middleware('auth')->name('getReceptionLigne');
Route::post('/reception-imprimer-action', [ReceptionAcheminementController::class, 'reception_imprimer_action'])->middleware('auth')->name('reception_imprimer_action');
Route::post('/reception-imprimer', [ReceptionAcheminementController::class, 'imprimer_reception'])->middleware('auth')->name('imprimer_reception');
Route::get('/reception_achemeniment/filter', [ReceptionAcheminementController::class, 'filterReceptionAche'])->middleware('auth')->name('filterReceptionAche');

Route::get('/acheminer-emballage', [AcheminementEmballageController::class, 'index'])->middleware('auth')->name('acheminer_emballage');
Route::get('/nouveau-acheminer-emballage', [AcheminementEmballageController::class, 'create'])->middleware('auth')->name('nouveau_acheminer_emballage');
Route::post('/store-acheminer-emballage', [AcheminementEmballageController::class, 'store'])->middleware('auth')->name('store.acheminer_emballage');
Route::get('/get-acheminer-emballage/{id}', [AcheminementEmballageController::class, 'getAcheminer'])->middleware('auth')->name('get.acheminer_by_acheminement_emballage');
Route::post('/acheminement-emballage-imprimer-action', [AcheminementEmballageController::class, 'acheminement_imprimer_action'])->middleware('auth')->name('acheminement_imprimer_action_emballage');
Route::post('/acheminement-emballage-imprimer', [AcheminementEmballageController::class, 'imprimer_acheminement'])->middleware('auth')->name('imprimer_acheminement_emballage');
Route::get('/acheminer-emballage/filter', [AcheminementEmballageController::class, 'filterAchminerEmballage'])->middleware('auth')->name('filterAchminerEmballage');


Route::get('/reception-acheminement-emballage', [ReceptionAcheminementEmballageController::class, 'index'])->middleware('auth')->name('reception_acheminement_emballage');
Route::get('/reception-acheminement-emballage/filter', [ReceptionAcheminementEmballageController::class, 'filterReceptionAchminementEmballage'])->middleware('auth')->name('filterReceptionAchminementEmballage');

Route::get('/nouveau-reception-acheminement-emballage', [ReceptionAcheminementEmballageController::class, 'create'])->middleware('auth')->name('nouveau_reception_acheminement_emballage');
Route::post('/store-reception-acheminement-emballage', [ReceptionAcheminementEmballageController::class, 'store'])->middleware('auth')->name('store_reception_acheminementt_emballage');
Route::post('/get-acheminement-reception-emballage', [ReceptionAcheminementEmballageController::class, 'getAcheminementemballage'])->middleware('auth')->name('getAcheminementt_emballage');
Route::get('/getReceptionLigne-emballage/{id}', [ReceptionAcheminementEmballageController::class, 'getReceptionLigne'])->middleware('auth')->name('getReceptionLignet_emballage');
Route::post('/reception-emballage-imprimer-action', [ReceptionAcheminementEmballageController::class, 'reception_imprimer_action'])->middleware('auth')->name('reception_imprimer_actiont_emballage');
Route::post('/reception-emballage-imprimer', [ReceptionAcheminementEmballageController::class, 'imprimer_reception'])->middleware('auth')->name('imprimer_reception_emballage');


//---------------------------------Seui stock
Route::get('/seuil-stock', [seuilStockController::class, 'index'])->middleware('auth')->name('seuilStock');
Route::post('/store-seuil-stock', [seuilStockController::class, 'storeSeuilStock'])->middleware('auth')->name('storeSeuilStock');
Route::post('/edit-seuil-stock', [seuilStockController::class, 'editSeuil'])->middleware('auth')->name('edit_seuil');
Route::put('/update-seuil-stock/{id}', [seuilStockController::class, 'updateSeuilStock'])->middleware('auth')->name('updateSeuilStock');

Route::get('/seuil-stock-produit', [seuilStockController::class, 'indexProduit'])->middleware('auth')->name('seuilStockProduit');
Route::post('/store-seuil-stock-produit', [seuilStockController::class, 'storeSeuilStockProduit'])->middleware('auth')->name('storeSeuilStockProduit');
Route::post('/edit-seuil-stock-produit', [seuilStockController::class, 'editSeuilProduit'])->middleware('auth')->name('edit_seuil_produit');
Route::put('/update-seuil-stock-produit/{id}', [seuilStockController::class, 'updateSeuilStockProduit'])->middleware('auth')->name('updateSeuilStockProduit');
Route::post('/choix-seuil', [seuilStockController::class, 'ChoixSeuil'])->middleware('auth')->name('choix_seuil');
Route::post('/choix-seuil-parametre', [parametreController::class, 'ChoixSeuilParametre'])->middleware('auth')->name('choix_seuil_parametre');
Route::post('/restituer-produit', [seuilStockController::class, 'RestituerProduit'])->middleware('auth')->name('restituer_produit');
Route::post('/restituer', [seuilStockController::class, 'Restituer'])->middleware('auth')->name('restituer');
Route::post('/store-seuil-stock-produit-all', [seuilStockController::class, 'storeSeuilStockProduitAll'])->middleware('auth')->name('seuil_produit_all');


//FIn Dossier produit--------------------------------------------------------------------------------------------------------------

//Dossier accueil--------------------------------------------------------------------------------------------------------------
Route::get('agences', [agenceController::class, 'listeAgences'])->middleware('auth')->name('agences');
Route::post('nouvelle_agence', [agenceController::class, 'storeAgence'])->middleware('auth')->name('storeAgence');
Route::put('update_agence/{id}', [agenceController::class, 'updateAgence'])->middleware('auth')->name('updateAgence');
Route::post('searchAgence', [agenceController::class, 'searchAgence'])->middleware('auth');

Route::get('magasin', [magasinController::class, 'listeMagasin'])->middleware('auth')->name('magasin');
// Route::get('nouveau_magasin', [magasinController::class, 'showForm'])->middleware('auth')->name('showFormMagasin');
Route::post('nouveau_magasin', [magasinController::class, 'storeMagasin'])->middleware('auth')->name('storeMagasin');
// Route::get('edit_magasin/{id}', [magasinController::class, 'editMagasin'])->middleware('auth')->name('editMagasin');
Route::put('update_magasin/{id}', [magasinController::class, 'updateMagasin'])->middleware('auth')->name('updateMagasin');
Route::post('/exportMagasins', [MagasinController::class, 'export'])->middleware('auth')->name('exportMagasins');
Route::post('/imprimerMagasins', [MagasinController::class, 'imprimer'])->middleware('auth')->name('imprimerMagasins');



Route::get('fournisseur', [fournisseurController::class, 'listeFournisseur'])->middleware('auth')->name('fournisseur');
Route::get('nouveau_fournisseur', [fournisseurController::class, 'showForm'])->middleware('auth')->name('showFormFournisseur');
Route::post('nouveau_fournisseur', [fournisseurController::class, 'storeFournisseur'])->middleware('auth')->name('storeFournisseur');
Route::get('edit_fournisseur/{id}', [fournisseurController::class, 'editFournisseur'])->middleware('auth')->name('editFournisseur');
Route::put('update_fournisseur/{id}', [fournisseurController::class, 'updateFournisseur'])->middleware('auth')->name('updateFournisseur');
Route::post('searchFournisseur', [fournisseurController::class, 'searchFournisseur'])->middleware('auth');
Route::post('/exportfournisseur', [fournisseurController::class, 'export'])->middleware('auth')->name('exportfournisseur');
Route::post('/imprimerfournisseur', [fournisseurController::class, 'imprimer'])->middleware('auth')->name('imprimerfournisseur');


Route::get('categorie_client', [categorieclientController::class, 'listeCategorieClient'])->middleware('auth')->name('categorieclient');
Route::get('nouvelle_categorie_client', [categorieclientController::class, 'showForm'])->middleware('auth')->name('ShowFormCategorieClient');
Route::post('nouvelle_categorie_client', [categorieclientController::class, 'storeCategorieClient'])->middleware('auth')->name('storeCategorieClient');
//Route::get('edit_categorie_client/{id}', [categorieclientController::class, 'editCategorieClient'])->middleware('auth')->name('editCategorieClient');
Route::put('update_categorie_client/{id}', [categorieclientController::class, 'updateCategorieClient'])->middleware('auth')->name('updateCategorieClient');

Route::get('client', [clientController::class, 'listeClient'])->middleware('auth')->name('client');
Route::get('nouveau_client', [clientController::class, 'showForm'])->middleware('auth')->name('showFormClient');
Route::post('nouveau_client', [clientController::class, 'storeClient'])->middleware('auth')->name('storeClient');
Route::get('edit_client/{id}', [clientController::class, 'editClient'])->middleware('auth')->name('editClient');
Route::put('update_client/{id}', [clientController::class, 'updateClient'])->middleware('auth')->name('updateClient');
Route::post('categorie_raccourci', [clientController::class, 'storeCategorieClientR'])->middleware('auth')->name('storeCategorieClientR');
Route::post('compte-client', [clientController::class, 'compteClient'])->middleware('auth')->name('compteClient');
Route::post('searchClient', [clientController::class, 'searchClient'])->middleware('auth');
Route::post('/filtre-compte', [clientController::class, 'filtreCompte'])->middleware('auth')->name('filtre-compte');

//Fin Dossier accueil------------------------------------------------------------------------------------------------------
//Dossier reglement----------------------------------------------------------------------------------------------------------
Route::get('reglement', [reglementController::class, 'listeReglement'])->middleware('auth')->name('reglement');
Route::get('nouveau_reglement', [reglementController::class, 'showForm'])->middleware('auth')->name('showFormReglement');
Route::post('sotre-reglement', [reglementController::class, 'store'])->middleware('auth')->name('store.reglement_nouveau');
Route::get('/get-detail-reglement/{id}', [reglementController::class, 'getDetailReglement'])->middleware('auth')->name('get.reglement_for_detail_reglement');
Route::get('/modifier-detail-reglement', [reglementController::class, 'modifierStatutDetailReglement'])->middleware('auth')->name('modifier_statut_detail_reglement');
Route::post('/imprimer-reglement', [reglementController::class, 'imprimerReglement'])->middleware('auth')->name('get.imprimerReglement');
Route::get('/imprimerReglement_periode', [reglementController::class, 'imprimerReglement_periode'])->middleware('auth')->name('imprimerReglement_periode');
Route::post('/export_impression_reglement_periode_pdf', [reglementController::class, 'export_impression_reglement_periode_pdf'])->middleware('auth');
Route::post('/export_excel_impression_reglement_periode', [reglementController::class, 'export_excel_impression_reglement_periode'])->middleware('auth');
Route::get('/liste-imprimer-reglement', [reglementController::class, 'listeImprimerReglement'])->middleware('auth')->name('get.listeImprimerReglement');

Route::post('/impression-reglement-A4', [reglementController::class, 'ImprimerReglementA4'])->middleware('auth')->name('impression-reglement-A4');
//Fin dossier reglement------------------------------------------------------------------------------------------------------




Route::get('facture_ff_flf', [FactureFFController::class, 'index'])->middleware('auth')->name('facture_FF__FLF');
Route::get('factureFF', [FactureFFController::class, 'factureFF'])->middleware('auth')->name('factureFF');
Route::post('export_factureFF_csv', [FactureFFController::class, 'export_factureFF_csv'])->middleware('auth')->name('export_factureFF_csv');

Route::get('facctureFLF', [FactureFFController::class, 'facctureFLF'])->middleware('auth')->name('facctureFLF');
Route::post('export_factureFLF_csv', [FactureFFController::class, 'export_factureFLF_csv'])->middleware('auth')->name('export_factureFLF_csv');

// Route::get('nouvel-utilisateur', [UtilisateurController::class, 'create'])->name('utilisateur_nouveau');
//dossier facturation-----------------------------------------------------------------------------------------------------
Route::get('proforma', [proformaController::class, 'listeProforma'])->middleware('auth')->name('proforma');
Route::get('recuperer_valeur_taxe/{taxeID}', [proformaController::class, 'recuperer_valeur_taxe'])->middleware('auth')->name('recuperer_valeur_taxe');
Route::get('recuperer_prix_produit/{produitId}', [proformaController::class, 'recuperer_prix_produit'])->middleware('auth')->name('recuperer_prix_produit');
Route::get('get_detail_proforma/{id}', [proformaController::class, 'getDetailProforma'])->middleware('auth')->name('getDetailProforma');
Route::post('store_proforma', [proformaController::class, 'storeProforma'])->middleware('auth')->name('storeProforma');
Route::get('nouveaupf', [proformaController::class, 'showForm'])->middleware('auth')->name('nouveaupf');
Route::get('/proformas/filter', [proformaController::class, 'filterProformas'])->middleware('auth')->name('filterProformas');


Route::get('edit_proforma/{id}', [proformaController::class, 'editProforma'])->middleware('auth')->name('editProforma');
Route::put('update_proforma/{id}', [proformaController::class, 'updateProforma'])->middleware('auth')->name('updateProforma');

Route::get('dupliquer_proforma/{id}', [proformaController::class, 'dupliquerProforma'])->middleware('auth')->name('dupliquerProforma');
Route::put('dupliquerStore_proforma/{id}', [proformaController::class, 'dupliquerStoreProforma'])->middleware('auth')->name('dupliquerStoreProforma');

Route::get('conversion_proforma/{id}', [proformaController::class, 'conversionProforma'])->middleware('auth')->name('conversionProforma');
Route::put('conversionStore/{id}', [proformaController::class, 'convertirEnFacture'])->middleware('auth')->name('convertirEnFacture');

Route::get('get-invoice-details/{id}', [proformaController::class, 'getLigneProforma'])->middleware('auth')->name('getLigneProforma');
Route::get('get-invoice-details2/{id}', [proformaController::class, 'getLigneProforma2'])->middleware('auth')->name('getLigneProforma2');
Route::get('recuperer_idstock/{id}', [proformaController::class, 'getstockId'])->middleware('auth')->name('getstockId');

Route::get('statistiqueGlobalFacture', [factureController::class, 'statistiqueGlobalFacture'])->middleware('auth')->name('statistiqueGlobalFacture');
Route::get('statistiqueDetailleFacture', [factureController::class, 'statistiqueDetailleFacture'])->middleware('auth')->name('statistiqueDetailleFacture');
Route::get('facture', [factureController::class, 'listeFacture'])->middleware('auth')->name('facture');
Route::get('get_detail_facture/{id}', [factureController::class, 'getDetailFacture'])->middleware('auth')->name('getDetailFacture');
Route::get('invaliderFacture/{id}', [factureController::class, 'invaliderFacture'])->middleware('auth')->name('invaliderFacture');
Route::get('normaliserFacture/{id}', [normalisationController::class, 'postInvoiceRequestDto'])->middleware('auth')->name('normaliserFacture');
Route::get('avoirFacture/{id}', [avoirController::class, 'storeFactureAvoir'])->middleware('auth')->name('avoirFacture');
Route::get('normaliserAvoir/{id}', [factureAvoirController::class, 'postInvoiceRequestDto'])->middleware('auth')->name('normaliserAvoir');

Route::get('nouveauf', [factureController::class, 'showForm'])->middleware('auth')->name('nouveauf');
Route::post('nouveauf', [factureController::class, 'storeFacture'])->middleware('auth')->name('storeFacture');
Route::get('avoir', [avoirController::class, 'listeavoir'])->middleware('auth')->name('avoir');
Route::get('get_detail_facture_avoir/{id}', [avoirController::class, 'getDetailFactureAvoir'])->middleware('auth')->name('getDetailFactureAvoir');
Route::get('invaliderAvoirFacture/{id}', [avoirController::class, 'invaliderAvoirFacture'])->middleware('auth')->name('invaliderAvoirFacture');

Route::get('/facture/filter', [FactureController::class, 'filterFacture'])->middleware('auth')->name('filter.facture');
Route::get('/factureAvoir/filter', [avoirController::class, 'filterFactureAvoir'])->middleware('auth')->name('filterFactureAvoir');

Route::get('/convertir-en-avoir/{factureId}', [avoirController::class, 'convertirEnAvoir'])->middleware('auth')->name('convertirEnAvoir');

Route::GET('imprimerProformaByPeriode', [documentController::class, 'proformaByPeriode'])->middleware('auth')->name('proformaByPeriode');
Route::get('imprimerAvoirByPeriode', [documentController::class, 'avoirByPeriode'])->middleware('auth')->name('avoirByPeriode');
Route::get('imprimerFactureByPeriode', [documentController::class, 'imprimerlisteFacture'])->middleware('auth')->name('imprimerlisteFacture');
Route::get('imprimerProformaA4/{id}', [documentController::class, 'PDFProformaA4'])->middleware('auth')->name('PDFProformaA4');
Route::get('imprimerFactureA4/{id}', [documentController::class, 'generatePDFA4'])->middleware('auth')->name('generatePDFA4');
Route::get('imprimerFactureA5/{id}', [documentController::class, 'generatePDFA4'])->middleware('auth')->name('generatePDFA5');
Route::get('imprimerFactureA8/{id}', [documentController::class, 'generatePDFA4'])->middleware('auth')->name('generatePDFA8');
Route::get('imprimerFactureavoirA4/{id}', [documentController::class, 'generatePDFAVOIRA4'])->middleware('auth')->name('generatePDFAVOIRA4');
Route::get('imprimerFactureavoirA5/{id}', [documentController::class, 'generatePDFAVOIRA4'])->middleware('auth')->name('generatePDFAVOIRA5');
Route::get('imprimerFactureavoirA8/{id}', [documentController::class, 'generatePDFAVOIRA4'])->middleware('auth')->name('generatePDFAVOIRA8');
Route::get('bordereauPDF/{id}', [documentController::class, 'bordereauPDF'])->middleware('auth')->name('bordereauPDF');

Route::post('/export-excel', [factureController::class, 'exportExcel'])->middleware('auth');
Route::post('/export_stat_global_pdf', [factureController::class, 'exportStatGlobalPdf'])->middleware('auth');
Route::post('/export_excel_stat_detaille_pdf', [factureController::class, 'exportStatDetaillePdf'])->middleware('auth');
Route::post('/export_excel_stat_detaille', [factureController::class, 'exportExcelStatDetaille'])->middleware('auth');
Route::post('/export_excel_impression_facture_periode', [documentController::class, 'exportFacturePeriode'])->middleware('auth');
Route::post('/export_impression_facture_periode_pdf', [documentController::class, 'exportFacturePeriodePdf'])->middleware('auth');
Route::post('/export_excel_impression_proforma_periode', [documentController::class, 'exportProformaPeriodeExcel'])->middleware('auth');
Route::post('/export_impression_proforma_periode_pdf', [documentController::class, 'exportProformaPeriodePdf'])->middleware('auth');
Route::post('/export_excel_impression_facture_avoir_periode', [documentController::class, 'exportFactureAvoirPeriodeExcel'])->middleware('auth');
Route::post('/export_impression_facture_avoir_periode_pdf', [documentController::class, 'exportFactureAvoirPeriodePdf'])->middleware('auth');
//::get('showa4', [documentController::class,'show'])->middleware('auth');

// Export et Import categorie client
Route::post('/export_excel_categorie_client', [categorieclientController::class, 'exportExcelCategorieClient'])->middleware('auth');
Route::post('/export_excel_client', [clientController::class, 'exportExcelClient'])->middleware('auth');
Route::post('/export_excel_client_compte', [clientController::class, 'export_excel_client_compte'])->middleware('auth');

Route::post('/import_pdf_categorie_client', [clientController::class, 'import_pdf_categorie_client'])->middleware('auth');
Route::post('/import_pdf_client', [clientController::class, 'import_pdf_client'])->middleware('auth');
Route::post('/import_pdf_compte_client', [clientController::class, 'import_pdf_compte_client'])->middleware('auth');



//::get('showa4', [documentController::class,'show'])->middleware('auth');

/* Les routes pour le sous menu parametre */
Route::prefix('/')->middleware('auth')->group(function () {
    Route::get('parametre', [parametreController::class, 'index'])->name('parametre');
    Route::post('parametre/store/prefixeReference', [parametreController::class, 'storePrefixeReference'])->name('parametre.store.prefixeReference');

    // Uploaader les entetes et pieds de pages
    Route::post('upload/entete-pied-a4', [parametreController::class, 'uploadEntetePiedA4'])->name('upload.entete-pied-a4');
    Route::post('storeEntetePiedExcel', [parametreController::class, 'storeEntetePiedExcel'])->name('storeEntetePiedExcel');
    Route::post('upload/entete-pied-a5', [parametreController::class, 'uploadEntetePiedA5'])->name('upload.entete-pied-a5');
    Route::post('upload/entete-pied-a8', [parametreController::class, 'uploadEntetePiedA8'])->name('upload.entete-pied-a8');
});
/* Fin route sous menu parametre */

/* Les routes pour le sous menu groupe */
Route::prefix('/')->middleware('auth')->group(function () {
    Route::resource('groupe', groupeController::class)->except(['show', 'destroy']);
    Route::post('searchGroupe', [groupeController::class, 'searchGroupe']);
    Route::get('groupe/{any}', function () {
        abort(404); // Retourner une erreur 404 (Not Found)
    })->where('any', '.*');
});
/* Fin route sous menu groupe */

/* Les routes pour le sous utiliasteurs */
Route::prefix('/')->middleware('auth')->group(function () {
    Route::resource('user', UtilisateurController::class)->except(['show', 'destroy']);
    Route::post('searchUser', [UtilisateurController::class, 'searchUser']);
    Route::get('user/{any}', function () {
        abort(404);
    })->where('any', '.*');
});
/* Fin route sous menu utilisateurs */

/* Les routes pour affectations et droits d'accès */
Route::prefix('/')->middleware('auth')->group(function () {
    Route::resource('groupeUser', affectationdroitController::class)->except(['show', 'create']);
    Route::get('groupeUser/{any}', function () {
        abort(404);
    })->where('any', '.*');

    // Les routes pour les affectation de droits
    Route::post('/affecterDroit/{action}', [affectationdroitController::class, 'affecterDroit'])->name('affecterDroit.groupeUser');
    Route::post('/retirerDroit/{action}', [affectationdroitController::class, 'retirerDroit'])->name('retirerDroit.groupeUser');

    // Les routes pour les affectation de droits
    Route::post('/affecterDroitUser/{action}', [affectationdroitController::class, 'affecterDroitUser'])->name('affecterDroitUser.User');
    Route::post('/retirerDroitUser/{action}', [affectationdroitController::class, 'retirerDroitUser'])->name('retirerDroitUser.User');

    // Affecter un ensemble d'action d'un module a un groupe ou lui retirer
    Route::post('/affecterToutGroupe/{groupe}', [affectationdroitController::class, 'affecterToutGroupe'])->name('affecterToutGroupe');
    Route::post('/retirerToutGroupe/{groupe}', [affectationdroitController::class, 'retirerToutGroupe'])->name('retirerToutGroupe');

    // Affceter un ensemble d'action d'un module a un utilisateur ou lui retirer
    Route::post('/affecterToutUser/{groupe}', [affectationdroitController::class, 'affecterToutUser'])->name('affecterToutUser');
    Route::post('/retirerToutUser/{groupe}', [affectationdroitController::class, 'retirerToutUser'])->name('retirerToutUser');

    // Agence dynamique a la selection d'utilisateur
    Route::post('selectAgenceDynamique', [affectationdroitController::class, 'selectAgenceDynamique'])->name('selectAgenceDynamique');
});
/* Fin des routes pour affectations et droits d'accès */

/* Les routes pour affecter les agences aux utilisateurs */
Route::prefix('/')->middleware('auth')->group(function () {
    Route::get('agence-utilisateur', [agenceUserController::class, 'index'])->name('agenceUtilisateur.index');

    Route::post('/affecterAgence', [agenceUserController::class, 'affecterAgence'])->name('affecterAgenceUser');
    Route::delete('/retirerAgence/{agenceUser}', [agenceUserController::class, 'retirerAgence'])->name('retirerAgenceUser');
});
/* Fin Les routes pour affecter les agences aux utilisateurs */

//routes statistiques
Route::get('statistiques', [statistiqueController::class, 'listestatistiques'])->middleware('auth')->name('statistiques');
Route::post('achat-cumule-par-mois', [statistiqueController::class, 'achatCumuleParMois'])->middleware('auth')->name('achat-cumule-par-mois');
Route::post('achat-cumule-par-categorie', [statistiqueController::class, 'achatCumuleParCategorie'])->middleware('auth')->name('achat-cumule-par-categorie');
Route::post('achat-cumule-par-fournisseur', [statistiqueController::class, 'achatCumuleParFournisseur'])->middleware('auth')->name('achat-cumule-par-fournisseur');
Route::post('achat-cumule-par-agence', [statistiqueController::class, 'achatCumuleParAgence'])->middleware('auth')->name('achat-cumule-par-agence');
Route::post('imprimer-achat-cumule-par-mois', [statistiqueController::class, 'imprimerAchatCumuleParMois'])->middleware('auth')->name('imprimer-achat-cumule-par-mois');
Route::post('imprimer-achat-cumule-par-categorie', [statistiqueController::class, 'imprimerAchatCumuleParCategorie'])->middleware('auth')->name('imprimer-achat-cumule-par-categorie');
Route::post('imprimer-achat-cumule-par-fournisseur', [statistiqueController::class, 'imprimerAchatCumuleParFournisseur'])->middleware('auth')->name('imprimer-achat-cumule-par-fournisseur');
Route::post('imprimer-achat-cumule-par-agence', [statistiqueController::class, 'imprimerAchatCumuleParAgence'])->middleware('auth')->name('imprimer-achat-cumule-par-agence');
//routes statistiques
Route::post('statistiquescumules ', [statistiqueController::class, 'Achatcumules'])->middleware('auth')->name('statistiquescumules');
Route::get('vente', [venteController::class, 'listevente'])->middleware('auth')->name('vente');
Route::post('vente', [venteController::class, 'venteStatistiques'])->middleware('auth')->name('venteStatistiques');
Route::post('vente-cumulee-par_produit', [venteController::class, 'venteCumuleeParProduit'])->middleware('auth')->name('vente-cumulee-par-produit');
Route::post('imprimer-vente', [venteController::class, 'imprimerventeCumuleeParProduit'])->middleware('auth')->name('imprimer-vente');
Route::post('imprimer-vente-quantite', [venteController::class, 'imprimerventeQuantite'])->middleware('auth')->name('imprimer-vente-quantite');
Route::post('export-vente', [venteController::class, 'exportVente'])->middleware('auth')->name('export-vente');
Route::post('export-vente-quantite', [venteController::class, 'exportVenteQuantite'])->middleware('auth')->name('export-vente-quantite');

Route::post('vente-cumulee-par_client', [venteController::class, 'venteCumuleeParClient'])->middleware('auth')->name('vente-cumulee-par_client');
Route::post('imprimer-vente-client', [venteController::class, 'imprimerventeCumuleeParClient'])->middleware('auth')->name('imprimer-vente-client');
Route::post('export-vente-client', [venteController::class, 'exportventeCumuleeParClient'])->middleware('auth')->name('export-vente-client');
Route::get('stocks', [stocksController::class, 'listestock'])->middleware('auth')->name('stocks');

Route::get('venteq', [venteController::class, 'listeventeq'])->middleware('auth')->name('venteq');
Route::post('venteq', [venteController::class, 'venteStatistiquesq'])->middleware('auth')->name('venteStatistiquesq');
Route::post('vente-cumulee-par_produitq', [venteController::class, 'venteCumuleeParProduitq'])->middleware('auth')->name('vente-cumulee-par-produitq');
Route::post('imprimer-venteq', [venteController::class, 'imprimerventeCumuleeParProduitq'])->middleware('auth')->name('imprimer-venteq');
Route::post('export-venteq', [venteController::class, 'exportVenteq'])->middleware('auth')->name('export-venteq');

Route::post('vente-cumulee-par_clientq', [venteController::class, 'venteCumuleeParClientq'])->middleware('auth')->name('vente-cumulee-par_clientq');
Route::post('imprimer-vente-clientq', [venteController::class, 'imprimerventeCumuleeParClientq'])->middleware('auth')->name('imprimer-vente-clientq');
Route::post('export-vente-clientq', [venteController::class, 'exportventeCumuleeParClientq'])->middleware('auth')->name('export-vente-clientq');

// Route::get('venteq', [venteqController::class, 'listeventeq'])->middleware('auth')->name('venteq');

Route::get('statistiqueStockMagasin', [stocksController::class, 'statistiqueStockMagasin'])->middleware('auth')->name('statistiqueStockMagasin');
Route::post('/export_excel_stat_stock_magasin', [stocksController::class, 'export_excel_stat_stock_magasin'])->middleware('auth');
Route::post('/export_stat_stock_magasin_pdf', [stocksController::class, 'export_stat_stock_magasin_Pdf'])->middleware('auth');
Route::get('statistiqueStockConsolide', [stocksController::class, 'statistiqueStockConsolide'])->middleware('auth')->name('statistiqueStockConsolide');
Route::post('/export_excel_stat_stock_consolide', [stocksController::class, 'export_excel_stat_stock_consolide'])->middleware('auth');
Route::post('/export_stat_stock_consolide_pdf', [stocksController::class, 'export_stat_stock_consolide_pdf'])->middleware('auth');
Route::get('ficheStockConsolide', [stocksController::class, 'ficheStockConsolide'])->middleware('auth')->name('ficheStockConsolide');
Route::post('/export_excel_fiche_stock_consolide', [stocksController::class, 'export_excel_fiche_stock_consolide'])->middleware('auth');
Route::post('/export_fiche_stock_consolide_pdf', [stocksController::class, 'export_fiche_stock_consolide_pdf'])->middleware('auth');
Route::get('/get-products-by-category-stock/{id}', [stocksController::class, 'getProductsByCategory'])->middleware('auth');



Route::get('marge', [margeController::class, 'listemarge'])->middleware('auth')->name('marge');
Route::get('margeParProduit', [margeController::class, 'margeParProduit'])->middleware('auth')->name('margeParProduit');
Route::post('/export_excel_marge_par_produit', [margeController::class, 'export_excel_marge_par_produit'])->middleware('auth');
Route::post('/export_marge_par_produit_pdf', [margeController::class, 'export_marge_par_produit_pdf'])->middleware('auth');
Route::get('margeParJour', [margeController::class, 'margeParJour'])->middleware('auth')->name('margeParJour')->middleware('auth');
Route::post('/export_excel_marge_par_jour', [margeController::class, 'export_excel_marge_par_jour'])->middleware('auth');
Route::post('/export_marge_par_jour_pdf', [margeController::class, 'export_marge_par_jour_pdf'])->middleware('auth');
Route::get('margeParMois', [margeController::class, 'margeParMois'])->middleware('auth')->name('margeParMois');
Route::post('/export_excel_marge_par_mois', [margeController::class, 'export_excel_marge_par_mois'])->middleware('auth');
Route::post('/export_marge_par_mois_pdf', [margeController::class, 'export_marge_par_mois_pdf'])->middleware('auth');
Route::get('margeParClient', [margeController::class, 'margeParClient'])->middleware('auth')->name('margeParClient');
Route::post('/export_excel_marge_par_client', [margeController::class, 'export_excel_marge_par_client'])->middleware('auth');
Route::post('/export_marge_par_client_pdf', [margeController::class, 'export_marge_par_client_pdf'])->middleware('auth');
Route::get('rapport', [rapportVenteController::class, 'listeRapport'])->middleware('auth')->name('rapport');
Route::get('rapportVente', [rapportVenteController::class, 'rapportVente'])->middleware('auth')->name('rapportVente');
Route::get('rapportVentesansmarge', [rapportVenteController::class, 'rapportVenteSansMarge'])->middleware('auth')->name('rapportVenteSansMarge');
Route::post('/export_excel_rapport_vente', [rapportVenteController::class, 'export_excel_rapport_vente'])->middleware('auth');
Route::post('/export_excel_rapport_vente_sans_marge', [rapportVenteController::class, 'export_excel_rapport_vente_sans_marge'])->middleware('auth');
Route::post('/export_rapport_vente_pdf', [rapportVenteController::class, 'export_rapport_vente_pdf'])->middleware('auth');
Route::post('/export_rapport_vente_sans_marge_pdf', [rapportVenteController::class, 'export_rapport_vente_sans_marge_pdf'])->middleware('auth');
Route::post('/expot_rapport_agnce', [rapportVenteController::class, 'expot_rapport_agnce'])->middleware('auth')->name('expot_rapport_agnce');

Route::get('rapportVentePrestation', [rapportVenteController::class, 'rapportVentePrestation'])->middleware('auth')->name('rapportVentePrestation');
Route::post('/export_excel_rapport_vente_prestation', [rapportVenteController::class, 'export_excel_rapport_vente_prestation'])->middleware('auth');
Route::post('/export_rapport_vente_prestation_pdf', [rapportVenteController::class, 'export_rapport_vente_prestation_pdf'])->middleware('auth');

Route::get('releve-sortie', [releveSortieController::class, 'releveSortie'])->middleware('auth')->name('releveSortie');
Route::get('releverSortiereq', [releveSortieController::class, 'releverSortiereq'])->middleware('auth')->name('releverSortiereq');
Route::post('export_excel_relever_sortie', [releveSortieController::class, 'export_excel_relever_sortie'])->middleware('auth')->name('export_excel_relever_sortie');
Route::post('export_relever_sortie_pdf', [releveSortieController::class, 'export_relever_sortie_pdf'])->middleware('auth')->name('export_relever_sortie_pdf');


Route::get('point-vente', [pointVenteController::class, 'pointVente'])->middleware('auth')->name('pointVente');
Route::get('pointVentePeriode', [pointVenteController::class, 'pointVentePeriode'])->middleware('auth')->name('pointVentePeriode');

Route::get('taxe', [parametreController::class, 'taxe'])->middleware('auth')->name('taxe');
Route::get('Nouvelle-taxe', [parametreController::class, 'taxeNouveau'])->middleware('auth')->name('taxeNouveau');
Route::get('edition-taxe/{id}', [parametreController::class, 'editTaxe'])->middleware('auth')->name('editTaxe');

// Route::get('/gestion-des-activites', [margeController::class, 'activites_user'])->middleware('auth')->name('user.activites');
// Route::get('/gestion-des-activites', [dashboardController::class, 'preferences_dashbord'])->middleware('auth')->name('user.dashboad_preferences');
Route::get('/preferences-utilisateur', function () {
    return view('page.accueil.home.user_preferences');
})->middleware('auth')->name('user.preferences');
Route::get('/gestion-des-activites', function () {
    return view('page.parametre_administration.activites.index');
})->middleware('auth')->name('gestion.activites');
Route::get('/get-user-dashboad-preferences', [dashboardController::class, 'user_preferences_dashbord'])->middleware('auth')->name('user.dashboad.preferences');

Route::middleware('auth')->group(function () {
    Route::get('/user-preferences', [UserPreferenceController::class, 'index'])->name('user.preferences.choice');
    Route::post('/user-preferences', [UserPreferenceController::class, 'store'])->name('user.preferences.store');
    Route::get('/api/user-widgets', [DashboardController::class, 'getUserWidgets'])->name('api.user.widgets');
});


Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.markAsRead');

        Route::get('/administration/centre-de-contrôle', [SurveillanceController::class, 'index'])->name('admin.surveillance');
        Route::get('/administration/centre-de-contrôle/getOnlineUsers', [SurveillanceController::class, 'getOnlineUsers'])->name('admin.online.users');
        Route::get('/administration/centre-de-contrôle/getLoginHistory', [SurveillanceController::class, 'getLoginHistory'])->name('admin.login.users.histories');
        Route::get('/administration/centre-de-contrôle/getActivity', [SurveillanceController::class, 'getActivity'])->name('admin.activites.users.histories');
        Route::get('/administration/centre-de-contrôle/getHistoriqueNomUser', [SurveillanceController::class, 'getHistoriqueNomUser'])->name('getHistoriqueNomUser');
        Route::get('/administration/centre-de-contrôle/getHistoriqueProduit', [SurveillanceController::class, 'getHistoriqueProduit'])->name('getHistoriqueProduit');
        Route::post('/administration/centre-de-contrôle/disconnectUser/{id}', [SurveillanceController::class, 'disconnectUser']);
        Route::post('/administration/centre-de-contrôle/forcePasswordChange/{id}', [SurveillanceController::class, 'forcePasswordChange']);

        Route::get('/administration/centre-de-contrôle/getHistoriqueProduit', [SurveillanceController::class, 'getHistoriqueProduit'])->name('getHistoriqueNomProduit');
});

//Importations et exportations
Route::post('/upload-file', [ExcelController::class, 'chargementFichier'])->middleware('auth')->name('chargementFichier');

Route::get('/exportProduits', [ExcelController::class, 'exportProduits'])->middleware('auth')->name('exportProduits');
Route::post('/importProduits', [ExcelController::class, 'importProduits'])->middleware('auth')->name('importProduits');
Route::get('/exportClients', [ExcelController::class, 'exportClients'])->middleware('auth')->name('exportClients');
Route::post('/importPrixProduits', [ExcelController::class, 'importPrixProduits'])->middleware('auth')->name('importPrixProduits');
Route::post('/importAgences', [ExcelController::class, 'importAgences'])->middleware('auth')->name('importAgences');
Route::post('/importMagasins', [ExcelController::class, 'importMagasins'])->middleware('auth')->name('importMagasins');
Route::post('/importUniteComptages', [ExcelController::class, 'importUniteComptages'])->middleware('auth')->name('importUniteComptages');
Route::post('/importCategorieProduits', [ExcelController::class, 'importCategorieProduits'])->middleware('auth')->name('importCategorieProduits');
Route::post('/importCategorieEmballages', [ExcelController::class, 'importCategorieEmballages'])->middleware('auth')->name('importCategorieEmballages');
Route::post('/importEmballages', [ExcelController::class, 'importEmballages'])->middleware('auth')->name('importEmballages');
Route::post('/importCategorieClients', [ExcelController::class, 'importCategorieClients'])->middleware('auth')->name('importCategorieClients');
Route::post('/importClients', [ExcelController::class, 'importClients'])->middleware('auth')->name('importClients');
Route::get('/exportFournisseurs', [ExcelController::class, 'exportFournisseurs'])->middleware('auth')->name('exportFournisseurs');
Route::post('/importFournisseurs', [ExcelController::class, 'importFournisseurs'])->middleware('auth')->name('importFournisseurs');
Route::post('/import-stock', [StockController::class, 'import'])->middleware('auth')->name('import.stock');
Route::post('/import.stock_emballage', [StockEmballageController::class, 'import'])->middleware('auth')->name('import.stock_emballage');
Route::post('/import-entree', [EntreeController::class, 'import'])->middleware('auth')->name('import.entree');
Route::post('/import-emballage', [EntreeEmballageController::class, 'import'])->middleware('auth')->name('import.emballage');
Route::post('/script', [reglementController::class, 'script'])->middleware('auth')->name('script');


// Exemple de fichier d'importation
Route::post('/exportFichier/agence', [ExcelController::class, 'exportFichierAgence'])->middleware('auth')->name('exportFicherAgence');
Route::post('/exportFichier/magasin', [ExcelController::class, 'exportFichierMagasin'])->middleware('auth')->name('exportFicherMagasin');
Route::post('/exportFichier/CategorieProduit', [ExcelController::class, 'exportFichierCategorieProduit'])->middleware('auth')->name('exportFicherCategorieProduit');
Route::post('/exportFichier/CategorieClient', [ExcelController::class, 'exportFichierCategorieClient'])->middleware('auth')->name('exportFicherCategorieClient');
Route::post('/exportFichier/UniteComptage', [ExcelController::class, 'exportFichierUniteComptage'])->middleware('auth')->name('exportFicherUniteComptage');


// Les Routes pour les pages où sont exécitées les actions pour la maintenace
Route::get('/maintenance', [MaintenanceController::class, 'index'])->middleware('auth')->name('maintenance');
Route::post('/storeMaintenanceIn', [MaintenanceController::class, 'storeInMaintenace'])->middleware('auth')->name('storeInMaintenance');
Route::post('/responseMaintenance', [MaintenanceController::class, 'responseMaintenance'])->middleware('auth')->name('responseMaintenance');
Route::get('/site-en-maintenance', [MaintenanceController::class, 'bladeMaintenance'])->middleware('auth')->name('bladeMaintenance');
Route::post('/end-maintenance', [MaintenanceController::class, 'endMaintenance'])->middleware('auth')->name('endMaintenance');

/* ------------------------------------------------------------------------------ */
/* emballage */

Route::get('categorie-emballage', [emballageController::class, 'categorie_emballage'])->middleware('auth')->name('categorie_emballage');
Route::post('nouvelle_categorie_emballage', [emballageController::class, 'storeCatEmballage'])->middleware('auth')->name('storeCatEmballage');
Route::put('modifier_categorie_emballage', [emballageController::class, 'updateCatEmballage'])->middleware('auth')->name('updateCatEmballage');
Route::post('importer-emballage', [emballageController::class, 'importerEmballage'])->middleware('auth')->name('importer_emballage');

Route::get('emballage', [emballageController::class, 'listeEmballage'])->middleware('auth')->name('emballage');
Route::post('nouvelle_emballage', [emballageController::class, 'storeEmballage'])->middleware('auth')->name('storeEmballage');
Route::put('modifier_emballage', [emballageController::class, 'updateEmballage'])->middleware('auth')->name('updateEmballage');

Route::post('nouvelle_emballage_produit', [CategorieController::class, 'storeEmballageProduit'])->middleware('auth')->name('storeEmballageProduit');

//------COnsignation
Route::get('/consignation', [consignationController::class, 'consignation'])->middleware('auth')->name('consignation');
Route::post('/store-consignation', [consignationController::class, 'storeConsignation'])->middleware('auth')->name('storeConsignation');
Route::get('/edit-consignation/{id}', [consignationController::class, 'editConsignation'])->middleware('auth')->name('editConsignation');
Route::put('/update-consignation/{id}', [consignationController::class, 'updateConsignation'])->middleware('auth')->name('updateConsignation');
Route::get('/get-detail-consignation/{id}', [consignationController::class, 'getDetailConsignation'])->middleware('auth')->name('getDetailConsignation');
Route::get('/consignation/filter', [consignationController::class, 'filterConsignation'])->middleware('auth')->name('filterConsignation');


Route::get('/situation-client', [consignationController::class, 'situationClient'])->middleware('auth')->name('situationClient');
Route::post('/export_excel_situation_client', [consignationController::class, 'export_excel_situation_client'])->middleware('auth');
Route::post('/export_situation_client_pdf', [consignationController::class, 'export_situation_client_pdf'])->middleware('auth');

// Route consignation entree
Route::get('/consignation-entree', [ConsignationEntreeController::class, 'consignation'])->middleware('auth')->name('consignation_entree');
Route::post('/store-consignation-entree', [ConsignationEntreeController::class, 'storeConsignation'])->middleware('auth')->name('storeConsignation_entree');
Route::get('/edit-consignation-entreee/{id}', [ConsignationEntreeController::class, 'editConsignation'])->middleware('auth')->name('editConsignation_entree');
Route::put('/update-consignation-entree/{id}', [ConsignationEntreeController::class, 'updateConsignation'])->middleware('auth')->name('updateConsignation_entree');
Route::get('/get-detail-consignation-entree/{id}', [ConsignationEntreeController::class, 'getDetailConsignation'])->middleware('auth')->name('getDetailConsignation_entree');
Route::get('/consignation_entree/filter', [ConsignationEntreeController::class, 'filterConsignationEntree'])->middleware('auth')->name('filterConsignationEntree');

Route::get('/form-consignation', [consignationController::class, 'showFormRegleConsignation'])->middleware('auth')->name('showFormRegleConsignation');
Route::get('/getConsignationDuClient/{clientId}', [consignationController::class, 'getConsignationDuClient'])->middleware('auth')->name('getConsignationDuClient');
Route::get('/getLigneConsignationSelectionnee/{consignationId}', [consignationController::class, 'getLigneConsignationSelectionnee'])->middleware('auth')->name('getLigneConsignationSelectionnee');
Route::post('/storeDeconsignation', [consignationController::class, 'storeDeconsignation'])->middleware('auth')->name('storeDeconsignation');

// Route Consignation entree
Route::get('/form-consignation-entree', [ConsignationEntreeController::class, 'showFormRegleConsignation'])->middleware('auth')->name('showFormRegleConsignation_entree');
Route::get('/getConsignationDuClient-entree/{clientId}', [ConsignationEntreeController::class, 'getConsignationDuFournisseur'])->middleware('auth')->name('getConsignationDuClient_entree');
Route::get('/getLigneConsignationSelectionnee-entree/{consignationId}', [ConsignationEntreeController::class, 'getLigneConsignationSelectionnee'])->middleware('auth')->name('getLigneConsignationSelectionnee_entree');
Route::post('/storeDeconsignation-entree', [ConsignationEntreeController::class, 'storeDeconsignation'])->middleware('auth')->name('storeDeconsignation_entree');

//Rotre caisse
Route::get('/categorie-depense', [categorieDepenseController::class, 'categorie_depense'])->middleware('auth')->name('categorie_depense');
Route::post('/storeCatDepense', [categorieDepenseController::class, 'storeCatDepense'])->middleware('auth')->name('storeCatDepense');
Route::PUT('/updateCatDepense', [categorieDepenseController::class, 'updateCatDepense'])->middleware('auth')->name('updateCatDepense');

Route::get('/categorie-recette', [categorieDepenseController::class, 'categorie_recette'])->middleware('auth')->name('categorie_recette');
Route::post('/storeCatRecette', [categorieDepenseController::class, 'storeCatRecette'])->middleware('auth')->name('storeCatRecette');
Route::PUT('/updateCatRecette', [categorieDepenseController::class, 'updateCatRecette'])->middleware('auth')->name('updateCatRecette');

Route::get('/caisse', [CaisseController::class, 'index'])->middleware('auth')->name('caisse_index');
Route::post('/ouvrirCaisse', [CaisseController::class, 'ouvrirCaisse'])->middleware('auth')->name('ouvrirCaisse');
Route::get('/getDetailCaisse/{id}', [CaisseController::class, 'getDetailCaisse'])->middleware('auth')->name('getDetailCaisse');
Route::get('/caisse/filter', [CaisseController::class, 'filterCaisse'])->middleware('auth')->name('filterCaisse');

Route::get('/caisse/{id}/fermer', [CaisseController::class, 'fermerCaisse'])->middleware('auth')->name('fermerCaisse');
Route::post('/caisse/{id}/entree', [CaisseController::class, 'enregistrerEntree'])->name('enregistrerEntree');
Route::post('/caisse/{id}/sortie', [CaisseController::class, 'enregistrerSortie'])->name('enregistrerSortie');

Route::get('/caisse/export-pdf/{id}', [CaisseController::class, 'caisseExportPdf'])->name('caisseExportPdf');
Route::get('/caisse/export-excel/{id}', [CaisseController::class, 'caisseExportExcel'])->name('caisseExportExcel');

Route::get('/depense', [DepenseController::class, 'index'])->middleware('auth')->name('depense');
Route::get('/nouvele-depense', [DepenseController::class, 'new_depense'])->middleware('auth')->name('new_depense');
Route::post('/storeDepense', [DepenseController::class, 'storeDepense'])->middleware('auth')->name('storeDepense');
Route::post('/annulerDepense', [DepenseController::class, 'annulerDepense'])->middleware('auth')->name('annulerDepense');
Route::get('/getDetailCaisse2/{id}', [DepenseController::class, 'getDetailCaisse2'])->middleware('auth')->name('getDetailCaisse2');
Route::get('/depense/filter', [DepenseController::class, 'filterDepense'])->middleware('auth')->name('filterDepense');

Route::get('/recette', [RecetteController::class, 'index'])->middleware('auth')->name('recette');
Route::get('/getDetailCaisse3/{id}', [RecetteController::class, 'getDetailCaisse3'])->middleware('auth')->name('getDetailCaisse3');
Route::get('/nouvele-recette', [RecetteController::class, 'new_recette'])->middleware('auth')->name('new_recette');
Route::post('/storeRecette', [RecetteController::class, 'storeRecette'])->middleware('auth')->name('storeRecette');

Route::get('/rapport-caisse', [RapportCaisseController::class, 'index'])->middleware('auth')->name('rapportCaisse');
Route::get('/rapport-rapportCaisseReq', [RapportCaisseController::class, 'rapportCaisseReq'])->middleware('auth')->name('rapportCaisseReq');
Route::post('/export_excel_rapportCaisse', [RapportCaisseController::class, 'export_excel_rapportCaisse'])->middleware('auth');
Route::post('/export_rapport_caisse_pdf', [RapportCaisseController::class, 'export_rapport_caisse_pdf'])->middleware('auth');

