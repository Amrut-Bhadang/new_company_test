@if($record)
  @if($type != 'court')
    <div class="col-md-12">
        <div class="form-group">
        <label class="control-label" for="type_id">{{ __('backend.banner_for') }}</label>
        <select name="type_id" id="type_id" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.banner_for') }}" data-dropdown-css-class="select2-primary">
        <option value="">--{{ __('backend.Select') }} {{ __('backend.banner_for') }}--</option>
            @foreach ($record as $record)
                <option  value="{{ $record->id }}" {{ $record->id == $type_id ? 'selected' : '' }}>{{ $record->name }}</option>
            @endforeach
          </select>
        </div>
    </div>
  @else 
    <div class="col-md-12">
        <div class="form-group">
        <label class="control-label" for="type_id">{{ __('backend.banner_for') }}</label>
        <select name="type_id" id="type_id" data-parsley-required="true" class="form-control select2"  data-placeholder="{{ __('backend.Select') }} {{ __('backend.banner_for') }}" data-dropdown-css-class="select2-primary">
        <option value="">--{{ __('backend.Select') }} {{ __('backend.banner_for') }}--</option>
            @foreach ($record as $record)
                <option value="{{ $record->id }}" {{ $record->id == $type_id ? 'selected' : '' }}>{{ $record->court_name }}</option>
            @endforeach
          </select>
        </div>
    </div>
  @endif
@endif
<script>
    $('.select2').select2();
</script>