<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'groupe_id'
    ];

    public function groupe(): BelongsTo{
        return $this->belongsTo(Groupe::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
