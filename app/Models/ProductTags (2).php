<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use Auth,App;

class ProductTags extends Model
{
	protected  $appends = ['total_items'];
    protected $table = 'product_tags';
    protected $fillable = [
        'product_id','tag','lang','status'
    ];

    protected $hidden = [
        'updated_at',
    ];

    /*public function getTagAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  $this->where(['lang'=>$locale])->first();
      dd($data->tag);
      // return $data->tag ?? $value;
    }*/

    public function getTotalItemsAttribute($value)
    {
    	$stores = 0;
    	$locale = App::getLocale();

    	if ($this->tag) {

            if (isset($this->main_category_id)) {
                $stores = ProductTags::select('product_id')->where(['status'=>1, 'tag'=>$this->tag, 'lang'=>$locale, 'products.main_category_id'=>$this->main_category_id])->join('products','products.id','=','product_tags.product_id')->groupBy('product_id')->get()->count();

            } else {
    		    $stores = ProductTags::select('product_id')->where(['status'=>1, 'tag'=>$this->tag, 'lang'=>$locale])->groupBy('product_id')->get()->count();
            }
    	}
    	return $stores;
    }
}
