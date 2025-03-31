@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('backend.Permissions') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('admin/permissions') }}">{{ __('backend.Permissions') }}</a></li>
                <li class="breadcrumb-item active">{{$user['name']}}</li>
            </ol>
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content user_permission">
    <div class="row justify-content-center">
        <div class="col-md-12">
           <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">
            {{ __('backend.Staff_Permissions') }} - {{$user['name']}}
            </h3>
          </div>
          <div class="card-body">
				<div class="table-responsive">
					<table id="user_permission_listing" class="table table-bordered table-striped table-sm">
                        <thead>
                           <tr>
                               <th>{{ __('backend.Action') }}</th>
                               <th>{{$user['name']}}</th>
                            </tr>
                            <tbody>
                             @foreach ($permissions as $permission)
                                <tr>
                                  <?php
                                    $permission_name = $permission['name'];

                                    if(str_starts_with($permission['name'], 'Brand-')) {
                                      $permission_name = str_replace("Brand","Vendor",$permission['name']);
                                    }
                                  ?>
                                  <td>{{ $permission_name }}</td>
                                  @if (in_array($permission['id'],$user_permission))
                                    <td><input type="checkbox" class="assign_permission" 
                                 data-permission_name = "{{$permission['id']}}" data-user_id = "{{$user['id']}}" checked /></td>
                                  @else
                                     <td><input type="checkbox" class="assign_permission" data-permission_name = "{{$permission['id']}}" data-user_id = "{{$user['id']}}"/></td>
                                  @endif
                                </tr>
                             @endforeach
                         </tbody>
                        </thead>
					</table>
				</div>
          </div>
          <!-- /.card -->
        </div>
        </div>
</div>

</div>
    <!-- /.content -->
<script type="text/javascript">
 $(document).ready(function(){
        
    $('.assign_permission').change(function(e){
        var action = '';
        if($(this).is(':checked'))
        {
            action = 'insert';
            
        }else{
            action = 'delete';
        }
        var permission_name = $(this).attr('data-permission_name');
        var user_id = $(this).attr('data-user_id');
        assign_permission_to_user(action,permission_name,user_id);
      });
      
      function assign_permission_to_user(action,permission_name,user_id)
      {
        console.log(action+"and"+permission_name+"and"+user_id);
        var p_name = permission_name;
        var u_id = user_id;
        if(action == 'insert')
        {
            url_link="{!! url('admin/permissions/add_user_permission' ) !!}" + "/" + u_id+'/'+p_name;
        }else{
             url_link="{!! url('admin/permissions/delete_user_permission' ) !!}" + "/" + u_id+'/'+p_name;
        }
        $.ajax({
          url:url_link,
          dataType:'json',
          type:'GET',
          success:function(result){
              if(result.status==1)
              {  
                toastr.success(result.message);
              }
              else
              {
                toastr.error(result.message);
              }
              
            }
            });
            return false;   
      } 
    });
</script>
@endsection