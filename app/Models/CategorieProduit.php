<?php

namespace App\Models;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategorieProduit extends Model
{
    use HasFactory;

    protected $fillable = ['Libelle','Enregistrer_par',];

    public function produits(){
        return $this->hasMany(Produit::class);
    }
}