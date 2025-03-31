<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\AttributesLang;

class AttributeValues extends Model
{    

    protected  $table = 'attribute_values';

    protected $fillable = [
        'attributes_lang_id',
    ];

    protected $hidden = [
        'updated_at',
    ];
   
}
