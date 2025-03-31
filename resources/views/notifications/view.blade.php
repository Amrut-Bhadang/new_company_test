@if($celebrity)
<div class="row">
 <div class="col-md-12">
    <center>
        <?php $imageUrl = (!empty($celebrity->image)) ? $celebrity->image : 'image.png'; ?>
        @if(file_exists('uploads/user_profile/'.$imageUrl))
            <img src="{{ URL::asset('uploads/user_profile')}}/{{$imageUrl}}" alt="user" class="img-circle"  width="100" height="100">
        @else
            <img src="{{ URL::asset('images/image.png')}}" alt="user" class="img-circle" width="100" height="100">
            
        @endif
    </center>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{$celebrity->name}}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{$celebrity->email}}</td>
            </tr>
            <tr>
                <td><strong>Mobile:</strong></td>
                <td>{{$celebrity->country_code}} {{$celebrity->mobile}}</td>
            </tr>
            <tr>
                <td><strong>Genres:</strong></td>
                <td>{{ucwords($celebrity->genres)}}</td>
            </tr>
            <tr>
                <td><strong>Type:</strong></td>
                <td>
                    @if($celebrity->type  == 3)
                        Celebrity
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td>{{$celebrity->address}}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    @if($celebrity->status  === 1)
                        Active
                    @else
                        Deactive
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Created At:</strong></td>
                <td>{{date('j F, Y', strtotime($celebrity->created_at))}} </td>
            </tr>
		</table>
	</div>      
  </div>      
</div>
@endif