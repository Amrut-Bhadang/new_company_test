@extends('layouts.master')

@section('content')
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Cash Register Manage') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Cash Register Manage') }}</li>
            </ol>
            @can('Banner-create')
              <!-- <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Banner') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a> -->
              <a href="{{ url('/cash-register/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a> 
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
                    <form method="POST" id="search-form" class="form-inline" role="form">
                        <div class="row">
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
								<div class="row input-daterange">
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
										<div class="form-group">
											<input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
										</div>
									</div>
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
										<div class="form-group" >
											<input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6">							
								<div class="form-group" style="margin-right:5px">
									<select name="driver_name" id="driver_name" class="form-control" >
										<option value="">-- Select Driver --</option>
										<option value="1001">Ravi</option>    
										<option value="1002">Manish</option>  
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-6">
								<div class="form-group" style="margin-right:5px">
									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
									<button type="button" name="refresh" id="refresh" class="btn btn-info" ><i class="fas fa-redo-alt"></i></button>
								</div>
							</div>
						</div>
                    </form>
                    <form method="POST" id="received_amount_form" role="form">
                    @csrf
                        <div class="row">
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="form-group col-sm-4" style="margin-top:20px;">
                                        <label class="control-label">Total Order Amount</label>
                                        <input id="orderTotalCOD" value="{{$orderTotalCOD}}" class="form-control" readonly/>
                                    </div>
                                    <div class="form-group col-sm-4" style="margin-top:20px;">
                                        <label class="control-label">Total Cash Received</label>
                                        <input id="CashRecevied" value="{{$CashRecevied}}" class="form-control"  readonly />
                                    </div>
                                    <div class="form-group col-sm-4" style="margin-top:20px;">
                                        <label class="control-label">Pending Amount</label>
                                        <input id="penddingAmount" value="{{$orderTotalCOD - $CashRecevied}}" class="form-control"  readonly />
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="form-group col-sm-6" style="margin-top:50px;">
                                        <input type="number" name="received_amount" id="received_amount" class="form-control" placeholder="Received Amount" data-parsley-type="number" />
                                    </div>
                                    <div class="form-group col-sm-6" style="margin-top:50px;">
                                        <button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Received Amount</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                      
                    <table  id="cash_register_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('Sr. no') }}</th>
                                <th>{{ __('Order ID') }}</th>
                                <th>{{ __('Driver Name') }}</th>
                                
                                <th>{{ __('Order Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" style="text-align:right">Total:</th>
                                <th></th>
                            </tr>
                        </tfoot>
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
var ajax_datatable;
$(document).ready(function(){
$('.input-daterange').datepicker({
  todayBtn:'linked',
  format:'yyyy-mm-dd',
  autoclose:true
 });

 $('#received_amount_form').parsley();
ajax_datatable = $('#cash_register_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ url('api/cash-register') }}',
        data: function (d) {
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
          d.driver_name = $('select[name=driver_name]').val();
        }
      },
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,"width": "20px"},
      { data: 'order_id', name: 'order_id' },
      { data: 'driver_name', name: 'driver_name' },
      { data: 'orders_date', name: 'orders_date' },
      { data: 'total_amount', name: 'total_amount' },
    ],
    
    rowCallback: function(row, data, iDisplayIndex) {  
      
        
    },
    "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            //console.log(data);
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    //console.log(a);
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(pageTotal);
        }
});

$('#search-form').on('submit', function(e) {
    ajax_datatable.draw();
    e.preventDefault();
    

});

$('#filter').click(function(e){
    var driver_name = $('select[name=driver_name]').val();
    // var from_date = $('input[name=from_date]').val();
    // var to_date = $('input[name=to_date]').val();
//+'&from_date='+from_date+'&to_date='+to_date
    $.ajax({
        url:'{{ url('cash-register/cash-received') }}'+'?driver_name='+driver_name,
        dataType:'json',
        type:'GET',
        success:function(res){
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
  ajax_datatable.draw();
 });

 $("#received_amount_form").on('submit',function(e){
    e.preventDefault();
    var driver_id = $('select[name=driver_name]').val();
    var driver_name =$("#driver_name").find("option:selected").text();
    if(driver_id){
        var _this=$(this); 
        var amount = $('input[name=received_amount]').val();
        var penddingAmounts = Number($('input[name=penddingAmount]').val());
        // if(!Number(amount)){
        //     toastr.error('Received Amount is Required!!');
        //     return false;
        // }

        var values = $('#received_amount_form').serialize();
        values += '&driver_id='+driver_id;
        values += '&driver_name='+driver_name;
        $.ajax({
            url:'{{ url('api/cash-register') }}',
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
        toastr.error('Please Select Driver Name!!');
        return false;
    }
 });
});
</script>

@endsection
