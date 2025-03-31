<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\Products;
use App\Models\GiftCategoryLang;
use App\Models\Gift;
class GiftCategory extends Model
{
    protected $connection = 'mysql2';
    protected $table  ='gift_categories';
    protected  $appends = ['total_gift'];
    protected $fillable = [
        'id','name','image','description','status','type'
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

    public function getTotalGiftAttribute($value) 
    {
        return  Gift::where(['category_id'=>$this->id,'is_active'=>1])->count();
    }

    public function getMedia(){
        return $this->hasMany(Media::class,'table_id','id')->where('table_name','Category');
    }

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  GiftCategoryLang::select('name')->where(['gift_category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }
    public function getDescriptionAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  GiftCategoryLang::select('description')->where(['gift_category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->description ?? $value;
    }
}
