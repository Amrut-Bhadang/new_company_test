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
        <a href="{{ url('restaurant') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Transaction List') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Transaction List') }}</li>
            </ol>
            <a href="{{ url('/restaurant/exportTransUsers/'.$id) }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
      <!-- <div class="row">
        <div class="col-md-6" style="margin-bottom: 10px;">
           <a href="{{ url('restaurant') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
        </div>
      </div> -->
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="restaurant_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('Order Id') }}</th>
                                <th>{{ __('Username') }}</th>
                                <th>{{ __('Mobile No.') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('KP') }}</th>
                                <th>{{ __('Order Status') }}</th>
                                <th>{{ __('Created At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction as $key => $value)
                            <tr>
                              <td>ORD# {{ $value->id}}</td>
                              <td>{{ $value->name ?? 'Name Not Available'}}</td>
                              <td>{{ $value->country_code.' '.$value->mobile }}</td>
                              <td>QAR {{ $value->amount}}</td>
                              <td>{{ $value->total_order_kp }}</td>
                              <td><a href="#" class="change_status"><span class='label label-rounded label-success'>{{ $value->order_status}}</span></a></td>
                              <td>{{date('j F, Y', strtotime($value->created_at))}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="4">Total KP</th>
                            <th class="ordered_kp" colspan="3">{{ $totalOrderKp }}</th>
                          </tr>
                        </tfoot>
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
    $('#restaurant_listing').DataTable();
} );
</script>
<!-- <script>
var ajax_datatable;
$(document).ready(function(){
$('#add_restaurant').parsley();
$('.select2').select2();
ajax_datatable = $('#restaurant_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ url('restaurant/menu') }}',
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'products_type', name: 'products_type' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {     
      //var links='';
      //var status = '';
      //links += `<div class="btn-group" role="group" >`;
      /*@can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/edit') }}/${data.id}" data-restaurant_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_staff" ><span class="fa fa-edit"></span></a>`;
      @endcan*/
      /*@can('Restaurant-delete')
      links += `<a href="#" data-restaurant_id="${data.id}" title="Delete staff" class="btn btn-danger btn-xs delete_staff" ><span class="fa fa-trash"></span></a>`;
      @endcan*/
      /*@can('Restaurant-edit')
      links += `<a href="#" data-restaurant_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan*/
      /*@can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/menu') }}/${data.id}" data-restaurant_id="${data.id}" title="View Menues" class="btn btn-success btn-xs view_menu" ><span class="fa fa-link"></span></a>`;
      @endcan*/ 
      /*links += `</div>`;
      if(data.status === 1){
        status += `<a href="#" data-restaurant_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-restaurant_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }*/
      //$('td:eq(4)', row).html(status);
      //$('td:eq(6)', row).html(links);
      },
});
</script> -->

@endsection
