<form method="PUT" action="{{ url('admin/api/roles/'.$role->id) }}" id="edit_role">
    @csrf
    <div class="form-group">
        <label class="control-label" for="name">Name*</label>
        <input type="text" name="name" value="{{$role->name}}" id="name" class="form-control" placeholder="Name"
            data-parsley-required="true" />
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
    $('#group_loader').fadeIn();
    var values = $('#edit_role').serialize();
    $.ajax({
    url:'{{ url('admin/api/roles/'.$role->id) }}',
    dataType:'json',
    data:values,
    type:'PUT',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
          toastr.success(`Role ${result.name} has been updated!`)
          setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
          $('#edit_role').parsley().reset();
          ajax_datatable.draw();
      },
    error:function(jqXHR,textStatus,textStatus){
      toastr.error(jqXHR.responseJSON.message)
    }
      });
      return false;   
    });
});
</script>