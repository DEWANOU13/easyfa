<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        // Définir la longueur par défaut des chaînes à 191 caractères
        Schema::defaultStringLength(191);

        $this->registerPolicies();

        // Facturation
        Gate::define('consulter-proformas', function () { return authorizeAccess(1); });
        Gate::define('creer-proforma', function () { return authorizeAccess(2); });
        Gate::define('modifier-proforma', function () { return authorizeAccess(3); });
        Gate::define('consulter-factures', function () { return authorizeAccess(4); });
        Gate::define('creer-facture', function () { return authorizeAccess(5); });
        Gate::define('annuler-facture', function () { return authorizeAccess(6); });
        Gate::define('normaliser-facture', function () { return authorizeAccess(7); });
        Gate::define('consulter-avoirs', function () { return authorizeAccess(8); });
        Gate::define('creer-avoir', function () { return authorizeAccess(9); });
        Gate::define('normaliser-avoir', function () { return authorizeAccess(10); });
        Gate::define('dupliquer-proforma', function () { return authorizeAccess(77); });
        Gate::define('convertir-proforma-en-facture', function () { return authorizeAccess(78); });
        Gate::define('imprimer-proforma', function () { return authorizeAccess(79); });
        Gate::define('imprimer-facture-en-A4', function () { return authorizeAccess(80); });
        Gate::define('imprimer-facture-en-A5', function () { return authorizeAccess(81); });
        Gate::define('imprimer-facture-en-A8', function () { return authorizeAccess(82); });
        Gate::define('bordereau-livraison', function () { return authorizeAccess(83); });
        Gate::define('invalider-avoir', function () { return authorizeAccess(84); });
        Gate::define('imprimer-avoir-en-A4', function () { return authorizeAccess(85); });
        Gate::define('imprimer-avoir-en-A5', function () { return authorizeAccess(86); });
        Gate::define('imprimer-avoir-en-A8', function () { return authorizeAccess(87); });

        Gate::define('voir-taxes', function () { return authorizeAccess(92); });
        Gate::define('creer-taxe', function () { return authorizeAccess(93); });
        Gate::define('modifier-taxe', function () { return authorizeAccess(94); });

        Gate::define('voir-impression-prorformas', function () { return authorizeAccess(177); });
        Gate::define('exporter-excel-proformas', function () { return authorizeAccess(178); });
        Gate::define('imprimer-proformas', function () { return authorizeAccess(179); });

        Gate::define('voir-impression-factures', function () { return authorizeAccess(180); });
        Gate::define('exporter-excel-factures', function () { return authorizeAccess(181); });
        Gate::define('imprimer-factures', function () { return authorizeAccess(182); });

        Gate::define('voir-statistique-globale-factures', function () { return authorizeAccess(183); });
        Gate::define('exporter-statistique-globale-excel-factures', function () { return authorizeAccess(184); });
        Gate::define('imprimer-statistique-globale-facture', function () { return authorizeAccess(185); });

        Gate::define('voir-statistique-detaillee-factures', function () { return authorizeAccess(186); });
        Gate::define('exporter-statistique-detaillee-excel-factures', function () { return authorizeAccess(187); });
        Gate::define('imprimer-statistique-detaillee-factures', function () { return authorizeAccess(188); });

        Gate::define('voir-impression-avoirs', function () { return authorizeAccess(189); });
        Gate::define('exporter-excel-avoirs', function () { return authorizeAccess(190); });
        Gate::define('imprimer-avoirs', function () { return authorizeAccess(191); });


        // Parametre
        Gate::define('consulter-liste-agence', function () { return authorizeAccess(11); });
        Gate::define('creer-agence', function () { return authorizeAccess(12); });
        Gate::define('modifier-agence', function () { return authorizeAccess(13); });
        Gate::define('consulter-liste-users', function () { return authorizeAccess(14); });
        Gate::define('creer-user', function () { return authorizeAccess(15); });
        Gate::define('modifier-user', function () { return authorizeAccess(16); });
        Gate::define('consulter-liste-groupe', function () { return authorizeAccess(17); });
        Gate::define('creer-groupe', function () { return authorizeAccess(18); });
        Gate::define('modifier-groupe', function () { return authorizeAccess(19); });
        Gate::define('droit-acces', function () { return authorizeAccess(20); });
        Gate::define('attribuer-agence', function () { return authorizeAccess(21); });
        Gate::define('show-liste-facture', function () { return authorizeAccess(22); });
        Gate::define('parametres', function () { return authorizeAccess(68); });
        Gate::define('importation', function () { return authorizeAccess(69); });

        Gate::define('affectation-groupe-utilisateur', function() { return authorizeAccess(315); });
        Gate::define('affectation-droit-utilisateur', function() { return authorizeAccess(316); });

        Gate::define('voir-entete-pied-excel', function() { return authorizeAccess(317); });
        Gate::define('voir-entete-pied-pdf', function() { return authorizeAccess(318); });


        // Produits
        Gate::define('consulter-liste-categorie-produit', function () { return authorizeAccess(23); });
        Gate::define('creer-categorie-produit', function () { return authorizeAccess(24); });
        Gate::define('modifier-categorie-produit', function () { return authorizeAccess(25); });
        Gate::define('consulter-unite-comptage', function () { return authorizeAccess(26); });
        Gate::define('creer-unite-comptage', function () { return authorizeAccess(27); });
        Gate::define('modifier-unite-comptage', function () { return authorizeAccess(28); });
        Gate::define('consulter-liste-produits', function () { return authorizeAccess(29); });
        Gate::define('creer-produit', function () { return authorizeAccess(30); });
        Gate::define('modifier-produit', function () { return authorizeAccess(31); });
        Gate::define('consulter-prix-vente', function () { return authorizeAccess(32); });
        Gate::define('creer-prix-vente', function () { return authorizeAccess(33); });
        Gate::define('modifier-prix-vente', function () { return authorizeAccess(34); }); // Ambigu
        Gate::define('exporter-produits', function () { return authorizeAccess(73); });
        Gate::define('importer-produits-actifs', function () { return authorizeAccess(74); });
        Gate::define('importer-produits-inactifs', function () { return authorizeAccess(75); });
        Gate::define('imprimer-prestations-actives', function () { return authorizeAccess(76); });

        Gate::define('liste-groupe-categorie-produit', function () { return authorizeAccess(95); });
        Gate::define('creer-groupe-categorie-produit', function () { return authorizeAccess(96); });
        Gate::define('modifier-groupe-categorie-produit', function () { return authorizeAccess(97); });
        Gate::define('associer-categorie-et-groupe-categorie-produit', function () { return authorizeAccess(98); });
        Gate::define('revoquer-associtaion-categorie-et-groupe-categorie-produit', function () { return authorizeAccess(99); });
        Gate::define('importer-prix-produits', function () { return authorizeAccess(133); });

        Gate::define('releve-sortie-produit', function () { return authorizeAccess(147); });

        Gate::define('exporter-excel-unite-comptage', function () { return authorizeAccess(170); });
        Gate::define('imprimer-unite-comptage', function () { return authorizeAccess(171); });

        Gate::define('historique-fixation-prix', function () { return authorizeAccess(172); });
        Gate::define('exporter-excel-prix-produit', function () { return authorizeAccess(173); });
        Gate::define('exporter-format-importation', function () { return authorizeAccess(174); });
        Gate::define('imprimer-prix-produit', function () { return authorizeAccess(175); });


        // Reglements
        Gate::define('consulter-reglement', function () { return authorizeAccess(35); });
        Gate::define('enregistrer-reglement', function () { return authorizeAccess(36); });
        Gate::define('annuler-reglement', function () { return authorizeAccess(37); });
        Gate::define('imprimer-reglement', function () { return authorizeAccess(88); });
        Gate::define('exporter-reglement', function () { return authorizeAccess(195); });

        Gate::define('voir-impression-reglement', function () { return authorizeAccess(196); });
        Gate::define('exporter-excel-impression-reglement', function () { return authorizeAccess(197); });
        Gate::define('imprimer-impression-reglement', function () { return authorizeAccess(198); });

        Gate::define('voir-impression-reglement-periode', function () { return authorizeAccess(199); });
        Gate::define('exporter-excel-impression-reglement-periode', function () { return authorizeAccess(200); });
        Gate::define('imprimer-impression-reglement-periode', function () { return authorizeAccess(201); });

        // Stocks
        Gate::define('consulter-liste-magasin', function () { return authorizeAccess(38); });
        Gate::define('creer-magasin', function () { return authorizeAccess(39); });
        Gate::define('modifier-magasin', function () { return authorizeAccess(40); });
        Gate::define('consulter-stock-produit', function () { return authorizeAccess(41); });
        Gate::define('consulter-entree-produit', function () { return authorizeAccess(42); });
        Gate::define('effectuer-entrer-produit', function () { return authorizeAccess(43); });
        Gate::define('consulter-sortie-produit', function () { return authorizeAccess(44); });
        Gate::define('effectuer-sortie-produit', function () { return authorizeAccess(45); });
        Gate::define('consulter-transfert-produit', function () { return authorizeAccess(46); });
        Gate::define('effectuer-transfert-produit', function () { return authorizeAccess(47); });
        Gate::define('consulter-inventaire', function () { return authorizeAccess(48); });
        Gate::define('effectuer-inventaire', function () { return authorizeAccess(49); });
        Gate::define('boucler-inventaire', function () { return authorizeAccess(50); });

        Gate::define('stock-produit-excel', function () { return authorizeAccess(286); });
        Gate::define('stock-produit-emballage-excel', function () { return authorizeAccess(287); });
        Gate::define('stock-produit-format-importation', function () { return authorizeAccess(288); });
        Gate::define('stock-produit-pdf', function () { return authorizeAccess(289); });
        Gate::define('stock-produit-emballage-pdf', function () { return authorizeAccess(290); });
        Gate::define('importer-stock-produit', function () { return authorizeAccess(291); });

        Gate::define('historique-stock-produit', function () { return authorizeAccess(292); });
        Gate::define('historique-mise-jour-stock-produit', function () { return authorizeAccess(293); });
        Gate::define('produit-sous-seuil-stock', function () { return authorizeAccess(294); });

        Gate::define('exporter-entree-produit', function () { return authorizeAccess(295); });
        Gate::define('importer-entree-produit', function () { return authorizeAccess(296); });

        Gate::define('voir-impression-entree-produit', function () { return authorizeAccess(297); });
        Gate::define('entree-produit-excel', function () { return authorizeAccess(298); });
        Gate::define('entree-produit-imprimer', function () { return authorizeAccess(299); });

        Gate::define('sortie-produit-excel', function () { return authorizeAccess(300); });
        Gate::define('exporter-impression-sortie-produit', function () { return authorizeAccess(301); });
        Gate::define('imprimer-impression-sortie-produit', function () { return authorizeAccess(302); });

        Gate::define('exporter-impression-inventaire-produit', function () { return authorizeAccess(303); });
        Gate::define('imprimer-impression-inventaire-produit', function () { return authorizeAccess(304); });

        Gate::define('transfert-produit-excel', function () { return authorizeAccess(305); });
        Gate::define('exporter-impression-transfert-produit', function () { return authorizeAccess(306); });
        Gate::define('imprimer-impression-transfert-produit', function () { return authorizeAccess(307); });

        //Approvisionnement
        Gate::define('consulter-liste-approvisionnement', function () { return authorizeAccess(51); });
        Gate::define('effectuer-approvisionnement', function () { return authorizeAccess(52); });
        Gate::define('consulter-liste-reception-approvisionnement', function () { return authorizeAccess(53); });
        Gate::define('receptionner-approvisionnement', function () { return authorizeAccess(54); });

        Gate::define('consulter-liste-acheminenment', function () { return authorizeAccess(134); });
        Gate::define('effectuer-acheminement', function () { return authorizeAccess(135); });
        Gate::define('consulter-liste-reception-acheminenment', function () { return authorizeAccess(136); });
        Gate::define('receptionner-acheminement', function () { return authorizeAccess(137); });
        //Fin approvisionnement

        // Approvisionnement et reception emallage
        Gate::define('consulter-liste-approvisionnement-emballage', function () { return authorizeAccess(138); });
        Gate::define('effectuer-approvisionnement-emballage', function () { return authorizeAccess(139); });
        Gate::define('consulter-liste-reception-approvisionnement-emballage', function () { return authorizeAccess(140); });
        Gate::define('receptionner-approvisionnement-emballage', function () { return authorizeAccess(141); });

        Gate::define('consulter-liste-acheminenment-emballage', function () { return authorizeAccess(142); });
        Gate::define('effectuer-acheminement-emballage', function () { return authorizeAccess(143); });
        Gate::define('consulter-liste-reception-acheminenment-emballage', function () { return authorizeAccess(144); });
        Gate::define('receptionner-acheminement-emballage', function () { return authorizeAccess(145); });
        // Fin approvisionement et reception emballage

        Gate::define('imprimer-liste-transfert', function () { return authorizeAccess(89); });
        Gate::define('imprimer-liste-entrees-produits', function () { return authorizeAccess(90); });
        Gate::define('imprimer-liste-sorties-produits', function () { return authorizeAccess(91); });

        // Tiers
        Gate::define('consulter-liste-fournisseurs', function () { return authorizeAccess(55); });
        Gate::define('creer-fournisseur', function () { return authorizeAccess(56); });
        Gate::define('modifier-fournisseur', function () { return authorizeAccess(57); });
        Gate::define('consulter-client', function () { return authorizeAccess(58); });
        Gate::define('creer-client', function () { return authorizeAccess(59); });
        Gate::define('modifier-client', function () { return authorizeAccess(60); });
        Gate::define('consulter-categorie-client', function () { return authorizeAccess(61); });
        Gate::define('creer-categorie-client', function () { return authorizeAccess(62); });
        Gate::define('modifier-categorie-client', function () { return authorizeAccess(63); });
        Gate::define('exporter-fournisseur', function () { return authorizeAccess(70); });
        Gate::define('exporter-client', function () { return authorizeAccess(71); });
        Gate::define('importer-compte-client', function () { return authorizeAccess(72); });

        Gate::define('exporter-excel-categorie-client', function () { return authorizeAccess(166); });
        Gate::define('imprimer-categorie-client', function () { return authorizeAccess(167); });
        Gate::define('imprimer-client', function () { return authorizeAccess(168); });
        Gate::define('voir-compte-client', function () { return authorizeAccess(169); });

        // Statistiques
        Gate::define('statistique-achat', function () { return authorizeAccess(64); });

        Gate::define('statistique-achat-cumulee-mois', function () { return authorizeAccess(209); });
        Gate::define('statistique-achat-cumulee-mois-excel', function () { return authorizeAccess(210); });
        Gate::define('statistique-achat-cumulee-mois-pdf', function () { return authorizeAccess(211); });

        Gate::define('statistique-achat-cumulee-categorie', function () { return authorizeAccess(212); });
        Gate::define('statistique-achat-cumulee-categorie-excel', function () { return authorizeAccess(213); });
        Gate::define('statistique-achat-cumulee-categorie-pdf', function () { return authorizeAccess(214); });

        Gate::define('statistique-achat-cumulee-fournisseur', function () { return authorizeAccess(215); });
        Gate::define('statistique-achat-cumulee-fournisseur-excel', function () { return authorizeAccess(216); });
        Gate::define('statistique-achat-cumulee-fournisseur-pdf', function () { return authorizeAccess(217); });

        Gate::define('statistique-achat-cumulee-agence', function () { return authorizeAccess(218); });
        Gate::define('statistique-achat-cumulee-agence-excel', function () { return authorizeAccess(219); });
        Gate::define('statistique-achat-cumulee-agence-pdf', function () { return authorizeAccess(220); });

        Gate::define('statistique-vente', function () { return authorizeAccess(65); });

        Gate::define('statistique-vente-valeur-cumulee-jour', function () { return authorizeAccess(221); });
        Gate::define('statistique-vente-valeur-cumulee-jour-excel', function () { return authorizeAccess(222); });
        Gate::define('statistique-vente-valeur-cumulee-jour-pdf', function () { return authorizeAccess(223); });

        Gate::define('statistique-vente-valeur-cumulee-categorie', function () { return authorizeAccess(224); });
        Gate::define('statistique-vente-valeur-cumulee-categorie-excel', function () { return authorizeAccess(225); });
        Gate::define('statistique-vente-valeur-cumulee-categorie-pdf', function () { return authorizeAccess(226); });

        Gate::define('statistique-vente-valeur-cumulee-client', function () { return authorizeAccess(227); });
        Gate::define('statistique-vente-valeur-cumulee-client-excel', function () { return authorizeAccess(228); });
        Gate::define('statistique-vente-valeur-cumulee-client-pdf', function () { return authorizeAccess(229); });

        Gate::define('statistique-vente-valeur-cumulee-produit', function () { return authorizeAccess(230); });
        Gate::define('statistique-vente-valeur-cumulee-produit-excel', function () { return authorizeAccess(231); });
        Gate::define('statistique-vente-valeur-cumulee-produit-pdf', function () { return authorizeAccess(232); });

        Gate::define('statistique-vente-valeur-cumulee-agence', function () { return authorizeAccess(233); });
        Gate::define('statistique-vente-valeur-cumulee-agence-excel', function () { return authorizeAccess(234); });
        Gate::define('statistique-vente-valeur-cumulee-agence-pdf', function () { return authorizeAccess(235); });

        Gate::define('statistique-vente-valeur-journal-vente', function () { return authorizeAccess(236); });
        Gate::define('statistique-vente-valeur-journal-vente-excel', function () { return authorizeAccess(237); });
        Gate::define('statistique-vente-valeur-journal-vente-pdf', function () { return authorizeAccess(238); });

        Gate::define('statistique-vente-valeur-facture-annulee-avoir', function () { return authorizeAccess(239); });
        Gate::define('statistique-vente-valeur-facture-annulee-avoir-excel', function () { return authorizeAccess(240); });
        Gate::define('statistique-vente-valeur-facture-annulee-avoir-pdf', function () { return authorizeAccess(241); });

        Gate::define('statistique-vente-valeur-facture-vente-utilisateur', function () { return authorizeAccess(242); });
        Gate::define('statistique-vente-valeur-facture-vente-utilisateur-excel', function () { return authorizeAccess(243); });
        Gate::define('statistique-vente-valeur-facture-vente-utilisateur-pdf', function () { return authorizeAccess(244); });

        Gate::define('statistique-vente-valeur', function () { return authorizeAccess(245); });
        Gate::define('statistique-vente-quantite', function () { return authorizeAccess(246); });

        Gate::define('statistique-vente-quantite-cumulee-categorie', function () { return authorizeAccess(247); });
        Gate::define('statistique-vente-quantite-cumulee-categorie-excel', function () { return authorizeAccess(248); });
        Gate::define('statistique-vente-quantite-cumulee-categorie-pdf', function () { return authorizeAccess(249); });

        Gate::define('statistique-vente-quantite-cumulee-produit', function () { return authorizeAccess(250); });
        Gate::define('statistique-vente-quantite-cumulee-produit-excel', function () { return authorizeAccess(251); });
        Gate::define('statistique-vente-quantite-cumulee-produit-pdf', function () { return authorizeAccess(252); });

        Gate::define('statistique-stock', function () { return authorizeAccess(66); });

        Gate::define('statistique-stock-magasin', function () { return authorizeAccess(253); });
        Gate::define('statistique-stock-magasin-excel', function () { return authorizeAccess(254); });
        Gate::define('statistique-stock-magasin-pdf', function () { return authorizeAccess(255); });

        Gate::define('statistique-stock-consolide', function () { return authorizeAccess(256); });
        Gate::define('statistique-stock-consolide-excel', function () { return authorizeAccess(257); });
        Gate::define('statistique-stock-consolide-pdf', function () { return authorizeAccess(258); });

        Gate::define('statistique-stock-fiche-consolide', function () { return authorizeAccess(259); });
        Gate::define('statistique-stock-fiche-consolide-excel', function () { return authorizeAccess(260); });
        Gate::define('statistique-stock-fiche-consolide-pdf', function () { return authorizeAccess(261); });

        Gate::define('statistique-marge', function () { return authorizeAccess(67); });

        Gate::define('statistique-marge-cumulee-produit', function () { return authorizeAccess(262); });
        Gate::define('statistique-marge-cumulee-produit-excel', function () { return authorizeAccess(263); });
        Gate::define('statistique-marge-cumulee-produit-pdf', function () { return authorizeAccess(264); });

        Gate::define('statistique-marge-cumulee-jour', function () { return authorizeAccess(265); });
        Gate::define('statistique-marge-cumulee-jour-excel', function () { return authorizeAccess(266); });
        Gate::define('statistique-marge-cumulee-jour-pdf', function () { return authorizeAccess(267); });

        Gate::define('statistique-marge-cumulee-mois', function () { return authorizeAccess(268); });
        Gate::define('statistique-marge-cumulee-mois-excel', function () { return authorizeAccess(269); });
        Gate::define('statistique-marge-cumulee-mois-pdf', function () { return authorizeAccess(270); });

        Gate::define('statistique-marge-cumulee-client', function () { return authorizeAccess(271); });
        Gate::define('statistique-marge-cumulee-client-excel', function () { return authorizeAccess(273); });
        Gate::define('statistique-marge-cumulee-client-pdf', function () { return authorizeAccess(274); });

        Gate::define('voir-rapport-statistique', function () { return authorizeAccess(100); });
        Gate::define('pdf-rapport-statistique', function () { return authorizeAccess(101); });
        Gate::define('excel-rapport-statistique', function () { return authorizeAccess(102); });

        Gate::define('statistique-rapport-vente-produit-sans-marge', function () { return authorizeAccess(275); });

        Gate::define('statistique-rapport-vente-produit', function () { return authorizeAccess(276); });
        Gate::define('statistique-rapport-vente-produit-excel', function () { return authorizeAccess(277); });
        Gate::define('statistique-rapport-vente-produit-pdf', function () { return authorizeAccess(278); });

        Gate::define('statistique-rapport-vente-prestation', function () { return authorizeAccess(279); });
        Gate::define('statistique-rapport-vente-prestation-excel', function () { return authorizeAccess(280); });
        Gate::define('statistique-rapport-vente-prestation-pdf', function () { return authorizeAccess(281); });

        Gate::define('voir-point-de-vente', function () { return authorizeAccess(103); });
        Gate::define('pdf-point-de-vente', function () { return authorizeAccess(104); });
        Gate::define('excel-point-de-vente', function () { return authorizeAccess(105); });

        Gate::define('excel-releve-sorti', function () { return authorizeAccess(282); });
        Gate::define('pdf-releve-sorti', function () { return authorizeAccess(283); });

        Gate::define('statistique-caisse-excel', function () { return authorizeAccess(284); });
        Gate::define('statistique-caisse-pdf', function () { return authorizeAccess(285); });

        // Emballages
        Gate::define('liste-categorie-emballage', function() { return authorizeAccess(106); });
        Gate::define('creer-categorie-emballage', function() { return authorizeAccess(107); });
        Gate::define('modifier-categorie-emballage', function() { return authorizeAccess(108); });
        Gate::define('liste-emballage', function() { return authorizeAccess(109); });
        Gate::define('creer-emballage', function() { return authorizeAccess(110); });
        Gate::define('modifier-emballage', function() { return authorizeAccess(111); });
        Gate::define('importer-liste-emballage', function() { return authorizeAccess(176); });

        Gate::define('voir-inventaire-stock-emballage', function() { return authorizeAccess(112); });
        Gate::define('exporter-pdf-inventaire-stock-emballage', function() { return authorizeAccess(113); });
        Gate::define('exporter-excel-inventaire-stock-emballage', function() { return authorizeAccess(114); });
        Gate::define('voir-liste-entree-emballage', function() { return authorizeAccess(115); });
        Gate::define('entre-emballage', function() { return authorizeAccess(116); });
        Gate::define('exporter-pdf-entre-emballage', function() { return authorizeAccess(117); });
        Gate::define('exporter-excel-entre-emballage', function() { return authorizeAccess(118); });
        Gate::define('voir-liste-sortie-emballage', function() { return authorizeAccess(119); });
        Gate::define('sortie-emballage', function() { return authorizeAccess(120); });
        Gate::define('exporter-pdf-sortie-emballage', function() { return authorizeAccess(121); });
        Gate::define('exporter-excel-sortie-emballage', function() { return authorizeAccess(122); });
        Gate::define('voir-liste-inventaire-emballage', function() { return authorizeAccess(123); });
        Gate::define('faire-inventaire-emballage', function() { return authorizeAccess(124); });
        Gate::define('exporter-pdf-inventaire-emballage', function() { return authorizeAccess(125); });
        Gate::define('exporter-excel-inventaire-emballage', function() { return authorizeAccess(126); });
        Gate::define('transfert-emballage', function() { return authorizeAccess(127); });
        Gate::define('faire-transfert-emballage', function() { return authorizeAccess(128); });
        Gate::define('exporter-pdf-transfert-emballage', function() { return authorizeAccess(129); });
        Gate::define('exporter-excel-transfert-emballage', function() { return authorizeAccess(130); });

        Gate::define('consignation-situation-client', function() { return authorizeAccess(192); });
        Gate::define('regler-consignation', function() { return authorizeAccess(193); });
        Gate::define('regler-consignation-entree', function() { return authorizeAccess(194); });

        Gate::define('voir-impression-entree-emballage', function() { return authorizeAccess(312); });

        Gate::define('voir-impression-sortie-emballage', function() { return authorizeAccess(313); });

        Gate::define('voir-impression-inventaire-emballage', function() { return authorizeAccess(314); });



        // Consignation
        Gate::define('consignation', function() { return authorizeAccess(131); });
        Gate::define('consignation-entree', function() { return authorizeAccess(146); });

        Gate::define('seuil-stock', function() { return authorizeAccess(132); });

        // Caisse
        Gate::define('voir-liste-categorie-depense', function() { return authorizeAccess(148); });
        Gate::define('creer-categorie-depense', function() { return authorizeAccess(149); });
        Gate::define('modifier-categorie-depense', function() { return authorizeAccess(150); });

        Gate::define('voir-liste-depense', function() { return authorizeAccess(151); });
        Gate::define('effectuer-une-depense', function() { return authorizeAccess(152); });

        Gate::define('voir-liste-categorie-recette', function() { return authorizeAccess(153); });
        Gate::define('creer-categorie-recette', function() { return authorizeAccess(154); });
        Gate::define('modifier-categorie-recette', function() { return authorizeAccess(155); });

        Gate::define('voir-liste-recette', function() { return authorizeAccess(156); });
        Gate::define('effectuer-une-recette', function() { return authorizeAccess(157); });

        Gate::define('voir-caisse', function() { return authorizeAccess(158); });
        Gate::define('ouvrir-caisse', function() { return authorizeAccess(159); });
        Gate::define('fermer-caisse', function() { return authorizeAccess(160); });

        Gate::define('statistique-rapport-caisse', function() { return authorizeAccess(161); });
        Gate::define('imprimer-pdf-stock-par-categorie', function() { return authorizeAccess(162); });

        Gate::define('voir-impression-depense', function() { return authorizeAccess(202); });
        Gate::define('voir-impression-recette', function() { return authorizeAccess(203); });

        Gate::define('exporter-caisse-excel', function() { return authorizeAccess(204); });
        Gate::define('exporter-caisse-pdf', function() { return authorizeAccess(205); });

        Gate::define('voir-impression-depense', function() { return authorizeAccess(206); });
        Gate::define('voir-impression-recette', function() { return authorizeAccess(207); });
        Gate::define('voir-impression-caisse', function() { return authorizeAccess(208); });

        Gate::define('exporter-format-importation-stock-emballage', function() { return authorizeAccess(209); });
        Gate::define('importer-stock-emballage', function() { return authorizeAccess(210); });

        Gate::define('historique-stock-emballage', function() { return authorizeAccess(211); });
        Gate::define('historique-mise-a-jour-stock-emballage', function() { return authorizeAccess(211); });
    }
}
