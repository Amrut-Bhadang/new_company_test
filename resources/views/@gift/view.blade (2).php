@if($gift)
<div class="row">
 <div class="col-md-12">
    <center>
        <?php $imageUrl = (!empty($gift->main_image)) ? $gift->main_image : ''; 
        ?>

        @if($imageUrl)
            <img src="{{$imageUrl}}" alt="user" class="img-circle"  width="100" height="100">
        @else
            <img src="{{ URL::asset('images/image.png')}}" alt="user" class="img-circle" width="100" height="100">
            
        @endif
    </center>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
			<tr>
				<td><strong>Name:</strong></td>
				<td>{{$gift->name}}</td>
			</tr>
			<tr>
				<td><strong>Category Name:</strong></td>
				<td>{{$gift->cat_name}}</td>
			</tr>
			<!-- <tr>
				<td><strong>Amount:</strong></td>
				<td>QAR {{$gift->amount}}</td>
			</tr> -->
			<!-- <tr>
				<td><strong>Discount:</strong></td>
				<td>{{$gift->discount}}</td>
			</tr> -->
			<tr>
				<td><strong>Kilo Point:</strong></td>
				<td>{{$gift->points}}</td>
			</tr>
			<tr>
				<td><strong>Quantity:</strong></td>
				<td>{{$gift->quantity}}</td>
			</tr>
			<tr>
				<td><strong>Weight:</strong></td>
				<td>{{$gift->weight}}</td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td>
				<td>
					@if($gift->is_active  === 1)
						Active
					@else
						Deactive
					@endif
				</td>
			</tr>
			<tr>
				<td><strong>Created At:</strong></td>
				<td>{{date('j F, Y', strtotime($gift->created_at))}} </td>
			</tr>
			<tr>
				<td><strong>Description:</strong></td>
				<td>{!! $gift->description !!}</td>
			</tr>
			<tr>
				<td colspan='2'><center><strong>Other Images:</strong></center></td>
			</tr>
			<tr>
				<td colspan='2' class="img_sec">
				@foreach($gift_images as $images)
					<img width="80" height="80" src="{{$images->image}}"/>
				@endforeach
				</td>
			</tr>

		</table>
	</div>      
  </div>      
  </div>
@endif