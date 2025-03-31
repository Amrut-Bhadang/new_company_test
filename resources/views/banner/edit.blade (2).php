@extends('layouts.master')

@section('content')
<?php

use App\Models\BannerLang;
use App\Models\Language;
use App\Models\CourtLang;

$language = Language::pluck('lang')->toArray();
?>
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <div class="d-flex align-items-center">
      <!-- <a href="{{ url('admin/banner') }}" class="btn btn-info btn-sm mr-3"><i class="fa fa-arrow-left"></i> {{ __('backend.Back') }} </a> -->
      <h4 class="text-themecolor">{{ __('backend.Edit_banner') }}</h4>
    </div>
  </div>
  <!-- <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"> Edit Court </h4>
    </div> -->
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active"> {{ __('backend.Edit_banner') }} </li>
      </ol>

    </div>
  </div>
</div>
<!-- <div class="row">
  <div class="col-md-6" style="margin-bottom: 10px;">
     <a href="{{ url('admin/courts') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
  </div>
</div> -->

<div class="container-fluid">

  <div class="content">
    <form method="PUT" action="{{ url('admin/api/banner/'.$banner->id) }}" enctype="" id="edit_banner">
      @csrf
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <!-- <h5 class="mb-2"> {{ __('backend.banner_Details') }} </h5> -->
              <div class="row">
                  <div class="col-md-6 ">
                    <div class="form-group">
                      <label class="control-label" for="type">{{ __('backend.banner_type') }}</label>
                        <select name="type" id="type" onchange="type_change()" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.banner_type') }}" data-dropdown-css-class="select2-primary">
                         <option value="">--{{ __('backend.Select') }} {{ __('backend.banner_type') }}--</option>
                          <option value="facility" {{ $banner->type == 'facility' ? 'selected' : '' }}>{{ __('backend.Facility') }}</option>
                          <option value="court" {{ $banner->type == 'court' ? 'selected' : '' }}>{{ __('backend.court') }}</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-md-6 type_data_show_div">
                    <div class="form-group">
                      <label class="control-label" for="type_id">{{ __('backend.banner_for') }}</label>
                        <select name="type_id" id="type_id" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.banner_for') }}" data-dropdown-css-class="select2-primary">
                         <option value="">--{{ __('backend.Select') }} {{ __('backend.banner_for') }}--</option>
                         
                        </select>
                    </div>
                  </div>
                </div>
              <div class="row">
              @if($language)
                    @foreach($language as  $key => $lang)
                    <?php
                      if(isset($banner))
                      {
                          $langData = BannerLang::where(['lang'=>$lang,'banner_id'=>$banner->id])->first();  
                      }
                  ?>
                <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="title"> {{__('backend.title')}} ({{__('backend.'.$lang)}})*</label>
                      <input type="text" name="title[{{$lang}}]" data-parsley-required="true" value="{{$langData->title}}" id="title" class="form-control" placeholder=" {{__('backend.title')}}"  />
                    </div>
                </div>
                @endforeach
                  @endif
              </div>
            </div>
          </div>
        </div>
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <h5 class=""> {{__('backend.Images')}} </h5>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <div class="form-group">
                    <div class="input-group">
                      <div id="image_preview"><img height="100" width="100" id="previewing" src="{{$banner->image}}"></div>
                      <div class="form-control" onclick="document.getElementById('file').click()">
                          <label for="files" >{{__('backend.Select_Image')}}</label>
                          <input type="file" id="file" name="image" style="visibility:hidden;" class="form-control">
                      </div>
                    </div>
                    <span class="text-muted">{{__('backend.Image_Note')}}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="save_btn">
          <button type="submit" class="btn btn-info save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>
          <a href="{{ url('admin/banner') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script type="text/javascript">
  $(function() {
    $('.datetimepicker').datetimepicker({
      // Formats
      // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
      format: 'HH:mm',

      // Your Icons
      // as Bootstrap 4 is not using Glyphicons anymore
      icons: {
        time: 'fa fa-clock-o',
        date: 'fa fa-calendar',
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-check',
        clear: 'fa fa-trash',
        close: 'fa fa-times'
      }
    });
  });
</script>

<script>
  function type_change() {
    var type = $('#type').val();
    var type_id = "{{$banner->type_id}}";
    if (type) {
        $.ajax({
           url:'{{url("admin/banner/show_type_data")}}/'+type+'/'+type_id,
           dataType: 'html',
           success:function(result)
           {
            $('.type_data_show_div').html(result);
            $('.link-div').hide();
           }
        });

    } else {
      $("input[name='category_type']:checked").prop('checked', false);
      toastr.error('Please choose main category first');
      return false;
    }
  }
  $(document).ready(function() {
    type_change();
    // initialize();
    // autoload({
    //   {
    //     $banner - > latitude
    //   }
    // }, {
    //   {
    //     $banner - > longitude
    //   }
    // });
    $('.select2').select2();
    $('#edit_banner').parsley();

    $(document).on('submit', "#edit_banner", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = new FormData(this);
      formData.append('_method', 'put');

      $('#group_loader').fadeIn();
      // var values = $('#edit_banner').serialize();
      $.ajax({
        url: "{{ url('admin/api/banner/'.$banner->id) }}",
        dataType: 'json',
        data: formData,
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
          before(_this)
        },
        // hides the loader after completion of request, whether successfull or failor.
        complete: function() {
          complete(_this)
        },
        success: function(result) {

          if (result.status) {
            window.location.href = "{{url('admin/banner')}}";
            toastr.success(result.message)
          } else {
            toastr.error(result.message)
            $('.save').prop('disabled', false);
            $('.formloader').css("display", "none");
          }
          $('#edit_banner').parsley().reset();
        },
        error: function(jqXHR, textStatus, textStatus) {
          if (jqXHR.responseJSON.errors) {
            $.each(jqXHR.responseJSON.errors, function(index, value) {
              toastr.error(value)
            });
          } else {
            toastr.error(jqXHR.responseJSON.message)
          }
        }
      });
      return false;
    });
  });
</script>

<script>
  $("#file").change(function() {
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;

    var file = $('#file')[0].files[0].name;
		$(this).prev('label').text(file);

    var match = ["image/jpeg", "image/png", "image/jpg"];
    if (!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))) {
      $('#previewing').attr('src', 'images/no-image-available.png');
      toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
      return false;
    } else {
      //console.log(imageSize);
      if (imageSize < 5000000) {
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[0]);
      } else {
        toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
        return false;
      }
    }
  });

  function imageIsLoaded(e) {
    //console.log(e);
    $("#file").css("color", "green");
    $('#previewing').attr('src', e.target.result);
  }
</script>
@endsection