@if($products)
<?php /*dd($products);*/  ?>
<div class="row">
 <div class="col-md-12">
    <center>
        <?php $imageUrl = (!empty($products->main_image)) ? $products->main_image : ''; ?>
       <!--  <img src="{{$products->main_image}}" alt="user" class="img-circle"  width="100" height="100"> -->
        @if($imageUrl)
            <img src="{{$imageUrl}}" alt="user" class="img-circle"  width="100" height="100">
        @else
            <img src="{{ URL::asset('images/image.png')}}" alt="user" class="img-circle" width="100" height="100">
            
        @endif
    </center>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
			<tr>
				<td><strong>Service:</strong></td>
				<td>{{$products->main_category_name}}</td>
			</tr>
			<tr>
				<td><strong>Vendor:</strong></td>
				<td>{{$products->brand_name}}</td>
			</tr>
			<tr>
				<td><strong>Category:</strong></td>
				<td>{{$products->category_name}}</td>
			</tr>
			<tr>
				<td><strong>Store:</strong></td>
				<td>{{$products->restaurant_name}}</td>
			</tr>
			<tr>
				<td><strong>Product Name:</strong></td>
				<td>{{$products->name}}</td>
			</tr>
			<!-- <tr>
				<td><strong>Category Name:</strong></td>
				<td>{{$products->cat_name}}</td>
			</tr> -->

			@if($products->products_type)
				<tr>
					<td><strong>Product Type:</strong></td>
					<td>{{$products->products_type}}</td>
				</tr>
			@endif

			@if($products->product_for == 'dish')
				<tr>
					<td><strong>Prepration Time:</strong></td>
					<td>{{$products->prepration_time.' Min'}}</td>
				</tr>
			@endif

			@if($products->customization == 'No')
				<tr>
					<td><strong>Original Price:</strong></td>
					<td>QAR {{$products->total_amount}}</td>
				</tr>
				<tr>
					<td><strong>Discount Price:</strong></td>
					<td>QAR {{$products->discount_price}}</td>
				</tr>
			@endif
			<tr>
				<td><strong>Point:</strong></td>
				<td>{{$products->kp}}</td>
			</tr>

			@if($products->extra_kilopoints)
				<tr>
					<td><strong>Extra Point:</strong></td>
					<td>{{$products->extra_kilopoints}}</td>
				</tr>
			@endif
			<!-- <tr>
				<td><strong>Customize Option:</strong></td>
				<td>{{ ucfirst($products->customize_option) }}</td>
			</tr>
			<tr>
				<td><strong>Specifics:</strong></td>
				<td>{{ $products->product_attr }}</td>
			</tr> -->
			<!-- <tr>
				<td><strong>Serve:</strong></td>
				<td>{{$products->serve}}</td>
			</tr> -->
			@if($products->video)
				<tr>
					<td><strong>Video URL:</strong></td>
					<td>{{$products->video}}</td>
				</tr>
			@endif

			@if($products->shop_type)
				<tr>
					<td><strong>Shop Type:</strong></td>
					<td>{{ucfirst($products->shop_type)}}</td>
				</tr>
			@endif
			
			@if($products->delivery_time)
				<tr>
					<td><strong>Delivery Time:</strong></td>
					<td>{{ucfirst($products->delivery_time)}} {{$products->delivery_hours ? $products->delivery_hours.' Days' : ''}}</td>
				</tr>
			@endif

			<tr>
				<td><strong>SKU Code:</strong></td>
				<td>{{$products->sku_code}}</td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td>
				<td>
					@if($products->is_active  === 1)
						Active
					@else
						Deactive
					@endif
				</td>
			</tr>
			<tr>
				<td><strong>Created At:</strong></td>
				<td>{{date('j F, Y', strtotime($products->created_at))}} </td>
			</tr>
			<!-- <tr>
				<td><strong>Product Ingredients:</strong></td>
				<td>
				@foreach($product_ingredients as $ingredients)
					{{$ingredients->name}}<br />
				@endforeach
				</td>
			</tr> -->
			<!-- <tr>
				<td><strong>Recipe Description:</strong></td>
				<td style="word-break: break-all;">{!! $products->recipe_description !!}
				</td>
			</tr> -->
			<tr>
				<td><strong>Description:</strong></td>
				<td style="word-break: break-all;">{!! $products->long_description !!}</td>
			</tr>
			
			<tr>
				<td colspan='2'><center><strong>Other Images:</strong></center></td>
			</tr>
			<tr>
				<td colspan='2'>
				@foreach($products_images as $images)
					<img width="80" height="80" src="{{$images->image}}"/>
				@endforeach
				</td>
			</tr>
		</table>
		<h4 class="table_heading">Customization</h4>

		<?php if ($products->customize_option == 'normal') { ?>
			<table id="attribute_listing" class="table table-striped table-bordered" style="width:100%">
			    <?php foreach ($products->product_attributes as $key => $value) { ?>
				    <thead>
				        <tr>
				           	<th>{{$value->attribute_name}}</th>
			            	<th>Is Mandatory</th>
							<th>Is Free</th>
			            	<th>Original Price</th>
				        </tr>
				    </thead>
				    <tbody>
				    	<?php if($value->attributeValues) { foreach ($value->attributeValues as $k_attr => $v_attr) { ?>
				    		<tr>
				            	<td>{{$v_attr->attribute_value_name}}</td>
								<td>{{$v_attr->is_mandatory == 1 ? 'Yes' : 'No'}}</td>
								<td>{{$v_attr->is_free == 1 ? 'Yes' : 'No'}}</td>

								@if($products->discount_price)
									<td>{{$products->discount_price + $v_attr->price}}</td>
								@else
									<td>{{$products->total_amount + $v_attr->price}}</td>
								@endif
						    </tr>
						<?php } } ?>
				    </tbody>
				<?php } ?>
			</table>

		<?php } else { ?>
				<table id="attribute_listing" class="table table-striped table-bordered" style="width:100%">
					<tr>
						<?php foreach ($products->product_attributes as $key => $value) { ?>
							<td>
								<table id="attribute_listing" class="table table-striped table-bordered" style="padding: 0 !important">
								    <thead>
								        <tr>
								           	<th>{{$value->attribute_name}}</th>
								        </tr>
								    </thead>
								    <tbody>
								    	<?php if($value->attributeValues) { foreach ($value->attributeValues as $k_attr => $v_attr) { ?>
								    		<tr>
								            	<td>{{$v_attr->attribute_value_name}}</td>
										    </tr>
										<?php } } ?>
								    </tbody>
								</table>
							</td>
						<?php } ?>
						<td>
							<table id="attribute_listing" class="table table-striped table-bordered" style="padding: 0 !important">
							    <thead>
							        <tr>
							           	<th>Original Price</th>
							           	<th>Discount Price</th>
							        </tr>
							    </thead>
							    <tbody>
								<?php foreach ($products->product_attributes as $key => $value) { ?>

									<?php if($key == 0) { ?>

								    	<?php if($value->attributeValues) { foreach ($value->attributeValues as $k_attr => $v_attr) { ?>
								    		<tr>
								            	<td>{{$v_attr->price}}</td>
								            	<td>{{$v_attr->discount_price}}</td>
										    </tr>
										<?php } } ?>
									<?php } ?>
								<?php } ?>
							    </tbody>
							</table>
						</td>						
					</tr>
				</table>
		<?php } ?>
	</div>
  </div>      
  </div>
@endif