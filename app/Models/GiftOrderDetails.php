<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use DB;

class GiftOrderDetails extends Model
{
	protected $connection = 'mysql2';
    protected $table = 'gift_order_details';

	protected $appends = [];

	protected $fillable = [
		'user_id','gift_order_id','points','gift_id','qty','gift_varient_id','varient_name','created_at'
	];

	protected $hidden = [
		'updated_at'
	];

		public function getGift(){
		return $this->hasOne(Gift::class, 'id' ,'gift_id');
	}


}