@if($brand)
<div class="row">
 <div class="col-md-12">
    <center>
        <img src="{{$brand->file_path}}" alt="user" class="img-circle"  width="100" height="100">
    </center>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
				<tr>
					<td><strong>Brand Name:</strong></td>
					@if(isset($brand->name))
						<td>{{$brand->name}}</td>
					@else 
						<td>No Brand Name</td> 
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