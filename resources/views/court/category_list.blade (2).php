@if($record)
 
    <div class="col-md-12">
        <div class="form-group">
        <label class="control-label" for="category_id">{{ __('backend.Category') }}</label>
        <select name="category_id" id="category_id" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.Category') }}" data-dropdown-css-class="select2-primary">
        <option value="">--{{ __('backend.Select') }} {{ __('backend.Category') }}--</option>
            @foreach ($record as $record)
                <option value="{{ $record->id }}" {{ $record->id == $category_id ? 'selected' : '' }}>{{ $record->name }}</option>
            @endforeach
          </select>
        </div>
    </div>
 
@endif
<script>
    $('.select2').select2();
</script>