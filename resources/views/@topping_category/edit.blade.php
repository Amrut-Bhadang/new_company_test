<?php
use App\Models\Language;
use App\Models\ToppingCategoryLang;
$language = Language::pluck('lang')->toArray();
?>
<form method="PUT" action="{{ url('api/topping_category/'.$category->id) }}" id="edit_role">
    @csrf
    <!-- <ul class="nav nav-tabs">
          @foreach($language as $key => $lang)
          <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#edittab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
          @endforeach
      </ul> -->
      <div class="tab-content" style="margin-top:10px">
        <div class="row">
        <div class="col-md-12">
            <div class="form-group">
              <label for="price_reflect_on">Topping Choose</label>
              <select name="topping_choose" class="form-control multiple-search" style="width: 100%;" data-parsley-required="true" >
                  <option value=''>--Select Topping Choose--</option>
                  <option value="0" {{ $category->topping_choose=='0'?'selected':'' }}>Single</option>
                  <option value="1" {{ $category->topping_choose=='1'?'selected':'' }}>Multiple</option>
              </select>
            </div>
          </div>
      </div>
    @if($lang)
    @foreach($language as $key => $lang)
    <?php
      if(isset($category))
      {
          $langData = ToppingCategoryLang::where(['lang'=>$lang,'topping_id'=>$category->id])->first();
      } ?>
    <!-- <div id="edittab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}}) *</label>
          <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="{{$langData->name}}" id="name" class="form-control" placeholder="Name"  />
        </div>
      </div>
    </div>
    <!-- </div> -->
    @endforeach
    @endif
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
      url:'{{ url('api/topping_category/'.$category->id) }}',
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