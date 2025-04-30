<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Magasin extends Model
{
    use HasFactory;
    protected $fillable = [
        'NomMagasin',
        'agence_id',
    ];

    public function agence(): BelongsTo{
        return $this->belongsTo(Agence::class);
    }

    public function userCree(): BelongsTo{
        return $this->belongsTo(User::class, 'Enregistrer_par');
    }

    public function userModifi(): BelongsTo{
        return $this->belongsTo(User::class, 'Modifier_par');
    }
}
