<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CashReceived extends Model
{    
    protected $table  ='cash_received';    
    protected $fillable = [
        'restaurant_id','restaurant_name','total_amount'
    ];

    protected $hidden = [
        'updated_at',
    ];
    
    
}
