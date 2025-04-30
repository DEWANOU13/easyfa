<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorierEmb extends Model
{
    use HasFactory;

    protected $fillable = [
        'Id_Inventaire_Emballage',
        'Id_Magasin',
        'Id_Stock',

    ];
}
