<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller as Controller;
use App\Models\CourtBooking;
use App\Models\CronJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

		// changeRestaurantStatus();
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function login(Request $request)
	{
		$input =  $request->all();
		$validator = Validator::make($input, [
			'mobile' => 'required',
			'password' => 'required|string|min:6',
		]);
		if ($validator->fails()) {
			$errors     =   $validator->errors();
			$response['status'] = false;
			$response['message'] = $errors;
			return response()->json($response, 200);
		} else {
			$data = ApiCurlMethod('login', $input, 'Normal');
			if ($data->status == true) {
				Session::put('AuthUserData', $data);
				if(isset($input["remember_me"]))
				{
				$hour = time() + 3600 * 24 * 30;
				setcookie('web_mobile', $input['mobile'], $hour);
				setcookie('web_password', $input['password'], $hour);
				setcookie('web_country_code', $input['country_code'], $hour);
				setcookie('web_remember_me', $input['remember_me'], $hour);
				}else{
					setcookie("web_mobile","");
					setcookie("web_password","");
					setcookie("web_country_code","");
					setcookie("web_remember_me","");
				}

				return response()->json($data, 200);
			} else {
				return response()->json($data, 200);
			}
		}
	}
	public function send_otp(Request $request)
	{
		// dd('dddddd',$request->all());
		session()->forget('userData');
		$input =  $request->all();
		$data = ApiCurlMethod('send-otp', $input, 'Normal');
		if ($data->status == true) {
			Session::put('userData', $input);
			return response()->json($data, 200);
		} else {
			return response()->json($data, 200);
		}
	}
	public function resend_otp(Request $request)
	{

		$userData = Session::get('userData') ?? null;

		$input =  $request->all();
		$userData['type'] = 'resend';
		// dd('resend_otp', $input, $userData);

		$data = ApiCurlMethod('send-otp', $userData, 'Normal');
		if ($data->status == true) {
			Session::put('userData', $userData);
			$data->status = 1;
			return response()->json($data, 200);
		} else {
			return response()->json($data, 200);
		}
	}
	public function verify_otp(Request $request)
	{
		$userData = Session::get('userData') ?? null;
		if ($userData != null) {
			$input =  $request->all();
			$input['country_code'] = $userData['country_code'];
			$input['mobile'] = $userData['mobile'];
			// dd($input);
			$data = ApiCurlMethod('verify-otp', $input, 'Normal');
			if ($data->status == true) {
				Session::put('userDataWithOtp', $input);
				return response()->json($data, 200);
			} else {
				return response()->json($data, 200);
			}
		} else {
			$data['message'] = __('api.invalid_user');
			return response()->json($data, 200);
		}
	}
	public function set_password(Request $request)
	{
		$userData = Session::get('userDataWithOtp') ?? null;
		// dd('set_password', $userData, $request->all());
		if ($userData != null) {
			$input =  $request->all();
			$input['country_code'] = $userData['country_code'];
			$input['mobile'] = $userData['mobile'];
			$input['otp'] = $userData['otp'];
			// dd($input);
			$data = ApiCurlMethod('set-password', $input, 'Normal');
			if ($data->status == true) {
				Session::put('AuthUserData', $data);
				return response()->json($data, 200);
			} else {
				return response()->json($data, 200);
			}
		} else {
			$data['message'] = __('api.invalid_user');
			return response()->json($data, 200);
		}
	}
	public function reset_password(Request $request)
	{
		$userData = Session::get('userDataWithOtp') ?? null;
		// dd('set_password', $userData, $request->all());
		if ($userData != null) {
			$input =  $request->all();
			$input['country_code'] = $userData['country_code'];
			$input['mobile'] = $userData['mobile'];
			$input['otp'] = $userData['otp'];
			// dd($input);
			$data = ApiCurlMethod('reset-password', $input, 'Normal');
			if ($data->status == true) {
				return response()->json($data, 200);
			} else {
				return response()->json($data, 200);
			}
		} else {
			$data['message'] = __('api.invalid_user');
			return response()->json($data, 200);
		}
	}
	public function web_logout()
	{
		// dd('ddd');
		session()->forget('AuthUserData');
		$AuthUserData = Session::get('AuthUserData') ?? null;
		if ($AuthUserData == null) {
			return redirect()->route('web.home')->with('success', 'User logout successfully');
		} else {
			return redirect()->Back()->with('success', 'Something went wrong');
		}
	}
	public function changeOrderAuto()
	{
		$today_date = date('Y-m-d');
		$booking = CourtBooking::where('order_status', '!=', 'Completed')->where('order_status', '!=', 'Cancelled')->whereDate('booking_date', '<', $today_date)->get();

		foreach ($booking as $key => $value) {
			if($value->booking_type == 'normal') {

				if ($value->payment_type == 'cash' && $value->payment_received_status == 'Received') {
					$value->update(['order_status' => 'Completed']);

				} else {

					if ($value->payment_type == 'online') {
						$value->update(['order_status' => 'Completed']);

					} else {
						$value->update(['order_status' => 'Cancelled']);
					}
				}
			}else{
				if ($value->payment_type == 'cash' && $value->payment_received_status == 'Received' && $value->joiner_payment_status == 'Received') {
					$value->update(['order_status' => 'Completed']);
				}else{
					if ($value->payment_type == 'online' && $value->joiner_payment_status == 'Received') {
						$value->update(['order_status' => 'Completed']);

					} else {
						$value->update(['order_status' => 'Cancelled']);
					}
				}
			}
		}
		$data['value'] = json_encode($booking);
		// dd($data, $booking);
		$cron_job = CronJob::create($data);
		return 'change status';
	}
}
