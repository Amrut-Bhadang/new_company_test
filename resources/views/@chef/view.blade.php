@if($chef)
<div class="row">
 <div class="col-md-12">
    <center>
        <?php $imageUrl = (!empty($chef->image)) ? $chef->image : 'image.png'; ?>
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
                <td>{{$chef->name}}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{$chef->email}}</td>
            </tr>
            <tr>
                <td><strong>Mobile:</strong></td>
                <td>{{$chef->country_code}} {{$chef->mobile}}</td>
            </tr>
            <tr>
                <td><strong>Type:</strong></td>
                <td>
                    @if($chef->type  == 2)
                        Chef
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Food License :</strong></td>
                <td>{{$chef->food_license}}</td>
            </tr>
            @if($chef->food_license == 'Yes')
            <tr>
                <td><strong>License Number:</strong></td>
                <td>{{$chef->license_number}}</td>
            </tr>
            <tr>
                <td><strong>License Image:</strong></td>
                <td><img width="80" height="80" src="{{ URL::asset('uploads/food-license')}}/{{$chef->license_image}}"/></td>
            </tr>
            @endif
            <tr>
                <td><strong>Address:</strong></td>
                <td>{{$chef->address}}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    @if($chef->status  === 1)
                        Active
                    @else
                        In-active
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Created At:</strong></td>
                <td>{{date('j F, Y', strtotime($chef->created_at))}} </td>
            </tr>
    </table>
	</div>
  </div>      
</div>
@endif