@extends('layouts.master')
<?php

use App\Models\Language;

$language = Language::pluck('lang')->toArray();

?>
@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <h4 class="text-themecolor">{{ __('backend.Popular_Time') }}</h4>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('backend.Popular_Time') }}</li>
      </ol>
      @can('Court-create')
      <a href="{{ url('admin/courts/popular_timing/create') }}/{{$id}}" class="btn btn-info d-none d-lg-block m-l-15" title="{{ __('Add Court') }}"><i class="fa fa-plus"></i> {{ __('backend.Add_Popular_Time') }}</a>
      <!-- <a href="{{ url('admin/users/exportUsers/0') }}" class="btn btn-warning d-none d-lg-block m-l-15" title="{{ __('backend.Export') }}"><i class="fa fa-download"></i> {{ __('backend.Export') }}</a> -->
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
                      <input type="text" name="from_date" id="from_date" class="form-control" placeholder="{{ __('backend.From_Date') }}" readonly />
                    </div>
                  </div>
                  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                      <input type="text" name="to_date" id="to_date" class="form-control" placeholder="{{ __('backend.To_Date') }}" readonly />
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <div class="form-group">
                  <select name="popular_time_status" id="popular_time_status" class="form-control select2" data-placeholder="{{ __('backend.Select_popular_time_status') }}" data-dropdown-css-class="select2-primary">
                    <option value="">--{{ __('backend.Select_popular_time_status') }}--</option>
                    <option value="Active">{{ __('backend.Active') }}</option>
                    <option value="Deactive">{{ __('backend.Deactive') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-xl-2 col-lg-3 col-md-3 col-12">
                <button type="submit" name="filter" id="filter" class="btn btn-info" style="margin-right:5px">{{ __('backend.Filter') }}</button>
                <a href="{{url()->current()}}" class="btn btn-dark"> <i class="fas fa-redo-alt"></i> {{ __('backend.Reset') }}</a>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table id="staff_listing" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <!-- <th>{{ __('Sr. no') }}</th> -->
                  <th>{{ __('backend.Popular_Day') }}</th>
                  <th>{{ __('backend.Popular_Time') }}</th>
                  <th>{{ __('backend.Status') }}</th>
                  <th>{{ __('backend.Created_At') }}</th>
                  <th>{{ __('backend.Action') }}</th>
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
      <form method="POST" action="{{ url('admin/api/courts') }}" id="add_staff">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Add New Court</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="facility_id">Facility</label>
                <select name="facility_id" id="facility_id1" class="form-control select2" data-placeholder="Select Facility" data-dropdown-css-class="select2-primary">
                  <option value="">--Select Facility--</option>
                  <option value="Facility 1">Facility 1</option>
                  <option value="Facility 2">Facility 2</option>
                  <option value="Facility 3">Facility 3</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="category_id">Category*</label>
                <select name="category_id" id="category_id1" data-parsley-required="true" class="form-control select2" data-placeholder="Select Category" data-dropdown-css-class="select2-primary">
                  <option value="">--Select Category--</option>
                  <option value="Category 1">Category 1</option>
                  <option value="Category 2">Category 2</option>
                  <option value="Category 3">Category 3</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            @if($language)
            @foreach($language as $key => $lang)
            <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"> {{__('Court Name')}} ({{__('backend.'.$lang)}})*</label>
                <input type="text" name="court_name[{{$lang}}]" data-parsley-required="true" value="" id="court_name" class="form-control" placeholder=" Court Name" />
              </div>
            </div>
            @endforeach
            @endif
          </div>
          <div class="row">
            <!-- <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="court_name">Court Name*</label>
                  <input type="text" name="court_name" value="" id="court_name" class="form-control" placeholder="Court Name" data-parsley-pattern="^[a-zA-Z]+$" data-parsley-pattern-message="Last name allow only character" data-parsley-required="true"  />
                </div>
              </div> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="address">Address*</label>
                <input type="text" placeholder="Address" name="address" class="form-control" id="address" autocomplete="off" data-parsley-required="true">
                <input type="hidden" class="latitude" id='latitude' name="latitude" />
                <input type="hidden" class="longitude" id='longitude' name="longitude" />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="minimum_hour_book">Minimum Hours Book*</label>
                <input type="text" name="minimum_hour_book" value="" id="minimum_hour_book" class="form-control" placeholder="Minimum Hours Book" autocomplete="off" data-parsley-required="true" />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="hourly_price">Hourly Price*</label>
                <input type="text" name="hourly_price" value="" id="hourly_price" class="form-control" placeholder="Hourly Price" autocomplete="off" data-parsley-required="true" />
              </div>
            </div>
            <div class="col-md-6 mb-2">
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
        <h4 class="modal-title">Edit Court</h4>
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
        <form method="POST" action="{{ url('admin/courts/send_notification') }}" id="sendUserNotification">
          @csrf
          <input type="hidden" name="user_id" id="notificationUserId" value="">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="title">Title*</label>
                <input type="text" name="title" value="" id="title" class="form-control" placeholder="Title" data-parsley-required="true" />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="message">Message*</label>
                <textarea id="message" name="message" data-parsley-required="true" class="form-control" placeholder="Message"></textarea>
              </div>
            </div>
          </div>
        </form>
        <hr style="margin: 1em -15px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary float-right save sendUserNotification"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Send Notification</button>
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
        <h4 class="modal-title">{{ __('backend.View_Court') }}</h4>
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
@php $login_user = Auth::user(); @endphp
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
  $(document).ready(function() {
    $('.input-daterange').datepicker({
      todayBtn: 'linked',
      format: 'yyyy-mm-dd',
      autoclose: true
    });
  });


  var ajax_datatable;
  $(document).ready(function() {
    $('#add_staff').parsley();
    $('.select2').select2();
    var court_id = "{{$id}}";
    ajax_datatable = $('#staff_listing').DataTable({
      processing: true,
      serverSide: true,
      language: {
        "sSearch": "{{__('backend.Search')}}",
        "sentries": "{{__('backend.entries')}}",
        "lengthMenu": "{{__('backend.Show')}} _MENU_ {{__('backend.entries')}}",
        "info": "{{__('backend.Showing')}} _START_ {{__('backend.to')}} _END_ {{__('backend.of')}} _TOTAL_ {{__('backend.entries')}}",
        "oPaginate": {
          "sNext": "{{__('backend.Next')}}",
          "sPrevious": "{{__('backend.Previous')}}",
        },
      },
      ajax: {
        url: '{{ url("admin/courts/popular_timing_index/") }}/'+court_id,
        data: function(d) {
          d.from_date = $('input[name=from_date]').val();
          d.to_date = $('input[name=to_date]').val();
          d.popular_time_status = $('select[name=popular_time_status]').val();
          //alert(d.country);
        }
      },
      columns: [
        /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
        {
          data: 'day',
          name: 'day'
        },
        {
          data: 'time',
          name: 'time'
        },
        {
          data: 'status',
          name: 'status'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'id',
          name: 'id',
          orderable: false,
          searchable: false
        }
      ],
      order: [
        [3, 'desc']
      ],
      rowCallback: function(row, data, iDisplayIndex) {
        // console.log(data, '-----------------------')
        var name = '';
        var image = '';
        var email = '';
        var links = '';
        var status = '';
        var is_featured = '';
        var total_order = '';
        var hourly_price = '';
        var timeslot = '';
        var admin_commission = '';
        links += `<div class="btn-group" role="group" >`;
        @can('Court-edit')
        links += `<a href="{{ url('admin/courts/popular_timing/edit') }}/${data.court_id}/${data.id}" data-staff_id="${data.id}" title="{{ __('backend.Edit_Details') }}" class="btn btn-info btn-xs" ><span class="fa fa-edit"></span></a>`;
        @endcan
        links += `</div>`;
        if (data.status === 1) {
          status += `<a href="#" data-staff_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{ __('backend.Active') }}</span></a>`;
        } else {
          status += `<a href="#" data-staff_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{ __('backend.Deactive') }}</span></a>`;
        }
        if (data.is_featured === 1) {
          is_featured += `<a href="#" data-staff_id="${data.id}" title="Active Account" data-is_featured="deactive" class="change_is_featured"><span class='label label-rounded label-success'>{{ __('backend.Yes') }}</span></a>`;
        } else {
          is_featured += `<a href="#" data-staff_id="${data.id}" title="Deactive Account" data-is_featured="active" class="change_is_featured"><span class='label label-rounded label-warning'>{{ __('backend.No') }}</span></a>`;
        }
        if (data.name == '') {
          name += `No Name`;
        } else {
          name += `<span> ${data.court_name} </span>`;
        }
        if (data.hourly_price == '') {
          hourly_price += `No Data`;
        } else {
          hourly_price += `<span> ${data.hourly_price} AED </span>`;
        }
        if (data.timeslot == '') {
          timeslot += `No Data`;
        } else {
          timeslot += `<span> ${data.timeslot} {{__('backend.Min')}} </span>`;
        }
        if (data.admin_commission == '') {
          admin_commission += `No Data`;
        } else {
          admin_commission += `<span> ${data.admin_commission} % </span>`;
        }

        if (data.email == null) {
          email += `No Email`;
        } else {
          email += `<span> ${data.email} </span>`;
        }
        if (data.image) {
          image = `<img width="100" height="100" src="${data.image}"/>`;
        }
        links += `</div>`;
        total_order += `<a href="{{ url('admin/courts/view_orders').'/' }}${data.id}" data-staff_id="${data.id}" class="view_order"><span class='label label-rounded label-primary'>${data.total_order}  Orders</span></a>`;
        // $('td:eq(0)', row).html(image);
        $('td:eq(2)', row).html(status);
        // $('td:eq(8)', row).html(is_featured);
        // $('td:eq(6)', row).html(hourly_price);
        // $('td:eq(5)', row).html(timeslot);
        // $('td:eq(7)', row).html(admin_commission);
        $('td:eq(4)', row).html(links);
      },
    });
  });
  $(document).on('click', '.change_status', function(e) {
    e.preventDefault();
    status = $(this).data('status');
    if (status == 'active') {
      var response = confirm("{{ __('backend.confirm_box_active_popular_time') }}");
    } else {
      var response = confirm("{{ __('backend.confirm_box_deactive_popular_time') }}");
    }
    if (response) {
      id = $(this).data('staff_id');
      $.ajax({
        type: 'post',
        data: {
          _method: 'get',
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        url: "{!! url('admin/courts/popular_timing/changeStatus' )!!}" + "/" + id + '/' + status,
        success: function(res) {
          if (res.status === 1) {
            toastr.success(res.message);
            ajax_datatable.draw();
          } else {
            toastr.error(res.message);
          }
        },
        error: function(jqXHR, textStatus, textStatus) {
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
  });


  $('#search-form').on('submit', function(e) {
    ajax_datatable.draw();
    e.preventDefault();
  });
  $('#refresh').click(function() {
    $('.select2').val(null).trigger('change');
    $('#from_date').val('');
    $('select[name=payment_mode]').val('');
    $('#to_date').val('');
    ajax_datatable.draw();
  });
  @can('Court-edit')
  $(document).on('click', '.send_notification', function(e) {
    e.preventDefault();
    $('#notification_response').empty();
    var userId = $(this).attr('data-staff_id');
    $('#notificationUserId').val(userId);
    $('#notificationModal').modal('show');
  });

  $('.sendUserNotification').parsley();
  $(document).on('click', '.sendUserNotification', function(e) {
    e.preventDefault();
    var _this = $(this);
    $('#group_loader').fadeIn();
    var values = $('#sendUserNotification').serialize();
    var title = $('#title').val();
    var message = $('#message').val();

    if (title && message) {
      $.ajax({
        url: '{{ url("admin/courts / send_notification") }}',
        dataType: 'json',
        data: values,
        type: 'POST',
        beforeSend: function() {
          before(_this)
        },
        // hides the loader after completion of request, whether successfull or failor.
        complete: function() {
          complete(_this)
        },
        success: function(result) {
          toastr.success(`Notification has been sent!`)
          setTimeout(function() {
            $('#disappear_add').fadeOut('slow')
          }, 3000)
          $("#sendUserNotification")[0].reset();
          // $('#sendUserNotification').parsley().reset();
          ajax_datatable.draw();
          window.location.reload();

        },
        error: function(jqXHR, textStatus, textStatus) {
          if (jqXHR.responseJSON.errors) {
            $.each(jqXHR.responseJSON.errors, function(index, value) {
              toastr.error(value)
            });
          } else {
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
  @can('Court-edit')
  //Edit staff
  $(document).on('click', '.view_btn', function(e) {
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-staff_id');
    $.ajax({
      url: '{{url("admin/courts/view")}}/' + id,
      dataType: 'html',
      success: function(result) {
        $('#view_response').html(result);
      }
    });
    $('#viewModal').modal('show');
  });
  @endcan
  @can('Court-delete')
  $(document).on('click', '.delete_staff', function(e) {
    e.preventDefault();
    var response = confirm("{{ __('backend.confirm_box_delete') }}");
    if (response) {
      id = $(this).data('staff_id');
      $.ajax({
        type: 'post',
        data: {
          _method: 'delete',
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        url: "{!! url('admin/api/courts' )!!}" + "/" + id,
        success: function() {
          toastr.success('{{ __("Court is deleted successfully") }}');
          ajax_datatable.draw();
        },
        error: function(jqXHR, textStatus, textStatus) {
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
      });
    }
    return false;
  });
  @endcan
</script>

@endsection