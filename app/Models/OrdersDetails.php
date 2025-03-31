<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OrdersDetails extends Model
{    
    protected $table = 'order_details';
    protected $fillable = [
        'order_id','product_id','user_id','qty','amount','product_price','admin_amount','points','created_at'
    ];

    protected $hidden = [
        'updated_at'
    ];

}
