<?php
?>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">  action="{{ url('product/importData') }}"-->
<form method="POST" id="import_dish_data" enctype="multipart/form-data">
    @csrf
    <div class="tab-content" style="margin-top:10px">
    </div>
        <div class="row">
          <input type="hidden" class="popup_product_for" name="product_for" value="">
          <input type="hidden" class="popup_main_category_id" id="popup_main_category_id" name="main_category_id" value="{{$main_category_id}}">

          <div class="col-md-6 show_brandDiv">
              <div class="form-group">
                <label class="control-label" for="brand_id">Brand*</label>
                <select name="brand_id" id="brand_id" class="form-control multiple-search" onchange="getRestro()" data-parsley-required="true" >
                  <option value="">---Select Brand----</option>
                  @foreach ($brandData as $brand)
                      <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
              </div>
          </div>
          <div class="col-md-6 show_popup_restroDiv">
            <div class="form-group">
                <label class="control-label restaurant_label" for="restaurant_id">Store*</label>
                <select name="restaurant_id" id="restaurant_id" class="form-control celebrityPrice select3" data-placeholder="Select" style="width: 100%;">
                    <option value=''>--Select--</option>
                </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="file">Import Excel File</label>
              <input type="file" name="file" id="file" onchange="excelChange(this)" class="form-control">
            </div>
          </div>

          <div class="col-md-6">
            <label for="file">Import Images Zip</label>
            <div class="form-group">
              <div class="input-group">
                <input type="file" name="images_zip" onchange="zipChange(this)" class="form-control">
              </div>
              <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group mt-btn custom_cls">
               <a href="{{url('public/uploads/Kilopoint_Restaurant_Demo_Upload.xlsx')}}" class="btn btn-success btn-lg" title="Demo Download"><i class="fas fa-file-excel" aria-hidden="true"></i> Sample File</a>
               <!-- <a href="javascript:void(0);" onclick="getProductSampleImport()" class="btn btn-success btn-lg" title="Demo Download"><i class="fas fa-file-excel" aria-hidden="true"></i> Sample File</a> -->
            </div>
          </div>
        </div>
                          

    <hr style="margin: 1em -15px">
    <div class="progress">
        <div class="progress-bar bar percent">0%</div>
    </div> 
    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save" id="save_btn"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>

</form>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script type="text/javascript">
  var bar = $('.bar');
  var percent = $('.percent');
  $("#import_dish_data").on('submit',function(e){
  e.preventDefault();
  
  var _this=$('#import_dish_data'); 
    var formData = new FormData(this);
    var percentComplete = 0;
   
    $.ajax({
        xhr: function() {
          var xhr = new window.XMLHttpRequest();

          xhr.upload.addEventListener("progress", function(evt) {

            if (evt.lengthComputable) {
              percentComplete = evt.loaded / evt.total;
              percentComplete = parseInt(percentComplete * 100);
              console.log(percentComplete);

              if (percentComplete === 100) {
                /*var percentVal = percentComplete + '%';
                bar.width(percentVal);
                percent.html(percentVal);*/
              }

            }
          }, false);

          return xhr;
        },
        url:'{{ url('product/importData') }}',
        dataType:'json',
        data:formData,
        type:'POST',
        cache:false,
        contentType: false,
        processData: false,
        beforeSend: function (){
          before(_this)
          var percentVal = '0%';
          bar.width(percentVal);
          percent.html(percentVal);
        },
        
        // hides the loader after completion of request, whether successfull or failor.
        complete: function (){
          complete(_this)
        },
        success:function(res){

            if (res.status === 1) {
              var percentVal = percentComplete + '%';
              bar.width(percentVal);
              percent.html(percentVal);


              setTimeout(function(){
                toastr.success(res.message);
                window.location.reload();
              }, 1000);

            }else{
              toastr.error(res.message);
            }
        },
        error:function(jqXHR,textStatus,textStatus) {

          if (jqXHR.responseJSON.errors) {

            $.each(jqXHR.responseJSON.errors, function( index, value ) {
              toastr.error(value)
            });

          } else {
            toastr.error(jqXHR.responseJSON.message)
          }
        }
      });
      return false;   
    });
</script>
<script type="text/javascript">
  
  function excelChange($this){
    var fileObj = $this.files[0];
    var imageFileType = fileObj.type;
    console.log(imageFileType);
    var imageSize = fileObj.size;
  
    var match = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#previewing').attr('src','images/image.png');
      toastr.error('Please select a valid excel file <br> Note: Only .xlsx file type allowed!!');
      return false;
    }
    
  };

  function zipChange($this){
    var fileObj = $this.files[0];
    var imageFileType = fileObj.type;
    console.log(imageFileType);
    var imageSize = fileObj.size;
  
    var match = ["application/zip"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#previewing').attr('src','images/image.png');
      toastr.error('Please select a valid zip file <br> Note: Only .zip file type allowed!!');
      return false;
    }
    
  };
</script>