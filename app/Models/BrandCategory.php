<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\BrandCategoryLang;

class BrandCategory  extends Model
{    
    protected $fillable = [
        'name','status','created_at'
    ];

    protected $hidden = [
        'updated_at',
    ]; 
    
    protected  $table = 'brand_category';

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  BrandCategoryLang::select('name')->where(['brand_category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

}
