@extends('layouts.master')

@section('content')
<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('backend.Content_Manager') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.Content_Manager') }}</li>
            </ol>
           <!--  @can('Content-create')
              <a href="#" class="btn btn-primary btn btn-primary d-lg-block m-l-15" title="{{ __('Add Content') }}" data-toggle="modal" data-target="#add_modal" ><i class="fa fa-plus"></i> {{ __('Add') }}</a>
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
                    <table id="content_listing" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <!-- <th>{{ __('Sr. no') }}</th> -->
                                <th>{{ __('backend.Name') }}</th>
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

<!-- Modals -->

<div class="modal fade" id="add_modal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
          <form method="POST" action="{{ url('admin/api/content') }}" id="add_content">
          @csrf
            <div class="modal-header">
              <h4 class="modal-title">Add New Content</h4>
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
            @if($lang)
            @foreach($language as  $key => $lang)
            <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="name">{{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
                  <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value=""  class="form-control" placeholder="Name"/>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}}) </label>
                  <textarea id="description_{{$lang}}" name="description_{{$lang}}" @if($key==0) data-parsley-required="true" @endif class="form-control ckeditor" placeholder="Description"></textarea>
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
              <h4 class="modal-title">{{__('backend.Edit_Content')}}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                  <div id="edit_content_response"></div>  
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
              <h4 class="modal-title">{{__('backend.View_Content')}}</h4>
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
<script>
var ajax_datatable;
$(document).ready(function(){
$('#add_content').parsley();
ajax_datatable = $('#content_listing').DataTable({
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
    ajax: '{{ url('admin/api/content') }}',
    columns: [
      /*{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},*/
      { data: 'name', name: 'name' },
      { data: 'status', name: 'status' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [2, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {  
      
      var links='';
      links += `<div class="btn-group" role="group" >`;
      @can('Content-edit')
        links += `<a href="#" data-content_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_content" ><span class="fa fa-edit"></span></a>`;
      @endcan
      @can('Content-delete')
      //links += `<a href="#" data-content_id="${data.id}" title="Delete Content" class="btn btn-danger btn-xs delete_content" ><span class="fa fa-trash"></span></a>`;
      @endcan
      @can('Content-edit')
        links += `<a href="#" data-content_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan
      links += `</div>`;
      var status = '';
      if(data.status === 1){
        status += `<a href="#" data-content_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>{{__('backend.Active')}}</span></a>`;
      }else{
        status += `<a href="#" data-content_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>{{__('backend.Deactive')}}</span></a>`;
      }
      $('td:eq(1)', row).html(status);
      $('td:eq(3)', row).html(links);
      },
});

$(document).on('click','.change_status',function(e){
      e.preventDefault();
      status = $(this).data('status');
      if(status == 'active'){
        var response = confirm("{{__('backend.confirm_box_active_content')}}");
      }else{
        var response = confirm("{{__('backend.confirm_box_deactive_content')}}");
      }
      if(response){
        id = $(this).data('content_id');
        $.ajax({
          type: 'post',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('admin/content/changeStatus' )!!}" + "/" + id +'/'+status,
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
 

@can('Content-create')
$("#add_content").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
  var description_en = CKEDITOR.instances.description_en.getData();
  var description_ar = CKEDITOR.instances.description_ar.getData();
  alert(description_en);
   var formData = new FormData(this);
    formData.append('description[en]', description_en);
    formData.append('description[ar]', description_ar);
    
    $.ajax({
    url:'{{ url('admin/api/content') }}',
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
      if(res.status == 1){
          toastr.success(res.message)
          $('#add_content')[0].reset();
          $('#add_content').parsley().reset();
          ajax_datatable.draw();
           CKEDITOR.instances.description_en.setData('');
           CKEDITOR.instances.description_ar.setData('');
      }else{
        toastr.error(res.message)
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
@can('Content-edit')
//Edit staff
$(document).on('click','.edit_content',function(e){
    e.preventDefault();
    $('#edit_content_response').empty();
    id = $(this).attr('data-content_id');
    $.ajax({
       url:'{{url('admin/content/edit')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#edit_content_response').html(result);
       } 
    });
    $('#editModal').modal('show');
 });
@endcan
@can('Content-edit')
//Edit staff
$(document).on('click','.view_btn',function(e){
    e.preventDefault();
    $('#view_response').empty();
    id = $(this).attr('data-content_id');
    $.ajax({
       url:'{{url('admin/content/view')}}/'+id,
       dataType: 'html',
       success:function(result)
       {
        $('#view_response').html(result);
       } 
    });
    $('#viewModal').modal('show');
 });
@endcan
@can('Content-delete')
$(document).on('click','.delete_content',function(e){
      e.preventDefault();
      var response = confirm('Are you sure want to delete this content?');
      if(response){
        id = $(this).data('content_id');
        $.ajax({
          type: 'post',
          data: {_method: 'delete', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('admin/api/content' )!!}" + "/" + id,
          success:function(){
            toastr.success('{{ __('Content is deleted successfully') }}');
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
 <script>
    $(document).ready(function(){
        CKEDITOR.replaceClass('ckeditor');       
    });
 </script>
@endsection
