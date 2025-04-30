<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrefixeReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'entre_produit',
        'sortie_produit',
        'transfert_produit',
        'inventaire',
        'proforma',
        'facture',
        'avoir',
        'reglement',
        'libelle_reserves',
        'approvisionnement',
        'reception_approvisionnement',
        'acheminement',
        'reception_acheminement',
        'titre_signataire_facture',
        'nom_signataire',
        'mode_impression',
        'prise_en_compte_reglement',
        'surplus_reglement',
        'type_normalisation',
        'emballage',
        'caisse',
        'pre_cocher_aib',
    ];
}
