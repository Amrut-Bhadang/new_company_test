@extends('layouts.web.master')
@section('title',$title)
@section('content')
@php
$auth_user = Session::get('AuthUserData');
@endphp
<main class="account-page inner_page_space">
    <section class="space-cls">
        <div class="container">
            <div class="account-page-in">
                @include('layouts.web.include.leftbar_itms')
                @if($data->status == true)
                @php
                $amount = $data->data->total_amount/2;
                $join_player = count($data->data->booking_challenges);
                @endphp
                <div class="content_sec">
                    <div class="challenges_detail">
                        <div class="challenges_detail_img"><img src="{{$data->data->court_image}}"></div>
                        <div class="challenges_head">
                            <div class="challenges_head_user"><img src="{{$data->data->user_image}}"></div>
                            <div class="challenges_head_con">
                                <h4>{{$data->data->user_name}}</h4>
                                <p class="challenges_head_designation">{{__('backend.Organizer')}}</p>
                            </div>
                            <div class="share_sec"><img src="{{asset('web/images/share.png')}}"></div>
                        </div>
                        <a href="http://maps.google.com/maps?q={{$data->data->latitude}},{{$data->data->longitude}}" target="_blank">
                            <div class="court-location">
                                <span class="court-ic">
                                    <img src="{{asset('web/images/location-1.png')}}">
                                </span>
                                <div class="address-distance">
                                    <span class="address">{{$data->data->address}}</span>
                                    <span class="distance">{{$data->data->distance}}</span>
                                </div>
                            </div>
                        </a>
                        <div class="challenges_detail_itms">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="challenges_detail_itm">
                                        <div class="challenges_detail_itm_ic"><img src="{{asset('web/images/ic1.png')}}"></div>
                                        <div class="challenges_detail_itm_con">
                                            <h5>{{isset($data->data->booking_date) ? date('d-m-Y',strtotime($data->data->booking_date)) :''}}</h5>
                                            <span>{{isset($data->data->booking_start_time) ? date('g:i',strtotime($data->data->booking_start_time)) :''}} {{isset($data->data->booking_start_time) ? __('backend.'.date('A',strtotime($data->data->booking_start_time))) :''}} ~ {{isset($data->data->booking_end_time) ? date('g:i',strtotime($data->data->booking_end_time)) :''}} {{isset($data->data->booking_end_time) ? __('backend.'.date('A',strtotime($data->data->booking_end_time))) :''}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <div class="challenges_detail_itm">
                                        <div class="challenges_detail_itm_ic"><img src="{{asset('web/images/ic2.png')}}"></div>
                                        <div class="challenges_detail_itm_con">
                                            <h5>{{__('backend.Age_group')}}</h5>
                                            <span>10 - 60</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <div class="challenges_detail_itm">
                                        <div class="challenges_detail_itm_ic"><img src="{{asset('web/images/ic3.png')}}"></div>
                                        <div class="challenges_detail_itm_con">
                                            <h5>{{__('backend.AED')}} {{$amount??''}}</h5>
                                            <span>{{__('backend.Fee_per_player')}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="challenges_detail_players">
                            <div class="challenges_detail_players_head">
                                <h4>{{__('backend.Joined_Players')}}</h4>
                                <span>{{$join_player}} {{__('backend.Player_going')}}</span>
                            </div>
                            @if(isset($data->data->booking_challenges))
                            @foreach($data->data->booking_challenges as $booking_challenges)
                            <div class="challenges_detail_players_body">
                                <div class="challenges_detail_player">
                                    <div class="challenges_player_img"><img src="{{$booking_challenges->user_details->image}}"></div>
                                    <div class="challenges_player_nm">{{$booking_challenges->user_details->name ?? __('backend.No_name')}}</div>
                                    <div class="challenges_player_status">{{__('backend.Joined')}}</div>
                                    @if($booking_challenges->payment_type == 'online')
                                    <div class="challenges_player_action">{{__('backend.Pre_Booked')}}</div>
                                    @else
                                    <div class="challenges_player_action">{{__('backend.On_Arrival')}}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            @endif
                            <form action="" method="POST" id="join_challenge_form">
                                @csrf
                                <input type="hidden" name="court_booking_id" value="{{$data->data->id}}">
                                <input type="hidden" name="amount" value="{{$amount}}">
                                <div class="challenges_detail_players_footer">
                                    <span>{{__('backend.AED')}} {{$amount}}/{{__('backend.Player')}}</span>
                                    @if($data->data->is_challenge == false)
                                    <a href="" id="join_challenge" data-id="{{$data->data->id}}" class="btn btn-primary">{{__('backend.Join')}}</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                {{$data->message}}
                @endif
            </div>
        </div>
    </section>
</main>
<div class="modal fade" id="join_challenge_checkout_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
                <div class="join_challenge_checkout_content" id="join_challenge_checkout_content">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // book court with check out
    $(document).ready(function() {
        $('#book_court').parsley();

        $(document).on('click', "#join_challenge", function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var formData = new FormData(document.getElementById("join_challenge_form"));
            var url = "{{ route('web.join_challenge_checkout')}}";
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
                
                success: function(result) {
                    $('#join_challenge_checkout_content').html(result);
                    $('#join_challenge_checkout_model').modal('show');
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
    $(document).ready(function() {
        $(document).on('submit', "#join_challenge_checkout_form", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var formData = new FormData(document.getElementById("join_challenge_checkout_form"));
            // var response = confirm("{{ __('backend.confirm_box_join_challenge') }}");
            if(formData.get('payment_type') == 'cash'){
                var response = confirm("{{ __('backend.confirm_box_cash_permission') }}");
            }else{
                 var response = 'online';
            }
            if (response) {
                var url = "{{ route('web.join_challenge')}}";

                $('#group_loader').fadeIn();
                $.ajax({
                    url: url,
                    data: formData,
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        // console.log(result, 'success');
                        if (result.status) {
                            if (result.payment) {
                              window.location.href = "{{url('payment/')}}";
                            }else{
                                toastr.success(result.message)
                                $('#join_challenge_checkout_model').modal('hide');
                                window.location.href = "{{route('web.thank.you')}}?slug=booking_cash";
                            }
                        } else {
                            toastr.error(result.message)
                            $('.save').prop('disabled', false);

                            $('.formloader').css("display", "none");
                        }
                        $('#book_court').parsley().reset();
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
            }
            return false;
        });
    });
</script>
@endsection