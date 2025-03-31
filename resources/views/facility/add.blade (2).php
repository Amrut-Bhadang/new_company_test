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
      <!-- <a href="{{ url('admin/facilities') }}" class="btn btn-info btn-sm mr-3"><i class="fa fa-arrow-left"></i> {{__('backend.Back')}} </a> -->
      <h4 class="text-themecolor">{{ __('backend.Add_facility') }}</h4>
    </div>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('backend.Add_facility') }}</li>
      </ol>

    </div>
  </div>
</div>
<!-- <div class="row">
  <div class="col-md-6" style="margin-bottom: 10px;">
     <a href="{{ url('admin/facility') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
  </div>
</div> -->


<div class="row">
  <div class="container-fluid">
    <form method="POST" action="{{ url('admin/api/facilities') }}" enctype="" id="add_facility">
      @csrf
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <!-- <h5 class="mb-2"> {{ __('backend.facility_Details') }} </h5> -->
              @php $login_user = Auth::user(); @endphp

              @if($login_user->type == '0')
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="facility_owner_id">{{ __('backend.Facility_owner') }}</label>
                    <select name="facility_owner_id" id="facility_owner_id" data-parsley-required="true" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{ __('backend.Facility_owner') }}" data-dropdown-css-class="select2-primary">
                      <option value="">--{{ __('backend.Select') }} {{ __('backend.Facility_owner') }}--</option>
                      @foreach ($facility_owner as $facility_owner)
                      <option value="{{ $facility_owner->id }}">{{ $facility_owner->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              @else
              <input type="hidden" name="facility_owner_id" value="{{$login_user->id}}">
              @endif
              <div class="row">
                @if($language)
                @foreach($language as $key => $lang)
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="name"> {{__('backend.facility_Name')}} ({{__('backend.'.$lang)}})*</label>
                    <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="" id="facility_name" class="form-control" placeholder=" {{__('backend.facility_Name')}}" />
                  </div>
                </div>
                @endforeach
                @endif
              </div>
              <div class="row">

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="name"> {{__('backend.Amenity')}} </label>
                    <select name="amenity_id[]" id="amenity_id" class="form-control select2" multiple="multiple" data-placeholder="{{ __('backend.Select_Amenity') }}" data-dropdown-css-class="select2-primary">
                      <option value="">--{{ __('backend.Select_Amenity') }}--</option>
                      @foreach ($amenities as $amenity)
                      <option value="{{ $amenity->id }}">{{ $amenity->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="category_id"> {{__('backend.Category')}} </label>
                    <select name="category_id[]" id="category_id" class="form-control select2" multiple="multiple" data-placeholder="{{ __('backend.Select_Category') }}" data-dropdown-css-class="select2-primary">
                      <option value="">--{{ __('backend.Select_Category') }}--</option>
                      @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                @if($login_user->type == '0')
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="position">{{__('backend.Position')}}*</label>
                    <select name="position" id="position" class="form-control select2" data-placeholder="{{__('backend.Position')}}" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                      @for ($i = 1; $i <= $total_facility; $i++)
                          <option value="{{ $i }}">{{ $i }}</option>
                      @endfor
                    </select>
                  </div>
                </div>
                @endif
                @if($login_user->type == '0')
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="position">{{__('backend.Commission')}}*</label>
                    <input type="text" name="commission" data-parsley-required="true" value="0" id="commission" class="form-control" placeholder=" {{__('backend.Commission')}}" autocomplete="off" />
                  </div>
                </div>
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
                      <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
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
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="address">{{__('backend.Address')}}*</label>
                    <input type="text" placeholder="{{__('backend.Address')}}" name="address" class="form-control" id="address" autocomplete="off" data-parsley-required="true">
                    <input type="hidden" class="latitude" id='latitude' name="latitude" />
                    <input type="hidden" class="longitude" id='longitude' name="longitude" />
                  </div>
                </div>
                <div class="col-md-12">
                  <style>
                    #map_canvas {
                      width: 100%;
                      height: 200px;
                    }

                    /* Optional: Makes the sample page fill the window. */
                    html,
                    body {
                      height: 100%;
                      margin: 0;
                      padding: 0;
                    }
                  </style>
                  <div class="form-group">
                    <div id="map_canvas"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <h5 class=""> {{__('backend.Rules')}} </h5>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="points">{{__('backend.Rules')}}*</label>
                    <a id='addButton' class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a>
                    <!-- <a id='removeButton' class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a> -->
                    <div id='TextBoxesGroup'>
                      <div id="TextBoxDiv1">
                        <div class="row">
                          <div class="col-md-5">
                            <input type='text' id='rule1' name="rules[0][en]" class="form-control" data-parsley-required="true" placeholder="{{__('backend.Rule_Name_English')}}">
                          </div>
                          <div class="col-md-5">
                            <input type='text' id='rule1' name="rules[0][ar]" class="form-control" data-parsley-required="true" placeholder="{{__('backend.Rule_Name_Arabic')}}">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="save_btn">
          <button type="submit" class="btn btn-info save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>
          <a href="{{ url('admin/facilities') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
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
  $(document).ready(function() {
    var counter = 2;
    $("#addButton").click(function() {
      /*if(counter>10){
        toastr.error("Only 10 textboxes allow");
        return false;
      }*/
      var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
      newTextBoxDiv.after().html('<br/> <div class="row appendAttrDiv_' + counter + '"><div class="col-md-5"><input type="text" id="rule' + counter + '" name="rules[' + counter + '][en]" class="form-control" data-parsley-required="true" placeholder="{{__('backend.Rule_Name_English')}}"></div> <div class = "col-md-5" ><input type = "text" id = "rule' + counter + '" name = "rules[' + counter + '][ar]" class = "form-control" data - parsley - required = "true" placeholder = "{{__('backend.Rule_Name_Arabic')}}" ></div> <a onclick="removeAttributeField(' + counter + ')" class="btn btn-danger btn-xs" style="color:white;"><i class="fa fa-trash"></i></a></div>');
      newTextBoxDiv.appendTo("#TextBoxesGroup");
      counter++;
    });
    $("#removeButton").click(function() {
      if (counter == 2) {
        toastr.error("one textbox is Required");
        return false;
      }
      counter--;
      $("#TextBoxDiv" + counter).remove();
    });
  });

  function removeAttributeField(id) {
    $('.appendAttrDiv_' + id).remove();
  }
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
  $('#add_facility').parsley();
  $(document).on('submit', "#add_facility", function(e) {
    e.preventDefault();
    var _this = $(this);
    $('#group_loader').fadeIn();
    var formData = new FormData(this);
    $.ajax({
      url: '{{ url("admin/api/facilities") }}',
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
          window.location.href = "{{url('admin/facilities')}}";
          toastr.success(res.message);
          $('#add_facility')[0].reset();
          $('#add_facility').parsley().reset();
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