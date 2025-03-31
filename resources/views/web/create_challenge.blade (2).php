<input type="hidden" id="court_id" name="court_id" value="{{$data->data->id}}">
<input type="hidden" name="facility_id" value="{{$data->data->facility_id}}">
<input type="hidden" name="facility_owner_id" value="{{$data->data->facility_owner_id}}">
<input type="hidden" name="timeslot" value="{{$data->data->timeslot}}">
<input type="hidden" name="hourly_price" value="{{$data->data->hourly_price}}">
<input type="hidden" name="booking_type" value="challenge">
<div class="court_dtl_form">
    <div class="create_ch_head">
        <h4>{{__('backend.Create_Challenge')}}</h4>
        <div class="create_ch_meta">
            <span class="create_ch_meta_price">{{__('backend.AED')}} <span class="price_popup">{{$data->data->hourly_price}} </span> / {{__('backend.Slot')}}</span>
            <div class="price-review-sec">
                <div class="review">
                    <span class="star-ic">
                        <img src="{{asset('web/images/star.png')}}">
                    </span>
                    <span>{{$data->data->average_rating}} ({{$data->data->total_rating}} {{__('backend.Review')}})</span>
                </div>
            </div>
        </div>
    </div>
    <div class="court_dtl_form_in">
        <div class="court_dtl_sidebar_itm sidebar_itm_pitch">
            <div class="sidebar_itm_info">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <div class="sidebar_title">
                                <h4>{{__('backend.Challenge_Type')}}</h4>
                            </div>
                            <select class="form-control" name="challenge_type">
                                <option value="public">{{__('backend.Public')}}</option>
                                <option value="private">{{__('backend.Private')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <div class="sidebar_title">
                                <h4>{{__('backend.Select_Court')}}</h4>
                            </div>
                            <select class="form-control" onchange="change_court(this)">
                                @foreach($data->data->available_court as $court)
                                <option value="{{$court->id}}" {{$data->data->court_name == $court->court_name ? 'selected':''}}>{{$court->court_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
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
                <input type="text" class="form-control datepicker1" id="datepicker1" data-parsley-required="true" name="booking_date" value="" placeholder="{{__('backend.Select_Date')}}">
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
                        <input type="checkbox" class="time_slot slots_{{$slot_class}}" data-index="{{$i}}" name="booking_time_slot[{{$i}}][start_time]" value="{{$time}}">
                        <span class="checkmark">
                            <!-- {{$time}} -->
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
        <button type="submit" class="btn-primary btn-block">{{__('backend.Create_Challenge_with_50_Booking')}}</button>
    </div>
</div>

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
                        if (result.status == true) {
                            $.each(result.data, function( index, value ) {
                                console.log('slots_'+value);
                                $("input.slots_"+value).prop("disabled", true).addClass('disable_slot').prop("checked", false);
                            });
                        } else {
                            $("input.time_slot").prop("disabled", false).removeClass('disable_slot').prop("checked", false);
                            // toastr.error(result.message)
                            // $('.save').prop('disabled', false);
                            // $('.formloader').css("display", "none");
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


    function change_court($this) {
        // $('#create_challenge_Modal').modal('hide');
        id = $($this).find("option:selected").val();
        $.ajax({
            url: "{{route('web.create_challenge','')}}/" + id,
            dataType: 'html',
            success: function(result) {
                $('#create_challenge_content').empty();
                $('#create_challenge_content').html(result);
            }
        });

    };
</script>