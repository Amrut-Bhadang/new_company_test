@extends('layouts.web.master')
@section('title',$title)
@section('content')
<?php
$userData =  Session::get('AuthUserData') ?? null;
$access_token = $userData->token ?? null;
// $header = json_encode(array('Accept:application/json','Authorization:Bearer ' . $access_token));
$header = $access_token;
// dd($userData->data);
?>
@if($data->status == true)
<main class="account-page inner_page_space">
    <section class="space-cls">
        <div class="container">
            <div class="account-page-in">

                @include('layouts.web.include.leftbar_itms')
               
                <div class="content_sec">
                    <form method="POST" enctype="" id="edit_player">
                        @csrf
                        <div class="content_sec_in">
                            <div class="content_sec_profile">
                                <div class="content_sec_profile_img" id="image_preview"><img id="previewing" src="{{$data->data->image}}"></div>
                                <div class="file_input">
                                    <input type="file" id="file" name="image" class="btn btn-primary">
                                    <span for="image">{{__('backend.Choose_Files')}}</span>
                                </div>
                            </div>
                            <div class="content_sec_profileinfo">
                                <h4>{{__('backend.Profile_Info')}}</h4>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{__('backend.First_Name')}}</label>
                                            <input type="text" name="first_name" value="{{$data->data->first_name}}" data-parsley-required="true" data-parsley-minlength="3" data-parsley-pattern="^[A-Za-z ]+$" data-parsley-pattern-message="{{__('backend.validation_only_alpha_space')}}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{__('backend.Last_Name')}} </label>
                                            <input type="text" name="last_name" value="{{$data->data->last_name}}" data-parsley-pattern="^[A-Za-z ]+$" data-parsley-pattern-message="{{__('backend.validation_only_alpha_space')}}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                    <div class=" input-group form-group custom-input-select">
                                        <select name="country_code" class="form-control" id="country_code" disabled>
                                            <option value="{{$data->data->country_code ?? ''}}">{{$data->data->country_code ?? ''}}</option>
                                        </select>
                                        <input type="text" id="mobile" name="mobile" value="{{$data->data->mobile}}"  class="form-control cust-control-cls" disabled>
                                    </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class=" form-group custom-input-select">
                                        <label>{{__('backend.Gender')}} </label>
                                            <select name="gender" class="form-control" id="gender">
                                                <option value="Male" {{ $data->data->gender == "Male"?'selected':'' }}>Male</option>
                                                <option value="Female" {{ $data->data->gender == "Female"?'selected':'' }}>Female</option>
                                                <option value="Other" {{ $data->data->gender == "Other"?'selected':'' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>{{__('backend.Email_Address')}}</label>
                                            <input type="text" name="email" value="{{$data->data->email}}" class="form-control" placeholder="{{__('backend.Email_Address')}}" autocomplete="off" data-parsley-required="true">
                                        </div>
                                    </div>
                                    <!-- <div class="col-xl-12">
                                        <div class="form-group">
                                            <label>{{__('backend.Gender')}}</label>
                                            <div class="radio_custom">
                                                <div class="radio_custom_itm">{{__('backend.Male')}}
                                                    <input type="radio" name="gender" value="Male" {{$data->data->gender == 'Male' ? 'checked':''}}>
                                                    <span class="checkmark"></span>
                                                </div>
                                                <div class="radio_custom_itm">{{__('backend.Female')}}
                                                    <input type="radio" name="gender" value="Female" {{$data->data->gender == 'Female' ? 'checked':''}}>
                                                    <span class="checkmark"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="col-xl-6">
                                        <div class="form-group mb-0 submit_btn">
                                            <input type="submit" name="" value="{{__('backend.Save_Profile')}}" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
@else
{{$data->message}}
@endif
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#edit_player').parsley();

        $(document).on('submit', "#edit_player", function(e) {
            e.preventDefault();
            var _this = $(this);
            var formData = new FormData(this);
            formData.append('_method', 'post');
            var url = "{{ url('api/auth/update-profile')}}";
            var header = "{{ $header }}";
            // alert(header);
            // alert(url);
            $('#group_loader').fadeIn();
            // var values = $('#edit_player').serialize();
            $.ajax({
                url: url,
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer {{$header ?? null}}"
                },
                dataType: 'json',
                data: formData,
                contentType: 'application/json; charset=utf-8',
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                // beforeSend: function() {
                //     before(_this)
                // },
                // hides the loader after completion of request, whether successfull or failor.
                complete: function() {
                    complete(_this)
                },
                success: function(result) {
                    // console.log(result, 'success');
                    if (result.status) {
                        window.location.href = "{{route('web.my_account')}}";
                        toastr.success(result.message)
                    } else {
                        toastr.error(result.message)
                        $('.save').prop('disabled', false);
                        $('.formloader').css("display", "none");
                    }
                    $('#edit_player').parsley().reset();
                },
                error: function(jqXHR, textStatus, textStatus) {
                    // console.log(textStatus, 'error');

                    if (jqXHR.responseJSON.errors) {
                        $.each(jqXHR.responseJSON.errors, function(index, value) {
                            toastr.error(value)
                        });
                    } else {
                        toastr.error(jqXHR.responseJSON.message)
                    }
                }
            });
            return false;
        });
    });
</script>
<script>
    $("#file").change(function() {
        var fileObj = this.files[0];
        var imageFileType = fileObj.type;
        var imageSize = fileObj.size;

        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))) {
            $('#previewing').attr('src', 'images/no-image-available.png');
            toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
            return false;
        } else {
            //console.log(imageSize);
            if (imageSize < 5000000) {
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
            } else {
                toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
                return false;
            }
        }
    });

    function imageIsLoaded(e) {
        //console.log(e);
        $("#file").css("color", "green");
        $('#previewing').attr('src', e.target.result);
    }
</script>
@endsection