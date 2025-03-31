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
        <h4 class="text-themecolor">{{ __('Gift Inventory') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Gift Inventory') }}</li>
            </ol>
            @can('Inventory-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Gift Category') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('backend.gift_inventory') }}</a>
              <a href="{{ url('/gift-category/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
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
								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
									<div class="form-group">
									  <select name="gift_id" id="gift_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Gift" data-dropdown-css-class="select2-primary">
										 <option value="">--Select Gift--</option>
										 @foreach ($get_gift as $gitf)
										 <option value="{{ $gitf->id }}">{{ $gitf->name }}</option>
										 @endforeach
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
							<table id="inventory_listing" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<!-- <th>{{ __('backend.sr_no') }}</th> -->
										<th>{{ __('backend.gift_category') }}</th>
										<th>{{ __('backend.gifts') }}</th>
										<th>{{ __('backend.quantity') }}</th>
										<!-- <th>{{ __('backend.points') }}</th> -->
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

</div>
    <!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <form method="POST" action="{{ url('api/inventory') }}" id="add_category">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">Add New Gift Inventory</h4>
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
                <label class="control-label" for="category_id">Gift Category*</label>
                <select name="gift_category_id" class="form-control" onchange="get_gifts()" data-parsley-required="true">
                  <option value="">---Select Gift Category---</option>
                  @foreach ($gift_category as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6 show_gifts">
              <div class="form-group">
                <label class="control-label" for="category_id">Gifts*</label>
                <select name="gift_id" class="form-control" data-placeholder="Select Gift" style="width: 100%;" data-parsley-required="true" >
                  <option value="">--Select Gift--</option>
                  @foreach ($get_gift as $record)
                      <option value="{{ $record->id }}" >{{ $record->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"> {{__('backend.points')}}*</label>
                <input type="text" name="price" data-parsley-required="true" value="" id="price" class="form-control" placeholder="Points"  />
              </div>
            </div> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"> {{__('backend.quantity')}}*</label>
                <input type="text" name="quantity" data-parsley-required="true" value="" id="quantity" class="form-control" placeholder="Quantity"  />
              </div>
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
        <h4 class="modal-title">Edit Gift Sub Category</h4>
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

<!-- /Modals -->
<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Gift Category</h4>
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
$('.select2').select2();
$('.select3').select2();
$('#add_category').parsley();
ajax_datatable = $('#inventory_listing').DataTable({
    processing: true,
    serverSide: true,

     ajax:{
          url:'{{ url('api/inventory') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.gift_id = $('select[name=gift_id]').val();
          }
        },


    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'gift_cat_name', name: 'gift_cat_name' },
      { data: 'gift_name', name: 'gift_name' },
      { data: 'quantity', name: 'quantity' },
      // { data: 'price', name: 'price' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {

      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Inventory-edit')
      links += `<a href="#" data-category_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_category" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Inventory-delete')
      //links += `<a href="#" data-category_id="${data.id}" title="Delete category" class="btn btn-danger btn-xs delete_category" ><span class="fa fa-trash"></span></a>`;
      @endcan

      @can('Inventory-edit')
      links += `<a href="#" data-category_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-category_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-category_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
      $('td:eq(3)', row).html(status);
      $('td:eq(5)', row).html(links);
      },
});

$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this gift inventory?');
      }else{
        var response = confirm('Are you sure want to deactive this gift inventory?');
      }
      if(response){
        id = $(this).data('category_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('inventory/changeStatus' )!!}" + "/" + id +'/'+status,
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

    $(document).on('click','.enable_category',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to active this gift category?');
      if(response){
        id = $(this).data('category_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('gift-category/changeStatus' )!!}" + "/" + id +'/'+'active',
          success:function(){
            toastr.success('{{ __('Gift Category Status updated successfully') }}');
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

@can('Category-create')
$("#add_category").on('submit',function(e){
  e.preventDefault();
  var _this=$(this);
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/inventory') }}',
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
                $('#previewing').attr('src','images/no-image-available.png');
                $('#add_category').parsley().reset();
                $('.select2').val(null).trigger('change');
                $('.select3').val(null).trigger('change');
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
@can('Category-edit')
//Edit staff
$(document).on('click','.edit_category',function(e){
    e.preventDefault();
    $('#edit_category_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('inventory/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_category_response').html(result);
       }
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Category-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('inventory/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       }
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Category-delete')
$(document).on('click','.delete_category',function(e){
  e.preventDefault();
  var response = confirm('Are you sure want to delete this gift inventory?');
  if(response){
    id = $(this).data('category_id');
    $.ajax({
        type: 'post',
        data: {_method: 'delete', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('api/inventory' )!!}" + "/" + id,
        success:function(){
          toastr.success('{{ __('Gift Inventory is deleted successfully') }}');
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

function get_gifts(){
   var gift_category_id = $("select[name='gift_category_id']").val();
   //alert(gift_category_id);

   $.ajax({
     url:'{{url('inventory/get_gifts')}}/'+gift_category_id,
     dataType: 'html',
     success:function(result)
     {
      $('.show_gifts').html(result);
     }
  });
}


</script>



@endsection
