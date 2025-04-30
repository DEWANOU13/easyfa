<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriquePrixProduit extends Model
{
    use HasFactory;
    protected $fillable = [
        'produit_id',
        'categorie_client_id',
        'agence_id',
        'date_changement_prix',
        'prix',
        'user_id',
        'Modifier_par',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function categorieClient()
    {
        return $this->belongsTo(CategorieClient::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
