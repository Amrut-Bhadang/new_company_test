
@if($category)
<div class="row">
 <div class="col-md-12">
	<div class="table-responsive">
        <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
              <tr>
                  <td><strong>Order No:</strong></td>
                  <td>{{ $order->order_no }}</td>
              </tr>
              <tr>
                  <td><strong>Message:</strong></td>
                  <td>{!! $category->message !!}</td>
              </tr>
              <tr>
                <td><strong>Is Reply:</strong></td>
                <td>
                    @if($category->is_reply  === 1)
                        Yes
                    @else
                        No
                    @endif
                </td>
               </tr>
               <tr>
                <td><strong>Status:</strong></td>
                <td>
                    @if($category->status  === 1)
                        Active
                    @else
                        Deactive
                    @endif
                </td>
               </tr>

              @if($category->replied_message)
                <tr>
                    <td><strong>Reply:</strong></td>
                    <td>{!! $category->replied_message !!}</td>
                </tr>
              @endif
        </table>
	</div>
        @if(!$category->replied_message)
          <form method="PUT" action="{{ url('api/order_report/'.$category->id) }}" id="edit_role">
            @csrf
            <td><strong>Reply:</strong></td>
            <td><textarea class="form-control" name="replied_message"></textarea></td>
            
            <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
                style="display: none;" role="status" aria-hidden="true"></span> Send</button>
          </form>
        @endif
  </div>      
  </div>

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
    url:'{{ url('api/order_report/'.$category->id) }}',
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
</script>
@endif
