<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgenceUser extends Model
{
    use HasFactory;

    protected $fillable =[
        'agence_id',
        'user_id',
    ];

    public function agence(): BelongsTo{
        return $this->belongsTo(Agence::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
