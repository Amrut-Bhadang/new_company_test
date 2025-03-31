@extends('layouts.master')

@section('content')
<?php

use App\Models\Language;
use App\Models\CourtLang;

$language = Language::pluck('lang')->toArray();
$login_user = Auth::user(); 

?>
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <div class="d-flex align-items-center">
      <!-- <a href="{{ url('admin/courts') }}" class="btn btn-info btn-sm mr-3" ><i class="fa fa-arrow-left"></i> {{ __('backend.Back') }} </a> -->
      <h4 class="text-themecolor">{{ __('backend.Edit_Court') }}</h4>
    </div>
  </div>
  <!-- <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"> Edit Court </h4>
    </div> -->
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active"> {{ __('backend.Edit_Court') }} </li>
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
    <form method="PUT" action="{{ url('admin/api/courts/'.$users->id) }}" id="edit_role">
      <div class="row">
        @csrf
        <div class="col-md-12">
          <div class="card card-primary card-outline">
            <div class="card-body">
              <div class="container-fluid" style="margin-top:10px">
                <!-- <h5 class="mb-2"> {{ __('backend.Court_Details') }} </h5> -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="facility_id">{{ __('backend.Facility') }}</label>
                      <select name="facility_id" id="facility_id" onchange="facility_change()" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{ __('backend.Facility') }}" data-dropdown-css-class="select2-primary">
                        <option value="">--{{ __('backend.Select') }} {{ __('backend.Facility') }}--</option>
                        @foreach ($facility as $facility)
                        <option value="{{ $facility->id }}" {{ $facility->id== $users->facility_id?'selected':'' }}>{{ $facility->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 category_data_show_div">
                    <div class="form-group">
                      <label class="control-label" for="category_id">{{ __('backend.Category') }}*</label>
                      <select name="category_id" id="category_id" data-parsley-required="true" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{ __('backend.Category') }}" data-dropdown-css-class="select2-primary">
                        <option value="">--{{ __('backend.Select') }} {{ __('backend.Category') }}--</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  @if($language)
                  @foreach($language as $key => $lang)
                  <?php
                  if (isset($users)) {
                    $langData = CourtLang::where(['lang' => $lang, 'court_id' => $users->id])->first();
                  }
                  ?>
                  <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="name"> {{__('backend.Court_Name')}} ({{__('backend.'.$lang)}})*</label>
                      <input type="text" name="court_name[{{$lang}}]" data-parsley-required="true" value="{{$langData->court_name}}" id="court_name" class="form-control" placeholder=" {{__('backend.Court_Name')}}" />
                    </div>
                  </div>
                  @endforeach
                  @endif
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="category_id">{{__('backend.Minimum_Hours_Book')}} ( {{__('backend.Time_Slot')}} )*</label>
                      <select name="timeslot" id="timeslot" data-parsley-required="true" class="form-control select2" data-placeholder="{{__('backend.Minimum_Hours_Book')}} ( {{__('backend.Time_Slot')}} )" data-dropdown-css-class="select2-primary">
                        <option value="">--Select {{__('backend.Minimum_Hours_Book')}} ( {{__('backend.Time_Slot')}} )--</option>

                        <?php
                        for ($i = 5; $i <= 5; $i++) {

                          for ($j = 1; $j <= 24; $j++) {

                            if ($i * $j >= 15) {
                              $time = $i * $j;

                              if ($users->timeslot == $time) {
                                echo "<option value=" . $time . " selected>" . $time . " ". __('backend.Min') . "</option>";
                              } else {
                                echo "<option value=" . $time . ">" . $time . " ". __('backend.Min') . "</option>";
                              }
                            }
                          }
                        }
                        ?>
                        <!-- <option value="20">20 Min</option>
                         <option value="25">25 Min</option> -->
                      </select>
                    </div>
                  </div>
                  <!-- <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="minimum_hour_book">{{__('backend.Minimum_Hours_Book')}}*</label>
                        <input type="text" name="minimum_hour_book" value="{{$users->minimum_hour_book}}" id="minimum_hour_book" class="form-control" placeholder="{{__('backend.Minimum_Hours_Book')}}" autocomplete="off" data-parsley-required="true" />
                      </div>
                    </div> -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="hourly_price">{{__('backend.Hourly_Price')}} {{__('backend.In_AED')}}*</label>
                      <input type="text" name="hourly_price" value="{{$users->hourly_price}}" id="hourly_price" class="form-control" placeholder="{{__('backend.Hourly_Price')}} {{__('backend.In_AED')}}" autocomplete="off" data-parsley-required="true" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="is_featured">{{__('backend.Is_Featured')}}*</label>
                      <select name="is_featured" id="is_featured" data-parsley-required="true" class="form-control select2" data-placeholder="{{__('backend.Is_Featured')}}" data-dropdown-css-class="select2-primary">
                        <option value="">---Select---</option>
                        <option value="1" {{ $users->is_featured == 1 ?'selected':'' }}>{{__('backend.Yes')}}</option>
                        <option value="0" {{ $users->is_featured == 0 ?'selected':'' }}>{{__('backend.No')}}</option>
                      </select>
                    </div>
                  </div>
                @if($login_user->type == '0')
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="admin_commission">{{__('backend.Admin_commission')}}(%)*</label>
                      <input type="text" name="admin_commission" value="{{$admin_commission}}" id="admin_commission" class="form-control" placeholder="{{__('backend.Admin_commission')}}" autocomplete="off" data-parsley-required="true" />
                    </div>
                  </div>
                  <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="position">{{__('backend.Position')}}*</label>
                    <select name="position" id="position" class="form-control select2" data-placeholder="{{__('backend.Position')}}" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                      @for ($i = 1; $i <= $total_court; $i++)
                          <option value="{{ $i }}" {{ $users->position ==  $i ?'selected':'' }}>{{ $i }}</option>
                      @endfor
                    </select>
                  </div>
                </div>
                  @else
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="admin_commission">{{__('backend.Admin_commission')}}(%)*</label>
                      <input type="text" name="admin_commission1" value="{{$admin_commission}}" id="admin_commission" class="form-control" placeholder="{{__('backend.Admin_commission')}}" autocomplete="off" data-parsley-required="true" disabled/>
                    <input type="hidden" name="admin_commission" value="{{$admin_commission}}">
                    </div>
                  </div>
                  <input type="hidden" name="position" value="{{$users->position}}">
                 @endif
                 <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="court_size">{{__('backend.Court_Size')}}*</label>
                    <select name="court_size" id="court_size" class="form-control select2" data-parsley-required="true" data-placeholder="{{__('backend.Court_Size')}}" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                          <option value="1v1" {{$users->court_size == "1v1" ? 'selected':''}}>1v1</option>
                          <option value="2v2" {{$users->court_size == "2v2" ? 'selected':''}}>2v2</option>
                          <option value="3v3" {{$users->court_size == "3v3" ? 'selected':''}}>3v3</option>
                          <option value="4v4" {{$users->court_size == "4v4" ? 'selected':''}}>4v4</option>
                          <option value="5v5" {{$users->court_size == "5v5" ? 'selected':''}}>5v5</option>
                          <option value="6v6" {{$users->court_size == "6v6" ? 'selected':''}}>6v6</option>
                          <option value="7v7" {{$users->court_size == "7v7" ? 'selected':''}}>7v7</option>
                          <option value="8v8" {{$users->court_size == "8v8" ? 'selected':''}}>8v8</option>
                          <option value="9v9" {{$users->court_size == "9v9" ? 'selected':''}}>9v9</option>
                          <option value="10v10" {{$users->court_size == "10v10" ? 'selected':''}}>10v10</option>
                          <option value="11v11" {{$users->court_size == "11v11" ? 'selected':''}}>11v11</option>
                    </select>
                  </div>
                </div>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <h5> {{__('backend.Popular_Timing')}} </h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="position-relative form-group">
                    <label for="name" class="">{{__('backend.Day')}}</label>
                    <select name="popular_day" id="popular_day" data-parsley-required="true" class="form-control select2" data-placeholder="{{__('backend.Popular_Day')}}" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                      <option value="Sunday" {{ $users->popular_day == 'Sunday' ?'selected':'' }}>{{__('backend.Sunday')}}</option>
                      <option value="Monday" {{ $users->popular_day == 'Monday' ?'selected':'' }}>{{__('backend.Monday')}}</option>
                      <option value="Tuesday" {{ $users->popular_day == 'Tuesday' ?'selected':'' }}>{{__('backend.Tuesday')}}</option>
                      <option value="Wednesday" {{ $users->popular_day == 'Wednesday' ?'selected':'' }}>{{__('backend.Wednesday')}}</option>
                      <option value="Thursday" {{ $users->popular_day == 'Thursday' ?'selected':'' }}>{{__('backend.Thursday')}}</option>
                      <option value="Friday" {{ $users->popular_day == 'Friday' ?'selected':'' }}>{{__('backend.Friday')}}</option>
                      <option value="Saturday" {{ $users->popular_day == 'Saturday' ?'selected':'' }}>{{__('backend.Saturday')}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="position-relative form-group">
                    <label for="name" class="">{{__('backend.Start_Time')}}</label>
                    <div class="react-datepicker-wrapper">
                      <div class="react-datepickerinput-container">
                        <input type="text" class="datetimepicker form-control end_time" data-parsley-required="true" name="popular_start_time" autocomplete="off" value="{{$users->popular_start_time ?? ''}}">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row"></div>
            </div>
          </div>
        </div> -->
          <div class="card card-primary card-outline">
            <div class="card-body">
              <div class="container-fluid" style="margin-top:10px">
                <h5> {{__('backend.Timing')}} </h5>
                <div class="row">
                  <!-- <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="is_featured">Is Featured*</label>
                        <select class="form-control" data-parsley-required="true" name="is_featured">
                          <option value="">---Select---</option>
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div>
                    </div> -->

                  <div class="col-md-6">
                    <div class="position-relative form-group">
                      <label for="name" class="">{{__('backend.Open_Time')}}</label>
                      <div class="react-datepicker-wrapper">
                        <div class="react-datepickerinput-container">
                          <input type="text" class="datetimepicker form-control start_time" name="start_time" value="{{$users->start_time}}" autocomplete="off" value="">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="position-relative form-group">
                      <label for="name" class="">{{__('backend.Close_Time')}}</label>
                      <div class="react-datepicker-wrapper">
                        <div class="react-datepickerinput-container">
                          <input type="text" class="datetimepicker form-control end_time" name="end_time" value="{{$users->end_time}}" autocomplete="off" value="">
                        </div>
                      </div>
                    </div>
                  </div>


                </div>
                <div class="row"></div>
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
          <div class="card card-primary card-outline">
            <div class="card-body">
              <div class="container-fluid" style="margin-top:10px">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label" for="address">{{__('backend.Address')}}*</label>

                      <input type="text" placeholder="Address" name="address" class="form-control" id="address" value="{{$users->address}}" autocomplete="off" data-parsley-required="true">
                      <input type="hidden" class="latitude" id='latitude' name="latitude" value="{{$users->latitude}}" />
                      <input type="hidden" class="longitude" id='longitude' name="longitude" value="{{$users->longitude}}" />

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
          <div class="save_btn">
            <button type="submit" class="btn btn-info save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>
            <a href="{{ url('admin/courts') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Back')}}</a>
          </div>
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
    facility_change();
    initialize();
    autoload({{ $users -> latitude}}, {{$users -> longitude}});
    $('.select2').select2();
    $('#edit_role').parsley();

    $(document).on('submit', "#edit_role", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = new FormData(this);
      formData.append('_method', 'put');

      $('#group_loader').fadeIn();
      // var values = $('#edit_role').serialize();
      $.ajax({
        url: "{{ url('admin/api/courts/'.$users->id) }}",
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
            window.location.href = "{{url('admin/courts')}}";
            toastr.success(result.message)
          } else {
            toastr.error(result.message)
            $('.save').prop('disabled', false);
            $('.formloader').css("display", "none");
          }
          $('#edit_role').parsley().reset();
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

  function facility_change() {
    var facility_id = $('#facility_id').val();
    var category_id = "{{$users->category_id}}";

    if (facility_id) {
      $.ajax({
        url: "{{url('admin/courts/show_court_category_data')}}/" + facility_id + '/' + category_id,
        dataType: 'html',
        success: function(result) {
          $('.category_data_show_div').html(result);
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