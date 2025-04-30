<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    public function categorie_client(): BelongsTo{
        return $this->belongsTo(CategorieClient::class, 'Categorie_client_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }


    protected $fillable = [
        'Denomination_sociale',
        'user_id',
    ];
}
