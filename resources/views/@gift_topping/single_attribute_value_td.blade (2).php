@if($records)
<tr class="attributes_values_tr_new_{{$customizeType}} attributes_values_tr_{{$category_id.$count}}">
	<?php foreach ($records as $k => $v) { ?>
		<td>
			<input type="hidden" name="attributes_lang_id" value="{{$v->id}}">
			<select name="attribute[{{$v->id.$count}}][attribute_value_lang_id][{{$v->id}}]" id="attribute_value_id" class="form-control select2" data-placeholder="Select Attribute" style="width: 100%;" data-parsley-required="true" >
			    <option value=''>--Select Attribute Values--</option>
			      @foreach ($v->attribute_values as $record)
			        <option value="{{ $record->id }}">{{ $record->name }}</option>
			    @endforeach
		  	</select>
		</td>
	<?php } ?>
	<td>
   		<select name="attribute[{{$v->id.$count}}][is_mandatory]" class="form-control is_mandatory is_mandatory_{{$v->id}}" disabled style="width: 100%;" data-parsley-required="true" >
            <option value=''>---Select---</option>
            <option value="1">Yes</option>
            <option selected value="0">No</option>
        </select>
        <input type="hidden" name="attribute[{{$v->id.$count}}][is_mandatory]" class="is_mandatory_input_{{$v->id}}" value="0">
   	</td>
   	<!-- <td>
   		<select name="attribute[{{$v->id.$count}}][is_free]" onchange="disabledPrice('{{$count}}')" class="form-control is_free isFree_{{$v->id}} is_free_{{$count}}" disabled style="width: 100%;" data-parsley-required="true" >
            <option value=''>---Select---</option>
            <option value="1">Yes</option>
            <option selected value="0">No</option>
        </select>
        <input type="hidden" name="attribute[{{$v->id.$count}}][is_free]" class="isFree_input_{{$v->id}}" value="0">
   	</td> -->
	<!-- <td><input type="text" name="attribute[{{$v->id.$count}}][price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="price_{{$count}} is_free_input_{{$v->id}} form-control" placeholder="Enter Price"></td> -->
	<!-- <td><input type="text" name="attribute[{{$v->id.$count}}][discount_price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="discount_price_{{$count}} form-control" placeholder="Enter Discount Price"></td> -->

	<!-- <td><input type="text" name="attribute[{{$v->id.$count}}][price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="price_{{$count}} form-control" placeholder="Enter Original Price"></td>
	<td><input type="text" name="attribute[{{$v->id.$count}}][discount_price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="discount_price_{{$count}} form-control" placeholder="Enter Discount Price"></td> -->
	<!-- <input type="hidden" name="attribute[{{$v->id.$count}}][points]"> -->
	<td><input type="text" name="attribute[{{$v->id.$count}}][points]" class="form-control" placeholder="Enter Kilo Points"></td>
   	<td><a id='removeAttribute' onclick="removeAttributeValues('{{$category_id.$count}}')" class="btn btn-warning btn-xs" style="color:white;"><i class="fa fa-minus"></i></a></td>
</tr>
@endif