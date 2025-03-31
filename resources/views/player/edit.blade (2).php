@extends('layouts.master')

@section('content')
<?php

use App\Models\Language;
use App\Models\CourtLang;

$language = Language::pluck('lang')->toArray();
?>
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <div class="d-flex align-items-center">
      <!-- <a href="{{ url('admin/players') }}" class="btn btn-info btn-sm mr-3"><i class="fa fa-arrow-left"></i> {{ __('backend.Back') }} </a> -->
      <h4 class="text-themecolor">{{ __('backend.Edit_player') }}</h4>
    </div>
  </div>
  <!-- <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"> Edit Court </h4>
    </div> -->
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active"> {{ __('backend.Edit_player') }} </li>
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
    <form method="PUT" action="{{ url('admin/api/players/'.$player->id) }}" enctype="multipart/form-data" id="edit_player">
      @csrf
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <!-- <h5 class="mb-2"> {{ __('backend.player_Details') }} </h5> -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="name"> {{__('backend.player_Name')}} </label>
                    <input type="text" name="name" data-parsley-required="true" data-parsley-pattern="^[A-Za-z ]+$" data-parsley-pattern-message="{{__('backend.validation_only_alpha_space')}}" value="{{$player->name}}" id="player_name" class="form-control" placeholder="{{__('backend.player_Name')}}" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="email">{{__('backend.email')}}*</label>
                    <input type="text" name="email" value="{{$player->email}}" id="email" class="form-control" placeholder="{{__('backend.email')}}" autocomplete="off" data-parsley-required="true" />
                  </div>
                </div>
              </div>
              <div class="row">
                <!-- <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="mobile">{{__('backend.mobile')}}*</label>
                    <input type="text" name="mobile" value="" id="mobile" class="form-control" placeholder="{{__('backend.mobile')}}" autocomplete="off" data-parsley-required="true" />
                  </div>
                </div> -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="mobile">{{ __('backend.Mobile') }}*</label>
                    <div class="row">
                      <div class="col-md-4">
                        <select name="country_code" class="form-control" data-parsley-required="true">
                          @foreach ($country as $country)
                          <option value="{{ $country->phonecode }}" {{ $player->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-8">
                        <input type="text" id="mobile" name="mobile" placeholder="{{ __('backend.Mobile') }}" value="{{$player->mobile}}" class="form-control form-control-line" data-parsley-required="true" data-parsley-pattern="^[0-9 ]{8,15}$" data-parsley-pattern-message="{{__('backend.validation_mobile_number')}}" minlength="8" maxlength="12">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="mobile">{{ __('backend.Gender') }}*</label>
                    <div class="row">
                      <div class="col-md-12">
                        <select name="gender" class="form-control" data-parsley-required="true">
                          <option value="">{{ __('backend.Select_gender') }}</option>
                          <option value="Male" {{ $player->gender == "Male"?'selected':'' }}>Male</option>
                          <option value="Female" {{ $player->gender == "Female"?'selected':'' }}>Female</option>
                          <option value="Other" {{ $player->gender == "Other"?'selected':'' }}>Other</option>
                        </select>
                      </div>
                     </div>
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label d-block" for="gender">{{__('backend.Gender')}}*</label>
                     <input type="radio" name="gender" value="" id="gender" class="form-control" placeholder="{{__('backend.gender')}}" autocomplete="off" data-parsley-required="true" />
                    <input type="radio" name="gender" value="" id="gender" class="form-control" placeholder="{{__('backend.gender')}}" autocomplete="off" data-parsley-required="true" /> 
                    <input type="radio" class="btn-check" name="gender" value="Male" id="male" autocomplete="off" {{ $player->gender== 'Male'?'checked':'' }}> 
                    <label class="control-label" for="male">Male</label>
                    <input type="radio" class="btn-check" name="gender" value="Female" id="female" autocomplete="off" {{ $player->gender== 'Female'?'checked':'' }}>
                    <label class="control-label" for="female">Female</label>
                    <input type="radio" class="btn-check" name="gender" value="Other" id="other" autocomplete="off" {{ $player->gender== 'Other'?'checked':'' }}>
                    <label class="control-label" for="other">Other</label>
                  </div>
                </div> -->
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
                      <div id="image_preview"><img height="100" width="100" id="previewing" src="{{$player->image}}"></div>
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
          <a href="{{ url('admin/players') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
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
  $(document).ready(function() {
    // initialize();
    // autoload({
    //   {
    //     $player - > latitude
    //   }
    // }, {
    //   {
    //     $player - > longitude
    //   }
    // });
    $('.select2').select2();
    $('#edit_player').parsley();

    $(document).on('submit', "#edit_player", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = new FormData(this);
      formData.append('_method', 'put');

      $('#group_loader').fadeIn();
      // var values = $('#edit_player').serialize();
      $.ajax({
        url: "{{ url('admin/api/players/'.$player->id) }}",
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
            window.location.href = "{{url('admin/players')}}";
            toastr.success(result.message)
          } else {
            toastr.error(result.message)
            $('.save').prop('disabled', false);
            $('.formloader').css("display", "none");
          }
          $('#edit_player').parsley().reset();
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