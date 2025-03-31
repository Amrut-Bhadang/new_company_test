@extends('layouts.master')
<?php /*dd($users);*/ ?>
@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Customer Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Customer Manager') }}</li>
            </ol>
            @can('Users-create')
              <!-- <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Staff') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('backend.add_user') }}</a> -->
               <a href="{{ url('/users/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('Export') }}"><i class="fa fa-download"></i> {{ __('Export') }}</a>
            @endcan
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content user_page">
    <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-body">
                      <form method="POST" id="search-form" class="form-inline-sec" role="form">
						<div class="row">
							<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
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
              <div class="col-xl-2 col-lg-6 col-md-6 col-sm-6">
                <div class="row">
                  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                      <input type="text" name="from_price" id="from_price" data-parsley-type="digits" class="form-control" placeholder="From Price" />
                    </div>
                  </div>
                  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                      <input type="text" name="to_price" id="to_price" data-parsley-type="digits" class="form-control" placeholder="To Price" />
                    </div>
                  </div>
                </div>
              </div>
							<div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 mobile_cls">
							  <div class="form-group">
								  <select name="country" id="country" class="form-control select2" multiple="multiple"  data-placeholder="Select Country" data-dropdown-css-class="select2-primary">
									 <option value="">--Select Country--</option>
									 @foreach ($country as $countries)
									 <option value="{{ str_replace('+', '',$countries->phonecode) }}">{{ $countries->name }}</option>
									 @endforeach
								  </select>
							  </div>
							</div>
              <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 mobile_cls">
                <div class="form-group">
                  <select name="service_id" id="service_id" class="form-control select2"  data-placeholder="Select Service" data-dropdown-css-class="select2-primary">
                    <option value="">--Select Service--</option>

                    @foreach ($main_category as $service)
                      <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
							<div class="col-xl-2 col-lg-3 col-md-3 col-12">
								<button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px">Filter</button>
								<a href="{{url()->current()}}" class="btn btn-info" > <i class="fas fa-redo-alt"></i> Reset</a>
							</div>
						</div>
                      </form>
					<div class="table-responsive">
						<table id="staff_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									 <!-- <th>{{ __('Sr. no') }}</th> -->
									<th>{{ __('Name') }}</th>
									<th>{{ __('Email') }}</th>
									<th>{{ __('Mobile') }}</th>
									<th>{{ __('Orders') }}</th>
									<th>{{ __('KiloPoints') }}</th> 
									<th>{{ __('Wallet') }} (QAR)</th>
                  <th>{{ __('Transaction') }} (QAR)</th>
                  <th>{{ __('Last Order Date') }}</th>
									<th>{{ __('Status') }}</th>
									<th>{{ __('Registered At') }}</th>
									<th>{{ __('Action') }}</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
              <tfoot>
                <tr>
                  <th colspan="5">Total</th>
                  <th class="wallet_amount" colspan="6">{{ number_format($wallet_amount,2)}} QAR</th>
                </tr>
              </tfoot>
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
        <form method="POST" action="{{ url('api/users') }}" id="add_staff">
        @csrf
          <div class="modal-header">
            <h4 class="modal-title">Add New Customer</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="first_name">First Name*</label>
                  <input type="text" name="first_name" value="" id="first_name" class="form-control" placeholder="First Name" data-parsley-pattern="^[a-zA-Z]+$" data-parsley-pattern-message="First name allow only character" data-parsley-required="true"  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="last_name">Last Name*</label>
                  <input type="text" name="last_name" value="" id="last_name" class="form-control" placeholder="Last Name" data-parsley-pattern="^[a-zA-Z]+$" data-parsley-pattern-message="Last name allow only character" data-parsley-required="true"  />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="email">Email*</label>
                  <input type="text" name="email" value="" id="email" class="form-control" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="mobile">Mobile*</label>
                    <div class="input-group mb-3">
						<div class="input-group-prepend">
						  <select name="country_code" class="form-control" style="width:180px" >
							@foreach ($country as $country)
								<option value="{{ $country->phonecode }}">{{ $country->name }} ({{ $country->phonecode }})</option>
							@endforeach
						  </select>
						</div>
						<input type="text" name="mobile" value="" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="8" data-parsley-maxlength="15"/>
					</div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="password">Password*</label>
                  <input type="password" name="password" value="" id="password" class="form-control" placeholder="Password" autocomplete="off" data-parsley-required="true"  />
                  <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleNewPassword"></i>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="confirm_password">Confirm Password*</label>
                  <input type="password" name="confirm_password" value="" id="confirm_password" class="form-control" placeholder="Confirm Password" data-parsley-required="true"  />
                  <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleConfirmPassword"></i>
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
        <h4 class="modal-title">Edit Customer</h4>
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

<div class="modal fade" id="notificationModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send Notification</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ url('users/send_notification') }}" id="sendUserNotification">
          @csrf
          <input type="hidden" name="user_id" id="notificationUserId" value="">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="title">Title*</label>
                <input type="text" name="title" value="" id="title" class="form-control" placeholder="Title" data-parsley-required="true"  />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="message">Message*</label>
                <textarea  id="message" name="message" data-parsley-required="true" class="form-control" placeholder="Message"></textarea>
              </div>
            </div>
          </div>
        </form>
        <hr style="margin: 1em -15px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary float-right save sendUserNotification"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Send Notification</button>
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
              <h4 class="modal-title">View Customer</h4>
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

<!-- /Modals -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
</script>

 <script>
var ajax_datatable;
$(document).ready(function(){
  const toggleNewPassword = document.querySelector('#toggleNewPassword');
  const newPassword = document.querySelector('#password');

  const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
  const confirm_password = document.querySelector('#confirm_password');

  toggleNewPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    newPassword.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  toggleConfirmPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
    confirm_password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  $('.input-daterange').datepicker({
    todayBtn:'linked',
    format:'yyyy-mm-dd',
    autoclose:true
  });
});

$('#search-form').on('submit', function(e) {
      ajax_datatable.draw();
        e.preventDefault();
      getWalletAmout();
});

function getWalletAmout() {
  var from_date = $('input[name=from_date]').val();
  var to_date = $('input[name=to_date]').val();
  var country = $('select[name=country]').val();
  var from_price = $('input[name=from_price]').val();
  var to_price = $('input[name=to_price]').val();
  var main_category_id = $('select[name=service_id]').val();

  var values = {
    from_date: from_date,
    to_date: to_date,
    from_price: from_price,
    to_price: to_price,
    country: country,
    main_category_id: main_category_id,
    _token: "{{ csrf_token() }}"
  };

  $.ajax({
      url:'{{ url('users/getWalletData') }}',
      dataType:'json',
      data:values,
      type:'POST',
      success:function(res){
        console.log(res);

        if(res.status === 1){
          $('.wallet_amount').text(res.data+' QAR')
          /*toastr.success(res.message);
          ajax_datatable.draw();*/
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


</script>


<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_staff').parsley();
$('.select2').select2();
ajax_datatable = $('#staff_listing').DataTable({
    processing: true,
    serverSide: true,

     ajax:{
          url:'{{ url('api/users') }}',
          data: function (d) {
              d.from_date = $('input[name=from_date]').val();
              d.to_date = $('input[name=to_date]').val();
              d.from_price = $('input[name=from_price]').val();
              d.to_price = $('input[name=to_price]').val();
              d.country = $('select[name=country]').val();
              d.main_category_id = $('select[name=service_id]').val();
              //alert(d.country);
          }
        },


    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'name', name: 'name' },
      { data: 'email', name: 'email' },
      { data: 'mobile', name: 'mobile' },
      { data: 'total_order', name: 'total_order', orderable: false, searchable: false},
      { data: 'total_points', name: 'total_points', orderable: false, searchable: false },
      { data: 'total_wallet', name: 'total_wallet', orderable: false , searchable: false},
      { data: 'total_trans_amt', name: 'total_trans_amt', orderable: false , searchable: false},
      { data: 'last_ordered_date', name: 'last_ordered_date', orderable: false , searchable: false},
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [9, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {
      // console.log(data, '-----------------------')
      var name='';
      var email='';
      var links='';
      var status = '';
      var total_order = '';
      links += `<div class="btn-group" role="group" >`;
      @can('Users-edit')
      // links += `<a href="#" data-staff_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_staff" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Users-delete')
      //links += `<a href="#" data-staff_id="${data.id}" title="Delete staff" class="btn btn-danger btn-xs delete_staff" ><span class="fa fa-trash"></span></a>`;
      @endcan

      @can('Users-edit')
      links += `<a href="#" data-staff_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      @can('Users-edit')
      links += `<a href="{{ url('users/view_address').'/' }}${data.id}" data-staff_id="${data.id}" title="View Address" class="btn btn-danger btn-xs view_address" ><span class="fa fa-home"></span></a>`;
      @endcan
      @can('Users-edit')
      links += `<a href="{{ url('users/transaction').'/' }}${data.id}" data-staff_id="${data.id}" title="View Transaction" class="btn btn-dark btn-xs view_transaction" ><span class="fa fa-credit-card"></span></a>`;
      @endcan
      @can('Users-edit')
      // links += `<a href="{{ url('users/giftstransaction').'/' }}${data.id}" data-staff_id="${data.id}" title="View Gifts Transaction" class="btn btn-secondary btn-xs view_transaction" ><span class="fa fa-gifts"></span></a>`;
      links += `<a href="javascript:void(0);" data-staff_id="${data.id}" title="Send Notification" class="btn btn-secondary btn-xs send_notification" ><span class="fa fa-bell"></span></a>`;
      @endcan
      links += `</div>`;
      if(data.status === 1){
        status += `<a href="#" data-staff_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      } else {
        status += `<a href="#" data-staff_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }
       if(data.name == ''){
        name += `No Name`;
      } else {
        name += `<span> ${data.name} </span>`;
      }

      if(data.email == null){
        email += `No Email`;
      } else {
        email += `<span> ${data.email} </span>`;
      }

      links += `</div>`;
        total_order += `<a href="{{ url('users/view_orders').'/' }}${data.id}" data-staff_id="${data.id}" class="view_order"><span class='label label-rounded label-primary'>${data.total_order}  Orders</span></a>`;

      $('td:eq(0)', row).html(name);
      $('td:eq(1)', row).html(email);
      $('td:eq(3)', row).html(total_order);
      $('td:eq(8)', row).html(status);
      $('td:eq(10)', row).html(links);
      },
});



$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm('Are you sure want to active this customer account?');
      }else{
        var response = confirm('Are you sure want to deactive this customer account?');
      }
      if(response){
        id = $(this).data('staff_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('users/changeStatus' )!!}" + "/" + id +'/'+status,
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

@can('Users-create')
$("#add_staff").on('submit',function(e){
  e.preventDefault();
  var _this=$(this);
    $('#group_loader').fadeIn();
    var values = $('#add_staff').serialize();
    $.ajax({
    url:'{{ url('api/users') }}',
    dataType:'json',
    data:values,
    type:'POST',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(res){
          if(res.status === 1){
            toastr.success(res.message);
            $('#add_staff')[0].reset();
            $('#add_staff').parsley().reset();
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
@can('Users-edit')
//Edit staff
$(document).on('click','.edit_staff',function(e){
    e.preventDefault();
    $('#edit_staff_response').empty();
    id = $(this).attr('data-staff_id');
    $.ajax({
       url:'{{url('users/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_staff_response').html(result);
       }
    });
    $('#editModal').modal('show');
 });
$(document).on('click','.send_notification',function(e){
    e.preventDefault();
    $('#notification_response').empty();
    var userId = $(this).attr('data-staff_id');
    $('#notificationUserId').val(userId);
    $('#notificationModal').modal('show');
 });

$('.sendUserNotification').parsley();
$(document).on('click','.sendUserNotification',function(e){
    e.preventDefault();
    var _this=$(this); 
      $('#group_loader').fadeIn();
      var values = $('#sendUserNotification').serialize();
      var title = $('#title').val();
      var message = $('#message').val();

      if (title && message) {
        $.ajax({
          url:'{{ url('users/send_notification') }}',
          dataType:'json',
          data:values,
          type:'POST',
          beforeSend: function (){before(_this)},
          // hides the loader after completion of request, whether successfull or failor.
          complete: function (){complete(_this)},
          success:function(result){
              toastr.success(`Notification has been sent!`)
              setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
              $("#sendUserNotification")[0].reset();
              // $('#sendUserNotification').parsley().reset();
              ajax_datatable.draw();
              window.location.reload();

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

      } else {
        toastr.error('Please fill all mandatory fields.')
      }
      return false;   
  });
@endcan
@can('Users-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-staff_id');
    $.ajax({
       url:'{{url('users/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       }
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Users-delete')
$(document).on('click','.delete_staff',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this staff?');
      if(response){
        id = $(this).data('staff_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('api/users' )!!}" + "/" + id,
          success:function(){
            toastr.success('{{ __('User is deleted successfully') }}');
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



  });
</script>

@endsection
