<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\AttributesLang;

class Attributes extends Model
{    
	protected  $appends = ['attributes_name'];

    protected  $table = 'attributes';

    protected $fillable = [
        'category_id, name',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function getAttributesNameAttribute($value){
    	$productAttr = AttributesLang::select('attributes_lang.name')->where(['attribute_id' => $this->id])->pluck('name')->toArray();

    	if ($productAttr) {
    		$attrs = implode(", ",array_unique($productAttr));
    	} else {
    		$attrs = '';
    	}
        return $attrs;
    }
   
}
