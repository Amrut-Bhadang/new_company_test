<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\ProductIngredientLang;
class ProductIngredients extends Model
{    
    protected $table = 'product_ingredients';
    protected $fillable = [
        'product_id','name',
    ];

    protected $hidden = [
        'updated_at'
    ];
   

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  ProductIngredientLang::select('name')->where(['product_ingredients_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

 

    // this is a recommended way to declare event handlers
   
}