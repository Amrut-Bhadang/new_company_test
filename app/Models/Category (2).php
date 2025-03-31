<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\Products;
use App\Models\CategoryLang;
class Category extends Model
{    
    protected  $appends = ['total_dish'];
    protected $fillable = [
        'id','name','image','description','status','type','main_category_id'
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

    public function getTotalDishAttribute($value) 
    {
        return Products::where(['category_id'=>$this->id,'is_active'=>1])->count();
    }

    public function getMedia(){
        return $this->hasMany(Media::class,'table_id','id')->where('table_name','Category');
    }

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  CategoryLang::select('name')->where(['category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }
    public function getDescriptionAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  CategoryLang::select('description')->where(['category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->description ?? $value;
    }
}
