@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />
<script src="{{ asset('dist/js/jquery-1.11.1.min.js') }}"></script>

<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
            <a href="{{ url('product') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
            <h4 class="text-themecolor">{{ __('Add New Item') }}</h4>
        </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('product') }}">{{ __('Product Manager') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add New Item') }}</li>
            </ol>
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('product') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
</div> -->
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="row">
        <form method="POST" action="{{ url('api/product') }}" id="add_form" style="width: 100%">
        @csrf   
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                    <!-- <ul class="nav nav-tabs">
                        @foreach($language as $key => $lang)
                        <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#tab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
                        @endforeach
                    </ul> -->
                    <div class="tab-content" style="margin-top:10px">
                    <div class="row">
                        <input type="hidden" class="product_for" name="product_for" value="">
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                            <label class="control-label" for="product_for">Product For *</label>
                            <select name="product_for" id="product_for" onchange="product_for_change()" class="form-control celebrityPrice select3" data-placeholder="Select Product For" data-parsley-required="true" style="width: 100%;">
                                <option value=''>--Select Product For--</option>
                                <option value="dish">Restaurant</option>
                                <option value="other">Other</option>
                            </select>
                            </div>
                        </div> -->
                        @if($user_type == 4)
                            <input type="hidden" name="main_category_id" id="main_category_id" value="{{$main_category_id}}">
                        @endif
                        @if($user_type != 4)
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label" for="main_category_id">Service*</label>
                                  <select name="main_category_id" id="main_category_id" onchange="getBrands()" class="form-control multiple-search"  data-parsley-required="true" >
                                    <option value="">---Select Service----</option>
                                    @foreach ($main_category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-6 show_brandDiv">
                                <div class="form-group">
                                  <label class="control-label" for="brand_id">Vendor*</label>
                                  <select name="brand_id" class="form-control multiple-search" data-parsley-required="true" >
                                    <option value="">---Select Vendor----</option>
                                  </select>
                                </div>
                            </div>
                        @endif

                        @if($user_type == 4)
                            <input type="hidden" name="main_category_id" id="main_category_id" value="{{$main_category_id}}">
                            <input type="hidden" name="brand_id" id="brand_id" value="{{$brand_id}}">
                            <input type="hidden" name="restaurant_id" id="restro_login_id" value="{{$restaurant_id}}">
                        @endif

                        @if($user_type == 1)
                            <div class="col-md-6 show_restroDiv">
                                <div class="form-group">
                                    <label class="control-label restaurant_label" for="restaurant_id">Store*</label>
                                    <select name="restaurant_id" id="restaurant_id" onchange="getCategory()" class="form-control celebrityPrice select3" data-placeholder="Select" style="width: 100%;">
                                        <option value=''>--Select--</option>
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
                    <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="control-label" for="name">{{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
                                <input type="text" name="name[{{$lang}}]" data-parsley-required="true" data-parsley-maxlength="30" class="form-control" placeholder="Name"  />
                                </div>
                            </div>
                    @endforeach
                        </div>
                    @endif
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                <label class="control-label" for="recipe_description">{{__('backend.recipe_description')}} ({{__('backend.'.$lang)}})</label>
                                <textarea  id="recipe_description_{{$lang}}" data-parsley-required="true" class="form-control ckeditor" placeholder="Recipe Description"></textarea>
                                </div>
                            </div> -->
                    @if($lang)
                        <div class="row">
                    @foreach($language as  $key => $lang)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}})*</label>
                                    <textarea  id="description_{{$lang}}" class="form-control ckeditor" placeholder="Description"></textarea>
                                </div>
                            </div>
                       <!--  </div> -->
                    @endforeach
                        </div>
                    @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 product_type hide">
                                <div class="form-group">
                                <label class="control-label" for="product_type">Type*</label>
                                <select name="product_type" class="form-control" style="width: 100%;">
                                    <option value=''>--Select Type--</option>
                                    <option value="Veg">Veg</option>
                                    <option value="Non-Veg">Non-Veg</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6 preparation_time hide">
                              <div class="form-group">

                                <label class="control-label" for="prepration_time">Preparation Time (In Minutes)*</label>
                                <input type="text" name="prepration_time" value="" id="prepration_time" class="form-control" placeholder="Preparation time" autocomplete="off" data-parsley-type="digits"  />
                              </div>
                            </div>
                            @if($lang)
                                @foreach($language as  $key => $lang)
                                    <div class="col-md-6">
                                      <div class="form-group tag_div">
                                        <label class="control-label" for="prepration_time">Tag Input ({{__('backend.'.$lang)}})*</label>
                                        <!-- <input type="text" id="#inputTag" name="inputTag[{{$lang}}]" class="form-control" placeholder="Enter tags" value="" data-role="tagsinput"> -->
                                        <input type="text" id="inputTag{{$lang}}" name="inputTag[{{$lang}}]" class="form-control input_tag_test_{{$lang}}" placeholder="Enter tags" value="">
                                      </div>
                                    </div> 
                                @endforeach
                            @endif
                            <div class="col-md-6 original_price">
                                <div class="form-group">
                                <label class="control-label" for="price">MRP(QAR)*</label>
                                <input type="text" name="price" value="" id="price" class="form-control" onkeyup="calculateKp()" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="MRP"/>
                                </div>
                            </div>
                            <div class="col-md-6 discount_price">
                                <div class="form-group">
                                <label class="control-label" for="admin_price">Discount Price(QAR)</label>
                                <input type="text" name="discount_price" value="" id="discount_price" class="form-control" onkeyup="calculateKp()" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Discount Price" />
                                </div>
                            </div>
                            <div class="col-md-6 kilopoints">
                                <div class="form-group">
                                    <label class="control-label" for="points">Kilo Points(%)*</label>
                                    <input type="text" name="points" value="" id="points" class="form-control" onkeyup="calculateKp()" placeholder="Kilo Points" min="1" max="100" data-parsley-required="true" data-parsley-type="digits"  />
                                    <span class="kpWillUpdate"></span>
                                </div>
                            </div>
                            <div class="col-md-6 extra_kilopoints">
                                <div class="form-group">
                                    <label class="control-label" for="extra_kilopoints">Extra Kilo Points(%)</label>
                                    <input type="text" name="extra_kilopoints" value="" id="extra_kilopoints" class="form-control" onkeyup="calculateKp()" min="1" max="100" placeholder="Extra Kilo Points" data-parsley-type="digits"  />
                                    <span class="extraKpWillUpdate"></span>
                                </div>
                            </div>
                            <div class="col-md-6 videoUrl hide">
                                <div class="form-group">
                                    <label class="control-label" for="video">Video</label>
                                    <input type="text" name="video" id="video" class="form-control" placeholder="Video Url" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                <label class="control-label" for="celebrity_id">Celebrity</label>
                                <select name="celebrity_id" id="celebrity_id" class="form-control celebrityPrice select3" data-placeholder="Select Celebrity" style="width: 100%;">
                                    <option value=''>--Select Celebrity--</option>
                                    @foreach ($celebrity as $celebrity)
                                        <option value="{{ $celebrity->id }}">{{ $celebrity->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6" >
                                <div class="form-group" id="celebrityDiv" style="display:none">
                                <label class="control-label" for="celebrity_price">Celebrity Price</label>
                                <input type="text" name="celebrity_price" value="" id="celebrity_price" class="form-control" placeholder="Celebrity Price"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="control-label" for="chef_id">Chef</label>
                                <select name="chef_id[]" id="chef_id" class="select2 chefPrice" multiple="multiple" data-placeholder="Select Chef" data-dropdown-css-class="select2-primary" style="width: 100%;">
                                    @foreach ($chef as $chef)
                                        <option value="{{ $chef->id }}">{{ $chef->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6" >
                                <div class="form-group" id="chefDiv" style="display:none">
                                <label class="control-label" for="chef_price">Chef Price</label>
                                <input type="text" name="chef_price" value="" id="chef_price" class="form-control" placeholder="Chef Price"/>
                                </div>
                            </div> -->
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                <label class="control-label" for="admin_price">Admin Prices *</label>
                                <input type="text" name="admin_price" value="" id="admin_price" class="form-control" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" data-parsley-required="true" placeholder="Admin Prices"   />
                                </div>
                            </div> -->
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="serve">Serve *</label>
                                    <select class="form-control" data-parsley-required="true" name="serve" id="serve">
                                        <option value="">--Select Here--</option>
                                        @for($i=1; $i <= 10; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div> -->
                        </div>
                        <div class="row product_for_other hide">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="shop_type">Shop Type *</label><br>
                                    <input type="radio" id="localshop" name="shop_type" value="localshop">
                                    <label for="localshop">Local Shop</label>
                                    <input type="radio" id="internationshop" name="shop_type" value="internationshop">
                                    <label for="internationshop">Internation Shop</label>
                                </div>
                            </div>

                            <div class="col-md-6 localshop_type hide">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_time">Delivery Time *</label><br>
                                    <input type="radio" id="sameday" name="delivery_time" value="sameday">
                                    <label for="sameday">Same Day</label>
                                    <input type="radio" id="nextday" name="delivery_time" value="nextday">
                                    <label for="nextday">Next Day</label>
                                    <input type="radio" id="manual" name="delivery_time" value="manual">
                                    <label for="manual">Manual</label>
                                </div>
                            </div>

                            <div class="col-md-6 internationshop_type hide">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_time">Delivery Time *</label><br>
                                    <!-- <input type="radio" id="express" name="delivery_time" value="express">
                                    <label for="express">Express</label>
                                    <input type="radio" id="normal" name="delivery_time" value="normal">
                                    <label for="normal">Normal</label> -->
                                    <input type="radio" id="manual_international" name="delivery_time" value="manual">
                                    <label for="manual_international">Manual</label>
                                </div>
                            </div>

                            <div class="col-md-6 timeSelectBox hide">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_hours">Delivery Time *</label><br>
                                    <select class="form-control" name="delivery_hours" id="delivery_hours">
                                        <option value="">--Select Days--</option>
                                        @for($i=1; $i <= 50; $i++)
                                        <option value="{{$i}}">{{$i}} Days</option>
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
                                        <select name="selecte_attribute_id" id="selecte_attribute_id" onchange="getAttributeValue(this)" class="form-control celebrityPrice select3" data-placeholder="Select Restaurant" style="width: 100%;">
                                            <option value=''>--Select Attributes--</option>
                                            @foreach ($attributes_lang as $attributes_name)
                                                <option value="{{ $attributes_name->id }}">{{ $attributes_name->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="show_attribute_value_div">
                                
                            </div>
                        </div> -->
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="points">Product Ingredients *</label>
                                    <a id='addButton' class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a>
                                    <a id='removeButton' class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a>
                                    <div id='TextBoxesGroup'>
                                        <div id="TextBoxDiv1">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type='text' id='textbox1' name="ingredients[en][]" class="form-control" placeholder="Ingredients English">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type='text' id='textbox1' name="ingredients[ar][]" class="form-control" placeholder="Ingredients Arabic" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        
                        <hr class="row" style="margin: 0 -10px;">
                        <div class="row"> 
                            <div class="col-md-6">
                                <label for="image">SKU Code*</label>
                                <div class="form-group input-group">
                                    <input type="text" name="sku_code" id="sku_code" class="form-control" data-parsley-required="true" placeholder="SKU Code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="image">Product Image</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                                        <input type="file" id="file" name="image" class="form-control">
                                    </div>
                                    <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group input-group">
                                    <div class="input-group"> 
                                        <input type="checkbox" name="customization" onclick="customizationOption()" id="customization">
                                        <label class="control-label" for="delivery_time">Customization*</label>
                                        <!-- <label for="image">Customise Your Product</label> -->
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-10 col-md-9 col-sm-6 customized_option_box hide">
                                <div class="form-group">
                                    <label for="image">Customization Option*</label>
                                    <select class="form-control" name="customize_option" onchange="customizedOptionChange()" id="customize_option">
                                        <option value="">--Select Option--</option>
                                        <option value="normal">Normal</option>
                                        <option value="combination">Combination</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="name">Other Images</label>
                                    <input type='file' id="multipalImage" class="form-control" name="multipalImage[]" multiple>
                                    <div class="previewing"></div>
                                </div>
                            </div> -->
                            
                            </div>
                        </div>
                        <div class="col-md-12 attributes_show_div">
                        
                        </div>
                        <!-- <div class="row">
                        </div>  -->
                        <hr style="margin: 1em -15px">
                        <div class="show_subcategoryDiv">
          
                        </div>
                        <hr style="margin: 1em -15px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="submit" style="margin-left:20px" class="btn btn-primary waves-effect waves-light m-r-10 save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add_variant_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <form method="POST" action="{{ url('api/banner') }}" id="add_banner">
      @csrf
      <div class="modal-header">
        <h4 class="modal-title"> Add Varient Name </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label" for="attribute_id">Attributes</label>
                    <select name="attribute_id" id="attribute_id" onchange="getAttributeValue(this)" class="form-control celebrityPrice select3" data-placeholder="Select Attributes" style="width: 100%;">
                        <option value=''>--Select Attributes--</option>
                        @foreach ($attributes_lang as $attributes_name)
                            <option value="{{ $attributes_name->id }}">{{ $attributes_name->name }}</option>
                        @endforeach
                    </select>
                    <a id='addAttributeName' data-toggle="modal" data-target="#add_variant_modal" class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
      </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
    <!-- /.content -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    var counter = 0;
    var user_type = "<?php echo $user_type ?>";

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
        
        if (main_category_id && category_id) {

            if (optionValue == 'combination') {
                showAttributes(main_category_id, category_id);

            } else {
                showAttributes(main_category_id, category_id, 'single');
            }

        } else {
            toastr.error('Please select main category and category first to see combinations.');
        }
    }

    function showAttributes(main_category_id, category_id, customizeOptionValue='') {

        if (customizeOptionValue) {
            $.ajax({
               url:'{{url('topping/show_single_attributes')}}/'+main_category_id+'/'+category_id+'/'+customizeOptionValue,
               dataType: 'html',
               success:function(result)
               {
                $('.attributes_show_div').html(result);
               }
            });

        } else {
            $.ajax({
               url:'{{url('topping/show_attributes')}}/'+main_category_id+'/'+category_id,
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
        var totalAddMoreAttr = $($this).attr('data-totalAddMoreAttr');
        var rowCount = $('.attributes_values_tr_new_'+attribute_id).length;

        if (rowCount < totalAddMoreAttr) {
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

        } else {
            toastr.error('There is only '+totalAddMoreAttr+' options.');
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

    function product_for_change() {
        var product_for = $('#product_for').val();

        if (product_for == 'dish') {
            $('.restaurant_label').text('Store*');
            $('.product_type').removeClass('hide');
            // $('.kilopoints').removeClass('hide');
            $('.videoUrl').removeClass('hide');
            // $('.discount_price').removeClass('hide');
            // $('.original_price').removeClass('hide');
            $('.preparation_time').removeClass('hide');

            $('.product_for_other').addClass('hide');
            $('.attibutes_div').addClass('hide');

        } else if (product_for == 'other') {
            $('.restaurant_label').text('Store*');
            $('.product_type').addClass('hide');
            // $('.kilopoints').addClass('hide');
            $('.videoUrl').addClass('hide');
            // $('.discount_price').addClass('hide');
            // $('.original_price').addClass('hide');
            $('.preparation_time').addClass('hide');

            $('.product_for_other').removeClass('hide');
            $('.attibutes_div').removeClass('hide');
        }
    }

    function getBrands() {
        var main_category_id = $('#main_category_id').val();
        $.ajax({
            url:'{{url('product/show_brands')}}/'+main_category_id,
            dataType: 'html',
            success:function(result)
            {
                $('.show_brandDiv').html(result);
                $('.attributes_show_div').html('');
            }
        });
        getRestro();
    }

    function getRestro() {
        var main_category_id = $('#main_category_id').val();
        var brand_id = $('#brand_id').val();
        var main_category_name = $( "#main_category_id option:selected" ).text();
        $('.show_restro_category_div').hide();
        getCategory();

        if (main_category_id && brand_id) {
            $.ajax({
                 url:'{{url('product/show_restro')}}/'+main_category_id+'/'+brand_id,
                 dataType: 'html',
                 success:function(result)
                 {
                    $('.show_restroDiv').html(result);

                    if (main_category_name == 'Food' || main_category_name == 'food' || main_category_id == '2') {
                        $('.product_for').val('dish');
                        $('.restaurant_label').text('Store*');
                        $('.product_type').removeClass('hide');
                        // $('.kilopoints').removeClass('hide');
                        $('.videoUrl').removeClass('hide');
                        // $('.discount_price').removeClass('hide');
                        // $('.original_price').removeClass('hide');
                        $('.preparation_time').removeClass('hide');

                        $('.product_for_other').addClass('hide');
                        $('.attibutes_div').addClass('hide');

                        $("#customize_option option[value='combination']").remove();

                        if ($("#customize_option option[value='normal']").length < 1) {
                            $("#customize_option").append('<option selected value="normal">Normal</option>');

                        } else {
                            $("#customize_option option[value='normal']").attr('selected', 'selected');
                        }

                    } else {
                        $('.product_for').val('other');
                        $('.restaurant_label').text('Store*');
                        $('.product_type').addClass('hide');
                        // $('.kilopoints').addClass('hide');
                        $('.videoUrl').addClass('hide');
                        // $('.discount_price').addClass('hide');
                        // $('.original_price').addClass('hide');
                        $('.preparation_time').addClass('hide');

                        $('.product_for_other').removeClass('hide');
                        $('.attibutes_div').removeClass('hide');

                        $("#customize_option option[value='normal']").remove();

                        if ($("#customize_option option[value='combination']").length < 1) {
                            $("#customize_option").append('<option selected value="combination">Combination</option>');

                        } else {
                            $("#customize_option option[value='combination']").attr('selected', 'selected');
                        }
                    }
                 }
            });

        } else {
            $('#restaurant_id').empty().append('<option value="">--Select--</option>');
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

    function getCategory() {
        var restaurant_id = $('#restaurant_id').val();
        var main_category_id = $('#main_category_id').val();
        var main_category_name = $( "#main_category_id option:selected" ).text();
        var kp_percent = $( "#restaurant_id option:selected" ).attr('data-kp_percent');

        if (kp_percent) {
            $('#points').val(kp_percent);
        }

        if (main_category_id) {
            $.ajax({
               url:'{{url('product/show_restro_category')}}/'+main_category_id,
               dataType: 'html',
               success:function(result)
               {
                    $('.show_restro_category_div').show();
                    $('.show_restro_category_div').html(result);
                    getSubCategory();

                    if (main_category_name == 'Food' || main_category_name == 'food' || main_category_id == '2') {
                        $('.product_for').val('dish');
                        $('.restaurant_label').text('Store*');
                        $('.product_type').removeClass('hide');
                        // $('.kilopoints').removeClass('hide');
                        $('.videoUrl').removeClass('hide');
                        // $('.discount_price').removeClass('hide');
                        // $('.original_price').removeClass('hide');
                        $('.preparation_time').removeClass('hide');

                        $('.product_for_other').addClass('hide');
                        $('.attibutes_div').addClass('hide');

                    } else {
                        $('.product_for').val('other');
                        $('.restaurant_label').text('Store*');
                        $('.product_type').addClass('hide');
                        // $('.kilopoints').addClass('hide');
                        $('.videoUrl').addClass('hide');
                        // $('.discount_price').addClass('hide');
                        // $('.original_price').addClass('hide');
                        $('.preparation_time').addClass('hide');

                        $('.product_for_other').removeClass('hide');
                        $('.attibutes_div').removeClass('hide');
                    }
               }
            });

        } else {
            $('.show_restro_category_div').hide();
        }
    }

    function getSubCategory() {
      var category_id = $('#category_id').val();

      if (category_id) {
        $.ajax({
            url:'{{url('subcategory/show_sub_category')}}/'+category_id,
            dataType: 'html',
            success:function(result)
            {
              $('.show_subcategoryDiv').html(result);
            }
        });
      }
    }

    function addAttributeName() {
        $.ajax({
           url:'{{url('product/show_attribute_name')}}/',
           dataType: 'html',
           success:function(result)
           {
            $('.show_attribute_value_div').append(result);
           }
        });
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

    function calculateKp() {
      calculateKpPercentage();
      calculateExtraKpPercentage();
    }

    function calculateKpPercentage() {
      var price_edit = parseInt($('#price').val());
      var discount_price_edit = parseInt($('#discount_price').val());
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
      var price_edit = parseInt($('#price').val());
      var discount_price_edit = parseInt($('#discount_price').val());
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

var ajax_datatable;
$(document).ready(function(){

    if (user_type == 4) {
        getCategory();
    }
$('#add_form').parsley();
$('.select2').select2();
$('.select3').select2();
@can('Product-create')
$("#add_form").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
  var description_en = CKEDITOR.instances.description_en.getData();
  var description_ar = CKEDITOR.instances.description_ar.getData();
  /*
  var recipe_description_en = CKEDITOR.instances.recipe_description_en.getData();
  var recipe_description_ar = CKEDITOR.instances.recipe_description_ar.getData();*/
  
   var formData = new FormData(this);
    formData.append('description[en]', description_en);
    formData.append('description[ar]', description_ar);/*
    formData.append('recipe_description[en]', recipe_description_en);
    formData.append('recipe_description[ar]', recipe_description_ar);*/
    
    $.ajax({
    url:'{{ url('api/product') }}',
    dataType:'json',
    data:formData,
    type:'POST',
    cache:false,
    contentType: false,
    processData: false,
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
        if(result.status === 1){ 
          toastr.success(result.message);
          window.location.href = "{{ url('product') }}";
          $('#add_form')[0].reset();
          $('#add_form').parsley().reset();
          $('#previewing').attr('src',"{{URL::asset('images/image.png')}}");
          $('.previewing').html('');
          $('.select2').val(null).trigger('change');
          $('.select3').val(null).trigger('change');
          /*CKEDITOR.instances.recipe_description_en.setData('');
          CKEDITOR.instances.recipe_description_ar.setData('');*/
          CKEDITOR.instances.description_en.setData('');
           CKEDITOR.instances.description_ar.setData('');
           // window.location.reload();
        }else{
          toastr.error(result.message);
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
@endcan

$("#file").change(function(){
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;
  
    var match = ["image/jpeg","image/png","image/jpg"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#previewing').attr('src','images/image.png');
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

 $("#multipalImage").change(function(){
        if (event.target.files && event.target.files[0]) {
            var filesAmount = event.target.files.length;
            
            for (let i = 0; i < filesAmount; i++) {
                let files = event.target.files[i];
                var reader = new FileReader();
                reader.onload =  (event) => {
                    $('.previewing').append("<img width='100' height='100' src='"+event.target.result+"' />")
                }
                reader.readAsDataURL(event.target.files[i]);
            }
        }
        
    });
});
$(document).ready(function(){
  var counter = 2;
  $("#addButton").click(function () {
    if(counter>10){
      toastr.error("Only 10 textboxes allow");
      return false;
    }
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
    newTextBoxDiv.after().html('<div class="row"><div class="col-md-6"><input type="text" class="form-control" placeholder="Ingredients English" name="ingredients[en][]" id="textbox' + counter + '" value="" ></div><div class="col-md-6"><input type="text" class="form-control" placeholder="Ingredients Arabic" name="ingredients[ar][]" id="textbox' + counter + '" value="" ></div></div>');
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    counter++;
 });
 $("#removeButton").click(function () {

  if(counter==2){
    toastr.error("one textbox is Required");
    return false;
  }
  counter--;
  $("#TextBoxDiv" + counter).remove();
 });


 $(".chefPrice").change(function(){
   let value = $('#chef_id').val();
   if(value.length !== 0){
    $("#chefDiv").show();
   }else{
    $("#chefDiv").hide();
    $('#chef_price').val(0);
   }
 })
 $(".celebrityPrice").change(function(){
  let value = $('#celebrity_id').val();
  if(value !== ''){
    $("#celebrityDiv").show();
  }else{
    $("#celebrityDiv").hide();
    $('#celebrity_price').val(0);
  }
 })

 $("#admin_price, #chef_price, #celebrity_price,#celebrity_id, #chef_id").on('keyup change', function (){
  let total = 0;
  let chefPrice = 0;
  let celebrityPrice = 0;
  let adminPrice = $('#admin_price').val();
  let chefDropDownValue = $('#chef_id').val();
  let celebrityDropDownValue = $('#celebrity_id').val();
  if(chefDropDownValue.length !== 0){
     chefPrice = $('#chef_price').val();
  }else{
     chefPrice = 0;
     $('#chef_price').val(0);
  }
  if(celebrityDropDownValue !== ''){
     celebrityPrice = $('#celebrity_price').val();
  }else{
     celebrityPrice = 0;
     $('#celebrity_price').val(0);
  }

  total = Number(adminPrice) + Number(chefPrice) + Number(celebrityPrice);
  $('#price').val(total);

 })
});
function imageIsLoaded(e){
    //console.log(e);
    $("#file").css("color","green");
    $('#previewing').attr('src',e.target.result);

  }


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
