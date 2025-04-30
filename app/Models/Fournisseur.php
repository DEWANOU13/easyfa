<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;
    protected $fillable = [
        'DenominationSociale',
        'AdresseFournisseur',
        'TelephoneFixe',
        'TelephoneMobile',
        'AdresseMail',
        'Pays',
        'NumeroIfu',
        'Statut_fournisseur',
        'user_id',

    ];
}
