<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\GiftAttributesLang;

class GiftProductAttributeValues extends Model
{    
	protected $connection = 'mysql2';
    protected  $table = 'gift_attribute_values';
    protected $fillable = [
        'id','gift_attributes_id','attribute_value_lang_id','attributes_lang_id',
    ];

    protected $hidden = [
        'updated_at',
    ];
   
}
