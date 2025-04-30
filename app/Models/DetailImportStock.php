<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailImportStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'Id_Import_Stock',
        'Id_Produit',
        'Id_Magasin',
        'Qte_Importee',
        'Prix_Achat',
    ];
}
