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
        <h4 class="text-themecolor">{{ __('Vendor Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Vendor Manager') }}</li>
            </ol>
            @can('Brand-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Vendor') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add Vendor') }}</a>
              <a href="{{ url('/brands/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
            @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content brand_sec">
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
								  <select name="main_category_id" id="main_category_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Service" data-dropdown-css-class="select2-primary">
									 <option value="">--Select Service--</option>
									 @foreach ($main_category as $mainCategory)
									 <option value="{{ $mainCategory->id }}">{{ $mainCategory->name }}</option>
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
						<table id="brand_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<!-- <th>{{ __('Sr. no') }}</th> -->
									<th>{{ __('Vendor Image') }}</th>
									<th>{{ __('Name') }}</th>
									<th>{{ __('Outlets') }}</th> 
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
    <form method="POST" action="{{ url('api/brands') }}" id="add_brand">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">Add New Vendor</h4>
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
                <label class="control-label" for="brand_id">Service*</label>
                <select name="main_category_id" class="form-control"  data-parsley-required="true" >
                  <option value="">---Select Service----</option>
                  @foreach ($main_category as $cat)
                      <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="brand_id">Vendor Category*</label>
                <select name="brand_category" class="form-control"  data-parsley-required="true" >
                  <option value="">---Select Category----</option>
                  @foreach ($brand_category as $brand_cat)
                      <option value="{{ $brand_cat->id }}">{{ $brand_cat->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          @if($lang)
          <div class="row">
            @foreach($language as  $key => $lang)
              <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
                
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="name">{{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
                        <input type="text" name="name[{{$lang}}]" data-parsley-required="true" id="name" class="form-control" placeholder="Name"  />
                      </div>
                    </div>
                
              <!-- </div> -->
            @endforeach
            </div>
          @endif
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
            <label class="control-label" for="mobile">Mobile*</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                <select name="country_code" class="form-control" style="width:180px" data-parsley-required="true" >
                  @foreach ($country as $country)
                      <option value="{{ $country->phonecode }}">{{ $country->name }} ({{ $country->phonecode }})</option>
                  @endforeach
                </select>
                </div>
                <input type="text" name="mobile"  value="" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="8" data-parsley-maxlength="15"/>
              </div> 
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="email">Email*</label>
              <input type="text" name="email" value="" id="email" class="form-control" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="password">Password*</label>
              <input type="password" name="password" value="" id="password" class="form-control" placeholder="Password" autocomplete="off" data-parsley-required="true"  />
              <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleNewPassword"></i>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="confirm_password">Confirm Password*</label>
              <input type="password" name="confirm_password" value="" id="confirm_password" class="form-control" placeholder="Confirm Password" data-parsley-required="true"  />
              <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleConfirmPassword"></i>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="brand_name">Brand Name *</label>
              <input type="text" name="brand_name" value="" id="brand_name" class="form-control" placeholder="Brand Name" data-parsley-required="true"  />
            </div>
          </div> -->
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="brand_type">Brand Type *</label>
              <select name="brand_type" class="form-control" data-placeholder="Select brand type" style="width: 100%;" data-parsley-required="true" >
                <option>--Select Brand Type--</option>
                <option value="Restaurant">Restaurant</option>
                <option value="Gift">Gift</option>
              </select>
            </div>
          </div> -->
        
        <div class="col-md-6">
            <label for="image">Image</label>
          <div class="form-group">
            <div class="input-group">
              <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
              <input type="file" id="file" name="image" class="form-control">
            </div>
            <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
          </div>
        </div>
      </div>
            
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
      </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="editModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Vendor</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div id="edit_brand_response"></div>  
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
        <h4 class="modal-title">View Vendor</h4>
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
</script>



<script>
var ajax_datatable;
$(document).ready(function(){
  const toggleNewPassword = document.querySelector('#toggleNewPassword');
  const newPassword = document.querySelector('#password');

  const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
  const confirm_password = document.querySelector('#confirm_password');

  toggleNewPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    newPassword.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  toggleConfirmPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
    confirm_password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });
  $('.select2').select2();
  $('#add_brand').parsley();
ajax_datatable = $('#brand_listing').DataTable({
    processing: true,
    serverSide: true,

     ajax:{
          url:'{{ url('api/brands') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.main_category_id = $('select[name=main_category_id]').val();
          }
        },

    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'file_path', name: 'file_path', orderable: false, searchable: false },
      { data: 'name', name: 'name' },
      { data: 'total_outlet', name: 'total_outlet', orderable: false, searchable: false },
      { data: 'created_at', name: 'created_at' },
      { data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [3, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Brand-edit')
      links += `<a href="#" data-brand_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_btn" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Brand-edit')
      links += `<a href="#" data-brand_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      links += `<a href="{{url('brands/restaurant')}}/${data.id}" title="View Store" class="btn btn-warning btn-xs" ><span class="fas fa-bread-slice"></span></a>`;
      @endcan
      
      links += `</div>`;
      var image = '';

      image +=`<img width="100" height="100" src="${data.file_path}">`;
      $('td:eq(0)', row).html(image);
      $('td:eq(4)', row).html(links);
      },
});
@can('Brand-create')
$("#add_brand").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/brands') }}',
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
                $('#add_brand')[0].reset();
                $('#previewing').attr('src','images/no-image-available.png');
                $('#add_brand').parsley().reset();
                ajax_datatable.draw();
                location.reload();
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
      return false;   
    });
@endcan
@can('Brand-edit')
//Edit staff
$(document).on('click','.edit_btn',function(e){
    e.preventDefault();
    $('#edit_brand_response').empty();
    id = $(this).attr('data-brand_id');
    $.ajax({
       url:'{{url('brands/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_brand_response').html(result);
        
       } 
    });
    $('#editModal').modal('show');
 });
@endcan

$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-brand_id');
    $.ajax({
       url:'{{url('brands/view')}}/'+id,
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
