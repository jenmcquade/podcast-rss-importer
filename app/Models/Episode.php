<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }
}
