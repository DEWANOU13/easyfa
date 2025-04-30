<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniteComptage extends Model
{
    use HasFactory;
    protected $fillable = ['Code','Libelle','Enregistrer_par'];
}
