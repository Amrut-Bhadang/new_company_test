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
        <h4 class="text-themecolor">{{ __('SubCategory Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('SubCategory Manager') }}</li>
            </ol>
            @can('Category-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add SubCategory') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add SubCategory') }}</a>
              <!-- <a href="{{ url('/subcategory/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a> -->
              <!-- <a href="" class="btn btn-info d-none d-lg-block m-l-15 import_btn" title="{{ __('Import') }}"><i class="fa fa-upload"></i> {{ __('Import') }}</a> -->
            @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content category_sec">
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

                <!-- @if($user_type != 4)
  								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
  									<div class="form-group">
  									  <select name="main_category_id" id="main_category_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Service" data-dropdown-css-class="select2-primary">
  										 <option value="">--Select Service--</option>
  										 @foreach ($main_category as $mainCategory)
  										 <option value="{{ $mainCategory->id }}">{{ $mainCategory->name }}</option>
  										 @endforeach
  									  </select>
  									</div>
  								</div>
                @endif -->
								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
									<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
								</div>
                            </div>
                        </form>
					<div class="table-responsive">
						<table  id="category_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<!-- <th>{{ __('backend.sr_no') }}</th> -->
									<th>{{ __('SubCategory Name') }}</th>
									<!-- <th>{{ __('Total Items') }}</th> -->
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
    <form method="POST" action="{{ url('api/subcategory') }}" id="add_category">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">{{ __('Add SubCategory') }}</h4>
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
        @if($user_type == 4)
          <input type="hidden" name="main_category_id" id="main_category_id" value="{{$main_category_id}}">
        @endif

        @if($user_type != 4)
          <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="brand_id">Service*</label>
                  <select name="main_category_id" id="main_category_id" class="form-control" onchange="getCategory()"  data-parsley-required="true" >
                    <option value="">---Select Service----</option>
                    @foreach ($main_category as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
          </div>
        @endif
        <div class="row">
          <div class="col-md-12 categoriesOptions">
            <div class="form-group">
              <label class="control-label" for="category_type">Category*</label>
              <select class="form-control" data-parsley-required="true" name="category">
                <option value="">---Select---</option>
              </select>
            </div>
          </div> 
        </div> 
          
        
      <div class="row">
      @if($lang)
        @foreach($language as  $key => $lang)
        <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
                <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="" id="name" class="form-control" placeholder=" Name"  />
              </div>
            </div>
        @endforeach
      @endif
      </div>
      <!-- <div class="row">
        @if($lang)
        @foreach($language as  $key => $lang)
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}}) *</label>
                <textarea name="description[{{$lang}}]" data-parsley-required="true" id="description" class="form-control" placeholder="Description" ></textarea>
              </div>
            </div>
        @endforeach
        @endif
      </div> -->
      </div>
      <div class="row">
         <div class="col-md-12 subcategoriesOptions">
           
         </div>
        <!-- <div class="col-md-6">
          <label for="image">Image</label>
          <div class="form-group">
            <div class="input-group">
              <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
              <input type="file" id="file" name="image" class="form-control">
            </div>
            <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
          </div>
        </div> -->
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
        <h4 class="modal-title">{{ __('Edit category') }}</h4>
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
        <h4 class="modal-title">{{ __('View Category') }}</h4>
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

<script type="text/javascript">
  function getCategory() {
    var main_category_id = $('select[name=main_category_id]').val();

    $.ajax({
       url:'{{url('subcategory/show_service_category')}}/'+main_category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.categoriesOptions').html(result);
       }
    });

  }

  function getSubcategory() {
    var category_id = $('select[name=category_id]').val();

    $.ajax({
       url:'{{url('subcategory/show_sub_category')}}/'+category_id,
       dataType: 'html',
       success:function(result)
       {
        $('.subcategoriesOptions').html(result);
       }
    });

  }
</script>

<script>
var ajax_datatable;
$(document).ready(function(){
  var loginUserId = "<?php echo $user_id; ?>";
  var loginUserType = "<?php echo $user_type; ?>";
  $('.select2').select2();
$('#add_category').parsley();
ajax_datatable = $('#category_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax:{
      url:'{{ url('api/subcategory') }}',
      data: function (d) {
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
          d.main_category_id = $('select[name=main_category_id]').val();
      }
    },
    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'name', name: 'name' },
      // { data: 'total_dish', name: 'total_dish', orderable: false, searchable: false },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [2, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Category-edit')

      @if($user_type == 1)
        links += `<a href="#" data-category_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_category" ><span class="fa fa-edit"></span></a>`;

      @else
        if(data.added_by == loginUserId) {
          links += `<a href="#" data-category_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_category" ><span class="fa fa-edit"></span></a>`;
        }
      @endif

      @endcan
      @can('Category-delete')
      //links += `<a href="#" data-category_id="${data.id}" title="Delete category" class="btn btn-danger btn-xs delete_category " ><span class="fa fa-trash"></span></a>`;
      @endcan
      @can('Category-edit')
      // links += `<a href="#" data-category_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){

        @if($user_type == 1)
          status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.active_category')}}" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{__('backend.active')}}</span></a>`;

        @else
          if(data.added_by == loginUserId) {
            status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.active_category')}}" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{__('backend.active')}}</span></a>`;

          } else {
            status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.active_category')}}" data-status="deactive" class=""><span class='label label-rounded label-success'>{{__('backend.active')}}</span></a>`;
          }
        @endif
      }else{

        @if($user_type == 1)
          status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.deactive_category')}}" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{__('backend.deactive')}}</span></a>`;
        @else
          if(data.added_by == loginUserId) {
            status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.deactive_category')}}" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{__('backend.deactive')}}</span></a>`;

          } else {
            status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.deactive_category')}}" data-status="active" class=""><span class='label label-rounded label-warning'>{{__('backend.deactive')}}</span></a>`;
          }
        @endif
      }

      /*var type = '';
      if(data.type === 1){
        type += `<span class='label label-rounded label-success'>{{__('backend.food')}}</span>`;
      }else{
        type += `<span class='label label-rounded label-warning'>{{__('backend.gift')}}</span>`;
      }*/
      //$('td:eq(2)', row).html(type);
      $('td:eq(1)', row).html(status);
      $('td:eq(3)', row).html(links);
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
          url: "{!! url('subcategory/changeStatus' )!!}" + "/" + id +'/'+status,
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
@can('Category-create')
$("#add_category").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/subcategory') }}',
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
                $('#add_category').parsley().reset();
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
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('subcategory/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan

@can('Category-create')
//Bulk Upload
$(document).on('click','.import_btn',function(e){
    e.preventDefault();
    $('#import_response').empty();
    id = $(this).attr('data-product_id');
    $.ajax({
       url:'{{url('subcategory/import')}}',
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


@can('Category-edit')
//Edit staff
$(document).on('click','.edit_category',function(e){
    e.preventDefault();
    $('#edit_category_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('subcategory/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_category_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Category-delete')
$(document).on('click','.delete_category',function(e){
  e.preventDefault();
  var response = confirm('Are you sure want to delete this category?');
  if(response){
    id = $(this).data('category_id');
    $.ajax({
        type: 'post',
        data: {_method: 'delete', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('api/subcategory' )!!}" + "/" + id,
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
