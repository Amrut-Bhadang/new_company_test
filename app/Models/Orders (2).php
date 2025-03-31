<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrdersDetails;
use App\Models\UserKiloPoints;
use App\Models\Rating;
use App\Models\Restaurant;
use App\Models\Modes;
use Auth,App;
use DB;

class Orders extends Model
{    
	// protected  $appends = ['kilo_points','is_rate','product_name','total_order_kp','rest_name','dine_in','car_detail','car_detail_color'];
    protected  $appends = ['booking_slots'];
    protected $table = 'orders';
    protected $fillable = [
        'random_order_id','app_type','user_id','user_name','user_country_code','user_phone_number','user_email','restaurant_id','restaurant_name','restaurant_email','restaurant_phone_number','pickup_point_id','pickup_point_name','pickup_point_email','pickup_point_phone_number','ordered_currency_code','delivery_address','delivery_lat','delivery_long','pickup_point_address','pickup_point_lat','pickup_point_long','discount_amount','shipping_charges','tax_amount','amount','org_amount','admin_amount','admin_commission','order_status','total_kp_received','payment_type','is_wallet_use','restaurant_table_code','driver_name','driver_email','driver_phone_number','status'];

    protected $hidden = [
        'updated_at'
    ];

    public function getBookingSlotsAttribute($value)
    {
        return "09:00-10:00, 10:00-11:00";
    }

    /*public function getPDFAttribute($value)
    {
        if ($value) {
            return url('uploads/order_detail_pdf/'.$value);
        } else {
            return "";
        }
    }

    public function getCarDetailAttribute($value){
        //dd($this->order_type);
        $result = Orders::select('car_color','car_number','car_brand')->where('id',$this->order_id)->first();
        // dd($result->car_color);
        $string = "Car Detail (Color:";
        $string .= $result->car_color ?? '---';
        $string .= " Brand:";
        $string .= $result->car_brand ?? '---';
        $string .= " Number:";
        $string .= $result->car_number ?? '---';
        $string .= ")";
        return $string;

    }

    public function getCarDetailColorAttribute($value){
        //dd($this->order_type);
        $result = Orders::select('car_color','car_number','car_brand')->where('id',$this->order_id)->first();
        // dd($result->car_color);
        $string = "Car Detail (Color:";

        if ($result && $result->car_color) {
            $string .= '<label class="color-box" style="background:'.$result->car_color.'"></label>' ?? '---';

        } else {
            $string .= '---';
        }
        $string .= " Brand:";
        $string .= $result->car_brand ?? '---';
        $string .= " Number:";
        $string .= $result->car_number ?? '---';
        $string .= ")";
        return $string;

    }

    public function getPreparationTimeAttribute($value){

        if ($this->order_type == '') {
            $orderDetail = Orders::where('id',$this->order_id)->first();

            $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$orderDetail->latitude.','.$orderDetail->longitude.'&destinations='.$data['latitude'].','.$data['longitude'].'&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $googleData = json_decode($googleData);
        }
        return '';

    }

    public function getDineInAttribute($value){
        //dd($this->order_type);
        $result = Modes::select('name')->where('id',$this->order_type)->first();
        return $result->name ?? '';
    }

    public function getProductNameAttribute($value) 
    {
       $product_ids = OrdersDetails::where(['order_id'=>$this->order_id])->pluck('product_id')->toArray();
       //dd($product_ids);
       $product_names = Products::whereIn('id',$product_ids)->pluck('name')->toArray();
       if ($product_names) {
          return implode(', ', $product_names);
       } else {
            return '';
       }
    }

    public function getTotalOrderKpAttribute($value) 
    {
       return OrdersDetails::where(['order_id'=>$this->order_id])->sum('points');
    }

    public function getRestNameAttribute($value) {
        // dd($this);
        $result = Restaurant::select('name')->where('id',$this->restaurant_id)->first();
        return $result->name ?? '';
    }

    public function getKiloPointsAttribute($value)
    {
        $user_id = Auth::user()->id;
        $points = UserKiloPoints::where(['order_id'=>$this->id, 'user_id'=>$user_id])->sum('points');
        return (string)$points;
    }

    public function getIsRateAttribute($value)
    {
        $user_id = Auth::user()->id;
        $is_rate = Rating::where(['order_id'=>$this->id, 'user_id'=>$user_id])->count();
        
        if ($is_rate) {
            return 1;

        } else {
            return 0;
        }
    }*/
}
