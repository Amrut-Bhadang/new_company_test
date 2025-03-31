@if($records)
<h4 class="table_heading">Customization</h4>
<div class="form-group">
	<div class="table-responsive">
		<table id="attribute_listing" class="table table-striped table-bordered" style="width:100%">
			<?php foreach ($records as $k => $v) { ?>
				<thead>
					<tr>
						<th>{{$v->name}}</th>
						<th>Is Mandatory <input type="checkbox" name="is_mandatory[{{$v->id}}]" id="is_mandatory_{{$v->id}}" onclick="onSelectIsMandatory({{$v->id}})"></th>
						<th>Is Free <input type="checkbox" name="is_free[{{$v->id}}]" id="is_free_{{$v->id}}" onclick="onSelectIsFree({{$v->id}})"></th>
						<th>Price(QAR)<span class="text-muted1">(Price will add into original and discount price)</span></th>
						<!-- <th>Discount Price(QAR)</th> -->
						<!-- <th>KP Points</th> -->
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr class="attributes_values_tr attributes_values_tr_new_{{$v->id}} attributes_values_tr_{{$k}}">
						<td>
							<input type="hidden" name="attributes_lang_id" value="{{$v->id}}">
							<select name="attribute[{{$v->id.$k}}][attribute_value_lang_id][{{$v->id}}]" id="attribute_value_id" class="form-control select2" data-placeholder="Select Attribute" style="width: 100%;" data-parsley-required="true" >
								<option value=''>--Select Attribute Values--</option>
								  @foreach ($v->attribute_values as $record)
									<option value="{{ $record->id }}">{{ $record->name }}</option>
								@endforeach
							</select>
						</td>
						<td>
							<select name="attribute[{{$v->id.$k}}][is_mandatory]" class="form-control is_mandatory is_mandatory_{{$v->id}}" disabled style="width: 100%;" data-parsley-required="true" >
								<option value=''>---Select---</option>
								<option value="1">Yes</option>
								<option selected value="0">No</option>
							</select>
							<input type="hidden" name="attribute[{{$v->id.$k}}][is_mandatory]" class="is_mandatory_input_{{$v->id}}" value="0">
						</td>
						<td>
							<select name="attribute[{{$v->id.$k}}][is_free]" onchange="disabledPrice(<?php echo $v->id.'0' ?>)" disabled class="form-control is_free isFree_{{$v->id}} is_free_<?php echo $v->id.'0' ?>" style="width: 100%;" data-parsley-required="true" >
								<option value=''>---Select---</option>
								<option value="1">Yes</option>
								<option selected value="0">No</option>
							</select>
							<input type="hidden" name="attribute[{{$v->id.$k}}][is_free]" class="isFree_input_{{$v->id}}" value="0">
						</td>
						<td><input type="text" name="attribute[{{$v->id.$k}}][price]" class="is_free_input_{{$v->id}}" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="price_<?php echo $v->id.'0' ?> form-control" placeholder="Enter Price"></td>
						<!-- <td><input type="text" name="attribute[{{$v->id.$k}}][price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="price_<?php echo $v->id.'0' ?> form-control" placeholder="Enter Original Price"></td>
						<td><input type="text" name="attribute[{{$v->id.$k}}][discount_price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="discount_price_<?php echo $v->id.'0' ?> form-control" placeholder="Enter Discount Price"></td> -->
						<input type="hidden" name="attribute[{{$v->id.$k}}][points]">
						<!-- <td><input type="text" name="attribute[{{$v->id.$k}}][points]" class="form-control" placeholder="Enter Kilo Points"></td> -->
						<td><a id='addMoreAttribute' onclick="addMoreAttributeSingleValues(this)" data-id="{{$v->id}}" data-totalAddMoreAttr="{{ count($v->attribute_values) }}" class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a></td>
					</tr>
					<tr class="attributes_values_tr_before-{{$v->id}}"></tr>
				</tbody>
			<?php } ?>
		</table>
	</div>
</div>
@endif