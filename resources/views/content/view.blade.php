@if($content)
<div class="row">
 <div class="col-md-12">
 <div class="table-responsive">
  <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
        <tr>
            <td><strong>{{__('backend.name')}}:</strong></td>
            <td>{{$content->name}}</td>
        </tr>
        <tr>
            <td><strong>{{__('backend.description')}}:</strong></td>
            <td>{!! $content->description !!}</td>
        </tr>
        
        <tr>
            <td><strong>{{__('backend.Status')}}:</strong></td>
            <td>
                @if($content->status  === 1)
                {{__('backend.Active')}}
                @else
                {{__('backend.Deactive')}}                    
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>{{__('backend.Created_At')}} :</strong></td>
            <td>{{date('j F, Y', strtotime($content->created_at))}} </td>
        </tr>
  </table>
  </div>      
  </div>      
  </div>
@endif