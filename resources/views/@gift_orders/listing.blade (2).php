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


         @elseif($order_status == 'Cancel')
         {{ __('Cancel Orders Manager') }}
         @else
         {{ __('New Orders Manager') }}
         @endif
      </h4>
   </div>
   <div class="col-md-7 align-self-center text-right">
      <div class="d-flex justify-content-end align-items-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">
               @if($order_status == 'Complete')
               {{ __('Complete Orders Manager') }}
               @elseif($order_status == 'Accepted')
               {{ __('Accepted Orders Manager') }}

               @elseif($order_status == 'Cancel')
               {{ __('Cancel Orders Manager') }}
               @else
               {{ __('New Orders Manager') }}
               @endif
            </li>
         </ol>
      </div>
   </div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content gift_listing">
   <div class="row">
      <div class="col-md-12">
         <div class="card card-primary card-outline">
            <div class="card-body">
               <ul class="nav nav-tabs ">
                  @if($order_status == 'Pending' || $order_status == 'Accepted')
                  <li class="nav-item ">
                     <a class="nav-link {{ ($order_status=='Pending'?'active':'') }} btn btn-block btn-outline-warning " href="{{ url('gift_orders/Pending') }}" role="tab"><span >{{ __('New Orders') }} </span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Accepted'?'active':'') }} btn btn-block btn-outline-primary"  href="{{ url('gift_orders/Accepted') }}" role="tab" ><span >{{ __('Accepted Orders') }}</span></a>
                  </li>
                  @endif
                  @if($order_status == 'Complete' || $order_status == 'Cancel')
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Complete'?'active':'') }} btn btn-block btn-outline-success"  href="{{ url('gift_orders/Complete') }}" role="tab" ><span >{{ __('Completed Orders') }}</span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Cancel'?'active':'') }} btn btn-block btn-outline-danger"  href="{{ url('gift_orders/Cancel') }}" role="tab" ><span >{{ __('Cancel Orders') }}</span></a>
                  </li>
                  @endif
               </ul>
               <div class="">
                  <form method="POST" id="search-form" class="form-inline-sec" role="form">	
						<div class="row">
							<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
								<div class="row input-daterange">
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
										<div class="form-group">
										   <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
										</div>
									</div>
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
										<div class="form-group">
										   <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
										</div>
									</div>
								</div>
							</div>
						   <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-6">
							  <select name="customer_id" id="customer_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Customers" data-dropdown-css-class="select2-primary">
								 <option value="">--Select Customer--</option>
								 @foreach ($customers as $customer)
								 <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->country_code }} {{ $customer->mobile }})</option>
								 @endforeach
							  </select>
						   </div>
						   <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-6">
							  <select name="gift_id" id="gift_id" style="width:200px" class="form-control select2" multiple="multiple"  data-placeholder="Select Gifts" data-dropdown-css-class="select2-primary">
								 <option value="">--Select Gifts--</option>
								 @foreach ($gifts as $gift)
								 <option value="{{ $gift->id }}">{{ $gift->name }}</option>
								 @endforeach
							  </select>
						   </div>
                     <!-- <div class="form-group" style="margin-right:5px">
                        <select name="payment_mode" id="payment_mode" style="width:200px" class="form-control"  data-placeholder="Select Payment Mode" >
                           <option value="">--Select Payment Mode--</option>
                           <option value="3">Online Payment</option>
                           <option value="1">Cash Payment</option>
                        </select>
                     </div> -->
					 <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
						 <button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
						 <a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
					 </div>
                  </form>
               </div>
				<div class="table-responsive">
				    <table id="listing" class="table table-striped table-bordered" style="width:100%">
					  <thead>
						 <tr>
							<!-- <th> {{ __('Sr. no') }} </th> -->
							<th> {{ __('Order Id') }} </th>
							<th> {{ __('Gifts') }} </th>
							<th> {{ __('User Name') }} </th>
							<th> {{ __('User Address') }} </th>
							<th> {{ __('Order Status') }} </th>
							<th>{{ __('Points') }}</th>
							<th>{{ __('Created At') }}</th>
							<th>{{ __('Info') }}</th>
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
<<!-- Modals -->
<div class="modal fade" id="order_cancel_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <form method="POST" action="{{ url('gift_orders/cancelOrderStatus') }}" id="cancel_order">
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
                  @foreach ($OrderCancelReasions as $row)
                      <option value="{{ $row->id }}">{{ $row->reasion }}</option>
                  @endforeach
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
         url:'{{ url('api/gift_orders') }}/{{$order_status}}',
         data: function (d) {
             //d.chef_id = $('select[name=chef_id]').val();
             //d.celebrity_id = $('select[name=celebrity_id]').val();
             d.gift_id = $('select[name=gift_id]').val();
             d.customer_id = $('select[name=customer_id]').val();
             d.from_date = $('input[name=from_date]').val();
             d.to_date = $('input[name=to_date]').val();
             d.payment_mode = $('select[name=payment_mode]').val();
         }
       },
       columns: [
         /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,"width": "20px"},*/
         { data: 'random_order_id', name: 'random_order_id' },
         { data: 'gift_name', name: 'gift_name', orderable: false, searchable: false },
         { data: 'user_name', name: 'user_name' },
         { data: 'address', name: 'address' },
         { data: 'order_status', name: 'order_status' },
         { data: 'points', name: 'points' },
         { data: 'created_at', name: 'created_at' },
         {data: 'gift_order_id', name: 'gift_order_id', orderable: false, searchable: false}
       ],
       order: [ [6, 'desc'] ],
       rowCallback: function(row, data, iDisplayIndex) {

           var links='';
           var user_name='';
           var gift_order_id='';
           var order_status = '';
           if(data.order_status == 'Pending' || data.order_status == 'Accepted') {
             order_status += `<div class="" role="group" >`;
             if(data.order_status == 'Pending'){
                 // order_status += `<a href="#" data-order_id="${data.gift_order_id}" title="Prepare Order" data-order_status="Pending" class="btn btn-primary btn-sm status_btn" >Accept</a>`;
                 order_status += `<div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Choose Status
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a href="#" data-order_id="${data.gift_order_id}" title="Prepare Order" data-order_status="Pending" class="dropdown-item status_btn" >Accept</a>
                    <a data-order_id="${data.gift_order_id}" title="Cancel Order" onclick="orderCancel(this)" data-order_status="Cancel" class="dropdown-item" href="javascript:void(0);">Cancel</a>
                  </div>
                </div>`;

             }else if(data.order_status == 'Accepted'){
               order_status += `<div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Choose Status
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a data-order_id="${data.gift_order_id}" title="Complete Order" data-order_status="Complete" class="dropdown-item  status_btn" href="#">Complete</a>
                    <a data-order_id="${data.gift_order_id}" title="Cancel Order" onclick="orderCancel(this)" data-order_status="Cancel" class="dropdown-item" href="javascript:void(0);">Cancel</a>
                  </div>
                </div>`;
                 /*order_status += `<a href="#" data-order_id="${data.order_id}" title="Accepted Order" data-order_status="Accepted" class="btn btn-primary btn-xs status_btn" >Accept</a>`;*/
             }
           } else {
             order_status += `${data.order_status}`;
           }

           //dd(data.order_status);
           if(data.user_name == null){
              user_name += `No Name`;
            } else {
              user_name += `<span> ${data.user_name} </span>`;
            }

           /*if (data.gift_order_id) {
            gift_order_id +=`ORD#${data.gift_order_id}`;
           }*/

           if (data.gift_order_id) {
            // order_id +=`ORD#${data.random_order_id}`;

            if (data.random_order_id) {
              gift_order_id +=`${data.random_order_id}`;

            } else {
              gift_order_id +=`ORD#${data.gift_order_id}`;
            }
           }
           
           links += `<a href="{{ url('gift_orders/view') }}/${data.gift_order_id}" title="Order Details" class="btn btn-info btn-xs" style='margin-left:5px'><span class="fa fa-eye"></span></a>`;

           $('td:eq(0)', row).html(gift_order_id);
           $('td:eq(2)', row).html(user_name);
           if(data.order_status == 'Pending' || data.order_status == 'Accepted'){
             $('td:eq(4)', row).html(order_status);
           } else {
             $('td:eq(4)', row).html(order_status);
           }
           $('td:eq(7)', row).html(links);
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
          url:'{{ url('gift_orders/cancelOrderStatus') }}',
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
       if(status == 'Pending'){
           var response = confirm('Are you sure want to accept this Order?');
       }else if(status == 'Cancel'){
           var response = confirm('Are you sure want to go Cancel this Order?');
       }else if(status == 'Complete'){
           var response = confirm('Are you sure want to complete this Order?');
       }
       if(response){
           id = $(this).data('order_id');
           $.ajax({
               type: 'post',
               data: {_method: 'get', _token: "{{ csrf_token() }}"},
               dataType:'json',
               url: "{!! url('gift_orders/changeOrderStatus' )!!}" + "/" + id +'/'+status,
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

  function orderCancel($this) {
    var order_id = $($this).attr('data-order_id');

    if (order_id) {
      $('#cancel_order_id').val(order_id);
      $('#order_cancel_modal').modal('show');

    } else {
      toastr.error('Somthing went wrong!');
    }
  }
</script>
@endsection