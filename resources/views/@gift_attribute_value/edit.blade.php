@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
use App\Models\ProductLang;
use App\Models\GiftAttributeValueLang;
$language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
            <a href="{{ url('gift_attribute_value') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
            <h4 class="text-themecolor">{{ __('Edit Attribute Value') }}</h4>
          </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('attribute') }}">{{ __('Attribute Manager') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Attribute') }}</li>
            </ol>
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('gift_attribute_value') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
</div> -->
<div class="content">
  <div class="row">
        <form method="POST" action="{{ url('api/gift_attribute_value'.$attribute_value->id) }}" id="edit_attribute" style="width: 100%">
            @csrf   
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="tab-content" style="margin-top:10px">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="category_id">Category*</label>
                                        <select disabled="" name="category_id" id="category_id" class="form-control select3" data-placeholder="Select Category" style="width: 100%;" data-parsley-required="true" >
                                            <option value=''>--Select Category--</option>
                                            @foreach ($category as $category)
                                                <option value="{{ $category->id }}" {{ $attribute_value->category_id == $category->id?'selected':'' }} >{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 categories_show_div">
                                    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="attributes_lang_id">Attribute Name*</label>
                                        <select name="attributes_lang_id" disabled="" class="form-control select3" data-placeholder="Select Category" style="width: 100%;" data-parsley-required="true" >
                                            <option value=''>--Select attribute name--</option>
                                            @foreach ($attributes_lang as $attributes_lang)
                                                <option value="{{ $attributes_lang->id }}" {{ $attribute_value->attributes_lang_id == $attributes_lang->id?'selected':'' }} >{{ $attributes_lang->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label class="control-label" for="points">Attribute Value*</label>
                                    <a id='addButtonEdit' class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a>
                                    <!-- <a id='removeButtonEdit' class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a> -->
                                    <div id='TextBoxesGroupEdit'>
                                      @foreach ($attribute_value_langs as $key => $attribute)
                                        <div id="TextBoxDivEdit{{$key+1}}">
                                        
                                          <?php

                                          if (isset($attribute->id)) {
                                              $langData_en = GiftAttributeValueLang::where(['id'=>$attribute->id])->first();  
                                          }
                                          ?>
                                            <br/>
                                            <div class="row appendAttrDiv_{{$key+1}}">
                                              <div class="col-md-4">
                                                <input type='text' id='textboxedit{{$key+1}}' name="old_attributes[en][{{$langData_en['id']}}][]" value="{{$langData_en['name']}}" class="form-control" placeholder="Attribute Value English" data-parsley-required="true">
                                              </div>
                                              <div class="col-md-4">
                                                <input type='text' id='textboxedit{{$key+1}}' name="old_attributes[ar][{{$langData_en['id']}}][]" value="{{$langData_en['lang']}}" class="form-control" placeholder="Attribute Value Arabic" >
                                              </div>

                                              @if($attribute_value->is_color == 1)
                                                <div class="col-md-2">
                                                  <input type='color' id='textboxedit{{$key+1}}' name="old_attributes[color_code][{{$langData_en['id']}}][]" value="{{$langData_en['color_code']}}" class="form-control" placeholder="Color Code" >
                                                </div>
                                              @endif
                                              @if($key > 0)
                                                <a onclick="removeAttributeField('{{$key+1}}')" class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a>
                                              @endif
                                            </div>
                                        </div>
                                      @endforeach
                                    </div>
                                  </div>
                                </div>                    
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <a href="{{ url('/gift_attribute_value') }}" class="btn btn-warning waves-effect waves-light m-r-10" data-dismiss="modal">Close</a>
                                        <button type="submit" style="margin-left:20px" class="btn btn-primary waves-effect waves-light m-r-10 save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript">

    function getCategory() {
        var category_id = $('#category_id').val();
        var sub_category_id = "<?php echo $attribute_value->sub_category_id ?>";
        $.ajax({
           url:'{{url('gift_attribute_value/show_category')}}/'+category_id+'/'+sub_category_id,
           dataType: 'html',
           success:function(result)
           {
            $('.categories_show_div').html(result);
           }
        });
    }
</script>
<script>
getCategory();
$(document).ready(function(){
$('#edit_attribute').parsley();
$('.select2').select2();
$('.select3').select2();
$("#edit_attribute").on('submit',function(e){ 
  e.preventDefault();
    var _this=$(this);    
    var formData = new FormData(this);
    formData.append('_method', 'put');    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
    url:'{{ url('api/gift_attribute_value/'.$attribute_value->id) }}',
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
              window.location.href = "{{ url('gift_attribute_value') }}"
              // $('#edit_attribute').parsley().reset();
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

  var counter = <?php echo count($attribute_value_langs)+1; ?>;
  $("#addButtonEdit").click(function () {
    if(counter>10){
      toastr.error("Only 10 textboxes allow");
      return false;
    }
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDivEdit' + counter);

    @if($attribute_value->is_color == 1)
      newTextBoxDiv.after().html('<br/><div class="row appendAttrDiv_'+counter+'"><div class="col-md-4"><input type="text" class="form-control" placeholder="Attribute Value English" name="attributes[en][]" id="textbox' + counter + '" value="" ></div><div class="col-md-4"><input type="text" class="form-control" placeholder="Attribute Value Arabic" name="attributes[ar][]" id="textbox' + counter + '" value="" ></div><div class="col-md-2"><input type="color" id="textbox' + counter + '" name="attributes[color_code][]" value="" class="form-control" placeholder="Color Code" ></div><a onclick="removeAttributeField('+counter+')" class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a></div>');
    @else 
      newTextBoxDiv.after().html('<br/><div class="row appendAttrDiv_'+counter+'"><div class="col-md-4"><input type="text" class="form-control" placeholder="Attribute Value English" name="attributes[en][]" id="textbox' + counter + '" value="" ></div><div class="col-md-4"><input type="text" class="form-control" placeholder="Attribute Value Arabic" name="attributes[ar][]" id="textbox' + counter + '" value="" ></div><a onclick="removeAttributeField('+counter+')" class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a></div>');
    @endif
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

  function removeAttributeField(id) {
    $('.appendAttrDiv_'+id).remove();
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
 @endsection