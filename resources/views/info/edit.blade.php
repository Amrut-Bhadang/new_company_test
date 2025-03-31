<?php
use App\Models\Language;
use App\Models\CategoryLang;
$language = Language::pluck('lang')->toArray();
?>
<form method="PUT" action="{{ url('api/category/'.$category->id) }}" id="edit_role">
    @csrf
    <!-- <ul class="nav nav-tabs">
          @foreach($language as $key => $lang)
          <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#edittab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
          @endforeach
      </ul> -->
      <div class="tab-content" style="margin-top:10px">
    @if($lang)
    <div class="row">
    @foreach($language as $key => $lang)
    <?php
      if(isset($category))
      {
          $langData = CategoryLang::where(['lang'=>$lang,'category_id'=>$category->id])->first();  
      } ?>
    <!-- <div id="edittab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
      <div class="col-md-6"> 
        <div class="form-group">
          <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}}) *</label>
          <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="{{$langData->name}}" id="name" class="form-control" placeholder="Name"  />
        </div>
      </div>
    @endforeach
    </div>
    @endif

    @if($lang)
    <div class="row">
    @foreach($language as $key => $lang)
    <?php
      if(isset($category))
      {
          $langData = CategoryLang::where(['lang'=>$lang,'category_id'=>$category->id])->first();  
      } ?>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}}) *</label>
          <!-- <input type="text" name="description[{{$lang}}]" data-parsley-required="true" value="{{$langData->description}}" id="description" class="form-control" placeholder="description"  /> -->
          <textarea name="description[{{$lang}}]" data-parsley-required="true" id="description" class="form-control" placeholder="Description">{{$langData->description}}</textarea>
        </div>
      </div>
    <!-- </div> -->
    @endforeach
    </div>
    @endif
    </div>
    <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-12" for="image">Image</label>
            <input type="file" id="editfile" name="image" class="form-control">
            <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$category->image}}"></div>
          </div>
        </div>
      </div>                   

    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>

</form>

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
      url:'{{ url('api/category/'.$category->id) }}',
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


    
$("#editfile").change(function(){
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;
  
    var match = ["image/jpeg","image/png","image/jpg"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      $('#editpreviewing').attr('src','images/image.png');
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