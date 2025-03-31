@if($records)
<h4 class="table_heading">Customization</h4>
<div class="form-group">
<div class="table-responsive">
  	<table id="attribute_listing" class="table table-striped table-bordered" style="width:100%">
	    <thead>
	        <tr>
	        	<?php foreach ($records as $key => $value) { ?>
	            	<th>{{$value->name}}</th>
	            <?php } ?>
            	<!-- <th>Is Mandatory</th> -->
            	<!-- <th>Is Free</th> -->
            	<th>Original Price</th>
            	<th>Discount Price</th>
            	<!-- <th>KP Points</th> -->
            	<th></th>
	        </tr>
	    </thead>
	    <tbody>
	    		<?php foreach ($product_attributes as $k_attr => $v_attr) { ?>
		    		<tr class="attributes_values_tr attributes_values_tr_{{$k_attr}}">
		    			<?php foreach ($records as $k => $v) { ?>
			            	<td>
			            		<input type="hidden" name="attributes_lang_id" value="{{$v->id}}">
			            		<select name="attribute[{{$k_attr}}][attribute_value_lang_id][{{$v->id}}]" id="attribute_value_id" class="form-control select2" data-placeholder="Select Attribute" style="width: 100%;" data-parsley-required="true" >
								    <option value=''>--Select Attribute Values--</option>
								      @foreach ($v->attribute_values as $record)
								        <option {{(in_array($record->id, $v_attr->selected_attr_values)) ? "selected" : ''}} value="{{ $record->id }}">{{ $record->name }}</option>
								    @endforeach
							  	</select>
							</td>
	            		<?php } ?>
	            		<!-- <td>
				       		<select name="attribute[{{$k_attr}}][is_mandatory]" class="form-control is_mandatory" disabled style="width: 100%;" data-parsley-required="true" >
				                <option value=''>---Select---</option>
				                <option {{$v_attr->is_mandatory == 1 ? "selected" : ''}} value="1">Yes</option>
				                <option {{$v_attr->is_mandatory == 0 ? "selected" : ''}} value="0">No</option>
				            </select>
				       	</td> -->
				        <input type="hidden" name="attribute[{{$k_attr}}][is_mandatory]" class="is_mandatory_input_{{$v->id}}" value="{{$v_attr->is_mandatory}}">
				       	<!-- <td>
				       		<select name="attribute[{{$k_attr}}][is_free]" onchange="disabledPrice('{{$k_attr}}')" class="form-control is_free is_free_{{$k_attr}}" disabled style="width: 100%;" data-parsley-required="true" >
				                <option value=''>---Select---</option>
				                <option {{$v_attr->is_free == 1 ? "selected" : ''}} value="1">Yes</option>
				                <option {{$v_attr->is_free == 0 ? "selected" : ''}} value="0">No</option>
				            </select>
				       	</td> -->
				        <input type="hidden" name="attribute[{{$k_attr}}][is_free]" class="isFree_input_{{$v->id}}" value="{{$v_attr->is_free}}">
				       	<td><input type="text" name="attribute[{{$k_attr}}][price]" {{$v_attr->is_free == 1 ? "disabled" : ''}} data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="price_{{$k_attr}} form-control" placeholder="Enter Original Price" value="{{$v_attr->price}}"></td>
				       	<td><input type="text" name="attribute[{{$k_attr}}][discount_price]" {{$v_attr->is_free == 1 ? "disabled" : ''}} data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="discount_price_{{$k_attr}} form-control" placeholder="Enter Discount Price" value="{{$v_attr->discount_price}}"></td>
				       	<!-- <td><input type="text" name="attribute[{{$k_attr}}][points]" class="form-control" placeholder="Enter Kilo Points" value="{{$v_attr->points}}"></td> -->
				       	<input type="hidden" name="attribute[{{$k_attr}}][points]" value="{{$v_attr->points}}">

				       	@if($k_attr == 0)
				       		<td><a id='addMoreAttribute' onclick="addMoreAttributeValues()" class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a></td>

				       	@else
				       		<td><a id='removeAttribute' onclick="removeAttributeValues('{{$k_attr}}')" class="btn btn-warning btn-xs" style="color:white;"><i class="fa fa-minus"></i></a></td>
				       	@endif
				    </tr>
				<?php } ?>
			    <tr class="attributes_values_tr_before"></tr>
	    </tbody>
	</table>
</div>
</div>
@endif