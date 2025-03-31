<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
class CourtBookingSlot extends Model
{    
    // protected  $appends = ['total_dish'];
    protected $fillable = [
        'court_id','booking_date','status'
    ];

    protected $table = 'court_booking_slots';

    protected $hidden = [
        'updated_at',
    ];
}
