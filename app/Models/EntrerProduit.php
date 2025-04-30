<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrerProduit extends Model
{
    use HasFactory;
    protected $fillable = [
        'Id_Entree_Produit',
        'Qte_Entree',
        'Prix_Achat_Net',
        'Qte_stockee'

        ];
}
