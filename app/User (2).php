<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\UserDevice;
use App\Models\Media;
use App\Models\Orders;
use App\Models\Country;
use App\Models\CourtBooking;
use App\Models\UserKiloPoints;
use App\Models\UserWallets;
use DB;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
	use Notifiable;
	use HasRoles;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	// protected  $appends = ['total_dish','designation','country','total_order', 'total_points','total_wallet','total_trans_amt','last_ordered_date'];
	protected  $appends = [];
	protected $fillable = [
		'id', 'name', 'email', 'password', 'type', 'first_name', 'last_name', 'gender', 'mobile', 'image', 'address', 'country_code', 'status','is_facility_owner','show_post_method', 'genres', 'latitude', 'longitude', 'food_license', 'license_number', 'license_image', 'social_type', 'social_id', 'parent_chef_id', 'restaurant_id', 'gift_user_id', 'gift_access_key', 'gift_secret_key', 'callback_url','is_deleted','created_at','updated_at','deleted_at'
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
		'email_verified_at' => 'datetime',
	];

	/**
	 * Get the identifier that will be stored in the subject claim of the JWT.
	 *
	 * @return mixed
	 */
	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Return a key value array, containing any custom claims to be added to the JWT.
	 *
	 * @return array
	 */
	public function getJWTCustomClaims()
	{
		return [];
	}

	public function getImageAttribute($value)
	{
		if ($value) {
			if ($this->image_type == 'url') {
				return $value;
			} else {
				return url('uploads/user/' . $value);
			}
		} else {
			return url('images/default_user.png');
		}
	}

	public function getMedia()
	{
		return $this->hasMany(Media::class, 'table_id', 'id')->where('table_name', 'User');
	}

	public function devices()
    {
        return $this->hasMany('App\Models\UserDevice','user_id');
	}

	public function getCountryAttribute($value)
	{
		$countryName = Country::select('name')->where(['phonecode' => $this->country_code])->first();
		if ($countryName) {
			$countryName = $countryName->name;
		}
		return $countryName;
	}
	public function courtBookingDetail()
    {
		return $this->belongsTo(CourtBooking::class, 'id', 'user_id');
	}
}
