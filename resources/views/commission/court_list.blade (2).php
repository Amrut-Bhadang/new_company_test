@if($record)
 
    <div class="col-md-12">
        <div class="form-group">
        <label class="control-label" for="court_id">{{ __('backend.court') }}</label>
        <select name="court_id" id="court_id" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.court') }}" data-dropdown-css-class="select2-primary">
        <option value="">--{{ __('backend.Select') }} {{ __('backend.court') }}--</option>
            @foreach ($record as $record)
                <option value="{{ $record->id }}" {{ $record->id == $court_id ? 'selected' : '' }}>{{ $record->court_name }}</option>
            @endforeach
          </select>
        </div>
    </div>
 
@endif
<script>
    $('.select2').select2();
</script>