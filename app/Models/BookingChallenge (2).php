<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
class BookingChallenge extends Model
{    
    // protected  $appends = ['booking_time_slot','is_review'];
    protected $fillable = [
        'user_id','court_booking_id','user_id','status'
    ];

    protected $table = 'booking_challenges';

    protected $hidden = [
        'updated_at',
    ];
    public function userDetails()
  {
    return $this->hasOne(User::class, 'id', 'user_id')->select('id', 'name', 'image', 'image_type','country_code','mobile');
  }
   
}
