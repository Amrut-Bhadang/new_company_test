@extends('layouts.master')
<?php

use App\Models\Language;

$language = Language::pluck('lang')->toArray();

?>
@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <h4 class="text-themecolor">
      @if($order_status == 'Complete')
      {{ __('Complete Orders Manager') }}
      @elseif($order_status == 'Cancel')
      {{ __('Cancel Orders Manager') }}
      @elseif($order_status == 'Accepted')
      {{ __('Accepted Orders Manager') }}
      @elseif($order_status == 'Prepare')
      {{ __('Ready To Deliver Orders Manager') }}

      @elseif($order_status == 'Cancel')
      {{ __('Cancel Orders Manager') }}
      @else
      {{ __('backend.Cash_Settlement_Manager') }}
      @endif
      <a href="{{url()->current()}}" class="btn btn-info"> <i class="fas fa-redo-alt"></i> {{ __('backend.Refresh') }}</a>
    </h4>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active">
          @if($order_status == 'Complete')
          {{ __('Complete Orders Manager') }}
          @elseif($order_status == 'Accepted')
          {{ __('Accepted Orders Manager') }}
          @elseif($order_status == 'Prepare')
          {{ __('Ready To Deliver Orders Manager') }}
          @elseif($order_status == 'Cancel')
          {{ __('Cancel Orders Manager') }}
          @else
          {{ __('backend.Cash_Settlement_Manager') }}
          @endif
        </li>
      </ol>
    </div>
  </div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content user_page">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-primary card-outline">
        <div class="card-body">
          <form method="POST" id="search-form" class="form-inline-sec" role="form">
            <div class="row input-daterange form_s">
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="form-group">
                  <input type="text" name="from_date" id="from_date" class="form-control" placeholder="{{ __('backend.From_Date') }}" readonly />
                </div>
              </div>
              <div class="col-xl-2 col-lg-3  col-md-4 col-sm-6">
                <div class="form-group">
                  <input type="text" name="to_date" id="to_date" class="form-control" placeholder="{{ __('backend.To_Date') }}" readonly />
                </div>
              </div>
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="form-group">
                  <select name="facility_id" id="facility_id" onchange="facility_change()" class="form-control select2" multiple="multiple" data-placeholder="{{ __('backend.Select_facility') }}" data-dropdown-css-class="select2-primary">
                    <option value="">--{{ __('backend.Select_facility') }}--</option>
                    @foreach ($facilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 court_data_show_div">
                <select name="court_id" id="court_id" style="width:200px" class="form-control select2" multiple="multiple" data-placeholder="{{ __('backend.Select_Court') }}" data-dropdown-css-class="select2-primary">
                  <option value="">--{{ __('backend.Select_Court') }}--</option>
                </select>
              </div>
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="form-group">
                  <select name="order_status" id="order_status" class="form-control select2" data-placeholder="{{ __('backend.Select_Order_Status') }}" data-dropdown-css-class="select2-primary">
                    <option value="">--{{ __('backend.Select_Order_Status') }}--</option>
                    <option value="Pending">{{__('backend.Pending')}}</option>
                    <option value="Accepted">{{__('backend.Accepted')}}</option>
                    <option value="Cancelled">{{__('backend.Cancelled')}}</option>
                    <option value="Completed">{{__('backend.Completed')}}</option>
                  </select>
                </div>
              </div>
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="form-group">
                  <select name="payment_type" id="payment_type" class="form-control select2" data-placeholder="{{ __('backend.Select_Mode_of_Payment') }}" data-dropdown-css-class="select2-primary">
                    <option value="">--{{ __('backend.Select_Mode_of_Payment') }}--</option>
                    <option value="online">{{__('backend.online')}}</option>
                    <option value="cash">{{__('backend.cash')}}</option>
                  </select>
                </div>
              </div>
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="form-group">
                  <select name="player_id" id="player_id" class="form-control select2" multiple="multiple" data-placeholder="{{ __('backend.Select_player') }}" data-dropdown-css-class="select2-primary">
                    <option value="">--{{ __('backend.Select_player') }}--</option>
                    @foreach ($players as $player)
                    <option value="{{ $player->id }}">{{ $player->name ? $player->name:$player->country_code.'-'.$player->mobile}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <button type="submit" name="filter" id="filter" class="btn btn-info" style="margin-right:5px">{{ __('backend.Filter') }}</button><a href="{{url()->current()}}" class="btn btn-dark"> <i class="fas fa-redo-alt"></i> {{ __('backend.Reset') }}</a>
                <a href="javascript:void(0);" onclick="exportOrderData()" class="btn btn-info" title="{{ __('backend.Export') }}"><i class="fa fa-download"></i> {{ __('backend.Export') }}</a>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table id="staff_listing" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th> {{ __('backend.Sr_no') }} </th>
                  <th> {{ __('backend.User_Name') }} </th>
                  <th> {{ __('backend.Court_Name') }} </th>
                  <th> {{ __('backend.Facility_Name') }} </th>
                  <th> {{ __('backend.booking_type') }} </th>
                  <th> {{ __('backend.Booking_Date') }} </th>
                  <th> {{ __('backend.Time_Slot') }} </th>
                  <th> {{ __('backend.Amount') }} </th>
                  <th> {{ __('backend.Paid_Amount') }} </th>
                  <th> {{ __('backend.Admin_Commission') }} </th>
                  <th> {{ __('backend.Mode_of_Payment') }} </th>
                  <th>{{ __('backend.Order_Status') }}</th>
                  <th>{{ __('backend.Created_At') }}</th>
                  <th>{{ __('backend.Action') }}</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
              <tfoot>
                <tr>
                  <th colspan="7">{{__('backend.Total')}}</th>
                  <th><span class="total_amount">{{$total_amount??''}}</span>  {{__('backend.AED')}}</th>
                  <th><span class="paid_amount">{{$paid_amount??''}}</span> {{__('backend.AED')}}</th>
                  <th><span class="admin_commission_amount">{{$admin_commission_amount??''}}</span> {{__('backend.AED')}}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

</div>
<!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ url('admin/api/orders') }}" id="add_staff">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add New Court</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="facility_id">Facility</label>
                <select name="facility_id" id="facility_id1" class="form-control select2" data-placeholder="Select Facility" data-dropdown-css-class="select2-primary">
                  <option value="">--Select Facility--</option>
                  <option value="Facility 1">Facility 1</option>
                  <option value="Facility 2">Facility 2</option>
                  <option value="Facility 3">Facility 3</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="category_id">Category*</label>
                <select name="category_id" id="category_id" data-parsley-required="true" class="form-control select2" data-placeholder="Select Category" data-dropdown-css-class="select2-primary">
                  <option value="">--Select Category--</option>
                  <option value="Category 1">Category 1</option>
                  <option value="Category 2">Category 2</option>
                  <option value="Category 3">Category 3</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            @if($language)
            @foreach($language as $key => $lang)
            <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"> {{__('Court Name')}} ({{__('backend.'.$lang)}})*</label>
                <input type="text" name="court_name[{{$lang}}]" data-parsley-required="true" value="" id="court_name" class="form-control" placeholder=" Court Name" />
              </div>
            </div>
            @endforeach
            @endif
          </div>
          <div class="row">
            <!-- <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="court_name">Court Name*</label>
                  <input type="text" name="court_name" value="" id="court_name" class="form-control" placeholder="Court Name" data-parsley-pattern="^[a-zA-Z]+$" data-parsley-pattern-message="Last name allow only character" data-parsley-required="true"  />
                </div>
              </div> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="address">Address*</label>
                <input type="text" placeholder="Address" name="address" class="form-control" id="address" autocomplete="off" data-parsley-required="true">
                <input type="hidden" class="latitude" id='latitude' name="latitude" />
                <input type="hidden" class="longitude" id='longitude' name="longitude" />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="minimum_hour_book">Minimum Hours Book*</label>
                <input type="text" name="minimum_hour_book" value="" id="minimum_hour_book" class="form-control" placeholder="Minimum Hours Book" autocomplete="off" data-parsley-required="true" />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="hourly_price">Hourly Price*</label>
                <input type="text" name="hourly_price" value="" id="hourly_price" class="form-control" placeholder="Hourly Price" autocomplete="off" data-parsley-required="true" />
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <label for="image">Image</label>
              <div class="form-group">
                <div class="input-group">
                  <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                  <input type="file" id="file" name="image" class="form-control">
                </div>
                <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="editModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Court</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="edit_staff_response"></div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="notificationModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send Notification</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ url('admin/orders/send_notification') }}" id="sendUserNotification">
          @csrf
          <input type="hidden" name="user_id" id="notificationUserId" value="">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="title">Title*</label>
                <input type="text" name="title" value="" id="title" class="form-control" placeholder="Title" data-parsley-required="true" />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="message">Message*</label>
                <textarea id="message" name="message" data-parsley-required="true" class="form-control" placeholder="Message"></textarea>
              </div>
            </div>
          </div>
        </form>
        <hr style="margin: 1em -15px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary float-right save sendUserNotification"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Send Notification</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('backend.View_booking') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="view_response"></div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- /Modals -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
</script>

<script>
  var ajax_datatable;
  $(document).ready(function() {
    $('.input-daterange').datepicker({
      todayBtn: 'linked',
      format: 'yyyy-mm-dd',
      autoclose: true
    });
  });


  var ajax_datatable;
  $(document).ready(function() {
    $('#add_staff').parsley();
    $('.select2').select2();
    ajax_datatable = $('#staff_listing').DataTable({
      processing: true,
      serverSide: true,
      language: {
        "sSearch":"{{__('backend.Search')}}",
        "sentries":"{{__('backend.entries')}}",
        "lengthMenu": "{{__('backend.Show')}} _MENU_ {{__('backend.entries')}}",
        "info": "{{__('backend.Showing')}} _START_ {{__('backend.to')}} _END_ {{__('backend.of')}} _TOTAL_ {{__('backend.entries')}}",
        "oPaginate": {           
            "sNext":    "{{__('backend.Next')}}",
            "sPrevious": "{{__('backend.Previous')}}",           
        },
      },
      ajax: {
        url: '{{ url("admin/api/cash-settlement") }}',
        data: function(d) {
          d.court_id = $('select[name=court_id]').val();
          d.facility_id = $('select[name=facility_id]').val();
          d.player_id = $('select[name=player_id]').val();
          d.order_status = $('select[name=order_status]').val();
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
          d.payment_type = $('select[name=payment_type]').val();
          //alert(d.country);
        }
      },
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        {
          data: 'user_name',
          name: 'users.name'

        },
        {
          data: 'court_name',
          name: 'courts_lang.court_name'
        },
        {
          data: 'facility_name',
          name: 'facilities_lang.name'
        },
        {
          data: 'booking_type',
          name: 'booking_type'
        },
        {
          data: 'booking_date',
          name: 'booking_date'
        },

        {
          data: 'booking_time_slot',
          name: 'booking_time_slot',
          orderable: false,
          searchable: false
        },
        {
          data: 'total_amount',
          name: 'total_amount'
        },
        {
          data: 'paid_amount',
          name: 'paid_amount',
          orderable: false,
          searchable: false
        },
        {
          data: 'admin_commission_amount',
          name: 'admin_commission_amount'
        },
        {
          data: 'payment_type',
          name: 'payment_type'
        },
        {
          data: 'order_status',
          name: 'order_status'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },

        {
          data: 'id',
          name: 'id',
          orderable: false,
          searchable: false
        }
      ],
      order: [
        [12, 'desc']
      ],
      rowCallback: function(row, data, iDisplayIndex) {
        // console.log(data, '-----------------------')

        var links = '';
        var user_name = '';
        var status = '';
        var total_order = '';
        var total_amount = '';
        var paid_amount = '';
        var admin_commission_amount = '';
        var booking_type = '';
        var order_status = '';
        var payment_type = '';

        links += `<div class="btn-group" role="group" >`;
        @can('Order-edit')
        // links += `<a href="{{ url('admin/orders/edit') }}/${data.id}" data-staff_id="${data.id}" title="{{ __('backend.Edit_Details') }}" class="btn btn-info btn-xs" ><span class="fa fa-edit"></span></a>`;
        @endcan
        // @can('Order-delete')
        // links += `<a href="#" data-staff_id="${data.id}" title="{{ __('backend.Delete') }}" class="btn btn-danger btn-xs delete_staff" ><span class="fa fa-trash"></span></a>`;
        // @endcan

        @can('Order-edit')
        links += `<a href="#" data-staff_id="${data.id}" title="{{ __('backend.View_Details') }}" class="btn btn-primary btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
        // links += `<a href="{{ url('admin/orders/manage_timing') }}/${data.id}" data-staff_id="${data.id}" title="{{ __('backend.timing') }}" class="btn btn-warning btn-xs" ><span class="fa fa-clock"></span></a>`;
        @endcan
        /*@can('Order-edit')
        links += `<a href="{{ url('admin/orders/view_address').'/' }}${data.id}" data-staff_id="${data.id}" title="View Address" class="btn btn-danger btn-xs view_address" ><span class="fa fa-home"></span></a>`;
        @endcan
        @can('Order-edit')
        links += `<a href="{{ url('admin/orders/transaction').'/' }}${data.id}" data-staff_id="${data.id}" title="View Transaction" class="btn btn-dark btn-xs view_transaction" ><span class="fa fa-credit-card"></span></a>`;
        @endcan
        @can('Order-edit')
        // links += `<a href="{{ url('admin/orders/giftstransaction').'/' }}${data.id}" data-staff_id="${data.id}" title="View Gifts Transaction" class="btn btn-secondary btn-xs view_transaction" ><span class="fa fa-gifts"></span></a>`;
        links += `<a href="javascript:void(0);" data-staff_id="${data.id}" title="Send Notification" class="btn btn-secondary btn-xs send_notification" ><span class="fa fa-bell"></span></a>`;*/
        @endcan
        links += `</div>`;
        if (data.status === 1) {
          status += `<a href="#" data-staff_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{ __('backend.Active') }}</span></a>`;
        } else {
          status += `<a href="#" data-staff_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{ __('backend.Deactive') }}</span></a>`;
        }
        if (data.user_name == '') {
          user_name += `No Name`;
        } else {
          user_name += `<span> ${data.user_name} </span>`;
        }
        if (data.total_amount == '') {
          total_amount += `No Amount`;
        } else {
          total_amount += `<span> ${data.total_amount} AED</span>`;
        }
        if (data.paid_amount == '') {
          paid_amount += `No Amount`;
        } else {
          paid_amount += `<span> ${data.paid_amount} AED</span>`;
        }
        if (data.payment_type == '') {
          payment_type += ` NO Payment Type`;
        } 
         else if (data.payment_type == 'online'){
          payment_type += `<span>{{__('backend.online')}}</span>`;
        }
        else if (data.payment_type == 'cash'){
          payment_type += `<span>{{__('backend.cash')}}</span>`;
        }
        else {
          payment_type += `<span>${data.payment_type}</span>`;
        }
        if (data.admin_commission_amount == '' || data.admin_commission_amount == null) {
          admin_commission_amount += `No Amount`;
        } else {
          admin_commission_amount += `<span> ${data.admin_commission_amount} AED</span>`;
        }
        if (data.booking_type == '') {
          booking_type += `No Booking Type`;
        } else if(data.booking_type == 'challenge') {
          booking_type += `<span class="text-capitalize"> {{__('backend.Challenge')}}</span>`;
        }
        else if(data.booking_type == 'normal') {
          booking_type += `<span class="text-capitalize"> {{__('backend.Normal')}}</span>`;
        }
        else  {
          booking_type += `<span class="text-capitalize"> ${data.booking_type}</span>`;
        }
        if (data.order_status == '') {
          order_status += `No Order Status`;
        } else if (data.order_status == 'Pending') {
          order_status += ` <div class="dropdown dropdown${data.id}">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-id="${data.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('backend.Pending') }}
                    </button>
                    <div class="dropdown-menu dropdownmenu${data.id}" aria-labelledby="dropdownMenuButton">
                      <a data-id="${data.id}" title="Prepare Order" onclick="change_status(this)" data-order_status="Accepted" class="dropdown-item  status_btn" href="javascript:void(0);">{{ __('backend.Accepted') }}</a>
                      <a data-id="${data.id}" title="Cancel Order" onclick="change_status(this)" data-order_status="Cancelled" class="dropdown-item" href="javascript:void(0);">{{ __('backend.Cancelled') }}</a>
                    </div>
                  </div>`;
        } 
        else if (data.order_status == 'Accepted') {
          order_status += `<span class="text-capitalize"> {{__('backend.Accepted')}}</span>`;
        }
        else if (data.order_status == 'Completed') {
          order_status += `<span class="text-capitalize"> {{__('backend.Completed')}}</span>`;
        }
        else if (data.order_status == 'Cancelled') {
          order_status += `<span class="text-capitalize"> {{__('backend.Cancelled')}}</span>`;
        }
        else {
          order_status += `<span class="text-capitalize"> ${data.order_status}</span>`;
        }
        links += `</div>`;
        total_order += `<a href="{{ url('admin/orders/view_orders').'/' }}${data.id}" data-staff_id="${data.id}" class="view_order"><span class='label label-rounded label-primary'>${data.total_order}  Orders</span></a>`;

        $('td:eq(1)', row).html(user_name);
        $('td:eq(4)', row).html(booking_type);
        $('td:eq(7)', row).html(total_amount);
        $('td:eq(8)', row).html(paid_amount);
        $('td:eq(9)', row).html(admin_commission_amount);
        $('td:eq(13)', row).html(links);
        $('td:eq(11)', row).html(order_status);
        $('td:eq(10)', row).html(payment_type);
      },
    });
  });

  function change_status($this) {

    id = $($this).attr("data-id");
    status = $($this).attr("data-order_status");
    if (status == 'Accepted') {
      var response = confirm("{{ __('backend.confirm_box_accepted_booking') }}");
    } else {
      var response = confirm("{{ __('backend.confirm_box_cancelled_booking') }}");
    }
    if (response) {

      $.ajax({
        type: 'post',
        data: {
          _method: 'get',
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        url: "{!! url('admin/orders/changeOrderStatus' )!!}" + "/" + id + '/' + status,
        success: function(res) {
          if (res.status === 1) {
            window.location.reload();
            toastr.success(res.message);
            ajax_datatable.draw();
          } else {
            toastr.error(res.message);
          }
        },
        error: function(jqXHR, textStatus, textStatus) {
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
  };

  $('#search-form').on('submit', function(e) {
    ajax_datatable.draw();
    e.preventDefault();
  });
  $('#refresh').click(function() {
    $('.select2').val(null).trigger('change');
    $('#from_date').val('');
    $('select[name=payment_mode]').val('');
    $('#to_date').val('');
    ajax_datatable.draw();
  });
  @can('Users-edit')
  $(document).on('click', '.send_notification', function(e) {
    e.preventDefault();
    $('#notification_response').empty();
    var userId = $(this).attr('data-staff_id');
    $('#notificationUserId').val(userId);
    $('#notificationModal').modal('show');
  });

  $('.sendUserNotification').parsley();
  $(document).on('click', '.sendUserNotification', function(e) {
    e.preventDefault();
    var _this = $(this);
    $('#group_loader').fadeIn();
    var values = $('#sendUserNotification').serialize();
    var title = $('#title').val();
    var message = $('#message').val();

    if (title && message) {
      $.ajax({
        url: '{{ url("admin/orders / send_notification ") }}',
        dataType: 'json',
        data: values,
        type: 'POST',
        beforeSend: function() {
          before(_this)
        },
        // hides the loader after completion of request, whether successfull or failor.
        complete: function() {
          complete(_this)
        },
        success: function(result) {
          toastr.success(`Notification has been sent!`)
          setTimeout(function() {
            $('#disappear_add').fadeOut('slow')
          }, 3000)
          $("#sendUserNotification")[0].reset();
          // $('#sendUserNotification').parsley().reset();
          ajax_datatable.draw();
          window.location.reload();

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

    } else {
      toastr.error('Please fill all mandatory fields.')
    }
    return false;
  });
  @endcan
  @can('Order-edit')
  //Edit staff
  $(document).on('click', '.view_btn', function(e) {
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-staff_id');
    $.ajax({
      url: '{{url("admin/orders/view")}}/' + id,
      dataType: 'html',
      success: function(result) {
        $('#view_response').html(result);
      }
    });
    $('#viewModal').modal('show');
  });
  @endcan
  @can('Order-delete')
  $(document).on('click', '.delete_staff', function(e) {
    e.preventDefault();
    var response = confirm("{{ __('backend.confirm_box_delete') }}");
    if (response) {
      id = $(this).data('staff_id');
      $.ajax({
        type: 'post',
        data: {
          _method: 'delete',
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        url: "{!! url('admin/api/orders' )!!}" + "/" + id,
        success: function() {
          toastr.success('{{ __("Court is deleted successfully ") }}');
          ajax_datatable.draw();
        },
        error: function(jqXHR, textStatus, textStatus) {
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
  });
  @endcan

  function exportOrderData() {
    var link = '?';
    var court_id = $('select[name=court_id]').val();

    if (court_id) {
      link += 'court_id=' + court_id;
    }

    var player_id = $('select[name=player_id]').val();

    if (player_id) {
      link += '&player_id=' + player_id;
    }
    var from_date = $('input[name=from_date]').val();

    if (from_date) {
      link += '&from_date=' + from_date;
    }
    var to_date = $('input[name=to_date]').val();

    if (to_date) {
      link += '&to_date=' + to_date;
    }

    var url = "{{ url('admin/orders/exportOrders') }}" + link;
    window.location.href = url;
  }

  function facility_change() {
    var facility_id = $('#facility_id').val();
    if (facility_id) {
      $.ajax({
        url: "{{url('admin/orders/show_court_data')}}/" + facility_id,
        dataType: 'html',
        success: function(result) {
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
  $(document).on('click', '.dropdown-toggle', function() {
    var id = $(this).data('id');
    $(this).attr('aria-expanded', 'true');
    $(".dropdown" + id).addClass('show');
    $(".dropdownmenu" + id).addClass('show');
  });

  $('#search-form').on('submit', function(e) {
      ajax_datatable.draw();
        e.preventDefault();
      getWalletAmout();
});

function getWalletAmout() {
  var from_date = $('input[name=from_date]').val();
  var to_date = $('input[name=to_date]').val();
  var court_id = $('select[name=court_id]').val();
  var facility_id = $('select[name=facility_id]').val();
  var player_id = $('select[name=player_id]').val();
  var order_status = $('select[name=order_status]').val();
  var payment_type = $('select[name=payment_type]').val();


  // alert(country);
  var values = {
    from_date: from_date,
    to_date: to_date,
    court_id: court_id,
    facility_id: facility_id,
    player_id: player_id,
    order_status: order_status,
    payment_type: payment_type,
    _token: "{{ csrf_token() }}"
  };

  $.ajax({
      url:"{{ url('admin/cash-settlement/getAmountData') }}",
      dataType:'json',
      data:values,
      type:'POST',
      success:function(res){
        console.log(res);
        if(res.status === 1){
          $('.total_amount').text(res.data.total_amount)
          $('.paid_amount').text(res.data.paid_amount)
          $('.admin_commission_amount').text(res.data.admin_commission_amount)
          /*toastr.success(res.message);
          ajax_datatable.draw();*/
        }else{
          toastr.error(res.message);
        }
      },
      error:function(jqXHR,textStatus,textStatus){
        console.log(jqXHR);
        toastr.error(jqXHR.statusText)
      }
  });

}

</script>

@endsection