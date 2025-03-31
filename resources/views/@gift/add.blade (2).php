@extends('layouts.master')
@section('content')
<?php
   use App\Models\Language;
   $language = Language::pluck('lang')->toArray();
   
   ?>
   <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<div class="container-fluid">
  <div class="row page-titles">
      <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
          <a href="{{ url('gift') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
          <h4 class="text-themecolor">{{ __('Add Gift') }}</h4>
        </div>
      </div>
      <div class="col-md-7 align-self-center text-right">
          <div class="d-flex justify-content-end align-items-center">
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                  <li class="breadcrumb-item"><a href="{{ url('product') }}">{{ __('Gift Manager') }}</a></li>
                  <li class="breadcrumb-item active">{{ __('Add Gift') }}</li>
              </ol>
          </div>
      </div>
  </div>
  <!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('gift') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
  </div> -->

   <div class="modal-content">
      <form method="POST" action="{{ url('api/gift') }}" id="add_form">
         @csrf
         <div class="modal-body">
            <!--  <ul class="nav nav-tabs">
               @foreach($language as $key => $lang)
               <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#tab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
               @endforeach
               </ul> -->
            <div class="tab-content" style="margin-top:10px">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                      <label class="control-label" for="brand_id">Brand*</label>
                      <select name="brand" class="form-control select3" data-parsley-required="true" >
                      <option value="" >---Select Brand----</option>
                      @if(isset($brands))
                      @foreach($brands as $key => $brand)
                      <option value="{{$brand['id']}}"> {{$brand['name']}}</option>
                      @endforeach
                      @endif
                      </select>
                  </div>
                </div>
              </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="control-label" for="category_id">Category*</label>
                        <select name="category_id" class="form-control category_id" id="category_id" onchange="category_change()" data-placeholder="Select Gift category" style="width: 100%;" data-parsley-required="true">
                           <option value="">--Select Gift--</option>
                           @foreach ($category as $category)
                           <option value="{{ $category->id }}">{{ $category->name }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6 sub_categories_show_div">
                     <div class="form-group">
                        <label class="control-label" for="sub_category_id">Sub Category*</label>
                        <select name="sub_category_id" id="sub_category_id" class="form-control" data-placeholder="Select sub category" style="width: 100%;" >
                           <option value="">--Select Sub-Category--</option>
                        </select>
                     </div>
                  </div>
               </div>

               <div class="row">
               @foreach($language as  $key => $lang)
               <div class="col-md-6">
                     <div class="form-group">
                        <label class="control-label" for="name">{{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
                        <input type="text" name="name[{{$lang}}]" data-parsley-required="true" id="name" class="form-control" placeholder="Name"  />
                     </div>
                  </div>
               @endforeach
               </div>



               @if($lang)
               <div class="row">
               @foreach($language as  $key => $lang)
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}})* </label>
                        <textarea id="description_{{$lang}}"  class="form-control ckeditor" placeholder="Description"></textarea>
                     </div>
                  </div>
               @endforeach
               </div>
               @endif
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="points">Kilo Points*</label>
                  <input type="text" name="points" value="" id="points" class="form-control" placeholder="Points" data-parsley-required="true" data-parsley-type="digits"  />
                </div>
              </div>
                <!-- <div class="col-md-6">
                  <div class="form-group">
                    <label for="image">Gift Discount* (%)</label>
                    <input type="text" name="gift_discount" id="gift_discount" class="form-control" placeholder="Gift Discount" data-parsley-type="digits" data-parsley-required="true"  />
                  </div>
                </div> -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="quantity">Quantity*</label>
                  <input type="text" name="quantity" id="quantity"  class="form-control" placeholder="Quantity" data-parsley-required="true" data-parsley-type="digits"  />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label  for="image">Weight*</label>
                  <input type="text" name="weight" id="gift_discount" class="form-control" placeholder="Weight" data-parsley-required="true"  />
                  </div>
                </div>

                
              
                  <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="brand_id">Gift Feature*</label>
                        <select name="gift_feature" class="form-control" data-parsley-required="true" >
                          <option value="">--Select Here--</option>
                          <option value="Yes">Yes</option>
                          <option value="No">No</option>
                        </select>
                    </div>
                  </div>
                 <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="is_ready">Is Ready*</label>
                        <select name="is_ready" class="form-control" data-parsley-required="true" >
                          <option value="">--Select Here--</option>
                          <option value="Yes">Yes</option>
                          <option value="No">No</option>
                        </select>
                    </div>
                  </div>

                  <div class="col-md-6">
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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="video">Video</label>
                      <input type="text" name="video" id="video" class="form-control" value="" placeholder="Video Url" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="sku_code">SKU Code*</label><br>
                        <input type="text" name="sku_code" id="sku_code" value="" class="form-control" placeholder="SKU Code" data-parsley-required="true"  />
                    </div>
                  </div>

                 <div class="col-md-12">
                    <label for="image">Image</label>
                    <div class="form-group">
                      <div class="input-group">
                        <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                          <input type="file" id="file" name="image" class="form-control">
                        </div>
                        <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
                      </div>
                    </div>
               </div>
                <div class="row">
                  <!-- <div class="form-group margin-class">
                    <label for="variant">Gift variant</label>
                    <div class="input_fields_wrap">
                        <div>
                          <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control" placeholder="Variant name (en)" name="variant[0][en]"> 
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control"  placeholder="Variant name (ar)"  name="variant[0][ar]"> 
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success add_field_button">Add More Fields</button>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div> -->
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
                              <!-- <option value="normal">Normal</option> -->
                              <option selected value="combination">Combination</option>
                          </select>
                      </div>
                  </div>
                </div>
                <div class="col-md-12 attributes_show_div">
                        
                </div>
              </div>

            </div>

         <div class="modal-footer justify-content-between">
            <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
         </div>
         
      </form>
      </div>
   </div>
</div>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script>
var counter = 0;
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<br/><div><div class="row"> <div class="col-md-5"><input type="text" class= "form-control"  placeholder="Variant name (en)" name="variant[' + x + '][en]"/></div> <div class="col-md-5"> <input type="text" class="form-control"  placeholder="Variant name (ar)" name="variant[' + x + '][ar]"/> </div> <button type="button" class="btn btn-danger remove_field">Remove</button> </div> </div>'); // add input boxes.
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});

function customizationOption() {

    if ($("#customization").prop('checked') == true){
       $('.customized_option_box').removeClass('hide'); 

    } else {
       $('.customized_option_box').addClass('hide');
    }
}

function customizedOptionChange() {
    var optionValue = $( "#customize_option option:selected" ).val();
    var category_id = $('#category_id').val();
    var sub_category_id = $('#sub_category_id').val();
    
    if (category_id && sub_category_id) {

      if (optionValue == 'combination') {
        showAttributes(category_id, sub_category_id);

      } else {
        showAttributes(category_id, sub_category_id, 'single');
      }

    } else {
      // $("#customize_option option:selected").removeAttr("selected");
      toastr.error('Please select category and sub-category first to see combinations.');
    }
}

function showAttributes(category_id, sub_category_id, customizeOptionValue='') {

  if (customizeOptionValue) {
      $.ajax({
         url:'{{url('gift_topping/show_single_attributes')}}/'+category_id+'/'+sub_category_id+'/'+customizeOptionValue,
         dataType: 'html',
         success:function(result)
         {
          $('.attributes_show_div').html(result);
         }
      });

  } else {
      $.ajax({
         url:'{{url('gift_topping/show_attributes')}}/'+category_id+'/'+sub_category_id,
         dataType: 'html',
         success:function(result)
         {
          $('.attributes_show_div').html(result);
         }
      });
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

function addMoreAttributeValues() {
    var category_id = $('#category_id').val();
    var sub_category_id = $('#sub_category_id').val();
    counter++;
    $.ajax({
       url:'{{url('gift_topping/show_attribute_values')}}/'+category_id+'/'+sub_category_id+'/'+counter,
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
        var category_id = $('#category_id').val();
        var sub_category_id = $('#sub_category_id').val();

        if (attribute_id && category_id && sub_category_id) {
            counter++;
            $.ajax({
               url:'{{url('gift_topping/show_attribute_values')}}/'+category_id+'/'+sub_category_id+'/'+counter+'/'+attribute_id,
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
</script>


<script>
   var ajax_datatable;
   function category_change() {
     var category_id = $(".category_id").val();
     //alert(category_id);
      $.ajax({
        url:'{{url('gift/show_subcategory')}}/'+category_id,
        dataType: 'html',
        success:function(result)
        {
         $('.sub_categories_show_div').html(result);
        } 
     });
   }

$(document).ready(function(){

@can('Gift-create')
$('#add_form').parsley();
$('.select2').select2();
$('.select3').select2();
$("#add_form").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
  var description_en = CKEDITOR.instances.description_en.getData();
  //alert(description_en);
  var description_ar = CKEDITOR.instances.description_ar.getData();
   var formData = new FormData(this);
    formData.append('description[en]', description_en);
    formData.append('description[ar]', description_ar);
    $.ajax({
    url:'{{ url('api/gift') }}',
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
          $('#add_form')[0].reset();
          window.location.href = "{{ url('gift') }}";
          $('#add_form').parsley().reset();
          $('#previewing').attr('src','images/no-image-available.png');
          $('.previewing').html('');
          ajax_datatable.draw();
          CKEDITOR.instances.description_en.setData('');
           CKEDITOR.instances.description_ar.setData('');
         $('.select2').val(null).trigger('change');
         $('.select3').val(null).trigger('change');
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
    function imageIsLoaded(e){
      $("#file").css("color","green");
      $('#editpreviewing').attr('src',e.target.result);
    }
});

   $(document).ready(function(){
     CKEDITOR.replaceClass('ckeditor');       
   });

 $(document).ready(function(){
       CKEDITOR.replace(document.getElementById('description'))       
   });


  
</script>

@endsection