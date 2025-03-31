@if($category)
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
				
				<tr>
					<td><strong>Item:</strong></td>
					<td>{{$products->name}}</td>
				</tr>
				<tr>
					<td><strong>Specifics:</strong></td>
					<td>{{$category->topping_name}}</td>
				</tr>
				<tr>
					<td><strong>Attribute:</strong></td>
					<td>{{$topping_category->name}}</td>
				</tr>
				<tr>
					<td><strong>Price:</strong></td>
					<td>{{$category->price}}</td>
				</tr>
				<!-- <tr>
					<td><strong>Price Reflect On:</strong></td>
					<td>{{$category->price_reflect_on}}</td>
				</tr> -->
				<tr>
					<td><strong>Is Mandatory:</strong></td>
					<td>
						@if($category->is_mandatory  === 1)
							Yes
						@else
							No
						@endif
					</td>
				</tr>
				<tr>
					<td><strong>Status:</strong></td>
					<td>
						@if($category->status  === 1)
							Active
						@else
							Deactive
						@endif
					</td>
				</tr>
				<tr>
					<td><strong>Created At:</strong></td>
					<td>{{date('j F, Y', strtotime($category->created_at))}} </td>
				</tr>
		  </table>
		</div>      
	</div>
</div>
@endif