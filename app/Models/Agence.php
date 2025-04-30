<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agence extends Model
{
    use HasFactory;
    protected $fillable = [
        'NomAgence',
        'EnActivite',
        'titre_signataire_facture',
        'nom_signataire',
        'create_user_id',
        'update_user_id'
    ];

    public function userCree(): BelongsTo{
        return $this->belongsTo(User::class, 'create_user_id');
    }

    public function userMod(): BelongsTo{
        return $this->belongsTo(User::class, 'update_user_id');
    }
}
