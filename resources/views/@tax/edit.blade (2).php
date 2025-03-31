<form method="PUT" action="{{ url('api/tax/'.$tax->id) }}" id="edit_role">
    @csrf
    <div class="tab-content" style="margin-top:10px">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="tax_id">Country*</label>
            <select name="country_id" class="form-control"  data-parsley-required="true" >
              <option value="">---Select Country----</option>
              @foreach ($countriesData as $cat)
                  <option value="{{ $cat->id }}" {{ $tax->country_id == $cat->id?'selected':'' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="tax_id">Currency*</label>
            <select name="currency_id" id="currency_edit_id" onchange="changeEditCurrency()" class="form-control"  data-parsley-required="true" >
              <option value="">---Select Currency----</option>
              @foreach ($currencyData as $currency)
                  <option data-currency_code="{{ $currency->currency_code }}" value="{{ $currency->id }}" {{ $tax->currency_id == $currency->id?'selected':'' }}>{{ $currency->currency_code.'('.$currency->currency_name.')' }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="tax">Tax (In %)*</label>
            <input type="text" name="tax" data-parsley-required="true" value="{{$tax->tax}}" id="tax" min="0" max="100" class="form-control" placeholder="Tax"  />
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="tax_id">Difference Amount(1 USD = <span class="converted_currency"></span>)*</label>
            <input type="text" name="difference_amount" data-parsley-required="true" value="{{$tax->difference_amount}}" id="difference_amount" class="form-control" placeholder="Difference Amount"  />
          </div>
        </div>
      </div>
    </div>
    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
</form>

<script>
$(document).ready(function(){
  changeEditCurrency();
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
      url:'{{ url('api/tax/'.$tax->id) }}',
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

function changeEditCurrency() {
  var currency_code = $('#currency_edit_id').find(':selected').attr('data-currency_code');
  $('.converted_currency').text(currency_code);
  // alert($('#currency_id').data('currency_code'));
}
</script>