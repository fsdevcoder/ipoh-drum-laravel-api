<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Get the roles for modules.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role')->withPivot( 'clearance');
    }
}
