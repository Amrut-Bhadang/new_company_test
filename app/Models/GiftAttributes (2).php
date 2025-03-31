<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\GiftAttributesLang;

class GiftAttributes extends Model
{    
    protected $connection = 'mysql2';
	protected  $appends = ['attributes_name'];

    protected  $table = 'attributes';

    protected $fillable = [
        'category_id, name',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function getAttributesNameAttribute($value){
    	$productAttr = GiftAttributesLang::select('attributes_lang.name')->where(['attribute_id' => $this->id])->pluck('name')->toArray();

    	if ($productAttr) {
    		$attrs = implode(", ",array_unique($productAttr));
    	} else {
    		$attrs = '';
    	}
        return $attrs;
    }
   
}
