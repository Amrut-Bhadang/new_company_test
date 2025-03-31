@if($data->status == true)
<div class="row">
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
</div>
@else
<div class="Data_not_found"><img src="{{asset('web/images/Data_not_found.png')}}"></div>
@endif