@php
use App\Models\CourtCategory;
$CategoryList = CourtCategory::select('id', 'name', 'image')->where(['status' => 1])->get();

if (str_contains($_SERVER['REQUEST_URI'], '/court')) { 
   $searchFrom = 'court';
} else {
   $searchFrom = 'facility'; 
}

@endphp
<section class="category_sec" style="background-image: url({{asset('web/images/banner.png')}})">
        <div class="container">
            <div class="banner-cont-sec">
                <div class="main-title">
                    <h1>{{__('backend.Find_Best_Player')}}</h1>
                    <p class="para-cls">{{__('backend.web_dashboard_catgory_section_text')}}</p>
                </div>
                <form method="POST" action="" enctype="" id="search_court">
                    @csrf   
                <div class="serch-form">
                    <div class="search-court">
                        <div class="form-group">
                            <div class="icon-cls">
                                <img src="{{asset('web/images/search.png')}}">
                            </div>
                            <input type="text" id="search_text" name="search_text" value="{{isset($_GET['search']) ? $_GET['search'] :''}}" placeholder="{{__('backend.Search')}}" class="form-control">
                        </div>
                    </div>
                    <input type="hidden" name="searchFrom" value="{{$searchFrom}}" id="search_for">
                    <div class="select-sport">
                        <div class="form-group">
                            <select name="category_id" id="category_id" class="form-control">
                                @php
                                $categoryId = $_GET['category_id'] ?? "";
                                @endphp
                                <option  value="">{{__('backend.Choose_Sport')}}</option>
                                @foreach($CategoryList as $sport)
                                    <option name="category_id" value="{{$sport->id}}" {{ $sport->id == $categoryId ? 'selected':''}}>{{$sport->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search-area">
                        <div class="form-group">
                            <div class="icon-cls">
                                <img src="{{asset('web/images/search.png')}}">
                            </div>
                            <input type="text" id="address" name="address" value="{{isset($_GET['address']) ? $_GET['address'] :''}}" placeholder="{{__('backend.Search_Area')}}" class="form-control">
                            <input type="hidden" class="latitude" id='latitude' name="latitude" />
                            <input type="hidden" class="longitude" id='longitude' name="longitude" />
                            <div class="icon-cls-right">
                                <img src="{{asset('web/images/location.png')}}">
                            </div>
                        </div>
                    </div>
                    <div class="search-btn">
                        <button type="submit" class="btn-primary">{{__('backend.Search')}}</button>
                    </div>
                </div>
            </form>
                <div class="arrow-down">
                    <div class="pulse-box">
                    <a href="#about-us">
                        <div class="pulse-css">
                            <img src="{{asset('web/images/arrow-down.png')}}">
                        </div>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).on('submit', "#search_court", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var search = $('#search_text').val();
            var search_for = $('#search_for').val();
            var category_id = $('#category_id').val();
            var address = $('#address').val();
            var latitude = $('#latitude').val();
            var longitude = $('#longitude').val();

            if (search_for == 'court') {
                window.location.href = "{{route('web.court_list')}}"+"?search="+search+'&category_id='+category_id+'&search_for='+search_for+'&address='+address+'&latitude='+latitude+'&longitude='+longitude;
                window.location.href = "{{route('web.court_list')}}"+"?search="+search+'&category_id='+category_id+'&search_for='+search_for+'&address='+address+'&latitude='+latitude+'&longitude='+longitude;

            } else {
                window.location.href = "{{route('web.facility')}}"+"?search="+search+'&category_id='+category_id+'&search_for='+search_for+'&address='+address+'&latitude='+latitude+'&longitude='+longitude;
            }
    });
    </script>