<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Company"
 * )
 */
class Company extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="company_type_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="img", type="string"),
     * @OA\Property(property="regno", type="string"),
     * @OA\Property(property="tel1", type="string"),
     * @OA\Property(property="tel2", type="string"),
     * @OA\Property(property="fax1", type="string"),
     * @OA\Property(property="fax2", type="string"),
     * @OA\Property(property="email1", type="string"),
     * @OA\Property(property="email2", type="string"),
     * @OA\Property(property="address1", type="string"),
     * @OA\Property(property="address2", type="string"),
     * @OA\Property(property="postcode", type="string"),
     * @OA\Property(property="city", type="string"),
     * @OA\Property(property="state", type="string"),
     * @OA\Property(property="country", type="string"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'hasbranch' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the companytype for the company.
     */
    public function companytype()
    {
        return $this->belongsTo('App\CompanyType', 'company_type_id');
    }

    /**
     * Get the groups for the company type.
     */
    public function groups()
    {
        return $this->hasMany('App\Group');
    }

     /**
     * company roles
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role','company_role_user')->withPivot('user_id','role_id','company_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
    }

    /**
     * company users
     */
    public function users()
    {
        return $this->belongsToMany('App\User','company_role_user')->withPivot('user_id','role_id','company_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
    }

    /**
     * Get the inventories of the company.
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }

    
    /**
     * Get the bloggers of the company.
     */
    public function bloggers()
    {
        return $this->hasMany('App\Blogger');
    }
    
    /**
     * Get the channels of the company.
     */
    public function channels()
    {
        return $this->hasMany('App\Channel');
    }
}
