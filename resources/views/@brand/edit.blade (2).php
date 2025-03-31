<?php
use App\Models\Language;
use App\Models\BrandLang;
$language = Language::pluck('lang')->toArray();
?>

<form method="PUT" action="{{ url('api/brands/'.$brand->id) }}" id="edit_role">
    @csrf
    <!-- <ul class="nav nav-tabs">
          @foreach($language as $key => $lang)
          <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#edittab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
          @endforeach
      </ul> -->
      <div class="tab-content" style="margin-top:10px">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="brand_id">Service*</label>
              <select name="main_category_id" class="form-control"  data-parsley-required="true" >
                <option value="">---Select Service----</option>
                @foreach ($main_category as $cat)
                    <option value="{{ $cat->id }}" {{ $brand->main_category_id == $cat->id?'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="brand_id">Vendor Category*</label>
              <select name="brand_category" class="form-control"  data-parsley-required="true" >
                <option value="">---Select Category----</option>
                @foreach ($brand_category as $brand_cat)
                    <option value="{{ $brand_cat->id }}" {{ $brand->brand_category == $brand_cat->id?'selected':'' }}>{{ $brand_cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        @if($lang)
        <div class="row">
        @foreach($language as $key => $lang)
        <?php
          if(isset($brand))
          {
              $langData = BrandLang::where(['lang'=>$lang,'brand_id'=>$brand->id])->first();  
          } ?>
        <!-- <div id="edittab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
        
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name"> {{__('backend.name')}} ({{__('backend.'.$lang)}})*</label>
              <input type="text" name="name[{{$lang}}]" data-parsley-required="true" value="{{$langData->name}}" id="name" class="form-control" placeholder="Name"  />
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
        <label class="control-label" for="mobile">Mobile*</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
            <select name="country_code" class="form-control" style="width:180px" data-parsley-required="true" >
              @foreach ($country as $country)
                  <option value="{{ $country->phonecode }}"  {{ $brand->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
              @endforeach
            </select>
            </div>
            <input type="text" name="mobile"  value="{{$brand->mobile}}" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="8" data-parsley-maxlength="15"/>
          </div> 
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="email">Email*</label>
          <input type="text" name="email" value="{{$brand->email}}" id="email" class="form-control" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="password">Password*</label>
          <input type="password" name="password" value="" id="edit_password" class="form-control" placeholder="Password"  />
          <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="togglePassword"></i>
          <span class="text-muted">{{__('Leave blank if you don’t want to change password.')}}</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="confirm_password">Confirm Password*</label>
          <input type="password" name="confirm_password" id="edit_confirm_password" class="form-control" placeholder="Confirm Password" data-parsley-equalto="#edit_password"/>
          <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleRePassword"></i>
        </div>
      </div>
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

<script>
$(document).ready(function(){
  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#edit_password');

  const toggleRePassword = document.querySelector('#toggleRePassword');
  const edit_confirm_password = document.querySelector('#edit_confirm_password');

  togglePassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  toggleRePassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = edit_confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
    edit_confirm_password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

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
        url:'{{ url('api/brands/'.$brand->id) }}',
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