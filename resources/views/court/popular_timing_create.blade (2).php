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
        <a href="{{ url('admin/courts') }}" class="btn btn-info btn-sm mr-3" ><i class="fa fa-arrow-left"></i> {{__('backend.Back')}} </a>
        <h4 class="text-themecolor">{{ __('backend.Add_Popular_Time') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.Add_Popular_Time') }}</li>
            </ol>

        </div>
    </div>
</div>
<!-- <div class="row">
  <div class="col-md-6" style="margin-bottom: 10px;">
     <a href="{{ url('admin/courts') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
  </div>
</div> -->


<div class="row">
  <div class="container-fluid">
    <form method="POST" action="{{ url('admin/courts/popular_timing/store') }}" enctype="" id="add_booking_slot">
      @csrf
      <input type="hidden" name="court_id" id="court_id" value="{{$id}}">
      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="container-fluid" style="margin-top:10px">
              <h5> {{__('backend.Popular_Timing')}} </h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="position-relative form-group">
                    <label for="name" class="">{{__('backend.Day')}}</label>
                    <select name="day" id="day" data-parsley-required="true" class="form-control select2" data-placeholder="{{__('backend.Popular_Day')}}" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                      <option value="Sunday">{{__('backend.Sunday')}}</option>
                      <option value="Monday">{{__('backend.Monday')}}</option>
                      <option value="Tuesday">{{__('backend.Tuesday')}}</option>
                      <option value="Wednesday">{{__('backend.Wednesday')}}</option>
                      <option value="Thursday">{{__('backend.Thursday')}}</option>
                      <option value="Friday">{{__('backend.Friday')}}</option>
                      <option value="Saturday">{{__('backend.Saturday')}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="position-relative form-group">
                    <label for="name" class="">{{__('backend.Start_Time')}}</label>
                    <select name="time" id="time" data-parsley-required="true" class="form-control select2" data-placeholder="{{__('backend.Start_Time')}}" data-dropdown-css-class="select2-primary">
                      <option value="">---Select---</option>
                      <?php foreach ($timeslot as $key => $value) { ?>
                      <option value="{{$value['start_time']}}">{{$value['start_time'].' -To- '. $value['end_time']}}</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row"></div>
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
  $(function () {
      $('.datetimepicker').datetimepicker({
          // Formats
          // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
          format: 'DD-MM-YYYY',
          minDate:new Date(),

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
      }).on('dp.change', function(e){
        // $('.slot_select').prop('checked', false).removeAttr("disabled");
        $('.date-label-check').removeClass('booked-cls');
        $('.date-label-check').removeClass('booked-cls-danger');
        $('.slot_select').prop('checked', false);
        var court_id = $('#court_id').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        if (court_id && start_date)
        $.ajax({
           url:'{{url("admin/courts/check_book_slot")}}/'+court_id+'/'+start_date,
           dataType: 'json',
           success:function(result)
           {

            if (result.status === 1) {
              $.each(result.data, function( index, value ) {
                console.log('#slots_'+value);

                if (index > 2) {
                  $('.slots_'+value).parent().addClass('booked-cls');

                } else {
                  $('.slots_'+value).parent().addClass('booked-cls-danger');
                }
                $('.slots_'+value).prop("checked", true);
              });

            }
           }
        });
      });

      $('.enddatetimepicker').datetimepicker({
          // Formats
          // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
          format: 'DD-MM-YYYY',
          minDate:new Date(),

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
  $('#add_booking_slot').parsley();
    $(document).on('submit', "#add_booking_slot",function(e){
      e.preventDefault();
      var _this=$(this);
        $('#group_loader').fadeIn();
        var formData = new  FormData(this);
        $.ajax({
        url:'{{ url("admin/courts/popular_timing/store") }}',
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
                window.location.href = "{{url('admin/courts/popular_timing/'.$id)}}";
                toastr.success(res.message);
                $('#add_booking_slot')[0].reset();
                $('#add_booking_slot').parsley().reset();
              //console.log('helo');
              // window.location.href =  url('admin/courts');
                ajax_datatable.draw();
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
</script>

@endsection