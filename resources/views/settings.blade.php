@extends('layouts.master')

@section('content')
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('backend.Settings') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('backend.Settings') }}</li>
                
            </ol>
            <a href="#" class="btn btn-info d-none d-lg-block m-l-15" title="{{ __('Change Password') }}" data-toggle="modal" data-target="#change_passwords" ><i class="fa fa-plus"></i> {{ __('backend.Change_Password') }}</a>
               <!--   <a href="#" class="btn btn-info d-none d-lg-block m-l-15" title="{{ __('Email Settings') }}" data-toggle="modal" data-target="#email_change" ><i class="fa fa-plus"></i> {{ __('Email Settings') }}</a> -->
        </div>
    </div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content profile_edit">
<div class="row">
<!-- Column -->
<div class="col-lg-4 col-xlg-3 col-md-5">
    <div class="card">
        <div class="card-body">
            <center class="m-t-30">       
                <img src="{{Auth::user()->image}}" class="img-circle" width="150">
                <h4 class="card-title m-t-10">{{ucwords(Auth::user()->name)}}</h4>
            </center>
        </div>
        <div>
            <hr> </div>
        <div class="card-body"> <small class="text-muted">{{ __('backend.Email_Address') }} </small>
            <h6>{{Auth::user()->email}}</h6> <small class="text-muted p-t-30 db">{{ __('backend.Mobile') }}</small>
            <h6>{{Auth::user()->mobile}}</h6> 
            
        </div>
    </div>
</div>
<!-- Column -->
<!-- Column -->
<div class="col-lg-8 col-xlg-9 col-md-7">
    <div class="card">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs profile-tab" role="tablist">
            <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#settings" role="tab" aria-selected="true">{{ __('backend.Settings') }}</a> </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content edit_profile_sec">
            <div class="tab-pane active" id="settings" role="tabpanel">
            <form method="POST" action="{{ url('admin/saveProfile') }}" id="save_profile">
            @csrf
             <div class="card-body">
                <div class="form-group">
                    <label for="first_name">{{ __('backend.First_Name') }}*</label>
                    <div>
                        <input type="text" placeholder="{{ __('backend.First_Name') }}" id="first_name" value="{{Auth::user()->first_name}}" name="first_name" class="form-control form-control-line" data-parsley-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name">{{ __('backend.Last_Name') }}*</label>
                    <div>
                        <input type="text" placeholder="{{ __('backend.Last_Name') }}" id="first_name" value="{{Auth::user()->last_name}}" name="last_name" class="form-control form-control-line" data-parsley-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">{{ __('backend.Email_Address') }}*</label>
                    <div>
                        <input type="text" value="{{Auth::user()->email}}" id="email" name="email" placeholder="{{ __('backend.Email_Address') }}" class="form-control form-control-line">
                    </div>
                </div>
                <div class="form-group">
                    <label for="mobile">{{ __('backend.Mobile') }}*</label>
                    <div class="row">
                      <div class="col-md-4">
                        <select name="country_code" class="form-control" data-parsley-required="true" >
                          @foreach ($country as $country)
                              <option value="{{ $country->phonecode }}" {{ Auth::user()->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-8">
                        <input type="text" id="mobile" name="mobile" placeholder="{{ __('backend.Mobile') }}" value="{{Auth::user()->mobile}}" class="form-control form-control-line"  data-parsley-required="true">
                      </div>
                    </div>
                    
                </div>
				<div class="row">
					<div class="col-md-12">
					<label for="image">{{ __('backend.Image') }}</label>
					<div class="form-group input-group">
					  <div id="image_preview"><img id="previewing" src="{{ URL::asset('images/image.png')}}"></div>
					  <!-- <input type="file" id="file" name="image" class="form-control"> -->
            <!-- <label for="files" >{{__('backend.Select_Image')}}</label>
            <input type="file" id="file" name="image" style="visibility:hidden;" class="form-control"> -->
            <div class="form-control" onclick="document.getElementById('file').click()">
                <label for="files" >{{__('backend.Select_Image')}}</label>
                <input type="file" id="file" name="image" style="visibility:hidden;" class="form-control">
            </div>
					</div>
					</div>
				</div>
                <div class="form-group">
                    <label class="col-md-12" for="mobile"></label>
                    <div class="col-md-12">
                    <button type="submit" class="btn btn-info"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{ __('backend.Save') }}</button>
                    </div>
                </div>
                
             </div>
             </form>
            </div>
        </div>
    </div>
</div>
<!-- Column -->
</div>
</div>


<div class="modal fade setting_change_password" id="change_passwords">
        <div class="modal-dialog">
          <div class="modal-content">
          <form method="POST" action="{{ url('admin/changePassword') }}" id="change_password">
          @csrf
            <div class="modal-header">
              <h4 class="modal-title">{{ __('backend.Password_Settings') }}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body password-error">
              <div class="form-group">
                <label class="control-label" for="name">{{ __('backend.Current_Password') }}*</label>
                <input type="password" name="current_password" value="" id="current_password" class="form-control" placeholder="{{ __('backend.Current_Password') }}" data-parsley-required="true"  />
                <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px; right: 10px;" id="toggleCurrentPassword"></i>
              </div>
              <div class="form-group">
                <label class="control-label" for="name">{{ __('backend.New_Password') }}*</label>
                <!-- <input type="password" name="new_password" value="" id="new_password" class="form-control" placeholder="{{ __('backend.New_Password') }}" data-parsley-required="true" data-parsley-pattern="/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/" data-parsley-pattern-message="{{__('backend.strong_password')}}" /> -->
                <input type="password" name="new_password" value="" id="new_password" class="form-control" placeholder="{{ __('backend.New_Password') }}" data-parsley-required="true" />
                <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px; right: 10px;" id="toggleNewPassword"></i>
              </div>
              <div class="form-group">
                <label class="control-label" for="name">{{ __('backend.Confirm_Password') }}*</label>
                <input type="password" name="confirm_password" value="" id="confirm_password" class="form-control" placeholder="{{ __('backend.Confirm_Password') }}" data-parsley-required="true" data-parsley-equalto="#new_password" data-parsley-equalto-message="{{__('backend.new_password_equalto')}}" />
                <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px; right: 10px;" id="toggleConfirmPassword"></i>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('backend.Close') }}</button>
               <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{ __('backend.Save') }}</button>
            </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
      <div class="modal fade" id="email_change">
        <div class="modal-dialog">
          <div class="modal-content">
          <form method="POST" action="{{ url('/sendVerificationLink') }}" id="change_email">
            @csrf
            <div class="modal-header">
              <h4 class="modal-title">{{ __('Email Settings') }}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label" for="email">Email*</label>
                
                <input type="text" name="email" value="{{Auth::user()->email}}" id="email" class="form-control" placeholder="Email" data-parsley-required="true"  />
              </div>
              <div class="form-group">
                <label class="control-label" for="name">Current Password*</label>
                <input type="password" name="current_password" value="" id="current_password" class="form-control" placeholder="Current Password" data-parsley-required="true"  />
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
            </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    <!-- /.content -->

<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
$(document).ready(function(){

  const toggleNewPassword = document.querySelector('#toggleNewPassword');
  const newPassword = document.querySelector('#new_password');

  const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
  const confirm_password = document.querySelector('#confirm_password');

  const toggleCurrentPassword = document.querySelector('#toggleCurrentPassword');
  const current_password = document.querySelector('#current_password');

  toggleNewPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    newPassword.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  toggleConfirmPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
    confirm_password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  toggleCurrentPassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = current_password.getAttribute('type') === 'password' ? 'text' : 'password';
    current_password.setAttribute('type', type);
    // toggle the eye / eye slash icon
    this.classList.toggle('fa-eye-slash');
  });

  /*$('.input-daterange').datepicker({
    todayBtn:'linked',
    format:'yyyy-mm-dd',
    autoclose:true
  });*/

$('#change_email').parsley();
$('#change_password').parsley();
$('#save_profile').parsley();

$("#change_email").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var values = $('#change_email').serialize();
    $.ajax({
    url:'{{ url('sendVerificationLink') }}',
    dataType:'json',
    data:values,
    type:'POST',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
        if(result.status){
          toastr.success(result.message)
          $('#change_email')[0].reset();
          $('#change_email').parsley().reset();
        }else{
          toastr.error(result.message)
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

$("#change_password").on('submit',function(e){
  e.preventDefault();
  var _this=$(this); 
    var values = $('#change_password').serialize();
    $.ajax({
    url:'{{ url('admin/changePassword') }}',
    dataType:'json',
    data:values,
    type:'POST',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
        if(result.status){
          toastr.success(result.message)
          $('#change_password')[0].reset();
          $('#change_password').parsley().reset();
          $('.setting_change_password').modal('hide');
        }else{
          toastr.error(result.message)
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
    $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
   });
    $("#save_profile").on('submit',function(e){
      e.preventDefault();
      var _this=$(this); 
      var formData = new FormData(this);
        $.ajax({
        url:'{{ url('admin/saveProfile') }}',
        dataType:'json',
        data:formData,
        cache:false,
        contentType: false,
        processData: false,
        type:'POST',
        beforeSend: function (){before(_this)},
        // hides the loader after completion of request, whether successfull or failor.
        complete: function (){complete(_this)},
        success:function(result){
            if(result.status){
              toastr.success(result.message)
              $('#save_profile')[0].reset();
              $('#save_profile').parsley().reset();
              window.location.reload();
            }else{
              toastr.error(result.message)
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

    $("#file").change(function(){
				var fileObj = this.files[0];
				var imageFileType = fileObj.type;
				var imageSize = fileObj.size;

        var file = $('#file')[0].files[0].name;
        $(this).prev('label').text(file);
			
				var match = ["image/jpeg","image/png","image/jpg"];
				if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
					$('#previewing').attr('src','images/image.png');
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
    function imageIsLoaded(e){
      //console.log(e);
      $("#file").css("color","green");
      $('#previewing').attr('src',e.target.result);

    }
  });
  
</script>

@endsection
