<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderBookingAmountLogs extends Model
{    
    protected $table  ='order_booking_amount_logs';    
    protected $fillable = [
        'booking_id','actual_amount','amount_type','payment_type','admin_comm_percentage','admin_comm_amount','action_by','action_by_id','reason'
    ];

    protected $hidden = [
        'updated_at',
    ];
    
    
}
