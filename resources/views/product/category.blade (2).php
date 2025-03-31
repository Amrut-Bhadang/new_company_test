@if($records)
<div class="form-group">
  <label for="category">Category*</label>
  <select name="category_id" id="category_id" class="form-control select2" onchange="getSubCategory();" data-placeholder="Select Attribute" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Attribute--</option>
      @foreach ($records as $record)
          <option {{$record->id == $category_id ? "selected" : ""}} value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif