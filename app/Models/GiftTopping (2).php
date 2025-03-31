<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\GiftToppingCategoryLang;
use App\Models\GiftProductAttributeValues;
class GiftTopping extends Model
{
    protected $connection = 'mysql2';
	protected  $appends = ['product_attr'];
    protected $fillable = [
        'id','category_id','sub_category_id','gift_id','status','created_at'
    ];
    
    protected $table  ='gift_toppings'; 
    
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    public function getProductAttrAttribute($value){
    	$productAttr = GiftProductAttributeValues::select('gift_attribute_values.attribute_value_lang_id','attribute_value_lang.name')->join('gift_attributes','gift_attributes.id','=','gift_attribute_values.product_attributes_id')->join('attribute_value_lang','attribute_value_lang.id','=','gift_attribute_values.attribute_value_lang_id')->where(['gift_topping_id' => $this->id])->pluck('name')->toArray();

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
