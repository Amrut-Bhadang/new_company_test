<!-- <style>
    .invite_player_status {
        margin-left: auto;
    }
    .invite_title {
        display: block;
    }
    .invite_detail_player {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }
    .invite_detail_player:last-child {
        border-bottom: 0;
    }
    @media screen and (max-width: 767px) {
        .invite_title strong {display: block;}
        span.invite_mobile strong {display: block;}
        span.invite_mobile {font-size: 14px;}
    }
</style> -->
@if($player_list->status == true)
<div class="challenges_detail_players">
    @foreach($player_list->data->player_list as $player)
        <input type="hidden" id="court_booking_id" name="court_booking_id" value="{{$court_booking_id}}">
        <div class="challenges_detail_players_body">
            <div class="invite_detail_player">
                <div class="invite_player_nm">
                    <span class="invite_title"><strong>{{__('backend.Name')}}</strong> {{$player->name ?? __('backend.No_name')}}</span>
                    <span class="invite_mobile"><strong>{{__('backend.Mobile_Number')}}</strong> {{$player->country_code ?? __('backend.No_name')}}-{{$player->mobile ?? __('backend.No_name')}}</span>
                </div>
                <div class="invite_player_status" >
                    <button type="button" class="btn btn-primary invite_player_submit" data-mobile="{{$player->mobile}}" data-country_code="{{$player->country_code}}" >{{__('backend.Invite_Player')}}</button>
                </div>
            </div>
        </div>
    @endforeach
</div>
@else
<input type="hidden" id="court_booking_id" name="court_booking_id" value="{{$court_booking_id}}">

<div class="challenges_detail_players">
<div class="invite_detail_player">
    <div class="invite_player_nm">
        <span class="invite_mobile">{{$player_list->message}}</span>
    </div>
    <div class="invite_player_status" >
        <button class="btn btn-primary invite_player_send_message">{{__('backend.Send_Message')}}</button>
    </div>
</div>
</div>
@endif