<script type="text/javascript" src="{{ asset('web/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('web/js/owl.carousel.js')}}"></script>
<script src="{{ asset('web/js/popper.min.js')}}"></script>
<script src="{{ asset('web/js/bootstrap.js')}}"></script>
<script src="{{ asset('web/js/custom.js')}}"></script>
<script src="{{ asset('web/js/range-slider.js')}}"></script>
<script src="{{ asset('web/js/jquery.matchHeight-min.js')}}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4"></script>
<script src="{{ URL::asset('dist/js/pubnub.4.21.7.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.14/moment-timezone-with-data-2012-2022.min.js"></script>


<script>
	$(function() {
		$('.datepicker').daterangepicker({
			opens: 'left'
		}, function(start, end, label) {
			console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
		});
	});
</script>
<script>
	// var x = document.getElementById("demo");
	$(document).ready(function() {
		getLocation();
	});

	function getLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		} else {
			x.innerHTML = "Geolocation is not supported by this browser.";
		}
	}

	function showPosition(position) {
		//   $('#latitude').val(position.coords.latitude);
		//   $('#longitude').val(position.coords.longitude);
		getHomeDataByLocation(position.coords.latitude, position.coords.longitude);
		// x.innerHTML = "Latitude: " + position.coords.latitude + 
		// "<br>Longitude: " + position.coords.longitude;
	}

	async function getHomeDataByLocation(lat = null, long = null) {
		var latitude = lat;
		var longitude = long;
		await setCookie('lat', lat, 1);
		await setCookie('long', long, 1);
	}

	function setCookie(cname, cvalue, exdays) {
		console.log(cname, cvalue);
		// document.cookie = cname + "=''";
		const d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		let expires = "expires=" + d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
		let name = cname + "=";
		let decodedCookie = decodeURIComponent(document.cookie);
		let ca = decodedCookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
</script>
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

		autocomplete = new google.maps.places.Autocomplete((document.getElementById('address')), {
			types: []
		});

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			// place variable will have all the information you are looking for.
			$('#latitude').val(place.geometry['location'].lat());
			$('#longitude').val(place.geometry['location'].lng());
			codeAddress();
		});
	}

	function autoload(latitude, longitude) {
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
			position: {
				lat: latitude,
				lng: longitude
			}
		});
		marker.addListener('click', toggleBounce);
	}

	function toggleBounce() {
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
<script>
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.main-header').addClass('newClass');
		} else {
			$('.main-header').removeClass('newClass');
		}
	});
</script>
<?php

use Illuminate\Support\Facades\Session;

    $login_user_data = Session::get('AuthUserData');
    $userType = '';
    $userId = '';
    if (isset($login_user_data->data)) {
    //   $userType = $login_user_data->data->type;
	  $userId = 'player_'.$login_user_data->data->id;
	// dd($login_user_data->data);
    //   if ($userType == 3) {
    //     $userId = 'player_'.$login_user_data->id;

    //   } else if ($userType == 0) {
    //     $userId = 'admin_'.$login_user_data->id;

    //   } else if ($userType == 1) {
    //     $userId = 'owner_'.$login_user_data->id;

    //   } else {
    //     $userId = $login_user_data->id;
    //   }
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
//   alert(channel_name);

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
         url:"{{url('notifications/getNotificationData/player')}}",
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

  function readNotification($this) {
    var notificationId = $($this).attr('data-id');
    var redirectUrl = $($this).attr('data-url');
    if (notificationId) {
      $.ajax({
          type: 'get',
          data: {_method: 'get', _token: "{{ csrf_token() }}"},
          dataType:'json',
          url: "{!! route('web.readNotification','' )!!}" + "/" + notificationId,
          success:function(res){
            if(res.status === true){ 
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
    var userId = "";
    $.ajax({
        type: 'get',
        data: {_method: 'get', _token: "{{ csrf_token() }}", userId: userId},
        dataType:'json',
        url: "{!! route('web.clearAllNotification' )!!}",
        success:function(res){
          if(res.status === true){ 
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
//   var offset = new Date().getTimezoneOffset();
	var zone = moment.tz.guess();
	// document.cookie = "timezone" + "=" + zone ;
	setCookie('timezone', zone, 1);


</script>