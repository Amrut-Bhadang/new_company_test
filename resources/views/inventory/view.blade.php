@if($inventory)
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
				<tr>
					<td><strong>Gift Category:</strong></td>
					<td>{{$gift_category_id->name}}</td>
				</tr>
				<tr>
					<td><strong>Gift:</strong></td>
					<td>{{$gift_id->name}}</td>
				</tr>
				<tr>
					<td><strong>Price:</strong></td>
					<td>QAR {{$inventory->price}}</td>
				</tr>
				<tr>
					<td><strong>Quantity:</strong></td>
					<td>{{$inventory->quantity}}</td>
				</tr>
				<tr>
					<td><strong>Status:</strong></td>
					<td>
						@if($inventory->status  === 1)
							Active
						@else
							Deactive
						@endif
					</td>
				</tr>
				<tr>
					<td><strong>Created At:</strong></td>
					<td>{{date('j F, Y', strtotime($inventory->created_at))}} </td>
				</tr>
			</table>
		</div>      
	</div>      
</div>
@endif