@if($record)
<div class="row">
    <div class="col-md-12">

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">

                <tr>
                    <td><strong>{{ __('backend.User_Name') }}:</strong></td>
                    @if(!empty($record->user_name))
                    <td>{{$record->user_name}} ({{$record->country_code}} - {{$record->mobile}}) {{$record->user_email}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }} ({{$record->country_code ?? ''}} - {{$record->mobile ?? ''}}) {{$record->user_email ?? ''}}</td>
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
                 @if(!empty($record->order_status) && $record->order_status =='Cancelled')
                    <tr>
                        <td><strong>{{ __('backend.Admin_Deduct_Percentage') }}:</strong></td>

                        @if(!empty($logAdminDeductPer))
                        <td>{{$logAdminDeductPer}}</td>
                        @else
                        <td>{{ __('backend.No_Data') }}</td>
                        @endif

                    </tr>
                    <tr>
                        <td><strong>{{ __('backend.Admin_Deduct_Amount') }}:</strong></td>

                        @if(!empty($logAdminDeductedAmt))
                        <td>{{$logAdminDeductedAmt}}</td>
                        @else
                        <td>{{ __('backend.No_Data') }}</td>
                        @endif

                    </tr>
                    <tr>
                        <td><strong>{{ __('backend.Action_By_User') }}:</strong></td>

                        @if(!empty($actionByUsename))
                        <td>{{$actionByUsename}}</td>
                        @else
                        <td>{{ __('backend.No_Data') }}</td>
                        @endif

                    </tr>
                @endif
                <tr>
                    <td><strong>{{ __('backend.Created_At') }}:</strong></td>
                    <td>{{date('j F, Y', strtotime($record->created_at))}} </td>
                </tr>
                @if(!empty($record->booking_type == 'challenge'))
                
                <table class="table table-striped table-bordered table-condensed" id="table2" style="width: 100%;">
                    <tr>
                        <td><strong>{{__('backend.User_Name')}} </strong></td>
                        <td><strong>{{ __('backend.Mode_of_Payment') }}</strong></td>
                        <td><strong>{{ __('backend.Amount') }} </strong></td>
                    </tr>
                    @foreach($record->bookingChallenges as $bookingChallenges)
                    <tr>
                    <td>{{$bookingChallenges->userDetails['name']}} ({{$bookingChallenges->userDetails['country_code']}} - {{$bookingChallenges->userDetails['mobile']}})</td>
                    <td>{{$bookingChallenges->payment_type}}</td>
                    <td>{{$bookingChallenges->amount}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                
                <br/><br/>
                @if(!empty($record->order_status == 'Cancelled'))
                 <b>{{__('backend.Cancellation_logs')}}</b>
                
                <table class="table table-striped table-bordered table-condensed" id="table2" style="width: 100%;">
                    <tr>
                        <td><strong>{{__('backend.Cancel_by')}} </strong></td>
                        <td><strong>{{ __('backend.Mode_of_Payment') }}</strong></td>
                        <td><strong>{{ __('backend.Actual_amount') }}</strong></td>
                        <td><strong>{{ __('backend.Admin_amount') }} </strong></td>
                        <td><strong>{{ __('backend.Joiner_amount') }} </strong></td>
                        <td><strong>{{ __('backend.Action_by') }} </strong></td>
                    </tr>
                    @foreach($alllogdata as $alllogdatas)
                    <tr>
                    <td>{{$alllogdatas->action_by}} </td>
                    <td>{{$alllogdatas->payment_type}}</td>
                    <td>{{$alllogdatas->actual_amount}}</td>
                    <td>{{$alllogdatas->amt_after_admin_comm_amount}}</td>
                    <td>{{$alllogdatas->amt_after_joiner_comm_amount}}</td>
                    <td>{{$alllogdatas->action_by_name}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif
            </table>
        </div>
    </div>
</div>
@endif