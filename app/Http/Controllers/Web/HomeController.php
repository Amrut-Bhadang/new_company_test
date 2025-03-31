<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller as Controller;
use App\Models\BookingChallenge;
use App\Models\Commission;
use App\Models\ContactUs;
use App\Models\Country;
use App\Models\CourtBooking;
use App\Models\CourtBookingSlot;
use App\Models\DeliveryPrice;
use App\User;
use DB, DateTime;
use App\Models\Courts;
use App\Models\EmailTemplateLang;
use App\Models\OrderBookingAmountLogs;
use App\Models\Facility;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\UserFavourates;
use Carbon\Carbon;
use CURLFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class HomeController extends Controller
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
	public function index()
	{
		$location = $this->location();
		$auth_user = Session::get('AuthUserData');
		// dd($auth_user);
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'slug' => 'about-us');
		$result = ApiCurlMethod('home', $parms, 'Bearer');
		$about_us = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.home'), 'data' => $result, 'about_us' => $about_us];
		return view('web.index', $data);
	}
	public function courts(Request $request )
	{
		$search = $_GET;
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
	
		if ($search != null) {
			if(isset($search['facility']) && !empty($search['facility'])){
				$parms['facility_id'] = $search['facility'];
			}
			if(isset($search['search']) && !empty($search['search'])){
				$parms['search_text'] = $search['search'];
			}
			if(isset($search['category_id']) && !empty($search['category_id'])){
				$parms['category_id'] = $search['category_id'];
			}
			if(isset($search['longitude']) && !empty($search['longitude']) && isset($search['latitude']) && !empty($search['latitude'])){
				$location['long'] = $search['longitude'];
				$location['lat'] = $search['latitude'];
				$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'search_text' => $search['search'], 'category_id' => $search['category_id']);
				$parms['distance'] = 'asc';
			}
		}
		
		$result = ApiCurlMethod('court_list', $parms, 'Bearer');
		$category_list = ApiCurlMethod('get-all-facility-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Courts'), 'data' => $result, 'category_list' => $category_list];
		return view('web.court_list', $data);
	}
	public function courts_pagination(Request $request)
	{
		
		// $search = $_GET;
		$input = $request->all();
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'page' => $input['page'],'booking_date' => $input['booking_date'],'court_sort' => $input['court_sort'],'category_id' => $input['category_id']);
		// if(isset($search) != null){
		// 	$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'],'search_text'=>$search['search'],'category_id'=>$search['category_id'],'page'=>$input['page']);
		// }
		$result = ApiCurlMethod('court_list', $parms, 'Bearer');
		$category_list = ApiCurlMethod('get-all-facility-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Courts'), 'data' => $result, 'category_list' => $category_list];
		return view('web.court_list_pagination', $data);
	}
	
	public function courts_filter(Request $request,$sort = null)
	{
		$input = $request->all();
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		if($request->date != null){
			$parms['date'] = $input['date'];

		}
		if($request->category_id != null){
			$parms['category_id'] = $input['category_id'];
		}
		if(isset($input['court_sort']) && !empty($input['court_sort'])){
			if($input['court_sort'] == 'distance_asc'){
				$parms['distance'] = 'asc';
			}elseif($input['court_sort'] == 'distance_desc'){
				$parms['distance'] = 'desc';
			}
			elseif($input['court_sort'] == 'rating_asc'){
				$parms['rating'] = 'asc';
			}
			elseif($input['court_sort'] == 'rating_desc'){
				$parms['rating'] = 'desc';
			}
		}
		$result = ApiCurlMethod('court_list', $parms, 'Bearer');
		$data = ['title' => __('backend.Courts'), 'data' => $result];
		return view('web.court_list_pagination', $data);
	}
	public function about_us()
	{
		$parms = array('slug' => 'about-us');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.About_US'), 'data' => $result];
		return view('web.about_us', $data);
	}
	public function terms_and_conditions()
	{
		$parms = array('slug' => 'terms-of-use');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.terms_and_conditions'), 'data' => $result];
		return view('web.terms_and_conditions', $data);
	}
	public function how_it_works()
	{
		$parms = array('slug' => 'how_its_work');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.how_it_works'), 'data' => $result];
		return view('web.how_it_works', $data);
	}
	public function private_policy()
	{
		$parms = array('slug' => 'private-policy');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.Private_Policy'), 'data' => $result];
		return view('web.private_policy', $data);
	}
	public function payment_confirmation()
	{
		$parms = array('slug' => 'payment_confirmation');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.Payment_Confirmation'), 'data' => $result];
		return view('web.payment_confirmation', $data);
	}
	public function refund_policy()
	{
		$parms = array('slug' => 'refund_policy');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.Refund_Policy'), 'data' => $result];
		return view('web.refund_policy', $data);
	}
	public function cancellation_policy()
	{
		$parms = array('slug' => 'cancellation_policy');
		$result = ApiCurlMethod('static-content', $parms, 'Bearer');
		$data = ['title' => __('backend.Cancellation_Policy'), 'data' => $result];
		return view('web.cancellation_policy', $data);
	}
	public function contact_us()
	{
		$country = Country::select('sortname', 'phonecode', 'name', 'id')->get();
		$data = ['title' => __('backend.Contact_us'), 'country' => $country];
		return view('web.contact_us', $data);
	}
	public function admin_contact(Request $request)
	{
		$mesasge = [
			'name.required' => __("backend.name_required"),
			'email.required' => __("backend.email_required"),
			'email.email' => __("backend.email_email"),
			'country_code.required' => __("backend.country_code_required"),
			'mobile.required' => __("backend.mobile_required"),
			'mobile.digits_between' => __("backend.mobile_digits_between"),
		];
		$this->validate($request, [
			'name' => 'required|max:255',
			'email' => 'required|email',
			'country_code' => 'required',
			'mobile' => 'required|digits_between:7,15',
		], $mesasge);

		$input = $request->all();
		// dd($input);
		$fail = false;
		if (!$fail) {
			try {

				$data = new ContactUs();
				$data->name = $input['name'];
				$data->email = $input['email'];
				$data->mobile = $input['mobile'];
				$data->country_code = $input['country_code'];
				$data->message = $input['message'];
				$data->status = 1;
				$data->save();

				$result['message'] = __('backend.admin_contact_message');
				$result['status'] = 1;
				return response()->json($result);
			} catch (Exception $e) {
				$result['message'] = __('backend.Something_went_wrong');
				$result['status'] = 0;
				return response()->json($result);
			}
		} else {
			$result['message'] = __('backend.Something_went_wrong');
			$result['status'] = 0;
			return response()->json($result);
		}
		return $this->jsonResponse();
	}
	public function court_detail(Request $request, $id,$slug='')
	{
		
		$location = $this->location();
		$parms = array('court_id' => $id, 'longitude' => $location['long'], 'latitude' => $location['lat']);
		$result = ApiCurlMethod('court-detail', $parms, 'Bearer');
		$favResult = ApiCurlMethod('user-court-favourate', $parms, 'Bearer');
		$facilityId = $result->data->facility_id;

		$facilityData = facility:: where('id',$facilityId)->first();
		if(isset($facilityData) && $facilityData!=''){
			$facility_is_deleted =$facilityData->is_deleted;
		}else{
			$facility_is_deleted = 1;
		}
		
		$data = ['title' => __('backend.Court_Details'), 'data' => $result, 'facility_is_deleted'=>$facility_is_deleted, 'favResult' =>$favResult, 'slug' => $slug];
		

		return view('web.court_detail', $data);
	}
	public function court_detail_data(Request $request, $id)
	{
		$location = $this->location();
		$parms = array('court_id' => $id, 'longitude' => $location['long'], 'latitude' => $location['lat']);
		$result = ApiCurlMethod('court-detail', $parms, 'Bearer');
		return response()->json($result, 200);
	}
	public function bookCourt(Request $request)
	{
		$court_booking_data = Session::get('court_booking_data') ?? null;
		$input = $court_booking_data;
		$input['payment_type'] = $request->payment_type;
		$randomDigit = random_int(100000000000, 999999999999);
		$input['transaction_id'] = $randomDigit;
		$input['payment_for'] = 'booking';
		if($input['payment_type'] == 'online'){
			$input['payment_received_status'] = 'Received';
		}
		Session::put('court_booking_check_out_data', $input);
		$court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
		if($court_booking_check_out_data['payment_type'] == 'online'){
			$response['status'] = true;
			$response['payment'] = 'online';
			return response()->json($response, 200);
		}else{
			$book_court = $this->bookCourt_check_out($court_booking_check_out_data);
			return response()->json($book_court, 200);
		}
		
		// dd($book_court);

	}
	public function bookCourt_check_out($input)
	{
		// dd($request->all(),'vvvvvvvvvvvvvvvvvvvvv');
		$this->code = 200;
		// $input =  $request->all();
		$this->requestdata = $input;
		// $input['booking_type'] = 'normal';
		$message = [
			'court_id.required' => __("api.court_id_required"),
			'court_id.exists' => __("api.court_id_exists"),
			'facility_id.required' => __("api.facility_id_required"),
			'facility_id.exists' => __("api.facility_id_exists"),
			'booking_time_slot.required' => __("api.booking_time_slot_required"),
			'total_amount.required' => __("api.total_amount_required"),
			'booking_type.required' => __("api.booking_type_required"),
			'booking_date.required' => __("api.booking_date_required"),
			'booking_date.date_format' => __("api.booking_date_date_format"),
			'end_booking_date.date_format' => __("api.end_booking_date_date_format"),
		];
		$validator = Validator::make($input, [
			'court_id'   => 'required|exists:courts,id',
			'facility_id' => 'required|exists:facilities,id',
			'booking_time_slot'   => 'required',
			// 'total_amount' => 'required',
			'booking_type' => 'required',
			'booking_date' => 'required|date_format:Y-m-d',
			'end_booking_date' => 'date_format:Y-m-d',

		], $message);
		if ($validator->fails()) {
			$this->errorValidation($validator);
		} else {
			if($input['booking_type'] == 'challenge'){
				$input['total_amount'] = $input['total_amount']*2;
			}
			// dd('bookCourt', $input);
			foreach ($input['booking_time_slot'] as $i => $data) {
				$timeslot = $input['timeslot'];
				$end_time = date('H:i', strtotime($data['start_time'] . "+$timeslot minutes"));
				// dd($data);
				$input['booking_time_slot'][$i]['start_time'] = $data['start_time'];
				$input['booking_time_slot'][$i]['end_time'] = $end_time;
				// booking_time_slot[{{$i}}][start_time]
			}
			// $total_amount = $input['hourly_price'] * count($input['booking_time_slot']);
			// $input['total_amount'] = $total_amount;
			$court = Courts::where('id', $input['court_id'])->first();
			$input['user_id'] = Session::get('AuthUserData')->data->id;
			$input['hourly_price'] = $court->hourly_price;
			$input['minimum_hour_book'] = $court->minimum_hour_book;
			// if end_booking_date input null
			if (!isset($input['end_booking_date'])) {
				$input['end_booking_date'] =  $input['booking_date'];
			}
			$admin_commission = Commission::where(['status' => 1, 'court_id' => $input['court_id']])->first();
			if ($admin_commission != null) {
				$input['admin_commission_percentage'] = $admin_commission->amount;
				$percentage = $admin_commission->amount;
				$total_amount = $input['total_amount'];
				$input['admin_commission_amount'] = ($percentage / 100) * $total_amount;
			} else {
				$input['admin_commission_percentage'] = 0;
				$input['admin_commission_amount'] = 0;
			}

			foreach ($input['booking_time_slot'] as $data) {
				$booking_start_time[] =  $data['start_time'];
			}
			// dd($input);
			// dd($booking_start_time, $input['booking_time_slot'][0]['start_time'], $input);
			// $booking = CourtBooking::where(['court_id' => $input['court_id'], 'booking_date' => $input['booking_date']])->get();
			$booking = CourtBookingSlot::join('court_booking', 'court_booking.id', 'court_booking_slots.court_booking_id')
				->select('court_booking.id', 'court_booking.court_id', 'court_booking.order_status', 'court_booking_slots.court_booking_id', 'court_booking_slots.booking_date', 'court_booking_slots.booking_start_time')
				->where(['court_booking.court_id' => $input['court_id'], 'court_booking.order_status' => 'Pending', 'court_booking.booking_date' => $input['booking_date']])
				->whereIn('court_booking_slots.booking_start_time', $booking_start_time)
				->get();
			// dd($booking);
			if (!count($booking)) {
				$court_booking = new CourtBooking();
				$court_booking->user_id = $input['user_id'];
				$court_booking->court_id = $input['court_id'];
				$court_booking->booking_date = $input['booking_date'];
				$court_booking->booking_type = $input['booking_type'];
				$court_booking->end_booking_date = $input['end_booking_date'];
				$court_booking->facility_id = $input['facility_id'];
				$court_booking->hourly_price = $input['hourly_price'];
				$court_booking->minimum_hour_book = $input['minimum_hour_book'];
				$court_booking->total_amount = $input['total_amount'];
				$court_booking->admin_commission_percentage = $input['admin_commission_percentage'];
				$court_booking->admin_commission_amount = $input['admin_commission_amount'];
				$court_booking->transaction_id = $input['transaction_id'];
				$court_booking->payment_type = $input['payment_type'];
				$court_booking->challenge_type = $input['challenge_type'] ?? 'public';
				$court_booking->payment_received_status = $input['payment_received_status'] ?? 'Pending';
				$court_booking->status = 1;
				$court_booking->save();

				if ($court_booking) {
					// insert booking time slot
					foreach ($input['booking_time_slot'] as $data) {
						$booking_slots = new CourtBookingSlot();
						$booking_slots->court_booking_id =  $court_booking->id;
						$booking_slots->booking_start_time =  $data['start_time'];
						$booking_slots->booking_end_time = $data['end_time'];
						$booking_slots->booking_date = $input['booking_date'];
						$booking_slots->booking_start_datetime = $input['booking_date'] . ' ' . $data['start_time'];
						$booking_slots->booking_end_datetime = $input['booking_date'] . ' ' . $data['end_time'];
						$booking_slots->status =  1;

						$booking_slots->save();
					}
					// create challenge
					$input['amount'] = $input['total_amount'] / 2;
					if ($input['booking_type'] == 'challenge') {
						$booking_challenge = new BookingChallenge();
						$booking_challenge->court_booking_id = $court_booking->id;
						$booking_challenge->user_id = $input['user_id'];
						$booking_challenge->amount = $input['amount'];
						$booking_challenge->challenge_status = 'Accepted';
						$booking_challenge->transaction_id = $input['transaction_id'] ?? '';
						$booking_challenge->payment_type = $input['payment_type'] ?? '';
						$booking_challenge->status = 1;
						$booking_challenge->save();
						  // send notification start
						  $locale = App::getLocale();
						  // App::SetLocale('ar');
						  $notification_title = 'notification_create_challenge_title';
						  $notification_message = 'notification_create_challenge_message';
						  if (isset($input['user_id'])) {
							  send_notification_add($input['user_id'], $user_type = 3, $notification_type = 3, $notification_for = 'create_challenge', $order_id = $court_booking->id, $title = $notification_title, $message = $notification_message);
							  App::SetLocale($locale);
							  $notification_title = __('api.notification_create_challenge_title');
							  $notification_message = __('api.notification_create_challenge_message');
							  send_notification(1, $input['user_id'], $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'create_challenge', 'key' => 'create_challenge'));
							 // panal notification start
							  $playerChanellId = 'pubnub_onboarding_channel_player_'.$input['user_id'];
							  // send_admin_notification($message = $notification_message, $title=$notification_title,$channel_name=$playerChanellId);
							  
							  $locale = App::getLocale();
							  $admin_notification_title = 'admin_notification_create_challenge_title';
							  $admin_notification_message = 'admin_notification_create_challenge_message';
							  $login_user_data = auth()->user();
							  $facility_owner_id = Courts::where('id',$input['court_id'])->first()->facility_owner_id ?? '';
							  $adminChanellId = 'pubnub_onboarding_channel_admin_1';
							  $ownerChanellId = 'pubnub_onboarding_channel_owner_'.$facility_owner_id;
							  
							  add_admin_notification($user_type = 0, $notification_type=0, $notification_for='create_challenge', $title = $admin_notification_title, $message=$admin_notification_message, $user_id = 1, $order_id = $court_booking->id);
							  add_admin_notification($user_type = 1, $notification_type=1, $notification_for='create_challenge', $title = $admin_notification_title, $message=$admin_notification_message, $user_id = $facility_owner_id, $order_id = $court_booking->id);
							  App::SetLocale($locale);
							  // $admin_notification_title = __('backend.admin_notification_create_challenge_message');
							  // $admin_notification_message = __('backend.admin_notification_create_challenge_message');
							  // send_admin_notification($message = $admin_notification_message, $title=$admin_notification_title,$channel_name=$adminChanellId);
							  // send_admin_notification($message = $admin_notification_message, $title=$admin_notification_title,$channel_name=$ownerChanellId);
							  //panal notification end
						  }
						  // send notification end
						
					}else{
						 // end challenge
                        // send notification start
                        $locale = App::getLocale();
                        // App::SetLocale('ar');
                        $notification_title = 'notification_court_book_title';
                        $notification_message = 'notification_court_book_message';
                        send_notification_add($input['user_id'], $user_type = 3, $notification_type = 3, $notification_for = 'book_court', $order_id = $court_booking->id, $title = $notification_title, $message = $notification_message);
                        App::SetLocale($locale);
                        $notification_title = __('api.notification_court_book_title');
                        $notification_message = __('api.notification_court_book_message');
                        send_notification(1, $input['user_id'], $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'order', 'key' => 'order'));
                        // panal notification start
                        $playerChanellId = 'pubnub_onboarding_channel_player_'.$input['user_id'];
                        // send_admin_notification($message = $notification_message, $title=$notification_title,$channel_name=$playerChanellId);
                        
                        $locale = App::getLocale();
                        $admin_notification_title = 'admin_notification_book_court_title';
                        $admin_notification_message = 'admin_notification_book_court_message';
                        $login_user_data = auth()->user();
                        $facility_owner_id = Courts::where('id',$input['court_id'])->first()->facility_owner_id ?? '';
                        $adminChanellId = 'pubnub_onboarding_channel_admin_1';
                        $ownerChanellId = 'pubnub_onboarding_channel_owner_'.$facility_owner_id;
                        
                        add_admin_notification($user_type = 0, $notification_type=0, $notification_for='book_court', $title = $admin_notification_title, $message=$admin_notification_message, $user_id = 1, $order_id = $court_booking->id);
                        add_admin_notification($user_type = 1, $notification_type=1, $notification_for='book_court', $title = $admin_notification_title, $message=$admin_notification_message, $user_id = $facility_owner_id, $order_id = $court_booking->id);
                        App::SetLocale($locale);
                        // $admin_notification_title = __('backend.admin_notification_book_court_message');
                        // $admin_notification_message = __('backend.admin_notification_book_court_message');
                        // send_admin_notification($message = $admin_notification_message, $title=$admin_notification_title,$channel_name=$adminChanellId);
                        // send_admin_notification($message = $admin_notification_message, $title=$admin_notification_title,$channel_name=$ownerChanellId);
                        //panal notification end
                        // send notification end
					}
					// end challenge
					try {
						$booking = $court_booking;
							if($booking->payment_type == 'online'){
							// send email
							$user = User::where('id', $input['user_id'])->first();
							$email = EmailTemplateLang::where('email_id', 7)->where('lang', 'en')->select(['name', 'subject', 'description', 'footer'])->first();
							$description = $email->description;
							$description = str_replace("[NAME]", $user->name, $description);
							$description = str_replace("[order_id]", $booking->id, $description);
			
							$name = $email->name;
							$name = str_replace("[NAME]", $user->name, $name);
			
							$record = (object)[];
							$record->description = $description;
							$record->footer = $email->footer;
							$record->name = $name;
							$record->subject = $email->subject;
							Mail::send('emails.booking_accepted', compact('record'), function ($message) use ($user, $email) {
								$message->to($user->email, config('app.name'))->subject($email->subject);
								$message->from('dev.inventcolabs@gmail.com', config('app.name'));
							});
							// send email
						}
					} catch (Exception $e) {
						
					}
					// session data blank
					Session::put('court_booking_check_out_data', '');
					// send response
					$response['status'] = true;
					$response['message'] = __("api.court_booking_successfully");
					$response['data'] = $court_booking;
					return $response;
				} else {
					$response['status'] = false;
					$response['message'] = __("api.something_worng");
					return $response;
				}
			} else {
				$response['status'] = false;
				$response['message'] = __("api.court_already_booked");
				return $response;
			}
		}
		return $this->jsonResponse();
	}
	public function bookCourtCheckout(Request $request)
	{
		$input = $request->all();
		$total_amount = $input['hourly_price'] * count($input['booking_time_slot']);
		$input['total_amount'] = $total_amount;
		$input['court_name'] = Courts::findorFail($input['court_id'])->court_name;
		if($input['booking_type'] == 'challenge'){
			$input['total_amount'] = $total_amount/2;
		}
		// dd($input);
		Session::put('court_booking_data', $input);
		$court_booking_data = Session::get('court_booking_data') ?? null;
		$court_booking_data['booking_time_slot'] = array_values($court_booking_data['booking_time_slot']);
		$court_booking_data['facility_owner_data'] = User::where('id', $input['facility_owner_id'])->first();

		if ($court_booking_data != null) {
			$court_booking_data = ['court_booking_data' => $court_booking_data];
			return view('web.checkout', $court_booking_data);
		} else {
			$data['message'] = __('api.invalid_user');
			return response()->json($data, 200);
		}
			
	}
	public function challenges(Request $request)
	{
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		$parms['booking_type'] = 'challenge';
		$result = ApiCurlMethod('all-challenge-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Challenges'), 'data' => $result];
		return view('web.challenges', $data);
	}
	public function challenges_pagination(Request $request)
	{
		$location = $this->location();
		$input = $request->all();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'page' => $input['page']);
		$parms['booking_type'] = 'challenge';
		$result = ApiCurlMethod('all-challenge-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Challenges'), 'data' => $result];
		return view('web.challenges_pagination', $data);
	}
	public function challenges_detail(Request $request, $id)
	{
		$location = $this->location();
		$parms = array('court_booking_id' => $id, 'longitude' => $location['long'], 'latitude' => $location['lat']);
		$parms['booking_type'] = 'challenge';
		$result = ApiCurlMethod('court-booking-detail', $parms, 'Bearer');
		$data = ['title' => 'Challenges Detail', 'data' => $result];
		return view('web.challenges_detail', $data);
	}
	public function create_challenge(Request $request, $id)
	{
		$location = $this->location();
		$parms = array('court_id' => $id, 'longitude' => $location['long'], 'latitude' => $location['lat']);
		$result = ApiCurlMethod('court-detail', $parms, 'Bearer');
		$data = ['title' => __('backend.Court_Details'), 'data' => $result];
		return view('web.create_challenge', $data);
	}
	public function join_challenge_checkout(Request $request)
	{
		$input = $request->all();
		$court_booking = CourtBooking::with('courtDetails','bookingTimeSlots')
		->where('id',$input['court_booking_id'])->first();
		$input['court_name'] = $court_booking->courtDetails->court_name;
		$input['booking_date'] = $court_booking->booking_date;
		$input['booking_time_slot'] = $court_booking->bookingTimeSlots[0]->booking_start_time;
		Session::put('join_challenge_data', $input);
		$join_challenge_data = Session::get('join_challenge_data') ?? null;
		if ($join_challenge_data != null) {
			$join_challenge_data = ['join_challenge_data' => $join_challenge_data];
			return view('web.join_challenge_checkout', $join_challenge_data);
		} else {
			$data['message'] = __('api.invalid_user');
			return response()->json($data, 200);
		}
	}
	public function join_challenge(Request $request)
	{
		$join_challenge_data = Session::get('join_challenge_data') ?? null;
		$input = $join_challenge_data;
		$input['payment_type'] = $request->payment_type;
		$randomDigit = random_int(100000000000, 999999999999);
		$input['transaction_id'] = $randomDigit;
		$input['payment_for'] = 'join_challenge';
		if($input['payment_type'] == 'online'){
			$input['payment_received_status'] = 'Received';
		}
		Session::put('court_booking_check_out_data', $input);
		$court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
		// dd($court_booking_check_out_data);
		if($court_booking_check_out_data['payment_type'] == 'online'){
			$response['status'] = true;
			$response['payment'] = 'online';
			return response()->json($response, 200);
		}else{
			$parms = $court_booking_check_out_data;
			$result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
			// session data blank
			Session::put('join_challenge_data', '');
			Session::put('court_booking_check_out_data', '');
			// send response
			return response()->json($result, 200);
		}
	}
	public function change_password()
	{
		$data = ['title' => __('backend.Change_Password')];
		return view('web.change_password', $data);
	}
	public function change_password_submit(Request $request)
	{
		$parms = $request->all();
		$result = ApiCurlMethod('change-password', $parms, 'Bearer');
		return response()->json($result, 200);
	}
	public function completed_booking()
	{
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		$parms['order_status'] = 'Completed';
		$result = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Completed_Bookings'), 'data' => $result];
		return view('web.completed_booking', $data);
	}
	public function completed_booking_pagination(Request $request)
	{
		$location = $this->location();
		$input = $request->all();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'page' => $input['page']);
		$parms['order_status'] = 'Completed';
		$result = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Completed_Bookings'), 'data' => $result];
		return view('web.completed_booking_pagination', $data);
	}
	public function cancelled_booking()
	{
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		$parms['order_status'] = 'Cancelled';
		$result = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Cancelled_Bookings'), 'data' => $result];
		return view('web.cancelled_booking', $data);
	}
	public function cancelled_booking_pagination(Request $request)
	{
		$location = $this->location();
		$input = $request->all();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'page' => $input['page']);
		$parms['order_status'] = 'Cancelled';
		$result = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Cancelled_Bookings'), 'data' => $result];
		return view('web.cancelled_booking_pagination', $data);
	}

	public function favourate_court()
	{
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		$result = ApiCurlMethod('court_list', $parms, 'Bearer');
		
		$auth_user = Session::get('AuthUserData');
	    $userId=$auth_user->data->id;
	
		$courtData =UserFavourates::where('user_id',$userId)->where('type','Court')->pluck('typeId')->toArray();
		$courtArrayData = $courtData;
		$favCourtData=implode(',', $courtArrayData);
		$favCourtList = array($favCourtData);
		
		
		$data = ['title' => __('backend.Favourate_court'), 'data' => $result ,'favCourtList'=>$favCourtList];
		return view('web.favourate_court', $data);
	}
	public function favourate_facility()
	{
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		$result = ApiCurlMethod('facility_list', $parms, 'Bearer');
		
		$auth_user = Session::get('AuthUserData');
	    $userId=$auth_user->data->id;
	
		$facilityData =UserFavourates::where('user_id',$userId)->where('type','Facilities')->pluck('typeId')->toArray();
		$facilityArrayData = $facilityData;
		$favFacilityData=implode(',', $facilityArrayData);
		$favFacilityList = array($favFacilityData);

		$data = ['title' => __('backend.favourate_facility'), 'data' => $result, 'favFacilityList'=>$favFacilityList];
		return view('web.favourate_facility', $data);

	}

	

	public function facility()
	{
		$search = $_GET;
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
	
		if ($search != null) {

			if (isset($search['facility']) && !empty($search['facility'])) {
				$parms['facility_id'] = $search['facility'];
			}

			if (isset($search['search']) && !empty($search['search'])) {
				$parms['search_text'] = $search['search'];
			}

			if (isset($search['category_id']) && !empty($search['category_id'])) {
				$parms['category_id'] = $search['category_id'];
			}

			if (isset($search['longitude']) && !empty($search['longitude']) && isset($search['latitude']) && !empty($search['latitude'])) {
				$location['long'] = $search['longitude'];
				$location['lat'] = $search['latitude'];
				$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'search_text' => $search['search'], 'category_id' => $search['category_id']);
				$parms['distance'] = 'asc';
			}
		}
		$result = ApiCurlMethod('facility_list', $parms, 'Bearer');
		$category_list = ApiCurlMethod('get-all-facility-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Facilities'), 'data' => $result, 'category_list' => $category_list];
		return view('web.facility_list', $data);
	}
	public function facility_pagination(Request $request)
	{
		$location = $this->location();
		$input = $request->all();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat'], 'page' => $input['page']);
		$result = ApiCurlMethod('facility_list', $parms, 'Bearer');
		$category_list = ApiCurlMethod('get-all-facility-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Facilities'), 'data' => $result, 'category_list' => $category_list];
		return view('web.facility_list_pagination', $data);
	}
	public function facility_filter(Request $request,$sort = null)
	{
		$input = $request->all();
		// dd($input);
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		if($request->date != null){
			$parms['date'] = $input['date'];
		}
		if($request->category_id != null){
			$parms['category_id'] = $input['category_id'];
		}
		if(isset($input['facility_sort']) && !empty($input['facility_sort'])){
			if($input['facility_sort'] == 'distance_asc'){
				$parms['distance'] = 'asc';
			}elseif($input['facility_sort'] == 'distance_desc'){
				$parms['distance'] = 'desc';
			}
			elseif($input['facility_sort'] == 'rating_asc'){
				$parms['rating'] = 'asc';
			}
			elseif($input['facility_sort'] == 'rating_desc'){
				$parms['rating'] = 'desc';
			}

		}
		// dd($parms);
		$result = ApiCurlMethod('facility_list', $parms, 'Bearer');
		$data = ['title' => __('backend.Facilities'), 'data' => $result];
		return view('web.facility_list_pagination', $data);
	}
	public function facility_detail($id)
	{
		$location = $this->location();
		$parms = array('facility_id' => $id, 'longitude' => $location['long'], 'latitude' => $location['lat']);
		$result = ApiCurlMethod('facility-detail', $parms, 'Bearer');
		$data = ['title' => __('backend.facility_Details'), 'data' => $result];
		return view('web.facility_detail', $data);
	}
	public function my_account(Request $request)
	{
		$parms = array();
		$result = ApiCurlMethod('user-profile', $parms, 'Bearer', 'GET');
		// update session name and image
		$userData =  Session::get('AuthUserData') ?? null;
		$userData->data->name = $result->data->name;
		$userData->data->image = $result->data->image;
		session()->put('AuthUserData', $userData);
		// update session name and image
		$data = ['title' => __('backend.My_Account'), 'data' => $result];
		return view('web.my_account', $data);
	}

	public function upcoming_booking()
	{
		$location = $this->location();
		$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
		$parms['order_status'] = 'Pending_Accepted';
		$parms['booking_type'] = 'challenge';
		$challenge = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
		$parms['booking_type'] = 'normal';
		$normal = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
		$data = ['title' => __('backend.Upcoming_Bookings'), 'challenge' => $challenge, 'normal' => $normal];
		return view('web.upcoming_booking', $data);
	}
	public function court_booking_cancel(Request $request, $id)
	{
		
		$parms = array('court_booking_id' => $id);
		$data = ApiCurlMethod('court-booking-cancel', $parms, 'Bearer');
		return response()->json($data, 200);
	}
	public function court_challenge_booking_cancel(Request $request, $id)
	{
						$booking_challenge = BookingChallenge::where('court_booking_id',$id)->orderBy('id','ASC')->first();

						$userData =  Session::get('AuthUserData') ?? null;
						$userDataId = $userData->data->id;
						$userDataname =$userData->data->name;
						
						if(isset($booking_challenge) & $booking_challenge!=''){
							if($userDataId == $booking_challenge->user_id ){//From Challenge Organiser
								

								$booking_Datas = CourtBooking::where('id',$id)->orderBy('id','DESC')->first();
								$booking_Challenge_Datas = BookingChallenge::where('court_booking_id',$id)->where('user_id',$userDataId)->orderBy('id','DESC')->first();
								
								//same cancelled entri in court booking
								$bookingData= CourtBooking::where('id',$id)->orderBy('id','DESC')->first();
								$bookingData->order_status='Cancelled';
								$bookingData->updated_at=date('Y-m-d H:i:s');
								$bookingData->update();
								$booking_id=$booking_Datas->id;
							
								//Entry in Orderbooking logs 
								$admin_cancel_charge = DeliveryPrice::where('id',1)->first();
								$cancellation_admin_charge = $admin_cancel_charge->cancellation_charge;
		                        $cancellation_joiner_charge =$admin_cancel_charge->joiner_cancellation_charge;

							$bookingChallenge = BookingChallenge::where('court_booking_id',$id)->orderBy('id','DESC')->get();
		                        if(isset($bookingChallenge) && $bookingChallenge!=''){
		                        	foreach($bookingChallenge as $bookingChallenges ){
				                        $bookingChallenge = BookingChallenge::where('court_booking_id',$id)->where('user_id',$bookingChallenges->user_id)->orderBy('id','DESC')->first();
										$bookingChallenge->challenge_status = 'Decline';
										$bookingChallenge->update();

				                        $logs= new OrderBookingAmountLogs();
				                        $logs->booking_id = $booking_id;
				                        $logs->actual_amount =$booking_Challenge_Datas->amount;
				                        $logs->amount_type ='debit';
				                        $logs->payment_type =$booking_Challenge_Datas->payment_type;
				                        $logs->admin_comm_percentage = $cancellation_admin_charge;
				                        $logs->admin_comm_amount = 0;
				                        $logs->amt_after_admin_comm_amount = 0;
				                        $logs->joiner_comm_percentage =0;
				                        $logs->amt_after_joiner_comm_amount =$booking_Challenge_Datas->amount ;
				                        $logs->action_by_id =$bookingChallenges->id;
				                        $logs->action_by_name =$userDataname;
				                        $logs->action_by ='organiser';
				                        $logs->reason ='Not available';
				                        $logs->created_at =date('Y-m-d H:i:s');
				                        $logs->save(); 
				                    }
		                    	}
		                	}else{
		                		//challenge from joiners
		                		$bookingChallenge = BookingChallenge::where('court_booking_id',$id)->where('user_id',$userDataId)->orderBy('id','DESC')->first();
		                		$booking_challenge->challenge_status = 'Decline';
								$booking_challenge->update();

								$booking_Datas = CourtBooking::where('id',$id)->orderBy('id','DESC')->first();
								$booking_Challenge_Datas = BookingChallenge::where('court_booking_id',$id)->where('user_id',$userDataId)->orderBy('id','DESC')->first();
								
								
								//Entry in Orderbooking logs 
								$admin_cancel_charge = DeliveryPrice::where('id',1)->first();
								$cancellation_admin_charge = $admin_cancel_charge->cancellation_charge;
		                        $cancellation_joiner_charge =$admin_cancel_charge->joiner_cancellation_charge;
								
								$login_user_data = Session::get('AuthUserData');
								$userIds = $login_user_data->data->id;

		                        $logs= new OrderBookingAmountLogs();
		                        $logs->booking_id = $booking_Datas->id;
		                        $logs->actual_amount =$booking_Challenge_Datas->amount;
		                        $logs->amount_type ='debit';
		                        $logs->payment_type =$booking_Challenge_Datas->payment_type;
		                        $logs->admin_comm_percentage = $cancellation_admin_charge;
		                        $logs->admin_comm_amount =(($booking_Challenge_Datas->amount*$cancellation_admin_charge)/100);
		                        $logs->amt_after_admin_comm_amount =$booking_Challenge_Datas->amount;
		                        $logs->joiner_comm_percentage =(($booking_Challenge_Datas->amount*$cancellation_joiner_charge)/100);
		                        $logs->amt_after_joiner_comm_amount =$booking_Challenge_Datas->amount -(($booking_Challenge_Datas->amount*$cancellation_joiner_charge)/100);
		                        $logs->action_by_id =$userIds;
		                        $logs->action_by_name =$userDataname;
		                        $logs->action_by ='joiner';
		                        $logs->reason ='Not available';
		                        $logs->created_at =date('Y-m-d H:i:s');
		                        $logs->save(); 
		                	}

						// send notification start
						  $locale = App::getLocale();
						  // App::SetLocale('ar');
						  $notification_title = 'notification_create_challenge_title';
						  $notification_message = 'notification_create_challenge_message';
						  if (isset($input['user_id'])) {
							  send_notification_add($input['user_id'], $user_type = 3, $notification_type = 3, $notification_for = 'create_challenge', $order_id = $booking_id, $title = $notification_title, $message = $notification_message);
							  App::SetLocale($locale);
							  $notification_title = __('api.notification_create_challenge_title');
							  $notification_message = __('api.notification_create_challenge_message');
							  send_notification(1, $input['user_id'], $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'create_challenge', 'key' => 'create_challenge'));
							 // panal notification start
							  $playerChanellId = 'pubnub_onboarding_channel_player_'.$userDataId;
							  // send_admin_notification($message = $notification_message, $title=$notification_title,$channel_name=$playerChanellId);
							  
							  $locale = App::getLocale();
							  $admin_notification_title = 'admin_notification_create_challenge_title';
							  $admin_notification_message = 'admin_notification_create_challenge_message';
							  $login_user_data = auth()->user();
							  $facility_owner_id = Courts::where('id',$booking_Datas->court_id)->first();
							  $facility_owner_id= $facility_owner_id->facility_owner_id;
							  $adminChanellId = 'pubnub_onboarding_channel_admin_1';
							  $ownerChanellId = 'pubnub_onboarding_channel_owner_'.$facility_owner_id;
							  
							  add_admin_notification($user_type = 0, $notification_type=0, $notification_for='create_challenge', $title = $admin_notification_title, $message=$admin_notification_message, $user_id = 1, $order_id = $booking_id);
							  add_admin_notification($user_type = 1, $notification_type=1, $notification_for='create_challenge', $title = $admin_notification_title, $message=$admin_notification_message, $user_id = $facility_owner_id, $order_id = $booking_id);
							  App::SetLocale($locale);
							 
							}
					}	  

					$location = $this->location();
					$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
					$parms['booking_type'] = 'challenge';
					$result = ApiCurlMethod('all-challenge-list', $parms, 'Bearer');
					$data = ['title' => __('backend.Challenges'), 'data' => $result];
					return view('web.challenges', $data);

	}
	public function location()
	{
		$latitude = ($_COOKIE && isset($_COOKIE['lat'])) ? $_COOKIE['lat'] : '25.3548';
		$longitude = ($_COOKIE && isset($_COOKIE['long'])) ? $_COOKIE['long'] : '51.1839';
		return array('lat' => $latitude, 'long' => $longitude);
	}
	public function checkBookedTimeslot(Request $request, $booking_date, $id){
		$parms = ['booking_date'=>$booking_date,'court_id'=>$id];
		$parms['timezone'] = isset($_COOKIE["timezone"]) ? $_COOKIE["timezone"] : 'Asia/Dubai' ;
		// dd($parms, $_COOKIE['timezone']);
		$booked_slot = ApiCurlMethod('court-booking-check-available', $parms, 'Bearer');
		$slotsArray = array();
		$bookingSlotData = $booked_slot->data->court_booking_avaliable;
        if (count($bookingSlotData)) {
            foreach ($bookingSlotData as $key => $value) {
                $slotsArray[] = date('H-i', strtotime($value));
            }
        }
        if (count($slotsArray)) {
            $result['status'] = true;
        } else {
            $result['status'] = false;
        }
        $result['data'] = $slotsArray;
        return response()->json($result);
	}
	public function playerList(Request $request){
		$input = $request->all();
		// dd($input);
		$parms = $input;
		$player_list = ApiCurlMethod('get-player-list', $parms, 'Bearer');
		$data = ['player_list' => $player_list, 'court_booking_id'=>$input['challenge_id']];
		return view('web.invite_player_list', $data);
	}
	public function invitePlayer(Request $request){
		$input = $request->all();
		// dd($input);
		$parms = $input;
		$result = ApiCurlMethod('invite-player', $parms, 'Bearer');
		return response()->json($result);
	}
	public function create_review(Request $request){
		$input = $request->all();
		$parms = array('type'=>'1','type_id'=>$input['court_id'],'review'=>$input['review'],'rating'=>$input['rating'],'order_id'=>$input['order_id']);
		$result = ApiCurlMethod('post-court-review', $parms, 'Bearer');
		return response()->json($result);

	}
	public function thankYou(Request $request)
	{
		
		if(isset($_GET) && isset($_GET['slug'])){
			$slug = $_GET['slug'];

			$data =explode('/',$slug);
			$slug=$data[0];
			$bookingId =$data[1];


			//Getting all data of This Booking Id.
			$location = $this->location();
			$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
			// $parms['order_status'] = 'Pending_Accepted';
			// $parms['booking_type'] = 'challenge';
			// $challenge = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
			$parms['id'] = $bookingId;
			$parms['booking_type'] = 'normal';
			$normal = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
			if(isset($normal) && $normal!=''){
				$bookingDetails = $normal->data->court_booking->data[0];
				$bookingDetailsId =$bookingDetails->id;
			}else{
				$bookingDetails ='';
				$bookingDetailsId='';
			}
			
			if($slug == 'booking_online'){
				$message = __('backend.Your_Payment_is_Successfully_Done');
			}elseif ($slug == 'booking_cash') {
				$message = __('backend.Your_booking_request_is_successfully_submitted');
			}else{
				$message = __('backend.Your_booking_request_is_successfully_submitted');
			}
		}else{
			$message = __('backend.Your_booking_request_is_successfully_submitted');
		}
		
		$data = ['title' => __('backend.Thank_You'), 'data' =>' $result', 'bookingDetailsId' => $bookingDetailsId];
		$data['message'] = $message;
		return view('web.thank_you',$data);
	}
	public function printOut(Request $request)
	{
		if(isset($request->id) && $request->id!=''){
			$bookingId =$request->id;
			
			//Getting all data of This Booking Id.
			$location = $this->location();
			$parms = array('longitude' => $location['long'], 'latitude' => $location['lat']);
			
			//Terms & Condition
			$terms_parms = array('slug' => 'terms-of-use');
			$termsresult = ApiCurlMethod('static-content', $terms_parms, 'Bearer');
			if(isset($termsresult) && $termsresult!=''){
				$termsresult =$termsresult->data;
			}else{
				$termsresult ='';
			}
		
			//Coart Booking List
			$parms['id'] = $bookingId;
			$parms['booking_type'] = 'normal';
			$normal = ApiCurlMethod('court-booking-list', $parms, 'Bearer');
			if(isset($normal) && $normal!=''){
				$bookingDetails = $normal->data->court_booking->data[0];
				$bookingDetailsId =$bookingDetails->id;
			}else{
				$bookingDetails ='';
				$bookingDetailsId='';
			}
			$message = __('backend.Your_booking_request_is_successfully_submitted');
		}else{
			$message = __('backend.Your_booking_request_is_successfully_submitted');
		}
		$data['message'] = $message;
		$data = ['title' => __('backend.Thank_You'), 'bookingDetails' => $bookingDetails,'termsresult'=>$termsresult ];
		return view('web.printout', $data);
	}
	public function readNotification($id) {
		$parms = array('notification_id'=>$id);
		$result = ApiCurlMethod('read-notification', $parms, 'Bearer');
		return response()->json($result);
    }

    public function clearAllNotification(Request $request) {
		// dd('clearAllNotification', $request->all());
    	$parms = array();
		$result = ApiCurlMethod('notification-remove', $parms, 'Bearer');
		return response()->json($result);
    }
	public function getNotificationDataPlayer() {
		$login_user_data = Session::get('AuthUserData');
		$userId = $login_user_data->data->id;
		$data = getNotificationPlayerList($userId);
		$data['notificaiton_list'] = $data['notificationData'];
		$data['notificaiton_count'] = $data['count'];
	    return view('orders.orderNotifyTopBarPlayer',$data);

    }
}
