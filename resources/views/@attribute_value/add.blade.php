@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />

<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
            <a href="{{ url('attribute_value') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
            <h4 class="text-themecolor">{{ __('Add New Attribute Value') }}</h4>
          </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('attribute_value') }}">{{ __('Attribute Value Manager') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Attribute Value') }}</li>
            </ol>
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('attribute_value') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
</div> -->
<!-- /.content-header -->
<!-- Main content -->
<div class="content">
    <div class="row">
        <form method="POST" action="{{ url('api/attribute_value') }}" id="add_attribute" style="width: 100%">
            @csrf   
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="tab-content" style="margin-top:10px">
                            <div class="row">
                                @if($user_type == 4)
                                    <input type="hidden" name="main_category_id" id="main_category_id" value="{{$main_category_id}}">
                                @endif

                                @if($user_type != 4)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="main_category_id">Service*</label>
                                            <select name="main_category_id" id="main_category_id" onchange="getCategory()" class="form-control select3" data-placeholder="Select Service" style="width: 100%;" data-parsley-required="true" >
                                                <option value=''>--Select Service--</option>
                                                @foreach ($main_category as $main_category)
                                                    <option value="{{ $main_category->id }}">{{ $main_category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-<?php echo $user_type == 4 ? '12' : '6'; ?> categories_show_div">
            
                                </div>
                                <div class="col-md-12 attribute_show_div">
                                </div>
                                <input type="hidden" name="is_color" id="is_attribute_have_color" value="">
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="points">Attribute Values*</label>
                                        <a id='addButton' class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a>
                                        <!-- <a id='removeButton' class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a> -->
                                        <div id='TextBoxesGroup'>
                                            <div id="TextBoxDiv1">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type='text' id='textbox1' name="attributes[en][]" class="form-control" placeholder="Attribute Value English">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type='text' id='textbox1' name="attributes[ar][]" class="form-control" placeholder="Attribute Value Arabic" >
                                                    </div>
                                                    <div class="col-md-2 is_color_div hide">
                                                      <input type='color' id='textbox1' name="attributes[color_code][]" value="" class="form-control" placeholder="Color Code" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                        
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <a href="{{ url('/attribute_value') }}" class="btn btn-warning waves-effect waves-light m-r-10" data-dismiss="modal">Close</a>
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
<!-- /.content -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
    var user_type = "{{$user_type}}";

    function getCategory() {

        if (user_type == 4) {
          var main_category_id = "{{$main_category_id}}";

        } else {
            var main_category_id = $('#main_category_id').val();
        }

        $.ajax({
           url:'{{url('attribute_value/show_category')}}/'+main_category_id,
           dataType: 'html',
           success:function(result)
           {
            $('.categories_show_div').html(result);
           }
        });
    }
    function getAttributes() {
        var main_category_id = $('#main_category_id').val();
        var category_id = $('#category_id').val();
        $.ajax({
           url:'{{url('attribute_value/show_attributes')}}/'+main_category_id+'/'+category_id,
           dataType: 'html',
           success:function(result)
           {
            $('.attribute_show_div').html(result);
           }
        });
    }
    function attributeValueChange() {
        var attributes_lang_color = $('#attributes_lang_id option:selected').attr("is_color");
        $('#is_attribute_have_color').val(attributes_lang_color);

        if (attributes_lang_color == 1) {
            $('.is_color_div').removeClass('hide');

        } else {
            $('.is_color_div').addClass('hide');
        }
    }
</script>
<script>
    var ajax_datatable;
    $(document).ready(function(){
        getCategory();
        $('#add_attribute').parsley();
        $('.select2').select2();
        $('.select3').select2();
        @can('Attribute-create')
        $(document).on('submit', "#add_attribute",function(e){
            e.preventDefault();
            var _this=$(this); 
          
            var formData = new FormData(this);    
            $.ajax({
            url:'{{ url('api/attribute_value') }}',
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
                  $('#add_attribute')[0].reset();
                  $('#add_attribute').parsley().reset();
                  window.location.href = "{{ url('attribute_value') }}"
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
    });

$(document).ready(function(){
    var counter = 2;
    $("#addButton").click(function () {

        if (counter>10) {
          toastr.error("Only 10 textboxes allow");
          return false;
        }
        var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
        var is_attribute_have_color = $('#is_attribute_have_color').val();

        if (is_attribute_have_color == 1) {
            var classAdd = 'show';
        } else {
            var classAdd = 'hide';
        }
        newTextBoxDiv.after().html('<br/><div class="row appendAttrDiv_'+counter+'"><div class="col-md-4"><input type="text" class="form-control" placeholder="Attribute Value English" name="attributes[en][]" id="textbox' + counter + '" value="" ></div><div class="col-md-4"><input type="text" class="form-control" placeholder="Attribute Value Arabic" name="attributes[ar][]" id="textbox' + counter + '" value="" ></div><div class="col-md-2 is_color_div '+classAdd+'"><input type="color" id="textbox' + counter + '" name="attributes[color_code][]" value="" class="form-control" placeholder="Color Code" ></div><a onclick="removeAttributeField('+counter+')" class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a></div>');
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
});

function removeAttributeField(id) {
    $('.appendAttrDiv_'+id).remove();
}
</script>
@endsection
