<input id="publish-button" type="hidden" value="Click here to Publish"/>
<script data-cfasync="false" src="{{ URL::asset('assets/jquery/email-decode.min.js')}}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ URL::asset('assets/popper/popper.min.js')}}"></script>
<script src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{ URL::asset('dist/js/perfect-scrollbar.jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<!--Wave Effects -->
<script src="{{ URL::asset('dist/js/waves.js')}}"></script>
<!--Menu sidebar -->
<script src="{{ URL::asset('dist/js/sidebarmenu.js')}}"></script>
<script src="{{ URL::asset('dist/js/bootstrap-tagsinput.js')}}"></script>
<!--stickey kit -->
<script src="{{ URL::asset('assets/sticky-kit-master/dist/sticky-kit.min.js')}}"></script>
<script src="{{ URL::asset('assets/sparkline/jquery.sparkline.min.js')}}"></script>
<!--Custom JavaScript -->
<script src="{{ URL::asset('dist/js/custom.min.js')}}"></script>
<!-- Editable -->
<script src="{{ URL::asset('assets/jsgrid/db.js')}}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/jsgrid/jsgrid.min.js')}}"></script>
<script src="{{ URL::asset('dist/js/pages/jsgrid-init.js')}}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('assets/morrisjs/morris.min.js') }}"></script>
<script src="{{ asset('assets/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('assets/d3/d3.min.js') }}"></script>
<script src="{{ asset('assets/c3-master/c3.min.js') }}"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js"></script>
<!-- Get PubNub javascript SDK-->
<!-- <script src="https://cdn.pubnub.com/sdk/javascript/pubnub.4.27.4.min.js"></script> -->
<!-- <script src="https://cdn.pubnub.com/sdk/javascript/pubnub.4.21.7.min.js"></script> -->
<script src="{{ URL::asset('dist/js/pubnub.4.21.7.min.js')}}"></script>



<script type="text/javascript">
  $(document).ready(function(){
    var tz = jstz.determine(); // Determines the time zone of the browser client
    var timezone = tz.name(); //For e.g.:"Asia/Kolkata" for the Indian Time.
    document.cookie = "timezone="+timezone; 
  });
</script>

<?php
    $login_user_data = auth()->user();
    $userType = '';

    if ($login_user_data) {
      $userType = $login_user_data->type;
      $userId = 'main_admin_'.$login_user_data->id;
    }
?>

 @if(auth()->user()->type == 4)   
    <script src="{{ asset('dist/js/dashboard_restro.js') }}"></script>
 @else 
    <script src="{{ asset('dist/js/dashboard1.js') }}"></script>
 @endif

<script type="text/javascript">
  function accountSwitch($this) {
    var userId = $($this).attr('data-userId');
    // alert();
    $.ajax({
        type: 'get',
        data: {_method: 'get', _token: "{{ csrf_token() }}"},
        dataType:'json',
        url: "{!! url('switchAccount' )!!}" + "/" + userId,
        success:function(res){

          if(res.status === 1){ 
            toastr.success(res.message);

            window.location.reload();

          } else {
            toastr.error(res.message);
          }
        },   
        error:function(jqXHR,textStatus,textStatus){
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
    });
  }

  function changeRestroOnOff($this) {
    var restroId = $($this).attr('data-restroId');
    var status = '';

    if ($($this).prop("checked") == true) {
      status = 1;

    } else {
      status = 0;
    }

    if (restroId) {
      $.ajax({
          type: 'POST',
          data: {_method: 'post', _token: "{{ csrf_token() }}", restroId : restroId, status : status},
          dataType:'json',
          url: "{!! url('restaurant/changeRestroOnOff' )!!}",
          success:function(res){

            if(res.status === 1){ 
              toastr.success(res.message);

              // window.location.reload();

            } else {
              toastr.error(res.message);
            }
          },   
          error:function(jqXHR,textStatus,textStatus){
            console.log(jqXHR);
            toastr.error(jqXHR.statusText)
          }
      });
    }
  }
</script>

<script>
    toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
    }
function before(_this=null){
_this.find('.save').prop('disabled',true);
_this.find('.formloader').css("display","inline-block");
}
function complete(_this=null){
_this.find('.save').prop('disabled',false);
_this.find('.formloader').css("display","none");
}
$(document).ready(function(){
  $('.kpip_logo_mini').css('display','inline-block').toggle();

  $('[data-widget="pushmenu"]').click(function(){
    if($( window ).width()>1000){
      $('.kpip_logo_mini').fadeToggle();
    }
  });
});
</script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyCnC4j2VEpmhzkDbSAPSG27BI4Ux5bwNrk"></script> -->
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCnC4j2VEpmhzkDbSAPSG27BI4Ux5bwNrk&libraries=places"></script> -->
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyAGUOsZuCthRY9GSPOu736D4JO8ik3V-os"></script> -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4"></script>

<script>
	var geocoder;
	var map;
	var marker;
	var infowindow = new google.maps.InfoWindow({
		size: new google.maps.Size(150, 50)
	});
    initialize();
	autoload(25.204849, 55.270783);


    var autocomplete;
    function initialize() {

		autocomplete = new google.maps.places.Autocomplete((document.getElementById('address')),{ types: [] });

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
		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
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
			$('#address').val(marker.formatted_address);
			$('#latitude').val(marker.getPosition().lat());
			$('#longitude').val(marker.getPosition().lng());
			infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
			infowindow.open(map, marker);
		});
	}

	function codeAddress() {
		var address = document.getElementById('address').value;
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
			  $('#address').val(address);
			}
			$('#latitude').val(marker.getPosition().lat());
			$('#longitude').val(marker.getPosition().lng());
			infowindow.open(map, marker);
		});
			google.maps.event.trigger(marker, 'click');
		} else {
		  alert('Geocode was not successful for the following reason: ' + status);
		}
	});
}

</script>

<?php
    $login_user_data = auth()->user();
    $userType = '';
    $userId = '';

    if ($login_user_data) {
      $userType = $login_user_data->type;

      if ($userType == 3) {
        $userId = 'player_'.$login_user_data->id;

      } else if ($userType == 0) {
        $userId = 'admin_'.$login_user_data->id;

      } else if ($userType == 1) {
        $userId = 'owner_'.$login_user_data->id;

      } else {
        $userId = $login_user_data->id;
      }
    }
?>

<script>
  const uuid = PubNub.generateUUID();
  // alert('here');
  const pubnub = new PubNub({
    publishKey: 'pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e',
    subscribeKey: 'sub-c-560305a8-8b03-11eb-83e5-b62f35940104',
    uuid: uuid
  });
  var channel_name = '';
  var userId = "<?php echo $userId ?>";

  /*@if($userType == 4)
  @endif*/
  var channel_name = 'pubnub_onboarding_channel_'+userId;
  // alert(channel_name);

  /*const button = document.getElementById('publish-button');

  button.addEventListener('click', () => {
    pubnub.publish({
      channel : "pubnub_onboarding_channel",
      message : {"sender": uuid, "content": "Hello From JavaScript SDK"}
    }, function(status, response) {
      //Handle error here
    });
  });*/

  pubnub.subscribe({
    channels: [channel_name],
    withPresence: true
  });

  pubnub.addListener({
    message: function(event) {
      // let pElement = document.createElement('p');
      toastr.success(event.message.message);

      /*var audio = new Audio("https://www.computerhope.com/jargon/m/example.mp3");
      audio.play();*/

      /*var myAudio = new Audio('http://3.20.147.34/cloudkitchen/public/uploads/H42VWCD-notification.mp3');
      myAudio.play();*/

      var audio = new Audio();
      audio.src='http://34.193.8.160/public/uploads/H42VWCD-notification.mp3';
      // when the sound has been loaded, execute your code
      audio.oncanplaythrough = (event) => {
        var playedPromise = audio.play();

        if (playedPromise) {
          playedPromise.catch((e) => {
             console.log(e)
             if (e.name === 'NotAllowedError' || e.name === 'NotSupportedError') { 
                   console.log(e.name);
              }
          }).then(() => {
              console.log("playing sound !!!");
          });
         }
      }

      $.ajax({
         url:"{{url('admin/notifications/getNotificationData')}}",
         dataType: 'html',
         success:function(result)
         {
          $('.realTimeNotificationDataUpdate').html(result);

          var current_url = "{{ (Route::current()->uri) }}";

          if (current_url == '/') {
            location.reload();
          }
         }
      });
      $('.notificaiton_count').text(event.message.adminCount);
      // pElement.appendChild(document.createTextNode(event.message.content));
      // document.body.appendChild(pElement);
    },
    presence: function(event) {
      // console.log(event,'presence');
      /*let pElement = document.createElement('p');
      pElement.appendChild(document.createTextNode(event.uuid + " has joined. That's you!"));
      document.body.appendChild(pElement);*/
    }
  });

  pubnub.history(
    {
      channel: channel_name,
      count: 10,
      stringifiedTimeToken: true,
    },
    function (status, response) {
      let pElement = document.createElement('h3');
      pElement.appendChild(document.createTextNode('historical messages'));
      document.body.appendChild(pElement);

      pElement = document.createElement('ul');
      let msgs = response.messages;
      for (let i in msgs) {
        msg = msgs[i];
        let pElement = document.createElement('li');
        pElement.appendChild(document.createTextNode('sender: ' + msg.entry.sender + ', content: ' + msg.entry.content));
        document.body.appendChild(pElement);
      }
    }
  );

</script>
<script type="text/javascript">
  function readNotification($this) {
    var notificationId = $($this).attr('data-id');
    var redirectUrl = $($this).attr('data-url');

    if (notificationId) {
      $.ajax({
          type: 'get',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! url('admin/notifications/readNotification' )!!}" + "/" + notificationId,
          success:function(res){
            if(res.status === 1){ 
              window.location.href = redirectUrl;

            } else {
              toastr.error(res.message);
            }
          },   
          error:function(jqXHR,textStatus,textStatus){
            console.log(jqXHR);
            toastr.error(jqXHR.statusText)
          }
      });
    }
  }

  function clearAllNotification() {
    var userId = "<?php echo $login_user_data->id ?>";
    $.ajax({
        type: 'get',
        data: {_method: 'get', _token: "{{ csrf_token() }}", userId: userId},
        dataType:'json',
        url: "{!! url('admin/notifications/clearAllNotification' )!!}",
        success:function(res){
          if(res.status === 1){ 
            window.location.reload();

          } else {
            toastr.error(res.message);
          }
        },   
        error:function(jqXHR,textStatus,textStatus){
          console.log(jqXHR);
          toastr.error(jqXHR.statusText)
        }
    });
  }
  $(document).ready(function() {

  });
</script>
  <!-- <script>
        $(document).ready(function() {
            $('.multiple-search').select2();
        })
        $(document).on('keyup mouseup', '#mobile', function() {
            var x = $(this).val().replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            $(this).val(!x[2] ? x[1] : x[1] + x[2] + (x[3] ? x[3] : ''));
        })
    </script> -->