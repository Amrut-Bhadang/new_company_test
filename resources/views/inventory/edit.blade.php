
<form method="PUT" action="{{ url('api/inventory/'.$inventory->id) }}" id="edit_role">
    @csrf
   <input type="hidden" id="gift_id" name="gift_id" value="{{ $inventory->gift_id }}">
      <div class="tab-content inventory_sec" style="margin-top:10px">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="category_id">Gift Category*</label>
              <select name="gift_category_id" class="form-control " id="gift_category_id" onchange="get_gifts2()" data-parsley-required="true">
                <option value="">---Select Gift Category----</option>
                @foreach ($gift_category as $category_list)
                    <option value="{{ $category_list->id }}" {{$category_list->id == $inventory->gift_category_id ? 'selected' : ''}} >{{ $category_list->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-6 show_gifts2">
              <div class="form-group">
                <label class="control-label" for="category_id">Gifts*</label>
                <select name="gift_id" class="form-control " data-placeholder="Select Gift" style="width: 100%;" data-parsley-required="true" >
                  <option value="">--Select Gift--</option>
                  @foreach ($get_gift as $record)
                      <option value="{{ $record->id }}" {{$record->id == $inventory->gift_id ? 'selected' : ''}}>{{ $record->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
        </div>
    
        <div class="row">
          <!-- <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name"> {{__('backend.points')}}*</label>
              <input type="text" name="price" data-parsley-required="true" value="{{ $inventory->price }}" id="price" class="form-control" placeholder="Points"  />
            </div>
          </div> -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name"> {{__('backend.quantity')}}*</label>
              <input type="text" name="quantity" data-parsley-required="true" value="{{ $inventory->quantity }}" id="quantity" class="form-control" placeholder="Quantity"  />
            </div>
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
$('.select2').select2();
$('.select3').select2();
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
      url:'{{ url('api/inventory/'.$inventory->id) }}',
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
      if(imageSize < 1000000){
        var reader = new FileReader();
        reader.onload = imageIsLoaded;
        reader.readAsDataURL(this.files[0]);
      }else{
        toastr.error('Images Size Too large Please Select 1MB File!!');
        return false;
      }

    }

  });
});

function imageIsLoaded(e){
			console.log(e);
			$("#file").css("color","green");
			$('#editpreviewing').attr('src',e.target.result);

		}
function get_gifts2(){
   var gift_category_id = $("#gift_category_id").val();
   //alert(gift_category_id);

   $.ajax({
     url:'{{url('inventory/get_gifts')}}/'+gift_category_id,
     dataType: 'html',
     success:function(result)
     {
      $('.show_gifts2').html(result);
     }
  });
}    
</script>