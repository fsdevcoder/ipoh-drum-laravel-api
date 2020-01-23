<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
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
     * Get the companies belongs to group.
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    /**
     * Get the users for the group.
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot( 'desc','status','created_at','updated_at');
    }
}
