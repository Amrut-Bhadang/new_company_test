@if($records)
<div class="form-group">
    <select name="restaurant_id" id="restaurant_id" class="form-control select3" multiple="multiple"  data-placeholder="Select Store" data-dropdown-css-class="select2-primary">
	   	<option value="">--Select Store--</option>
	   	@foreach ($records as $record)
          <option value="{{ $record->id }}">{{ $record->name }}</option>
      	@endforeach
	</select>
</div>
@endif

<script type="text/javascript">
	$('.select3').select2();
</script>