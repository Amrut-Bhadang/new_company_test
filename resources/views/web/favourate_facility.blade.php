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
                <div class=" influncer_ajax_llist">
                                @if($data->status == true)
                                    @foreach($data->data->court->data as $facility)
                                        @if(in_array($facility->id, $favFacilityList))
                                        <div class="col-sm-12 col-md-12 col-xl-12">
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
                                        @endif
                                    @endforeach
                                @else
                                <div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
                                @endif
                          <!--   @if($data->data->court && $data->data->court->current_page != $data->data->court->last_page)
                            <div class="loadmoar-class">
                                <p><a href="javascript:;" class="loadMoareLink">Load More..</a></p>
                            </div>
                            @endif -->
                        </div>
            </div>
        </div>
    </section>
</main>
<script>
    var i = 2;
    $(document).on('click', '.loadMoareLink', function() {
        $("#overlay").fadeIn(30);
        var url = "{{route('cancelled_booking_pagination')}}?page=" + i;
        $('.preload').show();
        $('.influncer_ajax_llist').append($('<div class="row">').load(url, function() {
            $('.preload').hide();
            i++;
            $("#overlay").fadeOut(30);
        }));
    });
</script>
@endsection