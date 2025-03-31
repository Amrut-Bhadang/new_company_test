<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeValues;
use App\Models\AttributeValueLang;
use Auth;

class AttributesLang extends Model
{
    protected $table  ='attributes_lang';       
    protected $tab  ='attributes';

    /*public function attributeValues()
    {
        return $this->hasMany(AttributeValues::class,'attributes_lang_id','id')
        ->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'attribute_values.id')
        ->select('attribute_value_lang.name as attribute_value_name')
        ->get();
    }*/
}
