@extends('layouts.master')
@section('content')
<?php /*echo "<pre>"; print_r($orders); die;*/ ?>

  <div class="container-fluid" style="padding-top: 7px;">
                  <!-- Content Header (Page header) -->
                  <div class="content order-page">
                     <div class="row">
                        <div class="col-md-6" style="margin-bottom: 10px;">
                           @if($orders->order_status == 'Pending' || $orders->order_status == 'Accepted' || $orders->order_status == 'Prepare')
                           <a href="{{ url('orders/Pending') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
                           @else
                           <a href="{{ url('orders/Complete') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
                           @endif
                        </div>
                        <!-- <div class="col-md-6">
                           <a href="{{ url('orders/pdf/'.$orders->id) }}" class="btn btn-primary btn-sm" ><i class="fa fa-pdf"></i> PDF </a>
                        </div> -->
                     </div>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="card card-primary card-outline">
                              <div class="card-body">
                                 
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="head_sss">

                                          <h4>Order ID- &nbsp;  ORD#{{$orders->random_order_id ? $orders->random_order_id : $orders->id }}</h4>
                                          <div class="head_sss_in">
                                          <p><span class="title">Order Date: </span> <span class="value">{{$orders->created_at}} </span></p>
                                          <p><span class="title">Order Status: </span><span class="value">{{$orders->order_status}}</span></p>

                                          <?php
                                          	$orderType = '';

                                          	if ($orders->dine_in == 'Pickup') {

                                          		if ($orders->pickup_option == 'Inside-The-Car') {
									                $orderType .= $orders->dine_in.' (Inside: Car) <br/>'.$orders->car_detail_color;

									            } else {

									            	if ($orders->pickup_option == 'Inside-Restro') {
									                	$orderType .= $orders->dine_in.' (Inside: Restaurant)';

									                } else {
									                	$orderType .= $orders->dine_in.'->'.$orders->pickup_option;
									                }
									            }

                                          	} else {
                                          		$orderType = $orders->dine_in;
                                          	}
                                          ?>

                                          <p><span class="title">Order Type: </span><span class="value">{!!$orderType!!}</span></p>

                                          @if ($orders->reasion)
                                          	<p><span class="title">Cancel Reasion: </span> <span class="value">{{$orders->reasion}}</span></p>
										  @endif
                                       </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="card card-primary card-outline" style="margin-top:10px">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <h4>Account Information</h4>
									   <div class="table-responsive">
										   <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
											  <tbody>
												 <tr>
													<th> Customer Name</th>
													<td> {{$orders->user_name}} </td>
												 </tr>
												 <tr>
													<th> Email</th>
													<td>{{$orders->user_email}} </td>
												 </tr>
												 <tr>
													<th> Phone</th>
													<td>  {{$orders->country_code}}-  {{$orders->user_mobile}} </td>
												 </tr>
												<!--  <tr>
													<th>Shipping Address </th>
													<td  style="white-space: pre-wrap;"> {!! $orders->user_address !!}
													</td>
												 </tr> -->
											  </tbody>
										   </table>
										</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="card card-primary card-outline" style="margin-top:10px">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <h4>Payment Information</h4>
									   <div class="table-responsive">
										   <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
											  <tbody>
												 <tr>
													<th> Txn id </th>
													@if(isset($orders->transaction_id))
													   <td> {{$orders->transaction_id}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr>
												 <tr>
													<th>Payment Type</th>
													@if(isset($orders->payment_type))
													   <td>{{$orders->payment_type ?? 'Case on delevery'}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr>
												 <tr>
													<th>Discount Code</th>
													@if(isset($orders->discount_code))
													   <td>{{$orders->discount_code}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr>
												 <!-- <tr>
													<th>Shipping Price </th>
													@if(isset($orders->orders_shipping_charges))
													   <td>QAR {{$orders->orders_shipping_charges}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr> -->
											  </tbody>
										   </table>
										</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>


                     <div class="row">
                        <div class="col-md-12">
                           <div class="card card-primary card-outline" style="margin-top:10px">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <h4>Products Ordered</h4>
                                    </div>
                                    <div class="col-md-12">
										<div class="table-responsive">
										   <table class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
											  <thead>
												 <tr>
													<th> Order Number </th>
													<th>Product Name</th>
													<th>Product Attributes</th>
													<th>SKU Code</th>
													<th> Product Type </th>
													<th>Item Qty</th>
													<th>KP</th>
													<th>Subtotal</th>
												 </tr>
											  </thead>
											  <tbody>

										@foreach($orders_details as $orders_detail)
										<div class="mt-4">
												 <tr>
													<td>
													   ORD#{{$orders->random_order_id ? $orders->random_order_id : $orders->id }}
													</td>
													<td>
													   {{$orders_detail->name}}
													</td>
													<td>
													   {{$orders_detail->product_attrs ? $orders_detail->product_attrs : 'Not Added'}}
													</td>
													<td>
													   {{$orders_detail->sku_code}}
													</td>

													 <!-- <td>
													<img src="{{$orders_detail->main_image}}" alt="Girl in a jacket" width="100" height="100">
													</td> -->


													<td> {{$orders_detail->products_type}}</td>
													<td>
													   <span class="qty-row">
													   {{$orders_detail->qty}}
													   </span>
													</td>
													<td>
													   {{$orders_detail->order_product_kp}}
													</td>
													<td> QAR {{ number_format($orders_detail->amount,2) }} </td>
												 </tr>

												 </div>
										  @endforeach
												 <!-- <tr>
													<td class="bold" colspan='4' style="text-align:right">Shipping Cost</td>
													@if(isset($orders->orders_shipping_charges))
													   <td>QAR {{$orders->orders_shipping_charges}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr> -->
												 <tr>
													<td class="bold" colspan='7' style="text-align:right">Discount Amount</td>
													@if(isset($orders->discount_amount))
													   <td>QAR {{$orders->discount_amount}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr>
												 <tr>
													<td class="bold" colspan='7' style="text-align:right">Tax Amount</td>
													@if(isset($orders->tax_amount))
													   <td>QAR {{$orders->tax_amount}}</td>
													@else
													   <td>Not Available</td>
													@endif
												 </tr>
												 <tr>
													<td class="bold" colspan='7' style="text-align:right">Grand Total</td>
													@php
													$a = $orders->orders_shipping_charges;
													$b = $orders->amount;
													$c = $orders->tax_amount;
													$total = $a+$b+$c;

													  @endphp
													<td>QAR {{number_format($total,2)}} </td>
												 </tr>
										   </table>
										</div>
									</div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
@endsection