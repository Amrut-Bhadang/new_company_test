@extends('layouts.master')
@section('content')
<?php
use App\Models\Language;
use App\Models\GiftVarientLang;
use App\Models\GiftLang;
$language = Language::pluck('lang')->toArray();
?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<div class="container-fluid">
  <div class="row page-titles">
      <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
          <a href="{{ url('gift') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
          <h4 class="text-themecolor">{{ __('Edit Gift') }}</h4>
        </div>
      </div>
      <div class="col-md-7 align-self-center text-right">
          <div class="d-flex justify-content-end align-items-center">
              <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                  <li class="breadcrumb-item"><a href="{{ url('product') }}">{{ __('Gift Manager') }}</a></li>
                  <li class="breadcrumb-item active">{{ __('Edit Gift') }}</li>
              </ol>
          </div>
      </div>
  </div>
  <!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('gift') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
  </div> -->
  <div class="modal-content gift_edit">
    <form method="PUT" action="{{ url('api/gift/'.$gift->id) }}" id="edit_gift_manager" style="width: 100%">
    @csrf
    <div class="modal-body">
      <div class="tab-content" style="margin-top:10px">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
                <label class="control-label" for="brand_id">Brand*</label>
                <select name="brand" class="form-control select3" data-parsley-required="true" >
                <option value="" >---Select Brand----</option>
                @if(isset($brands))
                @foreach($brands as $key => $brand)
                <option value="{{$brand->id}}"{{ $gift->brand_id== $brand->id?'selected':'' }}> {{$brand->name}}</option>
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
              <select name="category_id" class="form-control edit_category_id" id="category_id" onchange="category_change2()" data-placeholder="Select Gift category" style="width: 100%;" data-parsley-required="true" >
                <option>--Select Gift--</option>
                @foreach ($category as $category)
                    <option value="{{ $category->id }}" {{ $gift->category_id== $category->id?'selected':'' }}>{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6 edit_sub_categories_show_div">
                
          </div>
        </div>

       
      @if($lang)
      <div class="row">
      @foreach($language as  $key => $lang)
      <?php
      if(isset($category))
      {
          $langData = GiftLang::where(['lang'=>$lang,'gift_id'=>$gift->id])->first();
          //dd($langData);  
      } ?>
      <!-- <div id="edittab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
            <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="{{$langData->name}}" id="name" class="form-control" placeholder=" Name"  />
          </div>
        </div>
      @endforeach
      @endif
      </div>

      @if($lang)
      <div class="row">
        @foreach($language as  $key => $lang)
        <?php
        if(isset($category))
        {
            $langData = GiftLang::where(['lang'=>$lang,'gift_id'=>$gift->id])->first();
            //dd($langData);  
        } ?>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}})*</label>
            <textarea  id="descriptionedit_{{$lang}}" class="form-control ckeditor"  placeholder="Description">{{$langData->description}}</textarea>
          </div>
        </div>
      
      <!-- </div> -->
      @endforeach
      @endif
      </div>
      
      <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="points">Kilo Points*</label>
          <input type="text" name="points" value="{{ $gift->points }}" id="points" class="form-control" placeholder="Points" data-parsley-type="digits" data-parsley-required="true"  />
        </div>
      </div>
      <!-- <div class="col-md-6">
        <div class="form-group">
          <label for="gift_discount">Gift Discount* (%)</label>
        <input type="text" name="gift_discount" value="{{ $gift->discount }}" id="gift_discount" class="form-control" data-parsley-type="digits" placeholder="Gift Discount" data-parsley-required="true"  />
        </div>
      </div> -->

      <div class="col-md-6">
          <div class="form-group">
            <label for="quantity">Quantity*</label>
          <input type="text" name="quantity" id="quantity" value="{{ $gift->quantity }}" class="form-control" data-parsley-type="digits" placeholder="Quantity" data-parsley-required="true"  />
          </div>
        </div>
      
        <div class="col-md-6">
          <div class="form-group">
            <label for="weight">Weight*</label>
          <input type="text" name="weight" id="weight" value="{{ $gift->weight }}" class="form-control" placeholder="Weight" data-parsley-required="true"  />
          </div>
        </div>

        <div class="col-md-6">
        <div class="form-group">
            <label class="control-label" for="gift_feature">Gift Feature*</label>
            <select name="gift_feature" class="form-control" data-parsley-required="true" >
            <option value="">--Select Here--</option>
            <option value="yes" {{$gift->is_featured == 'Yes'? 'selected':'' }}>Yes</option>
            <option value="no" {{$gift->is_featured == 'No'? 'selected':''}}>No</option>
            </select>
        </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
              <label class="control-label" for="gift_feature">Is Ready*</label>
              <select name="is_ready" class="form-control" data-parsley-required="true">
              <option value="">--Select Here--</option>
              <option value="Yes" {{$gift->is_ready == 'Yes'? 'selected':''}}>Yes</option>
              <option value="No" {{$gift->is_ready == 'No'? 'selected':''}}>No</option>
              </select>
          </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="delivery_hours">Delivery Time*</label><br>
                <select class="form-control" name="delivery_hours" id="delivery_hours">
                    <option value="">--Select Hours--</option>
                    @for($i=1; $i <= 50; $i++)
                    <option {{$gift->delivery_hours == $i ? 'selected' : '' }} value="{{$i}}">{{$i}} Days</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="video">Video</label>
            <input type="text" name="video" id="video" class="form-control" value="{{$gift->video}}" placeholder="Video Url" />
          </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="sku_code">SKU Code*</label><br>
                <input type="text" name="sku_code" id="sku_code" value="{{ $gift->sku_code }}" class="form-control" placeholder="SKU Code" data-parsley-required="true"  />
            </div>
        </div>    


       
        <div class="col-md-12">
          <label for="image">Image</label>
        <div class="form-group">
          <div class="input-group">
            <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$gift->main_image}}"></div>
            <input type="file" id="editfile" name="image" class="form-control">
          </div>
          <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
        </div>
      </div>
      <!-- <div class="col-md-6">
          <div class="form-group margin-class">
             <label for="image">Gift variant <button class="btn btn-success add_field_button">Add More Fields</button></label>
             @foreach ($varient as $key => $varients)
              <?php            
              if(isset($varients->id))
              {
                  $langData_en = GiftVarientLang::where(['lang'=>'en','gift_varient_id'=>$varients->id])->first();
                  $langData_ar = GiftVarientLang::where(['lang'=>'ar','gift_varient_id'=>$varients->id])->first();  
              }
              ?>
            <div class="input_fields">
                <div>
                  <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" placeholder="Variant name (en)" value="{{$langData_en->name}}" name="variant[{{$key}}][en]"> 
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control"  placeholder="Variant name (ar)" value="{{$langData_ar->name}}" name="variant[{{$key}}][ar]"> 
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success add_field_button">Add More Fields</button>
                    </div>
                  </div>
                </div>
            </div>
            @endforeach
            <div class="input_fields_wrap"></div>

          </div>
                <button class="add_field_button">Add More Fields</button>
        </div> -->

        <div class="col-md-6">
          <div class="form-group input-group">
              <input type="checkbox" name="customization" {{$gift->customization == 'Yes' ? 'checked' : '' }} onclick="customizationOption()" id="customization"> 
              <label for="image">Customization*</label>
          </div>
        </div>

        <div class="col-md-6 customized_option_box hide">
          <div class="form-group">
            <label for="image">Customization Option*</label>
            <select class="form-control" name="customize_option" onchange="customizedOptionChange()" id="customize_option">
                <option value="">--Select Option--</option>
                <!-- <option {{$gift->customize_option == 'normal' ? 'selected' : ''}} value="normal">Normal</option> -->
                <!-- <option {{$gift->customize_option == 'combination' ? 'selected' : ''}} value="combination">Combination</option> -->
                <option selected value="combination">Combination</option>
            </select>
          </div>
        </div>
        <div class="col-md-12 attributes_show_div">
          
        </div>
      </div>
      
      </div>
      
    </div>     
            
    <!-- <hr style="margin: 1em -15px"> -->
    <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button> -->
        <div class="modal-footer justify-content-between">
            <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
        </div>

</form>
  </div>
</div>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script>
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<br/><div> <div class="row"> <div class="col-md-5"><input type="text" class= "form-control"  placeholder="Variant name (en)" name="variant[' + x + '][en]"/></div> <div class="col-md-5"> <input type="text" class="form-control"  placeholder="Variant name (ar)" name="variant[' + x + '][ar]"/> </div> <button type="button" class="btn btn-danger remove_field">Remove</button> </div> </div>'); // add input boxes.
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
</script>
<script>
var counter = "<?php echo count($selected_attributes_lang); ?>";
$(document).ready(function(){
  var sub_category_id = "{{$gift->sub_category_id}}";
  category_change2(sub_category_id);
  customizationOption();
  setTimeout(function(){ customizedOptionChange() }, 1500);
$('#edit_gift_manager').parsley();
$('.select2').select2();
$('.select3').select2();
$("#edit_gift_manager").on('submit', function(e) { 
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
    url:'{{ url('api/gift/'.$gift->id) }}',
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
        if(result.status){
          toastr.success(result.message);
          window.location.href = "{{ url('gift') }}";
          $('#edit_gift_manager').parsley().reset();
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
        if(imageSize < 5000000){
          var reader = new FileReader();
          reader.onload = imageIsLoaded;
          reader.readAsDataURL(this.files[0]);
        }else{
          toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
          return false;
        }
        
      }
    
  });
function imageIsLoaded(e){
  $("#file").css("color","green");
  $('#editpreviewing').attr('src',e.target.result);
}
});

function category_change2(sub_category_id = '') {
  var category_id = $(".edit_category_id").val();

   $.ajax({ 
     url:'{{url('gift/show_subcategory')}}/'+category_id+'/'+sub_category_id,
     dataType: 'html',
     success:function(result)
     {
      $('.edit_sub_categories_show_div').html(result);
     } 
  });
}

function customizationOption() {

    if ($("#customization").prop('checked') == true){
       $('.customized_option_box').removeClass('hide');
       $('.attributes_show_div').removeClass('hide');

    } else {
       $('.customized_option_box').addClass('hide');
       
       $('.attributes_show_div').html('');
       $('.attributes_show_div').addClass('hide');
       // $("#customize_option option:selected").prop("selected", false)

        $.each($("#customize_option option:selected"), function () {
            $(this).prop('selected', false); // <-- HERE
        });
    }
}

/*function customizationOption() {

    if ($("#customization").prop('checked') == true){
       $('.customized_option_box').removeClass('hide'); 

    } else {
       $('.customized_option_box').addClass('hide');
    }
}*/

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
  var gift_id = "<?php echo $gift->id; ?>";

    if (customizeOptionValue) {
        $.ajax({
           url:'{{url('gift_topping/show_single_attributes')}}/'+category_id+'/'+sub_category_id+'/'+customizeOptionValue+'/'+gift_id,
           dataType: 'html',
           success:function(result)
           {
            $('.attributes_show_div').html(result);
           }
        });

    } else {
        $.ajax({
           url:'{{url('gift_topping/show_attributes')}}/'+category_id+'/'+sub_category_id+'/'+gift_id,
           dataType: 'html',
           success:function(result)
           {
            $('.attributes_show_div').html(result);
           }
        });
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
}

function removeAttributeValues(id) {
    $('.attributes_values_tr_'+id).remove();
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
    $(document).ready(function(){
        CKEDITOR.replace(document.getElementById('descriptionedit_en'))  
        CKEDITOR.replace(document.getElementById('descriptionedit_ar'))      
    });
 </script>

@endsection
