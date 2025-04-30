<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrixVenteProduit extends Model
{
    use HasFactory;
    protected $fillable = [
        'produit_id',
        // 'categorie_produit_id',
        'categorie_client_id',
        'agence_id',
        'date_enregistrement',
        'date_variation_prix',
        'prix',
        'user_id'
    ];
}

