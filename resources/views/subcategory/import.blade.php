<?php
?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> -->
<form method="POST" action="{{ url('category/importData') }}" id="import_category_data" enctype="multipart/form-data">
    @csrf
    <div class="tab-content" style="margin-top:10px">
    </div>
        <div class="row">
          @if($user_type == 4)
            <input type="hidden" name="main_category_id" value="{{$main_category_id}}">
          @endif

          @if($user_type != 4)
            <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="popup_main_category_id">Service*</label>
                  <select name="main_category_id" id="popup_main_category_id" class="form-control multiple-search"  data-parsley-required="true" >
                    <option value="">---Select Service----</option>
                    @foreach ($main_category as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
          @endif

          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="file">Import Excel File</label>
              <input type="file" name="file" class="form-control">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group mt-btn">
               <a href="{{url('public/uploads/Category_import_sample.xlsx')}}" class="btn btn-success btn-lg" title="Demo Download"><i class="fas fa-file-excel" aria-hidden="true"></i> Sample File</a>
            </div>
          </div>
        </div>
                          

    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>

</form>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script> -->
<script>
$(document).ready(function(){
$('#edit_role').parsley();
$("#edit_role").on('submit',function(e){ 
  e.preventDefault();
  var _this=$(this); 
    var formData = new FormData(this);
    formData.append('_method', 'put');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
      url:'{{ url('api/category/') }}',
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
          $('#edit_role').parsley().reset();
          ajax_datatable.draw();
          $('.select2').val(null).trigger('change');
          $('.select3').val(null).trigger('change');
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