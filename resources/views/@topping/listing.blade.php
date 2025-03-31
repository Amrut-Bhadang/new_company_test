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
        <h4 class="text-themecolor">{{ __('Item Specifics') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Item Specifics') }}</li>
            </ol>
            @can('Toppings-create')
              <a href="{{url('topping/create')}}" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('backend.add_title_category') }}"><i class="fa fa-plus"></i> {{ __('Add Specifics') }}</a>
              <a href="{{ url('/topping/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
              <!-- <a href="" class="btn btn-info d-none d-lg-block m-l-15 import_btn" title="{{ __('Import') }}"><i class="fa fa-upload"></i> {{ __('Import') }}</a> -->
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
								<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
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
										<select name="main_category_id" id="main_category_id" class="form-control select3" onchange="getCategoryByIds()" multiple="multiple"  data-placeholder="Select Main Category" data-dropdown-css-class="select2-primary">
										   <option value="">--Select Main Category--</option>
										   @foreach ($main_category as $mainCategory)
										   <option value="{{ $mainCategory->id }}">{{ $mainCategory->name }}</option>
										   @endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 categoriesOptions">
									<div class="form-group" style="margin-right:5px">
										<select name="category_id" id="category_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Category" data-dropdown-css-class="select2-primary">
										   <option value="">--Select Category--</option>
										   <!-- @foreach ($category as $category)
										   <option value="{{ $category->id }}">{{ $category->name }}</option>
										   @endforeach -->
										</select>
									</div>
								</div>
								<div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 productsOptions">
									<div class="form-group">
										<select name="product_id" id="product_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Product" data-dropdown-css-class="select2-primary">
										   <option value="">--Select Product--</option>
										   <!-- @foreach ($products as $products)
										   <option value="{{ $products->id }}">{{ $products->name }}</option>
										   @endforeach -->
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
						<table id="topping_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<!-- <th>{{ __('backend.sr_no') }}</th> -->
									<th>{{ __('Specifics') }}</th>
									<th>{{ __('Item') }}</th>
									<th>{{ __('backend.status') }}</th> 
									<th>{{ __('backend.created_at') }}</th>
									<th>{{ __('backend.action') }}</th>
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
    <form method="POST" action="{{ url('api/topping') }}" id="add_category">
      @csrf
      <div class="modal-header">
        <h4 class="modal-title">{{ __('backend.add_new_topping') }}</h4>
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
          <div class="col-md-12">
            <div class="form-group">
              <label for="dish">Main Category</label>
              <select name="main_category_id" id="main_category_id" onchange="getAttributes()" class="form-control select2" data-placeholder="Select main category" style="width: 100%;" data-parsley-required="true" >
                  <option value=''>--Select main category--</option>
                  @foreach ($main_category as $main_category)
                      <option value="{{ $main_category->id }}">{{ $main_category->name }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6 categories_show_div">
            
          </div>
          <div class="col-md-6 dishes_show_div">
            
          </div>
        </div>
        <!-- <div class="row">
          @if($lang)
          @foreach($language as  $key => $lang)
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name">{{__('backend.topping_name')}} ({{__('backend.'.$lang)}})*</label>
                <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="" id="name" class="form-control" placeholder=" Name"  />
              </div>
            </div>          
          @endforeach
          @endif
        </div> -->
      </div>
      
        <!-- <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="is_mandatory">Is Mandatory*</label>
              <select name="is_mandatory" class="form-control is_mandatory" style="width: 100%;" data-parsley-required="true" >
                  <option value=''>---Select---</option>
                  <option value="1">Yes</option>
                  <option value="0">No</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" id="price">
              <label  for="price">Price (QAR)*</label>
              <input type="text" id="price" name="price" class="form-control" placeholder="Price (QAR)" data-parsley-type="digits">
            </div>
          </div>
        </div> -->
        <div class="row">
          <div class="col-md-12 attributes_show_div">
            
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
        <h4 class="modal-title">{{ __('backend.edit_topping') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div id="edit_category_response"></div>  
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
        <h4 class="modal-title">{{ __('backend.view_topping') }}</h4>
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

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>



<script>
  function getCategoryByIds() {
    var main_category_ids = $('select[name=main_category_id]').val();

    $.ajax({
       url:'{{url('topping/show_category_byIds')}}/'+main_category_ids,
       dataType: 'html',
       success:function(result)
       {
        $('.categoriesOptions').html(result);
       }
    });
  }

  function getProductByMainCat() {
    var category_ids = $('select[name=category_id]').val();

    $.ajax({
       url:'{{url('topping/show_productsByMainCatIds')}}/'+category_ids,
       dataType: 'html',
       success:function(result)
       {
        $('.productsOptions').html(result);
       }
    });    
  }
  var counter = 0;
  function getAttributes() {
    $('.dishes_show_div').html('');
    $('.attributes_show_div').html('');
    var main_category_id = $('#main_category_id').val();
    $.ajax({
       url:'{{url('topping/show_category')}}/'+main_category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.categories_show_div').html(result);
       }
    });
  }

  function showAttributes(main_category_id, category_id) {
    $.ajax({
       url:'{{url('topping/show_attributes')}}/'+main_category_id+'/'+category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.attributes_show_div').html(result);
       }
    });
  }

  function addMoreAttributeValues() {
    var main_category_id = $('#main_category_id').val();
    var category_id = $('#category_id').val();
    counter++;
    $.ajax({
       url:'{{url('topping/show_attribute_values')}}/'+main_category_id+'/'+category_id+'/'+counter,
       dataType: 'html',
       success:function(result)
       {
        $('.attributes_values_tr_before').before(result);
       }
    });
  }

  function removeAttributeValues(id) {
    $('.attributes_values_tr_'+id).remove();
  }

  function getDish() {
    var main_category_id = $('#main_category_id').val();
    var category_id = $('#category_id').val();
    $.ajax({
       url:'{{url('topping/show_dishes')}}/'+category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.dishes_show_div').html(result);
       }
    });
    showAttributes(main_category_id, category_id);
  }

  function getDishPopup() {
    var main_category_id = $('#popup_main_category_id').val();
    var category_id = $('#popup_category_id').val();
    $.ajax({
       url:'{{url('topping/show_dishes')}}/'+category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.show_popup_dishDiv').html(result);
       }
    });
  }

  function getCategoryOnPopup() {
    var main_category_id = $('#popup_main_category_id').val();
    var main_category_name = $( "#popup_main_category_id option:selected" ).text();

    $.ajax({
       url:'{{url('topping/show_category_popup')}}/'+main_category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.show_popup_categoryDiv').html(result);
       }
    });
  }

  function getToppingSampleFile() {
    var main_category_id = $('#popup_main_category_id').val();
    var category_id = $('#popup_category_id').val();

    if (main_category_id && category_id) {
      var url = '{{url('topping/exportSampleFileForSpecifics')}}/'+main_category_id+'/'+category_id

      window.location.href = url;

    } else {
      toastr.error('Please select main category and category for download the sample file.');
    }
  }
var ajax_datatable;
$(document).ready(function(){
$('.select2').select2();
$('.select3').select2();
$('#add_category').parsley();
ajax_datatable = $('#topping_listing').DataTable({
    processing: true,
    serverSide: true,

        ajax:{
          url:'{{ url('api/topping') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.main_category_id = $('select[name=main_category_id]').val();
              d.category_id = $('select[name=category_id]').val();
              d.product_id = $('select[name=product_id]').val();
          }
        },


   
    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'product_attr', name: 'product_attr', orderable: false, searchable: false},
      { data: 'dish', name: 'dish', searchable: false },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [3, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Toppings-edit')
      links += `<a href="{{url('topping/edit')}}/${data.id}" data-category_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Toppings-delete')
      //links += `<a href="#" data-category_id="${data.id}" title="Delete category" class="btn btn-danger btn-xs delete_category " ><span class="fa fa-trash"></span></a>`;
      @endcan
      @can('Toppings-edit')
      /*links += `<a href="#" data-category_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;*/
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.active_category')}}" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{__('backend.active')}}</span></a>`;
      }else{
        status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.deactive_category')}}" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{__('backend.deactive')}}</span></a>`;
      }
      $('td:eq(2)', row).html(status);
      $('td:eq(4)', row).html(links);
      },
});

$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this category?');
      }else{
        var response = confirm('Are you sure want to deactive this category?');
      }
      if(response){
        id = $(this).data('category_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('topping/changeStatus' )!!}" + "/" + id +'/'+status,
          success:function(res){
            if(res.status === 1){ 
              toastr.success(res.message);
              ajax_datatable.draw();
              $('.select2').val(null).trigger('change');
              $('.select3').val(null).trigger('change');
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
@can('Toppings-create')
$("#add_category").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/topping') }}',
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
                $('#add_category')[0].reset();
                $('#previewing').attr('src','images/image.png');
                $('#add_category').parsley().reset();
                ajax_datatable.draw();
                window.location.reload();

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

@can('Toppings-create')
//Edit staff
$(document).on('click','.import_btn',function(e){
    e.preventDefault();
    $('#import_response').empty();
    id = $(this).attr('data-product_id');
    $.ajax({
       url:'{{url('topping/import')}}',
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
@can('Toppings-create')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('topping/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Toppings-edit')
//Edit staff
$(document).on('click','.edit_category',function(e){
    e.preventDefault();
    $('#edit_category_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('topping/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_category_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Toppings-delete')
$(document).on('click','.delete_category',function(e){
  e.preventDefault();
  var response = confirm('Are you sure want to delete this category?');
  if(response){
    id = $(this).data('category_id');
    $.ajax({
        type: 'post',
        data: {_method: 'delete', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('api/topping' )!!}" + "/" + id,
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


$(document).on('change','.is_mandatory',function(e){
    e.preventDefault();
    //$('#music_category_type').hide();
    if($(this).val()=='1'){
      $('#price').hide();
    } else {
      $('#price').show();
    }
  });

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


  });

  function imageIsLoaded(e){
			//console.log(e);
			$("#file").css("color","green");
			$('#previewing').attr('src',e.target.result);
		}
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
