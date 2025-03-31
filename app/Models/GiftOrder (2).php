<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use DB;
use App\User;
use App\Models\Gift;
use App\Models\GiftOrderDetails;
use App\Models\GiftOrder;
use App\Models\OrderCancelReasions;
use App\Models\GiftRate;
use App\Models\Modes;

class GiftOrder extends Model
{
	protected $connection = 'mysql2';
    protected $table = 'gift_orders';

	protected $appends = ['gift_name','dine_in','car_detail','car_detail_color','is_rate'];

	protected $fillable = [
		'user_id','shipping_charges','points','address_id','longitude','latitude','building_number','building_name','landmark','address','address_type','contact_name','contact_number','is_wallet_use','order_status','wallet_amoount_use','status','order_type','pick_type','car_color','car_number','car_brand','tax_amount','taxPercentage'
	];

	protected $hidden = [
		'updated_at'
	];

	public function getUser(){
		return $this->hasOne(User::class, 'id' ,'user_id');
	}

	public function getDineInAttribute($value){
        //dd($this->order_type);
        $result = Modes::select('name')->where('id',$this->order_type)->first();
        return $result->name ?? '';
    }

    public function getCarDetailAttribute($value){
        //dd($this->order_type);
        $result = GiftOrder::select('car_color','car_number','car_brand')->where('id',$this->id)->first();
        // dd($result->car_color);
        $string = "Car Detail-> (Color:";
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
        $result = GiftOrder::select('car_color','car_number','car_brand')->where('id',$this->id)->first();
        // dd($result->car_color);
        $string = "Car Detail-> (Color:";

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

	public function getGiftNameAttribute($value) 
    {
       $gift_ids = GiftOrderDetails::where(['gift_order_id'=>$this->gift_order_id])->pluck('gift_id')->toArray();
       //dd($product_ids);
       $gift_names = Gift::whereIn('id',$gift_ids)->pluck('name')->toArray();
       if ($gift_names) {
          return implode(', ', $gift_names);
       } else {
            return '';
       }
    }

    public function getIsRateAttribute($value)
    {
        // $user_id = Auth::user()->id;
        $userData = giftAuthUserId();

        if ($userData) {
            $user_id =  $userData[0];
            $is_rate = GiftRate::where(['order_id'=>$this->id, 'user_id'=>$user_id])->count();
            
            if ($is_rate) {
                return 1;

            } else {
                return 0;
            }

        } else {
            return 0;
        }
    }

}