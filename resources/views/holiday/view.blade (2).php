@if($holiday)
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
				<tr>
					<td><strong>Store Name:</strong></td>
					@if(isset($restaurant->name))
						<td>{{$restaurant->name}}</td>
					@else
						<td>No Name</td>
					@endif
				</tr>
				<tr>
					<td><strong>Holiday Reason:</strong></td>
					@if(isset($holiday->holiday_reason))
						<td>{!! $holiday->holiday_reason !!}</td>
					@else 
						<td>No Reason Available</td>
					@endif
				</tr>
				<tr>
					<td><strong>Start Date:</strong></td>
					<td> {{date('j F, Y', strtotime($holiday->start_date_time))}}</td>
				</tr>
				<tr>
					<td><strong>End Date:</strong></td>
					<td>{{date('j F, Y', strtotime($holiday->end_date_time))}}</td>
				</tr>
				
				<tr>
					<td><strong>Status:</strong></td>
					<td>
						@if($holiday->status  === 1)
							Active
						@else
							Deactive
						@endif
					</td>
				</tr>
				<tr>
					<td><strong>Created At:</strong></td>
					<td>{{date('j F, Y', strtotime($holiday->created_at))}} </td>
				</tr>
		  </table>
		</div>     
	</div>     
</div>
@endif