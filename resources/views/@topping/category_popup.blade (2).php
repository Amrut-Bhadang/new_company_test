@if($records)
<div class="form-group">
  <label for="category">Category</label>
  <select name="popup_category_id" id="popup_category_id" onchange="getDishPopup()" class="form-control select2" data-placeholder="Select Category" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Category--</option>
      @foreach ($records as $record)
          <option {{$record->id == $category_id ? "selected" : ""}} value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif