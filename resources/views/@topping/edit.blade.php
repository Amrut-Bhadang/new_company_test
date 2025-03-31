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
        <h4 class="text-themecolor">{{ __('Edit Item Specific') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('topping') }}">{{ __('Item Specific Manager') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Item Specific') }}</li>
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
    <form method="PUT" action="{{ url('api/topping/'.$category->id) }}" id="edit_role">
            @csrf   
            <div class="col-md-12">
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
                                            <option {{$category->main_category_id == $main_category->id ? "selected" : ""}} value="{{ $main_category->id }}">{{ $main_category->name }}</option>
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
                                <!-- <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="is_mandatory">Is Mandatory*</label>
                                      <select name="is_mandatory" class="form-control is_mandatory_edit multiple-search" style="width: 100%;" data-parsley-required="true">
                                          <option value=''>--Select--</option>
                                          <option value="1" {{ $category->is_mandatory=='1'?'selected':'' }}>Yes</option>
                                          <option value="0" {{ $category->is_mandatory=='0'?'selected':'' }}>No</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group {{$category->is_mandatory == '0' ? 'show' : 'hide'}}" id="edit_price">
                                      <label for="price">Price (QAR)</label>
                                      <input type="text" id="price" name="price" value="{{ $category->price }}" class="form-control" placeholder="Price (QAR)" data-parsley-type="digits">
                                    </div>
                                  </div>
                                </div> -->
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
                </div>
            </div>
        </form>
  </div>
</div>

<script>
  var counter = "<?php echo $product_attributes; ?>";
  function getAttributes() {
    $('.dishes_show_div').html('');
    $('.attributes_show_div').html('');
    var main_category_id = $('#main_category_id').val();
    var selected_category = "<?php echo $category->category_id ?>";
    $.ajax({
       url:'{{url('topping/show_category')}}/'+main_category_id+'/'+selected_category,
       dataType: 'html',
       success:function(result)
       {
        $('.categories_show_div').html(result);
        getDish();
       }
    });

    // showAttributes(main_category_id);
  }

  function showAttributes(main_category_id, category_id) {
    var selected_dish = "<?php echo $category->dish_id ?>";
    $.ajax({
       url:'{{url('topping/show_attributes')}}/'+main_category_id+'/'+category_id+'/'+selected_dish,
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

  function getDish() {
    var main_category_id = $('#main_category_id').val();
    var category_id = $('#category_id').val();
    var selected_dish = "<?php echo $category->dish_id ?>";
    $.ajax({
       url:'{{url('topping/show_dishes')}}/'+category_id+'/'+selected_dish,
       dataType: 'html',
       success:function(result)
       {
        $('.dishes_show_div').html(result);
       }
    });
    showAttributes(main_category_id, category_id);
  }
$(document).ready(function(){
  getAttributes();
  $('#edit_role').parsley();
  $('.select2').select2();
  $('.select3').select2();

});

$("#edit_role").on('submit',function(e){
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
      url:'{{ url('api/topping/'.$category->id) }}',
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
$(document).on('change','.is_mandatory_edit',function(e){
  e.preventDefault();
  //$('#music_category_type').hide();
  if($(this).val()=='1'){
    $('#edit_price').hide();
  } else {
    $('#edit_price').show();
  }
});
</script>
@endsection