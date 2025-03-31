@if($contact_us)
<div class="row">
 <div class="col-md-12">
 <div class="table-responsive">
  <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
        <tr>
            <td><strong>{{__('backend.name')}}:</strong></td>
            <td>{{$contact_us->name}}</td>
        </tr>
        <tr>
            <td><strong>{{__('backend.Email')}}:</strong></td>
            <td>{{$contact_us->email}}</td>
        </tr>
        <tr>
            <td><strong>{{__('backend.Mobile_Number')}}:</strong></td>
            <td>{{$contact_us->country_code}}-{{$contact_us->mobile}}</td>
        </tr>
        <tr>
            <td><strong>{{__('backend.Message')}}:</strong></td>
            <td>{{$contact_us->message}}</td>
        </tr>
        <tr>
            <td><strong>{{__('backend.Status')}}:</strong></td>
            <td>
                @if($contact_us->status  === 1)
                {{__('backend.Active')}}
                @else
                {{__('backend.Deactive')}}                    
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>{{__('backend.Created_At')}} :</strong></td>
            <td>{{date('j F, Y', strtotime($contact_us->created_at))}} </td>
        </tr>
  </table>
  </div>      
  </div>      
  </div>
@endif