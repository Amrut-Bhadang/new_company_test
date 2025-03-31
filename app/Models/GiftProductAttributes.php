<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\GiftAttributesLang;
use App\Models\GiftAttributeValueLang;
use App\Models\GiftProductAttributeValues;

class GiftProductAttributes extends Model
{
    protected $connection = 'mysql2';
    protected  $table = 'gift_attributes';
    protected $fillable = [
        'id','gift_id','attributes_lang_id','price','video','points','discount_price','gift_topping_id','is_mandatory','is_free'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function attributeValues()
    {
        return $this->hasMany(GiftProductAttributeValues::class,'product_attributes_id','id');
    }   
}
