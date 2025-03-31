<form method="PUT" action="{{ url('api/gift_banner/'.$banner->id) }}" id="edit_gift_banner">
    @csrf
    <input type="hidden" id="category_id" name="category_id" value="{{ $banner->category_id }}">
    <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="category">Gift Category*</label>
              <select name="gift_category_id" class="form-control gift_category_id select2"  data-placeholder="Select Category" style="width: 100%;" data-parsley-required="true" >
                <option value="">--Select Category--</option>
                @foreach ($categories_list as $category)
                    <option value="{{ $category->id }}" {{ $category->id== $banner->gift_category_id?'selected':'' }}>{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

    <div class="row">
      <div class="col-md-6">
          <label for="image">Image</label>
        <div class="form-group">
          <div class="input-group">
            <input type="file" id="editfile" name="image" class="form-control">
            <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$banner->file_path}}"></div>
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

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

<script>
  $(document).ready(function(){
  $('.select2').select2();
  $('.select3').select2();
  $('#edit_gift_banner').parsley();
  $("#edit_gift_banner").on('submit',function(e){
    //alert('helo');
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
        url:'{{ url('api/gift_banner/'.$banner->id) }}',
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
            $('#edit_gift_banner').parsley().reset();
            window.location.href = "{{route('gift_banner')}}";
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
        toastr.error('Images Size Too large Please SelectLess Than 5MB File!!');
        return false;
      }

    }

  });
});

function imageIsLoaded(e){
			$("#editfile").css("color","green");
			$('#editpreviewing').attr('src',e.target.result);

		}

/*function category_change2(category_id = '') {

  var category_type = $("input[name='category_type']:checked").val();

   $.ajax({
     url:'{{url('banner/show_category')}}/'+category_type+'/'+category_id,
     dataType: 'html',
     success:function(result)
     {
      $('.categories_show_div2').html(result);
     }
  });
}*/
</script>