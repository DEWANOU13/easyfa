<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailReglement extends Model
{
    use HasFactory;

    public function reglement(): BelongsTo{
        return $this->belongsTo(Reglement::class, 'Id_Reglement');
    }

    public function libelleTypeOperation(): BelongsTo{
        return $this->belongsTo(LibelleTypeOperation::class, 'Id_Libelle_Type_Operation');
    }

    public function facture(): BelongsTo{
        return $this->belongsTo(Facture::class, 'Id_Facture');
    }
}