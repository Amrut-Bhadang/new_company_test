@if($record)
<div class="row">
 <div class="col-md-12">
   
<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
            <tr>
                <td><strong>{{ __('backend.Passbook_Image') }}:</strong></td>
                @if(!empty($record->passbook_image))
                    <?php $imageUrl = (!empty($record->passbook_image)) ? $record->passbook_image : 'image.png'; ?>
                    <td>
                        @if(file_exists('uploads/user_profile/'.$imageUrl))
                            <img src="{{ URL::asset('uploads/user_profile')}}/{{$imageUrl}}" alt="user" class="img-circle"  width="100" height="100">
                        @else
                            <img src="{{ $record->passbook_image }}" alt="user" class="img-circle" width="100" height="100">
                        @endif
                    </td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Account_Holder_Name') }}:</strong></td>
                @if(!empty($record->account_holder_name))
                    <td>{{$record->account_holder_name}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Bank_Name') }}:</strong></td>
                @if(!empty($record->bank_name))
                    <td>{{$record->bank_name}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Bank_Address') }}:</strong></td>
                @if(!empty($record->bank_address))
                    <td>{{$record->bank_address}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Bank_Code') }}:</strong></td>
                @if(!empty($record->bank_code))
                    <td>{{$record->bank_code}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Account_Number') }}:</strong></td>
                @if(!empty($record->account_number))
                    <td>{{$record->account_number}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.Account_Type') }}:</strong></td>
                @if(!empty($record->account_type))
                    <td>{{$record->account_type}}</td>
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
                <td>{{date('j-m-Y', strtotime($record->created_at))}} </td>
            </tr>
    </table>
  </div>
  </div>
</div>
@endif