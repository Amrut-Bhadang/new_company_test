<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CartSplitBillProduct extends Model
{    
    protected $table = 'cart_split_bill_products';
    protected $fillable = [
        'parent_cart_id','user_id','cart_split_bill_id','product_id','amount','created_at',
    ];

    protected $hidden = [
        'updated_at'
    ];
   
}
