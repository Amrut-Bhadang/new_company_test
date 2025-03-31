@if($record)
    <div class="col-md-12">
        <div class="form-group">
          <label class="control-label" for="category_id">{{$category_type}}*</label>
          <select name="category_id" class="form-control multipal  multiple-search" data-placeholder="Select {{$category_type}} category" style="width: 100%;" data-parsley-required="true" >
            <option value="">--Select {{$category_type}}--</option>
            @foreach ($record as $record)
                <option value="{{ $record->id }}" {{ $record->id == $category_id ? 'selected' : '' }} >{{ $record->name }}</option>
            @endforeach
          </select>
        </div>
    </div>
@endif