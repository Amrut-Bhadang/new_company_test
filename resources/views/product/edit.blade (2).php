@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
use App\Models\ProductLang;
use App\Models\ProductTags;
use App\Models\ProductIngredientLang;
$language = Language::pluck('lang')->toArray();
// dd($product);
?>
<style type="text/css">
.tag_div .ms-ctn.form-control{
    display: inherit;
}
.tag_div  .ms-helper {
    bottom: -17px;
    top: auto;
}
.tag_div .ms-ctn .ms-trigger {
    display: none;
}
.tag_div .ms-ctn .ms-sel-ctn {
    margin-left: 0;
    padding-right: 0;
}
.tag_div .ms-ctn .ms-sel-ctn input {
    padding-top: 2px;
}
</style>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="{{ asset('dist/js/jquery-1.11.1.min.js') }}"></script>
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
            <!-- <a href="{{ url('product') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a> -->
            <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
            <h4 class="text-themecolor">{{ __('Edit Item') }}</h4>
        </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('product') }}">{{ __('Item Manager') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Item') }}</li>
            </ol>
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('product') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
</div> -->
<div class="content">
  <div class="row">
    <form method="PUT" action="{{ url('api/product/'.$product->id) }}" id="edit_role" style="width: 100%">
    @csrf
    <div class="card card-primary card-outline">
      <div class="card-body">
        <!-- <ul class="nav nav-tabs">
            @foreach($language as $key => $lang)
            <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#tab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
            @endforeach
        </ul> -->
        <div class="tab-content" style="margin-top:10px">
         <div class="row">
          <input type="hidden" class="product_for" name="product_for" value="{{$product->product_for}}">
          <!-- <div class="col-md-6">
              <div class="form-group">
              <label class="control-label" for="product_for">Product For*</label>
              <select name="product_for" id="product_for" onchange="product_for_change()" class="form-control celebrityPrice select3" data-placeholder="Select Product For" data-parsley-required="true" style="width: 100%;">
                  <option value=''>--Select Product For--</option>
                  <option {{$product->product_for == 'dish' ? 'selected' : '' }} value="dish">Dish</option>
                  <option {{$product->product_for == 'other' ? 'selected' : '' }} value="other">Other</option>
              </select>
              </div>
          </div> -->
          @if($user_type == 4)
              <input type="hidden" name="main_category_id" id="main_category_id" value="{{$main_category_id}}">
              <input type="hidden" name="brand_id" id="brand_id" value="{{$brand_id}}">
              <input type="hidden" name="restaurant_id" id="restro_login_id" value="{{$restaurant_id}}">
          @endif

          @if($user_type != 4)
            <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="main_category_id">Service*</label>
                  <select name="main_category_id" id="main_category_id" onchange="getBrands()" class="form-control multiple-search"  data-parsley-required="true" >
                    <option value="">---Select Service----</option>
                    @foreach ($main_category as $cat)
                      <option {{$product->main_category_id == $cat->id ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="col-md-6 show_brandDiv">
                <div class="form-group">
                  <label class="control-label" for="brand_id">Vendor*</label>
                  <select name="brand_id" class="form-control multiple-search" onchange="getRestro()" data-parsley-required="true" >
                    <option value="">---Select Vendor----</option>
                  </select>
                </div>
            </div>
          @endif
          <!-- @if($user_type == 4)
            <input type="hidden" name="restaurant_id" value="{{$restaurant_id}}">
          @endif -->
          @if($user_type == 1)
            <div class="col-md-6 show_restroDiv">
                <div class="form-group">
                <label class="control-label " for="restaurant_id">Store</label>
                <select name="restaurant_id" id="restaurant_id" onchange="getCategory()" class="form-control celebrityPrice select3" data-placeholder="Select Store" style="width: 100%;">
                    <option value=''>--Select Store--</option>
                </select>
                </div>
            </div>
          @endif
          <div class="col-md-<?php echo $user_type == 4 ? '12' : '6'; ?> show_restro_category_div">
            
          </div>
         </div> 
        @if($lang)
          <div class="row">
        @foreach($language as  $key => $lang)
        <?php
          if(isset($product))
          {
              $langData = ProductLang::where(['lang'=>$lang,'product_id'=>$product->id])->first();  
          } ?>
        <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name">{{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
                <input type="text" name="name[{{$lang}}]" data-parsley-required="true" data-parsley-maxlength="30" value="{{$langData->name}}" id="name" class="form-control" placeholder="Name"   />
              </div>
            </div>
          @endforeach
            </div>
          @endif
            <!-- <div class="col-md-6">
              <div class="form-group">
              <label class="control-label" for="recipe_description">{{__('backend.recipe_description')}} ({{__('backend.'.$lang)}})</label>
              <textarea  id="recipe_descriptionedit_{{$lang}}" data-parsley-required="true" class="form-control ckeditor" placeholder="Recipe Description">{{$langData->recipe_description}}</textarea>
              </div>
            </div> -->
        @if($lang)
          <div class="row">
        @foreach($language as  $key => $lang)
        <?php
          if(isset($product))
          {
              $langData = ProductLang::where(['lang'=>$lang,'product_id'=>$product->id])->first();  
          } ?>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}})*</label>
                <textarea  id="descriptionedit_{{$lang}}" class="form-control ckeditor" placeholder="Description">{{$langData->long_description}}</textarea>
              </div>
            </div>
        @endforeach
          </div>
        @endif
        <!-- </div> -->
        
        </div>
        <div class="row product_for_dish hide">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="product_type">Type*</label>
              <select name="product_type" class="form-control" style="width: 100%;" >
                <option value=''>--Select Type--</option>
                <option value="Veg" {{ $product->products_type=='Veg'?'selected':'' }}>Veg</option>
                <option value="Non-Veg" {{ $product->products_type=='Non-Veg'?'selected':'' }}>Non-Veg</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="video">Video</label>
              <input type="text" name="video" id="video" class="form-control" value="{{$product->video}}" placeholder="Video Url" />
              <!-- <textarea name="video" id="video" class="form-control" placeholder="Video Url"></textarea> -->
            </div>
          </div>
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="celebrity_id">Celebrity</label>
              <select name="celebrity_id" id="celebrity_id_edit" class="form-control celebrityPriceEdit select2" data-placeholder="Select Celebrity" style="width: 100%;">
                <option value=''>--Select Celebrity--</option>
                @foreach ($celebrity as $celebrity)
                    <option value="{{ $celebrity->id }}" {{ $product->celebrity_id == $celebrity->id?'selected':'' }}>{{ $celebrity->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6" >
            <div class="form-group" id="celebrityDivEdit">
              <label class="control-label" for="celebrity_price">Celebrity Price</label>
              <input type="text" name="celebrity_price" value="{{$product->celebrity_amount}}" id="celebrity_price_edit" class="form-control" placeholder="Celebrity Price"/>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="chef_id">Chef</label>
              <select name="chef_id[]" id="chef_id_edit" class="select2 chefPriceEdit" multiple="multiple" data-placeholder="Select Chef" data-dropdown-css-class="select2-primary" style="width: 100%;">
                @foreach ($chef as $chefs)
                    
                    <option value="{{ $chefs->id }}" {{ (in_array($chefs->id,$productAssignTOChef))?'selected':'' }}>{{ $chefs->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        
          <div class="col-md-6" >
            <div class="form-group" id="chefDivEdit">
              <label class="control-label" for="chef_price">Chef Price</label>
              <input type="text" name="chef_price" value="{{$product->chef_amount}}" id="chef_price_edit" class="form-control" placeholder="Chef Price"/>
            </div>
          </div> -->
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="admin_price">Admin Prices *</label>
              <input type="text" name="admin_price" value="{{$product->admin_amount}}" id="admin_price_edit" class="form-control" placeholder="Admin Prices"/>
            </div>
          </div> -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="prepration_time">Preparation Time (In Minutes)*</label>
              <input type="text" name="prepration_time" value="{{$product->prepration_time}}" id="prepration_time" class="form-control" placeholder="Preparation time" autocomplete="off" data-parsley-type="digits"  />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="buy_one_get_one">Buy 1 Get 1*</label>
              <select class="form-control" data-parsley-required="true" name="buy_one_get_one">
                <option value="">---Select---</option>
                <option value="1" {{ $product->buy_one_get_one == 1 ?'selected':'' }}>Yes</option>
                <option value="0" {{ $product->buy_one_get_one == 0 ?'selected':'' }}>No</option>
              </select>
            </div>
          </div>
          <!-- <div class="col-md-6">
              <div class="form-group">
                  <label class="control-label" for="serve">Serve *</label>
                  <select class="form-control" data-parsley-required="true" name="serve" id="serve">
                      <option value="">--Select Here--</option>
                      @for($i=1; $i <= 10; $i++)
                      <option value="{{$i}}" @if($product->serve == $i) selected @endif >{{$i}}</option>
                      @endfor
                  </select>
              </div>
          </div> -->
        </div>
        <div class="row">
          @if($lang)
            @foreach($language as  $key => $lang)
            <?php
              if(isset($product))
              {
                  $langData = ProductTags::select('id', 'tag')->where(['lang'=>$lang,'product_id'=>$product->id])->get()->toArray();
                  // $tags = implode(",",$langData);
                  $tags = json_encode($langData);
              } ?>
                <div class="col-md-6">
                  <div class="form-group tag_div">
                    <label class="control-label" for="prepration_time">Tag Input ({{__('backend.'.$lang)}})*</label>
                    <!-- <input type="text" id="#inputTag" name="inputTag[{{$lang}}]" class="form-control" placeholder="Enter tags" value="{{$tags ?? ''}}" data-role="tagsinput"> -->
                    <input type="text" id="inputTag{{$lang}}" name="inputTag[{{$lang}}]" class="form-control input_tag_test_{{$lang}}" placeholder="Enter tags" value="{{$tags ?? ''}}">
                  </div>
                </div> 
            @endforeach
          @endif
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="price">MRP(QAR)*</label>
              <input type="text" name="price" value="{{$product->total_amount}}" id="price_edit" onkeyup="calculateKp()" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="form-control" placeholder="MRP" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="discount_price">Discount Price(QAR)</label>
              <input type="text" name="discount_price" value="{{$product->discount_price}}" onkeyup="calculateKp()" id="discount_price_edit" class="form-control" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Discount Price"/>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="points">Kilo Points(%)*</label>
              <input type="text" name="points" value="{{$product->points_percent}}" onkeyup="calculateKp()" id="points" class="form-control" data-parsley-required="true" placeholder="Points" min="1" max="100" data-parsley-type="digits" />
              <span class="kpWillUpdate"></span>
            </div>
          </div>
          <div class="col-md-6 extra_kilopoints">
              <div class="form-group">
                <label class="control-label" for="extra_kilopoints">Extra Kilo Points(%)</label>
                <input type="text" name="extra_kilopoints" value="{{$product->extra_kilopoints_percent}}" onkeyup="calculateKp()" id="extra_kilopoints" min="1" max="100" class="form-control" placeholder="Extra Kilo Points" data-parsley-type="digits"  />
                <span class="extraKpWillUpdate"></span>
              </div>
          </div>
        </div>
        <div class="row product_for_other hide">
          <div class="col-md-6">
              <div class="form-group">
                  <label class="control-label" for="shop_type">Shop Type *</label><br>
                  <input {{$product->shop_type == 'localshop' ? 'checked' : '' }} type="radio" id="localshop" name="shop_type" value="localshop">
                  <label for="localshop">Local Shop</label>
                  <input {{$product->shop_type == 'internationshop' ? 'checked' : '' }} type="radio" id="internationshop" name="shop_type" value="internationshop">
                  <label for="internationshop">International Shop</label>
              </div>
          </div>

          <div class="col-md-6 localshop_type hide">
              <div class="form-group">
                  <label class="control-label" for="delivery_time">Delivery Time*</label><br>
                  <input {{$product->shop_type == 'localshop' && $product->delivery_time == 'sameday' ? 'checked' : '' }} type="radio" id="sameday" name="delivery_time" value="sameday">
                  <label for="sameday">Same Day</label>
                  <input {{$product->shop_type == 'localshop' && $product->delivery_time == 'nextday' ? 'checked' : '' }} type="radio" id="nextday" name="delivery_time" value="nextday">
                  <label for="nextday">Next Day</label>
                  <input {{$product->shop_type == 'localshop' && $product->delivery_time == 'manual' ? 'checked' : '' }} type="radio" id="manual" name="delivery_time" value="manual">
                  <label for="manual">Manual</label>
              </div>
          </div>

          <div class="col-md-6 internationshop_type hide">
              <div class="form-group">
                  <label class="control-label" for="delivery_time">Delivery Time*</label><br>
                  <!-- <input {{$product->shop_type == 'internationshop' && $product->delivery_time == 'express' ? 'checked' : '' }} type="radio" id="express" name="delivery_time" value="express">
                                    <label for="express">Express</label>
                                    <input {{$product->shop_type == 'internationshop' && $product->delivery_time == 'normal' ? 'checked' : '' }} type="radio" id="normal" name="delivery_time" value="normal">
                                    <label for="normal">Normal</label> -->
                  <input {{$product->shop_type == 'internationshop' && $product->delivery_time == 'manual' ? 'checked' : '' }} type="radio" id="manual_international" name="delivery_time" value="manual">
                  <label for="manual_international">Manual</label>
              </div>
          </div>

          <div class="col-md-6 timeSelectBox hide">
              <div class="form-group">
                  <label class="control-label" for="delivery_hours">Delivery Time*</label><br>
                  <select class="form-control" name="delivery_hours" id="delivery_hours">
                      <option value="">--Select Days--</option>
                      @for($i=1; $i <= 50; $i++)
                      <option {{$product->delivery_hours == $i ? 'selected' : '' }} value="{{$i}}">{{$i}} Days</option>
                      @endfor
                  </select>
              </div>
          </div>
      </div>
        <!-- <div class="row attibutes_div hide">
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label" for="selecte_attribute_id">Attributes</label>
                        <select name="selecte_attribute_id" id="selecte_attribute_id" onchange="getAttributeValue(this)" class="form-control celebrityPrice select3" data-placeholder="Select Attributes" style="width: 100%;">
                            <option value=''>--Select Attributes--</option>
                            @foreach ($attributes_lang as $attributes_name)
                                <option {{ (in_array($attributes_name->id, $selected_attributes_lang)) ? 'disabled' : '' }} value="{{ $attributes_name->id }}">{{ $attributes_name->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @if(!empty($product_attributes))
              @foreach ($product_attributes as $product_attributes)
                <div class="row attributeValues_{{$product_attributes->attributes_lang_id}}">
                  <div class="col-md-1">
                    <a class="btn btn-danger btn-xs" onclick="removeAttributeDiv(this)" attribute_id={{$product_attributes->attributes_lang_id}} style="color:white;"><i class="fa fa-trash"></i></a>
                  </div>
                  <div class="col-md-5">
                    <div class="form-group">
                      <label class="control-label" for="attribute_name">Attribute Name *</label>
                      <input type="text" class="form-control" name="old_attribute[{{$product_attributes->attributes_lang_id}}][attribute_name]" value="{{$product_attributes->attribute_name}}" disabled="">
                      <input type="hidden" name="old_attribute[{{$product_attributes->attributes_lang_id}}][attribute_id]" value="{{$product_attributes->attributes_lang_id}}">
                      <input type="hidden" name="old_attribute[{{$product_attributes->attributes_lang_id}}][id]" value="{{$product_attributes->id}}">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="attribute_value_ids">Attribute Values *</label>
                      <select name="old_attribute[{{$product_attributes->attributes_lang_id}}][attribute_value_ids][]" class="form-control multipal select2 multiple-search" data-placeholder="Select Attribute Values" style="width: 100%;" data-parsley-required="true" multiple="multiple">
                        <option value="">--Select Attribute Values--</option>
                        <?php foreach ($product_attributes->attributeOption as $key => $value) { ?>
                          <option {{ (in_array($value['id'], $product_attributes->selected_attr_values)) ? 'selected' : '' }} value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="points">Kilo Points*</label>
                          <input type="text" name="old_attribute[{{$product_attributes->attributes_lang_id}}][points]" id="points" class="form-control" placeholder="Kilo Points" value="{{$product_attributes->points}}" data-parsley-type="digits"  />
                        </div>
                    </div>    
                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="video">Video*</label>
                          <input type="text" name="old_attribute[{{$product_attributes->attributes_lang_id}}][video]" id="video" class="form-control" placeholder="Video Url" value="{{$product_attributes->video}}" />
                        </div>
                    </div>
                  <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label" for="admin_price">Discount Prices*</label>
                        <input type="text" name="old_attribute[{{$product_attributes->attributes_lang_id}}][discount_price]" id="discount_price" class="form-control" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Discount Prices" value="{{$product_attributes->discount_price}}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="price">Original Prices*</label>
                          <input type="text" name="old_attribute[{{$product_attributes->attributes_lang_id}}][price]" id="price" class="form-control" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Total Prices" value="{{$product_attributes->price}}" />
                        </div>
                    </div>
                </div>
              @endforeach
            @endif
            <div class="show_attribute_value_div">
                
            </div>
        </div> -->
        <div class="row">

          <div class="col-md-6">
              <label for="image">SKU Code*</label>
              <?php if($product->sku_code){
                  /*$url = $product->sku_code;
                  $url_components = parse_url($url);
                  parse_str($url_components['query'], $params);
                  $sku_code = $params['text'];*/
                  $sku_code = $product->sku_code;
              } else {
                $sku_code = $product->sku_code;
              } ?>
              <div class="form-group input-group">
                  <input type="text" name="sku_code" id="sku_code" value="{{$sku_code}}" class="form-control" data-parsley-required="true" placeholder="SKU Code">
              </div>
          </div>
          
          <div class="col-md-6">
              <label for="image">Product Image</label>
            <div class="form-group">
              <div class="input-group">
                <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$product->main_image}}"></div>
                <input type="file" id="editfile" name="image" class="form-control">
              </div>
              <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
            </div>
          </div>

            <div class="col-md-6">
              <div class="form-group input-group">
                  <input type="checkbox" name="customization" {{$product->customization == 'Yes' ? 'checked' : '' }} onclick="customizationOption()" id="customization"> 
                  <label for="image">Customization*</label>
              </div>
            </div>

            <div class="col-md-6 customized_option_box hide">
              <div class="form-group">
                <label for="image">Customization Option*</label>
                <select class="form-control" name="customize_option" onchange="customizedOptionChange()" id="customize_option">
                    <option value="">--Select Option--</option>
                    <option {{$product->customize_option == 'normal' ? 'selected' : ''}} value="normal">Normal</option>
                    <option {{$product->customize_option == 'combination' ? 'selected' : ''}} value="combination">Combination</option>
                </select>
              </div>
            </div>
            <div class="col-md-12 attributes_show_div">
              
            </div>
            <!-- <div class="row">
            </div> -->
          <?php /*
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="points">Product Ingredients *</label>
              <a id='addButtonEdit' class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a>
              <a id='removeButtonEdit' class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a>
              <div id='TextBoxesGroupEdit'>
                @foreach ($productIngredients as $key => $productIngredient)
                  <div id="TextBoxDivEdit{{$key+1}}">
                  
                    <?php
                    
                    if(isset($productIngredient->id))
                    {
                        $langData_en = ProductIngredientLang::where(['lang'=>'en','product_ingredients_id'=>$productIngredient->id])->first();
                        $langData_ar = ProductIngredientLang::where(['lang'=>'ar','product_ingredients_id'=>$productIngredient->id])->first();  
                    }
                    ?>
                      <div class="row">
                        <div class="col-md-6">
                          <input type='text' id='textboxedit{{$key+1}}' name="ingredients[en][]" value="{{$langData_en->name}}" class="form-control" placeholder="Ingredients English" data-parsley-required="true">
                        </div>
                        <div class="col-md-6">
                          <input type='text' id='textboxedit{{$key+1}}' name="ingredients[ar][]" value="{{$langData_ar->name}}" class="form-control" placeholder="Ingredients Arabic" >
                        </div>
                      </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div> --> */ ?>
        </div>            
        <hr style="margin: 1em -15px">
        <div class="show_subcategoryDiv">
          
        </div>
        <hr style="margin: 1em -15px">
        <a href="{{ url('product') }}" class="btn btn-default" data-dismiss="modal">Back</a>
        <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
                style="display: none;" role="status" aria-hidden="true"></span> Save</button>
      </div>
    </div>
    </form>
  </div>
</div>
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
  /*$(document).ready(function() {
      $('.multipal').select2();
  });*/
</script>
<script type="text/javascript">
  var counter = "<?php echo count($selected_attributes_lang); ?>";

    function customizationOption() {

        if ($("#customization").prop('checked') == true){
           $('.customized_option_box').removeClass('hide');
           $('.attributes_show_div').removeClass('hide');

        } else {
           $('.customized_option_box').addClass('hide');

           $('.attributes_show_div').html('');
           $('.attributes_show_div').addClass('hide');

            // $("#customize_option option:selected").prop('selected', false);
            $.each($("#customize_option option:selected"), function () {
                $(this).prop('selected', false); // <-- HERE
            });
        }
    }

    function customizedOptionChange() {
        var optionValue = $( "#customize_option option:selected" ).val();
        var main_category_id = $('#main_category_id').val();
        var category_id = $('#category_id').val();

        if ($("#customization").prop('checked') == true){

          if (main_category_id && category_id) {

              if (optionValue == 'combination') {
                  showAttributes(main_category_id, category_id);

              } else if (optionValue == 'normal') {
                  showAttributes(main_category_id, category_id, 'single');
              }

          } else {
              toastr.error('Please select main category and category first to see combinations.');
          }
        }        
    }

    function showAttributes(main_category_id, category_id, customizeOptionValue='') {
      var dish_id = "<?php echo $product->id; ?>";

        if (customizeOptionValue) {
            $.ajax({
               url:'{{url('topping/show_single_attributes')}}/'+main_category_id+'/'+category_id+'/'+customizeOptionValue+'/'+dish_id,
               dataType: 'html',
               success:function(result)
               {
                $('.attributes_show_div').html(result);
               }
            });

        } else {
            $.ajax({
               url:'{{url('topping/show_attributes')}}/'+main_category_id+'/'+category_id+'/'+dish_id,
               dataType: 'html',
               success:function(result)
               {
                $('.attributes_show_div').html(result);
               }
            });
        }
    }

    function addMoreAttributeValues() {
        var main_category_id = $('#main_category_id').val();
        var category_id = $('#category_id').val();
        counter++;
        $.ajax({
           url:'{{url('topping/show_attribute_values')}}/'+main_category_id+'/'+category_id+'/'+counter,
           dataType: 'html',
           success:function(result)
           {
            $('.attributes_values_tr_before').before(result);
           }
        });
    }

    function addMoreAttributeSingleValues($this) {
        var attribute_id = $($this).attr('data-id');
        var main_category_id = $('#main_category_id').val();
        var category_id = $('#category_id').val();

        if (attribute_id && main_category_id && category_id) {
            counter++;
            $.ajax({
               url:'{{url('topping/show_attribute_values')}}/'+main_category_id+'/'+category_id+'/'+counter+'/'+attribute_id,
               dataType: 'html',
               success:function(result)
               {
                $('.attributes_values_tr_before-'+attribute_id).before(result);
                  onSelectIsMandatory(attribute_id);
                  onSelectIsFree(attribute_id);
               }
            });

        } else {
            toastr.error('Some of data is incorrect.');
        }
    }

    function removeAttributeValues(id) {
        $('.attributes_values_tr_'+id).remove();
    }

    function disabledPrice(index) {
        var is_free = $('.is_free_'+index).val();

        if (is_free == 1) {
          $('.price_'+index).attr( "disabled", true );
          $('.discount_price_'+index).attr( "disabled", true );

        } else {
          $('.price_'+index).attr( "disabled", false );
          $('.discount_price_'+index).attr( "disabled", false );
        }
    }

    function onSelectIsMandatory(attributes_lang_id) {

        if ($("#is_mandatory_"+attributes_lang_id).prop('checked') == true) {
            $('.is_mandatory_'+attributes_lang_id+' option[value="1"]').prop("selected", true);
            $('.is_mandatory_'+attributes_lang_id).prop('disabled', true);
            $('.is_mandatory_input_'+attributes_lang_id).val('1');

        } else {
            $('.is_mandatory_'+attributes_lang_id+' option[value="0"]').prop("selected", true);
            $('.is_mandatory_input_'+attributes_lang_id).val('0');
            // $('.is_mandatory_'+attributes_lang_id).prop('disabled', false);
        }
    }

    function onSelectIsFree(attributes_lang_id) {

        if ($("#is_free_"+attributes_lang_id).prop('checked') == true) {
            $('.isFree_'+attributes_lang_id+' option[value="1"]').prop("selected", true);
            $('.isFree_'+attributes_lang_id).prop('disabled', true);
            $('.isFree_input_'+attributes_lang_id).val('1');

            $('.is_free_input_'+attributes_lang_id).val('');
            $('.is_free_input_'+attributes_lang_id).prop('disabled', true);

        } else {
            $('.isFree_'+attributes_lang_id+' option[value="0"]').prop("selected", true);
            $('.isFree_input_'+attributes_lang_id).val('0');
            // $('.isFree_'+attributes_lang_id).prop('disabled', false);
            $('.is_free_input_'+attributes_lang_id).prop('disabled', false);
        }
    }
</script>
<script>
  function product_for_change() {
        var product_for = $('#product_for').val();

        if (product_for == 'dish') {
            $('.product_for_dish').removeClass('hide');
            $('.product_for_other').addClass('hide');
            $('.attibutes_div').addClass('hide');

        } else if (product_for == 'other') {
            $('.product_for_dish').addClass('hide');
            $('.product_for_other').removeClass('hide');
            $('.attibutes_div').removeClass('hide');
        }
        
    }

    function getBrands() {
        var main_category_id = $('#main_category_id').val();
        var brand_id = "<?php echo $product->brand_id; ?>";

        if (main_category_id) {
          $.ajax({
              url:'{{url('product/show_brands')}}/'+main_category_id+'/'+brand_id,
              dataType: 'html',
              success:function(result)
              {
                  $('.show_brandDiv').html(result);
                  $('.attributes_show_div').html('');
                  getRestro();
                  getCategory();
              }
          });
        }
    }

    function getRestro() {
        var main_category_id = $('#main_category_id').val();
        var brand_id = $('#brand_id').val();
        var restaurant_id = "<?php echo $product->restaurant_id; ?>";
        var main_category_name = $( "#main_category_id option:selected" ).text();

        if (main_category_id && brand_id) {
          $.ajax({
               url:'{{url('product/show_restro')}}/'+main_category_id+'/'+brand_id+'/'+restaurant_id,
               dataType: 'html',
               success:function(result)
               {
                  $('.show_restroDiv').html(result);

                  if (main_category_name == 'Food' || main_category_name == 'food' || main_category_id == '2') {
                    $('.product_for').val('dish');
                    $('.restaurant_label').text('Store*');
                    $('.product_for_dish').removeClass('hide');
                    $('.product_for_other').addClass('hide');
                    $('.attibutes_div').addClass('hide');

                    $("#customize_option option[value='combination']").remove();

                    if ($("#customize_option option[value='normal']").length < 1) {
                        $("#customize_option").append('<option value="normal">Normal</option>');

                    } else {
                        $("#customize_option option[value='normal']").attr('selected', 'selected');
                    }

                  } else {
                    $('.product_for').val('other');
                    $('.restaurant_label').text('Store*');
                    $('.product_for_dish').addClass('hide');
                    $('.product_for_other').removeClass('hide');
                    $('.attibutes_div').removeClass('hide');

                    $("#customize_option option[value='normal']").remove();

                    if ($("#customize_option option[value='combination']").length < 1) {
                        $("#customize_option").append('<option value="combination">Combination</option>');

                    } else {
                        $("#customize_option option[value='normal']").attr('selected', 'selected');
                    }
                  }
                  getCategory();
               }
          });

        } else {
          $('#restaurant_id').empty().append('<option value="">--Select--</option>');
        }

    }

    function getSubCategory() {
      var category_id = $('#category_id').val();
      var sub_category_id = "<?php echo $product->sub_category_id; ?>";

      if (category_id) {
        $.ajax({
            url:'{{url('subcategory/show_sub_category')}}/'+category_id+'/'+sub_category_id,
            dataType: 'html',
            success:function(result)
            {
              $('.show_subcategoryDiv').html(result);
            }
        });
      }
    }

    function show_delivery_time() {
      var shop_type = "<?php echo $product->shop_type; ?>";

      if (shop_type == 'localshop') {
          $('.localshop_type').removeClass('hide');
          $('.internationshop_type').addClass('hide');

      } else if (shop_type == 'internationshop') {
          $('.localshop_type').addClass('hide');
          $('.internationshop_type').removeClass('hide');
      }        
    }

    function show_delivery_hours() {
      var delivery_time = "<?php echo $product->delivery_time; ?>";

      if (delivery_time == 'manual') {
          $('.timeSelectBox').removeClass('hide');

      } else {
          $('.timeSelectBox').addClass('hide');
      }        
    }

    $('input[type=radio][name=shop_type]').change(function() {
        if (this.value == 'localshop') {
            $('.localshop_type').removeClass('hide');
            $('.internationshop_type').addClass('hide');
        }
        else if (this.value == 'internationshop') {
            $('.localshop_type').addClass('hide');
            $('.internationshop_type').removeClass('hide');
        }
    });

    $('input[type=radio][name=delivery_time]').change(function() {

        if (this.value == 'manual') {
            $('.timeSelectBox').removeClass('hide');

        } else {
            $('.timeSelectBox').addClass('hide');
        }
    });

    function getAttributeValue($this) {
        var attribute_id = $($this).val();
        $.ajax({
           url:'{{url('product/show_attribute_value')}}/'+attribute_id,
           dataType: 'html',
           success:function(result)
           {
                $("#selecte_attribute_id option[value='"+attribute_id+"']").prop('disabled',true);
                $('.show_attribute_value_div').append(result);
           }
        });
    }

    function removeAttributeDiv($this) {
      var attribute_id = $($this).attr('attribute_id');
      $("#selecte_attribute_id option[value='"+attribute_id+"']").prop('disabled',false);
      $('.attributeValues_'+attribute_id).remove();
    }

    function getCategory() {
        var main_category_id = $('#main_category_id').val();
        var category_id = "<?php echo $product->category_id; ?>";

        if (main_category_id) {
          $.ajax({
             url:'{{url('product/show_restro_category')}}/'+main_category_id+'/'+category_id,
             dataType: 'html',
             success:function(result)
             {
                $('.show_restro_category_div').show();
                $('.show_restro_category_div').html(result);
                getSubCategory();
             }
          });

        } else {
          $('.show_restro_category_div').hide();
        }
    }

    function calculateKp() {
      calculateKpPercentage();
      calculateExtraKpPercentage();
    }

    function calculateKpPercentage() {
      var price_edit = parseInt($('#price_edit').val());
      var discount_price_edit = parseInt($('#discount_price_edit').val());
      var points = parseInt($('#points').val());
      var calculatedKP = '';

      if (points && discount_price_edit != "") {

        if (discount_price_edit && discount_price_edit != "") {
          calculatedKP = parseInt((discount_price_edit/100) * points);
          $('.kpWillUpdate').text(calculatedKP+' KP`s Received');

        } else {
          calculatedKP = parseInt((price_edit/100) * points);
          $('.kpWillUpdate').text(calculatedKP+' KP`s Received');
        }
      }
    }

    function calculateExtraKpPercentage() {
      var price_edit = parseInt($('#price_edit').val());
      var discount_price_edit = parseInt($('#discount_price_edit').val());
      var points = parseInt($('#extra_kilopoints').val());
      var calculatedKP = '';

      if (points && discount_price_edit != "") {

        if (discount_price_edit && discount_price_edit != "") {
          calculatedKP = parseInt((discount_price_edit/100) * points);
          $('.extraKpWillUpdate').text(calculatedKP+' KP`s Received');

        } else {
          calculatedKP = parseInt((price_edit/100) * points);
          $('.extraKpWillUpdate').text(calculatedKP+' KP`s Received');
        }
      }
    }
$(document).ready(function(){
  // getRestro();
  getBrands();
  getCategory();
  customizationOption();
  setTimeout(function(){ customizedOptionChange() }, 1500);
  product_for_change();
  show_delivery_time();
  show_delivery_hours();
$('#edit_role').parsley();
$('.select2').select2();
$('.select3').select2();
$("#edit_role").on('submit',function(e){ 
  e.preventDefault();
  var _this=$(this); 
    var descriptionedit_en = CKEDITOR.instances.descriptionedit_en.getData();
    var descriptionedit_ar = CKEDITOR.instances.descriptionedit_ar.getData();
    
    var formData = new FormData(this);
    formData.append('_method', 'put');
    formData.append('description[en]', descriptionedit_en);
    formData.append('description[ar]', descriptionedit_ar);
    
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
    url:'{{ url('api/product/'.$product->id) }}',
    dataType:'json',
    data:formData,
    type:'POST',
    cache:false,
    contentType: false,
    processData: false,
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(res){
            if(res.status === 1){ 
              toastr.success(res.message);
              $('#edit_role').parsley().reset();
              window.location.href = "{{ url('product') }}"
               // window.location.reload();
            }else{
              toastr.error(res.message);
            }
      },
    error:function(jqXHR,textStatus,textStatus){
      if(jqXHR.responseJSON.errors){
        $.each(jqXHR.responseJSON.errors, function( index, value ) {
          toastr.error(value)
        });
      }else{
        toastr.error(jqXHR.responseJSON.message)
      }
    }
      });
      return false;   
    });

  $("#addButtonEdit").click(function () {
    if(counter>10){
      toastr.error("Only 10 textboxes allow");
      return false;
    }
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDivEdit' + counter);
    newTextBoxDiv.after().html('<div class="row"><div class="col-md-6"><input type="text" class="form-control" placeholder="Ingredients English" name="ingredients[en][]" id="textbox' + counter + '" value="" ></div><div class="col-md-6"><input type="text" class="form-control" placeholder="Ingredients Arabic" name="ingredients[ar][]" id="textbox' + counter + '" value="" ></div></div>');
    newTextBoxDiv.appendTo("#TextBoxesGroupEdit");
    counter++;
 });
 $("#removeButtonEdit").click(function () {

  if(counter==2){
    toastr.error("one textbox is Required");
    return false;
  }
  counter--;
  $("#TextBoxDivEdit" + counter).remove();
 });


 $(".chefPriceEdit").change(function(){
   let value = $('#chef_id_edit').val();
   if(value.length !== 0){
    $("#chefDivEdit").show();
   }else{
    $("#chefDivEdit").hide();
    $('#chef_price_edit').val(0);
   }
 })
 $(".celebrityPriceEdit").change(function(){
  let value = $('#celebrity_id_edit').val();
  if(value !== ''){
    $("#celebrityDivEdit").show();
  }else{
    $("#celebrityDivEdit").hide();
    $('#celebrity_price_edit').val(0);
  }
 })

    $("#admin_price_edit, #chef_price_edit, #celebrity_price_edit,#celebrity_id_edit, #chef_id_edit").on('keyup change', function (){
      let total = 0;
      let chefPrice = 0;
      let celebrityPrice = 0;
      let adminPrice = $('#admin_price_edit').val();
      let chefDropDownValue = $('#chef_id_edit').val();
      let celebrityDropDownValue = $('#celebrity_id_edit').val();
      if(chefDropDownValue.length !== 0){
        chefPrice = $('#chef_price_edit').val();
      }else{
        chefPrice = 0;
        $('#chef_price_edit').val(0);
      }
      if(celebrityDropDownValue !== ''){
        celebrityPrice = $('#celebrity_price_edit').val();
      }else{
        celebrityPrice = 0;
        $('#celebrity_price_edit').val(0);
      }

      total = Number(adminPrice) + Number(chefPrice) + Number(celebrityPrice);
      $('#price_edit').val(total);

    })

    $("#editfile").change(function(){
      var fileObj = this.files[0];
      var imageFileType = fileObj.type;
      var imageSize = fileObj.size;
    
      var match = ["image/jpeg","image/png","image/jpg"];
      if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
        $('#editpreviewing').attr('src','images/no-image-available.png');
        toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
        return false;
      }else{
        //console.log(imageSize);
        if(imageSize < 1000000){
          var reader = new FileReader();
          reader.onload = imageIsLoaded;
          reader.readAsDataURL(this.files[0]);
        }else{
          toastr.error('Images Size Too large Please Select 1MB File!!');
          return false;
        }
        
      }
    
  });
});

function imageIsLoaded(e){
    $("#file").css("color","green");
    $('#editpreviewing').attr('src',e.target.result);

  }
</script>
<script>
    $(document).ready(function(){
        CKEDITOR.replace(document.getElementById('descriptionedit_en'))  
        CKEDITOR.replace(document.getElementById('descriptionedit_ar'))    
        CKEDITOR.replace(document.getElementById('recipe_descriptionedit_en'))  
        CKEDITOR.replace(document.getElementById('drecipe_descriptionedit_ar'))      
    });
 </script>

<link href="{{ URL::asset('dist/magicsuggest/magicsuggest.css')}}" rel="stylesheet">
<script src="{{ URL::asset('dist/magicsuggest/magicsuggest.js')}}"></script>
<script type="text/javascript">
    $(function() {
        var continent = $('.input_tag_test_en').magicSuggest({
           /* autoSelect: false,
            allowFreeEntries: false,*/
            maxSelection:10,
            placeholder: 'Enter tag name',
            data: '{{url('product/showTags')}}',
            method:'get',
            valueField: 'tag',
            displayField: 'tag',
            renderer: function(data){
                return '<div class="country">' +
                        '<div class="name">' + data.tag + '</div>' +
                        '<div style="clear:both;"></div>' +
                        '<div style="clear:both;"></div>' +
                    '</div>';
            }
        });

        var continent = $('.input_tag_test_ar').magicSuggest({
           /* autoSelect: false,
            allowFreeEntries: false,*/
            maxSelection:10,
            placeholder: 'Enter tag name',
            data: '{{url('product/showTagsAr')}}',
            method:'get',
            valueField: 'tag',
            displayField: 'tag',
            renderer: function(data){
                return '<div class="country">' +
                        '<div class="name">' + data.tag + '</div>' +
                        '<div style="clear:both;"></div>' +
                        '<div style="clear:both;"></div>' +
                    '</div>';
            }
        });

    });
</script>
 @endsection