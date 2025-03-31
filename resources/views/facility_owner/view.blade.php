@if($record)
<div class="row">
 <div class="col-md-12">
   
<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
            <tr>
                <td><strong>{{ __('backend.Profile_Pic') }}:</strong></td>
                @if(!empty($record->image))
                    <?php $imageUrl = (!empty($record->image)) ? $record->image : 'image.png'; ?>
                    <td>
                        @if(file_exists('uploads/user_profile/'.$imageUrl))
                            <img src="{{ URL::asset('uploads/user_profile')}}/{{$imageUrl}}" alt="user" class="img-circle"  width="100" height="100">
                        @else
                            <img src="{{ $record->image }}" alt="user" class="img-circle" width="100" height="100">
                        @endif
                    </td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Name') }}:</strong></td>
                @if(!empty($record->name))
                    <td>{{$record->name}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Email') }}:</strong></td>
                @if(!empty($record->email))
                    <td>{{$record->email}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Gender') }}:</strong></td>
                @if(!empty($record->gender))
                    <td>{{$record->gender}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.mobile') }}:</strong></td>
                @if(!empty($record->mobile))
                    <td>{{$record->country_code}}-{{$record->mobile}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            
            <tr>
                <td><strong>{{ __('backend.Status') }}:</strong></td>
                <td>
                    @if($record->status  === 1)
                        {{ __('backend.Active') }}
                    @else
                        {{ __('backend.Deactive') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>{{ __('backend.Registration_Date') }}:</strong></td>
                <td>{{date('j F, Y', strtotime($record->created_at))}} </td>
            </tr>
    </table>
  </div>
  </div>
</div>
@endif