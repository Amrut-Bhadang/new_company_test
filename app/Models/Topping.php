<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\ToppingCategoryLang;
use App\Models\ProductAttributeValues;
class Topping extends Model
{    
	protected  $appends = ['product_attr'];
    protected $fillable = [
        'id','topping_category_id','status','dish_id','topping_name','price','price_reflect_on','created_at'
    ];
    
    protected $table  ='dish_toppings'; 
    
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    public function getProductAttrAttribute($value){
    	$productAttr = ProductAttributeValues::select('product_attribute_values.attribute_value_lang_id','attribute_value_lang.name')->join('product_attributes','product_attributes.id','=','product_attribute_values.product_attributes_id')->join('attribute_value_lang','attribute_value_lang.id','=','product_attribute_values.attribute_value_lang_id')->where(['dish_topping_id' => $this->id])->pluck('name')->toArray();

    	// dd($productAttr);

    	if ($productAttr) {
    		$attrs = implode(", ",array_unique($productAttr));
    	} else {
    		$attrs = '';
    	}
        return $attrs;
    }
    /*public function attrvalues()
    {
    	$productAttr = ProductAttributeValues::select('product_attribute_values.attribute_value_lang_id','attribute_value_lang.name')->join('product_attributes','product_attributes.id','=','product_attribute_values.product_attributes_id')->join('attribute_value_lang','attribute_value_lang.id','=','product_attribute_values.attribute_value_lang_id')->where(['dish_topping_id' => $value->id])->pluck('name')->toArray();
        return $this->hasMany(Products::class,'category_id','category_id');
    }*/
}
