@if($record)
<div class="row">
    <div class="col-md-12">

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">

                <tr>
                    <td><strong>{{ __('backend.User_Name') }}:</strong></td>
                    @if(!empty($record->user_name))
                    <td>{{$record->user_name}}</td>
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
                <tr>
                    <td><strong>{{ __('backend.Facility_Name') }}:</strong></td>
                    @if(!empty($record->facility_name))
                    <td>{{$record->facility_name}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.booking_type') }}:</strong></td>
                    @if(!empty($record->booking_type))
                    <td class="text-capitalize">{{$record->booking_type}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Booking_Date') }}:</strong></td>
                    @if(!empty($record->booking_date))
                    <td>{{$record->booking_date}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Time_Slot') }}:</strong></td>
                    @if(!empty($record->booking_time_slot))
                    <td>{{$record->booking_time_slot}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Amount') }}:</strong></td>
                    @if(!empty($record->total_amount))
                    <td>{{$record->total_amount}} {{__('backend.AED')}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Paid_Amount') }}:</strong></td>
                    @if(!empty($record->paid_amount))
                    <td>{{$record->paid_amount}} {{__('backend.AED')}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Admin_Commission') }}:</strong></td>
                    @if(!empty($record->admin_commission_amount))
                    <td>{{$record->admin_commission_amount}} {{__('backend.AED')}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Mode_of_Payment') }}:</strong></td>
                    @if(!empty($record->payment_type))
                    <td>{{$record->payment_type}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>

                <tr>
                    <td><strong>{{ __('backend.Status') }}:</strong></td>

                    @if(!empty($record->order_status))
                    <td>{{$record->order_status}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif

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