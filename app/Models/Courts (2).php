<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourtLang;
use DateTime;
use Illuminate\Support\Facades\App;

class Courts extends Model
{
  protected $appends = ['total_rating', 'available_time_slot'];

  protected $fillable = [
    'facility_id', 'category_id','position', 'court_name', 'image', 'status', 'is_featured', 'address', 'latitude', 'longitude', 'minimum_hour_book', 'hourly_price', 'start_time', 'end_time', 'timeslot', 'average_rating','popular_start_time','popular_day','court_size'
  ];

  protected $hidden = [
    'updated_at', 'deleted_at',
  ];

  public function getImageAttribute($value)
  {
    if ($value) {
      return url('uploads/court/' . $value);
    } else {
      return url('images/default_court.jpg');
    }
  }

  public function getCourtNameAttribute($value)
  {
    $locale = App::getLocale();
    $data =  CourtLang::select('court_name')->where(['court_id' => $this->id, 'lang' => $locale])->first();
    return $data->court_name ?? $value;
  }

  public function getFacilityNameAttribute($value)
  {
    $locale = App::getLocale();
    $data =  FacilityLang::select('name')->where(['facility_id' => $this->facility_id, 'lang' => $locale])->first();
    return $data->name ?? $value;
  }

  public function getAverageRatingAttribute($value)
  {
    return number_format($value, 1);
  }
  public function getTotalRatingAttribute($value)
  {

    $total_rating = Review::where(['status' => 1, 'type' => 1, 'type_id' => $this->id])->count();
    return $total_rating;
  }

  public function getAvailableTimeSlotAttribute($value)
  {
    $popular_time = '';
    $popular_times = CourtPopularTime::where(['court_id'=> $this->id])->get();

    if(count($popular_times)) {
      for ($i=1; $i <= 7; $i++) { 
        $date = date('Y-m-d', strtotime('+'.$i.' day'));
        $tomorrowDay = date('l', strtotime($date));
        $check_popular_times = CourtPopularTime::where(['court_id'=> $this->id, 'day'=>$tomorrowDay])->get();

        if (count($check_popular_times)) {

          foreach ($check_popular_times as $key => $value) {
            /*$checkAlareadyBook = CourtBookingSlot::join('court_booking', 'court_booking.id', 'court_booking_slots.court_booking_id')
              ->where(['court_booking.court_id' => $this->id, 'court_booking.order_status' => 'Pending', 'court_booking.booking_date' => $date])
              ->where('court_booking_slots.booking_start_time', $value->time)
              ->first();*/
            $checkAlareadyBook = CourtBookingSlot::join('court_booking', 'court_booking.id', 'court_booking_slots.court_booking_id')
              ->where(['court_booking.court_id' => $this->id])
              ->where('court_booking_slots.booking_start_time', $value->time)
              ->whereDate('court_booking_slots.booking_start_datetime', '<=', date('Y-m-d', strtotime($date)))
              ->whereDate('court_booking_slots.booking_end_datetime', '>=', date('Y-m-d', strtotime($date)))
              ->first();

            if (!$checkAlareadyBook) {
              $popular_time = $value;
              break;
            }
          }

        }
      }
    }
    // dd($popular_time);
    // get next date by day
      $date = new DateTime();
     
      // $data = (object) $data1;
      if(isset($popular_time) && !empty($popular_time)){
        $popular_day = $popular_time->day;
        $date->modify('next '.$popular_day);
        $popular_date =  $date->format('Y-m-d');
        $data['date'] = $popular_date;
        $data['day'] =  $popular_time->day ?? '';
        $data['time'] = $popular_time->time ??'';
        $data['time_class'] = date('H-i', strtotime($popular_time->time));
        
        return $data;
      }
      $data1= [];
      $data = (object) $data1;
    
    return $data;
  }

  // public function getAvailableTimeSlotAttribute($value)
  // {
  //   $popular_times = CourtPopularTime::where(['court_id'=> $this->id])->get();
    
  //   if(count($popular_times)) {
  //     foreach($popular_times as $popular_time){
  //       $day = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  //       if (in_array($popular_time->day, $day))
  //       {
  //         $popular_time = $popular_time;
  //         break;
  //       }
  //     }
  //   }
  //   // get next date by day
  //     $date = new DateTime();
     
  //     // $data = (object) $data1;
  //     if(isset($popular_time)){
  //       // dd($popular_time);
  //       $popular_day = $popular_time->day;
  //       $date->modify('next '.$popular_day);
  //       $popular_date =  $date->format('Y-m-d');
  //       $data['date'] = $popular_date;
  //       $data['day'] =  $popular_time->day ?? '';
  //       $data['time'] = $popular_time->time ??'';
  //       return $data;
  //     }
  //     $data1= [];
  //     $data = (object) $data1;
    
  //   return $data;
  // }
  // public function getAvailableTimeSlotAttribute($value)
  // {
  //   // add time slot key
  //   $start_time = date('Y-m-d') . ' ' . $this->start_time;
  //   $end_time = date('Y-m-d') . ' ' . $this->end_time;
  //   $interval = $this->timeslot;

  //   $ReturnArray = array(); // Define output
  //   $StartTime    = strtotime($start_time); //Get Timestamp
  //   $EndTime      = strtotime($end_time); //Get Timestamp

  //   $AddMins  = $interval * 60;
  //   $i = 0;
  //   while ($StartTime < $EndTime) //Run loop
  //   {
  //     // $ReturnArray[$i]['start_time'] = date("H:i", $StartTime);
  //     $ReturnArray[] = date("H:i:s", $StartTime);
  //     $StartTime += $AddMins; //Endtime check
  //     // $ReturnArray[$i]['end_time'] = date("H:i", $StartTime);
  //     $i++;
  //   }
  //   $selecttimeslot = $ReturnArray;

  //   $date = date('Y-m-d');
  //   $total_days = 7;

  //   for ($i = 0; $i < $total_days; $i++) {
  //     $newData = date('Y-m-d', strtotime($date . ' + ' . $i . ' days'));
  //     // dd($newData);
  //     $getCourtBookingAvaliable = CourtBooking::where(['court_booking.booking_date' => $newData, 'court_id' => $this->id])->whereNotIn('order_status', ['Completed', 'Cancelled'])
  //       ->join('court_booking_slots', 'court_booking_slots.court_booking_id', 'court_booking.id')
  //       ->pluck('booking_start_time')->toArray();
  //     $result = array_merge(array_diff($selecttimeslot, $getCourtBookingAvaliable), array_diff($getCourtBookingAvaliable, $selecttimeslot));
  //     // dd($selecttimeslot, $getCourtBookingAvaliable, $result);

  //     if (count($result)) {
  //       $timeSlot = $result[0];
  //       break;
  //     } else {
  //       $timeSlot = '';
  //     }
  //   }
  //   $data['date'] = $newData;
  //   $data['day'] = date('l', strtotime($newData));
  //   $data['time'] = $timeSlot;
  //   return $data;
  // }
  public function facilityDetails()
  {
    return $this->hasOne(Facility::class, 'id', 'facility_id');
  }
}
