@extends('layouts.master')

@section('content')
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Chef Manage') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Chef Manage') }}</li>
            </ol>
            @can('Chef-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Chef') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a>
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
                    <div class="table-responsive">
                    <table  id="chef_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('Sr. no') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Mobile') }}</th>
                                <!-- <th>{{ __('Gender') }}</th> -->
                                <th>{{ __('Status') }}</th>  
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
    <!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
          <form method="POST" action="{{ url('api/chef') }}" id="add_chef">
          @csrf
            <div class="modal-header">
              <h4 class="modal-title">Add New Chef</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="first_name">First Name *</label>
                        <input type="text" name="first_name" value="" id="first_name" class="form-control" placeholder="First Name" data-parsley-required="true"  />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="last_name">Last Name *</label>
                        <input type="text" name="last_name" value="" id="last_name" class="form-control" placeholder="Last Name" data-parsley-required="true"  />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="email">Email *</label>
                        <input type="text" name="email" value="" id="email" class="form-control" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label class="control-label" for="mobile">Mobile *</label>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                          <select name="country_code" class="form-control" style="width:180px" data-parsley-required="true" >
                            @foreach ($country as $country)
                                <option value="{{ $country->phonecode }}">{{ $country->name }} ({{ $country->phonecode }})</option>
                            @endforeach
                          </select>
                          </div>
                          <input type="text" name="mobile" value="" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="10" data-parsley-maxlength="10"/>
                        </div> 
                      </div>
                    </div>
                    
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="password">Password*</label>
                        <input type="password" name="password" value="" id="password" class="form-control" placeholder="Password" data-parsley-required="true"  />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="confirm_password">Confirm password*</label>
                        <input type="password" name="confirm_password" value="" id="confirm_password" class="form-control" placeholder="Confirm password" data-parsley-required="true"  />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="food_license" style="display:block">Do you have food license</label>
                        <div class="custom-control custom-radio" style="display:inline-block">
                            <input type="radio" class="custom-control-input licenseNumber" id='customRadio1' name="food_license" value="Yes" />
                            <label class="custom-control-label" for="customRadio1">Yes</label>
                        </div>
                        <div class="custom-control custom-radio" style="display:inline-block">
                            <input type="radio" class="custom-control-input licenseNumber" id='customRadio2' name="food_license" value="No" checked/> 
                            <label class="custom-control-label" for="customRadio2">No</label>
                        </div>
                                                
                        
                      </div>
                    </div>
                    <div class="col-md-6 licenseNumberDiv">
                      <label class="control-label" for="license_no">License No.</label>
                      <input type="text" name="license_no" value="" id="license_no" class="form-control" placeholder="License No." />
                    </div>
                    
                    
                  </div> 
                  <div class="row">
                    <div class="col-md-6 licenseNumberDiv">
                      <label class="control-label" for="license_no">License Images</label>
                      <input type="file" id="file" name="image" class="form-control" >
                      <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/image.png')}}"></div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="address">Address </label>
                        <input type="text" name="address" id="address" onfocus="geolocate()" class="form-control" placeholder="Address" />
                        <input type="hidden" class="latitude" id='latitude' name="latitude" />
                        <input type="hidden" class="longitude" id='longitude' name="longitude" />
                        
                      </div>
                    </div>
                    <style>
                          #map_canvas {
                              width: 100%;
                              height: 300px;
                            }
                            /* Optional: Makes the sample page fill the window. */
                            html, body {
                              height: 100%;
                              margin: 0;
                              padding: 0;
                            }                                                
                      </style>
                      <div class="col-md-12">
                        <div class="form-group">
                          <div id="map_canvas"></div>
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
              <h4 class="modal-title">Edit Chef</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="edit_chef_response"></div>  
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
              <h4 class="modal-title">View Chef</h4>
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

      <div class="modal fade" id="productModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">View Dish </h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="view_product_response"></div>  
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
<!-- /Modals -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_chef').parsley();
$('.select2').select2();
ajax_datatable = $('#chef_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ url('api/chef') }}',
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'email', name: 'email' },
      { data: 'mobile', name: 'mobile' },
      // { data: 'gender', name: 'gender' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [5, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Chef-edit')
      links += `<a href="#" data-chef_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_chef" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Chef-delete')
      //links += `<a href="#" data-chef_id="${data.id}" title="Delete chef" class="btn btn-danger btn-xs delete_chef" ><span class="fa fa-trash"></span></a>`;
      @endcan
      links += `<a href="#" data-chef_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      links += `<a href="{{ url('chef-staff-list') }}/${data.id}" title="View Chef List" class="btn btn-info btn-xs view_list" ><span class="fa fa-list-alt"></span></a>`;
      links += `<a href="{{ url('orders/1') }}/${data.id}" title="View Orders" class="btn btn-info btn-xs" ><span class="fas fa-shopping-cart"></span></a>`;
      
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-chef_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-chef_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
      $('td:eq(4)', row).html(status);
      $('td:eq(6)', row).html(links);
      },
});

$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this celebrity acoount?');
      }else{
        var response = confirm('Are you sure want to deactive this celebrity acoount?');
      };
      if(response){
        id = $(this).data('chef_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('chef/changeStatus' )!!}" + "/" + id +'/'+status,
          success:function(res){
            if(res.status === 1){ 
              toastr.success(res.message);
              ajax_datatable.draw();
                  location.reload()
            }else{
              toastr.error(res.message);
            }
          },   
          error:function(jqXHR,textStatus,textStatus){
            console.log(jqXHR);
            toastr.error(jqXHR.statusText)
          }
      });
      }
      return false;
    }); 

@can('Chef-create')
$("#add_chef").on('submit',function(e){
  e.preventDefault();
     var _this=$(this); 
   
    var formData = new FormData(this);
    $.ajax({
    url:'{{ url('api/chef') }}',
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
          $('#add_chef')[0].reset();
          $('#add_chef').parsley().reset();
          $('#previewing').attr('src',"{{ URL::asset('images/image.png')}}");
          $('.previewing').html('');
          ajax_datatable.draw();
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
@can('Chef-edit')
//Edit staff
$(document).on('click','.edit_chef',function(e){
    e.preventDefault();
    $('#edit_chef_response').empty();
    id = $(this).attr('data-chef_id');
    $.ajax({
       url:'{{url('chef/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_chef_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Chef-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-chef_id');
    $.ajax({
       url:'{{url('chef/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan

@can('Chef-delete')
$(document).on('click','.delete_chef',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this chef?');
      if(response){
        id = $(this).data('chef_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('api/chef' )!!}" + "/" + id,
          success:function(){
            toastr.success('{{ __('Chef is deleted successfully') }}');
            ajax_datatable.draw();
          },   
          error:function(jqXHR,textStatus,textStatus){
            console.log(jqXHR);
            toastr.error(jqXHR.statusText)
          }
      });
      }
      return false;
    }); 
@endcan

$("#file").change(function(){
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;
  
    var match = ["image/jpeg","image/png","image/jpg"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#previewing').attr('src','images/image.png');
      toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
      return false;
    }else{
      //console.log(imageSize);
      if(imageSize < 1000000){
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[0]);
      }else{
        toastr.error('Images Size Too large Please Select 1MB File!!');
        return false;
      }
      
    }
    
});
let value = $("input[name='food_license']:checked").val();
if(value == 'No'){
  $(".licenseNumberDiv").hide();
}
$(".licenseNumber").change(function(){
  let value = $("input[name='food_license']:checked").val();
  $(".licenseNumberDiv").hide();
  if(value !== 'No'){
    $(".licenseNumberDiv").show();
  }else{
    $(".licenseNumberDiv").hide();
  }
 })
});

  function imageIsLoaded(e){
			//console.log(e);
			$("#file").css("color","green");
			$('#previewing').attr('src',e.target.result);

		}
</script>

@endsection
