<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'Id_Utilisateur',
        'Date_Entree',
        'Reference_Import_Stock',
        'Observations',
        'Id_Agence',
    ];
}
