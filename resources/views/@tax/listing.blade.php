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
        <h4 class="text-themecolor">{{ __('Tax Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Tax Manager') }}</li>
            </ol>
            @can('Tax-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Tax') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add Tax') }}</a>
              <!-- <a href="{{ url('/tax/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a> -->
              @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content tax_sec">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="">
						<form method="POST" id="search-form" class="form-inline-sec" role="form">
							<div class="row">
								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
									<div class="row input-daterange">
										<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
											</div>
										</div>
										<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
											</div>
										</div>
									</div>
								</div>
								<div class="col-xl-3 col-lg-3 col-md-3 col-6 form-group">
								  <select name="country_id" id="country_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Country" data-dropdown-css-class="select2-primary">
									 <option value="">--Select Country--</option>
									 @foreach ($countriesData as $country)
									 <option value="{{ $country->id }}">{{ $country->name }}</option>
									 @endforeach
								  </select>
							  </div>
								<div class="col-xl-3 col-lg-3 col-md-3 col-6">
									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
									<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
								</div>
							</div>
                        </form>
                    <div class="table-responsive">    
						<table id="tax_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<!-- <th>{{ __('Sr. no') }}</th> -->
									<th>{{ __('Country Name') }}</th>
                  <th>{{ __('Currency Code') }}</th>
									<th>{{ __('Tax (In %)') }}</th>
                  <th>{{ __('Difference Amount(From $)') }}</th>
									<th>{{ __('Created At') }}</th>
									<th>{{ __('Action') }}</th>
								</tr>
							</thead>
							<tbody>
							   
							</tbody>
						</table>
					</div>
                    </div>
                </div>
            </div>
    </div>
</div>

</div>
    <!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ url('api/tax') }}" id="add_tax">
      @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add New Tax</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- <ul class="nav nav-tabs">
              @foreach($language as $key => $lang)
              <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#tab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
              @endforeach
          </ul> -->
          <div class="tab-content" style="margin-top:10px">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="tax_id">Country*</label>
                  <select name="country_id" class="form-control"  data-parsley-required="true" >
                    <option value="">---Select Country----</option>
                    @foreach ($countriesData as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="tax_id">Currency*</label>
                  <select name="currency_id" id="currency_id" onchange="changeCurrency()" class="form-control"  data-parsley-required="true" >
                    <option value="">---Select Currency----</option>
                    @foreach ($currencyData as $currency)
                        <option data-currency_code="{{ $currency->currency_code }}" value="{{ $currency->id }}">{{ $currency->currency_code.'('.$currency->currency_name.')' }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="tax_id">Tax (In %)*</label>
                  <input type="text" name="tax" data-parsley-required="true" value="" id="tax" min="0" max="100" class="form-control" placeholder="Tax"  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="tax_id">Difference Amount(1 USD = <span class="converted_currency"></span>)*</label>
                  <input type="text" name="difference_amount" data-parsley-required="true" value="" id="difference_amount" class="form-control" placeholder="Difference Amount"  />
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
          </div>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="editTaxModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Tax</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="edit_tax_response"></div>  
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Tax</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div id="view_response"></div>  
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>


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
});
$('#search-form').on('submit', function(e) {
      ajax_datatable.draw();
        e.preventDefault();
});

function changeCurrency() {
  var currency_code = $('#currency_id').find(':selected').attr('data-currency_code');
  $('.converted_currency').text(currency_code);
  // alert($('#currency_id').data('currency_code'));
}
</script>



<script>
var ajax_datatable;
$(document).ready(function(){
  $('.select2').select2();
$('#add_tax').parsley();
ajax_datatable = $('#tax_listing').DataTable({
    processing: true,
    serverSide: true,

     ajax:{
          url:'{{ url('api/tax') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.country_id = $('select[name=country_id]').val();
          }
        },

    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'name', name: 'name' },
      { data: 'currency_code', name: 'currency_code' },
      { data: 'tax', name: 'tax' },
      { data: 'difference_amount', name: 'difference_amount' },
      { data: 'created_at', name: 'created_at' },
      { data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Tax-edit')
      links += `<a href="#" data-tax_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_btn"><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Tax-edit')
      // links += `<a href="#" data-tax_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      
      links += `</div>`;
      $('td:eq(5)', row).html(links);
      },
});
@can('Tax-create')
$("#add_tax").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/tax') }}',
        dataType:'json',
        data:formData,
        type:'POST',
        cache:false,
        contentType: false,
        processData: false,
        beforeSend: function (){before(_this)},
        // hides the loader after completion of request, whether successfull or failor.
        complete: function (){complete(_this)},
        success:function(res){
              if(res.status === 1){ 
                toastr.success(res.message);
                $('#add_tax')[0].reset();
                $('#previewing').attr('src','images/no-image-available.png');
                $('#add_tax').parsley().reset();
                ajax_datatable.draw();
                location.reload();
              }else{
                toastr.error(res.message);
              }
          },
        error:function(jqXHR,textStatus,textStatus){
          console.log(jqXHR);
          console.log(textStatus);
          /*if(jqXHR.responseJSON.errors){
            $.each(jqXHR.responseJSON.errors, function( index, value ) {
              toastr.error(value)
            });
          }else{
            toastr.error(jqXHR.responseJSON.message)
          }*/
        }
      });
      return false;   
    });
@endcan
@can('Tax-edit')
//Edit staff
$(document).on('click','.edit_btn',function(e){
    e.preventDefault();
    $('#edit_tax_response').empty();
    id = $(this).attr('data-tax_id');

    $.ajax({
       url:'{{url('tax/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_tax_response').html(result);
       } 
    });
    $('#editTaxModal').modal('show');
 });
@endcan

$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-tax_id');
    $.ajax({
       url:'{{url('tax/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });

$("#file").change(function(){
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;
  
    var match = ["image/jpeg","image/png","image/jpg"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#previewing').attr('src','images/no-image-available.png');
      toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
      return false;
    }else{
      //console.log(imageSize);
      if(imageSize < 5000000){
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[0]);
      }else{
        toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
        return false;
      }
      
    }
    
  });
function imageIsLoaded(e){
      //console.log(e);
      $("#file").css("color","green");
      $('#previewing').attr('src',e.target.result);
    }

  });

  
</script>
@endsection
