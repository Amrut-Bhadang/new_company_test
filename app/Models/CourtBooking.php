<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use JWTAuth;


class CourtBooking extends Model
{
  protected  $appends = ['booking_time_slot', 'is_review', 'paid_amount'];
  protected $fillable = [
    'court_id', 'booking_type','booking_date', 'end_booking_date', 'status', 'order_status', 'transaction_id', 'payment_type', 'payment_received_status', 'challenge_type','joiner_payment_status','created_at','updated_at'
  ];

  protected $table = 'court_booking';

  protected $hidden = [
    
  ];
  public function getCourtImageAttribute($value)
  {
    if ($value) {
      return url('uploads/court/' . $value);
    } else {
      return url('images/default_court.jpg');
    }
  }

  public function getBookingTimeSlotAttribute($value)
  {

    $booking_time_slots = CourtBookingSlot::join('court_booking', 'court_booking.id', 'court_booking_slots.court_booking_id')
      // ->join('amenities','amenities.id','facility_amenities.amenity_id')
      ->where('court_booking_slots.court_booking_id', $this->id)
      ->select('court_booking_slots.booking_start_time', 'court_booking_slots.booking_end_time')
      ->get();
    // dd('dd',$booking_time_slots);

    if (count($booking_time_slots)) {
      foreach ($booking_time_slots as $value) {
        $booking_time_slot[] = date('H:i', strtotime($value->booking_start_time)) . ' - ' . date('H:i', strtotime($value->booking_end_time));
      }
      return implode(", ", $booking_time_slot);
    } else {
      return '';
    }
  }
  public function getIsReviewAttribute($value)
  {
    $auth_user = JWTAuth::user();
    if (isset($auth_user)) {
      $review =  Review::where(['order_id' => $this->id, 'user_id' => $auth_user->id, 'type' => 1])->first();
    }
    if (isset($review)) {
      return 'yes';
    } else {
      return 'no';
    }
  }
  public function getPaidAmountAttribute($value)
  {
    $data =  CourtBooking::where(['id' => $this->id])->first();
    if (isset($data) && $data->booking_type == 'challenge') {
      return $data->total_amount / 2;
    } else {
      return $data->total_amount;
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
  public function Shared_challenges()
  {
    return $this->hasMany(SharedChallenge::class, 'court_booking_id', 'id');
  }
}
