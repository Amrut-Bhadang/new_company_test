@if(!empty($attribute_values))
	<div class="row attributeValues_{{$attribute_id}}">
		<div class="col-md-6">
		  <div class="form-group">
		    <label class="control-label" for="attribute_name">Attribute Name*</label>
		    <input type="text" class="form-control" name="attribute[{{$attribute_id}}][attribute_name]" value="{{$attribute_values[0]['attribute_name']}}" disabled="">
		    <input type="hidden" name="attribute[{{$attribute_id}}][attribute_id]" value="{{$attribute_id}}">
		  </div>
		</div>
		<div class="col-md-6">
		  <div class="form-group">
		    <label class="control-label" for="attribute_value_ids">Attribute Values*</label>
		    <select name="attribute[{{$attribute_id}}][attribute_value_ids][]" class="form-control multipal multiple-search" data-placeholder="Select Attribute Values" style="width: 100%;" data-parsley-required="true" multiple="multiple">
		      <option value="">--Select Attribute Values--</option>
		      @foreach ($attribute_values as $attribute_values)
		          <option value="{{ $attribute_values['id'] }}">{{ $attribute_values['name'] }}</option>
		      @endforeach
		    </select>
		  </div>
		</div>
		<div class="col-md-6">
	        <div class="form-group">
		        <label class="control-label" for="points">Kilo Points*</label>
		        <input type="text" name="attribute[{{$attribute_id}}][points]" value="" id="points" class="form-control" placeholder="Kilo Points" data-parsley-type="digits"  />
	        </div>
	    </div>    
	    <div class="col-md-6">
	        <div class="form-group">
		        <label class="control-label" for="video">Video*</label>
		        <input type="text" name="attribute[{{$attribute_id}}][video]" id="video" class="form-control" placeholder="Video Url" />
	        </div>
	    </div>
		<div class="col-md-6">
	        <div class="form-group">
	        <label class="control-label" for="admin_price">Discount Prices*</label>
	        <input type="text" name="attribute[{{$attribute_id}}][discount_price]" value="" id="discount_price" class="form-control" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Discount Prices" />
	        </div>
	    </div>
	    <div class="col-md-6">
	        <div class="form-group">
		        <label class="control-label" for="price">Original Prices*</label>
		        <input type="text" name="attribute[{{$attribute_id}}][price]" value="" id="price" class="form-control" data-parsley-pattern="^-?[0-9]\d*(\.\d+)?$" placeholder="Total Prices"   />
	        </div>
	    </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('.multipal').select2();
		});
	</script>
@endif