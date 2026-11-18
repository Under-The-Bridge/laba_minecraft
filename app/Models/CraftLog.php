<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
