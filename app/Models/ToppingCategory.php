<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\ToppingCategoryLang;
use App\Models\Topping;
class ToppingCategory extends Model
{    
    protected $fillable = [
        'id','name','status'
    ];
    
    protected $table  ='toppings_category'; 
    
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  ToppingCategoryLang::select('name')->where(['topping_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function Toppings()
    {
        return $this->hasMany(Topping::class,'topping_category_id','id')->where('dish_toppings.status', 1);
    }
}
