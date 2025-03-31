<?php
use App\Models\Language;
use App\Models\ToppingLang;
$language = Language::pluck('lang')->toArray();
?>
<form method="PUT" action="{{ url('api/holiday/'.$holiday->id) }}" id="edit_role">
    @csrf
    <div class="tab-content" style="margin-top:10px">
    </div>
        <div class="row">
          @if($user_type == 4)
            <input type="hidden" name="restaurant_id" value="{{$restaurant_id}}">
          @endif
          @if($user_type == 1)
          <div class="col-md-12">
            <div class="form-group"> 
              <label  for="restaurant_id">Store Name*</label>
              <select name="restaurant_id" class="form-control select2" style="width: 100%;" data-parsley-required="true" >
                  <option value=''>--Select Store--</option>
                  @foreach ($restaurant as $restaurant)
                      <option value="{{ $restaurant->id }}" {{ $restaurant->id == $holiday->restaurant_id?'selected':'' }}>{{ $restaurant->name }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          @endif
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label  for="restaurant_id">Holiday reason*</label>
              <textarea name="holiday_reason" class="form-control" id="holiday_reason" data-parsley-required="true">{{ $holiday->holiday_reason }}</textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label  for="start_date_time">Start Date*</label>
              <input type="text" id="start_date_time" value="{{ $holiday->start_date_time }}" name="start_date_time" class="form-control datetimepicker" data-parsley-required="true">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" id="price">
              <label  for="end_date_time">End Date*</label>
              <input type="text" id="end_date_time" value="{{ $holiday->end_date_time }}" name="end_date_time" class="form-control datetimepicker" data-parsley-required="true">
            </div>
          </div>
        </div>

    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>

</form>


<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

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
      url:'{{ url('api/holiday/'.$holiday->id) }}',
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
          $('.select2').val(null).trigger('change');
          $('.select3').val(null).trigger('change');
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

$(function () {
      $('.datetimepicker').datetimepicker({
          // Formats
          // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
          format: 'DD-MM-YYYY',

          // Your Icons
          // as Bootstrap 4 is not using Glyphicons anymore
          icons: {
              time: 'fa fa-clock-o',
              date: 'fa fa-calendar',
              up: 'fa fa-chevron-up',
              down: 'fa fa-chevron-down',
              previous: 'fa fa-chevron-left',
              next: 'fa fa-chevron-right',
              today: 'fa fa-check',
              clear: 'fa fa-trash',
              close: 'fa fa-times'
          }
      });
  });

$(document).on('change','.is_mandatory',function(e){
    e.preventDefault();
    //$('#music_category_type').hide();
    if($(this).val()=='0'){
      $('#edit_price').hide();
    } else {
      $('#edit_price').show();
    }
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