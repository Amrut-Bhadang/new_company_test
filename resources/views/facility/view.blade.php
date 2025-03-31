@if($record)
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
                <tr>
                    <td><strong>{{ __('backend.Facility_image') }}:</strong></td>
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
                    <td><strong>{{ __('backend.Facility_Name') }}:</strong></td>
                    @if(!empty($record->name))
                    <td>{{$record->name}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Facility_Owner') }}:</strong></td>
                    @if(!empty($record->facility_owner_name))
                    <td>{{$record->facility_owner_name}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Address') }}:</strong></td>
                    @if(!empty($record->address))
                    <td>{{$record->address}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Amenities') }}:</strong></td>
                    @if(!empty($record->facility_amenities))
                    <td>{{$record->facility_amenities}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Category') }}:</strong></td>
                    @if(!empty($record->facility_categories))
                    <td>{{$record->facility_categories}}</td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif
                </tr>
                 <tr>
                    <td><strong>{{ __('backend.Commission') }}:</strong></td>
                    @if(!empty($record->commission))
                    <td>{{$record->commission}}</td>
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
                    <td><strong>{{ __('backend.Created_Date') }}:</strong></td>
                    <td>{{date('j F, Y', strtotime($record->created_at))}} </td>
                </tr>
                <tr>
                    <td><strong>{{ __('backend.Rules') }}:</strong></td>
                    @if(count($record->facilityRules))
                    <td>
                        @foreach($record->facilityRules as $rule)
                        {{ $rule->rules }}
                        @endforeach
                    </td>
                    @else
                    <td>{{ __('backend.No_Data') }}</td>
                    @endif

                </tr>
            </table>
        </div>
    </div>
</div>
@endif