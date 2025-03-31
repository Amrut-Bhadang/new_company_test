@extends('layouts.master')

@section('content')
<?php
  use App\Models\Language;
  use App\Models\RestaurantLang;
  $language = Language::pluck('lang')->toArray();

?>
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- Content Header (Page header) -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Settings') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Settings') }}</li>
                
            </ol>
        </div>
    </div>
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content">
<div class="row">
<!-- Column -->
<div class="col-lg-4 col-xlg-3 col-md-5">
    <div class="card">
        <div class="card-body">
            <center class="m-t-30">       
                <img src="{{ Auth::user()->image }}" class="img-circle" width="150">
                <h4 class="card-title m-t-10">{{ucwords(Auth::user()->name)}}</h4>
            </center>
        </div>
        <div>
            <hr> 
        </div>
        <div class="card-body"> <small class="text-muted">Email address </small>
            <h6>{{Auth::user()->email}}</h6> <small class="text-muted p-t-30 db">Phone</small>
            <h6>{{Auth::user()->mobile}}</h6> 
            
        </div>
    </div>
</div>
<!-- Column -->
<!-- Column -->
<div class="col-lg-8 col-xlg-9 col-md-7">
    <div class="card">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs profile-tab" role="tablist">
            <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#settings" role="tab" aria-selected="true">Settings</a> </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="settings" role="tabpanel">
              <form method="POST" action="{{ url('updateProfile') }}" id="edit_restro_profile">
                @csrf
                 <div class="card-body">
                  <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label" for="brand_id">Vendor*</label>
                      <select name="brand_id" class="form-control" data-parsley-required="true" disabled="true" >
                        <option value="">---Select Vendor----</option>
                        @foreach ($Brand as $Brand)
                            <option value="{{ $Brand->id }}" {{ $users->brand_id == $Brand->id?'selected':'' }}>{{ $Brand->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                    
                  </div>
                  <!-- <ul class="nav nav-tabs">
                    @foreach($language as $key => $lang)
                      <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#tab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
                    @endforeach
                  </ul> -->
                    <div class="tab-content" style="margin-top:10px">
                      @if($lang)
                        @foreach($language as  $key => $lang)
                          <?php
                          if (isset($users)) {
                              $langData = RestaurantLang::where(['lang'=>$lang,'restaurant_id'=>$users->id])->first();  
                          } ?>
                          <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
                            <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="control-label" for="restaurant_name">{{__('backend.restaurant_name')}} ({{__('backend.'.$lang)}})*</label>
                                    <input type="text" name="restaurant_name[{{$lang}}]" id="restaurant_name" value="{{$langData->name}}" class="form-control" placeholder="Restaurant Name"  />
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="control-label" for="tag_line">{{__('backend.tag_line')}} ({{__('backend.'.$lang)}})*</label>
                                    <input type="text" name="tag_line[{{$lang}}]" id="tag_line" value="{{$langData->tag_line}}" class="form-control" placeholder="Tag Line"  />
                                  </div>
                                </div>
                            </div>
                          <!-- </div> -->
                        @endforeach
                      @endif
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                       
                          <label class="control-label" for="email">Email*</label>
                          <input type="text" name="email" id="email" class="form-control" value="{{$users->email}}" placeholder="Email" autocomplete="off" data-parsley-required="true"  data-parsley-type ="email"/>
                        </div>
                      </div> 
                      <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label" for="mobile">Mobile*</label>
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <select name="country_code" class="form-control" style="width:180px" data-parsley-required="true" >
                                @foreach ($country as $country)
                                    <option value="{{ $country->phonecode }}"  {{ $users->country_code== $country->phonecode?'selected':'' }}>{{ $country->name }} ({{ $country->phonecode }})</option>
                                @endforeach
                              </select>
                            </div>
                            <input type="text" name="mobile" value="{{$users->phone_number}}" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="10" data-parsley-maxlength="10"/>
                          </div> 
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                       
                          <label class="control-label" for="landline">Landline*</label>
                          <input type="text" name="landline" value="{{$users->landline}}" id="landline" class="form-control" placeholder="Landline" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div> 
                      <!-- <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="password">Password*</label>
                          <input type="password" name="password" value="" id="password" class="form-control" autocomplete="off" placeholder="Password"  />
                        </div>
                      </div> -->
                      
                    </div>
                    <!-- <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="confirm_password">Confirm password*</label>
                          <input type="password" name="confirm_password" value="" id="confirm_password" autocomplete="off" class="form-control" placeholder="Confirm password"   />
                        </div>
                      </div>
                    </div> -->

                    <h5 class="mt-3"> Service Details </h5>
                    <div class="container-fluid" style="background-color:#e4e4e4; padding: 15px">
                    <br>
                    <!-- <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                       
                          <label class="control-label" for="prepration_time">Prepration Time (In minuts) *</label>
                          <input type="text" name="prepration_time" value="{{$users->prepration_time}}" id="prepration_time" class="form-control" placeholder="Prepration time" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div> 
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="delivery_time">Delivery Time (In minuts) *</label>
                          <input type="text" name="delivery_time" value="{{$users->delivery_time}}" id="delivery_time" class="form-control" placeholder="Delivery time" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div>
                    </div> -->

                    <div class="row">
                       
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="payment_type">Payment Type*</label>
                            <select class="form-control" data-parsley-required="true" name="payment_type">
                              <option value="">---Select---</option>
                              <option value="COD" {{ $users->payment_type == 'COD' ?'selected':'' }}>Cash On Delivery</option>
                              <option value="Online Payment" {{ $users->payment_type == 'Online Payment' ?'selected':'' }}>Online Payment</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="is_featured">Is Featured*</label>
                          <select class="form-control" data-parsley-required="true" name="is_featured">
                            <option value="">---Select---</option>
                            <option value="1" {{ $users->is_featured == 1 ?'selected':'' }}>Yes</option>
                            <option value="0" {{ $users->is_featured == 0 ?'selected':'' }}>No</option>
                          </select>
                        </div>
                      </div>
                      
                      <!-- <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="cancelation_charges">Cancelation Charges *</label>
                          <input type="text" name="cancelation_charges" value="{{$users->cancelation_charges}}" id="cancelation_charges" class="form-control" placeholder="Cancelation Charges" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div> -->
                    </div>

                    
                    <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="area_name">Price (For Two Person)*</label>
                        <input type="text" name="cost_for_two_price" value="{{$users->cost_for_two_price}}" id="cost_for_two_price" class="form-control" placeholder="Price" autocomplete="off" data-parsley-required="true"  />
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                      <label class="control-label" for="modes_id">Modes*</label>
                      <select name="modes_id[]" id="modes_id" class="select2 chefPrice" multiple="multiple" data-placeholder="Select Modes" data-dropdown-css-class="select2-primary" style="width: 100%;">
                          @foreach ($Modes as $mode)
                              <option value="{{ $mode->id }}" {{ (in_array($mode->id,$modeAssign))?'selected':'' }}>{{ $mode->name }}</option>
                          @endforeach
                      </select>
                      </div>
                  </div>
                </div>
                <div class="row">
                      <!-- <div class="col-md-6">
                        <div class="form-group">
                       
                          <label class="control-label" for="free_delivery_min_amount">Free Delivery Min Amount *</label>
                          <input type="text" name="free_delivery_min_amount" value="{{$users->free_delivery_min_amount}}" id="free_delivery_min_amount" class="form-control" placeholder="Free delivery min amount" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div> --> 
                      <div class="col-md-6">
                        <div class="form-group">
                       
                          <label class="control-label" for="admin_comission">Admin Comission(%)*</label>
                          <input type="text" name="admin_comission" value="{{$users->admin_comission}}" id="admin_comission" class="form-control" placeholder="Admin comission" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="min_order_amount">Min Order Amount(QAR)*</label>
                          <input type="text" name="min_order_amount" value="{{$users->min_order_amount}}" id="min_order_amount" class="form-control" placeholder="Min order amount" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div>
                      <div class="col-md-6" id="kp_percent">
                        <div class="form-group">
                          <label class="control-label" for="kp_percent">KiloPoint(%)*</label>
                          <input type="text" name="kp_percent" value="{{$users->kp_percent}}" class="form-control" min="1" max="100" data-parsley-required="true" data-parsley-type="digits" placeholder="KiloPoint Percentage" autocomplete="off" />
                        </div>
                      </div>
                      <!-- <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="delivery_charges_per_km">Delivery Charges Per KM *</label>
                          <input type="text" name="delivery_charges_per_km" value="{{$users->delivery_charges_per_km}}" id="delivery_charges_per_km" class="form-control" placeholder="Delivery charges per km" autocomplete="off" data-parsley-required="true"/>
                        </div>
                      </div> -->
                    </div>
                    <div class="row">
                      

                      <!-- <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="area_name">Area Name</label>
                          <input type="text" name="area_name" value="{{$users->area_name}}" id="area_name" class="form-control" placeholder="Area Name" autocomplete="off" data-parsley-required="true"  data-parsley-type ="area name"/>
                        </div>
                      </div> -->
                    </div>
                    

                    <!-- <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label" for="is_kilo_points_promotor">Kilo points promotor*</label>
                          <select class="form-control is_kilo_points_promotor" data-parsley-required="true" name="is_kilo_points_promotor">
                            <option value="">---Select---</option>
                            <option value="1" {{ $users->is_kilo_points_promotor == 1 ?'selected':'' }}>Yes</option>
                            <option value="0" {{ $users->is_kilo_points_promotor == 0 ?'selected':'' }}>No</option>
                          </select>
                        </div>
                      </div> 
                      <div class="col-md-6 {{$users->is_kilo_points_promotor == '1' ? 'show' : 'hide'}}" id="extra_kilopoint">
                        <div class="form-group">
                          <label class="control-label" for="delivery_time">Extra KiloPoints*</label>
                          <input type="text" name="extra_kilopoint" value="{{$users->extra_kilopoints}}" class="form-control" placeholder="Extra KiloPoints" autocomplete="off" data-parsley-required="true" data-parsley-type="digits" />
                        </div>
                      </div>
                      
                    </div> -->
                    </div>

                    <h5 class="mb-2 mt-4">Images</h5>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-md-12" for="image">Image</label>
                          <input type="file" id="edit_file" name="image" onchange="imageChange(this)" class="form-control">
                          <?php
                          if (isset($users->file_path)) {
                              $imageURL = $users->file_path;  
                          } else {
                              $imageURL = URL::asset('images/image.png');
                          } ?>
                          <div id="image_preview"><img height="100" width="100" id="edit_previewing" src="{{ $imageURL }}"></div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-md-12" for="logo">Logo</label>
                          <?php
                          if (isset($users->logo)) {
                              $imageURL2 = $users->logo;  
                          } else {
                              $imageURL2 = URL::asset('images/image.png');
                          } ?>
                          <input type="file" id="edit_file2" name="logo" onchange="imageChange1(this)" class="form-control">
                          <div id="image_preview2"><img height="100" width="100" id="edit_previewing2" src="{{ $imageURL2 }}"></div>
                        </div>
                      </div>
                    </div>
                    
                    <h5 class="mt-3">Address Details</h5>
                    <div class="container-fluid" style="background-color:#e4e4e4; padding: 15px;">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="control-label" for="address">Address</label>
                          <input type="text" name="address" id="editaddress" value="{{$users->address}}" class="form-control" placeholder="Address" />
                          <input type="hidden" class="latitude" id='editlatitude' name="latitude" value="{{$users->latitude}}"/>
                          <input type="hidden" class="longitude" id='editlongitude' name="longitude" value="{{$users->longitude}}"/>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <style>
                         #map_canvas_setting {
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
                          <div id="map_canvas_setting"></div> 
                        </div>
                      </div>
                    </div>
                    </div>
                <hr style="margin: 1em -15px">
                <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
                        style="display: none;" role="status" aria-hidden="true"></span> Save</button>

              </form>
            </div>
        </div>
    </div>
</div>
<!-- Column -->
</div>
</div>

<script src="{{ asset('js/parsley.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyDVBREanDQF283-XQcI-vWGu3FCUVaz9C8"></script> -->
<script>
$(document).ready(function(){
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
          console.log(autocomplete.getPlace());
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
      map = new google.maps.Map(document.getElementById('map_canvas_setting'), mapOptions);
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
          $('#editaddress').val(marker.formatted_address);
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
});

</script>
<script>

  
  $(document).ready(function(){
    $('.select2').select2();
    $('#edit_restro_profile').parsley();

    $(document).on('submit', "#edit_restro_profile",function(e){
      e.preventDefault();
      var _this=$(this); 
      var formData = new FormData(this);
      formData.append('_method', 'post');

        $('#group_loader').fadeIn();
        // var values = $('#edit_role').serialize();
        $.ajax({
        url:'{{ url('updateProfile') }}',
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
          if(result.status){
            toastr.success(result.message)
            $('#edit_restro_profile')[0].reset();
            $('#edit_restro_profile').parsley().reset();
            window.location.reload();
          }else{
            toastr.error(result.message)
          }
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

        /*function imageIsLoaded(e){
          console.log('helo');
        $("#edit_file").css("color","green");
        $('#edit_previewing').attr('src',e.target.result);
        }

        function imageIsLoaded2(e){
          //console.log(e);
          $("#edit_file2").css("color","green");
          $('#edit_previewing2').attr('src',e.target.result);
        }*/
        $(document).on('change','.is_kilo_points_promotor',function(e){
          e.preventDefault();
          //$('#music_category_type').hide();
          if($(this).val()=='0'){
            $('#extra_kilopoint').hide();
          } else {
            $('#extra_kilopoint').show();
          }
        });
        
    });

        function imageIsLoaded(e){
          //console.log(helo);
        $("#edit_file").css("color","green");
        $('#edit_previewing').attr('src',e.target.result);
        }

        function imageIsLoaded2(e){
          //console.log(e);
          $("#edit_file2").css("color","green");
          $('#edit_previewing2').attr('src',e.target.result);
        }
        function imageChange($this){
          var fileObj = $this.files[0];
          var imageFileType = fileObj.type;
          var imageSize = fileObj.size;
        
          var match = ["image/jpeg","image/png","image/jpg"];
          if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
            $('#edit_previewing').attr('src','images/image.png');
            toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
            return false;
          }else{
            //console.log(imageSize);
            if(imageSize < 1000000){
              var reader = new FileReader();
              reader.onload = imageIsLoaded;
              reader.readAsDataURL($this.files[0]);
            }else{
              toastr.error('Images Size Too large Please Select 1MB File!!');
              return false;
            } 
          }    
        }

        function imageChange1($this){
            var fileObj = $this.files[0];
            var imageFileType = fileObj.type;
            var imageSize = fileObj.size;
          
            var match = ["image/jpeg","image/png","image/jpg"];
            if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
              $('#edit_previewing2').attr('src','images/image.png');
              toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
              return false;
            }else{
              //console.log(imageSize);
              if(imageSize < 1000000){
                var reader = new FileReader();
                reader.onload = imageIsLoaded2;
                reader.readAsDataURL($this.files[0]);
              }else{
                toastr.error('Images Size Too large Please Select 1MB File!!');
                return false;
              } 
            }    
        }
  

</script>

@endsection
