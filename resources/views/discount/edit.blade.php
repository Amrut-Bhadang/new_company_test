<?php
use App\Models\Language;
use App\Models\DiscountLang;
$language = Language::pluck('lang')->toArray();
$login_user_data = auth()->user();
?>
<form method="PUT" action="{{ url('api/discount/'.$discount->id) }}" id="edit_role">
    @csrf
    <div class="row">
      @if($login_user_data->type == 4)
        <input type="hidden" class="form-check-input" value="Restaurant" name="category_type_edit">
        <input type="hidden" class="form-check-input" value="{{ $restaurant_id }}" name="category_id[]">
      @endif

      @if($login_user_data->type != 4)
        <div class="col-md-12">
          <div class="form-group">
            <label class="col-md-12" for="category">Choose Category*</label>
            <!-- <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" onchange="category_change2()" value="Category" {{ $discount->category_type=='Category'?'checked':'' }} name="category_type">Category
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" onchange="category_change2()" value="Dish" {{ $discount->category_type=='Dish'?'checked':'' }} name="category_type">Dish
              </label>
            </div> -->
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" onchange="category_change2()" value="Restaurant" {{ $discount->category_type=='Restaurant'?'checked':'' }} name="category_type_edit">Store
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" onchange="removeCategory2()" value="Flat-Discount" {{ $discount->category_type=='Flat-Discount'?'checked':'' }} name="category_type_edit">Flat Discount
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" onchange="infoSelection2()" value="Info" {{ $discount->category_type=='Info'?'checked':'' }} name="category_type_edit">Info Only
              </label>
            </div>
            <!-- <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input" onchange="category_change2()" value="Gift" {{ $discount->category_type=='Gift'?'checked':'' }} name="category_type_edit">Gift
              </label>
            </div> -->
          </div>
        </div>
      @endif
    </div>

    <div class="row categories_show_div2">

    </div>

    @if($login_user_data->type != 4)
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="discount_code">Select Country*</label>
            <select name="country_ids[]" id="country_ids" class="form-control select4" multiple="multiple"  data-placeholder="Select Country" data-dropdown-css-class="select2-primary">
               <option value="">--Select Country--</option>
               @foreach ($country as $country)
               <option value="{{ $country->id }}" <?php echo (in_array($country->id, $DiscountCountries))?'selected':''; ?> >{{ $country->name }}</option>
               @endforeach
              </select>
          </div>
        </div>
      </div>
    @endif
      <div class="row">
        @foreach($language as  $key => $lang)
        <?php
        if(isset($discount))
        {
            $langData = DiscountLang::where(['lang'=>$lang,'discount_id'=>$discount->id])->first(); 
            // dd($langDatalangDatalangData); 
        } ?>
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">{{__('backend.title')}} ({{__('backend.'.$lang)}})*</label>
              <input type="text" id="title" name="title[{{$lang}}]" class="form-control" data-parsley-required="true" value="{{ $langData->title }}" placeholder="Title">
            </div>
          </div>
        @endforeach
        </div>
        <div class="row">
          <div class="col-md-6 discount_code">
            <div class="form-group">
              <label for="discount_code">Discount Code</label>
              <input type="text" id="discount_code" name="discount_code" class="form-control" placeholder="Discount Code" value="{{ $discount->discount_code }}">
            </div>
          </div>
          <div class="col-md-6 percentage">
            <div class="form-group">
              <label for="percentage">Percentage</label>
              <input type="text" id="percentage" name="percentage" class="form-control" data-parsley-type="digits" placeholder="Percentage" value="{{ $discount->percentage }}">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 no_of_use">
            <div class="form-group">
              <label for="no_of_use">No. Of Use*</label>
              <input type="text" id="no_of_use" name="no_of_use" class="form-control" placeholder="Number Of Use" data-parsley-type="digits" value="{{ $discount->no_of_use }}">
            </div>
          </div>
          <div class="col-md-6 no_of_use_per_user">
            <div class="form-group">
              <label for="no_of_use_per_user">No. Of Use Per User</label>
              <input type="text" id="no_of_use_per_user" name="no_of_use_per_user" class="form-control" placeholder="Number Of Use Per User" data-parsley-type="digits" value="{{ $discount->no_of_use_per_user }}">
            </div>
          </div>
        </div>

         <div class="row">
          <div class="col-md-6 min_order_amount">
            <div class="form-group">
              <label for="min_order_amount">Min. Order amount(QAR) </label>
              <input type="number" id="min_order_amount" name="min_order_amount"  value="{{ $discount->min_order_amount }}" class="form-control" placeholder="Min Order Amount" >
            </div>
          </div>
          <div class="col-md-6 max_discount_amount">
            <div class="form-group">
              <label for="max_discount_amount"> Max. discount amount(QAR) </label>
              <input type="number" id="max_discount_amount" name="max_discount_amount"  value="{{ $discount->max_discount_amount }}" class="form-control" placeholder=" Max Discount Amount " data-parsley-type="digits">
            </div>
          </div>
        </div>

        <div class="row">
          @foreach($language as  $key => $lang)
            <?php
            if(isset($discount))
            {
                $langData = DiscountLang::where(['lang'=>$lang,'discount_id'=>$discount->id])->first(); 
                // dd($langDatalangDatalangData); 
            } ?>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="discount_code">{{__('backend.description')}} ({{__('backend.'.$lang)}})*</label>
                  <input type="text" id="description" name="description[{{$lang}}]" class="form-control" data-parsley-required="true" value="{{ $langData->description }}" placeholder="Description">
                </div>
              </div>
          @endforeach
          <!-- <div class="col-md-12">
            <div class="form-group">
              <label for="discription">Description</label>
               <textarea id="description" class="form-control ckeditor" name="description" data-parsley-required="true" placeholder="Description">{{ $discount->description }}</textarea>
            </div>
          </div> -->
        </div>

        <div class="row input-daterange">
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid From*</label>
              <input type="text" name="valid_from" id="valid_from" class="form-control" placeholder="From Date" value="{{ $discount->valid_from }}" readonly />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid Upto*</label>
              <input type="text" name="valid_upto" id="valid_upto" class="form-control" placeholder="To Date" value="{{ $discount->valid_upto }}" readonly />
            </div>
          </div>
        </div>

      <div class="row">
        <!-- <div class="col-md-6">
            <div class="form-group">
              <label for="discount_code">Valid Upto</label>
              <input type="date" id="valid_upto" name="valid_upto" class="form-control" data-parsley-required="true" placeholder="Valid Upto" value="{{ $discount->valid_upto }}" onkeydown="return false">
            </div>
        </div> -->
        <div class="col-md-6">
          <label for="image">Image</label>
          <div class="form-group">
            <div class="input-group">
              <div id="image_preview"><img height="100" width="100" id="editpreviewing" src="{{$discount->image}}"></div>
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
      $('.select4').select2();
      $('.input-daterange').datepicker({
        todayBtn:'linked',
        format:'yyyy-mm-dd',
        autoclose:true
      });
      var category_id = "<?php echo $discount->id; ?>";

      if (category_id) {
        category_change2(category_id);

      } else {
        category_change2();
      }
      infoSelection2();
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
      url:'{{ url('api/discount/'.$discount->id) }}',
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

function category_change2(category_id = '') {

  var category_type = $("input[name='category_type_edit']:checked").val();
  $('.categories_show_div2').show();
  $('.discount_code').show();
  $('.percentage').show();
  $('.no_of_use').show();
  $('.no_of_use_per_user').show();
  $('.min_order_amount').show();
  $('.max_discount_amount').show();
   $.ajax({
     url:'{{url('discount/show_category')}}/'+category_type+'/'+category_id,
     dataType: 'html',
     success:function(result)
     {
      $('.categories_show_div2').html(result);
     }
  });
}
function removeCategory2() {
  $('.categories_show_div2').hide();
  $('.discount_code').show();
  $('.percentage').show();
  $('.no_of_use').show();
  $('.no_of_use_per_user').show();
  $('.min_order_amount').show();
  $('.max_discount_amount').show();
}
function infoSelection2() {
  var category_type = $("input[name='category_type_edit']:checked").val();

  if (category_type == 'Info') {
    $('.categories_show_div2').hide();
    $('.discount_code').hide();
    $('.percentage').hide();
    $('.no_of_use').hide();
    $('.no_of_use_per_user').hide();
    $('.min_order_amount').hide();
    $('.max_discount_amount').hide();
  }
}
</script>