<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
        'Qte_stockee'
        ];

    public function produit(): BelongsTo{
        return $this->belongsTo(Produit::class, 'Id_Produit');
    }

    public function stockHistories()
    {
        return $this->hasMany(stockHistories::class)->orderBy('created_at', 'desc');
    }

}
