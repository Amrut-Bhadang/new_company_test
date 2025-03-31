@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <div class="d-flex align-items-center">
        <a href="{{ url('admin/users') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('backend.view_address_details') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.view_address_details') }}</li>
            </ol>
            
        </div>
    </div>
</div>
<div class="content">
    <!-- <div class="row">
        <div class="col-md-6" style="margin-bottom: 10px;">
           <a href="{{ url('admin/users') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        </div>
    </div> -->
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="order_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('backend.customer_name') }}</th>
                                <th>{{ __('backend.address_type') }}</th>
                                <th>{{ __('backend.zone_name') }}</th>
                                <th>{{ __('backend.address') }}</th>
                                <th>{{ __('backend.latitude') }}</th>
                                <th>{{ __('backend.longitude') }}</th>
                                <th>{{ __('backend.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @if(count($address))
                           @foreach($address as $key => $detail)
                             <tr>
                               <td>{{ $detail->customer_name ?? 'No Name'}}</td>
                               <td>{{ $detail->address_type }}</td>
                               <td>{{ $detail->landmark ?? 'No Zone'}}</td>
                               <td>{{ $detail->address }}</td>
                               <td>{{ $detail->latitude }}</td>
                               <td>{{ $detail->longitude }}</td>
                               <td>{{ $detail->created_at }}</td>
                             </tr>
                           @endforeach
                          @else
                            <tr>
                             <td colspan="8" style="text-align: center;">No Record Found</td>
                            </tr>
                          @endif 
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
    </div>
</div>

</div>


<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

<script type="text/javascript">
  $(document).ready( function () {
    $('#order_listing').DataTable();
} );
</script>
<!-- <script>
	
var ajax_datatable;
$(document).ready(function(){
$('#add_category').parsley();
ajax_datatable = $('#meditation_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ url('admin/gift_plans/user_details') }}',
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'price', name: 'price' },
      { data: 'transaction_id', name: 'transaction_id' },
      { data: 'months', name: 'months' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [5, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
    },
});
</script> -->

@endsection