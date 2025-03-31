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
      <h4 class="text-themecolor">{{ __('backend.Edit_Facility_owner') }}</h4>
    </div>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active"> {{ __('backend.Edit_Facility_owner') }} </li>
      </ol>

    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="content">
    <form method="PUT" action="{{ url('admin/api/facility_owner/'.$users->id) }}" enctype="" id="edit_facility_owner">
      @csrf
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="name"> {{__('backend.Facility_owner_Name')}} </label>
                    <input type="text" name="name" data-parsley-required="true" data-parsley-pattern="^[A-Za-z ]+$" data-parsley-pattern-message="{{__('backend.validation_only_alpha_space')}}" value="{{$users->name}}" id="Facility_owner_name" class="form-control" placeholder=" Facility Owner Name" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="email">{{__('backend.email')}}*</label>
                    <input type="text" name="email" value="{{$users->email}}" id="email" class="form-control" placeholder="{{__('backend.email')}}" autocomplete="off" data-parsley-required="true" />
                  </div>
                </div>

              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="mobile">{{ __('backend.Mobile') }}*</label>
                    <div class="row">
                      <div class="col-md-4">
                        <select name="country_code" class="form-control" data-parsley-required="true">
                          @foreach ($country as $country)
                          <option value="{{ $country->phonecode }}" {{ $users->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-8">
                        <input type="text" id="mobile" name="mobile" placeholder="{{ __('backend.Mobile') }}" value="{{$users->mobile}}" class="form-control form-control-line" data-parsley-required="true" data-parsley-required="true" data-parsley-pattern="^[0-9 ]{8,12}$"  minlength="8" maxlength ="12"  data-parsley-pattern-message="{{__('backend.mobile_digits_between')}}">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="Gender">{{ __('backend.Gender') }}*</label>
                    <div class="row">
                      <div class="col-md-12">
                        <select name="gender" class="form-control" data-parsley-required="true">
                          <option value="Male" {{ $users->gender == "Male"?'selected':'' }}>Male</option>
                          <option value="Female" {{ $users->gender == "Female"?'selected':'' }}>Female</option>
                          <option value="Other" {{ $users->gender == "Other"?'selected':'' }}>Other</option>
                        </select>
                      </div>
                    
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="show_post_method">Show Post Payment Method*</label>
                    <select name="show_post_method" id="show_post_method" class="form-control select2" data-placeholder="Show Post Payment Method" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                      <option value="Yes" {{$users->show_post_method == 'Yes' ? 'selected':''}} >Yes</option>
                      <option value="No" {{$users->show_post_method == 'No' ? 'selected':''}} >No</option>
                    </select>
                  </div>
                </div>
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
                      <div id="image_preview"><img height="100" width="100" id="previewing" src="{{$users->image}}"></div>
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
          <a href="{{ url('admin/facility_owner') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
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
      format: 'HH:mm',
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
    $('.select2').select2();
    $('#edit_facility_owner').parsley();

    $(document).on('submit', "#edit_facility_owner", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = new FormData(this);
      formData.append('_method', 'put');

      $('#group_loader').fadeIn();
      $.ajax({
        url: "{{ url('admin/api/facility_owner/'.$users->id) }}",
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
            window.location.href = "{{url('admin/facility_owner')}}";
            toastr.success(result.message)
          } else {
            toastr.error(result.message)
            $('.save').prop('disabled', false);
            $('.formloader').css("display", "none");
          }
          $('#edit_facility_owner').parsley().reset();
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
    $("#file").css("color", "green");
    $('#previewing').attr('src', e.target.result);
  }
</script>
@endsection