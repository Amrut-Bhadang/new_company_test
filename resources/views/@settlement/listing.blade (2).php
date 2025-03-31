@extends('layouts.master')

@section('content')
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Settlement Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Settlement Manager') }}</li>
            </ol>
              @can('Settlement-create')
              <!-- <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Banner') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a> -->
              <a href="{{ url('/settlement/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a> 
              @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content settlement">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <form method="POST" id="search-form" class="form-inline-sec" role="form">
                        <div class="row">
								<div class="col-md-6 col-lg-4 col-sm-6">
									<div class="row input-daterange">
										<div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-6" >
											<input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
										</div>
        								<div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-6" >
        									<input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
        								</div>
									</div>	
								</div>
                                <div class="form-group col-md-3 col-lg-2 col-sm-6">
                                  <select name="main_category_id" id="main_category_id" style="width:200px" class="form-control select2"  data-placeholder="Select Service" data-dropdown-css-class="select2-primary">
                                     <option value="">--Select Service--</option>
                                     @foreach ($main_category as $main_category)
                                     <option value="{{ $main_category->id }}">{{ $main_category->name }}</option>
                                     @endforeach
                                  </select>
                                </div>
                                <div class="form-group col-md-3 col-lg-2 col-sm-6" >
                                    <select name="restaurant_id" id="restaurant_id" onchange="filterTrigger()" style="width:200px" class="form-control select2"  data-placeholder="Select Store" data-dropdown-css-class="select2-primary">
                                       <option value="">--Select Store--</option>
                                       @foreach ($restaurant as $customer)
                                       <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                       @endforeach
                                    </select>
                                </div>
								<!-- <div class="form-group col-md-3 col-lg-2 col-sm-6" >
									<select name="order_status" id="order_status" style="width:200px" class="form-control select2" data-placeholder="Select Order Status" data-dropdown-css-class="select2-primary">
									   <option value="">Select Order Status</option>
									   <option value="Pending">Pending</option>
									   <option value="Accepted">Accepted</option>
									   <option value="Prepare">Prepare</option>
									   <option value="Deliver">Deliver</option>
									   <option value="Cancel">Cancel</option>
									   <option value="Complete">Complete</option>
									</select>
								</div> -->
								<div class="form-group col-md-3 col-lg-2 col-sm-6" >
									<select name="order_type" id="order_type" style="width:200px" class="form-control select2" data-placeholder="Select Order Type" data-dropdown-css-class="select2-primary">
									   <option value="">Select Order Type</option>
									   @foreach ($modes as $modes)
									   <option value="{{ $modes->id }}">{{ $modes->name }}</option>
									   @endforeach
									</select>
								</div>
								
								<div class="form-group col-md-3 col-lg-2 col-sm-6" >
                          <button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right: 5px" >Filter</button>
                          <button type="button" name="refresh" id="refresh" class="btn btn-info" ><i class="fas fa-redo-alt"></i> Reset</button></div>
                      </div>
                    </form>

                    <form method="POST" id="received_amount_form" role="form" class="settlement_form">
                    @csrf
                        <div class="row">
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="form-group col-sm-4" style="margin-top:20px;">
                                        <label class="control-label">Total Order Amount (QAR)</label>
                                        <input id="orderTotalCOD" value="{{ $orderTotalCOD }}" class="form-control" readonly/>
                                    </div>
                                    <div class="form-group col-sm-4" style="margin-top:20px;">
                                        <label class="control-label">Total Admin Commission (QAR)</label>
                                        <input id="CashRecevied" value="{{ $CashRecevied }}" class="form-control"  readonly />
                                    </div>
                                    <div class="form-group col-sm-4 mobile_margin" style="margin-top:20px;">
                                        <label class="control-label">Balance Amount (QAR)</label>
                                        <input id="penddingAmount" value="{{ $pendAmount }}" class="form-control"  readonly />
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-5">
                                <div class="received_submit_form hide">
                                    <div class="row">
                                        <div class="form-group col-sm-6" style="margin-top:25px !important;">
                                            <input type="number" name="received_amount" id="received_amount" class="form-control" placeholder="Received Amount (QAR)" data-parsley-type="number" />
                                        </div>
                                        <div class="form-group col-sm-6" style="margin-top:25px !important;">
                                            <button type="submit" name="filter" id="filter2" class="btn btn-primary" style="margin-right:5px">Received Amount</button>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </form>
                    <div class="table-responsive">
                      
                    <table  id="cash_register_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('Order ID') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Amount (QAR)') }}</th>
                                <th>{{ __('Coupons (QAR)') }}</th>
                                <th>{{ __('Admin Received (QAR)') }}</th>
                                <th>{{ __('Admin Commission (%)') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                        <!-- <tfoot>
                            <tr>
                                <th colspan="7" style="text-align:right">Total:</th>
                                <th></th>
                            </tr>
                        </tfoot> -->
                    </table>
                    </div>
                </div>
            </div>
    </div>
</div>

</div>
    <!-- /.content -->


<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
<script>
    function filterTrigger() {
        $('#filter').first().trigger( "click" );
    }
var ajax_datatable;
$(document).ready(function(){
    $('.input-daterange').datepicker({
      todayBtn:'linked',
      format:'yyyy-mm-dd',
      autoclose:true
     });

 $('#received_amount_form').parsley();
 $('.select2').select2();
 
 ajax_datatable = $('#cash_register_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: {    
        url: '{{ url('api/settlement') }}',
        data: function (d) {
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
          d.main_category_id = $('select[name=main_category_id]').val();
          d.restaurant_id = $('select[name=restaurant_id]').val();
          d.order_status = $('select[name=order_status]').val();
          d.order_type = $('select[name=order_type]').val();
        }
      }, 
    columns: [
         /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,"width": "20px"},*/
         { data: 'order_id', name: 'order_id' },
         { data: 'order_type', name: 'order_type' },
         { data: 'order_status', name: 'order_status' },
         { data: 'amount', name: 'amount' },
         { data: 'discount_amount', name: 'discount_amount' },
         { data: 'admin_amount', name: 'admin_amount' },
         { data: 'admin_commission', name: 'admin_commission' },
         { data: 'created_at', name: 'created_at' },
       ],
       order: [ [7, 'desc'] ],
       rowCallback: function(row, data, iDisplayIndex) {
        var order_id='';
        var order_status = '';
        var order_type = '';
        var amount = (data.amount + data.shipping_charges);
        var admin_amount = '';

        if (data.discount_type == 'Flat-Discount') {
          admin_amount +=`QAR ${data.admin_amount - data.discount_amount}`;

        } else {
          admin_amount +=`QAR ${data.admin_amount}`;
        }
        // var admin_amount = data.admin_amount - data.discount_amount;

        order_status += `<button title="Order Status" class="btn btn-primary btn-xs " style="min-width: 90px;" >${data.order_status}</button>`;
        order_type += `<button title="Order Type" class="btn btn-success btn-xs " style="min-width: 90px;" >${data.order_type}</button>`;

        if (data.order_id) {
         order_id +=`ORD#${data.order_id}`;
        }
        

        $('td:eq(1)', row).html(order_type);
        $('td:eq(2)', row).html(order_status);
        $('td:eq(3)', row).html(amount);
        $('td:eq(5)', row).html(admin_amount);
        $('td:eq(0)', row).html(order_id);
        
           
       },
       
    
});

$('#search-form').on('submit', function(e) {
    ajax_datatable.draw();
    e.preventDefault();
    
});

$('#filter').click(function(e){
    var main_category_id = $('select[name=main_category_id]').val();
    var restaurant_id = $('select[name=restaurant_id]').val();
    var order_status = $('select[name=order_status]').val();
    var order_type = $('select[name=order_type]').val();
     var from_date = $('input[name=from_date]').val();
     var to_date = $('input[name=to_date]').val();
     
    $.ajax({
        url:'{{ url('settlement/cash-received') }}'+'?restaurant_id='+restaurant_id+'&main_category_id='+main_category_id+'&from_date='+from_date+'&to_date='+to_date+'&order_status='+order_status+'&order_type='+order_type,
        dataType:'json',
        type:'GET',
        success:function(res){

            if (restaurant_id) {
                $('.received_submit_form').removeClass('hide');
            }
            $('#orderTotalCOD').val(res.orderTotalCOD);
            $('#CashRecevied').val(res.CashRecevied);
            var penddingAmount =  Number(res.orderTotalCOD) - Number(res.CashRecevied);
            $('#penddingAmount').val(penddingAmount);   
        }
    });
});

$('#refresh').click(function(){
  $('#from_date').val('');
  $('#to_date').val('');
  $('#restaurant_id').val('');
  $('#order_type').val('');
  $('#order_status').val('');
  $('#main_category_id').val('');
  ajax_datatable.draw();
  window.location.href = "{{url()->current()}}";
 });

 $("#received_amount_form").on('submit',function(e){
    e.preventDefault();
    var restaurant_id = $('select[name=restaurant_id]').val();
    var restaurant_name =$("#restaurant_id").find("option:selected").text();
    var order_type = $('select[name=order_type]').val();
    //alert(restaurant_name);
    if(restaurant_id){
        var _this=$(this); 
        var amount = $('input[name=received_amount]').val();
        var penddingAmounts = Number($('input[name=penddingAmount]').val());
        // if(!Number(amount)){
        //     toastr.error('Received Amount is Required!!');
        //     return false;
        // }

        var values = $('#received_amount_form').serialize();
        values += '&restaurant_id='+restaurant_id;
        values += '&restaurant_name='+restaurant_name;
        values += '&order_type='+order_type;
        $.ajax({
            url:'{{ url('settlement/saveCashRecevied') }}',
            dataType:'json',
            data:values,
            type:'POST',
            beforeSend: function (){before(_this)},
            complete: function (){complete(_this)},
            success:function(res){
                
                if(res.status === 1){  
                    $('#received_amount_form')[0].reset();
                    $('#received_amount_form').parsley().reset();
                    toastr.success(res.message);
                    ajax_datatable.draw();
                    $('#orderTotalCOD').val(res.data.orderTotalCOD);
                    $('#CashRecevied').val(res.data.CashRecevied);
                    $('#penddingAmount').val(res.data.penddingAmount); 
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
    }else{
        toastr.error('Please Select Restaurant Name!!');
        return false;
    }
 });
});
</script>

@endsection
