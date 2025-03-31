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
               @elseif($order_status == 'Prepare')
               {{ __('Ready To Deliver Orders Manager') }}
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
<div class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="card card-primary card-outline">
            <div class="card-body">
               <ul class="nav nav-tabs ">
                  <li class="nav-item ">
                     <a class="nav-link {{ ($order_status=='Pending'?'active':'') }} btn btn-block btn-outline-warning " href="{{ url('restaurant/deliver_order/Pending') }}" role="tab"><span >{{ __('New Orders') }} </span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Accepted'?'active':'') }} btn btn-block btn-outline-primary"  href="{{ url('restaurant/deliver_order/Accepted') }}" role="tab" ><span >{{ __('Accepted Orders') }}</span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Prepare'?'active':'') }} btn btn-block btn-outline-success"  href="{{ url('restaurant/deliver_order/Prepare') }}" role="tab" ><span >{{ __('Ready To Deliver Orders') }}</span></a>
                  </li>
                
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Complete'?'active':'') }} btn btn-block btn-outline-success"  href="{{ url('restaurant/deliver_order/Complete') }}" role="tab" ><span >{{ __('Completed Orders') }}</span></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link {{ ($order_status=='Cancel'?'active':'') }} btn btn-block btn-outline-danger"  href="{{ url('restaurant/deliver_order/Cancel') }}" role="tab" ><span >{{ __('Cancel Orders') }}</span></a>
                  </li>
               </ul>
               
                  <form method="POST" id="search-form" class="" role="form" style="margin-top:15px;">
                     <div class="input-daterange form_s">
                      <div class="row">
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                          <div class="form-group">
                             <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
                          </div>
                        </div>
                        <div class="col-xl-2 col-lg-3  col-md-4 col-sm-6">
                          <div class="form-group">
                             <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
                          </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                         <div class="form-group">
                            <select name="customer_id" id="customer_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Customers" data-dropdown-css-class="select2-primary">
                               <option value="">--Select Customer--</option>
                               @foreach ($customers as $customer)
                               <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                               @endforeach
                            </select>
                         </div>
                       </div>
                       <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                         <div class="form-group">
                            <select name="product_id" id="product_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Product" data-dropdown-css-class="select2-primary">
                               <option value="">--Select Products--</option>
                               @foreach ($products as $product)
                               <option value="{{ $product->id }}">{{ $product->name }}</option>
                               @endforeach
                            </select>
                         </div>
                       </div>
                       <div class="col-xl-2 col-lg-5 col-md-4 col-sm-6">
                         <div class="form-group">
                            <select name="payment_mode" id="payment_mode" class="form-control"  data-placeholder="Select Payment Mode" >
                               <option value="">--Select Payment Mode--</option>
                               <option value="3">Online Payment</option>
                               <option value="1">Cash Payment</option>
                            </select>
                         </div>
                       </div>
                       <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
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
                        <th> {{ __('Restaurant Name') }} </th> 
                        <th> {{ __('Products') }} </th> 
                        <th> {{ __('User Name') }} </th>
                        <!-- <th> {{ __('User Address') }} </th> -->
                        <th> {{ __('Order Type') }} </th>
                        <th> {{ __('Order Status') }} </th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Info') }}</th>
                     </tr>
                  </thead>
                  <tbody>
                    <!-- @foreach($orders as $key => $value)
                    <tr>
                      <td>{{ $key+1}}</td>
                      <td>{{ $value->id}}</td>
                      <td>{{ $value->products_type}}</td>
                      <td>{{ $value->cat_name}}</td>
                      <td>{{ $value->created_at}}</td>
                    </tr>
                    @endforeach -->
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
         url:'{{ url('orders') }}/{{$order_status}}',
         data: function (d) {
             //d.chef_id = $('select[name=chef_id]').val();
             //d.celebrity_id = $('select[name=celebrity_id]').val();
             d.product_id = $('select[name=product_id]').val();
             d.customer_id = $('select[name=customer_id]').val();
             d.from_date = $('input[name=from_date]').val();
             d.to_date = $('input[name=to_date]').val();
             d.payment_mode = $('select[name=payment_mode]').val();
         }
       },
       columns: [
         /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,"width": "20px"},*/
         { data: 'order_id', name: 'order_id' },
         { data: 'rest_name', name: 'rest_name' },
         { data: 'product_name', name: 'product_name' },
         { data: 'user_name', name: 'user_name' },
         /*{ data: 'user_address', name: 'user_address' },*/
         { data: 'dine_in', name: 'dine_in' },
         { data: 'order_status', name: 'order_status' },
         { data: 'amount', name: 'amount' },
         { data: 'created_at', name: 'created_at' },
         {data: 'order_id', name: 'order_id', orderable: false, searchable: false}
       ],
       order: [ [7, 'desc'] ],
       rowCallback: function(row, data, iDisplayIndex) {

           var links='';
           var order_id='';
           var amount = '';
           var order_status = '';
           if(data.order_status == 'Pending' || data.order_status == 'Accepted' || data.order_status == 'Prepare') {
             order_status += `<div class="btn-group" role="group" >`;
             if(data.order_status == 'Pending') {
                 order_status += `<a href="#" data-order_id="${data.order_id}" title="Prepare Order" data-order_status="Pending" class="btn btn-primary btn-lg status_btn" >Accept</a>`;
             } else if(data.order_status == 'Accepted') { 
                order_status += `<a href="#" data-order_id="${data.order_id}" title="Prepare Order" data-order_status="Accepted" class="btn btn-primary btn-lg status_btn" >Ready to deliver</a>`;
             } else if(data.order_status == 'Prepare') {
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
            amount +=`$${data.amount}`;
           }

           links += `<a href="{{ url('orders/view') }}/${data.order_id}" title="Order Details" class="btn btn-info btn-xs" style='margin-left:5px'><span class="fa fa-eye"></span></a>`;
            
           

           $('td:eq(0)', row).html(order_id);
           if(data.order_status == 'Pending' || data.order_status == 'Accepted' || data.order_status == 'Prepare'){
             $('td:eq(5)', row).html(order_status);
           } else {
             $('td:eq(5)', row).html(order_status);
           }
           $('td:eq(6)', row).html(amount);
           $('td:eq(8)', row).html(links);
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
   
   });
</script>
@endsection