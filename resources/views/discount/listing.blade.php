@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();
$login_user_data = auth()->user();
?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Discount Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Discount Manager') }}</li>
            </ol>
            @can('Discount-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Discount') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add Discount') }}</a>
              <a href="{{ url('/discount/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
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
                              @if($login_user_data->type != 4)
                								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
                									<div class="form-group">
                										<select name="restaurant_id" id="restaurant_id" class="form-control select2" multiple="multiple"  data-placeholder="Select Store" data-dropdown-css-class="select2-primary">
                										   <option value="">--Select Store--</option>
                										   @foreach ($restaurants as $restaurants)
                										   <option value="{{ $restaurants->id }}">{{ $restaurants->name }}</option>
                										   @endforeach
                										</select>
                									</div>
                								</div>
                              @endif
              								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
              									<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
              									<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
              								</div>
                            </div>
                      </form>

            					<div class="table-responsive">
            						<table id="discount_listing" class="table table-striped table-bordered" style="width:100%">
            							<thead>
            								<tr>
            									<!-- <th>{{ __('Sr. no') }}</th> -->
            									<th>{{ __('Discount Code') }}</th>
            									<th>{{ __('Category Type') }}</th>
            									<th>{{ __('Applied User') }}</th>
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
    <form method="POST" action="{{ url('api/discount') }}" id="add_discount">
    @csrf
      <div class="modal-header">
        <h4 class="modal-title">Add New Discount </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              @if($login_user_data->type == 4)
                <input type="hidden" class="form-check-input" value="Restaurant" name="category_type">
                <input type="hidden" class="form-check-input" value="{{ $restaurant_id }}" name="category_id[]">
              @endif
              @if($login_user_data->type != 4)
                <label class="col-md-12" for="category">Choose Category*</label>
                <!-- <div class="form-check-inline">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" onchange="category_change()" value="Category" name="category_type">Category
                  </label>
                </div>
                <div class="form-check-inline">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" onchange="category_change()" value="Dish" name="category_type">Dish
                  </label>
                </div> -->
                <div class="form-check-inline">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" checked onchange="category_change()" value="Restaurant" name="category_type">Store
                  </label>
                </div>
                <div class="form-check-inline">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" onchange="removeCategory()" value="Flat-Discount" name="category_type">Flat Discount
                  </label>
                </div>
                <div class="form-check-inline">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" onchange="infoSelection()" value="Info" name="category_type">Info Only
                  </label>
                </div>
              @endif
            </div>
          </div>
        </div>
        @if($login_user_data->type != 4)
          <div class="row categories_show_div">

          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="discount_code">Select Country*</label>
                <select name="country_ids[]" id="country_ids" class="form-control select2" multiple="multiple"  data-placeholder="Select Country" data-dropdown-css-class="select2-primary">
                   <option value="">--Select Country--</option>
                   @foreach ($country as $country)
                   <option value="{{ $country->id }}">{{ $country->name }}</option>
                   @endforeach
                  </select>
              </div>
            </div>
          </div>
        @endif
        <div class="row">
        @foreach($language as  $key => $lang)
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">{{__('backend.title')}} ({{__('backend.'.$lang)}})*</label>
              <input type="text" id="title" name="title[{{$lang}}]" class="form-control" data-parsley-required="true" placeholder="Title">
            </div>
          </div>
        @endforeach
        </div>
        
        <div class="row">
          <div class="col-md-6 discount_code">
            <div class="form-group">
              <label for="discount_code">Discount Code*</label>
              <input type="text" id="discount_code" name="discount_code" class="form-control" placeholder="Discount Code">
            </div>
          </div>
          <div class="col-md-6 percentage">
            <div class="form-group">
              <label for="discount_code">Percentage*</label>
              <input type="text" id="percentage" name="percentage" class="form-control" placeholder="Percentage" >
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 no_of_use">
            <div class="form-group">
              <label for="discount_code">No. Of Use*</label>
              <input type="text" id="no_of_use" name="no_of_use" class="form-control" placeholder="Number Of Use" data-parsley-type="digits">
            </div>
          </div>
          <div class="col-md-6 no_of_use_per_user">
            <div class="form-group">
              <label for="no_of_use_per_user">No. Of Use Per User*</label>
              <input type="text" id="no_of_use_per_user" name="no_of_use_per_user" class="form-control" placeholder="Number Of Use Per User" data-parsley-type="digits">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 min_order_amount">
            <div class="form-group">
              <label for="discount_code">Min. Order amount(QAR)*</label>
              <input type="number" id="min_order_amount" name="min_order_amount" class="form-control" placeholder="Min Order Amount" >
            </div>
          </div>
          <div class="col-md-6 max_discount_amount">
            <div class="form-group">
              <label for="max_discount_amount"> Max. discount amount(QAR)*</label>
              <input type="number" id="max_discount_amount" name="max_discount_amount" class="form-control" placeholder=" Max Discount Amount " data-parsley-type="digits">
            </div>
          </div>
        </div>



        <div class="row">
          @foreach($language as  $key => $lang)
            <div class="col-md-6">
              <div class="form-group">
                <label for="discount_code">{{__('backend.description')}} ({{__('backend.'.$lang)}})*</label>
                <textarea id="description" name="description[{{$lang}}]" class="form-control" data-parsley-required="true" placeholder="Description"></textarea>
              </div>
            </div>
          @endforeach
          <!-- <div class="col-md-12">
            <div class="form-group">
              <label for="discription">Description</label>
               <textarea id="description" class="form-control" name="description" data-parsley-required="true" placeholder="Description"></textarea>
            </div>
          </div> -->
        </div>
        <div class="row input-daterange">
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid From*</label>
              <input type="text" name="valid_from" id="valid_from" class="form-control" placeholder="From Date" readonly />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid Upto*</label>
              <input type="text" name="valid_upto" id="valid_upto" class="form-control" placeholder="To Date" readonly />
            </div>
          </div>
        </div>

        <div class="row">
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid From*</label>
              <input type="date" id="from_date" name="valid_from" class="form-control" data-parsley-required="true" placeholder="Valid Upto" onkeydown="return false">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid Upto*</label>
              <input type="date" id="to_date" name="valid_upto" class="form-control" data-parsley-required="true" placeholder="Valid Upto" onkeydown="return false">
            </div>
          </div> -->
          <div class="col-md-6">
              <label for="image">Image</label>
            <div class="form-group">
              <div class="input-group">
                <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                <input type="file" id="file" name="image" class="form-control">
              </div>
              <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
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
        <h4 class="modal-title">Edit Discount Code</h4>
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
        <h4 class="modal-title">View Discount Code</h4>
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
  category_change();
  $('.select2').select2();
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
var login_user_id = "<?php echo $login_user_data->id; ?>";
var user_type = "<?php echo $login_user_data->type; ?>";
ajax_datatable = $('#discount_listing').DataTable({
    processing: true,
    serverSide: true,
     ajax:{
          url:'{{ url('api/discount') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.restaurant_id = $('select[name=restaurant_id]').val();
          }
        },

    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'discount_code', name: 'discount_code'},
      { data: 'category_type', name: 'category_type' },
      { data: 'applied_user', name: 'applied_user', orderable: false , searchable: false},
      // { data: 'min_order_amount', name: 'min_order_amount'},
      // { data: 'max_discount_amount', name: 'max_discount_amount'},
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      { data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {
      var links='';
      var applied_user = '';
      links += `<div class="btn-group" role="group" >`;
      @can('Discount-edit')

      if (user_type == 1) {
        links += `<a href="#" data-banner_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_btn" ><span class="fa fa-edit"></span></a>`;

      } else {

        if (login_user_id == data.added_by) {
          links += `<a href="#" data-banner_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_btn" ><span class="fa fa-edit"></span></a>`;
        }
      }
      @endcan
      @can('Discount-edit')
      links += `<a href="#" data-banner_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      /*@can('Discount-edit')
      links += `<a href="{{ url('discount/user_details').'/' }}${data.id}" data-banner_id="${data.id}" title="User Details" class="btn btn-success btn-xs user_btn" ><span class="fa fa-user"></span></a>`;
      @endcan*/
      links += `</div>`;
      var category_type = data.category_type;

      if (data.category_type == 'Restaurant') {
        category_type = 'Store';
      }
      var status = '';

      if (user_type == 1) {

        if(data.status === 1){
          status += `<a href="#" data-discount_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
        }else{
          status += `<a href="#" data-discount_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
        }

      } else {

        if (login_user_id == data.added_by) {

          if(data.status === 1){
            status += `<a href="#" data-discount_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
          }else{
            status += `<a href="#" data-discount_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
          }

        } else {

          if(data.status === 1){
            status += `<a href="#" data-discount_id="${data.id}" title="Active Account" data-status="deactive" class=""><span class='label label-rounded label-success'>Active</span></a>`;
          }else{
            status += `<a href="#" data-discount_id="${data.id}" title="Deactive Account" data-status="active" class=""><span class='label label-rounded label-warning'>Deactive</span></a>`;
          }
        }
      }

      applied_user += `</div>`;
        applied_user += `<a href="{{ url('discount/user_details').'/' }}${data.id}" data-banner_id="${data.id}" class="view_details"><span class='label label-rounded label-primary'>Applied User  (${data.applied_user})</span></a>`;

      $('td:eq(1)', row).html(category_type);
      $('td:eq(2)', row).html(applied_user);
      $('td:eq(3)', row).html(status);
      $('td:eq(5)', row).html(links);
      },
});
@can('Discount-create')
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
                $('#previewing').attr('src','images/no-image-available.png');
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
@can('Discount-edit')
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

@can('Discount-edit')
//View staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-banner_id');

    $.ajax({
       url:'{{url('discount/view')}}/'+id,
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
    var category_type = $("input[name='category_type']:checked").val();
    $('.categories_show_div').show();
    $('.discount_code').show();
    $('.percentage').show();
    $('.no_of_use').show();
    $('.no_of_use_per_user').show();
    $('.min_order_amount').show();
    $('.max_discount_amount').show();

     $.ajax({
       url:'{{url('discount/show_category')}}/'+category_type,
       dataType: 'html',
       success:function(result)
       {
        $('.categories_show_div').html(result);
       }
    });
  }

  function removeCategory() {
    $('.categories_show_div').hide();
    $('.discount_code').show();
    $('.percentage').show();
    $('.no_of_use').show();
    $('.no_of_use_per_user').show();
    $('.min_order_amount').show();
    $('.max_discount_amount').show();
  }

  function infoSelection() {
  var category_type = $("input[name='category_type']:checked").val();

  if (category_type == 'Info') {
    $('.categories_show_div').hide();
    $('.discount_code').hide();
    $('.percentage').hide();
    $('.no_of_use').hide();
    $('.no_of_use_per_user').hide();
    $('.min_order_amount').hide();
    $('.max_discount_amount').hide();
  }
}

</script>
@endsection
