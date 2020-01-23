<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    public function video()
    {
        return $this->hasMany('App\Video');
    }
}
