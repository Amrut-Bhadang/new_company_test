@extends('layouts.master')

@section('content')
<?php

use App\Models\Language;

$language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('backend.Notifications_Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.Notifications_Manager') }}</li>
            </ol>
            @can('Notification-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Notification') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('backend.send_notification') }}</a>
            @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                   
                     <form method="POST" id="search-form" class="form-inline-sec" role="form">
						<div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
								<div class="row input-daterange">
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
									  <div class="form-group" style="margin-right:5px">
										  <input type="text" name="from_date" id="from_date" class="form-control" placeholder="{{ __('backend.From_Date') }}" readonly />
									  </div>
									</div>
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
									  <div class="form-group" style="margin-right:5px">
										  <input type="text" name="to_date" id="to_date" class="form-control" placeholder="{{ __('backend.To_Date') }}" readonly />
									  </div>
									</div>
								</div>
							</div>
							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
							  <button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">{{ __('backend.Filter') }}</button>
							  <a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> {{ __('backend.Reset') }}</a>
							</div>
						</div>
                    </form>
					<div class="table-responsive">
						<table id="notification_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>{{ __('backend.Title') }}</th>
									<th>{{ __('backend.Notification_Type') }}</th>
									<th>{{ __('backend.Notification_For') }}</th>
									<th>{{ __('backend.Message') }}</th>
									<th>{{ __('backend.Date') }}</th>
								</tr>
							</thead>
							<tbody>
							   
							</tbody>
						</table>
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
          <div class="modal-content notification_sec">
          <form method="POST" action="{{ url('admin/api/notifications') }}" id="add_notification">
          @csrf
            <div class="modal-header">
              <h4 class="modal-title">{{__('backend.New_Notification')}}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">   
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="notification_for">{{__('backend.Notification_For')}}*</label>
                    <select name="notification_for" id='notification_for' onchange="type_change()" class="form-control select2" data-placeholder="{{__('backend.Select_User_Type')}}" style="width: 100%;" data-parsley-required="true" data-dropdown-css-class="select2-primary">
                        <option value=''>--{{__('backend.Select_User_Type')}}--</option>
                        <option value="All">{{__('backend.All')}}</option>
                        <option value="Players">{{__('backend.Players')}}</option>
                        <option value="FacilityOwner">{{__('backend.Facility_Owner')}}</option>
                    </select>
                  </div>
                </div>  
                <div class="col-md-12 type_data_show_div">
                  <div class="form-group">
                    <label class="control-label" for="select_user">{{ __('backend.user') }}</label>
                    <select name="select_user[]" id="select_user" multiple="multiple" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{ __('backend.user') }}" data-dropdown-css-class="select2-primary">
                      <option value="">--{{ __('backend.Select') }} {{ __('backend.user') }}--</option>
                    
                    </select>
                  </div>
                </div>
                @if($language)
                @foreach($language as $key => $lang)            
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label" for="title">{{__('backend.Title')}} ({{__('backend.'.$lang)}})*</label>
                    <input type="text" name="title[{{$lang}}]" value="" id="title" class="form-control" placeholder="{{__('backend.Title')}}" data-parsley-required="true"  />
                  </div>
                </div>
                @endforeach
                @endif
                @if($language)
                @foreach($language as $key => $lang)   
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label" for="message">{{__('backend.Message')}} ({{__('backend.'.$lang)}})*</label>
                    <textarea name="message[{{$lang}}]" id="message" class="form-control" placeholder="{{__('backend.Message')}}" data-parsley-required="true"></textarea>
                  </div>
                </div>
                @endforeach
                @endif
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{__('backend.Close')}}</button>
              <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>
            </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->



<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>

<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_notification').parsley();
ajax_datatable = $('#notification_listing').DataTable({
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
    ajax:{
      url:'{{ url("admin/api/notifications") }}',
      data: function (d) {
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
      }
    },
    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'title', name: 'title' },
      { data: 'notification_type', name: 'notification_type' },
      { data: 'notification_for', name: 'notification_for' },
      { data: 'message', name: 'message' },
      { data: 'created_at', name: 'created_at' },
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
     var notification_for = '';
     var notification_type = '';
      if(data.notification_for == 'FacilityOwner'){
        notification_for += "{{__('backend.Facility_Owner')}}";
      }else if(data.notification_for == 'Players'){
        notification_for += "{{__('backend.Players')}}";
      }
      else{
        notification_for += "{{__('backend.All')}}";
      }
      if(data.notification_type == 'Send'){
        notification_type += "{{__('backend.Send')}}";
      }else{
        notification_type += data.notification_type;
      }
      $('td:eq(2)', row).html(notification_for);
      $('td:eq(1)', row).html(notification_type);
    },
});

@can('Notification-create')
$("#add_notification").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var values = $('#add_notification').serialize();
    $.ajax({
    url:"{{ url('admin/api/notifications') }}",
    dataType:'json',
    data:values,
    type:'POST',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(res){
          console.log(res);
          if(res.status === 1){ 
            toastr.success(res.message);
            $('#add_notification')[0].reset();
            $('#add_notification').parsley().reset();
            ajax_datatable.draw();
            location.reload();

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
@endcan
});
</script>

 <script>
var ajax_datatable;
$(document).ready(function(){
    $('.input-daterange').datepicker({
  todayBtn:'linked',
  format:'yyyy-mm-dd',
  autoclose:true
 });
});

$('#search-form').on('submit', function(e) {
      ajax_datatable.draw();
        e.preventDefault();
});
$('.select2').select2();

function type_change() {
    var type = $('#notification_for').val();
    // alert(type);
    // return false;
    if (type) {
      $.ajax({
        url: "{{url('admin/notifications/show_type_data')}}/" + type,
        dataType: 'html',
        success: function(result) {
          $('.type_data_show_div').html(result);
          $('.link-div').hide();
        }
      });

    } else {
      $("input[name='category_type']:checked").prop('checked', false);
      toastr.error('Please choose main notification_for first');
      return false;
    }
  }
 


</script>



@endsection
