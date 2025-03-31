<?php
use App\Models\Language;
use App\Models\GiftBrandLang;
$language = Language::pluck('lang')->toArray();
?>

<form method="PUT" action="{{ url('api/gift_brand/'.$brand->id) }}" id="edit_role">
    @csrf
    <!-- <ul class="nav nav-tabs">
          @foreach($language as $key => $lang)
          <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#edittab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
          @endforeach
      </ul> -->
      <div class="tab-content" style="margin-top:10px">
    @if($lang)
    @foreach($language as $key => $lang)
    <?php
      if(isset($brand))
      {
          $langData = GiftBrandLang::where(['lang'=>$lang,'brand_id'=>$brand->id])->first();  
      } ?>
    <!-- <div id="edittab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
          <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="{{$langData->name}}" id="name" class="form-control" placeholder="Name"  />
        </div>
      </div>
    </div>
    <!-- </div> -->
    @endforeach
    @endif
    </div>
    <div class="row">
      <!-- <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="brand_name">Brand Name *</label>
          <input type="text" name="brand_name" value="{{ $brand->name }}" id="brand_name" class="form-control" placeholder="Brand Name" data-parsley-required="true"  />
        </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="brand_type">Brand Type *</label>
          <select name="brand_type" class="form-control" data-placeholder="Select brand type" style="width: 100%;" data-parsley-required="true" >
            <option>--Select Brand Type--</option>
            <option value="Restaurant" {{ $brand->brand_type=='Restaurant'?'selected':'' }}>Restaurant</option>
            <option value="Gift" {{ $brand->brand_type=='Gift'?'selected':'' }}>Gift</option>
          </select>
        </div>
      </div> -->
    <!-- </div>
    <div class="row"> -->
      <div class="col-md-6">
          <label for="image">Image</label>
        <div class="form-group">
          <div class="input-group">
            <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$brand->file_path}}"></div>
            <input type="file" id="editfile" name="image" class="form-control">
          </div>
          <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
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
      url:'{{ url('api/gift_brand/'.$brand->id) }}',
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


    
$("#editfile").change(function(){
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;
  
    var match = ["image/jpeg","image/png","image/jpg"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#editpreviewing').attr('src','images/no-image-available.png');
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
});

function imageIsLoaded(e){
			$("#editfile").css("color","green");
			$('#editpreviewing').attr('src',e.target.result);

		}
</script>