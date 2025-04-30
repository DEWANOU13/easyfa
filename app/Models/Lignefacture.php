<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lignefacture extends Model
{
    use HasFactory;

    public function facture(): BelongsTo{
        return $this->belongsTo(Facture::class);
    }

    public function stock(): BelongsTo{
        return $this->belongsTo(Stock::class, 'stocks_id');
    }
}
