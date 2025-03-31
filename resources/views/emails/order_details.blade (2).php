@if($order_detail)
	<div class="table-responsive">
		<table style="border-collapse: collapse;padding: 0;margin: 20px auto;text-align: center;font-size: 17px;font-family: 'Poppins', sans-serif;
		" width="600px" bgcolor="#f7f7f7">
		  <thead>
			<tr>
			  <td style="padding: 15px 30px 10px;">
				<a href="#"><img src="{{ URL::asset('images/logo.png')}}"></a>
			  </td>
			</tr>
		  </thead>
		  <tbody style="background: #fff;">
			{!! $order_detail->description !!}
		  </tbody>
		  <tfoot>
			<tr>
			  <td style="padding: 30px;font-size: 14px;">{{ $order_detail->footer }}</td>
			</tr>
		  </tfoot>
		</table>
	</div>
@endif
