<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'IdFacture_originale',
        'Reference_facture',
        'Date_facture',
        'Compteur_type_facture',
        'Compteur_total',
        'Code_type_facture',
        'Date_signature',
        'Nim_machine',
        'Code_signature',
        'QrCode',
        'Aib',
        'Aib_deductible',
        'Objet_facture',
        'Validite',
        'user_id',
        'client_id',
        'Net_a_payer',
        'agence_id'
    ];

    public function client(): BelongsTo{
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function agence(): BelongsTo{
        return $this->belongsTo(Agence::class);
    }
}
