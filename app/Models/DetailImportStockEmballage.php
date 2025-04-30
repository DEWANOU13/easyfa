<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailImportStockEmballage extends Model
{
    use HasFactory;

    protected $fillable = [
        'Id_Import_Stock_Emballage',
        'Id_Emballage',
        'Id_Magasin',
        'Qte_Importee',
        'Prix_Achat',
        'updated_at',
        'created_at',
    ];
}
