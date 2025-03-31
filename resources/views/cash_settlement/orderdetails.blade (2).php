<div class="table-responsive">
	<table style="width: 100%; max-width: 900px; margin: 80px auto 0 ; border-spacing:0; border: solid 2px #000; padding:10px 0; ">
	  <tr>
		<td style="padding: 5px 5px 15px; border-bottom: solid 2px #000;"><img src="{{ URL::asset('images/logo2.png') }}" alt="" style="width: 100px;"></td>
		<td align="right" style="padding: 5px 5px 15px; border-bottom: solid 2px #000;">
		  <strong style="font-weight: bold;"> Order ID :</strong> {{$orders->id}}
		  <br><strong style="font-weight: bold;"> Order Date:</strong> {{$orders->created_at}} 
		  <br><strong style="font-weight: bold;"> Order Status:</strong> {{$orders->order_status}}

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
          <br><strong style="font-weight: bold;"> Order Type:</strong> {!!$orderType!!}
		</td>
	  </tr>
	  <tr>
		<td align="left" style="padding: 10px 0; border-bottom: solid 2px #000;">
		  <table style="width: 100%; border-spacing: 0;">
			<tr>
			  <td style="padding:5px;" colspan="2"><strong style="font-weight: bold;">Account Information</strong></td>
			</tr>
			<tr>
			  <td style="padding:5px 10px;">Customer Name</td>
			  <td style="padding:5px 10px;">{{$orders->user_name}}</td>
			</tr>
			<tr>
			  <td style="padding:5px 10px;">Email</td>
			  <td style="padding:5px 10px;">{{$orders->user_email}}</td>
			</tr>
			<tr>
			  <td style="padding:5px 10px;">Phone</td>
			  <td style="padding:5px 10px;">{{$orders->country_code}}  {{$orders->user_mobile}}</td>
			</tr>
			<tr>
			  <td style="padding:5px 10px;">Shipping Address</td>
			  <td style="padding:5px 10px;">{!! $orders->user_address !!}</td>
			</tr>
		  </table>
		</td>
		<td valign="top" style="padding: 10px 0; border-bottom: solid 2px #000;">
		  <table style="width: 100%; border-spacing: 0;">
			<tr>
			  <td style="padding:5px;" colspan="2" align="right"><strong style="font-weight: bold;">Payment Information</strong></td>
			</tr>
			<tr>
			  <td style="padding: 5px 10px;" align="left">Txn id</td>
			  <td style="padding:5px 10px;" align="right">@if(isset($orders->transaction_id))<td> {{$orders->transaction_id}}</td>@else<td>Not Available</td>@endif</td>
			</tr>
			<tr>
			  <td style="padding:5px 10px;" align="left">Payment Type</td>
			  <td style="padding:5px 10px;" align="right">@if(isset($orders->payment_type))<td>{{$orders->payment_type ?? 'Case on delevery'}}</td>@else<td>Not Available</td>
			  @endif</td>
			</tr>
			<tr>
			  <td style="padding:5px 10px;" align="left">Shipping Price</td>
			  <td style="padding:5px 10px;" align="right">@if(isset($orders->orders_shipping_charges))<td>QAR {{$orders->orders_shipping_charges}}</td>@else<td>Not Available</td>@endif</td>
			</tr>
		  </table>
		</td>
	  </tr>
	  <tr>
		<td colspan="4" style="padding: 10px 10px;"><strong style="font-weight: bold;">Products Ordered</strong></td>
	  </tr>
	  <tr>
		<td colspan="2">
		  <table style="width: 100%; border-spacing: 0;">
			<tr>
			  <th align="left" style="padding:10px 10px; border-bottom: solid 2px #000;">Order Number</th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">Product Name</th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">Product Attributes</th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">SKU Code</th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">Product Type  </th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">Item Qty</th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">KP</th>
			  <th align="left" style="padding:10px 5px; border-bottom: solid 2px #000;">Subtotal</th>
			</tr>
			@foreach($orders_details as $orders_detail)
			<tr>
			  <td style="padding:10px 10px;">ORD#{{$orders->random_order_id ? $orders->random_order_id : $orders->id }}</td>
			  <td style="padding:10px 5px;">{{$orders_detail->name}}</td>
			  <td style="padding:10px 5px;">{{$orders_detail->product_attrs ? $orders_detail->product_attrs : 'Not Added'}}</td>
			  <td style="padding:10px 5px;">{{$orders_detail->sku_code}}</td>
			  <td style="padding:10px 5px;">{{$orders_detail->products_type}}</td>
			  <td style="padding:10px 5px;">{{$orders_detail->qty}}</td>
			  <td style="padding:10px 5px;">{{$orders_detail->order_product_kp}}</td>
			  <td style="padding:10px 5px;">QAR {{ number_format($orders_detail->amount,2) }}</td>
			</tr>
			@endforeach
			<!-- <tr>
			  <td style="padding:10px 5px; border-top: solid 2px #000;" colspan="4" align="right"><strong style="font-weight: bold;">Shipping Cost :</strong></td>
			  @if(isset($orders->orders_shipping_charges))
				 <td style="padding:10px 5px;border-top: solid 2px #000;">QAR {{$orders->orders_shipping_charges}}</td>
			  @else
				 <td>Not Available</td>
			  @endif
			</tr> -->
			<tr>
			  <td style="padding:10px 5px; border-top: solid 2px #000;" colspan="7" align="right"><strong style="font-weight: bold;">Discount Amount :</strong></td>
			  @if(isset($orders->discount_amount))
				 <td style="padding:10px 5px;border-top: solid 2px #000;">QAR {{$orders->discount_amount}}</td>
			  @else
				 <td style="padding:10px 5px;border-top: solid 2px #000;">Not Available</td>
			  @endif
			</tr>
			<tr>
			  <td style="padding:10px 5px;" colspan="7" align="right"><strong style="font-weight: bold;">Tax Amount :</strong></td>
			  @if(isset($orders->tax_amount))
				 <td style="padding:10px 5px;">QAR {{$orders->tax_amount}}</td>
			  @else
				 <td style="padding:10px 5px;">Not Available</td>
			  @endif
			</tr>
			<tr>
			  <td style="padding:10px 5px;" colspan="7" align="right"><strong style="font-weight: bold;">Grand Total :</strong></td>
				@php
				  $a = $orders->orders_shipping_charges;
				  $b = $orders->amount;
				  $c = $orders->tax_amount;
				  $total = $a+$b+$c;
				@endphp
			  <td style="padding:10px 5px;">QAR {{$total}}</td>
			</tr>
		  </table>
		</td>
	  </tr>
	</table>
</div>