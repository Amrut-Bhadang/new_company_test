@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>



<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Attribute Value Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Attribute Value Manager') }}</li>
            </ol>
            @can('Attribute-create')
              <a href="{{ url('gift_attribute_value/create') }}" class="btn btn-primary d-lg-block m-l-15" title="{{ __('Add Attribute') }}"  ><i class="fa fa-plus"></i> {{ __('Add Attribute Value') }}</a>
            @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content attribute_sec">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">

                <div class="card-body">
                    <form method="POST" id="search-form" class="form-inline-sec" role="form">
                        <div class="row">
							<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
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
              
							<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<select name="category_id" id="category_id" class="form-control select4" onchange="getCategoryByIds()" multiple="multiple"  data-placeholder="Select Category" data-dropdown-css-class="select2-primary">
									   <option value="">--Select Category--</option>
									   @foreach ($category as $category)
									   <option value="{{ $category->id }}">{{ $category->name }}</option>
									   @endforeach
									</select>
								</div>
							</div>
              
							<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 categoriesOptions">
								<div class="form-group">
									<select name="sub_category_id" id="sub_category_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Sub-Category" data-dropdown-css-class="select2-primary">
									   <option value="">--Select Sub-Category--</option>
									</select>
								</div>
							</div>
                              <!-- <div class="form-group" style="margin-right:5px">
                                <select name="attributes_lang_id" id="attributes_lang_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Attribute" data-dropdown-css-class="select2-primary">
                                   <option value="">--Select Attribute--</option>
                                   @foreach ($attributes as $attributes)
                                   <option value="{{ $attributes->id }}">{{ $attributes->name }}</option>
                                   @endforeach
                                </select>
                             </div> -->
                            <!-- <div class="form-group" style="margin-right:5px">
                                <select name="customer_id" id="customer_id" style="width:200px" class="form-control select2" multiple="multiple"  data-placeholder="Select Customers" data-dropdown-css-class="select2-primary">
                                    <option value="">--Select Customer--</option>
                                   
                                </select>
                            </div>
                            <div class="form-group" style="margin-right:5px">
                                <select name="product_id" id="product_id" style="width:200px" class="form-control select2" multiple="multiple"  data-placeholder="Select Product" data-dropdown-css-class="select2-primary">
                                    <option value="">--Select Products--</option>
                                   
                                </select>
                            </div>
                            <div class="form-group" style="margin-right:5px">
                                <select name="payment_mode" id="payment_mode" style="width:200px" class="form-control"  data-placeholder="Select Payment Mode" >
                                    <option value="">--Select Payment Mode--</option>
                                    <option value="3">Online Payment</option>
                                    <option value="1">Cash Payment</option>
                                </select>
                            </div> -->
							<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
								<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
								<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
                            </div>
                        </div>
                    </form>
					<div class="table-responsive">
						<table  id="listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<!-- <th>{{ __('Sr. no') }}</th> -->
									<th>{{ __('Category') }}</th>
									<th>{{ __('Sub-Category') }}</th>
									<th>{{ __('Attribute Name') }}</th>
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


<div class="modal fade" id="editModal">
        <div class="modal-dialog modal-lg" >
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit Dish</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="edit_content_response"></div>  
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

      <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg" >
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">View Attribute</h4>
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
                  <div id="import_response"></div>  
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
              <h4 class="modal-title">Dish Images</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <form method="Put" id="add_images">
            @csrf
            <input type='hidden' id="product_id" class="form-control" name="product_id">
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


<script>
  var user_type = "{{$user_type}}";

  function getCategoryByIds() {
    var category_ids = $('select[name=category_id]').val();

    if (category_ids && category_ids.length > 0) {
      console.log(category_ids);
      $.ajax({
         url:'{{url('gift_attribute/show_category_byIds')}}/'+category_ids,
         dataType: 'html',
         success:function(result)
         {
          $('.categoriesOptions').html(result);
         }
      });
    }
  }

var ajax_datatable;
$(document).ready(function(){
  var loginUserType = "<?php echo $user_type; ?>";
  getCategoryByIds();
$('#add_form').parsley();
$('.select2').select2();
$('.select3').select2();
$('.select4').select2();
ajax_datatable = $('#listing').DataTable({
    processing: true,
    serverSide: true,

    ajax:{
      url:'{{ url('api/gift_attribute_value') }}',
      data: function (d) {
          d.attributes_lang_id = $('select[name=attributes_lang_id]').val();
          d.main_category_id = $('select[name=main_category_id]').val();
          d.category_id = $('select[name=category_id]').val();
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
      }
    },
    
    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, "width": "20px"},*/
      { data: 'category_name', name: 'category_name' },
      { data: 'sub_category_name', name: 'sub_category_name' },
      { data: 'attributes_name', name: 'attributes_name' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Product-edit')

      @if($user_type == 1)
        links += `<a href="{{url('gift_attribute_value/edit')}}/${data.id}" title="Edit Details" class="btn btn-primary btn-xs" ><span class="fa fa-edit"></span></a>`;

      @else
        if(data.added_by == loginUserId) {
          links += `<a href="{{url('gift_attribute_value/edit')}}/${data.id}" title="Edit Details" class="btn btn-primary btn-xs" ><span class="fa fa-edit"></span></a>`;
        }
      @endif

      @endcan
      @can('Product-delete')
      //links += `<a href="#" data-product_id="${data.id}" title="Delete" class="btn btn-danger btn-xs delete_btn" ><span class="fa fa-trash"></span></a>`;
      @endcan

      /*links += `<a href="#" data-product_id="${data.id}" title="Images Details" class="btn btn-primary btn-xs images_btn" ><span class="fa fa-image"></span></a>`;*/
      @can('Product-edit')
      links += `<a href="#" data-product_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-product_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-product_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
      $('td:eq(3)', row).html(status);
      $('td:eq(5)', row).html(links);
      },
});


$('#refresh').click(function(){
  $('#category_id').val('');
  ajax_datatable.draw();
 });
$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this attribute value?');
      }else{
        var response = confirm('Are you sure want to deactive this attribute value?');
      }
      if(response){
        id = $(this).data('product_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('gift_attribute_value/changeStatus' )!!}" + "/" + id +'/'+status,
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

@can('Product-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-product_id');
    $.ajax({
       url:'{{url('gift_attribute_value/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan

@can('Product-edit')
//Edit staff
$(document).on('click','.import_btn',function(e){
    e.preventDefault();
    $('#import_response').empty();
    id = $(this).attr('data-product_id');
    $.ajax({
       url:'{{url('gift_attribute_value/import')}}',
       dataType: 'html',
       success:function(result)
       {
        $('#import_response').html(result);
        $('.select3').select2();
       } 
    });
    $('#importModal').modal('show');
 });
@endcan

@can('Product-delete')
$(document).on('click','.delete_btn',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this product?');
      if(response){
        id = $(this).data('product_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('api/product' )!!}" + "/" + id,
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

    image_id = $('#product_id').val();

    $.ajax({
    url:'{{ url('gift_attribute_value/add-more-images') }}/'+image_id,
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
          $.each(result.data.productImage, function(i, img){
              $('.images_content_response').append(
              $('<img width="100" heigth="100" id="'+img.id+'" />').attr('src', '{{ URL::asset('uploads/product')}}/'+img.image),
                "<a class='btn btn-primary btn-xs removeImage' style='color:white;margin-top: 50px;' data-product_image_id='"+img.id+"'><span class='fa fa-trash'></span></a>"
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
});
$(document).ready(function(){
  var counter = 2;
  $("#addButton").click(function () {
    if(counter>10){
      toastr.error("Only 10 textboxes allow");
      return false;
    }
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
    newTextBoxDiv.after().html('<input type="text" class="form-control" placeholder="Ingredients" name="ingredients[]" id="textbox' + counter + '" value="" >');
    newTextBoxDiv.appendTo("#TextBoxesGroup");
    counter++;
 });
 $("#removeButton").click(function () {
  if(counter==2){
    toastr.error("one textbox is Required");
    return false;
  }
  counter--;
  $("#TextBoxDiv" + counter).remove();
 });
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
