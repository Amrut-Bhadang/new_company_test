<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class UsersCar extends Model
{    
	protected $appends = ['customer_name'];
    protected $table = 'user_car';
    protected $fillable = [
        'user_id','car_color','car_number','car_brand','is_defauld_car'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function getCustomerNameAttribute($value)
    {
        $user = User::select('name')->where('id',$this->user_id)->first();

        if ($user) {
        	return $user->name;
        } else {
        	return '';

        }
    }
   
}
