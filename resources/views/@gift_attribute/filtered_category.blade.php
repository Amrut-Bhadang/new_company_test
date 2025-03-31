@if($records)
<div class="form-group">
  <select name="sub_category_id" id="sub_category_id" class="form-control select4" multiple="multiple" data-placeholder="Select Sub-Category" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Sub-Category--</option>
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