@extends('layouts.master')

@section('content')

<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();

?>
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <div class="d-flex align-items-center">
        <a href="{{ url('gift_brand') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Brand Gifts') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Brand Gifts') }}</li>
            </ol>
            <!-- @can('Brand-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Brand') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a>
              @endcan -->
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-md-6" style="margin-bottom: 10px;">
       <a href="{{ url('gift_brand') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
    </div>
</div> -->
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="gifts_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('Sr. no') }}</th>
                                <th>{{ __('Gift Image') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Points') }}</th>
                                <th>{{ __('Created At') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                          <?php if ($gift) { ?>

                            <?php foreach ($gift as $key => $value) { ?>
                              <tr>
                                <td>{{$key+1}}</td>
                                <td><img src="{{$value->image}}" alt="user" width="100" height="100"></td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->quantity}}</td>
                                <td>{{$value->points}}</td>
                                <td>{{ date('j F,Y', strtotime($value->created_at)) }}</td>
                              </tr>
                            <?php } ?>
                          <?php } else { ?>
                            <tr>
                              <td style="text-align: center;" colspan="5">No record found!</td>
                            </tr>
                          <?php } ?>
                        </tbody>
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
<script type="text/javascript">
  $(document).ready( function () {
    $('#gifts_listing').DataTable();
} );
</script>
<!-- <script type="text/javascript">
  $(document).on('click','.change_status',function(e){
    e.preventDefault();
    status = $(this).data('status');
    if(status == 'Yes'){
      var response = confirm('Are you sure want to make this restaurant as main restaurant?');
    }else{
      var response = confirm('Are you sure want to remove this restaurant as main restaurant?');
    }
    if (response){
      id = $(this).data('restaurant_id');
      $.ajax({
        type: 'post',
        data: {_method: 'get', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('restaurant/makeMainRestro' )!!}" + "/" + id +'/'+status,
        success:function(res) {

          if (res.status === 1) { 
            toastr.success(res.message);
            window.location.reload();

          } else {
            toastr.error(res.message);
          }
        },   
        error:function(jqXHR,textStatus,textStatus){
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
  }); 
</script> -->
@endsection
