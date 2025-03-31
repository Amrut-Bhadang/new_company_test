<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftCart extends Model
{
	protected $connection = 'mysql2';
    protected $table = 'gift_cart';
    protected $fillable = [
        'shipping_charges','user_id','points','order_type','longitude','latitude','address_id','building_number','building_name','landmark','building_name','address','address_type','is_wallet_use','wallet_amount_used','contact_name','contact_number'
    ];

    protected $hidden = [
        'updated_at',
    ];  
}
