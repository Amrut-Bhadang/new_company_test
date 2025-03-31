@if($category)
<div class="row">
 <div class="col-md-12">
 	<center>
        <?php $imageUrl = (!empty($category->image)) ? $category->image : URL::asset('/imagesno-image-available.png'); ?>
        <img src="{{$imageUrl}}" class="img-circle"  width="100" height="100">
      
    </center>
  <div class="table-responsive">
	  <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
			<tr>
				<td><strong>Category Name:</strong></td>
				<td>{{$category->name}}</td>
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