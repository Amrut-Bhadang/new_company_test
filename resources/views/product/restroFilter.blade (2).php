@if($records)
<div class="form-group">
    <select name="restaurant_id" id="restaurant_id" class="form-control celebrityPrice select3" data-placeholder="Select Store" style="width: 100%;" data-parsley-required="true">
        <option value=''>--Select Store--</option>
        @foreach ($records as $record)
          <option value="{{ $record->id }}">{{ $record->name }}</option>
      	@endforeach
    </select>
</div>
@endif