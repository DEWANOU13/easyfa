<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Emballage extends Model
{
    use HasFactory;

    public function typeEmballage(): BelongsTo{
        return $this->belongsTo(CategorieEmballage::class, 'Categorie_emballage_id');
    }
}
