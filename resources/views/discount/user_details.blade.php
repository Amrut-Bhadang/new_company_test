@extends('layouts.master')

@section('content')
@if($user_details)
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <div class="d-flex align-items-center">
          <a href="{{ url('discount') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
          <h4 class="text-themecolor">{{ __('backend.user_details') }}</h4>
        </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.user_details') }}</li>
            </ol>
            <a href="{{ url('/discount/exportAppliedUsers/'.$discount_id) }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="meditation_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('backend.sr_no') }}</th>
                                <th>{{ __('backend.user') }}</th>
                                <th>{{ __('backend.email') }}</th>
                                <th>{{ __('backend.discount_percent') }}</th>  
                                <th>{{ __('backend.order_id') }}</th>    
                                <th>{{ __('backend.order_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @if(count($user_details))
                           @foreach($user_details as $key => $detail)
                             <tr>
                               <td>{{ $key+1 }}</td>
                               <td>
                                @if($detail->name  === '')
                                    No Name
                                @else
                                    {{$detail->name}}
                                @endif
                               </td>
                               <td>{{ $detail->email ?? 'No Email'}}</td>
                               <td>{{ $detail->discount_percent }}</td>
                               <td>ORD#{{ $detail->random_order_id ? $detail->random_order_id : $detail->id }}</td>
                               <td>{{date('j F, Y', strtotime($detail->created_at)) }}</td>
                             </tr>
                           @endforeach
                          @else
                            <tr>
                             <td colspan="6" style="text-align: center;">No Record Found</td>
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

<!-- <script>
	
var ajax_datatable;
$(document).ready(function(){
$('#add_category').parsley();
ajax_datatable = $('#meditation_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ url('gift_plans/user_details') }}',
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
@endif
@endsection