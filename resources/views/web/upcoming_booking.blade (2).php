@extends('layouts.web.master')
@section('title',$title)
@section('content')
<main class="account-page inner_page_space">
    <section class="space-cls">
        <div class="container">
        <div id="overlay">
          <div class="cv-spinner">
            <span class="spinner"></span>
          </div>
        </div>
            <div class="account-page-in">
                @include('layouts.web.include.leftbar_itms')
                <div class="content_sec">
                    <div class="challenges_itms">
                        @if($normal->status == true)
                        @foreach($normal->data->court_booking->data as $data)
                        <div class="challenges_itm">
                            <div class="challenges_itm_img">
                                <img src="{{$data->court_image}}">
                                @if($data->order_status == 'Accepted')
                                <div class="order_status">
                                    <span>{{__('backend.Accepted')}}</span>
                                </div>
                                @endif
                            </div>
                            <div class="challenges_itm_con">
                                <div class="challenges_itm_con_in">
                                    <div class="challenges_itm_title">
                                        <h3><a href="#">{{$data->court_name}}<span>({{$data->facility_name ?? ''}})</span></a></h3>
                                    </div>
                                    <div class="challenges_itm_meta">
                                    <a href="http://maps.google.com/maps?q={{$data->latitude}},{{$data->longitude}}" target="_blank">
                                        <div class="court-location">
                                            <span class="court-ic">
                                                <img src="{{asset('web/images/location-1.png')}}">
                                            </span>
                                            <span class="address">{{$data->address}}, {{$data->distance}}</span>
                                        </div>
                                    </a>
                                    </div>
                                    <div class="challenges_itm_meta1">
                                        <div class="challenges_itm_payment">
                                            <div class="challenges_itm_payment_ic"><img src="{{asset('web/images/money-bag.png')}}"></div>
                                            <!-- <span>{{__('backend.'.$data->payment_type)}}</span> -->
                                            @if($data->payment_type == 'cash')
                                                <span>{{__('backend.electronic_payment_Upon_Arrival')}}</span>
                                            @else
                                                <span>{{__('backend.electronic_payment_Pre_booking')}}</span>
                                            @endif
                                        </div>
                                        <div class="challenges_itm_price">
                                            <h4>{{__('backend.AED')}} {{$data->total_amount ?? ''}}</h4>
                                        </div>
                                    </div>
                                    <div class="challenges_itm_date_time">
                                        <div class="challenges_itm_date">
                                            <div class="challenges_itm_date_ic"><img src="{{asset('web/images/calender.png')}}"></div>
                                            <span>{{isset($data->booking_date) ? date('d-m-Y',strtotime($data->booking_date)) :''}}</span>
                                        </div>
                                        @php
                                        if(isset($data->booking_start_time) && isset($data->booking_end_time)){
                                        $start = strtotime($data->booking_start_time);
                                        $end = strtotime($data->booking_end_time);
                                        $mins = ($end - $start) / 3600;
                                        $difference = round($mins,1);
                                        }
                                        else{
                                        $difference = '';
                                        }
                                        @endphp
                                        <div class="challenges_itm_time">
                                            <div class="challenges_itm_time_cn">{{$difference}}h</div>
                                            <span>{{isset($data->booking_start_time) ? date('g:i A',strtotime($data->booking_start_time)) :''}} - {{isset($data->booking_end_time) ? date('g:i A',strtotime($data->booking_end_time)) :''}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="challenges_itm_right">
                                    
                                    <div class="share-cls">
                                        <div class="share-icon dropdown">
                                            <?php $name = ucwords(str_replace("'","",$data->court_name)); ?>
                                            <a href="javascript:;" class="nav-link dropdown-toggle invite_friens_btn" id="navbarDropdownShare" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-challenge-id ={{$data->id}}>
                                                <img src="{{asset('web/images/share.png')}}">
                                                <span>{{__('backend.Share')}}</span>
                                            </a>
                                            <div class="dropdown-menu share-dropdown" aria-labelledby="navbarDropdownShare">
                                                <ul>
                                                <li class="facebook">      
                                                    <a href="javascript:;" onclick="window.open('https://facebook.com/sharer.php?u={{route('web.upcoming_booking')}}&quote={!! $name !!}')">
                                                        <img src="{{ URL::asset('web/images/facebook.png')}}"> {{__('backend.Facebook')}}
                                                    </a>
                                                </li>
                                                <hr/>
                                                <li class="twiter">
                                                    <a href="javascript:;" onclick="window.open('https://twitter.com/share?url={{route('web.upcoming_booking')}}&text={!! $name !!}&via=Iseehat&hashtags=buyonIseehat')">
                                                        <img src="{{ URL::asset('web/images/twitter.png')}}"> {{__('backend.Twitter')}}
                                                    </a>
                                                </li>
                                                <hr/>
                                                <?php /*
                                                <li class="insta">
                                                <a href="mailto:%20?subject={!!  ucwords($productDetail->data->name) !!}&body={!!  ucwords($productDetail->data->name) !!}{!!  ucwords( strip_tags(str_replace('|','',$productDetail->data->long_description))) !!}{{route('web.upcoming_booking'a->id])}}">
                                                        <img src="{{ URL::asset('web/images/insta.png')}}"> Instagram
                                                    </a>
                                                </li> */?>
                                                <li class="whatup">
                                                    <a href="javascript:;" onclick="window.open('https://api.whatsapp.com/send?text={{route('web.upcoming_booking')}}')">
                                                        <img src="{{ URL::asset('web/images/whatup.png')}}"> 
                                                        {{__('backend.whatsapp')}}
                                                    </a>
                                                </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{route('web.court_booking_cancel',$data->id)}}" id="booking_cancel" data-id="{{$data->id}}" class="btn btn-primary btn-block">{{__('backend.Cancel_Booking')}}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                        @endif
                    </div>
                    <div class="challenges_heading">
                        <div class="arrowAnim_left">
                            <img src="{{asset('web/images/left_ar.png')}}">
                        </div>
                        <h3>{{__('backend.Challenges')}}</h3>
                        <div class="arrowAnim_right">
                            <img src="{{asset('web/images/right_ar.png')}}">
                        </div>
                    </div>
                    <div class="challenges_itms">
                        @if($challenge->status == true)
                        @foreach($challenge->data->court_booking->data as $data)
                        <div class="challenges_itm">
                            <div class="challenges_itm_img">
                                <img src="{{$data->court_image}}">
                                @if($data->challenge_type == 'private')
                                <div class="available-slot">
                                    <span>{{__('backend.Private_Booking')}}</span>
                                </div>
                                @endif
                                @if($data->order_status == 'Accepted')
                                <div class="order_status">
                                    <span>{{__('backend.Accepted')}}</span>
                                </div>
                                @endif
                            </div>
                            <div class="challenges_itm_con">
                                <div class="challenges_itm_con_in">
                                    <div class="challenges_itm_title">
                                        <h3><a href="#">{{$data->court_name}}<span>({{$data->facility_name ?? ''}})</span></a></h3>
                                    </div>
                                    <!-- <div class="court_contact_dtl">
                                        <span class="call-ic">
                                            <i class="fa fa-phone"></i>
                                        </span>
                                        <span class="phone_no">{{$data->country_code ?? ''}}-{{$data->mobile ?? ''}}</span>
                                    </div> -->
                                    <div class="challenges_itm_meta">
                                        <a href="http://maps.google.com/maps?q={{$data->latitude}},{{$data->longitude}}" target="_blank">
                                            <div class="court-location">
                                                <span class="court-ic">
                                                    <img src="{{asset('web/images/location-1.png')}}">
                                                </span>
                                                <span class="address">{{$data->address}}, {{$data->distance}}</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="challenges_itm_meta1">
                                        <div class="challenges_itm_payment">
                                            <div class="challenges_itm_payment_ic"><img src="{{asset('web/images/money-bag.png')}}"></div>
                                            <!-- <span>{{__('backend.'.$data->payment_type)}}</span> -->
                                            @if($data->payment_type == 'cash')
                                                <span>{{__('backend.electronic_payment_Upon_Arrival')}}</span>
                                            @else
                                                <span>{{__('backend.electronic_payment_Pre_booking')}}</span>
                                            @endif
                                        </div>
                                        <div class="challenges_itm_price">
                                            <h4>{{__('backend.AED')}} {{$data->total_amount ?? ''}}</h4>
                                        </div>
                                    </div>
                                    <div class="challenges_itm_date_time">
                                        <div class="challenges_itm_date">
                                            <div class="challenges_itm_date_ic"><img src="{{asset('web/images/calender.png')}}"></div>
                                            <span>{{isset($data->booking_date) ? date('d-m-Y',strtotime($data->booking_date)) :''}}</span>
                                        </div>
                                        @php
                                        if(isset($data->booking_start_time) && isset($data->booking_end_time)){
                                        $start = strtotime($data->booking_start_time);
                                        $end = strtotime($data->booking_end_time);
                                        $mins = ($end - $start) / 3600;
                                        $difference = round($mins,1);
                                        }
                                        else{
                                        $difference = '';
                                        }
                                        @endphp
                                        <div class="challenges_itm_time">
                                            <div class="challenges_itm_time_cn">{{$difference}}h</div>
                                            <span>{{isset($data->booking_start_time) ? date('g:i A',strtotime($data->booking_start_time)) :''}} - {{isset($data->booking_end_time) ? date('g:i A',strtotime($data->booking_end_time)) :''}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="challenges_itm_right">
                                @if($data->challenge_type == 'private')
                                    <a href="#" class="invite_friens_btn invite_player" data-challenge-id ={{$data->id}}>
                                        <img src="{{asset('web/images/invite.png')}}">
                                        <span>{{__('backend.Invite_Player')}}</span>
                                    </a>
                                @endif
                                    <a href="{{route('web.court_booking_cancel',$data->id)}}" id="booking_cancel" data-id="{{$data->id}}" class="btn btn-primary btn-block">{{__('backend.Cancel_Booking')}}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
    <?php
        use App\Models\DeliveryPrice;
        $admin_cancel_charge = deliveryPrice::where('id',1)->first()->cancellation_charge;
    ?>
<script>
    $(document).ready(function() {
        $(document).on('click', "#booking_cancel", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var text = "{{ __('backend.confirm_box_cancelled_booking_player') }}";
            let result = text.replace("{percentage}", "{{$admin_cancel_charge}}%"); 
            var response = confirm(result);
            if (response) {
                var url = "{{ route('web.court_booking_cancel','')}}/" + id;
                $('#group_loader').fadeIn();
                $("#overlay").fadeIn(30);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'GET',
                    cache: false,
                    complete: function() {
                        complete(_this)
                    },
                    success: function(result) {
                        $("#overlay").fadeOut(30);
                        // console.log(result, 'success');
                        if (result.status) {
                            toastr.success(result.message)
                            window.location.reload();
                        } else {
                            toastr.error(result.message)
                            $('.save').prop('disabled', false);
                            $('.formloader').css("display", "none");
                        }
                        $('#book_court').parsley().reset();
                    },
                    error: function(jqXHR, textStatus, textStatus) {
                        $("#overlay").fadeOut(30);
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
            }
            return false;
        });
    });

     //  invite player ajax code 
     $(document).ready(function() {
        // $('#checkout_form').parsley();
        $(document).on('click', ".invite_player_submit", function(e) {
            e.preventDefault();
            var court_booking_id = $('#court_booking_id').val();
            var mobile = $(this).data('mobile');
            var country_code = $(this).data('country_code');
                var url = "{{ route('web.invite.player')}}";
                $('#group_loader').fadeIn();
                // var values = $('#book_court').serialize();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: {_method:'POST', _token: "{{ csrf_token() }}", court_booking_id:court_booking_id,country_code:country_code,mobile:mobile},
                    type: 'POST',
                    cache: false,                   
                    success: function(result) {
                        // console.log(result, 'success');
                        if (result.status) {
                            toastr.success(result.message);
                            $('#invite_player_Modal').modal('hide');
                            window.location.reload();
                        } else {
                            toastr.error(result.message)
                            $('.save').prop('disabled', false);
                            $('.formloader').css("display", "none");
                        }
                        $('#checkout_form').parsley().reset();
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
        });
    });
     //  invite player send message ajax code 
     $(document).ready(function() {
        // $('#checkout_form').parsley();
        $(document).on('click', ".invite_player_send_message", function(e) {
            e.preventDefault();
            var court_booking_id = $('#court_booking_id').val();
            var mobile = $('#mobile').val();
            var country_code = $('#country_code').val();
            // var mobile = $(this).data('mobile');
            // var country_code = $(this).data('country_code');
            // alert(country_code);
            // return false;
                var url = "{{ route('web.invite.player')}}";
                $('#group_loader').fadeIn();
                // var values = $('#book_court').serialize();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: {_method:'POST', _token: "{{ csrf_token() }}", court_booking_id:court_booking_id,country_code:country_code,mobile:mobile},
                    type: 'POST',
                    cache: false,                   
                    success: function(result) {
                        // console.log(result, 'success');
                        if (result.status) {
                            toastr.success(result.message);
                            $('#invite_player_Modal').modal('hide');
                            window.location.reload();
                        } else {
                            toastr.error(result.message)
                            $('.save').prop('disabled', false);
                            $('.formloader').css("display", "none");
                        }
                        $('#checkout_form').parsley().reset();
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
        });
    });
    //  create invite player ajax code 

    $(document).ready(function() {
        $('#invite_player_form').parsley();

        $(document).on('submit', "#invite_player_form", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
                var url = "{{ route('web.get.player.list')}}";
                $('#group_loader').fadeIn();
                // var values = $('#book_court').serialize();
                $.ajax({
                    url: url,
                    dataType: 'html',
                    data: formData,
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    
                    complete: function() {
                        complete(_this)
                    },
                    success: function(result) {
                        // console.log(result, 'success');
                        $('#search_player_list').html(result);
                    },
                    error: function(jqXHR, textStatus, textStatus) {
                        if (jqXHR.responseJSON.errors) {
                            $.each(jqXHR.responseJSON.errors, function(index, value) {
                                toastr.error(value)
                            });
                        } else {
                            toastr.error(jqXHR.responseJSON.message)
                        }
                    }
                });
        });
    });
     //  create challenge ajax
     $(document).on('click', '.invite_player', function(e) {
        e.preventDefault();
        $('#view_response').empty();
        id = $(this).attr('data-challenge-id');
        $("#popup_challenge_id").val(id);
        $('#invite_player_Modal').modal('show');
    });
</script>
@endsection