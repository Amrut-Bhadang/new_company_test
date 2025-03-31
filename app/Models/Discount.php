<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\BrandLang;
use App\Models\Orders;

class Discount  extends Model
{
    protected $appends = ['applied_user'];

    protected $fillable = [
        'category_type','discount_code','added_by','percentage','valid_from','valid_upto','no_of_use','no_of_use_per_user','status','description','max_discount_amount','min_order_amount','title','image'
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected  $table = 'discount';

    /*public function getFilePathAttribute($value)
    {
      if ($value) {
          return url('uploads/brand/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }*/

    public function getAppliedUserAttribute($value)
    {

      if ($this->discount_code) {
        return Orders::where('discount_code',$this->discount_code)->where('order_status','!=','Cancel')->count();

      } else {
        return 0;
      }
    }

    public function getImageAttribute($value)
    {
      if ($value) {
        return url('uploads/discount/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }

}
