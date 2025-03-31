@if($record)
<div class="row">
    <div class="col-md-12">

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
                <tr>
                    <td><strong>{{ __('backend.Image') }}:</strong></td>
                    @if(!empty($record->image))
                    <?php $imageUrl = (!empty($record->image)) ? $record->image : 'image.png'; ?>
                    <td>
                        @if(file_exists('uploads/user_profile/'.$imageUrl))
                        <img src="{{ URL::asset('uploads/user_profile')}}/{{$imageUrl}}" alt="user" class="img-circle" width="100" height="100">
                        @else
                        <img src="{{ $record->image }}" alt="user" class="img-circle" width="100" height="100">
                        @endif
                    </td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Facility') }}:</strong></td>
                    @if(!empty($record->facility_name))
                    <td>{{$record->facility_name}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Category') }}:</strong></td>
                    @if(!empty($record->court_category_name))
                    <td>{{$record->court_category_name}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Court_Name') }}:</strong></td>
                    @if(!empty($record->court_name))
                    <td>{{$record->court_name}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <!-- <tr>
                <td><strong>{{ __('backend.Minimum_Hours_Book') }}:</strong></td>
                @if(!empty($record->minimum_hour_book))
                    <td>{{$record->minimum_hour_book}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr> -->
                <tr>
                    <td><strong>{{ __('backend.Hourly_Price') }}:</strong></td>
                    @if(!empty($record->hourly_price))
                    <td>{{$record->hourly_price}} AED</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Admin_commission') }}:</strong></td>
                    @if(!empty($record->admin_commission))
                    <td>{{$record->admin_commission}} %</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Position') }}:</strong></td>
                    @if(!empty($record->position))
                    <td>{{$record->position}}</td>
                    @else
                    <td>{{ __('backend.No_Position') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Court_Size') }}:</strong></td>
                    @if(!empty($record->court_size))
                    <td>{{$record->court_size}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>

                <tr>
                    <td><strong>{{ __('backend.Open_Time') }}:</strong></td>
                    @if(!empty($record->start_time))
                    <td>{{$record->start_time}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Close_Time') }}:</strong></td>
                    @if(!empty($record->end_time))
                    <td>{{$record->end_time}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Minimum_Hours_Book') }} ( {{__('backend.Time_Slot')}} ):</strong></td>
                    @if(!empty($record->timeslot))
                    <td>{{$record->timeslot}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Address') }}:</strong></td>
                    @if(isset($record->address))
                    <td>{{$record->address}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Is_Featured') }}:</strong></td>
                    <td>
                        @if($record->is_featured === 1)
                        {{ __('backend.Yes') }}
                        @else
                        {{ __('backend.No') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Popular_Day') }}:</strong></td>
                    @if(isset($record->popular_day))
                    <td>{{$record->popular_day}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Popular_Start_Time') }}:</strong></td>
                    @if(!empty($record->popular_start_time))
                    <td>{{$record->popular_start_time}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Status') }}:</strong></td>
                    <td>
                        @if($record->status === 1)
                        {{ __('backend.Active') }}
                        @else
                        {{ __('backend.Deactive') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Created_At') }}:</strong></td>
                    <td>{{date('j F, Y', strtotime($record->created_at))}} </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endif