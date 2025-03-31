@if($records)
<div class="form-group">
  <select name="brand_id" id="brand_id" class="form-control select2" style="width: 100%;" data-parsley-required="true" >
      <option value=''>--Select Vendor--</option>
      @foreach ($records as $record)
          <option value="{{ $record->id }}">{{ $record->name }}</option>
      @endforeach
  </select>
</div>
@endif