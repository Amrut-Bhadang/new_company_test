<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CartSplitBills extends Model
{    
    protected $table = 'cart_split_bills';
    protected $fillable = [
        'parent_cart_id','user_id','contact_name','contact_number','product_id','amount','status','created_at',
    ];

    protected $hidden = [
        'updated_at'
    ];
   
}
