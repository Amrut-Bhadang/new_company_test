<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\Products;
use App\Models\GiftSubCategoryLang;

class GiftSubCategory extends Model
{    
    protected $connection = 'mysql2';
    protected  $appends = ['category_name','total_gift'];
    protected $fillable = [
        'id','category_id','name','image','description','status'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    public function getTotalGiftAttribute($value) 
    {
        return  Gift::where(['sub_category_id'=>$this->id, 'is_active'=>1])->count();
    }

    public function getImageAttribute($value)
    {
      if ($value) {
        return url('uploads/gift-category/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }   

    public function getCategoryNameAttribute($value)
    {
      $locale = App::getLocale();
      $data =  GiftCategoryLang::select('name')->where(['gift_category_id'=>$this->category_id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    /*public function getTotalDishAttribute($value) 
    {
        return  Products::where(['category_id'=>$this->id,'is_active'=>1])->count();
    }

    public function getMedia(){
        return $this->hasMany(Media::class,'table_id','id')->where('table_name','Category');
    }*/

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  GiftSubCategoryLang::select('name')->where(['gift_sub_category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }
    public function getDescriptionAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  GiftSubCategoryLang::select('description')->where(['gift_sub_category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->description ?? $value;
    }
}
