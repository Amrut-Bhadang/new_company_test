@if($discount)
<div class="row">
 <div class="col-md-12">
    <center>
        <?php $imageUrl = (!empty($discount->image)) ? $discount->image : URL::asset('/imagesno-image-available.png'); ?>
        <img src="{{$imageUrl}}" class="img-circle"  width="100" height="100">
    </center>
 <div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
        <?php if ($discount->discount_code) { ?>
            <tr>
                <td><strong>Discount Code:</strong></td>
                <td>{{$discount->discount_code}}</td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong>Valid From:</strong></td>
            <td>{{date('j F, Y', strtotime($discount->valid_from))}}</td>
        </tr>
        <tr>
            <td><strong>Valid Upto:</strong></td>
            <td>{{date('j F, Y', strtotime($discount->valid_upto))}}</td>
        </tr>

        <?php if ($discount->no_of_use_per_user) { ?>
            <tr>
                <td><strong>No. Of Use Per User:</strong></td>
                <td>{{$discount->no_of_use_per_user}}</td>
            </tr>
        <?php } ?>

        <?php if ($discount->percentage) { ?>
            <tr>
                <td><strong>Percentage:</strong></td>
                <td>{{$discount->percentage}}</td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong>Category Type:</strong></td>
            <td>{{$discount->category_type}} ({{$discount->category_lists}})</td>
        </tr>

        <?php if ($discount->max_discount_amount) { ?>
            <tr>
                <td><strong>Max Discount Amount:</strong></td>
                <td>QAR {!! $discount->max_discount_amount ?? 'Not Available' !!}</td>
            </tr>
        <?php } ?>

        <?php if ($discount->min_order_amount) { ?>
            <tr>
                <td><strong>Min Order Amount:</strong></td>
                <td>QAR {!! $discount->min_order_amount ?? 'Not Available'!!}</td>
            </tr>
        <?php } ?>


        <tr>
            <td><strong>Description:</strong></td>
            <td>{!! $discount->description !!}</td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>
                @if($discount->status  === 1)
                    Active
                @else
                    Deactive
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Created At:</strong></td>
            <td>{{date('j F, Y', strtotime($discount->created_at))}} </td>
        </tr>
	</table>
	</div>
  </div>
  </div>
@endif