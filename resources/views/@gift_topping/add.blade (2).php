@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
use App\Models\ToppingLang;
$language = Language::pluck('lang')->toArray();
?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <div class="d-flex align-items-center">
        <a href="{{ url('topping') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Add Item Specific') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('topping') }}">{{ __('Item Specific Manager') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Item Specific') }}</li>
            </ol>
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('topping') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
</div> -->
<div class="content">
	<div class="row">
		<div class="col-md-12">
			<form method="POST" action="{{ url('api/topping') }}" id="add_category">
				@csrf 
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="tab-content" style="margin-top:10px">
                            <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label for="dish">Main Category</label>
                                    <select name="main_category_id" id="main_category_id" onchange="getAttributes()" class="form-control select2" data-placeholder="Select main category" style="width: 100%;" data-parsley-required="true" >
                                        <option value=''>--Select main category--</option>
                                        @foreach ($main_category as $main_category)
                                            <option value="{{ $main_category->id }}">{{ $main_category->name }}</option>
                                        @endforeach
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6 categories_show_div">
                                  
                                </div>
                                <div class="col-md-6 dishes_show_div">
                                  
                                </div>
                                <!-- @if($language)
                                @foreach($language as $key => $lang)
                                <?php /*
                                  if(isset($category))
                                  {
                                      $langData = ToppingLang::where(['lang'=>$lang,'dish_topping_id'=>$category->id])->first();
                                  } */ ?>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label class="control-label" for="name"> {{__('backend.topping_name')}} ({{__('backend.'.$lang)}})*</label>
                                      <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="" id="name" class="form-control" placeholder="Name"  />
                                    </div>
                                  </div>
                                @endforeach
                                @endif -->
                              </div>
                                <div class="row">
                                  <div class="col-md-12 attributes_show_div">
                                    
                                  </div>
                                </div> 
                                <div class="row">
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <a href="{{ url('/topping') }}" class="btn btn-warning waves-effect waves-light m-r-10" data-dismiss="modal">Close</a>
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
</div>

<script>
  var counter = 0;
  function getAttributes() {
    $('.dishes_show_div').html('');
    $('.attributes_show_div').html('');
    var main_category_id = $('#main_category_id').val();
    $.ajax({
       url:'{{url('topping/show_category')}}/'+main_category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.categories_show_div').html(result);
       }
    });
  }

  function showAttributes(main_category_id, category_id) {
    $.ajax({
       url:'{{url('topping/show_attributes')}}/'+main_category_id+'/'+category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.attributes_show_div').html(result);
       }
    });
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

  function removeAttributeValues(id) {
    $('.attributes_values_tr_'+id).remove();
  }

  function getDish() {
    var main_category_id = $('#main_category_id').val();
    var category_id = $('#category_id').val();
    $.ajax({
       url:'{{url('topping/show_dishes')}}/'+category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.dishes_show_div').html(result);
       }
    });
    showAttributes(main_category_id, category_id);
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

  $("#add_category").on('submit',function(e){
    e.preventDefault();
    var _this=$(this); 
      var formData = new FormData(this);
      $.ajax({
          url:'{{ url('api/topping') }}',
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
                  window.location.href = "{{ url('topping') }}"

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
var ajax_datatable;
$(document).ready(function(){
  $('.select2').select2();
  $('.select3').select2();
  $('#add_category').parsley();

@can('Toppings-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('topping/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Toppings-edit')
//Edit staff
$(document).on('click','.edit_category',function(e){
    e.preventDefault();
    $('#edit_category_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('topping/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_category_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Toppings-delete')
$(document).on('click','.delete_category',function(e){
  e.preventDefault();
  var response = confirm('Are you sure want to delete this category?');
  if(response){
    id = $(this).data('category_id');
    $.ajax({
        type: 'post',
        data: {_method: 'delete', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('api/topping' )!!}" + "/" + id,
        success:function(res){
          if(res.status === 1){ 
              toastr.success(res.message);
              ajax_datatable.draw();
            }else{
              toastr.error(res.message);
            }
        },   
        error:function(jqXHR,textStatus,textStatus){
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
    });
  }
  return false;
}); 
@endcan


$(document).on('change','.is_mandatory',function(e){
    e.preventDefault();
    //$('#music_category_type').hide();
    if($(this).val()=='1'){
      $('#price').hide();
    } else {
      $('#price').show();
    }
  });

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


  });

  function imageIsLoaded(e){
      //console.log(e);
      $("#file").css("color","green");
      $('#previewing').attr('src',e.target.result);
    }
</script>

<script>
var ajax_datatable;
$(document).ready(function(){
    $('.input-daterange').datepicker({
  todayBtn:'linked',
  format:'yyyy-mm-dd',
  autoclose:true
 });
});

$('#search-form').on('submit', function(e) {
      ajax_datatable.draw();
        e.preventDefault();
});


</script>


@endsection