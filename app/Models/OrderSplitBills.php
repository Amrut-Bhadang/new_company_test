<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderSplitBills extends Model
{    
    protected $table = 'order_split_bills';
    protected $fillable = [
        'order_id','contact_name','contact_number','product_id','amount','status','created_at',
    ];

    protected $hidden = [
        'updated_at'
    ];
   
}
