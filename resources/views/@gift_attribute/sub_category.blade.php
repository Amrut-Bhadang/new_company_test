@if($records)
<div class="form-group">
  <label for="category">Sub-Category*</label>
  <select name="sub_category_id" {{$sub_category_id > 0 ? "disabled" : ""}} id="sub_category_id" class="form-control select2" data-placeholder="Select Sub-Category" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Sub-Category--</option>
      @foreach ($records as $record)
          <option {{$record->id == $sub_category_id ? "selected" : ""}} value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif