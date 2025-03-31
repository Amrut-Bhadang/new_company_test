<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\AttributesLang;

class ProductAttributeValues extends Model
{    

    protected  $table = 'product_attribute_values';

    protected $fillable = [
        'id','product_attributes_id','attribute_value_lang_id','attributes_lang_id',
    ];

    protected $hidden = [
        'updated_at',
    ];
   
}
