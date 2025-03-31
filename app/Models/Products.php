<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;  
use App\User; 
use App\Models\ProductImages;
use App\Models\ProductIngredients;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Models\Favorite;
use App\Models\Cart;
use App\Models\Restaurant;
use App\Models\ProductTags;
use App\Models\ProductLang;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
use App\Models\Rating;
use App\Models\OrdersDetails;
use App\Models\Topping;
use DB;

class Products extends Model
{
    protected  $appends = ['is_favorite','is_topping','qty','attributes','product_attributes','add_on','avg_rating','category_name','can_rate','total_rate','product_attr','product_tags','points','product_images'];

    protected $fillable = [
        'name','category_id','restaurant_id','main_image','products_type','recipe_description','long_description','chef_amount','celebrity_amount','admin_amount','total_amount','price',
        'video','points','is_active','is_deleted','is_show','celebrity_id','sku_code','product_for','sub_category_id',
    ];

    protected $hidden = [
        'updated_at','deleted_at'
    ];


    public function user()
    {
        return $this->hasMany(User::class,'id','celebrity_id');
    }

    public function dishes()
    {
        return $this->hasMany(Products::class,'category_id','category_id');
    }

    public function scopePriceFilter($query, $serachData)
    {

        if (isset($serachData['min_price']) && isset($serachData['max_price'])) {
            $min_price = $serachData['min_price'];
            $max_price = $serachData['max_price'];

            return $query->whereBetween('products.price', [$min_price, $max_price])->orWhereHas('getProductTopping', function($q) use($serachData) {
                $q->orWhereBetween('price', [$serachData['min_price'], $serachData['max_price']])/*->orderBy('price', 'ASC')*/;
            })->orderBy('price', 'ASC');

        } else {
            return $query;
        }
    }

    public function scopeKPFilter($query, $serachData)
    {

        if (isset($serachData['min_kilo']) && isset($serachData['max_kilo'])) {
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];

            return $query->whereBetween('products.points', [$min_kilo, $max_kilo])->orWhereHas('getProductTopping', function($q) use($serachData) {
                $q->orWhereBetween('points', [$serachData['min_kilo'], $serachData['max_kilo']])/*->orderBy('price', 'ASC')*/;
            })->orderBy('points', 'ASC');

        } else {
            return $query;
        }
    }

    public function getSkuCodeAttribute($value) {

      if ($value) {
        // return url('uploads/barcode.php?codetype=Code39&size=40&text='.$value);
        return $value;

      } else {
        return null;
      }
    }

    public function restaurant()
    {
        return $this->hasOne(Restaurant::class,'id','restaurant_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class,'product_id','id')->where('user_id',auth()->user()->id);
    }
    
    public function getMainImageAttribute($value)
    {
        if ($value) {
            return url('uploads/product/'.$value);

        } else {
            return url('images/no-image-available.png');
        }
    }

    public function Category()
    {
        return $this->hasMany(Category::class,'id','category_id');
    }
    public function ProductImages()
    {
        return $this->hasMany(ProductImages::class,'product_id');
    }

    public function getProductTopping()
    {
        return $this->hasMany(ProductAttributes::class,'product_id', 'id');
    }

    public function getProductImagesAttribute()
    {
        return ProductImages::select('id','product_id','image')->where(['product_id'=>$this->id])->get();
    }

    public function ProductIngredients()
    {
        return $this->hasMany(ProductIngredients::class,'product_id');
    }

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  ProductLang::select('name')->where(['product_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getPriceAttribute($value)
    {
        if ($this->product_for != 'dish') {

            $selected_show_attribute = ProductAttributes::select('product_attributes.*')->where(['product_id' => $this->id])->orderBy('price', 'ASC')->first();
            return $selected_show_attribute->price ?? $value;

        } else {
            return $value;
        }
    }

    public function getPointsAttribute($value)
    {
        $points = $value;

        if (!empty($this->customize_option) && $this->customize_option == 'combination') {
            $selected_show_attribute = ProductAttributes::select('product_attributes.*')->where(['product_id' => $this->id])->orderBy('price', 'ASC')->first();

            if ($selected_show_attribute) {
                $points = $selected_show_attribute->points;
            }

        } else {
            $productDetail = Products::select('points','extra_kilopoints')->where(['id' => $this->id])->first();
            $totalKP = 0;

            if ($productDetail) {
                $totalKP = $productDetail->points + $productDetail->extra_kilopoints;
                $points = $totalKP;

            } else {
                $points = $value;
            }
        }
        return $points;
    }

    public function getCategoryNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  CategoryLang::select('name')->where(['category_id'=>$this->category_id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }
    public function getRecipeDescriptionAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  ProductLang::select('recipe_description')->where(['product_id'=>$this->id,'lang'=>$locale])->first();
      return $data->recipe_description ?? $value;
    }

    public function getLongDescriptionAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  ProductLang::select('long_description')->where(['product_id'=>$this->id,'lang'=>$locale])->first();
      return $data->long_description ?? $value;
    }

    public function getIsFavoriteAttribute($value){
        $user_id = Auth::user()->id;
        return Favorite::where(['type_id'=>$this->id, 'user_id'=> $user_id, 'type'=>'Dish'])->count();
    }
    public function getIsToppingAttribute($value)
    {
        
        $toppings = ProductAttributes::where('product_id', $this->id)->count();
        if ($toppings) {
            return 1;

        } else {
            return 0;
        }
        
    }
    public function getQtyAttribute($value)
    {
        if(isset(Auth::user()->id)){
           return  (int)Cart::where(['user_id'=>Auth::user()->id, 'product_id'=>$this->id])->sum('qty');
       }else{
            return 0;
       }
        
    }

    public function getProductAttrAttribute($value){
        $toppingData = Topping::where(['dish_id' => $this->id])->first();
        $attrs = '';

        if ($toppingData) {
            $productAttr = ProductAttributeValues::select('product_attribute_values.attribute_value_lang_id','attribute_value_lang.name')->join('product_attributes','product_attributes.id','=','product_attribute_values.product_attributes_id')->join('attribute_value_lang','attribute_value_lang.id','=','product_attribute_values.attribute_value_lang_id')->where(['dish_topping_id' => $toppingData->id])->pluck('name')->toArray();

            // dd($productAttr);

            if ($productAttr) {
                $attrs = implode(", ",array_unique($productAttr));
            } else {
                $attrs = '';
            }
        }
        return $attrs;
    }

    public function getProductTagsAttribute($value){
        $locale = App::getLocale();
        $tags = ProductTags::where(['product_id' => $this->id, 'lang'=>$locale])->pluck('tag')->toArray();
        $tags_arr = '';

        if ($tags) {
            $tags_arr = implode(", ",$tags);
        }
        return $tags_arr;
    }

    public function getProductAttributesAttribute($value){
        $product_attributes = ProductAttributeValues::select('product_attribute_values.id','product_attribute_values.attributes_lang_id','attributes_lang.name as attribute_name','attributes_lang.topping_choose')->where(['product_id' => $this->id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attributes_lang', 'attributes_lang.id', '=', 'product_attribute_values.attributes_lang_id')->groupBy('product_attribute_values.attributes_lang_id')->get();

        if ($product_attributes) {

            foreach ($product_attributes as $k => $v) {
                $attributeValues = ProductAttributeValues::select('product_attributes.*', 'product_attributes.is_free as is_selected', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id')->where(['product_id' => $this->id, 'product_attribute_values.attributes_lang_id' => $v->attributes_lang_id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->get();

                foreach ($attributeValues as $key => $value) {

                    if ($value->is_free == 1) {
                        $value->isSelected = true;

                    } else {
                        $value->isSelected = false;
                    }

                    if ($value->is_mandatory == 1) {

                        if ($key == 0) {
                            $value->isFirstIndexSelect = 1;
                            $v->is_selected = 1;

                        } else {
                            $value->isFirstIndexSelect = 0; 
                        }

                    } else {
                        $value->isFirstIndexSelect = 0;
                        $v->is_selected = 0;
                    }
                }

                $v->attributeValues = $attributeValues;
            }
        }
        return $product_attributes;
    }

    public function getAttributesAttribute($value){
          $attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$this->id,'topping_choose'=>0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
                            $query->where('dish_toppings.dish_id', $this->id);
                        }))->get();

                 return $attributes;  
    } 

    public function getAddOnAttribute($value){
          $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$this->id,'topping_choose'=>1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
                            $query->where('dish_toppings.dish_id', $this->id);
                        }))->get();
                 return $add_on;  
    } 

    public function getAvgRatingAttribute($value)
    {
      $data =  Rating::select(DB::raw( 'AVG( rating ) as rating'))->where(['product_id'=>$this->id])->first();
      $newdata = number_format($data->rating, 1);
      // number_format($data->rating,1);
      return $newdata ?? 0.0;
    }

    public function getCanRateAttribute($value)
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        if ($userId) {
            $checkAlreadyRated = Rating::where(['product_id'=>$this->id, 'user_id'=>$userId])->first();

            if ($checkAlreadyRated) {
                return 0;

            } else {
                $checkProductBuy = OrdersDetails::select('products.restaurant_id','order_details.product_id')->where(['order_details.product_id'=>$this->id, 'order_details.user_id'=>$userId])->join('products','products.id','=','order_details.product_id')->first();

                if ($checkProductBuy) {
                    return 1;

                } else {
                    return 0;
                }
            }

        } else {
            return 0;
        }
    }

    public function getTotalRateAttribute($value)
    {
        return Rating::where('product_id',$this->id)->count();
    }


}
