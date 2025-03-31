@if($record)
<div class="row">
 <div class="col-md-12">
    
<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
        
            <tr>
                <td><strong>{{ __('backend.Title') }}:</strong></td>
                @if(!empty($record->title))
                    <td>{{$record->title}}</td>
                @else
                    <td>{{ __('backend.No_Data') }}</td>
                @endif    
            </tr>
            <tr>
                <td><strong>{{ __('backend.description') }}:</strong></td>
                @if(!empty($record->description))
                    <td>{{$record->description}}</td>
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
                <td><strong>{{ __('backend.Created_Date') }}:</strong></td>
                <td>{{date('j F, Y', strtotime($record->created_at))}} </td>
            </tr>
    </table>
  </div>
  </div>
</div>
@endif