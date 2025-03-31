<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class DeliveryPrice extends Model
{    
    protected $table  ='delivery_price';    
    protected $fillable = [
        'common_commission_percentage','cancellation_charge'
    ];

    protected $hidden = [
        'updated_at',
    ];
    
    
}
