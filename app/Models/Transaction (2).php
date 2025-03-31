<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Transaction extends Model
{    
    protected $table = 'transaction';
    protected $fillable = [
        'order_id','user_id','total_amount','chef_amount','celebrity_amount','admin_amount','payment_type','payment_status','card_name','card_number','card_type',
        'card_expiry_date'
    ];

    protected $hidden = [
        'created_at','updated_at'
    ];
   
}
