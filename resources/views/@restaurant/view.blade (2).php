<?php /*dd($users);*/ ?>
@if($users)
<div class="row">
 <div class="col-md-12">
    <center>
        <?php $imageUrl = (!empty($users->file_path)) ? $users->file_path : 'image.png'; ?>
        <img src="{{$users->file_path}}" alt="user" class="img-circle"  width="100" height="100">
      
    </center>
	<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
            <tr>
                <td><strong>Vendor:</strong></td>
                @if(isset($users->brand_name))
                    <td>{{$users->brand_name}}</td>
                @else 
                    <td>No Name</td>
                @endif        
            </tr>
            <tr>
                <td><strong>Store Name:</strong></td>
                @if(isset($users->name))
                    <td>{{$users->name}}</td>
                @else 
                    <td>No Name</td>
                @endif        
            </tr>
            <tr>
                <td><strong>Tag Line:</strong></td>
                @if(isset($users->tag_line))
                    <td>{{$users->tag_line}}</td>
                @else 
                    <td>No Tag Line Available</td> 
                @endif       
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                @if(isset($users->email))
                    <td>{{$users->email}}</td>
                @else 
                    <td>No Email Available</td>
                @endif
            </tr>
            <tr>
                <td><strong>Mobile:</strong></td>
                @if(isset($users->phone_number))
                    <td>{{$users->country_code}}{{$users->phone_number}}</td>
                @else
                    <td>No Mobile Number Available</td> 
                @endif       
            </tr>
            <!-- <tr>
                <td><strong>Landline:</strong></td>
                @if(isset($users->landline))
                    <td>{{$users->landline}}</td>
                @else 
                    <td>Landline number is not available</td>  
                @endif      
            </tr> -->
            <tr>
                <td><strong>Address:</strong></td>
                @if(isset($users->address))
                    <td>{{$users->address}}</td>
                @else
                    <td>No Address</td>
                @endif        
            </tr>
            <tr>
                <td><strong>Valid Upto:</strong></td>
                <td><?php if ($users->restro_valid_upto) { echo date('j F, Y', strtotime($users->restro_valid_upto)); } else { echo 'No Date Available'; } ?> </td>
            </tr>
            <tr>
                <td><strong>Document:</strong></td>
                <td><a href="{{$users->document}}">View Document</a></td>
            </tr>
            <!-- <tr>
                <td><strong>Area Name:</strong></td>
                <td>{{$users->area_name}}</td>
            </tr> -->
            <!-- <tr>
                <td><strong>Dine In Code:</strong></td>
                <td>{{$users->dine_in_code}}</td>
            </tr> -->
            <!-- <tr>
                <td><strong>Kilo Points Promotor:</strong></td>
                <td>
                    @if($users->is_kilo_points_promotor  === 1)
                        Yes
                    @else
                        No 
                    @endif
                </td>
            </tr> -->
            <tr>
                <td><strong>Featured:</strong></td>
                <td>
                    @if($users->is_featured  === 1)
                        Yes
                    @else
                        No 
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Modes:</strong></td>
                @if(isset($users->mode_name))
                    <td>{{$users->mode_name}}</td>
                @else 
                    <td>No Name</td>
                @endif        
            </tr>
            <!-- <tr>
                <td><strong>Buy One Get One:</strong></td>
                <td>
                    @if($users->buy_one_get_one  === 1)
                        Yes
                    @else
                        No 
                    @endif
                </td>
            </tr> -->
            <!-- <tr>
                <td><strong>Prepration Time:</strong></td>
                <td>{{$users->prepration_time}}</td>
            </tr>
            <tr>
                <td><strong>Delivery Time:</strong></td>
                <td>{{$users->delivery_time}}</td>
            </tr> -->
            @if($users->main_category_id == 2)
                <tr>
                    <td><strong>Min Order Amount:</strong></td>
                    <td>QAR {{$users->min_order_amount}}</td>
                </tr>
                <tr>
                    <td><strong>Price (For Two Person):</strong></td>
                    @if(isset($users->cost_for_two_price))
                        <td>QAR {{$users->cost_for_two_price}}</td>
                    @else 
                        <td>Price Not Available</td>
                    @endif        
                </tr>
            @endif
            <tr>
                <td><strong>Admin Comission:</strong></td>
                @if(isset($users->admin_comission))
                    <td>{{$users->admin_comission}}%</td>
                @else 
                    <td>Admin Commission Not Available</td> 
                @endif       
            </tr>
            <tr>
                <td><strong>Video:</strong></td>
                @if(isset($users->video))
                    <td>{{$users->video}}%</td>
                @else 
                    <td>Video Not Available</td> 
                @endif       
            </tr>
            <tr>
                <td><strong>KiloPoint(%):</strong></td>
                @if(isset($users->kp_percent))
                    <td>{{$users->kp_percent}}%</td>
                @else 
                    <td>KiloPoint(%) Not Available</td> 
                @endif       
            </tr>
            <!-- <tr>
                <td><strong>Cancelation Charges:</strong></td>
                <td>{{$users->cancelation_charges}}</td>
            </tr> -->
            <!-- <tr>
                <td><strong>Payment Type:</strong></td>
                @if(isset($users->payment_type))
                    <td>{{$users->payment_type}}</td>
                @else
                    <td>Payment Type not available</td> 
                @endif       
            </tr> -->
            <!-- <tr>
                <td><strong>Free Delivery Min Amount:</strong></td>
                <td>{{$users->free_delivery_min_amount}}</td>
            </tr> -->
            <!-- <tr>
                <td><strong>Delivery Charges Per KM:</strong></td>
                <td>{{$users->delivery_charges_per_km}}</td>
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
            <!-- <?php /*
                $PNG_WEB_DIR = public_path('uploads/qrcode/temp/');
                //dd($PNG_WEB_DIR);
                $timestamp = time();
                include "public/uploads/qrcode/qrlib.php";
                $filename = $PNG_WEB_DIR.$timestamp.'-code.png';
                $newfilename = url('uploads/qrcode/temp').'/'.$timestamp.'-code.png';
                $code = 'DINEIN-'.$users->id;
                $data = QRcode::png($code, $filename, 'L', 4, 2);
                //dd($data);*/
            ?>
            <tr>
                <td><strong>QR Code:</strong></td>
                <td><img src="{{$newfilename ?? ''}}" height="100px" width="100px" /> </td>
            </tr> -->
    </table>
  </div>      
  </div>      
</div>
@endif