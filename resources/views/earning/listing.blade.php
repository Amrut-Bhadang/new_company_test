@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<div class="row page-titles">
   <div class="col-md-5 align-self-center">
      <h4 class="text-themecolor">
         {{ __('Earning Manager') }}
      </h4>
   </div>
   <div class="col-md-7 align-self-center text-right">
      <div class="d-flex justify-content-end align-items-center">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">
               {{ __('Earning Manager') }}
            </li>
         </ol>
      </div>
   </div>
</div>
<!-- Main content -->
<div class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="card card-primary card-outline">
            <div class="card-body">
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
						<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
							<div class="form-group">
								<select name="restaurant_id" id="restaurant_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Store" data-dropdown-css-class="select2-primary">
								   <option value="">--Select Store--</option>
								   @foreach ($restaurants as $restaurant)
								   <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
								   @endforeach
								</select>
							</div>
						</div>
						<!-- <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
							<div class="form-group">
								<select name="customer_id" id="customer_id" style="width:200px" class="form-control select2" multiple="multiple"  data-placeholder="Select Customers" data-dropdown-css-class="select2-primary">
								   <option value="">--Select Customer--</option>
								   @foreach ($customers as $customer)
								   <option value="{{ $customer->id }}">{{ ($customer->name && !empty($customer->name)) ? $customer->name : $customer->mobile }}</option>
								   @endforeach
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
							<div class="form-group">
								<select name="product_id" id="product_id" style="width:200px" class="form-control select2" multiple="multiple"  data-placeholder="Select Product" data-dropdown-css-class="select2-primary">
								   <option value="">--Select Products--</option>
								   @foreach ($products as $product)
								   <option value="{{ $product->id }}">{{ $product->name }}</option>
								   @endforeach
								</select>
							</div> 
						</div>  -->
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
					</div>
                  </form>
               </div>
				<div class="table-responsive">
				   <table id="listing" class="table table-striped table-bordered" style="width:100%">
					  <thead>
						 <tr>
							<!-- <th> {{ __('Sr. no') }} </th> -->
							<th> {{ __('Order Id') }} </th>
							<th> {{ __('Store Name') }} </th> 
							<th> {{ __('Products') }} </th> 
							<!-- <th> {{ __('User Name') }} </th> -->
							<!-- <th> {{ __('User Address') }} </th> -->
							<th> {{ __('Order Status') }} </th>
							<th>{{ __('Order Amount') }}</th>
              <th>{{ __('Admin Amount') }}</th>
							<th>{{ __('Created At') }}</th>
							<!-- <th>{{ __('Info') }}</th> -->
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
   ajax_datatable = $('#listing').DataTable({
       processing: true,
       serverSide: true,
       ajax:{
         url:'{{ url('api/earning') }}',
         data: function (d) {
             //d.chef_id = $('select[name=chef_id]').val();
             //d.celebrity_id = $('select[name=celebrity_id]').val();
             d.product_id = $('select[name=product_id]').val();
             d.customer_id = $('select[name=customer_id]').val();
             d.restaurant_id = $('select[name=restaurant_id]').val();
             d.from_date = $('input[name=from_date]').val();
             d.to_date = $('input[name=to_date]').val();
             d.payment_mode = $('select[name=payment_mode]').val();
         }
       },
       columns: [
         /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,"width": "20px"},*/
         { data: 'order_id', name: 'order_id' },
         { data: 'rest_name', name: 'rest_name', orderable: false, searchable: false },
         { data: 'product_name', name: 'product_name' },
         // { data: 'user_name', name: 'user_name' },
         // { data: 'user_address', name: 'user_address' },
         { data: 'order_status', name: 'order_status' },
         { data: 'amount', name: 'amount' },
         { data: 'admin_amount', name: 'admin_amount' },
         { data: 'created_at', name: 'created_at' },
         // {data: 'order_id', name: 'order_id', orderable: false, searchable: false}
       ],
       order: [ [6, 'desc'] ],
       rowCallback: function(row, data, iDisplayIndex) {

           var links='';
           var order_id='';
           var amount = '';
           var admin_amount = '';
           var order_status = '';
           if(data.order_status == 'Pending' || data.order_status == 'Accepted') {
             order_status += `<div class="btn-group" role="group" >`;
             if(data.order_status == 'Pending'){
                 order_status += `<a href="#" data-order_id="${data.order_id}" title="Prepare Order" data-order_status="Pending" class="btn btn-primary btn-xs status_btn" >Pending</a>`;
             }else if(data.order_status == 'Accepted'){
               order_status += `<div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Choose Status
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a data-order_id="${data.order_id}" title="Complete Order" data-order_status="Complete" class="dropdown-item  status_btn" href="#">Complete</a>
                    <a data-order_id="${data.order_id}" title="Cancel Order" data-order_status="Cancel" class="dropdown-item status_btn" href="#">Cancel</a>
                  </div>
                </div>`
                 /*order_status += `<a href="#" data-order_id="${data.order_id}" title="Accepted Order" data-order_status="Accepted" class="btn btn-primary btn-xs status_btn" >Accept</a>`;*/
             }
           } else {
             order_status += `${data.order_status}`;
           }

           //dd(data.order_status);

           if (data.order_id) {
            order_id +=`ORD#${data.order_id}`;
           }
           if (data.amount) {
            amount +=`QAR ${(data.amount + data.shipping_charges)}`;
           }

           if (data.admin_amount) {

            if (data.discount_type == 'Flat-Discount') {
              admin_amount +=`QAR ${data.admin_amount - data.discount_amount}`;

            } else {
              admin_amount +=`QAR ${data.admin_amount}`;
            }
           }
           links += `<a href="{{ url('earning/view') }}/${data.order_id}" title="Order Details" class="btn btn-info btn-xs" style='margin-left:5px'><span class="fa fa-eye"></span></a>`;

           $('td:eq(0)', row).html(order_id);
           if(data.order_status == 'Pending' || data.order_status == 'Accepted'){
             $('td:eq(3)', row).html(order_status);
           } else {
             $('td:eq(3)', row).html(order_status);
           }
           $('td:eq(4)', row).html(amount);
           $('td:eq(5)', row).html(admin_amount);
           // $('td:eq(8)', row).html(links);
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
               url: "{!! url('earning/changeOrderStatus' )!!}" + "/" + id +'/'+status,
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
</script>
@endsection