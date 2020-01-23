<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
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
    * Get the modules for the roles.
    */
   public function modules()
   {
       return $this->belongsToMany('App\Module')->withPivot( 'clearance');
   }


   
    /**
     * Get the users on hold this role
     */
    public function users()
    {
        return $this->belongsToMany('App\User','company_role_user')->withPivot('user_id','role_id','company_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
    }

    /**
     * Get the companies got this role
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company','company_role_user')->withPivot('user_id','role_id','company_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
    }
}
