<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class groupeAction extends Model
{
    use HasFactory;

    protected $fillable = ['groupe_id', 'action_id', 'created_at', 'updated_at'];
}
