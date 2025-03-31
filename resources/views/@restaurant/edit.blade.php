@extends('layouts.master')

@section('content')
<?php
  use App\Models\Language;
  use App\Models\RestaurantLang;
  $language = Language::pluck('lang')->toArray();
?>
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <div class="d-flex align-items-center">
        <a href="{{ url('restaurant') }}" class="btn btn-primary btn-sm mr-3" ><i class="fa fa-arrow-left"></i> Back </a>
        <h4 class="text-themecolor">{{ __('Edit Store') }}</h4>
      </div>
    </div>
    <!-- <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"> Edit Store </h4>
    </div> -->
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('backend.home') }}</a></li>
                <li class="breadcrumb-item active"> Edit Store </li>
            </ol>

        </div>
    </div>
</div>
<!-- <div class="row">
  <div class="col-md-6" style="margin-bottom: 10px;">
     <a href="{{ url('restaurant') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
  </div>
</div> -->

<div class="container-fluid" >

  <div class="content">
    <form method="PUT" action="{{ url('api/restaurant/'.$users->id) }}" id="edit_role">
      <div class="row">
        <div class="col-md-12">
         @csrf
          <!-- <ul class="nav nav-tabs">
            @foreach($language as $key => $lang)
              <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#tab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
            @endforeach
          </ul> -->
          <div class="card">
            <div class="card-body">
              <h5> Contact Details </h5>
              <div class="tab-content" style="margin-top:10px; padding: 15px">
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="brand_id">Service*</label>
                        <select name="main_category_id" id="main_category_id"  onchange="getBrands()" class="form-control multiple-search" data-parsley-required="true" >
                          <option value="">---Select Service----</option>
                          @foreach ($main_category as $cat)
                              <option value="{{ $cat->id }}" {{ $users->main_category_id == $cat->id?'selected':'' }}>{{ $cat->name }}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                  <div class="col-md-6 show_brandDiv">
                    <div class="form-group">
                      <label class="control-label" for="brand_id">Vendor*</label>
                      <select name="brand_id" class="form-control multiple-search" data-parsley-required="true" >
                        <option value="">---Select Vendor----</option>
                      </select>
                    </div>
                  </div>
                </div>
                @if($lang)
                  <div class="row">
                      @foreach($language as  $key => $lang)
                        <?php
                        if (isset($users)) {
                            $langData = RestaurantLang::where(['lang'=>$lang,'restaurant_id'=>$users->id])->first();
                        } ?>
                        <!-- <div id="tab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif"> -->
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label" for="restaurant_name">{{__('Store Name')}} ({{__('backend.'.$lang)}})*</label>
                                  <input type="text" name="restaurant_name[{{$lang}}]" data-parsley-required="true" id="restaurant_name" value="{{$langData->name}}" class="form-control" placeholder="Store Name"  />
                                </div>
                              </div>
                        <!-- </div> -->
                      @endforeach
                  </div>

                  <div class="row">
                    @foreach($language as  $key => $lang)
                      <?php
                      if (isset($users)) {
                          $langData = RestaurantLang::where(['lang'=>$lang,'restaurant_id'=>$users->id])->first();
                      } ?>
                      
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label" for="tag_line">{{__('backend.tag_line')}} ({{__('backend.'.$lang)}})*</label>
                                <input type="text" name="tag_line[{{$lang}}]" data-parsley-required="true" data-parsley-maxlength="80" id="tag_line" value="{{$langData->tag_line}}" class="form-control" placeholder="Tag Line"  />
                              </div>
                            </div>
                      
                    @endforeach
                  </div>
                @endif
              

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label" for="email">Email*</label>
                      <input type="email" name="email" id="email" class="form-control" value="{{$users['email']}}" placeholder="Email" />
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
                        <input type="text" name="mobile" value="{{$users->phone_number}}" id="mobile" class="form-control" placeholder="Mobile" autocomplete="off" data-parsley-required="true"  data-parsley-trigger="keyup" data-parsley-validation-threshold="1" data-parsley-debounce="500" data-parsley-type="digits" data-parsley-minlength="8" data-parsley-maxlength="15"/>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 hide">
                    <div class="form-group">
                      <label class="control-label" for="landline">Landline</label>
                      <input type="text" name="landline" value="{{$users->landline}}" id="landline" class="form-control" placeholder="Landline" autocomplete="off"/>
                    </div>
                  </div>
                  <div class="col-md-6 hide">
                    <div class="form-group">
                      <label class="control-label" for="password">Password*</label>
                      <input type="password" name="password" value="" id="password" class="form-control" autocomplete="off" placeholder="Password" />
                      <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleNewPassword"></i>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-6 hide">
                    <div class="form-group">
                      <label class="control-label" for="confirm_password">Confirm password*</label>
                      <input type="password" name="confirm_password" value="" id="confirm_password" autocomplete="off" class="form-control" placeholder="Confirm password" />
                      <i class="fa fa-eye" style="margin-left: -30px; cursor: pointer; position: absolute; top: 34px;" id="toggleConfirmPassword"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container-fluid" style="padding-top: 10px">
            <div class="card">
              <div class="card-body">
                <h5> Service Details </h5>
                <div class="row">
                       <!-- <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label" for="payment_type">Payment Type*</label>
                              <select class="form-control" data-parsley-required="true" name="payment_type">
                                <option value="">---Select---</option>
                                <option value="COD" {{ $users->payment_type == 'COD' ?'selected':'' }}>Cash On Delivery</option>
                                <option value="Online Payment" {{ $users->payment_type == 'Online Payment' ?'selected':'' }}>Online Payment</option>
                              </select>
                            </div>
                          </div> -->
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
                        <!-- <div class="col-md-6">
                          <div class="form-group">

                            <label class="control-label" for="free_delivery_min_amount">Free Delivery Min Amount *</label>
                            <input type="text" name="free_delivery_min_amount" value="{{$users->free_delivery_min_amount}}" id="free_delivery_min_amount" class="form-control" placeholder="Free delivery min amount" autocomplete="off" data-parsley-required="true"/>
                          </div>
                        </div> -->


                        <!-- <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="area_name">Area Name</label>
                            <input type="text" name="area_name" value="{{$users->area_name}}" id="area_name" class="form-control" placeholder="Area Name" autocomplete="off" data-parsley-required="true"  data-parsley-type ="area name"/>
                          </div>
                        </div> -->
                      
                        <div class="col-md-6 pricefor2">
                          <div class="form-group">
                            <label class="control-label" for="area_name">Price (For Two Person)</label>
                            <input type="text" name="cost_for_two_price" value="{{$users->cost_for_two_price}}" id="area_name" class="form-control" placeholder="Cost For Two Price" autocomplete="off" data-parsley-type="digits" />
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

                            <div class="row payment_div"   style="display:none">
                                        <div class="col-md-6"><input type="checkbox" name="payment_mode[]" value="pay_in_advance" @if(in_array('pay_in_advance',$modeTypeAssign)) checked @endif> Pay in advance</div>
                                        <div class="col-md-6"><input type="checkbox" name="payment_mode[]" value="pay_on_finish"  @if(in_array('pay_on_finish',$modeTypeAssign)) checked @endif> Pay on finish</div>
                                      </div>
                                      <div class="row pickup_div"  style="display:none">
                                        <div class="col-md-6"><input type="checkbox" name="pickup_mode[]" value="In_car" @if(in_array('In_car',$modeTypeAssign)) checked @endif> In Car</div>
                                        <div class="col-md-6"><input type="checkbox" name="pickup_mode[]" value="In_restaurant"  @if(in_array('In_restaurant',$modeTypeAssign)) checked @endif><span class="restaurant_label"> In Store</span></div>
                                      </div>
                                      <br/>
                        </div>
                        <!-- <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="mode_id">Mode *</label>
                            <select name="brand_id" class="form-control" data-parsley-required="true" >
                              <option value="">---Select Mode----</option>
                              @foreach ($Modes as $Modes)
                                  <option value="{{ $Modes->id }}" {{ $users->mode_id == $Modes->id?'selected':'' }}>{{ $Modes->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>  -->
                      
                        <div class="col-md-6">
                          <div class="form-group">

                            <label class="control-label" for="admin_comission">Admin Commission (In %)*</label>
                            <input type="text" name="admin_comission" value="{{$users->admin_comission}}" id="admin_comission" class="form-control" placeholder="Admin commission" autocomplete="off" data-parsley-required="true" data-parsley-type="digits" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="min_order_amount">Min Order Amount(QAR)</label>
                            <input type="text" name="min_order_amount" value="{{$users->min_order_amount}}" id="min_order_amount" class="form-control" placeholder="Min order amount" autocomplete="off" data-parsley-required="false" data-parsley-type="digits"/>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="video">Video</label>
                            <input type="text" name="video" id="video" class="form-control" value="{{$users->video}}" placeholder="Video Url" />
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
                            <label class="control-label" for="prepration_time">Prepration Time (In minuts) *</label>
                            <input type="text" name="prepration_time" value="{{$users->prepration_time}}" id="prepration_time" class="form-control" placeholder="Prepration time" autocomplete="off" data-parsley-required="true" data-parsley-type="digits" />
                          </div>
                        </div> -->
                        <!-- <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="delivery_time">Delivery Time (In minuts) *</label>
                            <input type="text" name="delivery_time" value="{{$users->delivery_time}}" id="delivery_time" class="form-control" placeholder="Delivery time" autocomplete="off" data-parsley-required="true" data-parsley-type="digits" />
                          </div>
                        </div> -->
                        <!-- <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="is_kilo_points_promotor">Is KiloPoints Promotor*</label>
                            <select class="form-control is_kilo_points_promotor" data-parsley-required="true" name="is_kilo_points_promotor">
                              <option value="">---Select---</option>
                              <option value="1" {{ $users->is_kilo_points_promotor=='1'?'selected':'' }}>Yes</option>
                              <option value="0" {{ $users->is_kilo_points_promotor=='0'?'selected':'' }}>No</option>
                            </select>
                          </div>
                        </div> -->
                        <!-- <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="is_kilo_points_promotor">Is Provide Extra Kilopoints*</label>
                            <select class="form-control" data-parsley-required="true" name="is_kilo_points_promotor">
                              <option value="">---Select---</option>
                              <option value="1" {{ $users->is_kilo_points_promotor == 1 ?'selected':'' }}>Yes</option>
                              <option value="0" {{ $users->is_kilo_points_promotor == 0 ?'selected':'' }}>No</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label" for="buy_one_get_one">Buy 1 Get 1*</label>
                            <select class="form-control" data-parsley-required="true" name="buy_one_get_one">
                              <option value="">---Select---</option>
                              <option value="1" {{ $users->buy_one_get_one == 1 ?'selected':'' }}>Yes</option>
                              <option value="0" {{ $users->buy_one_get_one == 0 ?'selected':'' }}>No</option>
                            </select>
                          </div>
                        </div> -->
                        <!-- <div class="col-md-6 {{$users->is_kilo_points_promotor == '1' ? 'show' : 'show'}}" id="extra_kilopoint">
                          <div class="form-group">
                            <label class="control-label" for="delivery_time">Extra KiloPoints</label>
                            <input type="text" name="extra_kilopoint" value="{{$users->extra_kilopoints}}" class="form-control" placeholder="Extra KiloPoints" autocomplete="off"  data-parsley-type="digits" />
                          </div>
                        </div> -->
                </div>
                <div class="row">
                  
                </div>
              </div>
            </div>
          </div>
          <div class="container-fluid" style="padding-top: 10px">
            <div class="card">
              <div class="card-body">
                <h5 class="mb-2"> Images </h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="image">Image</label>
                      <div class="form-group">
                        <div class="input-group">
                          <div id="image_preview"><img height="100" width="100" id="previewing" src="{{$users['file_path']}}"></div>
                          <input type="file" id="file" name="image" class="form-control">
                        </div>
                        <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                        <label for="logo">Logo</label>
                      <div class="form-group">
                        <div class="input-group">
                          <div id="image_preview2"><img height="100" width="100" id="previewing2" src="{{$users['logo']}}"></div>
                          <input type="file" id="file2" name="logo" class="form-control">
                        </div>
                        <span class="text-muted">Note: Image should be JPG, JPEG, PNG and Dimension of image is 500x500</span>
                      </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card card-primary card-outline">
            <div class="card-body">
              <div class="container-fluid" style="margin-top:10px">
                  <h5 class=""> Contract Document Details* </h5>
                  <div class="row">
                      <div class="col-md-6 mb-2">
                          <label  for="image">Upload Document*</label>
                          <div class="form-group">
                            <div class="input-group">
                              <!-- <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div> -->
                              <input type="file" id="file" name="document" onchange="documentChange(this)" class="form-control">
                            </div>
                            <span class="text-muted">Note: Document should be PDF, DOCX only.</span>
                          </div>
                      </div>

                      <div class="col-md-6">
                          <label  for="logo">Valid Upto*</label>
                          <div class="form-group">
                            <div class="input-group">
                              <?php

                                if ($users['restro_valid_upto']) {
                                  $restro_valid_upto = date('d-m-Y', strtotime($users['restro_valid_upto']));

                                } else {
                                  $restro_valid_upto = '';
                                }
                              ?>
                              <input type="text" id="restro_valid_upto" name="restro_valid_upto" value="{{ $restro_valid_upto }}" class="form-control datetimepicker" data-parsley-required="true">
                            </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="container-fluid" style="padding-top: 10px">
            <div class="card">
              <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="control-label" for="address">Address*</label>

                        <input type="text" placeholder="Address" name="address" value="{{$users['address']}}" class="form-control" id="address" autocomplete="off" data-parsley-required="true">
                        <input type="hidden" class="latitude" id='latitude' name="latitude" value="{{$users['latitude']}}" />
                        <input type="hidden" class="longitude" id='longitude' name="longitude" value="{{$users['longitude']}}" />

                      </div>
                    </div>
                    <div class="col-md-12">
                    <style>
                          #map_canvas {
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
                        <div id="map_canvas"></div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <hr style="margin: 1em -15px">
          <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
          <!-- <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button> -->
          <div class="save_btn">
            <button type="submit" class="btn btn-primary save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Save</button>
            <a href="{{ url('restaurant') }}" class="btn btn-default back"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> Back</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script type="text/javascript">
  $(function () {
      $('.datetimepicker').datetimepicker({
          // Formats
          // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
          format: 'DD-MM-YYYY',
          minDate:new Date(),

          // Your Icons
          // as Bootstrap 4 is not using Glyphicons anymore
          icons: {
              time: 'fa fa-clock-o',
              date: 'fa fa-calendar',
              up: 'fa fa-chevron-up',
              down: 'fa fa-chevron-down',
              previous: 'fa fa-chevron-left',
              next: 'fa fa-chevron-right',
              today: 'fa fa-check',
              clear: 'fa fa-trash',
              close: 'fa fa-times'
          }
      });
  });

  function documentChange($this){
    var fileObj = $this.files[0];
    var imageFileType = fileObj.type;
    console.log(imageFileType);
    var imageSize = fileObj.size;
  
    var match = ["application/pdf","application/vnd.openxmlformats-officedocument.wordprocessingml.document"];

    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
      toastr.error('Please select a valid document file <br> Note: Only .pdf and .docx file type allowed!!');
      return false;
    }
  };

  function getBrands() {
    var main_category_id = $('#main_category_id').val();
    var brand_id = "<?php echo $users['brand_id'] ?>";
    $.ajax({
       url:'{{url('restaurant/show_brands')}}/'+main_category_id+'/'+brand_id,
       dataType: 'html',
       success:function(result)
       {
          $('.show_brandDiv').html(result);

          if (main_category_id == '2') {
            $('.pricefor2').removeClass('hide');
            $('.restaurant_label').text(' In Store');

            $("#modes_id option[value='3']").remove();

            if ($("#modes_id option[value='1']").length < 1) {
              $("#modes_id").append('<option value="1">Dine In</option>');
            }

          } else {
            $('.pricefor2').addClass('hide');
            $('.restaurant_label').text(' In Store');

            $("#modes_id option[value='1']").remove();

            if ($("#modes_id option[value='3']").length < 1) {
              $("#modes_id").append('<option value="3">Delivery</option>');
            }
          }
       }
    });
  }
</script>

<script>

$(document).ready(function(){
  var selected=[];
  $('#modes_id option:selected').each(function(i){    
     selected[i]=$(this).val();
    });

  if ($.inArray('1', selected) != -1 && $.inArray('2', selected) != -1) {
      $('.payment_div').show();
      $('.pickup_div').show();

  } else {

    if(selected==1){
      $('.payment_div').show();
      $('.pickup_div').hide();

    } else if (selected==2) {
      $('.payment_div').hide();
      $('.pickup_div').show();

    }  else {
      $('.payment_div').hide();
      $('.pickup_div').show();
    }
  }

  $(document).on('change','#modes_id',function() {

    if ($.inArray('1', $(this).val()) != -1 && $.inArray('2', $(this).val()) != -1) {
        $('.payment_div').show();
        $('.pickup_div').show();

    } else {

      if ($(this).val()[0]==1) {
        $('.payment_div').show();
        $('.pickup_div').hide();

      } else if ($(this).val()[0]==2) {
        $('.payment_div').hide();
        $('.pickup_div').show();

      } else {
        $('.payment_div').hide();
        $('.pickup_div').hide();
      }
    }
  });
});

const toggleNewPassword = document.querySelector('#toggleNewPassword');
const newPassword = document.querySelector('#password');

const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
const confirm_password = document.querySelector('#confirm_password');

toggleNewPassword.addEventListener('click', function (e) {
  // toggle the type attribute
  const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
  newPassword.setAttribute('type', type);
  // toggle the eye / eye slash icon
  this.classList.toggle('fa-eye-slash');
});

toggleConfirmPassword.addEventListener('click', function (e) {
  // toggle the type attribute
  const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
  confirm_password.setAttribute('type', type);
  // toggle the eye / eye slash icon
  this.classList.toggle('fa-eye-slash');
});

$(document).ready(function(){
    initialize();
    autoload({{$users->latitude}}, {{$users->longitude}});
    $('.select2').select2();
    $('#edit_role').parsley();
    getBrands()

  $(document).on('submit', "#edit_role",function(e){
      e.preventDefault();
      var _this=$(this);
      var formData = new FormData(this);
      formData.append('_method', 'put');

      $('#group_loader').fadeIn();
      // var values = $('#edit_role').serialize();
      $.ajax({
        url:'{{ url('api/restaurant/'.$users->id) }}',
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

            if (result.status) {
              window.location.href = "{{url('restaurant')}}";
              toastr.success(result.message)

            } else {
              toastr.error(result.message)
              $('.save').prop('disabled',false);
              $('.formloader').css("display","none");
            }
              setTimeout(function(){$('#disappear_add').fadeOut('slow')},3000)
              $('#edit_role').parsley().reset();
              // ajax_datatable.draw();
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
<!-- <script>
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

</script> -->

<script>
$(document).ready(function(){

});


    $("#file").change(function(){
      var fileObj = this.files[0];
      var imageFileType = fileObj.type;
      var imageSize = fileObj.size;

      var match = ["image/jpeg","image/png","image/jpg"];
      if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
        $('#previewing').attr('src','images/no-image-available.png');
        toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
        return false;
      }else{
        //console.log(imageSize);
        if(imageSize < 5000000){
          var reader = new FileReader();
          reader.onload = imageIsLoaded;
          reader.readAsDataURL(this.files[0]);
        }else{
          toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
          return false;
        }
      }
  });

  $("#file2").change(function(){
      var fileObj = this.files[0];
      var imageFileType = fileObj.type;
      var imageSize = fileObj.size;

      var match = ["image/jpeg","image/png","image/jpg"];
      if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
        $('#previewing2').attr('src','images/no-image-available.png');
        toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
        return false;
      }else{
        //console.log(imageSize);
        if(imageSize < 5000000){
          var reader = new FileReader();
          reader.onload = imageIsLoaded2;
          reader.readAsDataURL(this.files[0]);
        }else{
          toastr.error('Images Size Too large Please Select Less Than 5MB File!!');
          return false;
        }
      }
  });

  $(document).on('change','.is_kilo_points_promotor',function(e){
    e.preventDefault();
    //$('#music_category_type').hide();
    if($(this).val()=='0'){
      $('#extra_kilopoint').hide();
    } else {
      $('#extra_kilopoint').show();
    }
  });

	function imageIsLoaded(e){
	  //console.log(e);
	  $("#file").css("color","green");
	  $('#previewing').attr('src',e.target.result);
	}

	function imageIsLoaded2(e){
	  //console.log(e);
	  $("#file2").css("color","green");
	  $('#previewing2').attr('src',e.target.result);
	}
</script>
@endsection