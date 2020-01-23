<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// HasApiTokens will provide a few helper methods to your model
// which allows you to inspect the authenticated user's token and scopes
use Laravel\Passport\HasApiTokens;

/** @OA\Schema(
 *     title="User"
 * )
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    //Customize login condition. Ex : use username to login
     /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)->where('status', true)->first();
    }

    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="icno", type="string"),
     * @OA\Property(property="tel1", type="string"),
     * @OA\Property(property="tel2", type="string"),
     * @OA\Property(property="address1", type="string"),
     * @OA\Property(property="address2", type="string"),
     * @OA\Property(property="postcode", type="string"),
     * @OA\Property(property="city", type="string"),
     * @OA\Property(property="state", type="string"),
     * @OA\Property(property="country", type="string"),
     * @OA\Property(property="password", type="string"),
     * @OA\Property(property="status", type="string"),
     * @OA\Property(property="last_login", type="string"),
     * @OA\Property(property="last_active", type="string"),
     * @OA\Property(property="remember_token", type="string"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */




    protected $fillable = [
        'uid', 'email', 'name', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'last_login' => 'datetime',
        'last_active' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * user roles
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role','company_role_user')->withPivot('company_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
    }

    /**
     * user companies
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company','company_role_user')->withPivot('role_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
    }


    /**
     * Get the group belongs to user.
     */
    public function groups()
    {
        return $this->belongsToMany('App\Group')->withPivot('desc', 'status', 'created_at', 'updated_at');
    }


     /**
     *  activity that done by user
     */
    public function activitylogs()
    {
        return $this->belongsToMany('App\User','logs','operator_id','affector_id')->withPivot('id', 'action','model','created_at','updated_at');
    }


    /**
     * activity that affected the user
     */
    public function affectedlogs()
    {
        return $this->belongsToMany('App\User','logs','affector_id','operator_id')->withPivot('id', 'action','model','created_at','updated_at');
    }

    /**
     * Get the created payments of the user.
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    /**
     * Get the created sales of the user.
     */
    public function sales()
    {
        return $this->hasMany('App\Sale');
    }

    /**
     * Get the created purchases of the user.
     */
    public function purchases()
    {
        return $this->hasMany('App\Purchase', 'user_id');
    }

    /**
     * Get the created purchases of the user.
     */
    public function productreviews()
    {
        return $this->hasMany('App\ProductReview');
    }
    
    /**
     * Get the created purchases of the user.
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }
    /**
     * Get the created purchases of the user.
     */
    public function storereviews()
    {
        return $this->hasMany('App\StoreReview');
    }

    public function bloggers()
    {
        return $this->hasMany('App\Blogger');
    }

    public function channels()
    {
        return $this->hasMany('App\Channel');
    }
    
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
    
    public function secondcomments()
    {
        return $this->hasMany('App\SecondComment');
    }

    
    public function purchasevideos()
    {
         return $this->belongsToMany('App\Video','user_purchased_video', 'user_id' , 'video_id' )->withPivot('status');

    }
    
    public function channelsales()
    {
        return $this->hasMany('App\ChannelSale');
    }
}
