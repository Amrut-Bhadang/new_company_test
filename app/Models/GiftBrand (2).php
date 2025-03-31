<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\GiftBrandLang;

class GiftBrand  extends Model
{    
    protected $connection = 'mysql2';
    protected $fillable = [
        'name','file_name','file_path','created_at'
    ];

    protected $hidden = [
        'updated_at',
    ]; 
    
    protected  $table = 'gift_brand';

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
      $data =  GiftBrandLang::select('name')->where(['brand_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

}
