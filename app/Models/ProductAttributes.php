<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\AttributesLang;
use App\Models\AttributeValueLang;
use App\Models\ProductAttributeValues;

class ProductAttributes extends Model
{
    protected  $table = 'product_attributes';
    protected $fillable = [
        'id','product_id','attributes_lang_id','price','video','points','discount_price','dish_topping_id','is_mandatory','is_free'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValues::class,'product_attributes_id','id');
    }   
}
