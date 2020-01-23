<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    /**
     *
     */
    public function inventories()
    {
        return $this->belongsToMany('App\Inventory')->withPivot('remark');
    }
    /**
     *
     */
    public function tickets()
    {
        return $this->belongsToMany('App\Ticket')->withPivot('remark');
    }
}
