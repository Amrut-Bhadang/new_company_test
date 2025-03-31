<form method="PUT" action="{{ url('api/banner/'.$banner->id) }}" id="edit_role">
    @csrf
    <input type="hidden" id="category_id" name="category_id" value="{{ $banner->category_id }}">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="main_category_id">Service*</label>
          <select name="main_category_id" id="main_category_id_edit" class="form-control" onchange="category_change2()"  data-parsley-required="true" >
            <option value="">---Select Service----</option>
            @foreach ($main_category as $cat)
                <option value="{{ $cat->id }}" {{ $banner->main_category_id == $cat->id?'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="col-md-12" for="category">Choose Category*</label>
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Category" {{ $banner->category_type=='Category'?'checked':'' }} name="category_type">Category
            </label>
          </div>
          <!-- <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Dish" {{ $banner->category_type=='Dish'?'checked':'' }} name="category_type">Product
            </label>
          </div> -->
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Link" {{ $banner->category_type=='Link'?'checked':'' }} name="category_type">Link
            </label>
          </div>
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Restaurant" {{ $banner->category_type=='Restaurant'?'checked':'' }} name="category_type">Store
            </label>
          </div>
          <!-- <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Gift" {{ $banner->category_type=='Gift'?'checked':'' }} name="category_type">Gift
            </label>
          </div> -->
        </div>
      </div>          
    </div>

    <div class="row categories_show_div2">

    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="valid_from">Valid From*</label>
          <input type="date" id="valid_from" min="<?php echo date('Y-m-d') ?>" name="valid_from" value="{{ $banner->valid_from }}" class="form-control" data-parsley-required="true" placeholder="Valid From" onkeydown="return false">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="valid_to">Valid To*</label>
          <input type="date" id="valid_to" min="<?php echo date('Y-m-d') ?>" name="valid_to" value="{{ $banner->valid_to }}" class="form-control" data-parsley-required="true" placeholder="Valid To" onkeydown="return false">
        </div>
      </div>
      <div class="col-md-6 link-div2">
        <label for="link">Link</label>
        <div class="form-group input-group">
          <input type="text" id="link" name="link" value="{{ $banner->link}}" class="form-control">
        </div>
      </div>
      <div class="col-md-6">
          <label for="image">Image</label>
        <div class="form-group">
          <div class="input-group">
            <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$banner->file_path}}"></div>
            <input type="file" id="editfile" name="image" class="form-control">
          </div>
          <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 1125x500</span>
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
      var category_id = $('#category_id').val();

      if (category_id) {
        category_change2(category_id);

      } else {
        category_change2();
      }
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
      url:'{{ url('api/banner/'.$banner->id) }}',
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
          $('#edit_role').parsley().reset();
          ajax_datatable.draw();
          location.reload();
          toastr.success(res.message);
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

function category_change2(category_id = '') {
  var main_category_id = $('#main_category_id_edit').val();

  if (main_category_id) {
    var category_type = $("input[name='category_type']:checked").val();

    if (category_type != 'Link') {
      $.ajax({
         url:'{{url('banner/show_category')}}/'+category_type+'/'+main_category_id+'/'+category_id,
         dataType: 'html',
         success:function(result)
         {
          $('.categories_show_div2').html(result);
          $('.link-div2').hide();
         } 
      });
    } else {
      $('.link-div2').show();
      $('.categories_show_div2').html('');
    }


  } else {
    $("input[name='category_type']:checked").prop('checked', false);
    toastr.error('Please choose main category first');
    return false;
  }
}
</script>