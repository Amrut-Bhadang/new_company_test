@extends('layouts.master')

@section('content')
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('backend.Admin_Settings') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.Admin_Settings') }}</li>                
            </ol>
          </div>
    </div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content">
<div class="row">
<!-- Column -->
<!-- Column -->
<!-- Column -->

<div class="col-lg-12 col-xlg-12 col-md-12">
    <div class="card">
        <!-- Nav tabs -->
        <!-- <ul class="nav nav-tabs profile-tab" role="tablist">
            <li class="nav-item"> <a class="nav-link active">Delivery Price</a> </li>
        </ul> -->
        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="settings" role="tabpanel">
            <form method="POST" id="prices_save_form">
            @csrf
             <div class="card-body">
                <div class="row">
                 
                  <div class="col-md-3">
                    <div class="form-group">
                        <label for="common_commission_percentage">{{ __('backend.Common_Commission') }} (%)*</label>
                        <input type="text" placeholder="{{ __('backend.Common_Commission') }} (%)" id="common_commission_percentage" value="{{$deliveryPrice[0]['common_commission_percentage']}}" name="common_commission_percentage" class="form-control form-control-line" data-parsley-required="true" data-parsley-type="digits">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                        <label for="cancellation_charge">{{ __('backend.cancellation_charge') }} (%)*</label>
                        <input type="text" placeholder="{{ __('backend.cancellation_charge') }} (%)" id="cancellation_charge" value="{{$deliveryPrice[0]['cancellation_charge']}}" name="cancellation_charge" class="form-control form-control-line" data-parsley-required="true" data-parsley-type="digits">
                    </div>
                  </div>
                </div>
            
                </div>
                <div class="form-group margin-class margin-left">
                  <label class="col-md-12" for="mobile"></label>
                  <div class="col-md-12">
                    <button type="submit" class="btn btn-success"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>
                  </div>
                </div>
             </div>
             </form>
            </div>
        </div>
    </div>
</div>
<!-- Column -->
</div>
</div>

<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
$(document).ready(function(){
  // initialize();
  // autoload({{$deliveryPrice[0]['latitude']}}, {{$deliveryPrice[0]['longitude']}});
  $('#prices_save_form').parsley();
  $("#prices_save_form").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var values = $('#prices_save_form').serialize();
    $.ajax({
    url:'{{ url('admin/price-save/'.$deliveryPrice[0]['id']) }}',
    dataType:'json',
    data:values,
    type:'POST',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
        if(result.status){
          toastr.success(result.message);
        }else{
          toastr.error(result.message)
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
      return false;   
    });
});
</script>

@endsection
