@if($attributes)
<div class="row">
 <div class="col-md-12">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
			<tr>
				<td><strong>Service Name:</strong></td>
				<td>{{$attributes->main_category_name}}</td>
			</tr>
			<tr>
				<td><strong>Category Name:</strong></td>
				<td>{{$attributes->category_name}}</td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td>
				<td>
					@if($attributes->status === 1)
						Active
					@else
						Deactive
					@endif
				</td>
			</tr>
			<tr>
				<td><strong>Created At:</strong></td>
				<td>{{date('j F, Y', strtotime($attributes->created_at))}} </td>
			</tr>
			<tr>
				<td><strong>Attributes:</strong></td>
				<td>
				@foreach($attribute_names as $ingredients)
					{{$ingredients->name}}<br />
				@endforeach
				</td>
			</tr>
		</table>
	</div>
  </div>      
  </div>
@endif