@if($users)
<div class="row">
 <div class="col-md-12">
    <!-- <center>
        <?php $imageUrl = (!empty($users->image)) ? $users->image : 'image.png'; ?>
        @if(file_exists('uploads/user_profile/'.$imageUrl))
            <img src="{{ URL::asset('uploads/user_profile')}}/{{$imageUrl}}" alt="user" class="img-circle"  width="100" height="100">
        @else
            <img src="{{ URL::asset('images/image.png')}}" alt="user" class="img-circle" width="100" height="100">

        @endif
    </center> -->
<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
            <tr>
                <td><strong>Name:</strong></td>
                @if(!empty($users->name))
                    <td>{{$users->name}}</td>
                @else
                    <td>No Name</td>
                @endif    
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                @if(!empty($users->email))
                    <td>{{$users->email}}</td>
                @else
                    <td>No Email</td>
                @endif        
            </tr>
            <tr>
                <td><strong>Mobile:</strong></td>
                @if(isset($users->mobile))
                    <td>{{$users->country_code}}{{$users->mobile}}</td>
                @else 
                    <td>No Mobile</td>
                @endif        
            </tr>
            <!-- <tr>
                <td><strong>Dob:</strong></td>
                @if(isset($users->dob))
                    <td>{{$users->dob}}</td>
                @else 
                    <td>No Date Of Birth</td> 
                @endif       
            </tr> -->
            <tr>
                <td><strong>Type:</strong></td>
                <td>
                    @if($users->type  === 1)

                    @else
                        Subadmin
                    @endif
                </td>
            </tr>
            <!-- <tr>
                <td><strong>Gender:</strong></td>
                <td>
                    @if($users->gender  === 1)
                        Female
                    @else
                        Male
                    @endif
                </td>
            </tr> -->
            <!-- <tr>
                <td><strong>Address:</strong></td>
                @if(isset($users->address))
                    <td>{{$users->address}}</td>
                @else 
                    <td>No Address</td>
                @endif        
            </tr> -->
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    @if($users->status  === 1)
                        Active
                    @else
                        Deactive
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Created At:</strong></td>
                <td>{{date('j F, Y', strtotime($users->created_at))}} </td>
            </tr>
    </table>
  </div>
  </div>
</div>
@endif