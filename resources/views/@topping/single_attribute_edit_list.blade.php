@if($records)
<h4 class="table_heading">Customization</h4>
<div class="form-group">
	<div class="table-responsive">
	  	<table id="attribute_listing" class="table table-striped table-bordered" style="width:100%">
		    <?php foreach ($records as $key => $value) { ?>
			    <thead>
			        <tr>
			           	<th>{{$value->name}}</th>
		            	<th>Is Mandatory <input type="checkbox" {{ ($value->product_attributes && $value->product_attributes[0]['is_mandatory'] == 1) ? 'checked' : '' }} name="is_mandatory[{{$value->id}}]" id="is_mandatory_{{$value->id}}" onclick="onSelectIsMandatory({{$value->id}})"></th>
						<th>Is Free <input type="checkbox" name="is_free[{{$value->id}}]" {{ ($value->product_attributes && $value->product_attributes[0]['is_free'] == 1) ? 'checked' : '' }} id="is_free_{{$value->id}}" onclick="onSelectIsFree({{$value->id}})"></th>
		            	<th>Original Price(QAR)<span class="text-muted1">(Price will add into original and discount price)</span></th>
		            	<!-- <th>Discount Price</th> -->
		            	<!-- <th>KP Points</th> -->
		            	<th></th>
			        </tr>
			    </thead>
			    <tbody>
			    		<?php if($value->product_attributes) { foreach ($value->product_attributes as $k_attr => $v_attr) { ?>

					    		<tr class="attributes_values_tr attributes_values_tr_{{$value->id.$k_attr}} attributes_values_tr_new_{{$value->id}}">
					            	<td>
					            		<input type="hidden" name="attributes_lang_id" value="{{$value->id}}">
					            		<select name="attribute[{{$value->id.$k_attr}}][attribute_value_lang_id][{{$value->id}}]" id="attribute_value_id" class="form-control select2" data-placeholder="Select Attribute" style="width: 100%;" data-parsley-required="true" >
										    <option value=''>--Select Attribute Values--</option>
										    @if($value->attribute_values)
										      	@foreach ($value->attribute_values as $record)
										        	<option {{(in_array($record->id, $v_attr->selected_attr_values)) ? "selected" : ''}} value="{{ $record->id }}">{{ $record->name }}</option>
										    	@endforeach
										    @endif
									  	</select>
									</td>
				            		<td>
							       		<select name="attribute[{{$value->id.$k_attr}}][is_mandatory]" class="form-control is_mandatory is_mandatory_{{$value->id}}" disabled style="width: 100%;" data-parsley-required="true" >
							                <option value=''>---Select---</option>
							                <option {{$v_attr->is_mandatory == 1 ? "selected" : ''}} value="1">Yes</option>
							                <option {{$v_attr->is_mandatory == 0 ? "selected" : ''}} value="0">No</option>
							            </select>
							            <input type="hidden" name="attribute[{{$value->id.$k_attr}}][is_mandatory]" class="is_mandatory_input_{{$value->id}}" value="{{$v_attr->is_mandatory}}">
							       	</td>
							       	<td>
							       		<select name="attribute[{{$value->id.$k_attr}}][is_free]" onchange="disabledPrice('{{$value->id.$k_attr}}')" disabled class="form-control is_free isFree_{{$value->id}} is_free_{{$value->id.$k_attr}}" style="width: 100%;" data-parsley-required="true" >
							                <option value=''>---Select---</option>
							                <option {{$v_attr->is_free == 1 ? "selected" : ''}} value="1">Yes</option>
							                <option {{$v_attr->is_free == 0 ? "selected" : ''}} value="0">No</option>
							            </select>
							            <input type="hidden" name="attribute[{{$value->id.$k_attr}}][is_free]" class="isFree_input_{{$value->id}}" value="{{$v_attr->is_free}}">
							       	</td>
							       	<td><input type="text" class="is_free_input_{{$value->id}}" name="attribute[{{$value->id.$k_attr}}][price]" {{$v_attr->is_free == 1 ? "disabled" : ''}} data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="price_{{$value->id.$k_attr}} form-control" placeholder="Enter Price" value="{{$v_attr->price}}"></td>
							       	<!-- <td><input type="text" name="attribute[{{$value->id.$k_attr}}][discount_price]" {{$v_attr->is_free == 1 ? "disabled" : ''}} data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="discount_price_{{$value->id.$k_attr}} form-control" placeholder="Enter Discount Price" value="{{$v_attr->discount_price}}"></td> -->
							       	<!-- <td><input type="text" name="attribute[{{$value->id.$k_attr}}][points]" class="form-control" placeholder="Enter Kilo Points" value="{{$v_attr->points}}"></td> -->
							       	<input type="hidden" name="attribute[{{$value->id.$k_attr}}][points]" value="{{$v_attr->points}}">

							       	@if($k_attr == 0)
							       		<td><a id='addMoreAttribute' onclick="addMoreAttributeSingleValues(this)" data-id="{{$value->id}}" class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a></td>

							       	@else
							       		<td><a id='removeAttribute' onclick="removeAttributeValues('{{$value->id.$k_attr}}')" class="btn btn-warning btn-xs" style="color:white;"><i class="fa fa-minus"></i></a></td>
							       	@endif
							    </tr>
						<?php } } ?>
					    <tr class="attributes_values_tr_before-{{$value->id}}"></tr>
			    </tbody>
			<?php } ?>
		</table>
	</div>
</div>
@endif