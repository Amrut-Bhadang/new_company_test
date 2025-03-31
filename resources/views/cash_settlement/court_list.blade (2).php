@if($record)
<div class="col-md-12">
    <div class="form-group">
        <select name="court_id" id="court_id" style="width:200px" class="form-control select2" multiple="multiple" data-placeholder="{{ __('backend.Select_Court') }}" data-dropdown-css-class="select2-primary">
            <option value="">--{{ __('backend.Select') }} {{ __('backend.Select_Court') }}--</option>
            @foreach ($record as $record)
            <option value="{{ $record->id }}">{{ $record->court_name }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif
<script>
    $('.select2').select2();
</script>