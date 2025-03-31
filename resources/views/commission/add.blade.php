@extends('layouts.master')

@section('content')
<?php

use App\Models\Language;

$language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <div class="d-flex align-items-center">
      <!-- <a href="{{ url('admin/commission') }}" class="btn btn-info btn-sm mr-3"><i class="fa fa-arrow-left"></i> {{__('backend.Back')}} </a> -->
      <h4 class="text-themecolor">{{ __('backend.Add_commission') }}</h4>
    </div>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('backend.Add_commission') }}</li>
      </ol>
    </div>
  </div>
</div>
<!-- <div class="row">
  <div class="col-md-6" style="margin-bottom: 10px;">
     <a href="{{ url('admin/commission') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
  </div>
</div> -->
<div class="row">
  <div class="container-fluid">
    <form method="POST" action="{{ url('admin/api/commission') }}" enctype="" id="add_commission">
      @csrf
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <!-- <h5 class="mb-2"> {{ __('backend.commission_Details') }} </h5> -->
              <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="facility_id">{{ __('backend.Facility') }}</label>
                        <select name="facility_id" id="facility_id" onchange="facility_change()" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.Facility') }}" data-dropdown-css-class="select2-primary">
                         <option value="">--{{ __('backend.Select') }} {{ __('backend.Facility') }}--</option>
                         @foreach ($facility as $facility)
                          <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="col-md-6 court_data_show_div">
                    <div class="form-group">
                      <label class="control-label" for="court_id">{{ __('backend.court') }}</label>
                        <select name="court_id" id="court_id" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.court') }}" data-dropdown-css-class="select2-primary">
                         <option value="">--{{ __('backend.Select') }} {{ __('backend.court') }}--</option>
                        </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="amount"> {{__('backend.Percentage')}} </label>
                    <input type="text" name="amount" data-parsley-required="true" value="" id="amount" class="form-control" placeholder="{{__('backend.Percentage')}}" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="save_btn">
          <button type="submit" class="btn btn-info save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>
          <a href="{{ url('admin/commission') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
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
  $('.select2').select2();
  $('#add_commission').parsley();
  $(document).on('submit', "#add_commission", function(e) {
    e.preventDefault();
    var _this = $(this);
    $('#group_loader').fadeIn();
    var formData = new FormData(this);
    $.ajax({
      url: '{{ url("admin/api/commission") }}',
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
      success: function(res) {
        if (res.status === 1) {
          window.location.href = "{{url('admin/commission')}}";
          toastr.success(res.message);
          $('#add_commission')[0].reset();
          $('#add_commission').parsley().reset();
          //console.log('helo');
          //window.location.href =  url('admin/api/restaurant');
          ajax_datatable.draw();
        } else {
          toastr.error(res.message);

        }
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

  $("#file").change(function() {
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;

    var match = ["image/jpeg", "image/png", "image/jpg"];
    if (!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))) {
      $('#previewing').attr('src', 'images/no-image-available.png');
      toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
      return false;
    } else {
      //console.log(imageSize);
      if (imageSize < 1000000) {
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[0]);
      } else {
        toastr.error('Images Size Too large Please Select 1MB File!!');
        return false;
      }
    }
  });

  function imageIsLoaded(e) {
    //console.log(e);
    $("#file").css("color", "green");
    $('#previewing').attr('src', e.target.result);
  }

  function facility_change() {
    var facility_id = $('#facility_id').val();
    if (facility_id) {
        $.ajax({
           url:"{{url('admin/commission/show_court_data')}}/"+facility_id,
           dataType: 'html',
           success:function(result)
           {
            $('.court_data_show_div').html(result);
            $('.link-div').hide();
           }
        });

    } else {
      $("input[name='category_type']:checked").prop('checked', false);
      toastr.error('Please choose main category first');
      return false;
    }
  }
</script>

@endsection