@extends('layouts.master')
@section('content')

  <div class="container-fluid" style="padding-top: 34px;">
                  <!-- Content Header (Page header) -->
                  <div class="content">
                     <div class="row">
                        <div class="col-md-12">
                           <div class="card card-primary card-outline">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 10px;">
                                       <button class="btn btn-primary btn-sm" onclick="history.length > 1 ? history.go(-1) : window.location = 'http://3.130.73.173/vendors/dashboard';">Back <i class="fa fa-arrow-left"></i></button>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-6">
                                       <h4>Order ID- &nbsp;  ORD#{{$orders->id}}</h4>
                                       <p><span class="title">Order Date:</span> <span class="value">{{$orders->created_at}} </span></p>
                                       <p><span class="title">Order Status :</span>
                                          <span class="value">
                                            {{$orders->order_status}}
                                          </span>
                                       </p>
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
												 <tr>
													<th>Shipping Address </th>
													<td  style="white-space: pre-wrap;"> {!! $orders->user_address !!}
													</td>
												 </tr>
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
													<td> {{$orders->transaction_id}}</td>
												 </tr>
												 <tr>
													<th>  Payment Type </th>
													<td> {{$orders->payment_type ?? 'Case on delevery'}}    </td>
												 </tr>
												 <tr>
													<th>Shipping Price </th>
													<td> {{$orders->orders_shipping_charges }}
													</td>
												 </tr>
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
													<!-- <th>Product Image</th> -->
													<th> Product Type </th>
													<th>Item Qty</th>
													<th>Subtotal</th>
												 </tr>
											  </thead>
											  <tbody>

										@foreach($orders_details as $orders_detail)
										<div class="mt-4">
												 <tr>
													<td>
													   ORD#{{$orders_detail->order_id}}
													</td>
													<td>
													   {{$orders_detail->name}}
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
													<td> ${{$orders_detail->amount }} </td>
												 </tr>

												 </div>
										  @endforeach
												 <tr>
													<td class="bold" colspan='4' style="text-align:right">Shipping Cost</td>
													<td>  {{$orders->orders_shipping_charges  }} </td>
												 </tr>
												 <tr>
													<td class="bold" colspan='4' style="text-align:right">Grand Total</td>
													@php
													$a = $orders->orders_shipping_charges;
													$b = $orders->amount;
													$total = $a+$b;

													  @endphp
													<td>  {{$total}} </td>
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