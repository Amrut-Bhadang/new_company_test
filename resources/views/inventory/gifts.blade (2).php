
@if($get_gift)
    
    <div class="form-group">
      <label class="control-label" for="category_id">Gifts*</label>
      <select name="gift_id" class="form-control " data-placeholder="Select Gift" style="width: 100%;" data-parsley-required="true" >
        <option value="">--Select Gift--</option>
        @foreach ($get_gift as $record)
            <option value="{{ $record->id }}">{{ $record->name }}</option>
        @endforeach
      </select>
    </div>
   
@endif
<script type="text/javascript">
  $('.select4').select2();
</script>