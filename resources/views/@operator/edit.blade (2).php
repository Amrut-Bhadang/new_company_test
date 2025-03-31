<style type="text/css">
  form i {
    margin-left: -30px;
    cursor: pointer;
    position: absolute;
    top: 34px;
  }
</style>
<form method="PUT" action="{{ url('api/operator/'.$users->id) }}" id="edit_role">
    @csrf
    <div class="row">

      @if($user_type == 4)
        <input type="hidden" name="restaurant_id" id="restro_login_id" value="{{$restaurant_id}}">
      @endif

      @if($user_type == 1)
        <div class="col-md-12">
          <div class="form-group">
            <label class="control-label" for="first_name">Select Store*</label>
            <select name="restaurant_id" id="restaurant_id" class="form-control select2"  data-placeholder="Select Store" data-dropdown-css-class="select2-primary">
              <option value="">--Select Store--</option>

              @foreach ($restaurants as $rest)
                <option {{$rest->id == $users->restaurant_id ? 'selected' : ''}} value="{{ $rest->id }}">{{ $rest->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      @endif
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="first_name">First Name*</label>
          <input type="text" name="first_name" value="{{$users->first_name}}" id="first_name" class="form-control" placeholder="First Name" data-parsley-required="true"  />
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="last_name">Last Name*</label>
          <input type="text" name="last_name" value="{{$users->last_name}}" id="last_name" class="form-control" placeholder="Last Name" data-parsley-required="true"  />
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
        <label class="control-label" for="mobile">Mobile*</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
            <select name="country_code" class="form-control" style="width:180px" data-parsley-required="true" >
              @foreach ($country as $country)
                  <option value="{{ $country->phonecode }}"  {{ $users->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
              @endforeach
            </select>
            </div>
            <input type="text" name="mobile"  value="{{$users->mobile}}" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="8" data-parsley-maxlength="15"/>
          </div> 
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="email">Email*</label>
          <input type="text" name="email" value="{{$users->email}}" id="email" class="form-control" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="password">Password*</label>
          <input type="password" name="password" value="" id="edit_password" class="form-control" placeholder="Password"  />
          <i class="fa fa-eye" id="togglePassword"></i>
          <span class="text-muted">{{__('Leave blank if you don’t want to change password.')}}</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="confirm_password">Confirm Password*</label>
          <input type="password" name="confirm_password" id="edit_confirm_password" class="form-control" placeholder="Confirm Password" data-parsley-equalto="#edit_password"/>
          <i class="fa fa-eye" id="toggleRePassword"></i>
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

  $('.select2').select2();
  $('#edit_role').parsley();
  $("#edit_role").on('submit',function(e){ 
    e.preventDefault();
    var _this=$(this); 
      $('#group_loader').fadeIn();
      var values = $('#edit_role').serialize();
      $.ajax({
      url:'{{ url('api/operator/'.$users->id) }}',
      dataType:'json',
      data:values,
      type:'PUT',
      beforeSend: function (){before(_this)},
      // hides the loader after completion of request, whether successfull or failor.
      complete: function (){complete(_this)},
      success:function(result){
            toastr.success(`Operator ${result.name} has been Updated!`)
            setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
            $('#edit_role').parsley().reset();
            ajax_datatable.draw();
            window.location.reload();

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