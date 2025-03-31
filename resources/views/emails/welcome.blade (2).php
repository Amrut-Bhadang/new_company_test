@if($record)
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
			<!-- <img src="{{ URL::asset('images/lock.png')}}" style="max-width: 150px;"> -->

			<h3 style="margin: 30px auto 15px;font-size: 38px;text-transform: uppercase;color: #383838;max-width: 70%;font-weight: 900;line-height: 1.2;">{{ $record->name }}</h3>
			<!-- <p style="font-size: 26px;margin-bottom: 0;line-height: 1.5;font-weight: bold;">{{ $record->subject }}</p> -->
			<p style="margin-top: 10px;line-height: 1.5;">{!! $record->description !!}</p>
			<!-- {{$record->user_email}}
			{{$record->user_token}} -->
			<a href="{{ url('admin/facility-owner-change-password').'?token='.$record->user_token.'&email='.$record->user_email }}" style="background: #02c1d7;color: #fff;text-decoration: none;text-transform: uppercase;padding: 20px 50px;border-radius: 5px;margin-top: 10px;display: inline-block;font-weight: 600;letter-spacing: 1px;font-size: 20px;">{{__('backend.Reset_Password')}}</a>
		  </td>
		</tr>
	  </tbody>
	  <tfoot>
		<tr>
		  <td style="padding: 30px;font-size: 14px;">{{ $record->footer }}</td>
		</tr>
	  </tfoot>
	</table>
</div>
    

@endif