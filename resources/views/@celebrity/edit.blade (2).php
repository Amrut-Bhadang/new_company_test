<form method="PUT" action="{{ url('api/celebrity/'.$celebrity->id) }}" id="edit_role">
    @csrf
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="first_name">First Name *</label>
          <input type="text" name="first_name" value="{{$celebrity->first_name}}" id="first_name" class="form-control" placeholder="First Name" data-parsley-required="true"  />
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="last_name">Last Name *</label>
          <input type="text" name="last_name" value="{{$celebrity->last_name}}" id="last_name" class="form-control" placeholder="Last Name" data-parsley-required="true"  />
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="email">Email *</label>
          <input type="text" name="email" value="{{$celebrity->email}}" id="email" class="form-control" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
        <label class="control-label" for="mobile">Mobile *</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
            <select name="country_code" class="form-control" style="width:180px" data-parsley-required="true" >
              @foreach ($country as $country)
                  <option value="{{ $country->phonecode }}"  {{ $celebrity->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
              @endforeach
            </select>
            </div>
            <input type="text" name="mobile" value="{{$celebrity->mobile}}" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="10" data-parsley-maxlength="10"/>
          </div> 
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="genres">Genres *</label>
          <select name="genres" class="form-control" data-parsley-required="true" >
          <option >-- Select Genres --</option>
              @foreach ($genres as $genres)
                  <option value="{{ $genres->id }}" {{ $celebrity->genres== $genres->id ?'selected':'' }}>{{ ucwords($genres->name)  }}</option>
              @endforeach
            </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="password">Password*</label>
          <input type="password" name="password" value="" id="edit_password" class="form-control" placeholder="Password"  />
          <span class="text-muted">{{__('Leave blank if you don’t want to change password.')}}</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="confirm_password">Confirm password*</label>
          <input type="password" name="confirm_password" value="" id="edit_confirm_password" class="form-control" placeholder="Confirm password" data-parsley-equalto="#edit_password"/>
        </div>
       
      </div>
    </div> 
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="address">Address </label>
          <input type="text" name="editaddress" id="editaddress" value="{{$celebrity->address}}" onfocus="geolocate()" class="form-control" placeholder="Address" />
          <input type="hidden" class="latitude" id='editlatitude' name="editlatitude" value="{{$celebrity->latitude}}"/>
          <input type="hidden" class="longitude" id='editlongitude' name="editlongitude" value="{{$celebrity->longitude}}"/>
        </div>
      </div>
    </div>
   
    <div class="col-md-12">
        <style>
              #map_canvas_edit {
                  width: 100%;
                  height: 200px;
                }
                /* Optional: Makes the sample page fill the window. */
                html, body {
                  height: 100%;
                  margin: 0;
                  padding: 0;
                }                                                
          </style>
          <div class="form-group">
            <div id="map_canvas_edit"></div>	
          </div>
        </div>               

    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>

</form>

<script>
    $(document).ready(function(){
$('.select2').select2();
$('#edit_role').parsley();
$("#edit_role").on('submit',function(e){ 
  e.preventDefault();
  var _this=$(this); 
    $('#group_loader').fadeIn();
    var values = $('#edit_role').serialize();
    $.ajax({
    url:'{{ url('api/celebrity/'.$celebrity->id) }}',
    dataType:'json',
    data:values,
    type:'PUT',
    beforeSend: function (){before(_this)},
    // hides the loader after completion of request, whether successfull or failor.
    complete: function (){complete(_this)},
    success:function(result){
          toastr.success(`Celebrity ${result.name} has been Updated!`)
          setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
          $('#edit_role').parsley().reset();
          ajax_datatable.draw();
          location.reload();
      },
    error:function(jqXHR,textStatus,textStatus){
      if(jqXHR.responseJSON.errors){
        $.each(jqXHR.responseJSON.errors, function( index, value ) {
          toastr.error(value)
        });
      }else{
        toastr.error(jqXHR.responseJSON.message)
      }
    }
      });
      return false;   
    });
});
</script>
<script>
	var geocoder;
	var map;
	var marker;
	var infowindow = new google.maps.InfoWindow({
		size: new google.maps.Size(150, 50)
	});
    initialize();
	autoload({{$chef->latitude}}, {{$chef->longitude}});

	
    var autocomplete;
    function initialize() {
      
		autocomplete = new google.maps.places.Autocomplete((document.getElementById('editaddress')),{ types: [] });
   
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
            // place variable will have all the information you are looking for.
            $('#latitude').val(place.geometry['location'].lat());
            $('#longitude').val(place.geometry['location'].lng());
			codeAddress();
		});
    }
	function autoload(latitude,longitude) {
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(latitude, longitude);
		var mapOptions = {
			zoom: 13,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById('map_canvas_edit'), mapOptions);
		google.maps.event.addListener(map, 'click', function() {
			infowindow.close();
		});
		
		marker = new google.maps.Marker({
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            position: {lat:latitude, lng: longitude}
          });
          marker.addListener('click', toggleBounce);
	}
	
	function toggleBounce() 
	{
        if (marker.getAnimation() !== null) {
            marker.setAnimation(null);
        } else {
            marker.setAnimation(google.maps.Animation.BOUNCE);
        }
    }
	function geocodePosition(pos) {
		geocoder.geocode({
			latLng: pos
		}, function(responses) {
			if (responses && responses.length > 0) {
				marker.formatted_address = responses[0].formatted_address;
			} else {
				marker.formatted_address = 'Cannot determine address at this location.';
			}
			$('#editaddress').val(marker.formatted_address);
			$('#editlatitude').val(marker.getPosition().lat());
			$('#editlongitude').val(marker.getPosition().lng());
			infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
			infowindow.open(map, marker);
		});
	}

	function codeAddress() {
		var address = document.getElementById('editaddress').value;
		geocoder.geocode({
			'address': address
		}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			if (marker) {
				marker.setMap(null);
				if (infowindow) infowindow.close();
			}
			marker = new google.maps.Marker({
				map: map,
				draggable: true,
				animation: google.maps.Animation.DROP,
				position: results[0].geometry.location
		 });
		google.maps.event.addListener(marker, 'dragend', function() {
			geocodePosition(marker.getPosition());
		});
		google.maps.event.addListener(marker, 'click', function() {
			if (marker.formatted_address) {
			  infowindow.setContent(marker.formatted_address + "<br>coordinates2: " + marker.getPosition().toUrlValue(6));
			  $('#address').val(marker.formatted_address);
			} else {
			  infowindow.setContent(address + "<br>coordinates3: " + marker.getPosition().toUrlValue(6));
			  $('#editaddress').val(address);
			}		
			$('#editlatitude').val(marker.getPosition().lat());
			$('#editlongitude').val(marker.getPosition().lng());
			infowindow.open(map, marker);
		});
			google.maps.event.trigger(marker, 'click');
		} else {
		  alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}

</script>