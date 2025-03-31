@extends('layouts.web.master')
@section('title',$title)
@section('content')
@php
$auth_user = Session::get('AuthUserData');
@endphp

<main>
    @include('web.category_sec')
    <section class="facilities-page" id="court_list">
        <div class="container">
        <div id="overlay">
          <div class="cv-spinner">
            <span class="spinner"></span>
          </div>
        </div>
            <form data-parsley-validate id="court_list_filter_form" method="POST">
                @csrf
                <div class="facilities-page_in">
                    <div class="filter_pop">
                        <h3>{{__('backend.Filter')}}</h3>
                        <a class="filter_icon" href="javascript:void(0);"><img src="{{asset('web/images/filter.png')}}"></a>
                    </div>

                    <div class="side-filter">
                        <div class="filter-inner">
                            <div class="remove_filter"><button type="button" class="filter_cross"><img src="{{asset('web/images/cross.png')}}"></button></div>
                            <h3>{{__('backend.Filter_by')}}</h3>
                            <div class="reset-filter">
                                <a href="{{route('web.court_list')}}">{{__('backend.Reset_Filters')}}</a>
                            </div>
                        </div>
                        
                        <div class="select-date">
                            <div class="filter-title">
                                <h4>{{__('backend.Select_Date')}}</h4>
                            </div>
                            <div class="sidebar_itm_info">
                                <input type="text" class="form-control datepicker1" id="filter_booking_date" onchange="change_filter()" name="date" value="" placeholder="{{__('backend.Select_Date')}}" />
                            </div>
                        </div>
                        
                        <div class="select-time">
                            <div class="filter-title">
                                <h4>{{__('backend.Choose_Sport')}}</h4>
                            </div>
                            <div class="time-slot">
                                @if($category_list->status == true)
                                @foreach($category_list->data->category_list as $sport)
                                <label class="time-label">
                                    <input type="radio" onchange="change_filter()" name="category_id" id="category_id" value="{{$sport->id}}">
                                    <span class="checkmark">
                                        <img src="{{$sport->image}}">
                                    </span>
                                </label>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="filter_item">
                            <div class="filter-title">
                                <h4>{{__('backend.Sort')}}</h4>
                            </div>
                            <div class="filter_itms">
                                <div class="radio_custom_itm court_sort distance_asc" onclick="change_filter('distance_asc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/arrow_down_up.png')}}"></span>
                                {{__('backend.Distance_Low_to_High')}}
                                    <input type="radio" id="distance_asc" value="distance_asc" name="court_sort">
                                </div>
                                <div class="radio_custom_itm court_sort distance_desc" onclick="change_filter('distance_desc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/arrow_up_down.png')}}"></span>
                                {{__('backend.Distance_High_to_Low')}}
                                    <input type="radio"  id="distance_desc" value="distance_desc" name="court_sort">
                                </div>
                                <div class="radio_custom_itm court_sort rating_asc" onclick="change_filter('rating_asc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/star.png')}}"></span>
                                {{__('backend.Rating_Low_to_High')}}
                                    <input type="radio" id="rating_asc" value="rating_asc" name="court_sort">
                                </div>
                                <div class="radio_custom_itm court_sort rating_desc" onclick="change_filter('rating_desc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/star.png')}}"></span>
                                {{__('backend.Rating_High_to_Low')}}
                                    <input type="radio" id="rating_desc" value="rating_desc" name="court_sort">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="facilities-side">
                        <div class="influncer_ajax_llist">
                            @if($data->status == true)
                            @foreach($data->data->court->data as $court)
                            <div class="single-court">
                                <div class="court-img">
                                @if($auth_user != null)
                                    <a href="{{route('web.court_detail',$court->id)}}">  <img src="{{$court->image}}"></a>
                                @else
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal">  <img src="{{$court->image}}"></a>
                                @endif
                                </div>
                                <div class="courtpage-cont">
                                    <div class="court-cont-inner">
                                        <div class="court-cont">
                                            <div class="box-title">
                                        @if($auth_user != null)
                                             <a href="{{route('web.court_detail',$court->id)}}"> <h3>{{$court->court_name}}<span>({{$court->facility_name ?? ''}})</span></h3></h3></a>
                                        @else
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#login_modal"> <h3>{{$court->court_name}}<span>({{$court->facility_name ?? ''}})</span></h3></h3></a>
                                        @endif
                                            </div>
                                            <!-- <div class="court_contact_dtl">
                                                <span class="call-ic">
                                                    <i class="fa fa-phone"></i>
                                                </span>
                                                <span class="phone_no">{{$court->country_code ?? ''}}-{{$court->mobile ?? ''}}</span>
                                            </div> -->
                                            <div class="price-review-sec">
                                                <div class="review">
                                                    <span class="star-ic">
                                                        <img src="{{asset('web/images/star.png')}}">
                                                    </span>
                                                    <span>{{$court->average_rating}} ({{$court->total_rating}} {{__('backend.Review')}})</span>
                                                </div>
                                                @if(isset($court->court_size))
                                                    <div class="court_size">
                                                        <p>{{__('backend.Court_Size')}} - {{$court->court_size ?? ''}}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        <a href="http://maps.google.com/maps?q={{$court->latitude}},{{$court->longitude}}" target="_blank">
                                            <div class="court-location">
                                                <span class="court-ic">
                                                    <img src="{{asset('web/images/location-1.png')}}">
                                                </span>
                                                <div class="address-distance ">
                                                    <span class="address">{{$court->address}}</span>
                                                    <span class="distance">{{$court->distance}}</span>
                                                </div>
                                            </div>
                                        </a>
                                         @if(count((array)$court->available_time_slot) > 0)
                                            <div class="available-slot">
                                                <span>{{__('backend.Available_Slot')}}: <span class="font-weight">{{__('backend.'.$court->available_time_slot->day)}}, {{date('g:i',strtotime($court->available_time_slot->time))}} {{__('backend.'.date('A',strtotime($court->available_time_slot->time)))}}</span></span>
                                            </div>
                                            @else
                                            @endif
                                        </div>
                                    </div>
                                    <div class="court-price-inner">
                                        <div class="box-title">
                                            <div class="court-icon">
                                                <img src="{{$court->category_image}}">
                                            </div>
                                        </div>
                                        <div class="price-wrap">
                                            <h4>{{__('backend.AED')}} {{$court->hourly_price}}/{{__('backend.Slot')}}</h4>
                                        </div>
                                        <div class="book-now">
                                            @if($auth_user != null)
                                                @if(count((array)$court->available_time_slot) > 0)
                                                    <a href="{{url('/court_detail').'/'.$court->id.'/booknow'}}"> <button type="button" class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                                @else
                                                    <a href="{{route('web.court_detail',$court->id)}}"> <button type="button" class="btn-primary">{{__('backend.Book_Now')}}</button></a>
                                                @endif
                                            @else
                                                <button type="button" class="btn-primary" data-toggle="modal" data-target="#login_modal">{{__('backend.Book_Now')}}</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                            @endif
                        </div>
                        @if($data->status == true)
                        @if($data->data->court->current_page != $data->data->court->last_page)
                        <div class="loadmoar-class">
                            <p><a href="javascript:;" class="loadMoareLink">{{__('backend.Load_More')}}</a></p>
                        </div>
                        @endif
                        
                        @endif
                       
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>

<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
     var i = 2;
    $(document).ready(function() {
        $('.datepicker1').datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
            startDate: '+1D',
            endDate: "+7D"
        });
    });

    function change_filter(key="") {

        var booking_date = $('#filter_booking_date').val();

        if (key) {
            $('.court_sort').removeClass('selected-sort');
            $("#overlay").fadeIn(30);
            var court_sort = $("div.court_sort input[type=radio]:checked").val();
            $('.'+court_sort).addClass('selected-sort');
        }
        $("#overlay").fadeIn(30);
        var formData = new FormData(document.getElementById('court_list_filter_form'));
        var url = "{{ route('web.court_list_filter')}}";
        $.ajax({
            url: url,
            dataType: 'html',
            data: formData,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            success: function(result) {
                $("#overlay").fadeOut(30);
                $('.influncer_ajax_llist').html(result);
                 i = 2;
            },
        });
    };
</script>
<script>
   
    $(document).on('click', '.loadMoareLink', function() {
        $("#overlay").fadeIn(30);

        var booking_date = $('#filter_booking_date').val();

        if (!booking_date) {
            booking_date = '';
        }
        var court_sort = $("div.court_sort input[type=radio]:checked").val();

        if (!court_sort) {
            court_sort = '';
        }
        var category_id = $('input[name="category_id"]:checked').val();

        if (!category_id) {
            category_id = '';
        }

        var url = "{{route('courts_pagination')}}?page=" + i+"&booking_date="+booking_date+"&court_sort="+court_sort+"&category_id="+category_id;
        $('.preload').show();
        $('.influncer_ajax_llist').append($('<div>').load(url, function() {
            $('.preload').hide();
            i++;
            $("#overlay").fadeOut(30);
        }));
      
    });
</script>
@endsection