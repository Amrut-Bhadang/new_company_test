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
        <h4 class="text-themecolor">{{ __('backend.customized_category') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.customized_category') }}</li>
            </ol>
            @can('Toppings_category-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('backend.add_title_category') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('backend.add_customized_category') }}</a>
              <a href="{{ url('/topping_category/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
              @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content ">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                      <form method="POST" id="search-form" class="form-inline" role="form">
                            <div class="row">
								<div class="col-xl-8 col-lg-8 col-md-8 col-sm-6">
									<div class="row input-daterange">
										<div class="form-group col-md-4 col-sm-6">
											<input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
										</div>
										<div class="form-group col-md-4 col-sm-6">
											<input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
										</div>
									</div>
								</div>
								<div class="col-md-4 col-sm-6">
									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
									<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
								</div>
                            </div>
                        </form>
				<div class="table-responsive">
                    <table  id="topping_category_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <!-- <th>{{ __('backend.sr_no') }}</th> -->
                                <th>{{ __('backend.category_name') }}</th>
                                <th>{{ __('backend.topping_choose') }}</th>
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
    <!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <form method="POST" action="{{ url('api/topping_category') }}" id="add_category">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">{{ __('backend.add_new_topping_category') }}</h4>
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
              <label for="price_reflect_on">Topping Choose</label>
              <select name="topping_choose" class="form-control multiple-search" style="width: 100%;" data-parsley-required="true" >
                  <option value=''>--Select Topping Choose--</option>
                  <option value="0">Single</option>
                  <option value="1">Multiple</option>
              </select>
            </div>
          </div>
      </div>
      @if($lang)
      @foreach($language as  $key => $lang)
      <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}}) *</label>
              <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="" id="name" class="form-control" placeholder=" Name"  />
            </div>
          </div>
        </div>
      <!-- </div> -->

      @endforeach
      @endif
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
        <h4 class="modal-title">{{ __('backend.edit_topping_category') }}</h4>
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
        <h4 class="modal-title">{{ __('backend.view_topping_category') }}</h4>
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
$('#add_category').parsley();
ajax_datatable = $('#topping_category_listing').DataTable({
    processing: true,
    serverSide: true,

       ajax:{
          url:'{{ url('api/topping_category') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
          }
        },


    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'name', name: 'name' },
      { data: 'topping_choose', name: 'topping_choose' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [3, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {

      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Category-edit')
      links += `<a href="#" data-category_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_category" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Category-delete')
      //links += `<a href="#" data-category_id="${data.id}" title="Delete category" class="btn btn-danger btn-xs delete_category " ><span class="fa fa-trash"></span></a>`;
      @endcan
      @can('Category-edit')
      links += `<a href="#" data-category_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.active_category')}}" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{__('backend.active')}}</span></a>`;
      }else{
        status += `<a href="#" data-category_id="${data.id}" title="{{__('backend.deactive_category')}}" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{__('backend.deactive')}}</span></a>`;
      }
      var topping_choose = '';
      if(data.topping_choose === 1){
        topping_choose += 'Multiple';
      }else{
        topping_choose += 'Single';
      }
      $('td:eq(1)', row).html(topping_choose);
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
          url: "{!! url('topping_category/changeStatus' )!!}" + "/" + id +'/'+status,
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
        url:'{{ url('api/topping_category') }}',
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
       url:'{{url('topping_category/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       }
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Category-edit')
//Edit staff
$(document).on('click','.edit_category',function(e){
    e.preventDefault();
    $('#edit_category_response').empty();
    id = $(this).attr('data-category_id');
    $.ajax({
       url:'{{url('topping_category/edit')}}/'+id,
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
        url: "{!! url('api/topping_category' )!!}" + "/" + id,
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


  });

  function imageIsLoaded(e){
			//console.log(e);
			$("#file").css("color","green");
			$('#previewing').attr('src',e.target.result);
		}
</script>

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
