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
        <h4 class="text-themecolor">{{ __('Store Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Store Manager') }}</li>
            </ol>
            @can('Restaurant-create')
              <!-- <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Staff') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a> --> 
              <a href="{{ url('restaurant/create') }}" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Store') }}" ><i class="fa fa-plus"></i> {{ __('Add Store') }}</a>
              <a href="{{ url('/restaurant/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
              <!-- <a href="" class="btn btn-info d-none d-lg-block m-l-15 import_btn" title="{{ __('Import') }}"><i class="fa fa-upload"></i> {{ __('Import') }}</a> -->
            @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content restaurant_sec">
      <div class="row">
        <div class="col-md-12">
    			<div class="card card-primary card-outline">
            <div class="card-body">
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
    							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 mobile_cls">
    								<div class="form-group">
    									<select name="main_category_id" id="main_category_id" class="form-control select2" onchange="getBrands()" data-placeholder="Select Service" data-dropdown-css-class="select2-primary">
    									   <option value="">--Select Service--</option>
    									   @foreach ($main_category as $mainCategory)
    									   <option <?php echo (isset($_GET['mainCatId']) && $_GET['mainCatId'] == $mainCategory->id) ? 'selected' : ''; ?> value="{{ $mainCategory->id }}">{{ $mainCategory->name }}</option>
    									   @endforeach
    									</select>
    								</div>
    							</div>
                  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 mobile_cls show_brandDiv">
                    <div class="form-group">
                      <select name="brand_id" id="brand_id" class="form-control select2" data-placeholder="Select Vendor" data-dropdown-css-class="select2-primary">
                         <option value="">--Select Vendor--</option>
                      </select>
                    </div>
                  </div>
    							<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
    								<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
    								<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
    							</div>
    						</div>
    					</form>
    				  <div class="table-responsive">
    						<table id="restaurant_listing" class="table table-striped table-bordered" style="width:100%">
    							<thead>
    								<tr>
    									<!-- <th>{{ __('Sr. no') }}</th> -->
    									<th>{{ __('Name') }}</th>
    									<th>{{ __('Email') }}</th>
    									<th>{{ __('Modes') }}</th>
    									<th>{{ __('Mobile') }}</th>
    									<th>{{ __('Status') }}</th>  
    									<th>{{ __('Income') }}</th>  
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
   
<div class="modal fade" id="editModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit Store</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="edit_staff_response"></div>  
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
              <h4 class="modal-title">View Store</h4>
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

      <div class="modal fade" id="setTimeModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">View Store Service Time</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="setTime_response"></div>  
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

      <div class="modal fade" id="imagesModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Store Images</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body image_preview_modal">
            <form method="Put" id="add_images">
            @csrf
            <input type='hidden' id="restaurant_id" class="form-control" name="restaurant_id">
                <div class="col-md-12">
                  <div class="form-group">
                      <label class="control-label" for="name">Add More Other Images</label>
                      <input type='file' id="addMoremultipalImage" class="form-control" name="addMoremultipalImage[]" multiple>
                      <div class="previewing"></div>
                  </div>
                  <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
                </div>
              </form>
                  <div class="images_content_response"></div>  
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

      <div class="modal fade" id="importModal">
        <div class="modal-dialog modal-lg" >
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Import Excel</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ url('restaurant/importData') }}" id="import_dish_data" enctype="multipart/form-data">
                @csrf
                <div class="tab-content" style="margin-top:10px"></div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="col-md-6" for="file">Import Excel File</label>
                      <input type="file" name="file" class="form-control">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group mt-btn">
                       <a href="{{url('public/uploads/Dish_import_sample.xlsx')}}" class="btn btn-success btn-lg" title="Demo Download"><i class="fas fa-file-excel" aria-hidden="true"></i> Sample File</a>
                    </div>
                  </div>
                </div>
                <hr style="margin: 1em -15px">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<!-- /Modals -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- Select2 -->
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

function getBrands() {
  var main_category_id = $('#main_category_id').val();

  $.ajax({
     url:'{{url('restaurant/show_brands_for_list')}}/'+main_category_id,
     dataType: 'html',
     success:function(result)
     {
        $('.show_brandDiv').html(result);
     }
  });
}
</script>



<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_restaurant').parsley();
$('.select2').select2();
ajax_datatable = $('#restaurant_listing').DataTable({
    processing: true,
    serverSide: true,

     ajax:{
          url:'{{ url('api/restaurant') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.main_category_id = $('select[name=main_category_id]').val();
              d.brand_id = $('select[name=brand_id]').val();
          }
        },


    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'name', name: 'name' },
      { data: 'email', name: 'email' },
      { data: 'mode_name', name: 'mode_name', orderable: false , searchable: false},
      { data: 'phone_number', name: 'phone_number' },
      { data: 'status', name: 'status' },
      { data: 'total_income', name: 'total_income', orderable: false , searchable: false},
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [6, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {
      console.log(data);
      var links='';
      var status = '';
      links += `<div class="btn-group" role="group" >`;
      @can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/edit') }}/${data.id}" data-restaurant_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Restaurant-delete')
      //links += `<a href="#" data-restaurant_id="${data.id}" title="Delete staff" class="btn btn-danger btn-xs delete_staff" ><span class="fa fa-trash"></span></a>`;
      @endcan
      @can('Restaurant-edit')
      links += `<a href="#" data-restaurant_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      links += `<a href="#" data-restaurant_id="${data.id}" title="Store Images" class="btn btn-danger btn-xs images_btn" ><span class="fa fa-image"></span></a>`;
      links += `<a href="{{ url('restaurant/tables') }}/${data.id}" data-restaurant_id="${data.id}" title="Store Tables" class="btn btn-secondary btn-xs" ><span class="fas fa-chair"></span></a>`;
      @endcan
      @can('Restaurant-edit')
      links += `<a href="#" data-restaurant_id="${data.id}" title="Set Service Timings" class="btn btn-warning btn-xs set_time" ><span class="fa fa-clock"></span></a>`;
      @endcan
      @can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/menu') }}/${data.id}" data-restaurant_id="${data.id}" title="View Items" class="btn btn-success btn-xs view_menu" ><span class="fa fa-utensils"></span></a>`; 
      @endcan 
      @can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/transaction') }}/${data.id}" data-restaurant_id="${data.id}" title="View Transaction" class="btn btn-dark btn-xs view_menu" ><span class="fa fa-credit-card"></span></a>`;
      links += `<a href="{{url('permissions/user_permissions').'/'}}${data.user_id}" title="Set permissions" class="btn btn-warning btn-xs" ><span class="fa fa-key"></span></a>`;
      @endcan
      /*@can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/deliver_order') }}/${data.id}" data-restaurant_id="${data.id}" title="View deliver orders" class="btn btn-danger btn-xs view_deliver_order" ><span class="fa fa-info"></span></a>`;
      @endcan*/
      links += `</div>`;
      if(data.status === 1){
        status += `<a href="#" data-restaurant_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-restaurant_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
      $('td:eq(4)', row).html(status);
      $('td:eq(7)', row).html(links);
      },
});



$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this store account?');
      }else{
        var response = confirm('Are you sure want to deactive this store account?');
      }
      if (response){
        id = $(this).data('restaurant_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('restaurant/changeStatus' )!!}" + "/" + id +'/'+status,
          success:function(res){
            if(res.status === 1){ 
              toastr.success(res.message);
              ajax_datatable.draw();
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

/*$(document).on('submit', "#add_restaurant",function(e){
  e.preventDefault();
  var _this=$(this); 
    $('#group_loader').fadeIn();
    var formData = new  FormData(this);
    $.ajax({
    url:'{{ url('api/restaurant') }}',
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
            $('#add_restaurant')[0].reset();
            $('#add_restaurant').parsley().reset();
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
    });*/
@can('Restaurant-edit')
//Edit staff
$(document).on('click','.edit_staff',function(e){
    e.preventDefault();
    $('#edit_staff_response').empty();
    id = $(this).attr('data-restaurant_id');
    $.ajax({
       url:'{{url('restaurant/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_staff_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Restaurant-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-restaurant_id');
    $.ajax({
       url:'{{url('restaurant/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan

$(document).on('click','.import_btn',function(e){
    e.preventDefault();
    $('#import_response').empty();
    $.ajax({
       url:'{{url('restaurant/import')}}',
       dataType: 'html',
       success:function(result)
       {
        $('#import_response').html(result);
        $('.select3').select2();
       } 
    });
    $('#importModal').modal('show');
 });

/*show time service*/
@can('Restaurant-edit')
//Edit staff
$(document).on('click','.set_time',function(e){
    e.preventDefault();
    $('#setTime_response').empty();
    id = $(this).attr('data-restaurant_id');
    $.ajax({
       url:'{{url('restaurant/set_time')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#setTime_response').html(result);
       } 
    });
    $('#setTimeModal').modal('show');
 });
@endcan
/*show time service end*/

@can('Restaurant-delete')
$(document).on('click','.delete_staff',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this staff?');
      if(response){
        id = $(this).data('restaurant_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('api/restaurant' )!!}" + "/" + id,
          success:function(){
            toastr.success('{{ __('Store is deleted successfully') }}');
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

  $("#file2").change(function(){
      var fileObj = this.files[0];
      var imageFileType = fileObj.type;
      var imageSize = fileObj.size;
    
      var match = ["image/jpeg","image/png","image/jpg"];
      if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
        $('#previewing2').attr('src','images/image.png');
        toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
        return false;
      }else{
        //console.log(imageSize);
        if(imageSize < 5000000){
          var reader = new FileReader();
          reader.onload = imageIsLoaded2;
          reader.readAsDataURL(this.files[0]);
        }else{
          toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
          return false;
        } 
      }    
  });
  $("#add_images").on('submit',function(e){ 
    e.preventDefault();
    var _this=$(this); 

      var formData = new FormData(this);
      formData.append('_method', 'put');
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      image_id = $('#restaurant_id').val();

      $.ajax({
      url:'{{ url('restaurant/add-more-images') }}/'+image_id,
      dataType:'json',
      data:formData,
      type:'POST',
      cache:false,
      contentType: false,
      processData: false,
      beforeSend: function (){before(_this)},
      // hides the loader after completion of request, whether successfull or failor.
      complete: function (){complete(_this)},
      success:function(result){
          if(result.status == 1){
            toastr.success(result.message);
            $('#add_images')[0].reset();
            $('#add_images').parsley().reset();
            $('.previewing').html('');
            $('.images_content_response').empty();
            $.each(result.data.restaurantImage, function(i, img){
                $('.images_content_response').append('<div class="img_prv"><img width="100" heigth="100" src="'+img.image+'" id="'+img.id+'" />'+"<a class='btn btn-primary btn-xs removeImage' style='color:white;margin-top: 50px;' data-restaurant_image_id='"+img.id+"'><span class='fa fa-trash'></span></a></div>")
            });
          }else{
            toastr.error(result.message);
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


   $(document).on('click','.images_btn',function(e){
      e.preventDefault();
      $('.images_content_response').empty();
      u_id = $(this).attr('data-restaurant_id');
      $('#restaurant_id').val(u_id);
      $.ajax({
         url:'{{url('restaurant/imageView')}}/'+u_id,
         dataType: 'json',
         success:function(result)
         {
          $.each(result.restaurantImage, function(i, img){
            var imagePath = img.image;
                $('.images_content_response').append(
                  $('<div class="img_prv"><img width="100" heigth="100" id="'+img.id+'" src="'+imagePath+'"/><a class="btn btn-primary btn-xs removeImage" style="color:white;" data-restaurant_image_id="'+img.id+'"><span class="fa fa-trash"></span></a>')
                )
          });
            //$('#images_content_response').html(result);
         } 
      });
      $('#imagesModal').modal('show');
   });

   $(document).on('click','.removeImage',function(e){
    e.preventDefault();
        var response = confirm('Are you sure want to delete this store image?');
        if(response){
          id = $(this).attr('data-restaurant_image_id');
          $("#"+id).remove();
          $(this).remove();
          $.ajax({
            type: 'post',
            data: {_method: 'delete', _token: "{{ csrf_token() }}"},
            dataType:'json',
            url: "{!! url('restaurant/restaurantImagesDelete' )!!}" + "/" + id,
            success:function(res){
              if(res.status === 1){ 
                toastr.success(res.message);
                
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
   $("#multipalImage").change(function(){
          if (event.target.files && event.target.files[0]) {
              var filesAmount = event.target.files.length;
              
              for (let i = 0; i < filesAmount; i++) {
                  let files = event.target.files[i];
                  var reader = new FileReader();
                  reader.onload =  (event) => {
                      $('.previewing').append("<img width='100' height='100' src='"+event.target.result+"' />")
                  }
                  reader.readAsDataURL(event.target.files[i]);
              }
          }
          
      });

      
  $("#addMoremultipalImage").change(function(){
          if (event.target.files && event.target.files[0]) {
              var filesAmount = event.target.files.length;
              
              for (let i = 0; i < filesAmount; i++) {
                  let files = event.target.files[i];
                  var reader = new FileReader();
                  reader.onload =  (event) => {
                      $('.previewing').append("<img width='100' height='100' src='"+event.target.result+"' />")
                  }
                  reader.readAsDataURL(event.target.files[i]);
              }
          }
          
      });
  });
function imageIsLoaded(e){
  //console.log(e);
  $("#file").css("color","green");
  $('#previewing').attr('src',e.target.result);
}

function imageIsLoaded2(e){
  //console.log(e);
  $("#file2").css("color","green");
  $('#previewing2').attr('src',e.target.result);
}
</script>
<script type="text/javascript">
    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    }); 
</script>
<script>
    $(document).ready(function(){

$(document).on('submit', "#time_role",function(e){
  e.preventDefault();
  var _this=$(this); 
  var formData = new FormData(this);
  formData.append('_method', 'post');
  var restro_id = $('#restro_id').val();

    $('#group_loader').fadeIn();
    // $('#time_role')[0].reset();
    // $('#time_role').parsley().reset();
    // var values = $('#edit_role').serialize();
    $.ajax({
    url:'{{ url('restaurant/time_update') }}'+ "/" + restro_id,
    dataType:'json',
    data:formData,
    type:'POST',
    cache:false,
    contentType: false,
    processData: false,
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){

        if (result.status) {
          toastr.success(result.message)

        } else {
          toastr.error(result.message)
        }
          $('#time_role')[0].reset();
          $('#time_role').parsley().reset();
          window.location.reload();
          setTimeout(function(){$('#time_role').fadeOut('slow')},1000)
          // ajax_datatable.draw();
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
