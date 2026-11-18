<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $timestamps = false;

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
