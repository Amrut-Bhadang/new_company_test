@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
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
         {{ __('backend.Booking_Manager') }}
         @endif
        <a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> {{ __('backend.Refresh') }}</a>
      </h4>
   </div>
   <div class="col-md-7 align-self-center text-right">
      <div class="d-flex justify-content-end align-items-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
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
               {{ __('backend.Booking_Manager') }}
               @endif
            </li>
         </ol>
      </div>
   </div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content order-page">
   <div class="row">
      <div class="col-md-12">
         <div class="card card-primary card-outline">
            <div class="card-body">
               <ul class="nav nav-tabs mb-2">
                  @if($order_status == 'Pending' || $order_status == 'Accepted' || $order_status == 'Prepare')
                  <li class="nav-item ">
                     <!-- <a class="nav-link {{ ($order_status=='Pending'?'active':'') }} btn btn-block btn-outline-warning " href="{{ url('orders/Pending') }}" role="tab"><span >{{ __('New Orders') }} </span></a> -->
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Accepted'?'active':'') }} btn btn-block btn-outline-primary"  href="{{ url('orders/Accepted') }}" role="tab" ><span >{{ __('Accepted Orders') }}</span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Prepare'?'active':'') }} btn btn-block btn-outline-success"  href="{{ url('orders/Prepare') }}" role="tab" ><span >{{ __('Ready To Deliver Orders') }}</span></a>
                  </li>
                  @endif
                  @if($order_status == 'Complete' || $order_status == 'Cancel')
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Complete'?'active':'') }} btn btn-block btn-outline-success"  href="{{ url('orders/Complete') }}" role="tab" ><span >{{ __('Completed Orders') }}</span></a>
                  </li>
                  <li class="nav-item">
                     <!-- <a class="nav-link {{ ($order_status=='Cancel'?'active':'') }} btn btn-block btn-outline-danger"  href="{{ url('orders/Cancel') }}" role="tab" ><span >{{ __('Cancel Orders') }}</span></a> -->
                  </li>
                  @endif
               </ul>
               
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
                            <select name="court_id" id="court_id" style="width:200px" class="form-control select2"  data-placeholder="{{ __('backend.Select_Court') }}" data-dropdown-css-class="select2-primary">
                               <option value="">--{{ __('backend.Select_Court') }}--</option>
                               @foreach ($courts as $court)
                               <option value="{{ $court->id }}">{{ $court->court_name }}</option>
                               @endforeach
                            </select>
                        </div>
                       <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                         <div class="form-group">
                            <select name="customer_id" id="customer_id" class="form-control select2" multiple="multiple"  data-placeholder="{{ __('backend.Select_Customer') }}" data-dropdown-css-class="select2-primary">
                               <option value="">--{{ __('backend.Select_Customer') }}--</option>
                               @foreach ($customers as $customer)
                               <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->country_code }} {{ $customer->mobile }})</option>
                               @endforeach
                            </select>
                         </div>
                       </div>
                       <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                         <div class="form-group">
                            <select name="order_status" id="order_status" class="form-control select2" data-placeholder="{{ __('backend.Select_Order_Status') }}" data-dropdown-css-class="select2-primary">
                               <option value="">--{{ __('backend.Select_Order_Status') }}--</option>
                               <option value="Pending">Pending</option>
                               <option value="Accepted">Accepted</option>
                               <option value="Cancel">Cancelled</option>
                               <option value="Complete">Completed</option>
                            </select>
                         </div>
                       </div>
                       <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                         <button type="submit" name="filter" id="filter" class="btn btn-info" style="margin-right:5px">{{ __('backend.Filter') }}</button><a href="{{url()->current()}}" class="btn btn-dark" > <i class="fas fa-redo-alt"></i> {{ __('backend.Reset') }}</a>
                         <a href="javascript:void(0);" onclick="exportOrderData()" class="btn btn-info" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('backend.Export') }}</a>
                       </div>
                     </div>
                  </form>
                </div>
                <div class="table-responsive">

					<table id="listing" class="table table-striped table-bordered" style="width:100%">
						<thead>
						 <tr>
							<th> {{ __('backend.Sr_no') }} </th>
							<th> {{ __('backend.User_Name') }} </th>
              <th> {{ __('backend.Court_Name') }} </th>
              <th> {{ __('backend.Facility_Name') }} </th>
							<th> {{ __('backend.Booking_Date') }} </th>
              <th> {{ __('backend.Time_Slot') }} </th>
              <th> {{ __('backend.Amount') }} </th> 
              <th> {{ __('backend.Admin_Commission') }} </th> 
							<th> {{ __('backend.Mode_of_Payment') }} </th>
							<th>{{ __('backend.Status') }}</th>
							<th>{{ __('backend.Created_At') }}</th>
              <th>{{ __('backend.Action') }}</th>
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
<!-- /.content -->
<!-- Modals -->
<div class="modal fade" id="order_cancel_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <form method="POST" action="{{ url('orders/cancelOrderStatus') }}" id="cancel_order">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">Reason For Cancel</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tab-content" style="margin-top:10px">
          <input type="hidden" id="cancel_order_id" name="order_id" value="">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="reason">Reason*</label>
                <select name="reasion_id" class="form-control"  data-parsley-required="true" >
                  <option value="">---Select Reason----</option>
                  
                </select>
              </div>
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
<!-- /Modals -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
<script>
  function getRestroListByMainCat() {
    var main_category_ids = $('select[name=main_category_id]').val();

    $.ajax({
         url:'{{url("orders/show_restro_byMainCatIds")}}/'+main_category_ids,
         dataType: 'html',
         success:function(result)
         {
            $('.show_filter_restroDiv').html(result);
         }
    });
  }

   var ajax_datatable;
   $(document).ready(function(){
    $('.input-daterange').datepicker({
     todayBtn:'linked',
     format:'yyyy-mm-dd',
     autoclose:true
    });
   
   $('.select2').select2();

       $('.select2').select2();
   $('#add_form').parsley();
   $('#cancel_order').parsley();
   ajax_datatable = $('#listing').DataTable({
       processing: true,
       serverSide: true,
       ajax:{
         url:'{{ url("admin/api/orders") }}/{{$order_status}}',
         data: function (d) {
             //d.chef_id = $('select[name=chef_id]').val();
             d.court_id = $('select[name=court_id]').val();
             d.customer_id = $('select[name=customer_id]').val();
             d.order_status = $('select[name=order_status]').val();
             d.from_date = $('input[name=from_date]').val();
             d.to_date = $('input[name=to_date]').val();
             d.payment_mode = $('select[name=payment_mode]').val();
         }
       },
       columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,"width": "20px"},
         { data: 'user_name', name: 'users.name' },
         { data: 'court_name', name: 'courts.court_name' },
         { data: 'facility_name', name: 'facilities.name' },
         { data: 'booking_datetime', name: 'booking_datetime'},
         { data: 'booking_time_slot', name: 'booking_time_slot'},
         { data: 'total_amount', name: 'total_amount' },
         { data: 'admin_commission_amount', name: 'admin_commission_amount' },
         { data: 'payment_type', name: 'payment_type' },
         { data: 'order_status', name: 'order_status', orderable: false, searchable: false },
         { data: 'created_at', name: 'created_at' },
         {data: 'id', name: 'id', orderable: false, searchable: false}
       ],
       order: [ [10, 'desc'] ],
       rowCallback: function(row, data, iDisplayIndex) {
           var links='';
           var random_order_id='';
           var amount = '';
           var order_status = '';
           var currency_code = '';
           // order_status += `${data.order_status}`;
           links += `<div class="btn-group" role="group" >`;

           if(data.order_status == 'Pending'){
              order_status += `<a href="#" data-staff_id="${data.id}" title="" data-status="Pending" class=""><span class='label label-rounded label-success'>${data.order_status}</span></a>`;
            } else {
              order_status += `<a href="#" data-staff_id="${data.id}" title="" data-status="${data.order_status}" class=""><span class='label label-rounded label-warning'>${data.order_status}</span></a>`;
            }

           if (data.amount) {
            var totalORDAmout = data.amount + data.shipping_charges + data.tax_amount;
            var formated_amount = data.ordered_currency_code+totalORDAmout.toFixed(2);
            amount +=`${formated_amount}`;
           }

           // links += `<a href="#" data-order_id="${data.id}" title="{{ __('backend.View_Details') }}" class="btn btn-success btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;

            if (data.payment_type == 'Cash' && data.order_status == 'Pending') {
                links += `<a href="#" data-order_id="${data.id}" title="{{ __('backend.confirm_cash_received') }}" class="btn btn-info btn-xs cash_confirm" ><span class="fas fa-money-bill-wave"></span></a>`;
            }

           /*if(data.order_status == 'Pending' || data.order_status == 'Accepted' || data.order_status == 'Prepare') {
              links += `<a href="{{ url('orders/view') }}/${data.id}" title="Order Details" class="btn btn-info btn-xs" style='margin-left:5px'><span class="fa fa-eye"></span></a>`;
            } else {
                links += `<a href="{{ url('orders/history/view') }}/${data.id}" title="Order Details" class="btn btn-info btn-xs" style='margin-left:5px'><span class="fa fa-eye"></span></a>`;
            }*/
            links += `</div>`;
            

           if(data.order_status == 'Pending' || data.order_status == 'Accepted' || data.order_status == 'Prepare'){
             $('td:eq(9)', row).html(order_status);
           } else {
             $('td:eq(9)', row).html(order_status);
           }
           $('td:eq(11)', row).html(links);
       },
   });
   $('#search-form').on('submit', function(e) {
         ajax_datatable.draw();
           e.preventDefault();
   });
   $('#refresh').click(function(){
       $('.select2').val(null).trigger('change');
     $('#from_date').val('');
     $('select[name=payment_mode]').val('');
     $('#to_date').val('');
     ajax_datatable.draw();
    });

    $("#cancel_order").on('submit',function(e) {
      e.preventDefault();
      var _this=$(this); 
      var formData = new FormData(this);
      $.ajax({
          url:'{{ url('orders/cancelOrderStatus') }}',
          dataType:'json',
          data:formData,
          type:'POST',
          cache:false,
          contentType: false,
          processData: false,
          beforeSend: function (){before(_this)},
          // hides the loader after completion of request, whether successfull or failor.
          complete: function (){complete(_this)},
          success:function(res) {

            if(res.status === 1){ 
              toastr.success(res.message);
              location.reload();

            } else {
              toastr.error(res.message);
            }
          },
          error:function(jqXHR,textStatus,textStatus) {

            if (jqXHR.responseJSON.errors) {
              $.each(jqXHR.responseJSON.errors, function( index, value ) {
                toastr.error(value)
              });

            } else {
              toastr.error(jqXHR.responseJSON.message)
            }
          }
      });
      return false;   
    });
   $(document).on('click','.status_btn',function(e){
       e.preventDefault();
       status = $(this).data('order_status');
       payment_type = $(this).data('payment_type');
       if(status == 'Pending'){
           var response = confirm('Are you sure want to accept this order?');
       }else if(status == 'Accepted'){
           var response = confirm('Are you sure order is ready for delivery?');
       }else if(status == 'Cancel'){
           var response = confirm('Are you sure want to go Cancel this order?');
       }else if(status == 'Complete'){

          if (payment_type == 'Cash') {
           var response = confirm('Did you receive the amount of this order?');

          } else {
            var response = confirm('Are you sure want to go Complete this order?');
          }
       }
       if(response){
           id = $(this).data('order_id');
           $.ajax({
               type: 'post',
               data: {_method: 'get', _token: "{{ csrf_token() }}"},
               dataType:'json',
               url: "{!! url('orders/changeOrderStatus' )!!}" + "/" + id +'/'+status,
               success:function(res){
               if(res.status === 1){
                   toastr.success(res.message);
                   ajax_datatable.draw();
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
       return false;
   });
  });

    $(document).on('click','.cash_confirm',function(e){
        e.preventDefault();
        var response = confirm("{{ __('backend.confirm_cash_received') }}");
        if(response){
            id = $(this).data('order_id');
            $.ajax({
              type: 'get',
              data: { _token: "{{ csrf_token() }}"},
              dataType:'json',
              url: "{!! url('orders/cash_confirm' )!!}" + "/" + id,
              success:function(){
                toastr.success('{{ __('backend.order_status_change') }}');
                ajax_datatable.draw();
              },
              error:function(jqXHR,textStatus,textStatus){
                console.log(jqXHR);
                toastr.error(jqXHR.statusText)
              }
            });
        }
        return false;
    });

  function orderCancel($this) {
    var order_id = $($this).attr('data-order_id');

    if (order_id) {
      $('#cancel_order_id').val(order_id);
      $('#order_cancel_modal').modal('show');

    } else {
      toastr.error('Somthing went wrong!');
    }
  }

  function exportOrderData() {
    var link = '?';
    var court_id = $('select[name=court_id]').val();

    if (court_id) {
      link += 'court_id='+court_id;
    }

    var customer_id = $('select[name=customer_id]').val();

    if (customer_id) {
      link += '&customer_id='+customer_id;
    }
    var from_date = $('input[name=from_date]').val();

    if (from_date) {
      link += '&from_date='+from_date;
    }
    var to_date = $('input[name=to_date]').val();

    if (to_date) {
      link += '&to_date='+to_date;
    }

    var url = "{{ url('/orders/exportOrders') }}"+link;
    window.location.href = url;
  }
</script>
@endsection