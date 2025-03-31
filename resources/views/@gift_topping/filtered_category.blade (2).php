@if($records)
<div class="form-group">
  <select name="category_id" id="category_id" class="form-control select4" onchange="getProductByMainCat()" multiple="multiple" data-placeholder="Select Category" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Category--</option>
      @foreach ($records as $record)
          <option value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif

<script type="text/javascript">
	$(document).ready(function(){
		$('.select4').select2();
	});
</script>