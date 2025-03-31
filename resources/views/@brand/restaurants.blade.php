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
        <a href="{{ url('brands') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Stores List') }}</h4>
      </div>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Brand Manager') }}</li>
            </ol>
            <!-- @can('Brand-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Brand') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a>
            @endcan -->
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
                    <div class="table-responsive">
                    <table  id="restro_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <!-- <th>{{ __('Sr. no') }}</th> -->
                                <th>{{ __('Store Image') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Mobile') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('Total Income') }}</th>
                                <th>{{ __('Main Store') }}</th>
                                <th>{{ __('Created At') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                          <?php if(count($restaurants)) { ?>

                            <?php foreach ($restaurants as $key => $value) { ?>
                              <tr>
                                <!-- <td>{{$key+1}}</td> -->
                                <td><img src="{{$value->file_path}}" alt="user" width="100" height="100"></td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->email}}</td>
                                <td>{{$value->country_code.' '.$value->phone_number}}</td>
                                <td>{{$value->address}}</td>
                                <td>{{$value->total_income}}</td>
                                <td>
                                  <?php if ($value->is_main_branch == 1) { ?>
                                    <a href="#" data-restaurant_id="{{$value->id}}" title="Main Store" data-status="No" class="change_status"><span class='label label-rounded label-success'>Yes</span></a>

                                  <?php } else { ?>
                                    <a href="#" data-restaurant_id="{{$value->id}}" title="Not Main Store" data-status="Yes" class="change_status"><span class='label label-rounded label-warning'>No</span></a>
                                  <?php } ?>
                                </td>
                                <td>{{ date('d/m/Y h:i:s', strtotime($value->created_at)) }}</td>
                              </tr>
                            <?php } ?>
                          <?php } else { ?>
                            <tr>
                              <td style="text-align: center;" colspan="6">No record found!</td>
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
  $(document).on('click','.change_status',function(e){
    e.preventDefault();
    status = $(this).data('status');
    if(status == 'Yes'){
      var response = confirm('Are you sure want to make this store as main store?');
    }else{
      var response = confirm('Are you sure want to remove this store as main store?');
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
</script>
@endsection
