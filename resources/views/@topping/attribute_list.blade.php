@if($records && !empty($records))
<h4 class="table_heading">Customization</h4>
<div class="form-group">
	<div class="table-responsive">
		<table id="attribute_listing" class="table table-striped table-bordered" style="width:100%">
			<thead>
				<tr>
					<?php foreach ($records as $key => $value) { ?>
						<th>{{$value->name}}</th>
					<?php } ?>
					<!-- <th>Is Mandatory</th>
					<th>Is Free</th> -->
					<th>Original Price(QAR)</th>
					<th>Discount Price(QAR)</th>
					<!-- <th>KP Points</th> -->
					<th></th>
				</tr>
			</thead>
			<tbody>
					<tr class="attributes_values_tr attributes_values_tr_0">
						<?php foreach ($records as $k => $v) { ?>
							<td>
								<input type="hidden" name="attributes_lang_id" value="{{$v->id}}">
								<select name="attribute[0][attribute_value_lang_id][{{$v->id}}]" id="attribute_value_id" class="form-control select2" data-placeholder="Select Attribute" style="width: 100%;" data-parsley-required="true" >
									<option value=''>--Select Attribute Values--</option>
									 @foreach ($v->attribute_values as $record)
										<option value="{{ $record->id }}">{{ $record->name }}</option>
									@endforeach
								</select>
							</td>
						<?php } ?>
						<!-- <td>
							<select name="attribute[0][is_mandatory]" class="form-control is_mandatory" disabled style="width: 100%;" data-parsley-required="true" >
								<option value=''>---Select---</option>
								<option selected value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</td> -->
						<input type="hidden" name="attribute[0][is_mandatory]" class="is_mandatory_input_0" value="1">
						<!-- <td>
							<select name="attribute[0][is_free]" onchange="disabledPrice('0')" class="form-control is_free is_free_0" disabled style="width: 100%;" data-parsley-required="true" >
								<option value=''>---Select---</option>
								<option value="1">Yes</option>
								<option selected value="0">No</option>
							</select>
						</td> -->
						<input type="hidden" name="attribute[0][is_free]" class="isFree_input_0" value="0">
						<td><input type="text" name="attribute[0][price]" class="price_0" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Enter Original Price"></td>
						<td><input type="text" name="attribute[0][discount_price]" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" class="discount_price_0" placeholder="Enter Discount Price"></td>
						<input type="hidden" name="attribute[0][points]">
						<!-- <td><input type="text" name="attribute[0][points]" placeholder="Enter Kilo Points"></td> -->
						<td><a id='addMoreAttribute' onclick="addMoreAttributeValues()" class="btn btn-primary btn-xs" style="color:white;"><i class="fa fa-plus"></i></a></td>
					</tr>
					<tr class="attributes_values_tr_before"></tr>
			</tbody>
		</table>
	</div>
</div>
@endif