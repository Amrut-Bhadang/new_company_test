@if($register_detail)
	<div class="table-responsive">
		<table style="border-collapse: collapse;padding: 0;margin: 20px auto;text-align: center;font-size: 17px;font-family: 'Poppins', sans-serif;
		" width="600px" bgcolor="#f7f7f7">
		  <thead>
			<tr>
			  <td style="padding: 15px 30px 10px;">
				<a href="#"><img src="{{ URL::asset('assets/images/logo-light-text.png')}}"></a>
			  </td>
			</tr>
		  </thead>
		  <tbody style="background: #fff;">
			<tr>
			  <td style="padding: 50px 30px;">
				<img src="{{ URL::asset('images/register.png')}}" style="max-width: 150px;">

				<h3 style="margin: 30px auto 15px;font-size: 38px;text-transform: uppercase;color: #383838;max-width: 70%;font-weight: 900;line-height: 1.2;">{{ $register_detail->name }}</h3>
				<p style="margin-top: 10px;line-height: 1.5;font-size: 26px;margin-bottom: 10px;">{{ $register_detail->subject }}</p>
				<p style="margin-bottom: 0;margin-top: 0;">{!! $register_detail->description !!}</p>
			  </td>
			</tr>
		  </tbody>
		  <tfoot>
			<tr>
			  <td style="padding: 30px;font-size: 14px;">{{ $register_detail->footer }}</td>
			</tr>
		  </tfoot>
		</table>
	</div>
@endif
