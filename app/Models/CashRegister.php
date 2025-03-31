<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CashRegister extends Model
{    
    protected $table  ='cash_register';    
    protected $fillable = [
        'order_id','driver_id','driver_name','total_amount'
    ];

    protected $hidden = [
        'updated_at',
    ];
    
    
}
