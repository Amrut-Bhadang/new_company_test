@if($faq)
<div class="row">
 <div class="col-md-12">
	<div class="table-responsive"> 
	  <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
			<tr>
				<td><strong>Type</strong></td>
				<td>{{ $faq->type }}</td>
			</tr>
			<tr>
				<td><strong>Question:</strong></td>
				@if(isset($faq->question))
					<td>{{$faq->question}}</td>
				@else 
					<td>No Question</td>
				@endif    
			</tr>
			<tr>
				<td><strong>Answer:</strong></td>
				@if(isset($faq->answer))
					<td>{{$faq->answer}}</td>
				@else 
					<td>No Answer</td>
				@endif    
			</tr>

			<tr>
				<td><strong>Status:</strong></td>
				<td>
					@if($faq->status  === 1)
						Active
					@else
						Deactive
					@endif
				</td>
			</tr>
			<tr>
				<td><strong>Created At:</strong></td>
				<td>{{date('j F, Y', strtotime($faq->created_at))}} </td>
			</tr>
	  </table>
	</div>
  </div>      
  </div>
@endif