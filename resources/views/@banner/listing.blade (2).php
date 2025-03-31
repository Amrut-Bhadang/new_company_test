@extends('layouts.master')

@section('content')

<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Banner Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Banner Manager') }}</li>
            </ol>
            @can('Banner-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Banner') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add Banner') }}</a>
              <!-- <a href="{{ url('/banner/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-file-excel-o"></i> {{ __('Excel') }}</a> -->
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
									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
									<a href="{{url()->current()}}" class="btn btn-info"> <i class="fas fa-redo-alt"></i> Reset</a>
								</div>
							</div>
                        </form>
						<div class="table-responsive">
							<table id="banner_listing" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<!-- <th>{{ __('Sr. no') }}</th> -->
										<th>{{ __('Banner Image') }}</th>
										<th>{{ __('Service Type') }}</th>
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
    <form method="POST" action="{{ url('api/banner') }}" id="add_banner">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title"> Add New Banner </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="main_category_id">Service*</label>
              <select name="main_category_id" id="main_category_id" onchange="category_change()" class="form-control"  data-parsley-required="true" >
                <option value="">---Select Service----</option>
                @foreach ($main_category as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-12" for="category">Choose Category*</label>
              <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Category" name="category_type">Category
                </label>
              </div>
              <!-- <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Dish" name="category_type">Product
                </label>
              </div> -->
              <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Link" name="category_type">Link
                </label>
              </div>
              <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Restaurant" name="category_type">Store
                </label>
              </div>
              <!-- <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Gift" name="category_type">Gift
                </label>
              </div> -->
            </div>
          </div>
        </div>

        <div class="row categories_show_div">
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="valid_from">Valid From*</label>
              <input type="date" id="valid_from" min="<?php echo date('Y-m-d') ?>" name="valid_from" class="form-control" data-parsley-required="true" placeholder="Valid From" onkeydown="return false">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="valid_to">Valid To*</label>
              <input type="date" id="valid_to" min="<?php echo date('Y-m-d') ?>" name="valid_to" class="form-control" data-parsley-required="true" placeholder="Valid To" onkeydown="return false">
            </div>
          </div>
          <div class="col-md-6 link-div hide">
            <label for="link">Link</label>
            <div class="form-group input-group">
              <input type="text" id="link" name="link" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
              <label for="image">Image</label>
            <div class="form-group">
              <div class="input-group">
                <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                <input type="file" id="file" name="image" class="form-control">
              </div>
              <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 1125x500</span>
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
        <h4 class="modal-title">Edit Banner</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div id="edit_banner_response"></div>
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
        <h4 class="modal-title">View Banner</h4>
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
$('#add_banner').parsley();
ajax_datatable = $('#banner_listing').DataTable({
    processing: true,
    serverSide: true,
     ajax:{
          url:'{{ url('api/banner') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
          }
        },

    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'file_path', name: 'file_path', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'created_at', name: 'created_at' },
      { data: 'id', name: 'id', orderable: false, searchable: false}
    ],

    order: [ [2, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Banner-edit')
      links += `<a href="#" data-banner_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_btn" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Banner-delete')
      links += `<a href="#" data-banner_id="${data.id}" title="Delete Banner" class="btn btn-danger btn-xs delete_banner" ><span class="fa fa-trash"></span></a>`;
      @endcan

      links += `</div>`;
      var image = '';
      var name = data.name;

      if (name == 'Restaurants') {
        name = 'Stores';
      }

      image +=`<img width="100" height="100" src="${data.file_path}">`;
      $('td:eq(0)', row).html(image);
      $('td:eq(1)', row).html(name);
      $('td:eq(3)', row).html(links);
      },
});
@can('Banner-create')
$("#add_banner").on('submit',function(e){
  e.preventDefault();
  var _this=$(this);
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/banner') }}',
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
                $('#add_banner')[0].reset();
                $('#previewing').attr('src','images/no-image-available.png');
                $('#add_banner').parsley().reset();
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

@can('Banner-edit')
$(document).on('click','.delete_banner',function(e){
    e.preventDefault();
    var response = confirm('Are you sure want to delete this banner?');
    if(response){
      id = $(this).data('banner_id');
      $.ajax({
        type: 'post',
        data: {_method: 'delete', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('api/banner' )!!}" + "/" + id,
        success:function(){
          toastr.success('{{ __('Banner is deleted successfully') }}');
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
//Edit staff
$(document).on('click','.edit_btn',function(e){
    e.preventDefault();
    $('#edit_banner_response').empty();
    id = $(this).attr('data-banner_id');
    $.ajax({
       url:'{{url('banner/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_banner_response').html(result);
       }
    });
    $('#editModal').modal('show');
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
function category_change() {
    var main_category_id = $('#main_category_id').val();

    if (main_category_id) {
      var category_type = $("input[name='category_type']:checked").val();

      if (category_type != 'Link') {
        $.ajax({
           url:'{{url('banner/show_category')}}/'+category_type+'/'+main_category_id,
           dataType: 'html',
           success:function(result)
           {
            $('.categories_show_div').html(result);
            $('.link-div').hide();
           }
        });

      } else {
        $('.link-div').show();
        $('.categories_show_div').html('');
      }

    } else {
      $("input[name='category_type']:checked").prop('checked', false);
      toastr.error('Please choose main category first');
      return false;
    }
  }

</script>
@endsection
