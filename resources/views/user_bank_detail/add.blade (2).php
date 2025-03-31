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
      <!-- <a href="{{ url('admin/user_bank_detail') }}" class="btn btn-info btn-sm mr-3"><i class="fa fa-arrow-left"></i> {{__('backend.Back')}} </a> -->
      <h4 class="text-themecolor">{{ __('backend.Add_Bank_Detail') }}</h4>
    </div>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('backend.Add_Bank_Detail') }}</li>
      </ol>

    </div>
  </div>
</div>
<!-- <div class="row">
  <div class="col-md-6" style="margin-bottom: 10px;">
     <a href="{{ url('admin/user_bank_detail') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
  </div>
</div> -->
<div class="row">
  <div class="container-fluid">
    <form method="POST" action="{{ url('admin/api/user_bank_detail') }}" enctype="" id="add_bank_detail">
      @csrf
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <!-- <h5 class="mb-2"> {{ __('backend.Facility_owner_Details') }} </h5> -->
              @php $login_user = Auth::user(); @endphp

                @if($login_user->type == '0')
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label" for="user_id">{{ __('backend.Facility_owner') }}</label>
                      <select name="user_id" id="user_id" data-parsley-required="true" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{ __('backend.Facility_owner') }}" data-dropdown-css-class="select2-primary">
                        <option value="">--{{ __('backend.Select') }} {{ __('backend.Facility_owner') }}--</option>
                        @foreach ($facility_owner as $facility_owner)
                        <option value="{{ $facility_owner->id }}">{{ $facility_owner->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                @else
                <input type="hidden" name="user_id" value="{{$login_user->id}}">
                @endif
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="bank_name"> {{__('backend.Bank_Name')}} </label>
                    <input type="text" name="bank_name" data-parsley-required="true" data-parsley-minlength="3" data-parsley-pattern="^[A-Za-z ]+$" data-parsley-pattern-message="{{__('backend.validation_only_alpha_space')}}" value="" id="bank_name" class="form-control" placeholder="{{__('backend.Bank_Name')}}" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="bank_code">{{__('backend.Bank_Code')}}*</label>
                    <input type="text" name="bank_code" value="" id="bank_code" class="form-control" placeholder="{{__('backend.Bank_Code')}}" autocomplete="off" data-parsley-required="true" />
                  </div>
                </div>
              </div>   
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="bank_address">{{__('backend.Bank_Address')}}*</label>
                    <input type="text" name="bank_address" value="" id="bank_address" class="form-control" placeholder="{{__('backend.Bank_Address')}}" autocomplete="off" data-parsley-required="true" />
                  </div>
                </div>
              </div>  
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="account_number"> {{__('backend.Account_Number')}} </label>
                    <input type="text" name="account_number" data-parsley-required="true" data-parsley-type="digits" data-parsley-minlength="7" data-parsley-maxlength="19" value="" id="account_number" class="form-control" placeholder="{{__('backend.Account_Number')}}" />
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="account_type">{{__('backend.Account_Type')}}*</label>
                    <input type="text" name="account_type" value="" id="account_type" class="form-control" placeholder="{{__('backend.Account_Type')}}" autocomplete="off" data-parsley-required="true" />
                  </div>
                </div> -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="account_type">{{__('backend.Account_Type')}}*</label>
                    <select name="account_type" id="account_type" data-parsley-required="true" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{__('backend.Account_Type')}}*" data-dropdown-css-class="select2-primary">
                      <option value="">--{{ __('backend.Select') }} {{__('backend.Account_Type')}}*--</option>
                      <option value="Current">{{__('backend.Current')}}</option>
                      <option value="Saving">{{__('backend.Saving')}}</option>
                    </select>
                  </div>
                </div>
              </div>  
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="account_holder_name"> {{__('backend.Account_Holder_Name')}} </label>
                    <input type="text" name="account_holder_name" data-parsley-required="true" data-parsley-minlength="3" value="" id="account_holder_name" class="form-control" placeholder="{{__('backend.Account_Holder_Name')}}" />
                  </div>
                </div>
              </div>        
            </div>
          </div>
        </div>
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <h5 class=""> {{__('backend.Passbook_Image')}} </h5>
              <div class="row">
                <div class="col-md-12 mb-6">
                  <div class="form-group">
                    <div class="input-group">
                      <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                      <div class="form-control" onclick="document.getElementById('file').click()">
                          <label for="files" >{{__('backend.Select_Image')}}</label>
                          <input type="file" id="file" name="image" data-parsley-required="true" style="visibility:hidden;" class="form-control">
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
          <a href="{{ url('admin/user_bank_detail') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
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
  $('#add_bank_detail').parsley();
  $(document).on('submit', "#add_bank_detail", function(e) {
    e.preventDefault();
    var _this = $(this);
    $('#group_loader').fadeIn();
    var formData = new FormData(this);
    $.ajax({
      url: '{{ url("admin/api/user_bank_detail") }}',
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
          window.location.href = "{{url('admin/user_bank_detail')}}";
          toastr.success(res.message);
          $('#add_bank_detail')[0].reset();
          $('#add_bank_detail').parsley().reset();
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

    var file = $('#file')[0].files[0].name;
		$(this).prev('label').text(file);

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
</script>

@endsection