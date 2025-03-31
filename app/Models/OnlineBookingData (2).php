<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OnlineBookingData extends Model
{    
    protected $table = 'online_booking_data';
    protected $fillable = [
        'order_id','booking_data','user_data',
    ];

    protected $hidden = [
        'created_at','updated_at'
    ];
   
}
