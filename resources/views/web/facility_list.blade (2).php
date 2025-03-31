@extends('layouts.web.master')
@section('title',$title)
@section('content')
@if($data->status == true || $data->status == false)
<main>
    @include('web.category_sec')
    <section class="facilities-page" id="facility_list">
        <div class="container">
        <div id="overlay">
          <div class="cv-spinner">
            <span class="spinner"></span>
          </div>
        </div>
            <form data-parsley-validate id="facility_list_filter_form" method="POST">
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
                                <a href="{{route('web.facility')}}">{{__('backend.Reset_Filters')}}</a>
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
                                    <input type="radio" onchange="change_filter()" name="category_id" value="{{$sport->id}}">
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
                                <div class="radio_custom_itm facility_sort distance_asc" onclick="change_filter('distance_asc')">
                                     <span class="arrow-down-up"><img src="{{asset('web/images/arrow_down_up.png')}}"></span>
                                {{__('backend.Distance_Low_to_High')}}
                                    <input type="radio" id="distance_asc" value="distance_asc" name="facility_sort">
                                </div>
                                <div class="radio_custom_itm facility_sort distance_desc" onclick="change_filter('distance_desc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/arrow_up_down.png')}}"></span>
                                {{__('backend.Distance_High_to_Low')}}
                                    <input type="radio"  id="distance_desc" value="distance_desc" name="facility_sort">
                                </div>
                                <div class="radio_custom_itm facility_sort rating_asc" onclick="change_filter('rating_asc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/star.png')}}"></span>
                                {{__('backend.Rating_Low_to_High')}}
                                    <input type="radio" id="rating_asc" value="rating_asc" name="facility_sort">
                                </div>
                                <div class="radio_custom_itm facility_sort rating_desc" onclick="change_filter('rating_desc')">
                                <span class="arrow-down-up"><img src="{{asset('web/images/star.png')}}"></span>
                                {{__('backend.Rating_High_to_Low')}}
                                    <input type="radio" id="rating_desc" value="rating_desc" name="facility_sort">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="facilities-side facilities_itms">
                        <div class=" influncer_ajax_llist">
                            <div class="row">
                                @if($data->status == true)
                                @foreach($data->data->court->data as $facility)
                                <div class="col-sm-12 col-md-6 col-xl-6">
                                    <div class="court-box">
                                        <a href="{{route('web.facility_detail',$facility->id)}}">
                                            <div class="court-bg">
                                                <img src="{{$facility->image}}">
                                            </div>
                                        </a>
                                        <div class="court-cont court_content">
                                            <a href="{{route('web.facility_detail',$facility->id)}}">
                                                <div class="box-title">
                                                    <h3>{{$facility->name}}</h3>
                                                </div>
                                            </a>
                                            <div class="available-court">
                                                <span>{{$facility->available_court}} {{__('backend.Courts_Available')}} </span>
                                                <span class="courts_icons">
                                                    @foreach($facility->available_category as $category)
                                                    <img src="{{$category->image}}">
                                                    @endforeach
                                                </span>
                                            </div>
                                            <div class="price-review-sec">
                                                <a href="http://maps.google.com/maps?q={{$facility->latitude}},{{$facility->longitude}}" target="_blank">
                                                    <div class="court-location">
                                                        <span class="court-ic">
                                                            <img src="{{asset('web/images/location-1.png')}}">
                                                        </span>
                                                        <div class="address-distance">
                                                            <span class="address">{{$facility->address}}</span>
                                                            <span class="distance">{{$facility->distance}}</span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="review">
                                                    <span class="star-ic">
                                                        <img src="{{asset('web/images/star.png')}}">
                                                    </span>
                                                    <span>{{$facility->average_rating}} ({{$facility->total_rating}} {{__('backend.Review')}})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                                @endif
                            </div>
                            @if($data->data->court && $data->data->court->current_page != $data->data->court->last_page)
                            <div class="loadmoar-class">
                                <p><a href="javascript:;" class="loadMoareLink">Load More..</a></p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>
@else
{{$data->message}}
@endif
<script>
    $(document).ready(function() {
        $('.datepicker1').datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
            startDate: '+1D',
            endDate: "+7D"
        });
    });
    var i = 2;
    function change_filter(key="") {
        if(key){
            $('.facility_sort').removeClass('selected-sort');
            $("#overlay").fadeIn(30);
            var facility_sort = $("div.facility_sort input[type=radio]:checked").val();
            $('.'+facility_sort).addClass('selected-sort');
        }
        $("#overlay").fadeIn(30);
        var formData = new FormData(document.getElementById('facility_list_filter_form'));
        var url = "{{ route('web.facility_list_filter')}}";
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
                $('.facility_list_filter_data').html(result);
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
        var facility_sort = $("div.facility_sort input[type=radio]:checked").val();

        if (!facility_sort) {
            facility_sort = '';
        }
        var category_id = $('input[name="category_id"]:checked').val();

        if (!category_id) {
            category_id = '';
        }
        var url = "{{route('facility_pagination')}}?page=" + i+"&booking_date="+booking_date+"&facility_sort="+facility_sort+"&category_id="+category_id;
        $('.preload').show();
        $('.influncer_ajax_llist').append($('<div class="row">').load(url, function() {
            $('.preload').hide();
            i++;
            $("#overlay").fadeOut(30);
        }));
    });
</script>
@endsection