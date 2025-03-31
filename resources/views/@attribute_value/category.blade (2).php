@if($records)
<div class="form-group">
  <label for="category">Category</label>
  <select name="category_id" {{$category_id > 0 ? "disabled" : ""}} id="category_id" onchange="getAttributes()" class="form-control select2" data-placeholder="Select Category" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Category--</option>
      @foreach ($records as $record)
          <option {{$record->id == $category_id ? "selected" : ""}} value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif