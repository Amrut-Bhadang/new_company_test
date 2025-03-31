<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantLang;
use App\Models\Restaurant;
use App\Models\BrandLang;
use App\Models\Rating;
use App\Models\Orders;
use App\Models\RestaurantMode;
use App\Models\RestaurantsTiming;
use App\Models\Holiday;
use App\Models\Modes;
use App\Models\RestaurantImages;
use DB;
use Auth,App;

class Restaurant  extends Model
{    
	protected  $appends = ['brand_name','avg_rating','Total_rating','total_income','mode_name','qr_code','is_open', 'restaurant_images'];

    protected $fillable = [
        'brand_id','tag_line','name','is_main_branch','status','id','main_category_id','buy_one_get_one','video','avg_rating','kp_percent','is_on'
    ];

    protected $hidden = [
        'updated_at',
    ]; 
    
    protected  $table = 'restaurants';

    public function getQrCodeAttribute($value){
      return $code = 'DINEIN-'.$this->id;
    }

    public function getModeNameAttribute($value) 
    {
       $mode_ids = RestaurantMode::where(['restaurant_id'=>$this->id])->pluck('mode_id')->toArray();
       //dd($product_ids);
       $mode_names = Modes::whereIn('id',$mode_ids)->pluck('name')->toArray();
       if ($mode_names) {
          return implode(', ', $mode_names);
       } else {
            return '';
       }
    }

    public function getIsOpenAttribute($value) 
    {
      $week[0] = 'sunday';
      $week[1] = 'monday';
      $week[2] = 'tuesday';
      $week[3] = 'wednesday';
      $week[4] = 'thursday';
      $week[5] = 'friday';
      $week[6] = 'saturday';
      $date = new \DateTime();

      $tz = new \DateTimeZone('Asia/Kolkata');
      $dt = new \DateTime(date('Y-m-d H:i:s'));
      $dt->setTimezone($tz);
      $dateNew = $dt->format('Y-m-d H:i:s');


      /*$timeZone = $date->getTimezone();
      $timeZoneName = $timeZone->getName();*/
      // $dateNew = date('Y-m-d H:i:s');
      $dateNewFormat = $dt->format('Y-m-d');
      $dayofweek = date('w', strtotime($dateNew));
      $currentTime = $dt->format('H:i');
      $dayName = $week[$dayofweek];

      $restroTime = RestaurantsTiming::where(['restro_id'=>$this->id, 'day'=>$dayName])->first();

      if ($restroTime) {

        if ($restroTime->is_close == 'Yes') {
          return 0;

        } else {
          // $checkHoliday = Holiday::where(['restaurant_id'=>$this->id])->where('start_date_time', '>=', $dateNewFormat)->whereDate('end_date_time', '<=', $dateNewFormat)->first();
          // $query->whereRaw('"'.$dateNewFormat.'" between `start_date_time` and `end_date_time`');
          $checkHoliday = Holiday::where(['restaurant_id'=>$this->id])->where(function($query) use ($dateNewFormat){
                 $query->whereDate('start_date_time', '<=', $dateNewFormat);
                 $query->whereDate('end_date_time', '>=', $dateNewFormat);
             })->first();

          // $checkHoliday = Holiday::where(['restaurant_id'=>$this->id])->whereRaw('? between start_date_time and end_date_time', [$dateNewFormat])->first();
          //$checkHoliday = Holiday::where('start_date_time','>=',$dateNewFormat)->where('end_date_time','<=',$dateNewFormat)->get()->toArray();
          // echo "<pre> ".$dateNewFormat; print_r($checkHoliday); die;

          if ($checkHoliday) {
            return 0;

          } else {

            if ($restroTime->start_time <= date('H:i', strtotime($currentTime)) && $restroTime->end_time >= date('H:i', strtotime($currentTime))) {
              return 1;

            } else {
              return 0;
            }
          }
        }

      } else {
        return 0;
      }
       /*$mode_ids = RestaurantsTiming::where(['restaurant_id'=>$this->id])->pluck('mode_id')->toArray();
       //dd($product_ids);
       $mode_names = Modes::whereIn('id',$mode_ids)->pluck('name')->toArray();
       if ($mode_names) {
          return implode(', ', $mode_names);
       } else {
            return '';
       }*/
    }

    public function getFilePathAttribute($value)
    {
    	if ($value) {
      		return url('uploads/user/'.$value);

    	} else {
      		return url('images/no-image-available.png');
    	}
    }

    public function getLogoAttribute($value)
    {
    	if ($value) {
      		return url('uploads/user/'.$value);
    	} else {
      		return url('images/no-image-available.png');
    	}
    }

    public function getDocumentAttribute($value)
    {
      if ($value) {
          return url($value);
      } else {
          return url('images/no-image-available.png');
      }
    }

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  RestaurantLang::select('name')->where(['restaurant_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getTagLineAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  RestaurantLang::select('tag_line')->where(['restaurant_id'=>$this->id,'lang'=>$locale])->first();
      return $data->tag_line ?? $value;
    }

    public function getBrandNameAttribute($value)
    {
      $locale = App::getLocale();
      $data =  BrandLang::select('name')->where(['brand_id'=>$this->brand_id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getAvgRatingAttribute($value)
    {
      $locale = App::getLocale();
      $data =  Rating::select(DB::raw( 'AVG( rating ) as rating'))->where(['restaurant_id'=>$this->id])->first();
      $newdata = number_format($data->rating, 1);

      $updateRate['avg_rating'] = $newdata;
      Restaurant::where('id',$this->id)->update($updateRate);
      // number_format($data->rating,1);
      return $newdata ?? 0.0;
    }

    public function getTotalRatingAttribute($value)
    {
      $data =  Rating::where(['restaurant_id'=>$this->id])->count();
      return $data ?? 0.0;
    }

    public function getRestaurantImagesAttribute($value)
    {
      $data =  RestaurantImages::where(['restaurant_id'=>$this->id])->get();
      return $data;
    }

    public function getTotalIncomeAttribute($value)
    {
      return 'QAR '.Orders::where('restaurant_id',$this->id)->where('order_status','Complete')->sum('amount');

    }

    

}
