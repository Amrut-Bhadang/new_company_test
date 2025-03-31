@extends('layouts.master')

@section('content')
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('backend.Email_Template_Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.Email_Template_Manager') }}</li>
            </ol>
            <!-- @can('Email-Template-create')
              <a href="#" class="btn btn-primary d-none d-lg-block m-l-15" title="{{ __('Add Staff') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a>
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
                    <div class="table-responsive">
						<table id="email_listing" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>{{ __('backend.Sr_no') }}</th>
									<th>{{ __('backend.Name') }}</th>
									<th>{{ __('backend.subject') }}</th>
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
    <!-- /.content -->


<div class="modal fade" id="editModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">{{__('backend.Edit_Email_Template')}}</h4>
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

      <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">{{__('backend.View_Email_Template')}}</h4>
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


<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_staff').parsley();
$('.select2').select2();
ajax_datatable = $('#email_listing').DataTable({
    processing: true,
    serverSide: true,
    language: {
        "sSearch":"{{__('backend.Search')}}",
        "sentries":"{{__('backend.entries')}}",
        "lengthMenu": "{{__('backend.Show')}} _MENU_ {{__('backend.entries')}}",
        "info": "{{__('backend.Showing')}} _START_ {{__('backend.to')}} _END_ {{__('backend.of')}} _TOTAL_ {{__('backend.entries')}}",
        "oPaginate": {           
            "sNext":    "{{__('backend.Next')}}",
            "sPrevious": "{{__('backend.Previous')}}",           
        },
      },
    ajax: "{{ url('admin/api/emails') }}",
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'subject', name: 'subject' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {     
      var links='';
      var status = '';
      links += `<div class="btn-group" role="group" >`;
      @can('Email-Template-edit')
      links += `<a href="#" data-staff_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_staff" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Email-Template-delete')
      //links += `<a href="#" data-staff_id="${data.id}" title="Delete staff" class="btn btn-danger btn-xs delete_staff" ><span class="fa fa-trash"></span></a>`;
      @endcan
      @can('Email-Template-edit')
      links += `<a href="#" data-staff_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      if(data.status === 1){
        status += `<a href="#" data-staff_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{__('backend.Active')}}</span></a>`;
      }else{
        status += `<a href="#" data-staff_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{__('backend.Deactive')}}</span></a>`;
      }
      $('td:eq(3)', row).html(status);
      $('td:eq(5)', row).html(links);
      },
});



$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm("{{__('backend.confirm_box_active_email')}}");
      }else{
        var response = confirm("{{__('backend.confirm_box_deactive_email')}}");
      }
      if(response){
        id = $(this).data('staff_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('admin/emails/changeStatus' )!!}" + "/" + id +'/'+status,
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

@can('Email-Template-create')
$("#add_staff").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    $('#group_loader').fadeIn();
    var values = $('#add_staff').serialize();
    $.ajax({
    url:"{{ url('admin/api/emails') }}",
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
@can('Email-Template-edit')
//Edit staff
$(document).on('click','.edit_staff',function(e){
    e.preventDefault();
    $('#edit_staff_response').empty();
    id = $(this).attr('data-staff_id');
    $.ajax({
       url:'{{url("admin/emails/edit")}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_staff_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Email-Template-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-staff_id');
    $.ajax({
       url:'{{url("admin/emails/view")}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Email-Template-delete')
$(document).on('click','.delete_staff',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this staff?');
      if(response){
        id = $(this).data('staff_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('admin/api/emails' )!!}" + "/" + id,
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
