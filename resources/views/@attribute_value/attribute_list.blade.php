@if($records)
<div class="form-group">
    <label class="control-label" for="attributes_lang_id">Attribute Name*</label>
    <select name="attributes_lang_id" id="attributes_lang_id" class="form-control select3" onchange="attributeValueChange()" data-placeholder="Select Attribute Name" style="width: 100%;" data-parsley-required="true" >
        <option value=''>--Select attribute name--</option>
        @foreach ($records as $records)
            <option is_color="{{$records->is_color}}" value="{{ $records->id }}">{{ $records->name }}</option>
        @endforeach
    </select>
</div>
@endif