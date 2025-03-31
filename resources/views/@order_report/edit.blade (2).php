<form method="PUT" action="{{ url('api/discount/'.$discount->id) }}" id="edit_role">
    @csrf
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="col-md-12" for="category">Choose Category*</label>
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Category" {{ $discount->category_type=='Category'?'checked':'' }} name="category_type">Category
            </label>
          </div>
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Dish" {{ $discount->category_type=='Dish'?'checked':'' }} name="category_type">Dish
            </label>
          </div>
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Restaurant" {{ $discount->category_type=='Restaurant'?'checked':'' }} name="category_type">Restaurant
            </label>
          </div>
          <div class="form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" onchange="category_change2()" value="Gift" {{ $discount->category_type=='Gift'?'checked':'' }} name="category_type">Gift
            </label>
          </div>
        </div>
      </div>          
    </div>

    <div class="row categories_show_div2">

    </div>
   <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="discount_code">Discount Code</label>
              <input type="text" id="discount_code" name="discount_code" class="form-control" data-parsley-required="true" placeholder="Discount Code" value="{{ $discount->discount_code }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="discount_code">Percentage</label>
              <input type="text" id="percentage" name="percentage" class="form-control" data-parsley-required="true" placeholder="Percentage" value="{{ $discount->percentage }}">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="discount_code">Valid Upto</label>
              <input type="date" id="valid_upto" name="valid_upto" class="form-control" data-parsley-required="true" placeholder="Valid Upto" value="{{ $discount->valid_upto }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-md-6" for="no_of_use_per_user">No. Of Use Per User</label>
              <input type="text" id="no_of_use_per_user" name="no_of_use_per_user" class="form-control" data-parsley-required="true" placeholder="Number Of Use Per User" data-parsley-type="digits" value="{{ $discount->no_of_use_per_user }}">
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
      var category_id = "<?php echo $discount->id; ?>";

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


    

});



function category_change2(category_id = '') {

  var category_type = $("input[name='category_type']:checked").val();

   $.ajax({
     url:'{{url('discount/show_category')}}/'+category_type+'/'+category_id,
     dataType: 'html',
     success:function(result)
     {
      $('.categories_show_div2').html(result);
     } 
  });
}
</script>