<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use JWTAuth;


class UserBankDetail extends Model
{
  // protected  $appends = ['booking_time_slot', 'is_review', 'paid_amount'];
  protected $fillable = [
    'bank_name', 'bank_address', 'bank_code', 'account_number', 'account_type', 'account_holder_name', 'passbook_image','status','user_id'
  ];

  protected $table = 'user_bank_detail';

  protected $hidden = [
    'updated_at',
  ];
  public function getPassbookImageAttribute($value)
  {
    if ($value) {
      return url('uploads/bank_detail/' . $value);
    } else {
      return url('images/no-image-available.png');
    }
  }

  public function bookingTimeSlots()
  {
    return $this->hasMany(CourtBookingSlot::class, 'court_booking_id', 'id');
  }
  public function bookingChallenges()
  {
    return $this->hasMany(BookingChallenge::class, 'court_booking_id', 'id');
  }
  public function bookingTimeSlot()
  {
    return $this->hasMany(CourtBookingSlot::class, 'court_booking_id', 'id');
  }
  public function userDetails()
  {
    return $this->hasOne(User::class, 'id', 'user_id')->select('id', 'name', 'image', 'image_type');
  }
  public function courtDetails()
  {
    return $this->hasOne(Courts::class, 'id', 'court_id');
  }
}
