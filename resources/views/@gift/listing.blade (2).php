@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Gift Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Gift Manager') }}</li>
            </ol>
            @can('Gift-create')
              <a href="{{route('gift.add')}}" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Gift') }}"><i class="fa fa-plus"></i> {{ __('backend.add_gift') }}</a>
              <a href="{{ url('/gift/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
              <a href="" class="btn btn-info d-none d-lg-block m-l-15 import_btn" title="{{ __('Import') }}"><i class="fa fa-upload"></i> {{ __('Import') }}</a>
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
                    <div class="">

                       <form method="POST" id="search-form" class="form-inline-sec" role="form">
                            <div class="row">
								<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
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
								<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
									<div class="form-group" style="margin-right:5px">
										<select name="brand_id" class="select2" multiple="multiple"  data-dropdown-css-class="select2-primary" data-placeholder="Select Gift Brand" style="width: 100%;">
										   <option value="">--Select Brand--</option>
										   @foreach ($brands as $brands)
										   <option value="{{ $brands->id }}">{{ $brands->name }}</option>
										   @endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
									<div class="form-group" style="margin-right:5px">
										<select name="category_id" class="form-control select2" multiple="multiple" data-dropdown-css-class="select2-primary" data-placeholder="Select Gift Category" style="width: 100%;">
										   <option value="">--Select Category--</option>
										   @foreach ($category as $category)
										   <option value="{{ $category->id }}">{{ $category->name }}</option>
										   @endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
									<div class="form-group" style="margin-right:5px">
										<select name="sub_category_id" class="form-control select2" multiple="multiple" data-dropdown-css-class="select2-primary" data-placeholder="Select Gift Sub-Category" style="width: 100%;">
										   <option value="">--Select Sub-Category--</option>
										   @foreach ($record as $record)
										   <option value="{{ $record->id }}">{{ $record->name }}</option>
										   @endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
									<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
								</div>
                            </div>
                        </form>
						<div class="table-responsive">
							<table id="listing" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>{{ __('Sr. no') }}</th>
										<th>{{ __('Name') }}</th>
										<th>{{ __('SKU Code') }}</th>
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

</div>


      <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">View Gift</h4>
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

      <div class="modal fade" id="skuModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">SKU Code</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="Put" id="add_sku">
              @csrf
                <input type='hidden' id="gift_id" class="form-control" name="gift_id">
                <div class="col-md-12">
                     <div class="form-group">
                        <label class="control-label" for="sku_code">{{__('backend.sku_code')}}  </label>
                        <input type="text" name="sku_code" id="sku_code" class="form-control" data-parsley-required="true" placeholder="SKU Code">
                     </div>
                  <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
                </div>
              </form>  
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

      <div class="modal fade" id="importModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Import Excel</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <form method="POST" id="import_gift_data" enctype="multipart/form-data">
                @csrf
                <!-- <input type='hidden' id="gift_id" class="form-control" value="{{ url('gift/importData') }}" name="gift_id"> -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="col-md-6" for="file">Import Excel File*</label>
                      <input type="file" name="file" onchange="excelChange(this)" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="col-md-6" for="file">Import Images Zip</label>
                      <input type="file" name="images_zip" onchange="zipChange(this)" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mt-btn custom_cls">
                       <a href="{{url('public/uploads/gift_import_sample.xlsx')}}" class="btn btn-success btn-lg" title="Demo Download"><i class="fas fa-file-excel" aria-hidden="true"></i>Sample File</a>
                    </div>
                  </div>
                </div>
                <hr style="margin: 1em -15px">
                <div class="progress">
                    <div class="progress-bar bar percent">0%</div>
                </div>
                  
                <hr style="margin: 1em -15px">
                <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
               
              </form>  
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>


      <div class="modal fade" id="imagesModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Gifts Images</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body image_preview_modal">
            <form method="Put" id="add_images">
            @csrf
            <input type='hidden' id="gift_id" class="form-control" name="gift_id">
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

<!-- /Modals -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>

<script type="text/javascript">
  var bar = $('.bar');
  var percent = $('.percent');
  $("#import_gift_data").on('submit',function(e){
    e.preventDefault();
  
    var _this=$('#import_dish_data'); 
    var formData = new FormData(this);
    var percentComplete = 0;
   
    $.ajax({
        xhr: function() {
          var xhr = new window.XMLHttpRequest();

          xhr.upload.addEventListener("progress", function(evt) {

            if (evt.lengthComputable) {
              percentComplete = evt.loaded / evt.total;
              percentComplete = parseInt(percentComplete * 100);
              console.log(percentComplete);

              if (percentComplete === 100) {
                /*var percentVal = percentComplete + '%';
                bar.width(percentVal);
                percent.html(percentVal);*/
              }

            }
          }, false);

          return xhr;
        },
        url:'{{ url('gift/importData') }}',
        dataType:'json',
        data:formData,
        type:'POST',
        cache:false,
        contentType: false,
        processData: false,
        beforeSend: function (){
          before(_this)
          var percentVal = '0%';
          bar.width(percentVal);
          percent.html(percentVal);
        },
        
        // hides the loader after completion of request, whether successfull or failor.
        complete: function (){
          complete(_this)
        },
        success:function(res){

            if (res.status === 1) {
              var percentVal = percentComplete + '%';
              bar.width(percentVal);
              percent.html(percentVal);


              setTimeout(function(){
                toastr.success(res.message);
                window.location.reload();
              }, 1000);

            }else{
              toastr.error(res.message);
            }
        },
        error:function(jqXHR,textStatus,textStatus) {

          if (jqXHR.responseJSON.errors) {

            $.each(jqXHR.responseJSON.errors, function( index, value ) {
              toastr.error(value)
            });

          } else {
            toastr.error(jqXHR.responseJSON.message)
          }
        }
      });
      return false;   
    });
</script>
<script>
var ajax_datatable;
$(document).ready(function(){
  $('.select2').select2();
$('#add_form').parsley();
ajax_datatable = $('#listing').DataTable({
    processing: true,
    serverSide: true,

    ajax:{
          url:'{{ url('api/gift') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.brand_id = $('select[name=brand_id]').val();
              d.category_id = $('select[name=category_id]').val();
              d.sub_category_id = $('select[name=sub_category_id]').val();
          }
        },
        
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'sku_code', name: 'sku_code' },
      { data: 'is_active', name: 'is_active' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {   
      
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Gift-edit')
      links += `<a href="{{route('gift.edit')}}/${data.id}" title="Edit Details" class="btn btn-primary btn-xs" ><span class="fa fa-edit"></span></a>`;
      @endcan

      @can('Gift-delete')
      //links += `<a href="#" data-gift_id="${data.id}" title="Delete Gift" class="btn btn-danger btn-xs delete_btn" ><span class="fa fa-trash"></span></a>`;
      @endcan
      links += `<a href="#" data-gift_id="${data.id}" title="Images Details" class="btn btn-success btn-xs images_btn" ><span class="fa fa-image"></span></a>`;
      @can('Gift-edit')
      links += `<a href="#" data-gift_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      @can('Gift-edit')
      // links += `<a href="#" data-gift_id="${data.id}" title="SKU Code" class="btn btn-warning btn-xs sku_code" ><span class="fa fa-barcode"></span></a>`;
      @endcan

      /*var image = '';
      if(data.sku_code == null){
        image +='Not Available';
      }else{
        image +=`<img alt='SKU Code' src="${data.sku_code}"&print=true/>`;
      }*/

      links += `</div>`;
      var status = '';
      if(data.is_active === 1){
        status += `<a href="#" data-gift_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-gift_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
      // $('td:eq(2)', row).html(image);
      $('td:eq(3)', row).html(status);
      $('td:eq(5)', row).html(links);
      },
});

$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this gift?');
      }else{
        var response = confirm('Are you sure want to deactive this gift?');
      }
      if(response){
        id = $(this).data('gift_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('gift/changeStatus' )!!}" + "/" + id +'/'+status,
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

    
@can('Gift-create')
$("#add_form").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
  var description_en = CKEDITOR.instances.description_en.getData();
  var description_ar = CKEDITOR.instances.description_ar.getData();
   var formData = new FormData(this);
    formData.append('description[en]', description_en);
    formData.append('description[ar]', description_ar);
    $.ajax({
    url:'{{ url('api/gift') }}',
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
        if(result.status === 1){ 
          toastr.success(result.message);
          $('#add_form')[0].reset();
          $('#add_form').parsley().reset();
          $('#previewing').attr('src','images/no-image-available.png');
          $('.previewing').html('');
          ajax_datatable.draw();
          CKEDITOR.instances.description_en.setData('');
           CKEDITOR.instances.description_ar.setData('');
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
@endcan
@can('Gift-edit')
//Edit staff
$(document).on('click','.edit_btn',function(e){
    e.preventDefault();
    $('#edit_content_response').empty();
    id = $(this).attr('data-gift_id');
    $.ajax({
       url:'{{url('gift/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_content_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan

@can('Gift-edit')
$(document).on('click','.import_btn',function(e){
    e.preventDefault();
    $('#import_response').empty();
    $('#importModal').modal('show');
 });
@endcan

@can('Gift-edit')
//View staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-gift_id');
    
    $.ajax({
       url:'{{url('gift/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan

@can('Gift-edit')
//View staff
$(document).on('click','.sku_code',function(e){
    e.preventDefault();
    $('#sku_response').empty();
    u_id = $(this).attr('data-gift_id');
    $('#gift_id').val(u_id);
    $('#skuModal').modal('show');
 });
@endcan

@can('Gift-delete')
$(document).on('click','.delete_btn',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this gift?');
      if(response){
        id = $(this).data('gift_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('api/gift' )!!}" + "/" + id,
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
@endcan
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

    image_id = $('#gift_id').val();

    $.ajax({
    url:'{{ url('gift/add-more-images') }}/'+image_id,
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
          $.each(result.data.giftImage, function(i, img){
              var imagePath = img.image;
              $('.images_content_response').append($('<div class="img_prv"><img width="100" heigth="100" id="'+img.id+'" src="'+imagePath+'"/><a class="btn btn-primary btn-xs removeImage" style="color:white;" data-restaurant_image_id="'+img.id+'"><span class="fa fa-trash"></span></a>')
            )
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

$("#add_sku").on('submit',function(e){ 
  e.preventDefault();
  var _this=$(this); 

    var formData = new FormData(this);
    formData.append('_method', 'put');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    sku_id = $('#gift_id').val();
    //alert(sku_id);
    $.ajax({
    url:'{{ url('gift/add_sku') }}/'+sku_id,
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
          $('#add_sku')[0].reset();
          $('#add_sku').parsley().reset();
          $('.previewing').html('');
          window.location.reload();
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
    u_id = $(this).attr('data-gift_id');
    $('#gift_id').val(u_id);
    $.ajax({
       url:'{{url('gift/imageView')}}/'+u_id,
       dataType: 'json',
       success:function(result)
       {
        $.each(result.giftImage, function(i, img){
            var imagePath = img.image;
            $('.images_content_response').append($('<div class="img_prv"><img width="100" heigth="100" id="'+img.id+'" src="'+imagePath+'"/><a class="btn btn-primary btn-xs removeImage" style="color:white;" data-product_image_id="'+img.id+'" data-restaurant_image_id="'+img.id+'"><span class="fa fa-trash"></span></a>')

              /*$('.images_content_response').append(
              $('<img width="100" height="100" id="'+img.id+'" />').attr('src', img.image),
                "<a class='btn btn-danger btn-xs removeImage' style='color:white;margin-top: 78px; margin-left: 5px; ' data-product_image_id='"+img.id+"'><span class='fa fa-trash d-block'></span></a>"*/
            )
        });
          //$('#images_content_response').html(result);
       } 
    });
    $('#imagesModal').modal('show');
 });

 $(document).on('click','.removeImage',function(e){
  e.preventDefault();
      var response = confirm('Are you sure want to delete this product image?');
      if(response){
        id = $(this).attr('data-product_image_id');
        $("#"+id).remove();
        $(this).remove();
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('gift/giftImagesDelete' )!!}" + "/" + id,
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
                  if(filesAmount > 1){
                    $('.previewing').append("<img width='100' height='100' src='"+event.target.result+"' />")
                  } else {
                    $('.previewing').html("<img width='100' height='100' src='"+event.target.result+"' />")
                  }
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

    function category_change() {
      var category_id = $(".category_id").val();
      //alert(category_id);
       $.ajax({
         url:'{{url('gift/show_subcategory')}}/'+category_id,
         dataType: 'html',
         success:function(result)
         {
          $('.sub_categories_show_div').html(result);
         } 
      });
    }

    function excelChange($this){
      var fileObj = $this.files[0];
      var imageFileType = fileObj.type;
      console.log(imageFileType);
      var imageSize = fileObj.size;
    
      var match = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
      if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
        $('#previewing').attr('src','images/image.png');
        toastr.error('Please select a valid excel file <br> Note: Only .xlsx file type allowed!!');
        return false;
      }
      
    };

    function zipChange($this){
      var fileObj = $this.files[0];
      var imageFileType = fileObj.type;
      console.log(imageFileType);
      var imageSize = fileObj.size;
    
      var match = ["application/zip"];
      if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
        $('#previewing').attr('src','images/image.png');
        toastr.error('Please select a valid zip file <br> Note: Only .zip file type allowed!!');
        return false;
      }
      
    };

</script>
 <script>
    $(document).ready(function(){
      CKEDITOR.replaceClass('ckeditor');       
    });
 </script>

  <script>
    $(document).ready(function(){
        CKEDITOR.replace(document.getElementById('description'))       
    });
 </script>

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


@endsection
