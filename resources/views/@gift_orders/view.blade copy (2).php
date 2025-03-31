@extends('layouts.master')

@section('content')
@if($orders)
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ __('Order Details') }}</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item">
                @if($orders->order_status == 'Complete')
                    <a href="{{ url('orders') }}/{{$orders->order_status}}">{{ __('Complete Orders Manager') }}</a>
                @elseif($orders->order_statuss == 'Accepted')
                    <a href="{{ url('orders') }}/{{$orders->order_status}}">{{ __('Accepted Orders Manager') }}</a>
                <!-- @elseif($orders->order_status == 3)
                    <a href="{{ url('orders') }}/{{$orders->order_status}}">{{ __('Perpare Orders Manager') }}</a>
                @elseif($orders->order_status == 4)
                    <a href="{{ url('orders') }}/{{$orders->order_status}}">{{ __('Deliver Orders Manager') }}</a> -->
                @elseif($orders->order_status == 'Cancel')
                    <a href="{{ url('orders') }}/{{$orders->order_status}}">{{ __('Cancel Orders Manager') }}</a>
                @else
                    <a href="{{ url('orders') }}/{{$orders->order_status}}">{{ __('New Orders Manager') }}</a>
                @endif
                </li>
                <li class="breadcrumb-item active">{{ __('Order Details') }}</li>
            </ol>
        </div>
    </div>
</div>
<div class="content giftorder-page">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <strong>Order No :</strong>
                        </div>
                        <div class="col-md-2">
                            ORD #{{$orders->id}}
                        </div>
                        <div class="col-md-2">
                            <strong>Total Amount :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$orders->amount}}
                        </div>
                        <div class="col-md-2">
                            <strong>Status :</strong>
                        </div>
                        <div class="col-md-2">
                            @if($orders->order_status == 'Complete')
                                Complete Order
                            @elseif($orders->order_statuss == 'Accepted')
                                Accepted Order
                            <!-- @elseif($orders->order_status == 3)
                                Perpare Order
                            @elseif($orders->order_status == 4)
                                Deliver Order -->
                            @elseif($orders->order_status == 'Cancel')
                                Cancel Order
                            @else
                                New Order
                            @endif
                        </div>

                    </div>
                    <br>
                    @if(isset($chef_details))
                    <div class="row">
                        <div class="col-md-2">
                            <strong>Accpeted Chef  :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$chef_details->name}}
                        </div>
                        <div class="col-md-2">
                            <strong>Chef Mobile:</strong>
                        </div>
                        <div class="col-md-2">
                            {{$chef_details->mobile}}
                        </div>
                        <div class="col-md-2">
                            <strong>Chef Email :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$chef_details->email}}
                        </div>
                    </div>
                    @endif
                    <br>

                    <div class="row">
                        <div class="col-md-2">
                            <strong>Customer Name :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$orders->user_name}}
                        </div>
                        <div class="col-md-2">
                            <strong>Customer Mobile :</strong>
                        </div>
                        <div class="col-md-2">
                        +{{$orders->country_code}}  {{$orders->user_mobile}}
                        </div>
                        <div class="col-md-2">
                            <strong>Customer Email :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$orders->user_email}}
                        </div>
                        <div class="col-md-2">
                            <strong>Customer Address :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$orders->user_address}}
                        </div>
                        <div class="col-md-2">
                            <strong>Latitude :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$orders->latitude}}
                        </div>
                        <div class="col-md-2">
                            <strong>Longitude :</strong>
                        </div>
                        <div class="col-md-2">
                            {{$orders->longitude}}
                        </div>

                    </div>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-sm">
							<thead>
								<tr>
									<th>{{ __('Product Name') }}</th>
									<th> {{ __('Qty') }}</th>
									<th>{{ __('Amount') }}</th>
									<!-- <th>{{ __('Chef Amount') }}</th>
									<th>{{ __('Celebrity Amount') }}</th>
									<th>{{ __('Admin Amount') }}</th> -->
									<th>{{ __('Points') }}</th>

								</tr>
							</thead>
							<tbody>
								@foreach($orders_details as $orders_detail)
								<tr>
								   <td>{{$orders_detail->name}}</td>
								   <td>{{$orders_detail->qty}}</td>
								   <td>{{$orders_detail->total_amount}}</td>
								   <!-- <td>{{$orders_detail->chef_amount}}</td>
								   <td>{{$orders_detail->celebrity_amount}}</td>
								   <td>{{$orders_detail->admin_amount}}</td> -->
								   <td>{{$orders_detail->points}}</td>
								</tr>
								@endforeach
								<!-- <tr>
								   <td>Total Amount:</td>
								   <td></td>
								   <td>{{$orders->total_amount}}</td>
								   <td>{{$orders->chef_amount}}</td>
								   <td>{{$orders->celebrity_amount}}</td>
								   <td>{{$orders->admin_amount}}</td>
								   <td>{{$orders->points}}</td>
								</tr> -->
							</tbody>
						</table>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection