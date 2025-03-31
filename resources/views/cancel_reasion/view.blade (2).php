@if($brand)
<div class="row">
 <div class="col-md-12">
    <div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
				<tr>
					<td><strong>Reason:</strong></td>
					<td>{{$brand->reasion}}</td>
				</tr>
				<!-- <tr>
					<td><strong>Brand Type:</strong></td>
					<td>{{$brand->brand_type}}</td>
				</tr> -->
				<tr>
					<td><strong>Status:</strong></td>
					<td>
						@if($brand->status  === 1)
							Active
						@else
							In-active
						@endif
					</td>
				</tr>
				<tr>
					<td><strong>Created At:</strong></td>
					<td>{{date('j F, Y', strtotime($brand->created_at))}} </td>
				</tr>
		</table>
	</div>
  </div>      
</div>
@endif