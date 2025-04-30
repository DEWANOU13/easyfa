<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEmballage extends Model
{
    use HasFactory;

    protected $fillable = [
        'Qte_stockee'
    ];
}
