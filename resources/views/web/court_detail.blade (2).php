@php
$auth_user = Session::get('AuthUserData');
@endphp
@extends('layouts.web.master')
@section('title',$title)
@section('content')
@if($data->status == true)
<main class="court-dtl-page inner_page_space">
    <section class="court_dtl_sec space-cls">
        <div class="container">
            <div id="overlay">
              <div class="cv-spinner">
                <span class="spinner"></span>
              </div>
            </div>
            <div class="court_dtl_sec_in">
                <div class="filter_pop">
                    <h3>{{__('backend.Book_Court')}}</h3>
                    <a class="filter_icon" href="javascript:void(0);"><img src="{{asset('web/images/arrow-right.png')}}"></a>
                </div>
                <div class="court_dtl_sidebar">
                    <form data-parsley-validate id="book_court" method="POST">
                        @csrf
                        <input type="hidden" id="court_id" name="court_id" value="{{$data->data->id}}">
                        <input type="hidden" name="facility_id" value="{{$data->data->facility_id}}">
                        <input type="hidden" name="facility_owner_id" value="{{$data->data->facility_owner_id}}">
                        <input type="hidden" name="timeslot" value="{{$data->data->timeslot}}">
                        <input type="hidden" name="hourly_price" id="hourly_price" value="{{$data->data->hourly_price}}">
                        <input type="hidden" name="booking_type" value="normal">
                        <div class="court_dtl_form">
                            <div class="court_dtl_sidebar_head">
                                <div class="remove_filter"><button type="button" class="filter_cross"><img src="{{asset('web/images/cross.png')}}"></button></div>
                                <h3>{{__('backend.Book_Court')}}</h3>
                            </div>
                            <div class="court_dtl_form_in">
                                <div class="court_dtl_sidebar_itm sidebar_itm_pitch">
                                    <div class="sidebar_title">
                                        <h4>{{__('backend.Select_Court')}}</h4>
                                    </div>
                                    <div class="sidebar_itm_info">
                                        <select class="form-control" onchange="change_court(this)">
                                            @foreach($data->data->available_court as $court)
                                            <option value="{{$court->id}}" {{$data->data->court_name == $court->court_name ? 'selected':''}}>{{$court->court_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="court_dtl_sidebar_itm sidebar_itm_mintime">
                                    <p>{{__('backend.Min_Time_to_Book')}}: {{$data->data->timeslot}} {{__('backend.Min')}}</p>
                                </div>
                                <div class="court_dtl_sidebar_itm sidebar_itm_date">
                                    <div class="sidebar_title">
                                        <h4>{{__('backend.Select_Date')}}</h4>
                                    </div>
                                    <div class="sidebar_itm_info">
                                        <input type="text" class="form-control datepicker1" id="datepicker1" data-parsley-required="true" name="booking_date" value="" placeholder="{{__('backend.Select_Date')}}" />
                                    </div>
                                </div>
                                <div class="court_dtl_sidebar_itm sidebar_itm_time">
                                    <div class="sidebar_title">
                                        <h4>{{__('backend.Select_Time')}}</h4>
                                    </div>
                                    <div class="sidebar_itm_info">
                                        <div class="time-slot">
                                            @php $i = 0; @endphp
                                            @foreach($data->data->selecttimeslot as $time)
                                            @php
                                             $slot_class =  date('H-i', strtotime($time));
                                             @endphp
                                            <label class="time-label">
                                                <input type="checkbox" class="time_slot slots_{{$slot_class}}"  name="booking_time_slot[{{$i}}][start_time]" value="{{$time}}" data-index="{{$i}}">
                                                <span class="checkmark">
                                                {{date('g:i',strtotime($time))}} {{__('backend.'.date('A',strtotime($time)))}}
                                                </span>
                                            </label>
                                            @php $i++; @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="court_dtl_sidebar_bot">
                                <div class="court_dtl_sidebar_price">
                                    <span class="court_dtl_sidebar_price_lable">{{__('backend.Price')}}</span>
                                    <span class="court_dtl_sidebar_price_prc">{{__('backend.AED')}} <span id="price">{{$data->data->hourly_price}}</span></span>
                                </div>
                                @if($auth_user != null)
                                <button type="submit" class="btn-primary btn-block checkoutButtonClick">{{__('backend.Checkout')}}</button>
                                <button type="button" class="btn-dark btn-block create_challenge" data-court_id="{{$data->data->id}}" data-toggle="modal" data-target="#myModal">{{__('backend.Create_Challenge_with_50_Booking')}}</button>
                                @else
                                <button type="button" class="btn-primary btn-block" data-toggle="modal" data-target="#login_modal">{{__('backend.Checkout')}}</button>
                                <button type="button" class="btn-dark btn-block" data-toggle="modal" data-target="#login_modal">{{__('backend.Create_Challenge_with_50_Booking')}}</button>
                                    <!-- <button type="button" class="btn-primary" data-toggle="modal" data-target="#login_modal">{{__('backend.Book_Now')}}</button> -->
                                @endif
                               
                            </div>
                        </div>
                        </form>
                    </div>
                
                <div class="court_dtl_con">
                    <div class="detail_slider">
                        <div class="owl-carousel">
                            <div class="item">
                                <div class="slider_itm"><img src="{{$data->data->image}}"></div>
                            </div>
                            <div class="item">
                                <div class="slider_itm"><img src="{{$data->data->image}}"></div>
                            </div>
                            <div class="item">
                                <div class="slider_itm"><img src="{{$data->data->image}}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="facilities-dtl-cont">
                        <div class="box-title">
                            <h3>{{$data->data->court_name}}
                                @if($facility_is_deleted == 0)
                                <a href="{{ url('facility_detail')}}/{{$data->data->facility_id}}" target='_blank'> 
                                     <span>({{$data->data->facility_name}})</span></h3>
                                </a>
                                @else
                                    <h3><span>({{$data->data->facility_name}})</span></h3>
                                @endif
                       
                           
                            <div class="court-icon ml-auto mr-3">
                                <img src="{{$data->data->category_image}}">
                            </div>
                            <div class="share-cls">
                                <div class="share-icon dropdown">
                                    <?php $name = ucwords(str_replace("'","",$data->data->court_name)); ?>
                                        <a href="javascript:;" class="nav-link dropdown-toggle" id="navbarDropdownShare" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <div class="court-icon">
                                        <img src="{{asset('web/images/share.png')}}">
                                    </div>
                                    </a>
                                    <div class="dropdown-menu share-dropdown" aria-labelledby="navbarDropdownShare">
                                        <ul>
                                        <li class="facebook">      
                                            <a href="javascript:;" onclick="window.open('https://facebook.com/sharer.php?u={{route('web.court_detail',['id'=>$data->data->id])}}&quote={!! $name !!}')">
                                                <img src="{{ URL::asset('web/images/facebook.png')}}"> {{__('backend.Facebook')}}
                                            </a>
                                        </li>
                                        <hr/>
                                        <li class="twiter">
                                            <a href="javascript:;" onclick="window.open('https://twitter.com/share?url={{route('web.court_detail',['id'=>$data->data->id])}}&text={!! $name !!}&via=Iseehat&hashtags=buyonIseehat')">
                                                <img src="{{ URL::asset('web/images/twitter.png')}}"> {{__('backend.Twitter')}}
                                            </a>
                                        </li>
                                        <hr/>
                                        <?php /*
                                        <li class="insta">
                                        <a href="mailto:%20?subject={!!  ucwords($productDetail->data->name) !!}&body={!!  ucwords($productDetail->data->name) !!}{!!  ucwords( strip_tags(str_replace('|','',$productDetail->data->long_description))) !!}{{route('web.court_detail',['id'=>$data->data->id])}}">
                                                <img src="{{ URL::asset('web/images/insta.png')}}"> Instagram
                                            </a>
                                        </li> */?>
                                        <li class="whatup">
                                            <a href="javascript:;" onclick="window.open('https://api.whatsapp.com/send?text={{route('web.court_detail',['id'=>$data->data->id])}}')">
                                                <img src="{{ URL::asset('web/images/whatup.png')}}"> 
                                                {{__('backend.whatsapp')}}
                                            </a>
                                        </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="court_contact_dtl">
                            <!-- <span class="call-ic">
                                <i class="fa fa-phone"></i>
                            </span>
                            <span class="phone_no">{{$data->data->country_code ?? ''}}-{{$data->data->mobile ?? ''}}</span> -->
                            <a href="https://api.whatsapp.com/send?phone={{$data->data->country_code ?? ''}}{{$data->data->mobile ?? ''}}&text=Hello%20{{$name}}" target="_blank">
                                <img src="{{ URL::asset('web/images/whatup.png')}}">{{__('backend.whatsapp')}}</a>
                            <!-- <a href="javascript:;" onclick="window.open('https://api.whatsapp.com/send?text={{route('web.court_detail',['id'=>$data->data->id])}}')">
                                <img src="{{ URL::asset('web/images/whatup.png')}}"> 
                                {{__('backend.whatsapp')}}
                            </a> -->
                        </div>
                        <div class="price-review-sec">
                            <div class="review">
                                <span class="star-ic">
                                    <img src="{{asset('web/images/star.png')}}">
                                </span>
                                <span>{{$data->data->average_rating}} ({{$data->data->total_rating}} {{__('backend.Review')}})</span>
                            </div>
                            @if(isset($data->data->court_size))
                            <div class="court_size">
                                <p>{{__('backend.Court_Size')}} - {{$data->data->court_size ?? ''}}</p>
                            </div>
                            @endif
                        </div>
                        <div class="court_meta">
                            <a href="http://maps.google.com/maps?q={{$data->data->latitude}},{{$data->data->longitude}}" target="_blank">
                            <div class="court-location">
                                <span class="court-ic">
                                    <img src="{{asset('web/images/location-1.png')}}">
                                </span>
                                <span class="address">{{$data->data->address}}, {{$data->data->distance}}</span>
                            </div>
                            </a>
                            <div class="court_price_right">
                                <span>{{__('backend.AED')}} {{$data->data->hourly_price}} / {{__('backend.Slot')}}</span>
                            </div>
                        </div>
                        <div class="available-slot-sec">
                          @if(count((array)$data->data->available_time_slot) > 0)
                            <span>{{__('backend.Available_Slot')}} : <span class="font-weight">{{__('backend.'.$data->data->available_time_slot->day)}}, {{date('g:i',strtotime($data->data->available_time_slot->time))}} {{__('backend.'.date('A',strtotime($data->data->available_time_slot->time)))}}</span></span>
                           @endif
                            <span>{{__('backend.Opening_Hours')}} : <span class="font-weight">{{date('g:i',strtotime($data->data->start_time))}} {{__('backend.'.date('A',strtotime($data->data->start_time)))}} - {{date('g:i',strtotime($data->data->end_time))}} {{__('backend.'.date('A',strtotime($data->data->end_time)))}}</span></span>
                        </div>
                        <div class="amenities-cls">
                            <div class="ame-title">
                                <h4>{{__('backend.Amenities')}}</h4>
                            </div>
                            <div class="ameni-row">
                                @foreach($data->data->facility_details->facility_amenities as $amenity)
                                <div class="ameni-col">
                                    <div class="ameni-box">
                                        <div class="ameni-ic">
                                            <img src="{{$amenity->amenity_details->image}}">
                                        </div>
                                        <div class="ameni-name">
                                            <h5>{{$amenity->amenity_details->name}}</h5>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- <div class="pitch-cls">
                            <div class="ame-title">
                                <h4>{{__('backend.Pitch_Type')}}: <span>Turf, Venue Type: Outdoor</span></h4>
                            </div>
                        </div> -->
                        @if(count($data->data->facility_details->facility_rules))
                        <div class="rules-cls">
                            <div class="ame-title">
                                <h4>{{__('backend.Rules')}}</h4>
                            </div>
                            <div class="ameni-row">
                                <div class="rules-col">
                                    <ul>
                                        @foreach($data->data->facility_details->facility_rules as $rule)
                                        <li>
                                            <span class="tick-ic"><img src="{{asset('web/images/tick.png')}}"></span> {{$rule->rules}}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- <div class="rules-cls">
                            <div class="ame-title">
                                <h4>{{__('backend.About_Court')}}</h4>
                            </div>
                            <div class="rules-cls-con">
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<div class="modal fade" id="create_challenge_Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <form data-parsley-validate id="book_court_challenge" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="create_challenge_content" id="create_challenge_content">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="checkout_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><img src="{{asset('web/images/cross.png')}}"></button>
            <!-- Modal body -->
            <div class="modal-body p-0">
            <div class="checkout_content" id="checkout_content">
               
            </div>
            </div>
        </div>
    </div>
</div>
@else
{{$data->message}}
@endif

<script src="{{ asset('js/parsley.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var slug = "{{$slug}}";
        
        if (slug == 'booknow') {
            var timeClass = "{{$data->data->available_time_slot->time_class ?? ''}}";
            $('#datepicker1').val("{{$data->data->available_time_slot->date ?? ''}}");
            $('.slots_'+timeClass).prop("checked", true);
            let form = $('#book_court');
            var formData = new FormData(form[0]);
            var time_slot = $("input[name*='booking_time_slot']:checked").val();
            if(typeof time_slot === "undefined"){
                var message = "{{__('backend.Please_Select_Time')}}"
                toastr.error(message);
                return false;
            }
            submitCourt(formData)
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.datepicker1').datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
            startDate: '+0D',
            endDate: "+14D"
        });
    });
    
//  time slot ajax code 
    $(document).ready(function() {
            $('.datepicker1').on('changeDate', function (e) {
            e.preventDefault();
            var booking_date = $(this).val();
            var id = $('#court_id').val();
            // alert(id);
                var url = "{{ url('check-booked-timeslot')}}/"+booking_date+'/'+id;
                $('#group_loader').fadeIn();
                // var values = $('#book_court').serialize();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    // data: formData,
                    type: 'GET',
                    cache: false,
                    contentType: false,
                    processData: false,
                    // complete: function() {
                    //     complete(_this)
                    // },
                    success: function(result) {
                        // console.log(result, 'success');
                        $("input.time_slot").prop("disabled", false).removeClass('disable_slot').prop("checked", false);

                        if (result.status == true) {
                            selectedTimeSlotArr = [];
                            $("input[name^='booking_time_slot']").prop("checked", false);
                            $.each(result.data, function( index, value ) {
                                $("input.slots_"+value).prop("disabled", true).addClass('disable_slot').prop("checked", false);
                            });
                        } else {
                            $("input.time_slot").prop("disabled", false).removeClass('disable_slot').prop("checked", false);
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
        });
    });


    // book court with check out
    $(document).ready(function() {
        $('#book_court').parsley();

        $(document).on('submit', "#book_court", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var time_slot = $("input[name*='booking_time_slot']:checked").val();
            if(typeof time_slot === "undefined"){
                var message = "{{__('backend.Please_Select_Time')}}"
                toastr.error(message);
                return false;
            }
            submitCourt(formData);
        });
    });

    function submitCourt(formData){
           var url = "{{ route('web.book_court_checkout')}}";
            $('#group_loader').fadeIn();
            // var values = $('#book_court').serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
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
                    $('#checkout_content').html(result);
                    $('body').removeClass('open_filter');
                    $('#checkout_model').modal('show');
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
//  create challenge  ajax code 

    $(document).ready(function() {
        $('#book_court').parsley();
        $(document).on('submit', "#book_court_challenge", function(e) {
            e.preventDefault();
            // var _this = $(this);
            var formData = new FormData(this);
            // console.log(formData);return false;
            var time_slot = $("input[name*='booking_time_slot']:checked").val();
            if(typeof time_slot === "undefined"){
                var message = "{{__('backend.Please_Select_Time')}}"
                toastr.error(message);
                return false;
            }
                var url = "{{ route('web.book_court_checkout')}}";
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
                        $('#create_challenge_Modal').modal('hide');
                        $('#checkout_content').html(result);
                        $('#checkout_model').modal('show');
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
    $(document).on('click', '.create_challenge', function(e) {
        e.preventDefault();
        $('#view_response').empty();
        id = $(this).attr('data-court_id');
        var booking_date = $('#datepicker1').val();
        var selected_slot = [];
        var disabled_slot = [];
        $("input[name^='booking_time_slot']:checked").map(function(){
        selected_slot.push($(this).val());
        });
        $("input[name^='booking_time_slot']:disabled").map(function(){
            disabled_slot.push($(this).val());
        });
        
        $.ajax({
            url: "{{route('web.create_challenge','')}}/" + id,
            dataType: 'html',
            success: function(result) {
                $('#create_challenge_content').html('');
                $('#create_challenge_content').html(result);
                $('body').removeClass('open_filter');
                if(booking_date != ''){
                    $('.datepicker1').val(booking_date);
                }
                if(selected_slot != ''){
                    $.each(selected_slot, function(index, value) {
                    objDate =  value.replace(":", "-");
                    $("input.slots_"+objDate).prop("checked", true);
                });
                }
                if(disabled_slot != ''){
                    $.each(disabled_slot, function(index, value) {
                    objDate =  value.replace(":", "-");
                    $("input.slots_"+objDate).prop("disabled", true).addClass('disable_slot').prop("checked", false);
                });
                }
            }
        });
        $('#create_challenge_Modal').modal('show');
    });
    //  book court ajax code 
    $(document).ready(function() {
        $('#checkout_form').parsley();
        $(document).on('submit', "#checkout_form", function(e) {
            e.preventDefault();
        
            var formData = new FormData(this);
            // alert(formData.get('payment_type'));
            // return false;
            if(formData.get('payment_type') == 'cash'){
                var response = confirm("{{ __('backend.confirm_box_cash_permission') }}");
            }else{
                 var response = 'online';
            }
            if (response) {
                $("#overlay").fadeIn(30);
                var url = "{{ route('web.book_court')}}";
                $('#group_loader').fadeIn();
                // var values = $('#book_court').serialize();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: formData,
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    complete: function() {
                        complete(_this)
                    },
                    success: function(result) {
                        $("#overlay").fadeOut(30);
                        if (result.status) {
                            if (result.payment) {
                              window.location.href = "{{url('payment/')}}";
                            }else{
                                toastr.success(result.message);
                                $('#checkout_model').modal('hide');
                                window.location.href = "{{route('web.thank.you')}}?slug=booking_cash/"+result.data.id;
                            }
                            
                        } else {
                            toastr.error(result.message)
                            $('.save').prop('disabled', false);
                            $('.formloader').css("display", "none");
                        }
                        $('#checkout_form').parsley().reset();
                    },
                    error: function(jqXHR, textStatus, textStatus) {
                        $("#overlay").fadeOut(30);
                        console.log(textStatus, 'error');
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
    var selectedTimeSlotArr = [];
    // change price 
    $(document).on('change', '.time_slot', function(e) {
        e.preventDefault();
        var hourly_price =  $('#hourly_price').val();
        var selected_slot = [];
        var dataIndex = $(this).attr('data-index');

        if (!selectedTimeSlotArr.includes(dataIndex.toString())) {
            selectedTimeSlotArr.push(dataIndex);

        } else {
            selectedTimeSlotArr.splice($.inArray(dataIndex, selectedTimeSlotArr), 1);
        }
        var onePlus = parseInt(dataIndex) + 1;
        var oneMinus = parseInt(dataIndex) - 1;
        var total=$("input[name^='booking_time_slot']:checked").length;

        if (selectedTimeSlotArr.includes(onePlus.toString()) || selectedTimeSlotArr.includes(oneMinus.toString())) {

            if ($("input[name='booking_time_slot["+dataIndex+"][start_time]']").prop("checked") != true & selectedTimeSlotArr.includes(onePlus.toString()) & selectedTimeSlotArr.includes(oneMinus.toString())) {
                selectedTimeSlotArr = [];
                total = 1;
                $("input[name^='booking_time_slot']").prop("checked", false);
            }

        } else {

            if (total > 1) {
                selectedTimeSlotArr = [];
                $("input[name^='booking_time_slot']").prop("checked", false);
                
                total = 1;
                selectedTimeSlotArr.push(dataIndex)
                $("input[name='booking_time_slot["+dataIndex+"][start_time]']").prop("checked", true);
            }
        }

        if (total > 0) {
            var total_price = total * hourly_price;
            $('#price').text(total_price);
        }
    });
  

    function change_court($this) {
        id = $($this).find("option:selected").val();
        window.location.href = "{{route('web.court_detail','')}}/" + id;
    };
</script>
@endsection