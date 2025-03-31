@if($categories)
<select name="category_id">
  <option value=''>--Select Attribute--</option>
  @foreach ($categories as $record)
      <option value="{{ $record->id }}">{{ $record->name }}</option>
  @endforeach
</select>
@endif