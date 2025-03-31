@if($records)
<div class="form-group">
  <label for="dish">Item</label>
  <select name="dish_id" class="form-control select2" data-placeholder="Select Item" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select item--</option>
      @foreach ($records as $record)
          <option {{$record->id == $dish_id ? "selected" : ""}} value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif