@if($records)
<div class="form-group">
    <label class="control-label restaurant_label" for="restaurant_id">Store*</label>
    <select name="restaurant_id" id="restaurant_id" onchange="getCategory()" class="form-control celebrityPrice select3" data-placeholder="Select" style="width: 100%;" data-parsley-required="true">
        <option value=''>--Select--</option>
        @foreach ($records as $record)
          <option data-kp_percent="{{ $record->kp_percent }}" {{$record->id == $restaurant_id ? "selected" : ""}} value="{{ $record->id }}">{{ $record->name }}</option>
      	@endforeach
    </select>
</div>
@endif