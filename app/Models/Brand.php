<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\BrandLang;
use App\Models\Restaurant;

class Brand  extends Model
{    
    protected $fillable = [
        'name','brand_type','file_name','type','file_path','user_id','country_code','mobile','email','password','brand_category'
    ];

    protected  $appends = ['total_outlet','outlets'];

    protected $hidden = [
        'updated_at',
    ]; 
    
    protected  $table = 'brands';

    public function getFilePathAttribute($value)
    {
      if ($value) {
          return url('uploads/brand/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  BrandLang::select('name')->where(['brand_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }


    public function getTotalOutletAttribute($value){
      return Restaurant::where('brand_id', $this->id)->count();
      
    }

    public function getOutletsAttribute($value){
      return Restaurant::join('products','products.restaurant_id','=','restaurants.id')->where(['restaurants.brand_id'=>$this->id,'restaurants.status'=>1])->groupBy('restaurants.id')->get()->count();
      
    }

}
