<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CartParent extends Model
{    
    protected $table = 'cart_parent';

    protected $fillable = [
        'discount_code','discount_percent','discount_amount','user_id','amount','org_amount','order_type','pick_type','pick_datetime','address_id','contact_name','contact_number'
    ];

    protected $hidden = [
        'updated_at',
    ];  
}
