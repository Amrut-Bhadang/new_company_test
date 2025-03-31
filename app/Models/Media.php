<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant;
use App\Models\Category;
use Auth,App;

class Media  extends Model
{    
    protected $appends = ['category_name'];
    protected $fillable = [
        'table_name','table_id','type','file_name','file_path','extension','category_type','category_id','main_category_id','link','valid_from','valid_to'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
    protected $tab  ='medias'; 
    
    protected  $table = 'medias';

    public function getFilePathAttribute($value)
    {
    	if ($value) {
      		return url('uploads/banner/'.$value);

    	} else {
      		return url('images/image.png');
    	}
    }

    public function getCategoryNameAttribute($value)
    {
      $locale = App::getLocale();

      if ($this->category_type == 'Restaurant') {
        $categoryData = Restaurant::select('name')->where('id', $this->category_id)->first();

      } else if ($this->category_type == 'Category') {
        $categoryData = Category::select('name')->where('id', $this->category_id)->first();

      } else {
      }
      // $data =  BrandLang::select('name')->where(['brand_id'=>$this->category_id,'lang'=>$locale])->first();
      return $categoryData ? $categoryData->name : '';
    }
}
