<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntreeProduit extends Model
{
    use HasFactory;
    protected $fillable = [
    'Date_Entree',
    'Reference_Entree',
    'Observations',
    
    ];
}
