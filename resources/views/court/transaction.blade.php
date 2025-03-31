@extends('layouts.master')

@section('content')

<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();
// dd($transaction);
?>
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <div class="d-flex align-items-center">
        <a href="{{ url('admin/users') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Transaction List') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Transaction List') }}</li>
            </ol>
            <!-- <a href="{{ url('admin/users/exportTransUsers/'.$id) }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a> -->
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
      <!-- <div class="row">
        <div class="col-md-6" style="margin-bottom: 10px;">
           <a href="{{ url('admin/users') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
        </div>
      </div> -->
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="">
                      <form method="GET" action="{{url()->current()}}" id="search-form" class="form-inline" role="form" style="margin-top:15px;">
                            <!-- <div class="col-xl-2 col-lg-6 col-md-6 col-sm-6"> -->
                              <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                  <div class="form-group">
                                    <input type="text" name="from_price" id="from_price" value="<?php echo isset($_REQUEST['from_price']) ? $_REQUEST['from_price'] : '' ?>" data-parsley-type="digits" class="form-control" placeholder="From Price" />
                                  </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                  <div class="form-group">
                                    <input type="text" name="to_price" id="to_price" value="<?php echo isset($_REQUEST['to_price']) ? $_REQUEST['to_price'] : '' ?>" data-parsley-type="digits" class="form-control" placeholder="To Price" />
                                  </div>
                                </div>
                              </div>
                            <!-- </div> -->
                            <button type="submit" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
                            <a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
                      </form>
					<div class="table-responsive">
						<table id="transaction_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>{{ __('Order Id') }}</th>
                  <th>{{ __('Type Of Service') }}</th>
                  <th>{{ __('Store Name') }}</th>
                  <th>{{ __('Total KP') }}</th>
									<th>{{ __('KP Status') }}</th>
									<th>{{ __('Amount') }}</th>
									<th>{{ __('Order Status') }}</th>
									<th>{{ __('Created At') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($transaction as $key => $value)
								<tr>
                  <td>ORD#{{ $value->random_order_id ? $value->random_order_id : $value->id}}</td>
								  <!-- <td>ORD# {{ $value->id}}</td> -->
								  <td>{{ $value->service_type}}</td>
                  <td>{{ $value->store_name}}</td>
                  <td>{{ $value->total_order_kp}}</td>
                  <td>{{ ($value->is_kp_transfer == 'Yes' && $value->order_status == 'Complete') ? 'Transferred' : 'Not Transferred'}}</td>
								  <td>QAR {{ $value->amount}}</td>
								  <td><a href="#" class="change_status"><span class='label label-rounded label-<?php if ($value->order_status == 'Cancel') { echo "danger"; } else if ($value->order_status == 'Pending') { echo "warning"; } else { echo "success"; } ?>'>{{ $value->order_status}}</span></a></td>
								  <!-- <td>{{date('j F, Y', strtotime($value->created_at))}}</td> -->
                  <td>{{ $value->created_at }}</td>
								</tr>
								@endforeach
							</tbody>
              <tfoot>
                <tr>
                  <th colspan="4">Total</th>
                  <th class="wallet_amount" colspan="3">{{ number_format($total->totalAvlAmount,2) }} QAR</th>
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
    

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
  $(document).ready( function () {
    $('#transaction_listing').DataTable({
      /*processing: true,
      serverSide: true,

      ajax:{
          url:'{{ url('admin/users/transaction') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              //alert(d.from_date);
          }
        },*/
        order: [ [7, 'desc'] ]
      });
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


</script>


@endsection
