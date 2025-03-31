@if($brand)
<?php /*dd($brand);*/ ?>
<div class="row">
 <div class="col-md-12">
    <center>
        <img src="{{$brand->file_path}}" alt="user" class="img-circle"  width="100" height="100">
    </center>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
				<tr>
					<td><strong>Service:</strong></td>
					@if(isset($brand->brand_type))
						<td>{{$brand->brand_type}}</td>
					@else 
						<td>No Service</td> 
					@endif       
				</tr>
				<tr>
					<td><strong>Brand Category:</strong></td>
					@if(isset($brand->brand_category))
						<td>{{$brand->brand_category}}</td>
					@else 
						<td>No Brand Category</td> 
					@endif       
				</tr>
				<tr>
					<td><strong>Vendor Email:</strong></td>
					@if(isset($brand->email))
						<td>{{$brand->email}}</td>
					@else 
						<td>No Vendor Email</td> 
					@endif       
				</tr>
				<tr>
					<td><strong>Vendor Mobile No.:</strong></td>
					@if(isset($brand->mobile))
						<td>{{$brand->country_code.' '.$brand->mobile}}</td>
					@else 
						<td>No Vendor Number</td> 
					@endif       
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
							Deactive
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