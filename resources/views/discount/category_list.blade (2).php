<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"> 
@if($record)
    <div class="col-md-12">
        <div class="form-group">
          <label class="control-label" for="category_id">{{$category_type == 'Restaurant' ? 'Store' : $category_type}}*</label>
          <select name="category_id[]" id="category_id" class="select3" multiple="multiple" data-placeholder="Select {{$category_type == 'Restaurant' ? 'Store' : $category_type}}" style="width: 100%;" data-dropdown-css-class="select2-primary">
            @foreach ($record as $record)
                <option value="{{ $record->id }}" <?php echo (in_array($record->id, $DiscountCategories))?'selected':''; ?> >{{ $record->name }}</option>
            @endforeach
          </select>
        </div>
    </div>
@endif
<script type="text/javascript">
  $('.select3').select2();
</script>
