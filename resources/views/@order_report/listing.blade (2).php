@extends('layouts.master')

@section('content')
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Order Report Manage') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Order Report Manage') }}</li>
            </ol>
            <!-- @can('Order-Report-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Discount') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a>
              @endcan -->
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
								<div class="col-xl-8 col-lg-8 col-md-8 col-sm-6">
									<div class="row input-daterange">
										<div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-6">
											<input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
										</div>
										<div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-6">
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
							<table id="report_listing" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<!-- <th>{{ __('Sr. no') }}</th> -->
										<th>{{ __('Message') }}</th>
										<th>{{ __('Is Reply') }}</th>
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
    <!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <form method="POST" action="{{ url('api/order_report') }}" id="add_discount">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">Add New Banner</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="col-md-12" for="category">Choose Category*</label>
              <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Category" name="category_type">Category
                </label>
              </div>
              <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Dish" name="category_type">Dish
                </label>
              </div>
              <div class="form-check-inline">
                <label class="form-check-label">
                  <input type="radio" class="form-check-input" onchange="category_change()" value="Restaurant" name="category_type">Restaurant
                </label>
              </div>
            </div>
          </div>          
        </div>

        <div class="row categories_show_div">

        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="discount_code">Discount Code</label>
              <input type="text" id="discount_code" name="discount_code" class="form-control" data-parsley-required="true" placeholder="Discount Code">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="discount_code">Percentage</label>
              <input type="text" id="percentage" name="percentage" class="form-control" data-parsley-required="true" placeholder="Percentage" >
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="discount_code">Valid Upto</label>
              <input type="date" id="valid_upto" name="valid_upto" class="form-control" data-parsley-required="true" placeholder="Valid Upto" >
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="no_of_use_per_user">No. Of Use Per User</label>
              <input type="text" id="no_of_use_per_user" name="no_of_use_per_user" class="form-control" data-parsley-required="true" placeholder="Number Of Use Per User" data-parsley-type="digits">
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
$('#add_discount').parsley();
$('.select2').select2();
ajax_datatable = $('#report_listing').DataTable({
    processing: true,
    serverSide: true,
        ajax:{
          url:'{{ url('api/order_report') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
          }
        },


    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'message', name: 'message'},
      { data: 'is_reply', name: 'is_reply' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      { data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [2, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      var links='';
      links += `<div class="btn-group" role="group" >`;
      /*@can('Banner-edit')
      links += `<a href="#" data-banner_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_btn" ><span class="fa fa-edit"></span></a>`;
      @endcan*/
      @can('Order-Report-edit')
      links += `<a href="#" data-report_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-discount_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-discount_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
      var is_reply = '';
      if(data.is_reply === 1){
        is_reply += 'Yes';
      }else{
        is_reply += 'No';
      }
      $('td:eq(1)', row).html(is_reply);
      $('td:eq(2)', row).html(status);
      $('td:eq(4)', row).html(links);
      },
});
@can('Banner-create')
$("#add_discount").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var formData = new FormData(this);
    $.ajax({
        url:'{{ url('api/discount') }}',
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
                $('#add_discount')[0].reset();
                $('#previewing').attr('src','images/image.png');
                $('#add_discount').parsley().reset();
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
//Edit staff
$(document).on('click','.edit_btn',function(e){
    e.preventDefault();
    $('#edit_banner_response').empty();
    id = $(this).attr('data-banner_id');
    $.ajax({
       url:'{{url('discount/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_banner_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan

@can('Banner-edit')
//View staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-report_id');
    
    $.ajax({
       url:'{{url('order_report/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan

$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this gift category?');
      }else{
        var response = confirm('Are you sure want to deactive this gift category?');
      }
      if(response){
        id = $(this).data('discount_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('discount/changeStatus' )!!}" + "/" + id +'/'+status,
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
function imageIsLoaded(e){
      //console.log(e);
      $("#file").css("color","green");
      $('#previewing').attr('src',e.target.result);
    }

  

  });
function category_change() {
    var category_type = $("input[name='category_type']:checked").val();

     $.ajax({
       url:'{{url('discount/show_category')}}/'+category_type,
       dataType: 'html',
       success:function(result)
       {
        $('.categories_show_div').html(result);
       } 
    });
  }
  
</script>
@endsection
