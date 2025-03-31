<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class UsersAddress extends Model
{    
	protected $appends = ['customer_name'];
    protected $table = 'user_address';
    protected $fillable = [
        'user_id','address','latitude','longitude','landmark','building_name','is_defauld_address','building_number','address_type','city','state'
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
