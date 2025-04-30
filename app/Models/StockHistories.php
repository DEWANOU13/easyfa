<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistories extends Model
{
    use HasFactory;


    protected $fillable = [
        'Date' , 'Id_Produit', 'agence_id', 'Id_Magasin', 'Motif', 'Justificatif', 'Quantite', 'Id_Utilisateur'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

}
