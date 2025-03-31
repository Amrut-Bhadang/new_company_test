@extends('layouts.master')
@section('content')
<?php /*echo "<pre>"; print_r($GiftOrder->toArray());die;*/ ?>
  <div class="container-fluid" style="padding-top: 10px;">
                  <!-- Content Header (Page header) -->
                  <div class="content giftorder-page">
                     <div class="row">
                        <div class="col-md-12" style="margin-bottom: 10px;">
                           @if($GiftOrder->order_status == 'Pending' || $GiftOrder->order_status == 'Accepted' )
                           <a href="{{ url('gift_orders/Pending') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
                           @else
                           <a href="{{ url('gift_orders/Complete') }}" class="btn btn-primary btn-sm" ><i class="fa fa-arrow-left"></i> Back </a>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12">
                           <div class="card card-primary card-outline">
                              <div class="card-body">
                                 
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="head_sss">
                                          <!-- <h4>Order ID- &nbsp;  ORD#{{$GiftOrder->id}}</h4> -->
                                          <h4>Order ID- &nbsp;  {{$GiftOrder->random_order_id ? $GiftOrder->random_order_id : $GiftOrder->id }}</h4>
                                          <div class="head_sss_in">
                                          <p><span class="title">Order Date: </span> <span class="value">{{$GiftOrder->created_at}} </span></p>
                                          <p><span class="title">Order Status: </span><span class="value">{{$GiftOrder->order_status}}</span></p>
                                          <?php
                                             $orderType = '';

                                             if ($GiftOrder->dine_in == 'Pickup') {

                                                if ($GiftOrder->pickup_option == 'Inside-The-Car') {
                                                    $orderType .= $GiftOrder->dine_in.'->'.$GiftOrder->pickup_option.'->'.$GiftOrder->car_detail_color;

                                                } else {
                                                    $orderType .= $GiftOrder->dine_in.'->'.$GiftOrder->pickup_option;
                                                }

                                             } else {
                                                $orderType = $GiftOrder->dine_in;
                                             }
                                          ?>

                                          @if ($orderType)
                                             <p><span class="title">Order Type: </span><span class="value">{!!$orderType!!}</span></p>
                                          @endif

                                          @if ($GiftOrder->reasion)
                                            <p><span class="title">Cancel Reasion: </span> <span class="value">{{$GiftOrder->reasion}}</span></p>
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
                        <div class="col-md-12">
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
													<td> {{$GiftOrder->getUser->name  ?? 'No Name '}}  </td>
												 </tr>
												 <tr>
													<th> Email</th>
													<td> {{$GiftOrder->getUser->email  ?? 'No Email '}} </td>
												 </tr>
												 <tr>
													<th> Phone</th>
													<td>  {{$GiftOrder->getUser->mobile  ?? 'No Mobile '}}  </td>
												 </tr>
												 <!-- <tr>
													<th>Shipping Address </th>
													<td  style="white-space: pre-wrap;"> {{$GiftOrder['address_id']}}
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
                        <!-- <div class="col-md-6">
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
													<td> {{$GiftOrder['transaction_id'] ?? 'Not Available'}}  </td>
												 </tr>
												 <tr>
													<th>  Payment Type </th>
													<td> {{$GiftOrder['payment_type'] ?? 'Not Available'}}  </td>
												 </tr>
												 <tr>
													<th>Shipping Price </th>
													<td>{{$GiftOrder['shipping_charges'] ?? 'Not Available'}}
													</td>
												 </tr>
											  </tbody>
										   </table>
										</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div> -->
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
                                                <th>Order Number </th>
                                                <th>Gift Name</th>
                                                <th>SKU Code</th>
                                                <th>Gift Attributes</th>
                                                <th>Item Qty</th>
                                                <th>Points</th>

                                             </tr>
                                          </thead>
                                          <tbody>

                                    @foreach($GiftOrderDetails as $orders_detail)
                                    <div class="mt-4">
                                       <tr>
                                          <td>
                                             {{$GiftOrder->random_order_id ? $GiftOrder->random_order_id : $GiftOrder->id }}
                                             <!-- {{$orders_detail->gift_order_id}} -->
                                          </td>
                                          <td>
                                             {{$orders_detail->getGift->name ?? 'No gift Name'}}
                                          </td>
                                          <td>
                                             {{$orders_detail->getGift->sku_code ?? 'No SKU Code'}}
                                          </td>

                                          <!-- <td>{{$orders_detail->varient_name ?? 'Not Available'}}</td> -->
                                          <td>{{$orders_detail->product_attrs ? $orders_detail->product_attrs : 'Not Added'}}</td>
                                          <td><span class="qty-row">{{$orders_detail->qty}}</span></td>
                                          <td>{{$orders_detail->points ?? 'Not Available'}}</td>
                                       </tr>
                                    </div>
                                    @endforeach
                                          <tr>
                                             <td class="bold" colspan='5' style="text-align:right">Total KP</td>
                                             <td> {{$GiftOrder->points ?? 'Not Available' }} </td>
                                          </tr>
                                          <tr>
                                             <td class="bold" colspan='5' style="text-align:right">Tax</td>
                                             <td> {{$GiftOrder->tax_amount ?? 'Not Applied' }} </td>
                                          </tr>
                                          <tr>
                                             <td class="bold" colspan='5' style="text-align:right">Shipping Charges</td>
                                             <td> {{$GiftOrder->shipping_charges ?? 'Not Applied' }} </td>
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