@extends('layouts.master')

@section('content')

<?php
use App\Models\Language;
$language = Language::pluck('lang')->toArray();
//dd($menu);
?>
 
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <div class="d-flex align-items-center">
        <a href="{{ url('restaurant') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Store Tables') }}</h4>
      </div>
        <!-- <h4 class="text-themecolor">{{ __('Store Menu') }}</h4> -->
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Store Tables') }}</li>
            </ol>
            
        </div>
    </div>
</div>
<!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
      <!-- <div class="row">
        <div class="col-md-6" style="margin-bottom: 10px;">
           <a href="{{ url('restaurant') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
        </div>
      </div> -->
    <div class="row">
      <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <form method="POST" id="restaurant-table-form" class="form-inline-sec" role="form">
                  @csrf
                  <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                      <div class="row input-daterange">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                          <div class="form-group">
                            <input type="hidden" name="restro_id" id="restro_id" value="{{$restro_id}}"/>
                            <input type="number" name="table_count" id="table_count" class="form-control" placeholder="Enter Tables Count"/>
                          </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
                          <button type="submit" name="filter" id="filter" class="btn btn-primary" style="margin-right:5px"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span>Submit</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                <div class="table-responsive">
                  <table id="restaurant_tables" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                          <tr>
                              <th>{{ __('Table. no') }}</th>
                              <th>{{ __('QR Code') }}</th>
                              <th>{{ __('Created At') }}</th>
                              <!-- <th>{{ __('Action') }}</th> -->
                          </tr>
                      </thead>
                      <tbody>
                           
                          @foreach($records as $key => $value)
                          <tr>
                            <td>{{ $value->table_no}}</td>

                            <?php 
                              $newfilename = url('uploads/qrcode/temp').'/'.$value->qr_code;  
                            ?>
                            <td>
                              <div class="{{'html-content-holder-'.$value->id}}">
                                <img src="{{ $newfilename }}" height="100px" width="100px" /><p>Code: <?php echo $value->table_code; ?></p>
                              </div>
                              <a data-newdownload="{{'QR-'.$value->table_code.'.jpg'}}" href="javascript:void(0);" data-id="{{$value->id}}" onclick="convertHTML2Img(this)" class="btn-Convert-Html2Image" title="Download QR Code">
                                Download
                              </a>

                            </td>
                            <td>{{date('j F, Y', strtotime($value->created_at))}}</td>
                            <!-- <td><a href="{{ url('product/edit').'/'.$value->id }}" data-restaurant_id="{{$value->id}}" title="Edit Details" class="btn btn-primary btn-xs" ><span class="fa fa-edit"></span></a></td> -->
                          </tr>
                          @endforeach
                           <!-- <a href="javascript:void(0);" class="btn-Convert-Html2Image" title="Download QR Code">
                                Download
                              </a> -->
                      </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
    

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script type="text/javascript">
  $(document).ready( function () {    

    /*$(".btn-Convert-Html2Image").on('click', function () {
      html2canvas(document.querySelector(".html-content-holder"), {width: 150,height: 120}).then(canvas => {
          var imgageData = canvas.toDataURL("image/png");
           downloadImage(imgageData, 'my-canvas.jpeg');
      });
    });*/
    $('#restaurant_tables').DataTable();
  });

  function convertHTML2Img($this) {
    var imageName = $($this).attr('data-newdownload');
    var id = $($this).attr('data-id');
    html2canvas(document.querySelector(".html-content-holder-"+id), {width: 150,height: 120}).then(canvas => {
      var imgageData = canvas.toDataURL("image/jpg");
      downloadImage(imgageData, imageName);
    });
  }
  function downloadImage(data, filename = 'untitled.jpeg') {
      var a = document.createElement('a');
      a.href = data;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
  }
  /*$('#restaurant-table-form').on('submit', function(e) {
    
  });*/

  $(document).on('submit', "#restaurant-table-form",function(e){
    e.preventDefault();
    var _this=$(this); 
      $('#group_loader').fadeIn();
      var formData = new  FormData(this);
      var restro_id = $('#restro_id').val();
      $.ajax({
      url:'{{ url('restaurant/table_update') }}'+ "/" + restro_id,
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
            if (res.status === 1) { 
              toastr.success(res.message);
              $('#restaurant-table-form')[0].reset();
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


</script>
<!-- <script>
var ajax_datatable;
$(document).ready(function(){
$('#add_restaurant').parsley();
$('.select2').select2();
ajax_datatable = $('#restaurant_listing').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ url('restaurant/menu') }}',
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
      { data: 'name', name: 'name' },
      { data: 'products_type', name: 'products_type' },
      { data: 'created_at', name: 'created_at' },
      {data: 'id', name: 'id', orderable: false, searchable: false}
    ],
    order: [ [4, 'desc'] ],
    rowCallback: function(row, data, iDisplayIndex) {     
      //var links='';
      //var status = '';
      //links += `<div class="btn-group" role="group" >`;
      /*@can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/edit') }}/${data.id}" data-restaurant_id="${data.id}" title="Edit Details" class="btn btn-primary btn-xs edit_staff" ><span class="fa fa-edit"></span></a>`;
      @endcan*/
      /*@can('Restaurant-delete')
      links += `<a href="#" data-restaurant_id="${data.id}" title="Delete staff" class="btn btn-danger btn-xs delete_staff" ><span class="fa fa-trash"></span></a>`;
      @endcan*/
      /*@can('Restaurant-edit')
      links += `<a href="#" data-restaurant_id="${data.id}" title="View Details" class="btn btn-info btn-xs view_btn" ><span class="fa fa-eye"></span></a>`;
      @endcan*/
      /*@can('Restaurant-edit')
      links += `<a href="{{ url('restaurant/menu') }}/${data.id}" data-restaurant_id="${data.id}" title="View Menues" class="btn btn-success btn-xs view_menu" ><span class="fa fa-link"></span></a>`;
      @endcan*/ 
      /*links += `</div>`;
      if(data.status === 1){
        status += `<a href="#" data-restaurant_id="${data.id}" title="Active Account" data-status="deactive" class="change_status"><span class='label label-rounded label-success'>Active</span></a>`;
      }else{
        status += `<a href="#" data-restaurant_id="${data.id}" title="Deactive Account" data-status="active" class="change_status"><span class='label label-rounded label-warning'>Deactive</span></a>`;
      }*/
      //$('td:eq(4)', row).html(status);
      //$('td:eq(6)', row).html(links);
      },
});
</script> -->

@endsection
