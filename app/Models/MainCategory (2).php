<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
// use App\Models\Products;
use App\Models\MainCategoryLang;
// use App\Models\Restaurant;
class MainCategory extends Model
{    
    // protected  $appends = ['total_restaurant'];
    protected $table  ='main_category';
    protected $fillable = [
        'id','name','status','image','total_stores'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
    
    public function getImageAttribute($value)
    {
      if ($value) {
        return url('uploads/category/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }

    /*public function getTotalRestaurantAttribute($value)
    {
      $total_restaurant = Restaurant::where('main_category_id', $this->id)->count();
      return $total_restaurant;        
    }*/
}
