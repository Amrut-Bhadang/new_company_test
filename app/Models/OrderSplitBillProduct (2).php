<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderSplitBillProduct extends Model
{    
    protected $table = 'order_split_bill_products';
    protected $fillable = [
        'order_id','user_id','order_split_bill_id','product_id','amount','product_price','created_at',
    ];

    protected $hidden = [
        'updated_at'
    ];
   
}
