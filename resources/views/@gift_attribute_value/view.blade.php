@if($attribute_value)
<div class="row">
 <div class="col-md-12">
 <div class="table-responsive">
  <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
        <tr>
            <td><strong>Attribute Name:</strong></td>
            <td>{{$attribute_value->attributes_name}}</td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>
                @if($attribute_value->status  === 1)
                    Active
                @else
                    Deactive
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Created At:</strong></td>
            <td>{{date('j F, Y', strtotime($attribute_value->created_at))}} </td>
        </tr>
        <tr>
            <td><strong>Attribute Values:</strong></td>
            <td>
            @foreach($attribute_value_langs as $ingredients)
                {{$ingredients->name}}<br />
            @endforeach
            </td>
        </tr>

    </table>
	</div>
  </div>      
  </div>
@endif