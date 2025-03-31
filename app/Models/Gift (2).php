<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\GiftLang;
use App\Models\BrandLang;
use App\Models\GiftImage;
use App\Models\GiftLike;
use App\Models\GiftView;
use App\Models\GiftVarient;
use App\Models\GiftRate;
use App\Models\GiftCategory;
use App\Models\GiftCategoryLang;
use App\Models\GiftSubCategoryLang;
use App\Models\GiftProductAttributes;
use App\Models\GiftProductAttributeValues;
use App\Models\GiftAttributesLang;
use App\Models\GiftAttributeValueLang;
use DB;

class Gift extends Model
{
    protected $connection = 'mysql2';
    protected  $appends = ['image','brand_name','category_name','sub_category_name','is_like','total_likes','gift_attributes','total_views','total_rate','avg_rating'];

    protected $fillable = [
        'name','category_id','points','amount','description','main_image','is_active','is_deleted','discount','sku_code','customization','customize_option','video'
    ];

    protected $hidden = [
        'updated_at','deleted_at'
    ];

    public function getSkuCodeAttribute($value) {

      if ($value) {
        // return url('uploads/barcode.php?codetype=Code39&size=40&text='.$value);
        return $value;

      } else {
        return null;
      }
    }

    public function getImageAttribute($value)
    {
      if (!empty($this->main_image)) {
        //dd($this->main_image);
          return $this->main_image;

      } else {
          return url('images/no-image-available.png');
      }
    }

    public function getMainImageAttribute($value)
    {
      if ($value) {
        //dd($this->main_image);
          return url('uploads/gift/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }

    public function gift_images()
    {
      return $this->hasMany(GiftImage::class,'gift_id','id');
    }

    public function variants()
    {
      return $this->hasMany(GiftVarient::class,'gift_id','id');
    }

    public function getBrandNameAttribute($value)
    {
      $locale = App::getLocale();
      $data =  BrandLang::select('name')->where(['brand_id'=>$this->brand_id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getCategoryNameAttribute($value)
    {
      $locale = App::getLocale();
      $data =  GiftCategoryLang::select('name')->where(['gift_category_id'=>$this->category_id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getSubCategoryNameAttribute($value)
    {
      $locale = App::getLocale();
      $data =  GiftSubCategoryLang::select('name')->where(['gift_sub_category_id'=>$this->sub_category_id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  GiftLang::select('name')->where(['gift_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }
    public function getDescriptionAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  GiftLang::select('description')->where(['gift_id'=>$this->id,'lang'=>$locale])->first();
      return $data->description ?? $value;
    }

    public function getIsLikeAttribute($value){
      // $request = app('request');
      $userData = giftAuthUserId();
      $user_id =  $userData[0];
      return GiftLike::where(['gift_id'=>$this->id, 'user_id'=> $user_id])->count();
    }

    public function getTotalLikesAttribute($value) {
        return GiftLike::where(['gift_id'=>$this->id])->count();
    }

    public function getTotalViewsAttribute($value) {
        return GiftView::where(['gift_id'=>$this->id])->count();
    }

    public function getTotalRateAttribute($value) {
        return GiftRate::where(['gift_id'=>$this->id])->count();
    }

    public function getAvgRatingAttribute($value)
    {
      $locale = App::getLocale();
      $data =  GiftRate::select(DB::raw( 'AVG( rating ) as rating'))->where(['gift_id'=>$this->id])->first();
      return number_format($data->rating, 1) ?? 0;
    }

    public function getGiftAttributesAttribute($value){
        $attrVals = [];

        $selected_show_attribute = GiftProductAttributes::select('gift_attributes.*')->where(['gift_id' => $this->id])->orderBy('price', 'ASC')->first();

        if ($selected_show_attribute) {
          $attrVals = GiftProductAttributeValues::select('gift_attribute_values.*')->where(['gift_attributes_id' => $selected_show_attribute->id])->pluck('attribute_value_lang_id')->toArray();
        }

        $gift_attributes = GiftAttributesLang::select('attributes_lang.name as attribute_name','attributes_lang.id as attributes_lang_id','attributes_lang.is_color')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.category_id'=>$this->category_id, 'attributes.sub_category_id'=>$this->sub_category_id])->get();

        if ($gift_attributes) {

          foreach ($gift_attributes as $key => $value) {
              $attributeValues = GiftAttributeValueLang::select('attribute_value_lang.name as attribute_value_name','attribute_value_lang.id as attribute_value_lang_id','attribute_value_lang.color_code')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where('attribute_values.attributes_lang_id', $value->attributes_lang_id)->get();

              if (count($attributeValues) < 1) {
                  unset($gift_attributes[$key]);
              }

              
              foreach ($attributeValues as $k => $v) {

                  if (in_array($v->attribute_value_lang_id, $attrVals)) {
                      $v->is_selected = 1;

                  } else {
                      $v->is_selected = 0;
                  }
              }
              $value->attributeValues = $attributeValues;

          }
        }


        /*$gift_attributes = GiftProductAttributeValues::select('gift_attribute_values.id','gift_attribute_values.attributes_lang_id','attributes_lang.name as attribute_name','attributes_lang.topping_choose')->where(['gift_id' => $this->id])->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attributes_lang', 'attributes_lang.id', '=', 'gift_attribute_values.attributes_lang_id')->groupBy('gift_attribute_values.attributes_lang_id')->get();
        // dd($gift_attributes);

        if ($gift_attributes) {

            foreach ($gift_attributes as $k => $v) {
                $attributeValues = GiftProductAttributeValues::select('gift_attributes.*', 'gift_attributes.is_free as is_selected', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.color_code', 'attribute_value_lang.id as attribute_value_lang_id','gift_attribute_values.id as gift_attribute_values_id')->where(['gift_id' => $this->id, 'gift_attribute_values.attributes_lang_id' => $v->attributes_lang_id])->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->get();

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
        }*/
        return $gift_attributes;
    }
   
}
