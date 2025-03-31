@if($record)
  <div class="form-group">
    <label class="control-label" for="select_user">{{ __('backend.user') }}</label>
    <select name="select_user[]" id="select_user" multiple="multiple" class="form-control select2" data-placeholder="{{ __('backend.Select') }} {{ __('backend.user') }}" data-dropdown-css-class="select2-primary">
      <option value="">--{{ __('backend.Select') }} {{ __('backend.user') }}--</option>
      @foreach($record as $user)
      <option value="{{$user->id}}">{{$user->name ??''}} ({{$user->country_code ??''}}-{{$user->mobile ??''}})</option>
      @endforeach
    </select>
  </div>
@endif
<script>
    $('.select2').select2();
</script>