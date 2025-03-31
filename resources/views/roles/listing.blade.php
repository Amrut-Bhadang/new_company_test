@extends('layouts.master')

@section('content')
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Roles') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Roles') }}</li>
            </ol>
        </div>
    </div>
</div>
<!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
    <div class="row justify-content-center">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
                <div class="card-header">{{ __('Manage Roles') }}
              @can('Role-create')
                <a href="#" class="btn btn-primary float-right" title="{{ __('Add Roles') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('backend.add_roles') }}</a>
              @endcan
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                    <table  id="roles_listing" class="table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
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
    <!-- /.content -->

<!-- Modals -->

<div class="modal fade" id="add_modal">
        <div class="modal-dialog">
          <div class="modal-content">
          <form method="POST" action="{{ url('admin/api/roles') }}" id="add_role">
          @csrf
            <div class="modal-header">
              <h4 class="modal-title">Add New Role</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                    <div class="form-group">
                      <label class="control-label" for="name">Name*</label>
                      <input type="text" name="name" value="" id="name" class="form-control" placeholder="Name" data-parsley-required="true"  />
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
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit Role</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="edit_role_response"></div>  
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
<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_role').parsley();
ajax_datatable = $('#roles_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ url('admin/api/roles') }}',
    columns: [
      { data: 'name', name: 'name' },
      { data: 'created_at', name: 'created_at' },
      { data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    rowCallback: function(row, data, iDisplayIndex) {     
      var links='';
      @can('Role-edit')
        links += `<a href="#" data-role_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_role mr-1" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Role-delete')
        links += `<a href="#" data-role_id="${data.id}" title="Delete role" class="btn btn-danger btn-xs delete_role mr-1" ><span class="fa fa-trash"></span></a>`;
      @endcan
      $('td:eq(2)', row).html(links);
      },
});
@can('Role-create')
$("#add_role").on('submit',function(e){ 
  e.preventDefault();
  var _this=$(this); 
    $('#group_loader').fadeIn();
    var values = $('#add_role').serialize();
    $.ajax({
    url:'{{ url('admin/api/roles') }}',
    dataType:'json',
    data:values,
    type:'POST',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
          toastr.success(`Role ${result.name} has been created!`)
          setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
          $('#add_role')[0].reset();
          $('#add_role').parsley().reset();
          ajax_datatable.draw();
                location.reload();

      },
    error:function(jqXHR,textStatus,textStatus){
      toastr.error(jqXHR.responseJSON.message)
    }
      });
      return false;   
    });

  @endcan
@can('Role-edit')
 //Edit role
 $(document).on('click','.edit_role',function(e){
    e.preventDefault();
    $('#edit_role_response').empty();
    id = $(this).attr('data-role_id');
    $.ajax({
       url:'{{url('admin/roles/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_role_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Role-delete')
 $(document).on('click','.delete_role',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this role?');
      if(response){
        id = $(this).data('role_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('admin/api/roles' )!!}" + "/" + id,
          success:function(){
            toastr.success('{{ __('Role is deleted successfully') }}');
            ajax_datatable.draw();
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
      }
      return false;
    }); 
@endcan
  });
</script>

@endsection
