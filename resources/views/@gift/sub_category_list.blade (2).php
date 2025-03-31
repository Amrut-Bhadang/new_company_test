@if($record)
	<div class="form-group">
        <label class="control-label " for="sub_category_id">Sub Category*</label>
		<select name="sub_category_id" id="sub_category_id" class="form-control select3" data-placeholder="Select sub category" style="width: 100%;" >
		    <option value="">--Select Sub-Category--</option>
		    @foreach ($record as $record)
		        <option value="{{ $record->id }}" {{ $record->id == $sub_category_id ? 'selected' : '' }} >{{ $record->name }}</option>
		    @endforeach
		</select>
	</div>
@endif