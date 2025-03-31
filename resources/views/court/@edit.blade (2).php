<?php
use App\Models\Language;
use App\Models\CourtLang;
$language = Language::pluck('lang')->toArray();

?>
<style type="text/css">
  form i {
    margin-left: -30px;
    cursor: pointer;
    position: absolute;
    top: 34px;
  }
</style>
<form method="PUT" action="{{ url('api/courts/'.$users->id) }}" id="edit_role">
    @csrf
    <div class="row">
      <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="facility_id">Facility</label>
              <select name="facility_id" id="facility_id" class="form-control select2"  data-placeholder="Select Facility" data-dropdown-css-class="select2-primary">
               <option value="">--Select Facility--</option>
               <option value="Facility 1" {{ $users->facility_id=='Facility 1'?'selected':'' }}>Facility 1</option>
               <option value="Facility 2" {{ $users->facility_id=='Facility 2'?'selected':'' }}>Facility 2</option>
               <option value="Facility 3" {{ $users->facility_id=='Facility 3'?'selected':'' }}>Facility 3</option>
              </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="category_id">Category*</label>
              <select name="category_id" id="category_id" data-parsley-required="true" class="form-control select2"  data-placeholder="Select Category" data-dropdown-css-class="select2-primary">
               <option value="">--Select Category--</option>
               <option value="Category 1" {{ $users->category_id=='Category 1'?'selected':'' }}>Category 1</option>
               <option value="Category 2" {{ $users->category_id=='Category 2'?'selected':'' }}>Category 2</option>
               <option value="Category 3" {{ $users->category_id=='Category 3'?'selected':'' }}>Category 3</option>
              </select>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      @if($language)
        @foreach($language as  $key => $lang)
        <?php
          if(isset($users))
          {
              $langData = CourtLang::where(['lang'=>$lang,'court_id'=>$users->id])->first();  
          }
        ?>
        <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"> {{__('Court Name')}} ({{__('backend.'.$lang)}})*</label>
                <input type="text" name="court_name[{{$lang}}]" data-parsley-required="true" value="{{$langData->court_name}}" id="court_name" class="form-control" placeholder=" Court Name"  />
              </div>
            </div>
        @endforeach
      @endif
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="address">Address*</label>
          <input type="text" placeholder="Address" name="address" class="form-control" id="editaddress" value="{{$users->address}}" autocomplete="off" data-parsley-required="true">
          <input type="hidden" class="latitude" id='editlatitude' name="latitude" value="{{$users->latitude}}" />
          <input type="hidden" class="longitude" id='editlongitude' name="longitude" value="{{$users->longitude}}" />
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="minimum_hour_book">Minimum Hours Book*</label>
          <input type="text" name="minimum_hour_book" value="{{$users->minimum_hour_book}}" id="minimum_hour_book" class="form-control" placeholder="Minimum Hours Book" autocomplete="off" data-parsley-required="true" />
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label" for="hourly_price">Hourly Price*</label>
          <input type="text" name="hourly_price" value="{{$users->hourly_price}}" id="hourly_price" class="form-control" placeholder="Hourly Price" autocomplete="off" data-parsley-required="true" />
        </div>
      </div>
      <div class="col-md-6 mb-2">
          <label  for="image">Image</label>
          <div class="form-group">
            <div class="input-group">
              <div id="image_preview"><img height="100" width="100" id="previewing" src="{{$users->image}}"></div>
              <input type="file" id="file" name="image" class="form-control">
            </div>
            <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
          </div>
      </div>
    </div>
  </div>
      
    </div>
    <hr style="margin: 1em -15px">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>

</form>

<script>
  var geocoder;
  var map;
  var marker;
  var infowindow = new google.maps.InfoWindow({
    size: new google.maps.Size(150, 50)
  });
    initialize();
     autoload({{$users->latitude}}, {{$users->longitude}});


    var autocomplete;
    function initialize() {

    autocomplete = new google.maps.places.Autocomplete((document.getElementById('editaddress')),{ types: [] });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      var place = autocomplete.getPlace();
            // place variable will have all the information you are looking for.
            $('#editlatitude').val(place.geometry['location'].lat());
            $('#editlongitude').val(place.geometry['location'].lng());
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

<script>
$(document).ready(function(){
  $('.select2').select2();
  $('#edit_role').parsley();
  $("#edit_role").on('submit',function(e){ 
    e.preventDefault();
    var _this=$(this);
    var formData = new FormData(this);
    formData.append('_method', 'put');
      $('#group_loader').fadeIn();
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
      url:'{{ url('api/courts/'.$users->id) }}',
      dataType:'json',
      data:formData,
      type:'POST',
      cache:false,
      contentType: false,
      processData: false,
      beforeSend: function (){before(_this)},
      // hides the loader after completion of request, whether successfull or failor.
      complete: function (){complete(_this)},
      success:function(result){
            toastr.success(`Court has been Updated!`)
            setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
            $('#edit_role').parsley().reset();
            ajax_datatable.draw();
            window.location.reload();

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