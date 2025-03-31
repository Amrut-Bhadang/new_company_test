<?php

/****************************************************/
// Developer By @Inventcolabs.com
/****************************************************/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\User;
use App\Models\Category;
use App\Models\Products;
use App\Models\Media;
use App\Models\UsersAddress;
use App\Models\MainCategory;
use App\Models\BrandCategory;
use App\Models\Cart;
use App\Models\UserWallets;
use App\Models\Discount;
use App\Models\DiscountCategories;
use App\Models\CartParent;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\DiscountReadUsers;
use App\Models\OrderBookingAmountLogs;
use App\Models\Subcategory;
use App\Models\Tax;
use Illuminate\Support\Facades\App;
use App\Models\BookingChallenge;
use App\Models\Commission;
use App\Models\CourtBooking;
use App\Models\CourtBookingSlot;
use App\Models\CourtCategory;
use App\Models\Courts;
use App\Models\DeliveryPrice;
use App\Models\Facility;
use App\Models\FacilityAmenity;
use App\Models\FacilityCategory;
use App\Models\OnlineBookingData;
use App\Models\Review;
use App\Models\SharedChallenge;
use App\Models\Transaction;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class ApiController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth:api');
        $this->middleware('auth:api', ['except' => ['getAllCourt', 'getAllFacility', 'facilityDetail', 'courtDetail', 'courtReview', 'getAllFacilityList', 'searchFacilityAndCourt', 'getAllCourtList']]);

        define("A_TO_Z", 'a_to_z');
        define("Z_TO_A", 'z_to_a');
        $this->radius = 100;
        $this->store_id = '27115';
        $this->test_mode = '0';
    }

    /**
     * This function is use for mobile app to show discount coupons.
     *
     * @method Post
     */
    public function courtBookingCancel(Request $request)
    {
        
        $serachData = $request->all();
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'court_booking_id'   => 'required',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $user_id = JWTAuth::user()->id;
            $booking = CourtBooking::where(['id' => $input['court_booking_id'], 'user_id' => $user_id])->first();
            if (isset($booking)) {
                    if($booking->payment_type == 'online' && $booking->booking_type == 'normal'){
                        // refund payment start
                     
                        $transation = Transaction::where('trx_reference',$booking->transaction_id)->first();

                        if ($transation) {
                            $tran_ref = json_decode($transation->response, true)['order']['transaction']['ref'] ;
                            $booking_date = $booking->bookingTimeSlots[0]->booking_start_datetime;
                            $booking_time_diff = Carbon::createFromFormat('Y-m-d H:i:s', $booking_date)->diffInMinutes(\Carbon\Carbon::now());
                            $admin_cancel_charge = DeliveryPrice::where('id',1)->first();
                            if($booking_time_diff > 1440){
                                $ivp_amount = $booking->total_amount;
                            }else{
                                $cancellation_charge = 100 - $admin_cancel_charge->cancellation_charge;
                                $ivp_amount = $booking->total_amount * $cancellation_charge /100;
                            }
                            // dd($booking_time_diff, \Carbon\Carbon::now(),$booking_date ,$ivp_amount);
                            $refund_payment = $this->refundPaymentWithCurl($transation->cart_id, $ivp_amount, $tran_ref);

                            if($refund_payment['auth']['status'] == 'A'){
                                $booking_update = $booking->update(['order_status' => 'Cancelled']);

                            } else if($refund_payment['auth']['status'] == 'E') {
                                $response['status'] = false;
                                $response['message'] = $refund_payment['auth']['message'];
                                return response()->json($response, 200);

                            }else{
                                $response['status'] = false;
                                $response['message'] = __("api.something_worng");
                                return response()->json($response, 200);
                            }

                        } else {
                            $response['status'] = false;
                            $response['message'] = __("api.transation_detail_not_found");
                            return response()->json($response, 200);
                        }
                    // refund payment end
                    }elseif($booking->booking_type == 'challenge'){
                        $booking_challenge = $booking->bookingChallenges;
                        $auth_user_id = JWTAuth::user()->id;
                        
                        foreach($booking_challenge as $booking_challenge){
                            if($booking_challenge->payment_type == 'online'){
                                 // refund payment start
                                $transation = Transaction::where('trx_reference',$booking_challenge->transaction_id)->first();
                                $tran_ref = '';

                                if ($transation) {
                                    $tran_ref = json_decode($transation->response, true)['order']['transaction']['ref'] ;
                                    if($booking_challenge->user_id == $auth_user_id){
                                    $booking_date = $booking->bookingTimeSlots[0]->booking_start_datetime;
                                    $booking_time_diff = Carbon::createFromFormat('Y-m-d H:i:s', $booking_date)->diffInMinutes(\Carbon\Carbon::now());
                                    $admin_cancel_charge = DeliveryPrice::where('id',1)->first();
                                        if($booking_time_diff > 1440){
                                            $ivp_amount = $booking_challenge->amount;
                                        }else{
                                            $cancellation_charge = 100 - $admin_cancel_charge->cancellation_charge;
                                            $ivp_amount = $booking_challenge->amount * $cancellation_charge /100;
                                        }

                                    }else {
                                        $response['status'] = false;
                                        $response['message'] = __("api.transation_detail_not_found");
                                        return response()->json($response, 200);
                                    }
                                }else{
                                    $ivp_amount = $booking_challenge->amount;
                                }
                                    // dd($transation, $booking_challenge, $ivp_amount);
                                    $refund_payment = $this->refundPaymentWithCurl($transation->cart_id ?? '', $ivp_amount, $tran_ref);

                                    if($refund_payment['auth']['status'] == 'A'){
                                        $booking_update = $booking->update(['order_status' => 'Cancelled']);

                                    } else if($refund_payment['auth']['status'] == 'E') {
                                        $response['status'] = false;
                                        $response['message'] = $refund_payment['auth']['message'];
                                        return response()->json($response, 200);

                                    }else{
                                        $response['status'] = false;
                                        $response['message'] = __("api.something_worng");
                                        return response()->json($response, 200);
                                    }
                             // refund payment end
                    }else{
                                //$booking_challenge->payment_type != 'online'
                                $transation = Transaction::where('trx_reference',$booking_challenge->transaction_id)->first();
                                $tran_ref = '';

                                if ($transation) {
                                    $tran_ref = json_decode($transation->response, true)['order']['transaction']['ref'] ;
                                    if($booking_challenge->user_id == $auth_user_id){

                                    $booking_date = $booking->bookingTimeSlots[0]->booking_start_datetime;
                                    $booking_time_diff = Carbon::createFromFormat('Y-m-d H:i:s', $booking_date)->diffInMinutes(\Carbon\Carbon::now());

                                        $admin_cancel_charge = DeliveryPrice::where('id',1)->first();
                                        // if($booking_time_diff > 1440){
                                        //     $cancellation_admin_charge = 0;
                                        //     $cancellation_joiner_charge =0;

                                        //     // $login_user_data = auth()->user();
                                        //     // $userIds = $login_user_data->id;

                                        //     // $logs= new OrderBookingAmountLogs();
                                        //     // $logs->booking_id = $booking->id;
                                        //     // $logs->actual_amount =$booking_challenge->amount;
                                        //     // $logs->amount_type ='debit';
                                        //     // $logs->payment_type =$booking_challenge->payment_type;
                                        //     // $logs->admin_comm_percentage = $cancellation_admin_charge;
                                        //     // $logs->admin_comm_amount =(($booking_challenge->amount*$cancellation_admin_charge)/100);
                                        //     // $logs->amt_after_admin_comm_amount =$booking_challenge->amount -(($booking_challenge->amount*$cancellation_admin_charge)/100);
                                        //     // $logs->joiner_comm_percentage =(($booking_challenge->amount*$cancellation_joiner_charge)/100);
                                        //     // $logs->amt_after_joiner_comm_amount =$booking_challenge->amount -(($booking_challenge->amount*$cancellation_joiner_charge)/100);
                                        //     // $logs->action_by_id =$userIds;
                                        //     // $logs->action_by ='joiners';
                                        //     // $logs->reason ='Not available';
                                        //     // $logs->created_at =date('Y-m-d H:i:s');
                                        //     // $logs->save(); 


                                        // }else{
                                        //     // $cancellation_admin_charge = $admin_cancel_charge->cancellation_charge;
                                        //     // $cancellation_joiner_charge =$admin_cancel_charge->joiner_cancellation_charge;

                                        //     // $login_user_data = auth()->user();
                                        //     // $userIds = $login_user_data->id;

                                        //     // $logs= new OrderBookingAmountLogs();
                                        //     // $logs->booking_id = $booking->id;
                                        //     // $logs->actual_amount =$booking_challenge->amount;
                                        //     // $logs->amount_type ='debit';
                                        //     // $logs->payment_type =$booking_challenge->payment_type;
                                        //     // $logs->admin_comm_percentage = $cancellation_admin_charge;
                                        //     // $logs->admin_comm_amount =(($booking_challenge->amount*$cancellation_admin_charge)/100);
                                        //     // $logs->amt_after_admin_comm_amount =$booking_challenge->amount -(($booking_challenge->amount*$cancellation_admin_charge)/100);
                                        //     // $logs->joiner_comm_percentage =(($booking_challenge->amount*$cancellation_joiner_charge)/100);
                                        //     // $logs->amt_after_joiner_comm_amount =$booking_challenge->amount -(($booking_challenge->amount*$cancellation_joiner_charge)/100);
                                        //     // $logs->action_by_id =$userIds;
                                        //     // $logs->action_by ='joiners';
                                        //     // $logs->reason ='Not available';
                                        //     // $logs->created_at =date('Y-m-d H:i:s');
                                        //     // $logs->save(); 
                                        // }

                                    }else {
                                        $response['status'] = false;
                                        $response['message'] = __("api.transation_detail_not_found");
                                        return response()->json($response, 200);
                                    }
                                }else{
                                    $ivp_amount = $booking_challenge->amount;
                                }
                    }
                          
                    }
                    $booking_update = $booking->update(['order_status' => 'Cancelled']);
                }
                else{
                    $booking_update = $booking->update(['order_status' => 'Cancelled']);
                }
                    // send notification start
            if (isset($booking_update)) {
                $locale = App::getLocale();
                // App::SetLocale('ar');
                $notification_title = 'notification_booking_cancel_title';
                $notification_message = 'notification_booking_cancel_message';
                send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'booking_cancel', $order_id = $booking->id, $title = $notification_title, $message = $notification_message);
                App::SetLocale($locale);
                $notification_title = __('api.notification_booking_cancel_title');
                $notification_message = __('api.notification_booking_cancel_message');
                send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'booking_cancel', 'key' => 'booking_cancel'));
                // panal notification start
                // $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                // send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);

                $locale = App::getLocale();
                $admin_notification_title = 'admin_notification_booking_cancel_title';
                $admin_notification_message = 'admin_notification_booking_cancel_message';
                $login_user_data = auth()->user();
                $facility_owner_id = Courts::where('id', $booking->court_id)->first()->facility_owner_id ?? '';
                $adminChanellId = 'pubnub_onboarding_channel_admin_1';
                $ownerChanellId = 'pubnub_onboarding_channel_owner_' . $facility_owner_id;

                add_admin_notification($user_type = 0, $notification_type = 0, $notification_for = 'booking_cancel', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = 1, $order_id = $booking->id);
                add_admin_notification($user_type = 1, $notification_type = 1, $notification_for = 'booking_cancel', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = $facility_owner_id, $order_id = $booking->id);
                App::SetLocale($locale);
                // $admin_notification_title = __('backend.admin_notification_booking_cancel_title');
                // $admin_notification_message = __('backend.admin_notification_booking_cancel_message');
                // send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $adminChanellId);
                // send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $ownerChanellId);
                //panal notification end
            }
            // send notification end
            $response['status'] = true;
            $response['message'] = __("api.booking_cancelled_successfully");
        } else {
            $data = [];
            $response['status'] = false;
            $response['message'] = __("api.something_worng");
        }
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function postCourtReview(Request $request)
    {
        $serachData = $request->all();
        $this->code = 200;
        $input =  $request->all();
        $input['user_id'] = JWTAuth::user()->id;
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'type'   => 'required',
            'type_id'   => 'required',
            'rating'   => 'required|numeric|min:1|max:5',

        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $exist_review = Review::where(['order_id' => $input['order_id'], 'user_id' => $input['user_id']])->first();
            if (!isset($exist_review)) {
                $review = new Review();
                $review->type = $input['type'];
                $review->type_id = $input['type_id'];
                $review->review = $request->review ? $input['review'] : null;
                $review->rating = $input['rating'];
                $review->user_id = $input['user_id'];
                $review->order_id = $input['order_id'];
                $review->status = 1;
                $review->save();

                if ($review) {
                    // create avg rating in facility
                    if ($input['type'] == '0') {
                        $average_rating_facility = Review::where(['type' => 0, 'type_id' => $input['type_id']])->avg('rating');
                        $facility = Facility::where('id', $input['type_id'])->first();
                        if ($facility != null) {
                            $update_rating = $facility->update(['average_rating' => $average_rating_facility]);
                        }
                    }
                    // create avg rating in court
                    if ($input['type'] == '1') {
                        $average_rating_court = Review::where(['type' => 1, 'type_id' => $input['type_id']])->avg('rating');
                        $court = Courts::where('id', $input['type_id'])->first();
                        if ($court != null) {
                            $update_rating = $court->update(['average_rating' => $average_rating_court]);
                        }
                    }
                    $data = $review;
                    $response['status'] = true;
                    $response['message'] = __("api.review_created_successfully");
                } else {
                    $data = [];
                    $response['status'] = false;
                    $response['message'] = __("api.something_worng");
                }
            } else {
                $data = [];
                $response['status'] = false;
                $response['message'] = __("api.user_review_already_exists");
            }

            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function courtReview(Request $request)
    {
        $serachData = $request->all();
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'court_id'   => 'required',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $id = $request->court_id;
            $average_rating = Review::where(['status' => 1, 'type' => 1, 'type_id' => $id])->avg('rating');
            $total_rating = Review::where(['status' => 1, 'type' => 1, 'type_id' => $id])->count();

            if ($average_rating && $total_rating) {
                $data['average_rating'] = $average_rating;
                $data['total_rating'] = $total_rating;
                $response['status'] = true;
            } else {
                $data = [];
                $response['status'] = false;
                $response['message'] = 'No record found';
            }
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function getDiscountCode(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $query = CartParent::where(['id' => $input['parent_cart_id']])->first();
            $date = new \DateTime();
            $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime(date('Y-m-d H:i:s'));
            $dt->setTimezone($tz);
            $dateNew = $dt->format('Y-m-d H:i:s');
            $dateNewFormat = $dt->format('Y-m-d');

            if ($query) {
                $discountIds = DiscountCategories::where(['category_id' => $query->restaurant_id, 'category_type' => 'Restaurant'])->join('discount', 'discount.id', '=', 'discount_categories.discount_id')->groupBy('discount_id')->pluck('discount_id')->toArray();

                $getDiscount = Discount::where(['status' => 1, 'category_type' => 'Flat-Discount'])->orWhereIn('id', $discountIds)->where('valid_upto', '>=', $dateNewFormat)->get();
                $newDataDiscount = [];

                if ($getDiscount) {

                    foreach ($getDiscount as $key => $value) {
                        $countOrderedCoupon = Orders::where(['discount_code' => $value->discount_code])->count();

                        if ($value->no_of_use <= $countOrderedCoupon) {
                        } else {
                            $newDataDiscount[] = $value;
                        }
                    }
                    $getDiscount = $newDataDiscount;
                }
            } else {
                $getDiscount = Discount::where('status', 1)->get();
            }

            if (count($getDiscount)) {
                $response['status'] = 1;
                $response['data'] = $getDiscount;
                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['message'] = 'Discount Code Not Found.';
                return response()->json($response, 200);
            }
        }
    }

    /**
     * This function is use for mobile app to show popup for discount/info.
     *
     * @method Get
     */

    public function getOffer()
    {
        $userData = auth()->user();
        $userId =  $userData->id;
        $date = new \DateTime();
        $tz = new \DateTimeZone('Asia/Kolkata');
        $dt = new \DateTime(date('Y-m-d H:i:s'));
        $dt->setTimezone($tz);
        $dateNew = $dt->format('Y-m-d H:i:s');
        $dateNewFormat = $dt->format('Y-m-d');

        $getReadDiscounts = DiscountReadUsers::where('user_id', $userId)->pluck('discount_id')->toArray();

        /*$countOrderedCoupon = Orders::where(['discount_code'=>$data['discount_code']])->count();

        if ($data->no_of_use <= $countOrderedCoupon) {
            $fail = true;
            $response['status'] = 0;
            $response['message'] = 'This discount coupon is no longer available.';
        }*/
        $getDiscount = Discount::where('status', 1)->where('valid_upto', '>=', $dateNewFormat)->whereNotIn('id', $getReadDiscounts)->get();

        if ($getDiscount) {
            $couponDataNew = '';

            foreach ($getDiscount as $key => $value) {

                if ($value->category_type == 'Info') {
                    $couponDataNew = $value;

                    $data = new DiscountReadUsers();
                    $data->discount_id = $value->id;
                    $data->user_id = $userId;
                    $data->save();
                    break;
                } else {

                    if ($value->no_of_use > $value->applied_user) {
                        $couponDataNew = $value;

                        $data = new DiscountReadUsers();
                        $data->discount_id = $value->id;
                        $data->user_id = $userId;
                        $data->save();
                        break;
                    }
                }
            }

            if ($couponDataNew) {
                $response['status'] = 1;
                $response['data'] = $couponDataNew;
                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['message'] = 'Offer Not Found.';
                return response()->json($response, 200);
            }
        } else {
            $response['status'] = 0;
            $response['message'] = 'Offer Not Found.';
            return response()->json($response, 200);
        }
    }

    public function getRandomCode()
    {
        $userData = auth()->user();
        $userId =  $userData->id;
        $date = new \DateTime();
        $tz = new \DateTimeZone('Asia/Kolkata');
        $dt = new \DateTime(date('Y-m-d H:i:s'));
        $dt->setTimezone($tz);
        $dateNew = $dt->format('Y-m-d H:i:s');
        $dateNewFormat = $dt->format('Y-m-d');
        $getDiscount = Discount::where(['status' => 1])->where('category_type', '!=', 'Info')->where('valid_upto', '>=', $dateNewFormat)->inRandomOrder()->first();

        if ($getDiscount) {
            $response['status'] = 1;
            $response['data'] = $getDiscount;
            return response()->json($response, 200);
        } else {
            $response['status'] = 0;
            $response['message'] = 'Offer Not Found.';
            return response()->json($response, 200);
        }
    }
    public function bookCourt(Request $request)
    {


        // dd('book court', $request->all());
        $jsonData = new CronJob();
        $jsonData->value = json_encode($request->header());
        $jsonData->save();

        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
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
            'total_amount' => 'required',
            'booking_type' => 'required',
            'booking_date' => 'required|date_format:Y-m-d',
            'end_booking_date' => 'date_format:Y-m-d',

        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $facility_data = Facility::select('users.show_post_method')->where(['facilities.id' => $input['facility_id']])->join('users', 'users.id', 'facilities.facility_owner_id')->first();
            $court = Courts::where('id', $input['court_id'])->first();
            $input['user_id'] = JWTAuth::user()->id;
            $input['hourly_price'] = $court->hourly_price;
            $input['minimum_hour_book'] = $court->minimum_hour_book;
            // if end_booking_date input null
            if ($request->end_booking_date == null) {
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
            // dd($booking_start_time, $input['booking_time_slot'][0]['start_time'], $input);
            // $booking = CourtBooking::where(['court_id' => $input['court_id'], 'booking_date' => $input['booking_date']])->get();
            $booking = CourtBookingSlot::join('court_booking', 'court_booking.id', 'court_booking_slots.court_booking_id')
                ->select('court_booking.id', 'court_booking.court_id', 'court_booking.order_status', 'court_booking_slots.court_booking_id', 'court_booking_slots.booking_date', 'court_booking_slots.booking_start_time')
                ->where(['court_booking.court_id' => $input['court_id'], 'court_booking.order_status' => 'Pending', 'court_booking.booking_date' => $input['booking_date']])
                ->whereIn('court_booking_slots.booking_start_time', $booking_start_time)
                ->get();
            // dd($booking);
            if ($input['payment_type'] == 'online') {
                $input['payment_received_status'] = 'Received';
            }
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
                $court_booking->transaction_id = $request->transaction_id ? $input['transaction_id'] : '';
                $court_booking->payment_type = $request->payment_type ? $input['payment_type'] : '';
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
                    if ($input['booking_type'] == 'challenge') {
                        $booking_challenge = new BookingChallenge();
                        $booking_challenge->court_booking_id = $court_booking->id;
                        $booking_challenge->user_id = $input['user_id'];
                        $booking_challenge->amount = $input['amount'];
                        $booking_challenge->challenge_status = 'Accepted';
                        $booking_challenge->transaction_id = $request->transaction_id ? $input['transaction_id'] : '';
                        $booking_challenge->payment_type = $request->payment_type ? $input['payment_type'] : '';
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
                            $playerChanellId = 'pubnub_onboarding_channel_player_' . $input['user_id'];
                            send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);

                            $locale = App::getLocale();
                            $admin_notification_title = 'admin_notification_create_challenge_title';
                            $admin_notification_message = 'admin_notification_create_challenge_message';
                            $login_user_data = auth()->user();
                            $facility_owner_id = Courts::where('id', $input['court_id'])->first()->facility_owner_id ?? '';
                            $adminChanellId = 'pubnub_onboarding_channel_admin_1';
                            $ownerChanellId = 'pubnub_onboarding_channel_owner_' . $facility_owner_id;

                            add_admin_notification($user_type = 0, $notification_type = 0, $notification_for = 'create_challenge', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = 1, $order_id = $court_booking->id);
                            add_admin_notification($user_type = 1, $notification_type = 1, $notification_for = 'create_challenge', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = $facility_owner_id, $order_id = $court_booking->id);
                            App::SetLocale($locale);
                            $admin_notification_title = __('backend.admin_notification_create_challenge_message');
                            $admin_notification_message = __('backend.admin_notification_create_challenge_message');
                            send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $adminChanellId);
                            send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $ownerChanellId);
                            //panal notification end

                        }
                        // send notification end
                    } else {
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
                        $playerChanellId = 'pubnub_onboarding_channel_player_' . $input['user_id'];
                        send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);

                        $locale = App::getLocale();
                        $admin_notification_title = 'admin_notification_book_court_title';
                        $admin_notification_message = 'admin_notification_book_court_message';
                        $login_user_data = auth()->user();
                        $facility_owner_id = Courts::where('id', $input['court_id'])->first()->facility_owner_id ?? '';
                        $adminChanellId = 'pubnub_onboarding_channel_admin_1';
                        $ownerChanellId = 'pubnub_onboarding_channel_owner_' . $facility_owner_id;

                        add_admin_notification($user_type = 0, $notification_type = 0, $notification_for = 'book_court', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = 1, $order_id = $court_booking->id);
                        add_admin_notification($user_type = 1, $notification_type = 1, $notification_for = 'book_court', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = $facility_owner_id, $order_id = $court_booking->id);
                        App::SetLocale($locale);
                        $admin_notification_title = __('backend.admin_notification_book_court_message');
                        $admin_notification_message = __('backend.admin_notification_book_court_message');
                        send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $adminChanellId);
                        send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $ownerChanellId);
                        //panal notification end
                        // send notification end
                    }
                    // send response
                    $response['status'] = true;
                    $response['message'] = __("api.court_booking_successfully");
                    $response['data'] = $court_booking;
                    $response['facility_data'] = $facility_data;
                    return response()->json($response, 200);
                } else {
                    $response['status'] = false;
                    $response['message'] = __("api.something_worng");
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = false;
                $response['message'] = __("api.court_already_booked");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }
    public function invitePlayer(Request $request)
    {
        // dd('invite Player', $request->all());
        $locale = App::getLocale();
        $serachData = $request->all();
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'court_booking_id.required' => __("api.court_booking_id_required"),
            'court_booking_id.exists' => __("api.court_booking_id_exists"),
            'mobile.required' => __("api.mobile_required"),
            'country_code.required' => __("api.country_code_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
        ];
        $validator = Validator::make($input, [
            'court_booking_id'   => 'required',
            'country_code'   => 'required',
            'mobile' => 'required|min:7|max:15',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $from_id = JWTAuth::user()->id;
            $user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile'], 'type' => 3])->first();
            if (isset($user)) {
                $shared_challenge = new SharedChallenge();
                $shared_challenge->court_booking_id = $input['court_booking_id'];
                $shared_challenge->from_id =  $from_id;
                $shared_challenge->to_id = $user->id;
                $shared_challenge->save();
                // send notification start
                $locale = App::getLocale();
                // App::SetLocale('ar');
                $notification_title = 'notification_invite_player_title';
                $notification_message = 'notification_invite_player_message';
                if (isset($shared_challenge)) {
                    send_notification_add($user->id, $user_type = 3, $notification_type = 3, $notification_for = 'invite_player', $order_id = $input['court_booking_id'], $title = $notification_title, $message = $notification_message);
                    App::SetLocale($locale);
                    $notification_title = __('api.notification_invite_player_title');
                    $notification_message = __('api.notification_invite_player_message').'challenges_detail/'.$input['court_booking_id'];
                    send_notification(1, $user->id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'create_challenge', 'key' => 'create_challenge'));
                    // panal notification start
                    $playerChanellId = 'pubnub_onboarding_channel_player_' . $user->id;
                    send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);
                    // panal notification end
                }

                // send notification end
                $response['status'] = true;
                $response['data'] = $shared_challenge;
                $response['message'] = __("api.shared_challenge_successfully");
                $response['send_msg'] = $notification_message;
                return response()->json($response, 200);
            } else {
                $message = __('api.invite_player_text_message').'/challenges_detail/'.$input['court_booking_id'];
                /*Send SMSCountry*/
                if ($locale == 'en') {
                    $langType = 'N';
                } else {
                    $langType = 'LNG';
                }
                $username = 'Tahadiyaat';
                $sender_id = 'TAHADIYATAE';
                $password = 'Tahadiyaat01$';

                $curlUrlNew = "http://api.smscountry.com/SMSCwebservice_bulk.aspx?mobilenumber=" . $input['country_code'] . $input['mobile'] . "&message=" . urlencode($message) . "&User=" . $username . "&passwd=" . $password . "&sid=" . $sender_id . "&mtype=" . $langType . "&DR=Y";
                // dd($curlUrlNew);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $curlUrlNew,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "postman-token: 7e81e559-a81b-d18c-a629-908156cda911"
                    ),
                ));
                $curlResponse = curl_exec($curl);
                $err = curl_error($curl);
                // echo "<pre>";print_r($curlResponse);die;
                curl_close($curl);
                /*Send SMSCountry*/
                $response['status']   = true;
                $response['message'] = __("api.message_sent");
                $response['send_msg'] = $message;
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }
    public function joinChallenge(Request $request)
    {
        // dd('join Challenge',$request->all());
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            // 'court_booking_id.required' => __("api.court_booking_id_required"),
            // 'court_booking_id.exists' => __("api.court_booking_id_exists"),
        ];
        $validator = Validator::make($input, [
            'court_booking_id'   => 'required',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $input['user_id'] = JWTAuth::user()->id;
            $check_join = BookingChallenge::where(['court_booking_id' => $input['court_booking_id'], 'challenge_status' => 'Accepted'])->get();
            $check_join_with_user = BookingChallenge::where(['court_booking_id' => $input['court_booking_id'], 'user_id' => $input['user_id'], 'challenge_status' => 'Accepted'])->first();
            // dd($check_join, $check_join_with_user);
            if (!isset($check_join_with_user)) {
                if (count($check_join) < 2) {
                    // create challenge
                    $booking_challenge = new BookingChallenge();
                    $booking_challenge->court_booking_id = $input['court_booking_id'];
                    $booking_challenge->user_id = $input['user_id'];
                    $booking_challenge->amount = $input['amount'];
                    $booking_challenge->challenge_status = 'Accepted';
                    $booking_challenge->transaction_id = $request->transaction_id ? $input['transaction_id'] : '';
                    $booking_challenge->payment_type = $request->payment_type ? $input['payment_type'] : '';
                    $booking_challenge->status = 1;
                    $booking_challenge->save();
                    // end challenge

                    if ($booking_challenge) {
                        if ($booking_challenge->payment_type == 'online') {
                            CourtBooking::where('id', $booking_challenge->court_booking_id)->update(['joiner_payment_status' => 'Received']);
                        }
                        // send notification end
                        $user_id = $input['user_id'];
                        $locale = App::getLocale();
                        // App::SetLocale('ar');
                        $notification_title = 'notification_join_challenge_title';
                        $notification_message = 'notification_join_challenge_message';
                        send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'join_challenge', $order_id = $input['court_booking_id'], $title = $notification_title, $message = $notification_message);
                        App::SetLocale($locale);
                        $notification_title = __('api.notification_join_challenge_title');
                        $notification_message = __('api.notification_join_challenge_message');
                        send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'join_challenge', 'key' => 'join_challenge'));
                        // panal notification start
                        $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                        send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);
                        // panal notification end
                        // send notification end
                        // send notification start
                        $booking = CourtBooking::where(['id' => $input['court_booking_id'], 'booking_type' => 'challenge'])->first();
                        if (isset($booking)) {
                            $user_id =  $booking->user_id;
                            $locale = App::getLocale();
                            // App::SetLocale('ar');
                            $notification_title = 'notification_accepted_challenge_title';
                            $notification_message = 'notification_accepted_challenge_message';
                            send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'accepted_challenge', $order_id = $input['court_booking_id'], $title = $notification_title, $message = $notification_message, $lang = 'ar');
                            App::SetLocale($locale);
                            $notification_title = __('api.notification_accepted_challenge_title');
                            $notification_message = __('api.notification_accepted_challenge_message');
                            send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'accepted_challenge', 'key' => 'accepted_challenge'));
                            // panal notification start
                            $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                            send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);
                            // panal notification end
                        }
                        // send notification end
                        // send response
                        $response['status'] = true;
                        $response['message'] = __("api.join_challenge_successfully");
                        $response['data'] = $booking_challenge;
                        return response()->json($response, 200);
                    } else {
                        $response['status'] = false;
                        $response['message'] = __("api.something_worng");
                        return response()->json($response, 200);
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = __("api.booking_full");
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = false;
                $response['message'] = __("api.challenge_already_joined");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }

    public function CourtBookingCheckAvaliable(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'booking_date'   => 'required',
            'court_id'   => 'required',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            // add time slot key
            $getCourt = Courts::findorfail($input['court_id']);
            // dd($getCourt);
            $start_time = date('Y-m-d') . ' ' . $getCourt->start_time;
            $end_time = date('Y-m-d') . ' ' . $getCourt->end_time;
            $interval = (int)$getCourt->timeslot;

            $ReturnArray = array(); // Define output
            $StartTime    = strtotime($start_time); //Get Timestamp
            $EndTime      = strtotime($end_time); //Get Timestamp

            $AddMins  = $interval * 60;
            $i = 0;

            while ($StartTime < $EndTime) //Run loop
            {
                $diff = (int)abs($EndTime - $StartTime) / 60;
                if ($diff >= $interval) {
                    $ReturnArray[] = date("H:i:s", $StartTime);
                    $StartTime += $AddMins; //Endtime check
                    $i++;
                } else {
                    break;
                }
            }
            $totaltimeslots = $ReturnArray;
            // dd($totaltimeslot);
            // end time slot key
            $oldtime = [];
            $timeZone = $input['timezone'] ?? 'Asia/Dubai';
            $current_time = Carbon::now()->setTimeZone($timeZone)->format('H:i:s');
            if(Carbon::now()->setTimeZone($timeZone)->format('Y-m-d') >= $input['booking_date']){
                foreach ($totaltimeslots as $totaltimeslot) {
                    if ($current_time >= $totaltimeslot) {
                        $oldtime[] = $totaltimeslot;
                    } 
                }
             }
            // dd($oldtime,'ddddddddd');
            $getCourtBookingAvaliables = CourtBooking::where(['court_id' => $input['court_id']])
                ->whereDate('court_booking_slots.booking_start_datetime', '<=', date('Y-m-d', strtotime($input['booking_date'])))
                ->whereDate('court_booking_slots.booking_end_datetime', '>=', date('Y-m-d', strtotime($input['booking_date'])))
                ->whereNotIn('order_status', ['Completed', 'Cancelled'])
                ->join('court_booking_slots', 'court_booking_slots.court_booking_id', 'court_booking.id')
                ->pluck('booking_start_time')->toArray();
                if(count($oldtime)){
                //    $getCourtBookingAvaliable = array_unique(array_merge($getCourtBookingAvaliables, $oldtime)) ;
                   $getCourtBookingAvaliable =  array_unique (array_merge ($getCourtBookingAvaliables, $oldtime));
                //    dd($getCourtBookingAvaliable,'dd');
                }else{
                    $getCourtBookingAvaliable = $getCourtBookingAvaliables;
                }
                // dd($getCourtBookingAvaliables,'ddddddddd', $totaltimeslots,$current_time,$oldtime);
            if (count($getCourtBookingAvaliable)) {
                foreach ($getCourtBookingAvaliable as $value) {
                    $getCourtBookingAvaliables[] = date('H:i', strtotime($value));
                }
                $data['court_booking_avaliable'] = $getCourtBookingAvaliables;
                $response['status'] = true;
            } else {
                $data['court_booking_avaliable'] = [];
                $response['status'] = false;
                $response['message'] = 'No record found';
            }
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    /**
     * This function is use for mobile app to show old dashboard data.
     *
     * @method Post
     */
    public function getAllCourtBooking(Request $request)
    {
        $serachData = $request->all();
        $limit = 10;
        $is_pagination = 1;
        // $radius = 10;
        $getCourtBooking = $this->getCourtBooking($serachData, $limit, $is_pagination);
        if (count($getCourtBooking)) {
            foreach ($getCourtBooking as $key => $val) {
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $serachData['latitude'] . ',' . $serachData['longitude'] . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $val->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $val->distance = '';
                    }
                }
                $booking_time_slots = $val->bookingTimeSlots;

                if (count($booking_time_slots)) {
                    $val->booking_start_time = $booking_time_slots[0]->booking_start_time;
                    $val->booking_end_time = $booking_time_slots[count($booking_time_slots) - 1]->booking_end_time;
                }
                $val->user_name = $val->userDetails->name;
                $val->user_image = $val->userDetails->image;
            }

            $data['court_booking'] = $getCourtBooking;
            $response['status'] = true;
        } else {
            $data['court_booking'] = [];
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }
    public function CourtBookingDetail(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'court_booking_id'   => 'required',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $serachData = $request->all();
            $id = $request->court_booking_id;
            $user_id = JWTAuth::user()->id;

            $getCourtBooking =  CourtBooking::with('bookingChallenges.userDetails')
                ->join('courts', 'courts.id', 'court_booking.court_id')
                ->select(
                    'court_booking.*',
                    'courts.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                )->where(['court_booking.id' => $id, 'court_booking.status' => 1]);
            if (array_key_exists("booking_type", $serachData)) {
                $booking_type = $serachData['booking_type'];
                $getCourtBooking =  $getCourtBooking->where('court_booking.booking_type', $booking_type)->first();
            } else {
                $getCourtBooking =  $getCourtBooking->first();
            }
            if ($getCourtBooking) {
                $data = $getCourtBooking;
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $data->latitude . ',' . $data->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $data->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $data->distance = '';
                    }
                }
                $booking_time_slots = $data->bookingTimeSlots;
                // dd( $booking_time_slots);

                if (count($booking_time_slots)) {
                    $data['booking_start_time'] = $booking_time_slots[0]->booking_start_time;
                    $data['booking_end_time'] = $booking_time_slots[count($booking_time_slots) - 1]->booking_end_time;
                }
                $data['user_name'] = $data->userDetails->name;
                $data['user_image'] = $data->userDetails->image;
                $auth_user_id = JWTAuth::user()->id;

                $check_join_challenge = BookingChallenge::where(['court_booking_id' => $id, 'user_id' => $auth_user_id])->get();
                $check_join_count = BookingChallenge::where(['court_booking_id' => $id])->count();
                if (count($check_join_challenge)) {
                    $data['is_challenge'] = true;
                } else {
                    if ($check_join_count == 2) {
                        $data['is_challenge'] = true;
                    } else {
                        $data['is_challenge'] = false;
                    }
                }




                $response['status'] = true;
            } else {
                $data = [];
                $response['status'] = false;
                $response['message'] = 'No record found';
            }
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function getAllJoinChallenge(Request $request)
    {

        $serachData = $request->all();
        $limit = 10;
        $is_pagination = 1;
        // $radius = 10;
        $getJoinChallenge = $this->getJoinChallenge($serachData, $limit, $is_pagination);
        if (count($getJoinChallenge)) {
            foreach ($getJoinChallenge as $key => $val) {
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $serachData['latitude'] . ',' . $serachData['longitude'] . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $val->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $val->distance = '';
                    }
                }
                $booking_time_slots = $val->bookingTimeSlots;

                if (count($booking_time_slots)) {
                    $val->booking_start_time = $booking_time_slots[0]->booking_start_time;
                    $val->booking_end_time = $booking_time_slots[count($booking_time_slots) - 1]->booking_end_time;
                }

                $val->user_name = $val->userDetails->name;
                $val->user_image = $val->userDetails->image;
                $auth_user_id = JWTAuth::user()->id;
                $check_join_challenge = BookingChallenge::where(['court_booking_id' => $val->id, 'user_id' => $auth_user_id])->get();
                $check_join_count = BookingChallenge::where(['court_booking_id' => $val->id])->count();
                if (count($check_join_challenge)) {
                    $val->is_challenge = true;
                } else {
                    if ($check_join_count == 2) {
                        $val->is_challenge = true;
                    } else {
                        $val->is_challenge = false;
                    }
                }
            }
            $data['Join_challenge'] = $getJoinChallenge;
            $response['status'] = true;
        } else {
            $data['Join_challenge'] = [];
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function getJoinChallenge($serachData, $limit, $is_pagination)
    {
        $locale = App::getLocale();
        if ($locale == null) {
            $locale = 'en';
        }
        $user_id = JWTAuth::user()->id;
        // short data by booking_type_short 
        $court_booking_public_id = CourtBooking::where('challenge_type', 'public')->where('court_booking.order_status', 'Pending')->pluck('id')->toArray();
        $court_booking_private_id = CourtBooking::where('challenge_type', 'private')->where('court_booking.order_status', 'Pending')->whereHas('Shared_challenges', function ($q) use ($user_id) {
            $q->where('to_id', $user_id);
        })->pluck('id')->toArray();
        $court_booking_self_id = CourtBooking::where('user_id', $user_id)->where('court_booking.order_status', 'Pending')->pluck('id')->toArray();

        $court_booking_id = array_unique(array_merge($court_booking_public_id, $court_booking_private_id, $court_booking_self_id));
        if (array_key_exists("booking_type_short", $serachData)) {
            $booking_type_short = $serachData['booking_type_short'];
            $JoinChallengeList = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
                ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
                ->leftjoin('facilities', 'facilities.id', 'court_booking.facility_id')
                ->leftjoin('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
                ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->select(
                    'court_booking.*',
                    'courts_lang.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                    'facilities_lang.name as facility_name',
                    'users.mobile','users.country_code',
                )->whereIn('court_booking.id', $court_booking_id)
                ->where('courts_lang.lang', $locale)
                ->where('facilities_lang.lang', $locale);
            if ($booking_type_short == 'asc') {
                $JoinChallengeList = $JoinChallengeList->orderBy('court_booking.booking_type', 'asc');
            }
            if ($booking_type_short == 'desc') {
                $JoinChallengeList = $JoinChallengeList->orderBy('court_booking.booking_type', 'desc');
            }
        } else {
            $JoinChallengeList = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
                ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
                ->leftjoin('facilities', 'facilities.id', 'court_booking.facility_id')
                ->leftjoin('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
                ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->select(
                    'court_booking.*',
                    'courts_lang.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                    'facilities_lang.name as facility_name',
                    'users.mobile','users.country_code',
                )->whereIn('court_booking.id', $court_booking_id)
                ->where('courts_lang.lang', $locale)
                ->where('facilities_lang.lang', $locale);
        }

        if (array_key_exists("court_id", $serachData)) {
            $court_id = $serachData['court_id'];
            $JoinChallengeList->where('court_id', $court_id);
        }
        if (array_key_exists("booking_type", $serachData)) {
            $booking_type = $serachData['booking_type'];
            $JoinChallengeList->where('booking_type', $booking_type);
        }
        if (array_key_exists("order_status", $serachData)) {
            $order_status = $serachData['order_status'];
            $JoinChallengeList->where('order_status', $order_status);
        }

        if (array_key_exists("search_text", $serachData)) {
            $search = $serachData['search_text'];
            $JoinChallengeList->Where("court_name", 'like', '%' . $search . '%');
        }

        if ($limit != 'All') {
            if ($is_pagination == 1) {
                $JoinChallengeDetail = $JoinChallengeList->paginate($limit);
            } else {
                $JoinChallengeDetail = $JoinChallengeList->limit($limit);
                $JoinChallengeDetail = $JoinChallengeList->get();
            }
        } else {
            $JoinChallengeDetail = $JoinChallengeList->get();
        }
        return $JoinChallengeDetail;
    }
    public function getCourtBooking($serachData, $limit, $is_pagination)
    {
        $locale = App::getLocale();
        if ($locale == null) {
            $locale = 'en';
        }
        // dd($locale);
        $user_id = JWTAuth::user()->id;
        $getJoinBookingChallenge = BookingChallenge::where('user_id', $user_id)->groupBy('court_booking_id')->pluck('court_booking_id')->toArray();
        $userBookingCourts = CourtBooking::where('user_id', $user_id)->pluck('id')->toArray();
        $bookingIds = array_unique(array_merge($getJoinBookingChallenge, $userBookingCourts));
        // dd($bookingIds);
        // dd($getJoinBookingChallenge); 
        // short data by booking_type_short 
        if (array_key_exists("booking_type_short", $serachData)) {
            $booking_type_short = $serachData['booking_type_short'];
            $CourtBookingList = CourtBooking::with('bookingChallenges.userDetails')
                ->Join('courts', 'courts.id', 'court_booking.court_id')
                ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
                ->leftjoin('facilities', 'facilities.id', 'court_booking.facility_id')
                ->leftjoin('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
                ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->select(
                    'court_booking.*',
                    'courts_lang.id as court_lang_id',
                    'courts_lang.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                    'facilities_lang.name as facility_name',
                    'users.mobile','users.country_code',
                )->where(function ($query) use ($bookingIds, $locale) {
                    $query->whereIn('court_booking.id', $bookingIds);
                    $query->where('courts_lang.lang', $locale);
                    $query->where('facilities_lang.lang', $locale);
                })->with(array('bookingChallenges' => function ($query) use ($user_id) {
                    $query->where('booking_challenges.user_id', '!=', $user_id);
                }));

            if ($booking_type_short == 'asc') {
                $CourtBookingList = $CourtBookingList->orderBy('court_booking.booking_type', 'asc');
            }
            if ($booking_type_short == 'desc') {
                $CourtBookingList = $CourtBookingList->orderBy('court_booking.booking_type', 'desc');
            }
        } else {
            $CourtBookingList = CourtBooking::with('bookingChallenges.userDetails')
            ->leftjoin('facilities', 'facilities.id', 'court_booking.facility_id')
            ->leftjoin('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
            ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->Select(
                    'court_booking.*',
                    'courts_lang.id as court_lang_id',
                    'courts_lang.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                    'facilities_lang.name as facility_name',
                    'users.mobile','users.country_code',
                )->where(function ($query) use ($bookingIds, $locale) {
                    $query->whereIn('court_booking.id', $bookingIds);
                    $query->where('courts_lang.lang', $locale);
                    $query->where('facilities_lang.lang', $locale);
                })->orderBy('court_booking.id', 'desc')
                ->join('courts', 'courts.id', 'court_booking.court_id')
                ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
                ->with(array('bookingChallenges' => function ($query) use ($user_id) {
                    $query->where('booking_challenges.user_id', '!=', $user_id);
                }));
        }

        // if ($getJoinBookingChallenge) {
        //     $CourtBookingList = $CourtBookingList->orWhereIn('court_booking.id', $getJoinBookingChallenge);
        // }
        // dd($CourtBookingList->get());
        if (array_key_exists("court_id", $serachData)) {
            $court_id = $serachData['court_id'];
            $CourtBookingList->where('court_id', $court_id);
        }
        if (array_key_exists("booking_type", $serachData)) {
            $booking_type = $serachData['booking_type'];
            $CourtBookingList->where('booking_type', $booking_type);
        }
        if (array_key_exists("order_status", $serachData)) {
            $order_status = $serachData['order_status'];
            // dd($order_status, 'dd');
            if ($order_status == 'Cancel_Completed') {
                $CourtBookingList->whereIn('order_status', ['Completed', 'Cancelled']);
            } elseif ($order_status == 'Pending_Accepted') {
                $CourtBookingList->whereIn('order_status', ['Pending', 'Accepted']);
            } else {
                $CourtBookingList->where('order_status', $order_status);
                // $CourtBookingList->orWhere('order_status', 'Accepted');
                // $CourtBookingList->whereIn('order_status', ['Pending', 'Accepted']);
            }
        }

        if (array_key_exists("search_text", $serachData)) {
            $search = $serachData['search_text'];
            $CourtBookingList->Where("court_name", 'like', '%' . $search . '%');
        }
        if ($limit != 'All') {
            if ($is_pagination == 1) {
                $CourtBookingDetail = $CourtBookingList->paginate($limit);
            } else {
                $CourtBookingDetail = $CourtBookingList->limit($limit);
                $CourtBookingDetail = $CourtBookingList->get();
            }
        } else {
            $CourtBookingDetail = $CourtBookingList->get();
        }
        return $CourtBookingDetail;
    }

    public function searchHomeData(Request $request)
    {
        $serachData = $request->all();
        $limit = 10;
        $is_pagination = 1;
        // $radius = 10;
        $getCourt = $this->getCourt($serachData, $limit, $is_pagination);

        if (count($getCourt)) {
            foreach ($getCourt as $key => $val) {
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $serachData['latitude'] . ',' . $serachData['longitude'] . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $val->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $val->distance = '';
                    }
                }
                $val->category_image = CourtCategory::where(['status' => 1, 'id' => $val->category_id])->first()->image;
            }
            $data['court'] = $getCourt;
        } else {
            $data['court'] = [];
        }

        $getFacility = $this->getFacility($serachData, $limit, $is_pagination);

        if (count($getFacility)) {

            foreach ($getFacility as $key => $val) {
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $serachData['latitude'] . ',' . $serachData['longitude'] . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $val->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $val->distance = '';
                    }
                }
                $val->available_court = Facility::join('courts', 'facilities.id', 'courts.facility_id')
                    ->where('facilities.id', $val->id)
                    ->where('courts.status', 1)
                    ->count();
                $val->available_category = FacilityCategory::join('facilities', 'facilities.id', 'facility_categories.facility_id')
                    ->join('court_categories', 'court_categories.id', 'facility_categories.category_id')
                    ->where('facility_categories.facility_id', $val->id)
                    ->select('court_categories.id', 'court_categories.name', 'court_categories.image')->get();
            }
            $data['facility'] = $getFacility;
        } else {
            $data['facility'] = [];
        }
        $response['data'] = $data;

        if (count($data['facility']) || count($data['court'])) {
            $response['status'] = true;
            $response['message'] = 'Record found';
        } else {
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        return response()->json($response, 200);
    }
    public function getAllCourt(Request $request)
    {

        $serachData = $request->all();
        $limit = 10;
        $is_pagination = 1;
        // $radius = 10;
        $getCourt = $this->getCourt($serachData, $limit, $is_pagination);

        if (count($getCourt)) {
            foreach ($getCourt as $key => $val) {
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $serachData['latitude'] . ',' . $serachData['longitude'] . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $val->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $val->distance = '';
                    }
                }
                $val->category_image = CourtCategory::where(['status' => 1, 'id' => $val->category_id])->first()->image ?? '';
            }
            $data['court'] = $getCourt;
            $response['status'] = true;
        } else {
            $data['court'] = [];
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }
    public function userCourtFavourate(Request $request)
    {
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'court_id'   => 'required',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $courtId = $request->court_id;
            $userId = $request->user_id;
            $getCourt = UserCourtFavourate::where('court_id',$courtId)->where('user_id',$userId)->get();
            if ($getCourt) {
                $data = $getCourt;
                $data['category_image'] = CourtCategory::where(['status' => 1, 'id' => $data->category_id])->first()->image;
            } else {
                $data = [];
                $response['status'] = false;
                $response['message'] = 'No record found';
            }
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }

    public function courtDetail(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'court_id'   => 'required',
        ]);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $id = $request->court_id;
            // $getCourt = Courts::with(array('facilityDetails.facilityAmenities' => function ($query) {
            //     $query->select('id', 'name');
            // }))
            //     ->where(['id' => $id, 'status' => 1])
            //     ->select('courts.*')
            //     ->first();
            $getCourt = Courts::leftjoin('facilities', 'facilities.id', "=", 'courts.facility_id')
            ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
            ->with('facilityDetails.facilityAmenities.amenityDetails', 'facilityDetails.facilityRules')
                ->where(['courts.id' => $id, 'courts.status' => 1])
                ->select('courts.*','facilities.name as facility_name','users.mobile','users.country_code','users.show_post_method')
                ->first();

            if ($getCourt) {
                $data = $getCourt;
                $data['category_image'] = CourtCategory::where(['status' => 1, 'id' => $data->category_id])->first()->image;
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $data->latitude . ',' . $data->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $data->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $data->distance = '';
                    }
                }
                $data['available_court'] = Courts::where(['status' => 1, 'facility_id' => $data->facility_id])->select('id', 'court_name')->get()->makeHidden(['average_rating', 'total_rating']);
                // add time slot key
                $start_time = date('Y-m-d') . ' ' . $getCourt->start_time;
                $end_time = date('Y-m-d') . ' ' . $getCourt->end_time;
                $interval = (int)$getCourt->timeslot;

                $ReturnArray = array(); // Define output
                $StartTime    = strtotime($start_time); //Get Timestamp
                $EndTime      = strtotime($end_time); //Get Timestamp

                $AddMins  = $interval * 60;
                $i = 0;

                while ($StartTime < $EndTime) //Run loop
                {
                    $diff = (int)abs($EndTime - $StartTime) / 60;
                    if ($diff >= $interval) {
                        // echo $diff."<br/>";
                        // dd($diff,$interval);
                        // $ReturnArray[$i]['start_time'] = date("H:i", $StartTime);
                        $ReturnArray[] = date("H:i", $StartTime);
                        $StartTime += $AddMins; //Endtime check
                        // $ReturnArray[$i]['end_time'] = date("H:i", $StartTime);
                        $i++;
                    } else {
                        break;
                    }
                }
                $data['selecttimeslot'] = $ReturnArray;

                // end time slot key

                $response['status'] = true;
            } else {
                $data = [];
                $response['status'] = false;
                $response['message'] = 'No record found';
            }
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function getAllFacility(Request $request)
    {
        $serachData = $request->all();
        $limit = 10;
        $is_pagination = 1;
        $getFacility = $this->getFacility($serachData, $limit, $is_pagination);

        if (count($getFacility)) {

            foreach ($getFacility as $key => $val) {
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $serachData['latitude'] . ',' . $serachData['longitude'] . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $val->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $val->distance = '';
                    }
                }
                $val->available_court = Facility::join('courts', 'facilities.id', 'courts.facility_id')
                    ->where('facilities.id', $val->id)
                    ->where('courts.status', 1)
                    ->count();
                $val->available_category = FacilityCategory::join('facilities', 'facilities.id', 'facility_categories.facility_id')
                    ->join('court_categories', 'court_categories.id', 'facility_categories.category_id')
                    ->where('facility_categories.facility_id', $val->id)
                    ->select('court_categories.id', 'court_categories.name', 'court_categories.image')->get();
            }
            $data['court'] = $getFacility;
            $response['status'] = true;
        } else {
            $data['court'] = [];
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function facilityDetail(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $validator = Validator::make($input, [
            'facility_id'   => 'required',
        ]);
        $locale = App::getLocale();

        if (!$locale) {
            $locale = 'en';
        }
        // dd($locale);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $id = $request->facility_id;
            $data = Facility::with('facilityAmenities.amenityDetails', 'facilityRules', 'facilityCategory.categoryDetails')
                ->where(['id' => $id, 'status' => 1, 'is_deleted' => 0])
                ->first();
            if ($data) {
                $data = $data;
                $data['amenities'] = FacilityAmenity::join('facilities', 'facilities.id', 'facility_amenities.facility_id')
                    ->join('amenities', 'amenities.id', 'facility_amenities.amenity_id')
                    ->join('amenities_lang', 'amenities.id', 'amenities_lang.amenity_id')
                    ->where('facility_amenities.facility_id', $id)
                    ->where('amenities_lang.lang', $locale)
                    ->select('amenities.id', 'amenities_lang.name', 'amenities.image')
                    ->get();
                if ($request->latitude && $request->longitude) {
                    $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $data->latitude . ',' . $data->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                    $getDistance = json_decode($destance);
                    if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                        $data->distance = $getDistance->rows[0]->elements[0]->distance->text;
                    } else {
                        $data->distance = '';
                    }
                }
                $data['court_list'] =  $this->getCourt($input, 'All', 0);

                if (count($data['court_list'])) {

                    foreach ($data['court_list'] as $key => $value) {
                        if ($request->latitude && $request->longitude) {
                            $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $value->latitude . ',' . $value->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                            $getDistance = json_decode($destance);
                            if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                                $value->distance = $getDistance->rows[0]->elements[0]->distance->text;
                            } else {
                                $value->distance = '';
                            }
                        }
                        $value->category_image = CourtCategory::where(['status' => 1, 'id' => $value->category_id])->first()->image;
                    }
                }
                $response['status'] = true;
            } else {
                $data = [];
                $response['status'] = false;
                $response['message'] = 'No record found';
            }
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }

    /**
     * This function is use for mobile app to show latest dashboard data.
     *
     * @method Post
     */

    public function getHomeData(Request $request)
    {

        $serachData = $request->all();
        $limit = 10;
        $radius = 10;
        $is_featured = 1;

        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
            'main_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            // getCountryTaxByLatLong($serachData['latitude'],$serachData['longitude']);
            $main_category_name = MainCategory::select('id', 'name')->where('id', $serachData['main_category_id'])->first();
            $getCategory = $this->getCategory($serachData, $limit);
            $getFeaturedRestro = $this->getRestro($serachData, $limit, $is_featured);
            $getBrands = $this->getBrands($serachData, $limit);
            $getAllBrandCategory = BrandCategory::select('id', 'name')->where('status', 1)->get();
            $getAllBrandCategory2 = [];

            if (count($getAllBrandCategory)) {

                foreach ($getAllBrandCategory as $k_BC => $v_BC) {
                    $brands = $this->getBrands($serachData, $limit, 0, $v_BC->id);

                    if (count($brands)) {
                        $v_BC->brands = $brands;
                        array_push($getAllBrandCategory2, $v_BC);
                    } else {
                        // unset($getAllBrandCategory[$k_BC]);
                    }
                }
            }
            /*$all_brands['topCoffeeBrand'] = $this->getBrands($serachData, $limit, 0, 'Coffee');
            $all_brands['topSweetBrand'] = $this->getBrands($serachData, $limit, 0, 'Sweet');
            $all_brands['topSandwichBrand'] = $this->getBrands($serachData, $limit, 0, 'Sandwich');
            $all_brands['topPizzaBrand'] = $this->getBrands($serachData, $limit, 0, 'Pizza');
            $all_brands['topRestaurantBrand'] = $this->getBrands($serachData, $limit, 0, 'Restaurant');*/

            $getHostDish =  $this->getHotDish($serachData, $limit);
            $getTopPickupsDish =  $this->getTopPickupsDish($serachData, $limit);
            // $getTopDineInDish =  $this->getTopDineInDish($serachData, $limit);
            $getTopDishs =  $this->getTopDishs($serachData, $limit);
            $getTopDineInRestro =  $this->getTopRestrosBySlug($serachData, $limit, 'DineIn');
            $getTopPickupsRestro =  $this->getTopRestrosBySlug($serachData, $limit, 'PickUp');
            $getBOGORestro =  $this->getBOGORestros($serachData, $limit);
            $getBanner = $this->getBanner($serachData);
            $cartCount = $this->getCart();

            if (count($getCategory)) {
                $data['category'] = $getCategory;
            } else {
                $data['category'] = [];
            }

            if (count($getFeaturedRestro)) {

                foreach ($getFeaturedRestro as $key => $value) {
                    $value->distance = number_format($value->distance, 2) . ' KM';
                }
                $data['featuredRestro'] = $getFeaturedRestro;
                // $data['restroImageBaseUrl'] = asset('uploads/user').'/';

            } else {
                $data['featuredRestro'] = [];
            }
            $getTopGiftingItems = $this->getTopGiftingItems($serachData, $limit);

            if (count($getTopGiftingItems)) {

                foreach ($getTopGiftingItems as $key => $value) {
                    $value->distance = number_format($value->distance, 2) . ' KM';
                }
                $data['topGiftingItems'] = $getTopGiftingItems;
            } else {
                $data['topGiftingItems'] = [];
            }

            /*$getTopGiftingRestro = $this->getTopGiftingRestro($serachData, $limit);

            if (count($getFeaturedRestro)) {
                $data['giftingRestro'] = $getFeaturedRestro;

            } else {
                $data['giftingRestro'] = [];
            }*/

            if (count($getBrands)) {
                $data['brands'] = $getBrands;
            } else {
                $data['brands'] = [];
            }
            $data['all_brands'] = $getAllBrandCategory2;

            if (count($getHostDish)) {
                $data['hotDish'] = $getHostDish;
            } else {
                $data['hotDish'] = [];
            }

            if (count($getHostDish)) {
                $data['productOnFire'] = $getHostDish;
            } else {
                $data['productOnFire'] = [];
            }

            if (count($getTopPickupsDish)) {
                $data['topPickupDishes'] = $getTopPickupsDish;
            } else {
                $data['topPickupDishes'] = [];
            }

            if (count($getTopDishs)) {
                $data['topDishes'] = $getTopDishs;
            } else {
                $data['topDishes'] = [];
            }

            if (count($getTopDineInRestro)) {

                foreach ($getTopDineInRestro as $key => $value) {
                    $value->distance = number_format($value->distance, 2) . ' KM';
                }
                $data['topDineInRestros'] = $getTopDineInRestro;
            } else {
                $data['topDineInRestros'] = [];
            }

            if (count($getTopPickupsRestro)) {

                foreach ($getTopPickupsRestro as $key => $value) {
                    $value->distance = number_format($value->distance, 2) . ' KM';
                }
                $data['topPickupsRestro'] = $getTopPickupsRestro;
            } else {
                $data['topPickupsRestro'] = [];
            }

            if (count($getBOGORestro)) {

                foreach ($getBOGORestro as $key => $value) {
                    $value->distance = number_format($value->distance, 2) . ' KM';
                }
                $data['getOfferRestro'] = $getBOGORestro;
            } else {
                $data['getOfferRestro'] = [];
            }

            if (count($getBanner)) {
                $data['banner'] = $getBanner;
            } else {
                $data['banner'] = [];
            }
            $data['cartCount'] = $cartCount;
            $data['main_category_name'] = $main_category_name;
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response, 200);
        }
    }

    /**
     * This function is use for get all banners.
     *
     * @global function for this class.
     */

    public function getBanner($serachData)
    {
        $bannerList = Media::select('category_type', 'category_id', 'link', 'file_path')->where(['medias.table_name' => 'Banner']);

        if (array_key_exists("main_category_id", $serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $bannerList->where('main_category_id', $main_category_id);
        }

        $bannerDetail = $bannerList->get();

        return $bannerDetail;
    }

    /**
     * This function is use for mobile app to show Services (Main Category).
     *
     * @method Get
     */

    public function getMainCategory()
    {
        $main_category = MainCategory::select('id', 'name', 'image', 'mix_order')->where('status', 1)->orderBy('position', 'ASC')->get();
        if (count($main_category)) {
            $data = $main_category;
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response, 200);
        } else {
            $data = [];
            $response['status'] = 0;
            $response['data'] = $data;
            $response['message'] = 'Main Category Not Found.';
            return response()->json($response, 200);
        }
    }

    /**
     * This function is use for count of user cart.
     *
     * @global function for this class.
     */

    public function getCart()
    {
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id', $userId)->get();
        $totalQty = 0;
        if (count($query)) {
            foreach ($query as $data) {
                $totalQty += $data->qty;
            }
        } else {
            $totalQty = 0;
        }
        return $totalQty;
    }

    /**
     * This function is use for mobile app to show user addresses.
     *
     * @method Get
     */

    public function getAllAddress()
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        if (!empty($userId)) {
            $userAddressList =  UsersAddress::where('user_id', $userId)->orderBy('id', 'DESC')->get();

            if (count($userAddressList)) {
                $data = $userAddressList;
            } else {
                $data = [];
            }

            if (count($data)) {
                $response['status'] = 1;
                $response['data'] = $data;
                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['message'] = 'Adderss Not Found.';
                return response()->json($response, 200);
            }
        } else {
            $response['status'] = 0;
            $response['message'] = 'Something worng.';
            return response()->json($response, 200);
        }
    }

    /**
     * This function is use for mobile app to add new address of user.
     *
     * @method Post
     */

    public function addAddress(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'address_type' => 'required',
            'building_number' => 'required',
            'building_name' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =    $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {

            if (!empty($userId)) {
                $inputs = [
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                    'address_type' => $request->address_type,
                    'building_number' => $request->building_number,
                    'building_name' => $request->building_name,
                    'landmark' => $request->landmark,
                    'address' => $request->address,
                ];
                /*$address = '';
                if ($request->has('address_type')) {
                    $address .= $request->address_type.', ';
                }
                if ($request->has('building_number')) {
                    $address .= $request->building_number.', ';
                }
                if ($request->has('street_line_1')) {
                    $address .= $request->street_line_1.', ';
                }
                if ($request->has('street_line_2')) {
                    $address .= $request->street_line_2.', ';
                }
                $inputs['address'] = $address;*/

                if (array_key_exists('address_id', $request->all())) {

                    // $checkAddressExist = UsersAddress::where(['latitude'=>$request->latitude, 'longitude'=>$request->longitude, 'user_id'=>$userId])->where('id','!=',$request->address_id)->first();
                    $checkAddressExist = UsersAddress::where(['address_type' => $request->address_type, 'user_id' => $userId])->where('id', '!=', $request->address_id)->first();

                    if (!$checkAddressExist) {
                        $address_id  = $request->address_id;
                        $inputs['user_id'] = $userId;
                        $userAdderssList = UsersAddress::findOrFail($address_id);
                        // dd($inputs);
                        if ($userAdderssList->update($inputs)) {
                            $inputs['is_defauld_address'] = $userAdderssList->is_defauld_address;
                            $response['status'] = 1;
                            $response['data'][] = $inputs;
                            $response['message'] = 'Address Updated Successfully.';
                            return response()->json($response, 200);
                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Address Can`t be Updated.';
                            return response()->json($response, 200);
                        }
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Please select another Tag.';
                        return response()->json($response, 200);
                    }
                } else {
                    $checkAddressExist = UsersAddress::where(['address_type' => $request->address_type, 'user_id' => $userId])->first();

                    if (!$checkAddressExist) {
                        $userHaveAddress = UsersAddress::where(['user_id' => $userId])->first();

                        if (!$userHaveAddress) {
                            $inputs['is_defauld_address'] = 1;

                            $inputAddress = [
                                'address' => $request->address,
                                'latitude' => $request->latitude,
                                'longitude' => $request->longitude,
                            ];
                            $usersList = User::findOrFail($userId);
                            $usersList->update($inputAddress);
                        }

                        $inputs['user_id'] = $userId;
                        $userAdderss = UsersAddress::create($inputs);

                        if ($userAdderss) {
                            $inputs['is_defauld_address'] = 1;
                            $response['status'] = 1;
                            $response['data'][] = $inputs;
                            $response['message'] = 'Address Added Successfully.';
                            return response()->json($response, 200);
                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Address Can`t be added.';
                            return response()->json($response, 200);
                        }
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Please select another Tag.';
                        return response()->json($response, 200);
                    }
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'Something worng.';
                return response()->json($response, 200);
            }
        }
    }

    /**
     * This function is use for mobile app to delete address of user by address id.
     *
     * @method Post
     */

    public function deleteAddress(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {

            if (!empty($userId)) {
                $address_id  = $request->address_id;
                $addressDetails =  UsersAddress::where(['id' => $address_id, 'user_id' => $userId])->first();

                if (!empty($addressDetails)) {
                    UsersAddress::where('id', $address_id)->where('user_id', $userId)->delete();
                    $defaultAddress  = UsersAddress::where('user_id', $userId)->get()->count();
                    $inp = ['address' => '', 'latitude' => '', 'longitude' => ''];

                    if ($defaultAddress == 0) {
                        $User = User::findOrFail($userId);
                        $User->update($inp);
                    }
                    $response['status'] = 1;
                    $response['message'] = 'Address deleted Successfully.';
                    return response()->json($response, 200);
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Address not found.';
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'Something worng.';
                return response()->json($response, 200);
            }
        }
    }

    /**
     * This function is use for mobile app to get default address of user.
     *
     * @method Get
     */

    public function getDefaultAddress()
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        if (!empty($userId)) {
            $userAddressList =  UsersAddress::where(['user_id' => $userId, 'is_defauld_address' => 1])->first();

            if ($userAddressList) {
                $response['status'] = 1;
                $response['data'] = $userAddressList;
                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['message'] = 'Adderss Not Found.';
                return response()->json($response, 200);
            }
        } else {
            $response['status'] = 0;
            $response['message'] = 'Something worng.';
            return response()->json($response, 200);
        }
    }

    /**
     * This function is use for mobile app to get wallet transactions.
     *
     * @method Get
     */

    public function wallet_list()
    {
        $userData = auth()->user();
        $userId =  $userData->id;
        $User = User::findOrFail($userId);
        $taxQARData = Tax::select('tax.*', 'countries.name', 'countries.phonecode', 'countries.sortname', 'currency.currency_code')->join('countries', 'countries.id', '=', 'tax.country_id')->leftJoin('currency', 'currency.id', '=', 'tax.currency_id')->where('currency.currency_code', 'QAR')->first();
        $response['data']['usd_to_qar'] = $taxQARData->difference_amount;

        $taxAEDData = Tax::select('tax.*', 'countries.name', 'countries.phonecode', 'countries.sortname', 'currency.currency_code')->join('countries', 'countries.id', '=', 'tax.country_id')->leftJoin('currency', 'currency.id', '=', 'tax.currency_id')->where('currency.currency_code', 'AED')->first();
        $response['data']['usd_to_aed'] = $taxAEDData->difference_amount;

        if (!empty($userId)) {
            $totalCR =  UserWallets::where(['user_id' => $userId, 'transaction_type' => 'CR'])->sum('amount');
            $totalDR =  UserWallets::where(['user_id' => $userId, 'transaction_type' => 'DR'])->sum('amount');
            $userWalletList =  UserWallets::select('transaction_type', 'amount', 'comment', 'transaction_id', 'created_at')->where(['user_id' => $userId])->orderBy('id', 'desc')->get();

            if (count($userWalletList)) {
                $response['status'] = 1;
                $available_balance = $totalCR - $totalDR;
                $response['data']['available_balance'] = (float)number_format($available_balance, 2, '.', '');
                $response['data']['total_points'] = $User->total_points;
                /*$response['data']['balance'] = $available_balance;
                $response['data']['totalCR'] = $totalCR;
                $response['data']['totalDR'] = $totalDR;*/
                $response['data']['wallet_list'] = $userWalletList;
                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['message'] = 'No record found.';
                $response['data']['available_balance'] = 0.0;
                $response['data']['total_points'] = $User->total_points;
                return response()->json($response, 200);
            }
        } else {
            $response['status'] = 0;
            $response['message'] = 'User not found.';
            return response()->json($response, 200);
        }
    }

    function saveOrder(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'app_type' => 'required',
            'random_order_id' => 'required',
            'user_id' => 'required',
            'user_name' => 'required',
            'user_country_code' => 'required',
            'user_phone_number' => 'required',
            'user_email' => 'required',
            'restaurant_id' => 'required',
            'restaurant_name' => 'required',
            'restaurant_email' => 'required',
            'restaurant_phone_number' => 'required',
            'pickup_point_id' => 'required',
            'pickup_point_name' => 'required',
            'pickup_point_email' => 'required',
            'pickup_point_phone_number' => 'required',
            'ordered_currency_code' => 'required',
            'delivery_address' => 'required',
            'delivery_lat' => 'required',
            'delivery_long' => 'required',
            'pickup_point_address' => 'required',
            'pickup_point_lat' => 'required',
            'pickup_point_long' => 'required',
            'discount_amount' => 'nullable',
            'shipping_charges' => 'nullable',
            'tax_amount' => 'nullable',
            'amount' => 'required',
            'org_amount' => 'required',
            'admin_amount' => 'nullable',
            'admin_commission' => 'nullable',
            'order_status' => 'required',
            'total_kp_received' => 'required',
            'payment_type' => 'required',
            'is_wallet_use' => 'required',
            'restaurant_table_code' => 'nullable',
            'driver_name' => 'nullable',
            'driver_email' => 'nullable',
            'driver_phone_number' => 'nullable',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $orderData = new Orders();
            $orderData->main_user_id = $userId;
            $orderData->main_category_id = $request->main_category_id ?? null;
            $orderData->app_type = $request->app_type;
            $orderData->random_order_id = $request->random_order_id;
            $orderData->user_id = $request->user_id;
            $orderData->user_name = $request->user_name;
            $orderData->user_country_code = $request->user_country_code;
            $orderData->user_phone_number = $request->user_phone_number;
            $orderData->user_email = $request->user_email;
            $orderData->restaurant_id = $request->restaurant_id;
            $orderData->restaurant_name = $request->restaurant_name;
            $orderData->restaurant_email = $request->restaurant_email;
            $orderData->restaurant_phone_number = $request->restaurant_phone_number;
            $orderData->pickup_point_id = $request->pickup_point_id;
            $orderData->pickup_point_name = $request->pickup_point_name;
            $orderData->pickup_point_email = $request->pickup_point_email;
            $orderData->pickup_point_phone_number = $request->pickup_point_phone_number;
            $orderData->ordered_currency_code = $request->ordered_currency_code;
            $orderData->delivery_address = $request->delivery_address;
            $orderData->delivery_lat = $request->delivery_lat;
            $orderData->delivery_long = $request->delivery_long;
            $orderData->pickup_point_address = $request->pickup_point_address;
            $orderData->pickup_point_lat = $request->pickup_point_lat;
            $orderData->pickup_point_long = $request->pickup_point_long;
            $orderData->discount_amount = $request->discount_amount ?? null;
            $orderData->shipping_charges = $request->shipping_charges ?? null;
            $orderData->tax_amount = $request->tax_amount ?? null;
            $orderData->amount = $request->amount;
            $orderData->org_amount = $request->org_amount;
            $orderData->admin_amount = $request->admin_amount ?? null;
            $orderData->admin_commission = $request->admin_commission ?? null;
            $orderData->order_status = $request->order_status;
            $orderData->total_kp_received = $request->total_kp_received;
            $orderData->payment_type = $request->payment_type;
            $orderData->is_wallet_use = $request->is_wallet_use;
            $orderData->restaurant_table_code = $request->restaurant_table_code ?? null;
            $orderData->driver_name = $request->driver_name ?? null;
            $orderData->driver_email = $request->driver_email ?? null;
            $orderData->driver_phone_number = $request->driver_phone_number ?? null;

            if ($orderData->save()) {
                $response['status'] = 1;
                $response['message'] = 'Order Save Successfully.';
            } else {
                $response['status'] = 0;
                $response['message'] = 'Error Occured.';
            }
            return response()->json($response, 200);
        }
    }

    /**
     * This function is use for mobile app to add money in wallet.
     *
     * @method Post
     */

    public function add_money(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|not_in:0',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {

            if (!empty($userId)) {
                $data = new UserWallets;
                $data->user_id = $userId;
                $data->transaction_type = 'CR';
                $data->amount = $request->amount;
                $data->comment = 'Add Money';

                if ($data->save()) {
                    $totalCR =  UserWallets::where(['user_id' => $userId, 'transaction_type' => 'CR'])->sum('amount');
                    $totalDR =  UserWallets::where(['user_id' => $userId, 'transaction_type' => 'DR'])->sum('amount');
                    $response['status'] = 1;
                    $response['message'] = 'Amount added successfully.';
                    $response['data']['available_balance'] = (float)number_format($totalCR - $totalDR, 2, '.', '');
                    return response()->json($response, 200);
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'User not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function update_transaction(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|not_in:0',
            'transaction_type' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {

            if (!empty($userId)) {
                $data = new UserWallets;
                $data->user_id = $userId;
                $data->transaction_type = $request->transaction_type;
                $data->amount = $request->amount;
                $data->comment = $request->comment;

                if ($data->save()) {
                    $totalCR =  UserWallets::where(['user_id' => $userId, 'transaction_type' => 'CR'])->sum('amount');
                    $totalDR =  UserWallets::where(['user_id' => $userId, 'transaction_type' => 'DR'])->sum('amount');
                    $response['status'] = 1;
                    $response['message'] = 'Amount added successfully.';
                    $response['data']['available_balance'] = (float)number_format($totalCR - $totalDR, 2, '.', '');
                    return response()->json($response, 200);
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'User not found.';
                return response()->json($response, 200);
            }
        }
    }

    /**
     * This function is use for mobile app to set users default address.
     *
     * @method Post
     */

    public function defaultAddress(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {

            if (!empty($userId)) {

                if (array_key_exists('address_id', $request->all())) {
                    $updateDefaultAddress = [
                        'is_defauld_address' => 0
                    ];
                    UsersAddress::where('user_id', $userId)->update($updateDefaultAddress);

                    $address_id  = $request->address_id;
                    $addressDetails =  UsersAddress::where('id', $address_id)->where('user_id', $userId)->first();

                    if (!empty($addressDetails)) {
                        $inputs = [
                            'address' => $addressDetails->address,
                            'latitude' => $addressDetails->latitude,
                            'longitude' => $addressDetails->longitude
                        ];
                        $usersList = User::findOrFail($userId);
                        $usersList->update($inputs);

                        //Set default address
                        $inputAdd = [
                            'is_defauld_address' => 1
                        ];
                        $addressDetails->update($inputAdd);

                        $response['status'] = 1;
                        $response['message'] = 'Default address set successfully.';
                        return response()->json($response, 200);
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Address can`t be set.';
                        return response()->json($response, 200);
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'Something worng.';
                return response()->json($response, 200);
            }
        }
    }

    /**
     * This function is use for mobile app to get all category data.
     *
     * @method Post
     */

    public function getAllCategory(Request $request)
    {
        $serachData = $request->all();
        $limit =  'All';
        $getCategory = $this->getCategory($serachData, $limit);


        if (count($getCategory)) {
            $data = $getCategory;
        } else {
            $data = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        // $response['imageBaseUrl'] = asset('uploads/category').'/';

        return response()->json($response, 200);
    }

    public function getSubCategory(Request $request)
    {
        $serachData = $request->all();
        $limit =  'All';

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {

            $data = Subcategory::select('id', 'main_category_id', 'category_id', 'name', 'parent_id', 'parent_id')->where(['category_id' => $request->category_id, 'status' => 1])->where('parent_id', '=', 0)->get();

            // dd($data->toArray());

            if ($data) {

                foreach ($data as $key => $value) {

                    if (count($value->childs)) {
                        $this->childCategory($value->childs);
                    }
                }
            }
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response, 200);
        }
    }

    function childCategory($childs)
    {

        foreach ($childs as $key => $child) {

            if (count($child->childs)) {
                $this->childCategory($child->childs);
            }
        }
    }


    public function getAllPlayers(Request $request)
    {
        $serachData = $request->all();
        $PlayeList = User::select('id', 'name', 'country_code', 'mobile')->where(['status' => 1])
        ->where(function ($query) {
            $query->where('type','=',3)
            ->orWhere('is_facility_owner','=',1);
        })
        ->orderBy('id', 'desc');
        if (array_key_exists("country_code", $serachData)) {
            $search = $serachData['country_code'];
            $PlayeList->Where("country_code", 'like', '%' . $search . '%');
        }
        if (array_key_exists("mobile", $serachData)) {
            $search = $serachData['mobile'];
            $PlayeList->Where("mobile", 'like', '%' . $search . '%');
        }
        $PlayeLists = $PlayeList->get();
        if (count($PlayeLists)) {
            $response['status'] = true;
            $data['player_list'] = $PlayeLists;
        } else {
            $data['player_list'] = [];
            $response['status'] = false;
            $response['message'] = __('api.No_record_found');
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }
    public function getAllCourtList(Request $request)
    {
        $serachData = $request->all();
        $CourtList = Courts::leftjoin('facilities', 'facilities.id', "=", 'courts.facility_id')
        ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
        ->select('courts.*','facilities.name as facility_name','users.mobile','users.country_code')->where(['courts.status' => 1])->orderBy('courts.id', 'desc');
        if (array_key_exists("facility_id", $serachData)) {
            $facility_id = $serachData['facility_id'];
            $CourtList->where('courts.facility_id', $facility_id);
            $CourtLists = $CourtList->get()->makeHidden(['created_at', 'category_id', 'image', 'address', 'latitude', 'longitude', 'minimum_hour_book', 'is_featured', 'status', 'facility_owner_id']);
        } else {
            $CourtLists = $CourtList->get();
        }
        if (count($CourtLists)) {
            foreach ($CourtLists as $key => $val) {
                // add time slot key
                $start_time = date('Y-m-d') . ' ' . $val->start_time;
                $end_time = date('Y-m-d') . ' ' . $val->end_time;
                $interval = (int)$val->timeslot;

                $ReturnArray = array(); // Define output
                $StartTime    = strtotime($start_time); //Get Timestamp
                $EndTime      = strtotime($end_time); //Get Timestamp

                $AddMins  = $interval * 60;
                $i = 0;

                while ($StartTime < $EndTime) //Run loop
                {
                    $diff = (int)abs($EndTime - $StartTime) / 60;

                    if ($diff >= $interval) {
                        // echo $diff."<br/>";
                        // dd($diff,$interval);
                        // $ReturnArray[$i]['start_time'] = date("H:i", $StartTime);
                        $ReturnArray[] = date("H:i", $StartTime);
                        $StartTime += $AddMins; //Endtime check
                        // $ReturnArray[$i]['end_time'] = date("H:i", $StartTime);
                        $i++;
                    } else {
                        break;
                    }
                }
                $val->selecttimeslot = $ReturnArray;

                // end time slot key
            }
            $response['status'] = true;
            $data['court_list'] = $CourtLists;
        } else {
            $data['court_list'] = [];
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }
    public function getCourt($serachData, $limit, $is_pagination)
    {
        // $CourtList = Courts::select('*')->where(['status' => 1])->orderBy('id', 'desc');
        // filter data by date 
        if (array_key_exists("date", $serachData)) {
            $date = $serachData['date'];
            
            $courtIds = CourtBooking::select('*')->where(['status' => 1, 'booking_date' => $date])->pluck('court_id')->toArray();
            if (isset($courtIds)) {
                $CourtList = Courts::leftjoin('facilities', 'facilities.id', "=", 'courts.facility_id')
                ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->select('courts.*','facilities.name as facility_name','users.mobile','users.country_code')
                ->where(['courts.status' => 1])
                ->where(['courts.is_deleted' => 0]);
                //->whereNotIn('id', $courtIds);
                //->whereIn('id', $courtIds);
            } else {
                $CourtList = Courts::leftjoin('facilities', 'facilities.id', "=", 'courts.facility_id')
                ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->select('courts.*','facilities.name as facility_name','users.mobile','users.country_code')
                ->where(['courts.status' => 1])
                ->where(['courts.is_deleted' => 0]);
            }
        } else {
            $CourtList = Courts::leftjoin('facilities', 'facilities.id', "=", 'courts.facility_id')
            ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
            ->select('courts.*','facilities.name as facility_name','users.mobile','users.country_code')
            ->where(['courts.status' => 1])
            ->where(['courts.is_deleted' => 0]);
        }
        // short data by rating 
        if (array_key_exists("rating", $serachData)) {
            $avg_rating = $serachData['rating'];
            if ($avg_rating == 'asc') {
                $CourtList->orderBy('courts.average_rating', 'asc');
            }
            if ($avg_rating == 'desc') {
                $CourtList->orderBy('courts.average_rating', 'desc');
            }
        } else if (array_key_exists("distance", $serachData)) {
            $distance = $serachData['distance'];
            $latitude = $serachData['latitude'];
            $longitude = $serachData['longitude'];
            // $CourtList = Courts::select('*')->where(['status' => 1]);
            if ($latitude && $longitude) {
                $CourtList->selectRaw("( 6371 * acos( cos( radians(" . $latitude . ") ) *cos( radians(courts.latitude) ) * cos( radians(courts.longitude) - radians(" . $longitude . ") ) +  sin( radians(" . $latitude . ") ) * sin( radians(courts.latitude) ) ) )  AS distance");
            }
            // dd($CourtList->get());
            if ($distance == 'asc') {
                $CourtList->orderBy('distance', 'asc');
            }
            if ($distance == 'desc') {
                $CourtList->orderBy('distance', 'desc');
            }
        } else {
            $CourtList->orderBy(DB::raw('courts.position IS NULL, courts.position'), 'asc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('id', 'desc');
        }

        if (array_key_exists("facility_owner_id", $serachData)) {
            $facility_owner_id = $serachData['facility_owner_id'];
            $CourtList->where('courts.facility_owner_id', $facility_owner_id);
        }
        if (array_key_exists("category_id", $serachData) && !empty($serachData['category_id'])) {
            $category_id = $serachData['category_id'];
            $court_ids = Courts::where('category_id', $category_id)->pluck('id')->toArray();
            $CourtList->whereIn('courts.id', $court_ids);
        }

        if (array_key_exists("facility_id", $serachData)) {
            $facility_id = $serachData['facility_id'];
            $CourtList->where('courts.facility_id', $facility_id);
        }

        if (array_key_exists("search_text", $serachData)) {
            $search = $serachData['search_text'];
            $CourtList->Where("courts.court_name", 'like', '%' . $search . '%');
        }

        if ($limit != 'All') {
            if ($is_pagination == 1) {
                $CourtDetail = $CourtList->paginate($limit);
            } else {
                $CourtDetail = $CourtList->limit($limit);
                $CourtDetail = $CourtList->get();
            }
        } else {
            $CourtDetail = $CourtList->get();
        }
        return $CourtDetail;
    }
    public function searchFacilityAndCourt(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $serachData = $request->all();
        $limit = 10;
        $is_pagination = 1;

        $message = [
            'latitude.required' => __("api.latitude_required"),
            'longitude.required' => __("api.longitude_required"),
        ];
        $validator = Validator::make($input, [
            'latitude'   => 'required',
            'longitude'   => 'required',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $courts = Courts::select('*')->where('status', 1);
            if (array_key_exists("search_text", $serachData)) {
                $search = $serachData['search_text'];
                $courts->Where("courts.court_name", 'like', '%' . $search . '%');
            }
            if ($limit != 'All') {
                if ($is_pagination == 1) {
                    if (array_key_exists("sort_by", $serachData)) {
                        $sort = $serachData['sort_by'];
                        if ($sort == 'distance') {
                            $courts->orderBy('distance', 'asc');
                        }
                    }
                    $data['court_data'] = $courts->paginate($limit);
                } else {
                    $courts->limit($limit);
                    $data['court_data'] = $courts->get();
                }
            } else {
                $data['court_data'] = $courts->get();
            }
            $facilities = Facility::select('*')->where('status', 1);
            if (array_key_exists("search_text", $serachData)) {
                $search = $serachData['search_text'];
                $facilities->Where("facilities.name", 'like', '%' . $search . '%');
            }
            if ($limit != 'All') {
                if ($is_pagination == 1) {
                    if (array_key_exists("sort_by", $serachData)) {
                        $sort = $serachData['sort_by'];
                        if ($sort == 'distance') {
                            $facilities->orderBy('distance', 'asc');
                        }
                    }
                    $data['facility_data'] = $facilities->paginate($limit);
                } else {
                    $courts->limit($limit);
                    $data['facility_data'] = $facilities->get();
                }
            } else {
                $data['facility_data'] = $facilities->get();
            }
            if (isset($data)) {
                $response['status'] = true;
                $response['data'] = $data;
                return response()->json($response, 200);
            } else {
                $response['status'] = false;
                $response['message'] = __("api.something_worng");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }
    public function getAllFacilityList(Request $request)
    {
        $court_ids = Courts::pluck('facility_id')->toArray();
        $FacilityList = Facility::select('id', 'name')->where(['status' => 1])->where(['is_deleted' => 0])->whereIn('id', $court_ids)->get()->makeHidden(['facility_amenities', 'average_rating', 'total_rating', 'facility_categories']);
        $CategoryList = CourtCategory::select('id', 'name', 'image')->where(['status' => 1])->get();
        if (count($FacilityList)) {
            $response['status'] = true;
            $data['facility_list'] = $FacilityList;
            $data['category_list'] = $CategoryList;
        } else {
            $data['facility_list'] = [];
            $data['category_list'] = [];
            $response['status'] = false;
            $response['message'] = 'No record found';
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }
    public function getFacility($serachData, $limit, $is_pagination)
    {
        // filter data by single court & Multiple court 
        $court_ids = Courts::pluck('facility_id')->toArray();
        if (array_key_exists("court", $serachData)) {
            $court = $serachData['court'];

            if ($court == 'Single') {
                $facilityIds = Courts::select('*')->where(['status' => 1])->groupBy('facility_id')->having(DB::raw('count(facility_id)'), '=', 1)->pluck('facility_id')->toArray();
                $FacilityList = Facility::select('*')->where(['status' => 1])->where(['is_deleted' => 0])->whereIn('id', $facilityIds);
            } else if ($court == 'Multiple') {
                $facilityIds = Courts::select('*')->where(['status' => 1])->groupBy('facility_id')->having(DB::raw('count(facility_id)'), '>', 1)->pluck('facility_id')->toArray();
                $FacilityList = Facility::select('*')->where(['status' => 1])->where(['is_deleted' => 0])->whereIn('id', $facilityIds);
            } else {
                $FacilityList = Facility::select('*')->where(['status' => 1])->where(['is_deleted' => 0])->whereIn('id', $court_ids);
            }
        } else {
            $FacilityList = Facility::select('*')->where(['status' => 1])->where(['is_deleted' => 0])->whereIn('id', $court_ids);
        }
        // filter data by date 
        if (array_key_exists("date", $serachData)) {
            $date = $serachData['date'];
            $facilityIds = CourtBooking::select('*')->where(['status' => 1, 'booking_date' => $date])->pluck('facility_id')->toArray();
            if (isset($facilityIds)) {
                 //$FacilityList = Facility::select('*')->where(['status' => 1])->whereIn('id', $court_ids)->whereNotIn('id', $facilityIds)->orderBy('id', 'desc');
                $FacilityList->whereNotIn('id', $facilityIds);
            } else {
               //  $FacilityList = Facility::select('*')->where(['status' => 1])->whereIn('id', $court_ids)->orderBy('id', 'desc');
                $FacilityList =  $FacilityList;
            }
        }

        // short data by distance 
        if (array_key_exists("distance", $serachData)) {
            $distance = $serachData['distance'];
            $latitude = $serachData['latitude'];
            $longitude = $serachData['longitude'];
            // $FacilityList = Facility::select('*')->where(['status' => 1])->whereIn('id', $court_ids);
            if ($latitude && $longitude) {
                $FacilityList->selectRaw("( 6371 * acos( cos( radians(" . $latitude . ") ) *cos( radians(facilities.latitude) ) * cos( radians(facilities.longitude) - radians(" . $longitude . ") ) +  sin( radians(" . $latitude . ") ) * sin( radians(facilities.latitude) ) ) )  AS distance");
            }
            if ($distance == 'asc') {
                $FacilityList->orderBy('distance', 'asc');
            }
            if ($distance == 'desc') {
                $FacilityList->orderBy('distance', 'desc');
            }
        }
        // short data by rating 
        else if (array_key_exists("rating", $serachData)) {
            $avg_rating = $serachData['rating'];
            // $FacilityList = Facility::select('facilities.*')->where(['facilities.status' => 1])->whereIn('id', $court_ids);
            if ($avg_rating == 'asc') {
                $FacilityList->orderBy('average_rating', 'asc');
            }
            if ($avg_rating == 'desc') {
                $FacilityList->orderBy('average_rating', 'desc');
            }
        } else {
            $FacilityList->orderBy(DB::raw('position IS NULL, position'), 'asc')
                ->orderBy('id', 'desc');
        }

        if (array_key_exists("facility_owner_id", $serachData)) {
            $facility_owner_id = $serachData['facility_owner_id'];
            $FacilityList->where('facility_owner_id', $facility_owner_id);
        }
        if (array_key_exists("category_id", $serachData)) {
            $category_id = $serachData['category_id'];
            $facility_ids = FacilityCategory::where('category_id', $category_id)->pluck('facility_id')->toArray();
            $FacilityList->whereIn('id', $facility_ids);
        }

        if (array_key_exists("search_text", $serachData)) {
            $search = $serachData['search_text'];
            $FacilityList->Where("name", 'like', '%' . $search . '%');
        }

        if ($limit != 'All') {
            if ($is_pagination == 1) {
                $FacilityDetail = $FacilityList->paginate($limit);
            } else {
                $FacilityDetail = $FacilityList->limit($limit);
                $FacilityDetail = $FacilityList->get();
            }
        } else {
            $FacilityDetail = $FacilityList->get();
        }
        return $FacilityDetail;
    }

    public function getCategory($serachData, $limit)
    {
        $getAllDishCatIds = Products::where(['is_active' => 1])->groupBy('category_id')->pluck('category_id')->toArray();
        $categoryList = Category::select('id', 'name', 'description', 'image', 'main_category_id')->where(['type' => 1, 'status' => 1])->whereIn('id', $getAllDishCatIds);

        if (array_key_exists("main_category_id", $serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $categoryList->where('main_category_id', $main_category_id);
        }

        if (array_key_exists("search_text", $serachData)) {
            $search = $serachData['search_text'];
            $categoryList->Where("categories.name", 'like', '%' . $search . '%');
        }
        if ($limit != 'All') {
            $categoryList->limit($limit);
        }
        $categoryDetail = $categoryList->get();
        return $categoryDetail;
    }

    // public function getBrands($serachData, $limit, $is_pagination = 0, $slug = '')
    // {
    //     // $getRestroBrandIds = Restaurant::where(['status'=>1])->groupBy('brand_id')->pluck('brand_id')->toArray();
    //     $getRestroBrandIds = Restaurant::select('restaurants.brand_id')->join('products', 'products.restaurant_id', '=', 'restaurants.id')->where(['restaurants.status' => 1])->groupBy('restaurants.brand_id')->pluck('brand_id')->toArray();
    //     $brandList = Brand::select('id', 'name', 'brand_type', 'brand_category', 'file_path')->where(['status' => 1])->whereIn('id', $getRestroBrandIds);

    //     if ($slug && !empty($slug)) {
    //         $brandList->Where("brands.brand_category", $slug);
    //     }

    //     if (array_key_exists("brand_category", $serachData)) {
    //         $brand_category = $serachData['brand_category'];
    //         $brandList->Where("brands.brand_category", $brand_category);
    //     }

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $brandList->Where("brands.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("brand_category", $serachData)) {
    //         $brand_category = $serachData['brand_category'];
    //         $brandList->Where("brands.brand_category", $brand_category);
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $brandList->where('main_category_id', $main_category_id);
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $brandDetail = $brandList->paginate($limit);
    //         } else {
    //             $brandList->limit($limit);
    //             $brandDetail = $brandList->get();
    //         }
    //     } else {
    //         $brandDetail = $brandList->get();
    //     }

    //     return $brandDetail;
    // }


    // public function getGift($serachData, $limit)
    // {
    //     $giftList = Gift::where(['is_active' => 1]);
    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $giftList->Where("gifts.name", 'like', '%' . $search . '%');
    //     }
    //     if ($limit != 'All') {
    //         $giftList->limit($limit);
    //     }
    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $giftList->where("gifts.category_id", $category_id);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $giftList->orderBy('gifts.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $giftList->orderBy('gifts.name', 'desc');
    //         }
    //     }
    //     $giftDetail = $giftList->get();
    //     return $giftDetail;
    // }


    // public function getGiftCategory($serachData, $limit)
    // {
    //     $giftCategoryList = Category::where(['type' => 2, 'status' => 1]);
    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $giftCategoryList->Where("categories.name", 'like', '%' . $search . '%');
    //     }
    //     if ($limit != 'All') {
    //         $giftCategoryList->limit($limit);
    //     }
    //     $giftDetail = $giftCategoryList->get();
    //     return $giftDetail;
    // }
    // public function getCelebrity($serachData, $limit)
    // {

    //     $usersList = User::select('users.*', 'celebrity_categories.name as genres_name')->leftjoin('celebrity_categories', 'celebrity_categories.id', '=', 'users.genres')->where(['users.type' => 3, 'users.status' => 1]);

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $usersList->Where("users.name", 'like', '%' . $search . '%');
    //     }
    //     if ($limit != 'All') {
    //         $usersList->limit($limit);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $usersList->orderBy('users.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $usersList->orderBy('users.name', 'desc');
    //         }
    //     }
    //     $usersDetail = $usersList->get();

    //     return $usersDetail;
    // }

    // public function getRestro($serachData, $limit, $is_featured, $is_pagination = 0)
    // {
    //     // $subQuery = "(select ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$this->radius." order by `distance` asc) t";
    //     $getProductRestroIds = '';

    //     if (array_key_exists("buy_one_get_one", $serachData)) {

    //         if (!empty($serachData['buy_one_get_one'])) {
    //             $getProductRestroIds = Products::where(['is_active' => 1, 'buy_one_get_one' => 1])->groupBy('restaurant_id')->pluck('restaurant_id')->toArray();
    //         }
    //     } else {
    //         $getProductRestroIds = Products::where('products.is_active', 1)->groupBy('restaurant_id')->pluck('restaurant_id')->toArray();
    //     }


    //     $restroList = Restaurant::select('restaurants.id', 'restaurants.main_category_id', 'restaurants.name', 'restaurants.brand_id', 'restaurants.file_path', 'restaurants.logo', 'restaurants.tag_line', 'restaurants.address', 'restaurants.latitude', 'restaurants.longitude', 'restaurants.is_open', 'restaurants.area_name', 'restaurants.dine_in_code', 'restaurants.is_kilo_points_promotor', 'restaurants.extra_kilopoints', 'restaurants.is_featured', 'restaurants.country_code', 'restaurants.phone_number', 'restaurants.landline', 'restaurants.email', 'restaurants.min_order_amount', 'restaurants.prepration_time', 'restaurants.delivery_time', 'restaurants.cancelation_charges', 'restaurants.free_delivery_min_amount', 'restaurants.delivery_charges_per_km', 'restaurants.cost_for_two_price', 'restaurants.video')->where(['restaurants.status' => 1])->whereIn('restaurants.id', $getProductRestroIds);

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $restroList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("slug", $serachData)) {
    //         $slug = $serachData['slug'];

    //         if ($slug == 'DineIn' || $slug == 'dine') {
    //             $restroIds = OrdersDetails::where(['order_type' => '1'])->join('orders', 'orders.id', '=', 'order_details.order_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();
    //         } else if ($slug == 'PickUp' || $slug == 'pickup') {
    //             $restroIds = OrdersDetails::where(['order_type' => '2'])->join('orders', 'orders.id', '=', 'order_details.order_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();
    //         }

    //         if ($slug == 'featured') {
    //             $restroList->where('is_featured', 1);
    //         }

    //         if (isset($restroIds) && !empty($restroList)) {
    //             $restroList->whereIn('restaurants.id', $restroIds);
    //         }
    //     }

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $restroList->Where("restaurants.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("is_kilo_points_promotor", $serachData)) {

    //         if ($serachData['is_kilo_points_promotor'] == 'Yes') {
    //             $restroList->Where("restaurants.is_kilo_points_promotor", 1);
    //         } else if ($serachData['is_kilo_points_promotor'] == 'No') {
    //             $restroList->Where("restaurants.is_kilo_points_promotor", 0);
    //         }
    //     }

    //     if (array_key_exists("is_open", $serachData)) {

    //         if ($serachData['is_open'] == 'Yes') {
    //             $restroList->Where("restaurants.is_open", 1);
    //         } else if ($serachData['is_open'] == 'No') {
    //             $restroList->Where("restaurants.is_open", 0);
    //         }
    //     }

    //     if (array_key_exists("cost_price_min", $serachData) && array_key_exists("cost_price_max", $serachData)) {

    //         if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) {
    //             $cost_price_min = $serachData['cost_price_min'];
    //             $cost_price_max = $serachData['cost_price_max'];
    //             $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
    //         }
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $restroList->join('products', 'products.restaurant_id', '=', 'restaurants.id')->where('products.category_id', $category_id)->groupBy('products.restaurant_id');
    //     }

    //     if (array_key_exists("service_mode", $serachData)) {
    //         $service_mode = $serachData['service_mode'];
    //         $restroList->join('restaurant_modes', 'restaurant_modes.restaurant_id', '=', 'restaurants.id')->where('restaurant_modes.mode_id', $service_mode)->groupBy('restaurant_modes.restaurant_id');
    //     }

    //     if ($is_featured == 1) {
    //         $restroList->where('is_featured', 1);
    //     }

    //     if (array_key_exists("is_featured", $serachData)) {
    //         $is_featured = $serachData['is_featured'];

    //         if ($is_featured == 'Yes') {
    //             $restroList->where('is_featured', 1);
    //         }
    //     }
    //     //defalut set latest UP

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];

    //         if ($sort == 'A_TO_Z') {
    //             $restroList->orderBy('restaurants.name', 'asc');
    //         } else if ($sort == 'Z_TO_A') {
    //             $restroList->orderBy('restaurants.name', 'desc');
    //         } else if ($sort == 'KPs') {
    //             $restroList->orderBy('restaurants.extra_kilopoints', 'desc');
    //         } else if ($sort == 'distance') {
    //             $restroList->orderBy('distance', 'asc');
    //         } else if ($sort == 'rating') {
    //             $restroList->orderBy('avg_rating', 'desc');
    //         } else {
    //             // $restroList->orderBy('restaurants.id', 'desc');
    //             $restroList->orderBy('restaurants.is_featured', 'desc');
    //         }
    //     }
    //     //  short data by date 
    //     $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {

    //             /*if (array_key_exists("sort_by",$serachData)){
    //                 $sort = $serachData['sort_by'];

    //                 if ($sort == 'distance') {
    //                     $restroList->orderBy('distance', 'asc');
    //                 } 
    //             }*/
    //             $restroDetail = $restroList->paginate($limit);
    //         } else {
    //             $restroList->limit($limit);
    //             $restroDetail = $restroList->get();
    //         }
    //     } else {
    //         $restroDetail = $restroList->get();
    //     }

    //     return $restroDetail;
    // }

    // public function getTopGiftingRestro($serachData, $limit, $is_featured = 0, $is_pagination = 0)
    // {
    //     /* $subQuery = "(select ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";*/

    //     // $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km','restaurants.cost_for_two_price')->where(['restaurants.status'=>1, 'restaurants.is_kilo_points_promotor'=>1]);
    //     $restroList = Restaurant::select('restaurants.id', 'restaurants.name', 'restaurants.brand_id', 'restaurants.file_path', 'restaurants.logo', 'restaurants.tag_line', 'restaurants.address', 'restaurants.latitude', 'restaurants.longitude', 'restaurants.is_open', 'restaurants.area_name', 'restaurants.dine_in_code', 'restaurants.is_kilo_points_promotor', 'restaurants.is_featured', 'restaurants.country_code', 'restaurants.phone_number', 'restaurants.landline', 'restaurants.email', 'restaurants.min_order_amount', 'restaurants.prepration_time', 'restaurants.delivery_time', 'restaurants.cancelation_charges', 'restaurants.free_delivery_min_amount', 'restaurants.delivery_charges_per_km', 'restaurants.cost_for_two_price')->where(['restaurants.status' => 1]);

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $restroList->Where("restaurants.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("is_kilo_points_promotor", $serachData)) {

    //         if ($serachData['is_kilo_points_promotor'] == 'Yes') {
    //             $restroList->Where("restaurants.is_kilo_points_promotor", 1);
    //         } else if ($serachData['is_kilo_points_promotor'] == 'No') {
    //             $restroList->Where("restaurants.is_kilo_points_promotor", 0);
    //         }
    //     }

    //     if (array_key_exists("is_open", $serachData)) {

    //         if ($serachData['is_open'] == 'Yes') {
    //             $restroList->Where("restaurants.is_open", 1);
    //         } else if ($serachData['is_open'] == 'No') {
    //             $restroList->Where("restaurants.is_open", 0);
    //         }
    //     }

    //     if (array_key_exists("cost_price_min", $serachData) && array_key_exists("cost_price_max", $serachData)) {

    //         if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) {
    //             $cost_price_min = $serachData['cost_price_min'];
    //             $cost_price_max = $serachData['cost_price_max'];
    //             $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
    //         }
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //     }

    //     if ($is_featured == 1) {
    //         $restroList->where('is_featured', 1);
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];

    //         if ($sort == 'A_TO_Z') {
    //             $restroList->orderBy('restaurants.name', 'asc');
    //         } else if ($sort == 'Z_TO_A') {
    //             $restroList->orderBy('restaurants.name', 'desc');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

    //             if (array_key_exists("sort_by", $serachData)) {
    //                 $sort = $serachData['sort_by'];

    //                 if ($sort == 'distance') {
    //                     $restroList->orderBy('distance', 'asc');
    //                 }
    //             }
    //             $restroDetail = $restroList->paginate($limit);
    //         } else {
    //             $restroList->limit($limit);
    //             $restroDetail = $restroList->get();
    //         }
    //     } else {
    //         $restroDetail = $restroList->get();
    //     }

    //     return $restroDetail;
    // }

    // public function getBrandRestro($serachData, $limit, $is_pagination = 0)
    // {
    //     /* $subQuery = "(select ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";*/

    //     $restroList = Restaurant::select('restaurants.id', 'restaurants.name', 'restaurants.brand_id', 'restaurants.file_path', 'restaurants.logo', 'restaurants.tag_line', 'restaurants.address', 'restaurants.latitude', 'restaurants.longitude', 'restaurants.is_open', 'restaurants.area_name', 'restaurants.dine_in_code', 'restaurants.is_kilo_points_promotor', 'restaurants.is_featured', 'restaurants.country_code', 'restaurants.phone_number', 'restaurants.landline', 'restaurants.email', 'restaurants.min_order_amount', 'restaurants.prepration_time', 'restaurants.delivery_time', 'restaurants.cancelation_charges', 'restaurants.free_delivery_min_amount', 'restaurants.delivery_charges_per_km')->join('products', 'products.restaurant_id', '=', 'restaurants.id')->where(['restaurants.status' => 1, 'restaurants.brand_id' => $serachData['brand_id']])->groupBy('products.restaurant_id');
    //     // $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km')->where(['restaurants.status'=>1, 'restaurants.brand_id'=>$serachData['brand_id']]);

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $restroList->Where("restaurants.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];

    //         if ($sort == A_TO_Z) {
    //             $restroList->orderBy('restaurants.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $restroList->orderBy('restaurants.name', 'desc');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //             $restroDetail = $restroList->paginate($limit);
    //         } else {
    //             $restroList->limit($limit);
    //             $restroDetail = $restroList->get();
    //         }
    //     } else {
    //         $restroDetail = $restroList->get();
    //     }

    //     return $restroDetail;
    // }

    // public function getDishsList($serachData, $limit, $radius = 10, $is_pagination = 0)
    // {
    //     $tag_ids = '';

    //     if (array_key_exists("tag_ids", $serachData)) {

    //         if (!empty($serachData['tag_ids'])) {
    //             $id_array = explode(",", $serachData['tag_ids']);

    //             $productTagData = ProductTags::whereIn('id', $id_array)->groupBy('product_id')->pluck('tag')->toArray();
    //             $tag_ids = ProductTags::whereIn('tag', $productTagData)->groupBy('product_id')->pluck('product_id')->toArray();
    //         }
    //     }

    //     if ($tag_ids) {
    //         $productsList = Products::select('products.id', 'products.name', 'products.long_description', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.shop_type', 'products.delivery_time', 'products.delivery_hours', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id', 'restaurants.latitude', 'restaurants.longitude')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->priceFilter($serachData)->KPFilter($serachData)->whereIn('products.id', $tag_ids)->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
    //     } else {
    //         $productsList = Products::select('products.id', 'products.name', 'products.long_description', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.shop_type', 'products.delivery_time', 'products.delivery_hours', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id', 'restaurants.latitude', 'restaurants.longitude')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->priceFilter($serachData)->KPFilter($serachData)->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
    //     }


    //     if (array_key_exists("latitude", $serachData)) {
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $productsList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("brand_id", $serachData)) {
    //         $brand_id = $serachData['brand_id'];
    //         $productsList->where('restaurants.brand_id', $brand_id);
    //     }

    //     if (array_key_exists("search_text", $serachData)) {

    //         if (!empty($serachData['search_text'])) {
    //             $search = $serachData['search_text'];
    //             $productsList->Where("products.name", 'like', '%' . $search . '%');
    //         }
    //     }

    //     if (array_key_exists("restaurant_id", $serachData)) {

    //         if (!empty($serachData['restaurant_id'])) {
    //             $restaurant_id = $serachData['restaurant_id'];
    //             $productsList->Where("products.restaurant_id", $restaurant_id);
    //         }
    //     }

    //     if (array_key_exists("buy_one_get_one", $serachData)) {

    //         if (!empty($serachData['buy_one_get_one'])) {
    //             $buy_one_get_one = $serachData['buy_one_get_one'];
    //             $productsList->Where("products.buy_one_get_one", $buy_one_get_one);
    //         }
    //     }

    //     if (array_key_exists("category_id", $serachData)) {

    //         if (!empty($serachData['category_id'])) {
    //             $category_id = $serachData['category_id'];
    //             $productsList->Where("products.category_id", $category_id);
    //         }
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("restaurant_id", $serachData)) {
    //         $restaurant_id = $serachData['restaurant_id'];
    //         $productsList->Where("products.restaurant_id", $restaurant_id);
    //     }

    //     if (array_key_exists("dish_type", $serachData)) {

    //         if (!empty($serachData['dish_type'])) {
    //             $dish_type = $serachData['dish_type'];

    //             if ($dish_type != 'Both') {
    //                 $productsList->Where("products.products_type", $dish_type);
    //             }
    //         }
    //     }

    //     /*if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) { 
    //         $min_price = $serachData['min_price'];
    //         $max_price = $serachData['max_price'];
    //         $productsList->whereBetween('products.price', [$min_price, $max_price]);
    //     }*/

    //     /*if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
    //         $min_kilo = $serachData['min_kilo'];
    //         $max_kilo = $serachData['max_kilo'];
    //         $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
    //     }*/

    //     if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) {
    //         $min_discount = $serachData['min_discount'];
    //         $max_discount = $serachData['max_discount'];
    //         $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
    //     }

    //     if (isset($serachData['price']) && !empty($serachData['price'])) {

    //         if ($serachData['price'] == 'LTH') {
    //             $productsList->orderBy('products.price', 'asc');
    //         }

    //         if ($serachData['price'] == 'HTL') {
    //             $productsList->orderBy('products.price', 'desc');
    //         }
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         } else if ($sort == 'Newest') {
    //             $productsList->orderBy('products.id', 'desc');
    //         }
    //     }
    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }
    //     return $productsDetail;
    // }

    // public function getFavDishsList($serachData, $limit, $radius = 10, $is_pagination = 0, $favIds = null)
    // {
    //     $productsList = Products::select('products.id', 'products.name', 'products.long_description', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.points', 'products.restaurant_id', 'products.buy_one_get_one', 'categories_lang.name as category')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->where('categories_lang.lang', App::getLocale())->whereIn('products.id', $favIds)->where('products.is_active', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $productsList = $productsList->where(['restaurants.main_category_id' => $serachData['main_category_id']]);
    //     }

    //     if (array_key_exists("latitude", $serachData)) {
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }
    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     }
    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }
    //     return $productsDetail;
    // }

    // public function getDish($serachData, $limit, $radius = 10, $is_pagination = 0)
    // {
    //     $productsList = Products::select('products.id', 'products.name', 'products.long_description', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id', DB::raw('(IFNULL(products.points,0) + IFNULL(products.extra_kilopoints,0)) as totalCalKP'))->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
    //     // $productsList = Products::with('Restaurant')->where('products.is_active',1);

    //     if (array_key_exists("slug", $serachData)) {
    //         $slug = $serachData['slug'];

    //         if ($slug == 'top-pickup-dishes') {
    //             $pickupProductsIds = OrdersDetails::where(['order_type' => '2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders', 'orders.id', '=', 'order_details.order_id')->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
    //             $productsList->whereIn('products.id', $pickupProductsIds);
    //         }
    //     }

    //     if (array_key_exists("latitude", $serachData)) {
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("brand_id", $serachData)) {
    //         $brand_id = $serachData['brand_id'];
    //         $productsList->where('restaurants.brand_id', $brand_id);
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $productsList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("restaurant_id", $serachData)) {
    //         $restaurant_id = $serachData['restaurant_id'];
    //         $productsList->Where("products.restaurant_id", $restaurant_id);
    //     }

    //     if (array_key_exists("dish_type", $serachData)) {

    //         if (!empty($serachData['dish_type'])) {
    //             $dish_type = $serachData['dish_type'];

    //             if ($dish_type != 'Both') {
    //                 $productsList->Where("products.products_type", $dish_type);
    //             }
    //         }
    //     }

    //     if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) {
    //         $min_price = $serachData['min_price'];
    //         $max_price = $serachData['max_price'];
    //         $productsList->whereBetween('products.price', [$min_price, $max_price]);
    //     }

    //     if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) {
    //         $min_kilo = $serachData['min_kilo'];
    //         $max_kilo = $serachData['max_kilo'];
    //         $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
    //     }

    //     if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) {
    //         $min_discount = $serachData['min_discount'];
    //         $max_discount = $serachData['max_discount'];
    //         $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
    //     }

    //     if (isset($serachData['price']) && !empty($serachData['price'])) {

    //         if ($serachData['price'] == 'LTH') {
    //             $productsList->orderBy('products.price', 'asc');
    //         }

    //         if ($serachData['price'] == 'HTL') {
    //             $productsList->orderBy('products.price', 'desc');
    //         }
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }

    //     if (array_key_exists("slug", $serachData)) {
    //         $slug = $serachData['slug'];

    //         if ($slug == 'top_gift') {
    //             $productsList->orderBy('totalCalKP', 'desc');
    //         }
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         } else if ($sort == 'top_gift') {
    //             $productsList->orderBy('totalCalKP', 'desc');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }
    //     /*if($limit != 'All'){
    //         $productsList->limit($limit);
    //     }*/
    //     // $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getHotDish($serachData, $limit, $radius = 10, $is_pagination = 0)
    // {
    //     $productsList = Products::select('products.id', 'products.name', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
    //     // $productsList = Products::with('Restaurant')->where('products.is_active',1);

    //     if (array_key_exists("slug", $serachData)) {
    //         $slug = $serachData['slug'];

    //         if ($slug == 'top-pickup-dishes') {
    //             $pickupProductsIds = OrdersDetails::where(['order_type' => '2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders', 'orders.id', '=', 'order_details.order_id')->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
    //             $productsList->whereIn('products.id', $pickupProductsIds);
    //         }
    //     }

    //     /*if (array_key_exists("latitude",$serachData)){ 
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }*/

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("brand_id", $serachData)) {
    //         $brand_id = $serachData['brand_id'];
    //         $productsList->where('restaurants.brand_id', $brand_id);
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $productsList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("restaurant_id", $serachData)) {
    //         $restaurant_id = $serachData['restaurant_id'];
    //         $productsList->Where("products.restaurant_id", $restaurant_id);
    //     }

    //     if (array_key_exists("dish_type", $serachData)) {

    //         if (!empty($serachData['dish_type'])) {
    //             $dish_type = $serachData['dish_type'];
    //             $productsList->Where("products.products_type", $dish_type);
    //         }
    //     }

    //     if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) {
    //         $min_price = $serachData['min_price'];
    //         $max_price = $serachData['max_price'];
    //         $productsList->whereBetween('products.price', [$min_price, $max_price]);
    //     }

    //     if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) {
    //         $min_kilo = $serachData['min_kilo'];
    //         $max_kilo = $serachData['max_kilo'];
    //         $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
    //     }

    //     if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) {
    //         $min_discount = $serachData['min_discount'];
    //         $max_discount = $serachData['max_discount'];
    //         $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
    //     }

    //     if (isset($serachData['price']) && !empty($serachData['price'])) {

    //         if ($serachData['price'] == 'LTH') {
    //             $productsList->orderBy('products.price', 'asc');
    //         }

    //         if ($serachData['price'] == 'HTL') {
    //             $productsList->orderBy('products.price', 'desc');
    //         }
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     }

    //     if (isset($serachData['main_category_id']) && !empty($serachData['main_category_id'])) {

    //         if ($serachData['main_category_id'] != 5 && $serachData['main_category_id'] != 6) {
    //             $productsDetail = $productsList->groupBy('products.restaurant_id');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }
    //     /*if($limit != 'All'){
    //         $productsList->limit($limit);
    //     }*/
    //     // $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getTopPickupsDish($serachData, $limit, $is_pagination = 0)
    // {
    //     $pickupProductsIds = OrdersDetails::where(['order_type' => '2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders', 'orders.id', '=', 'order_details.order_id')->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
    //     // $productsList = Products::with('Restaurant')->where('products.is_active',1)->whereIn('products.id',$pickupProductsIds);

    //     $productsList = Products::select('products.id', 'products.name', 'products.long_description', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.shop_type', 'products.delivery_time', 'products.delivery_hours', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id', 'restaurants.latitude', 'restaurants.longitude')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number')->whereIn('products.id', $pickupProductsIds);

    //     if (array_key_exists("latitude", $serachData)) {
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("brand_id", $serachData)) {
    //         $brand_id = $serachData['brand_id'];
    //         $productsList->where('restaurants.brand_id', $brand_id);
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $productsList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("restaurant_id", $serachData)) {
    //         $restaurant_id = $serachData['restaurant_id'];
    //         $productsList->Where("products.restaurant_id", $restaurant_id);
    //     }

    //     if (array_key_exists("dish_type", $serachData)) {

    //         if (!empty($serachData['dish_type'])) {
    //             $dish_type = $serachData['dish_type'];
    //             $productsList->Where("products.products_type", $dish_type);
    //         }
    //     }

    //     if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) {
    //         $min_price = $serachData['min_price'];
    //         $max_price = $serachData['max_price'];
    //         $productsList->whereBetween('products.price', [$min_price, $max_price]);
    //     }

    //     if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) {
    //         $min_kilo = $serachData['min_kilo'];
    //         $max_kilo = $serachData['max_kilo'];
    //         $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
    //     }

    //     if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) {
    //         $min_discount = $serachData['min_discount'];
    //         $max_discount = $serachData['max_discount'];
    //         $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
    //     }

    //     if (isset($serachData['price']) && !empty($serachData['price'])) {

    //         if ($serachData['price'] == 'LTH') {
    //             $productsList->orderBy('products.price', 'asc');
    //         }

    //         if ($serachData['price'] == 'HTL') {
    //             $productsList->orderBy('products.price', 'desc');
    //         }
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }

    //     /*if($limit != 'All'){
    //         $productsList->limit($limit);
    //     }*/
    //     // $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getTopDineInDish($serachData, $limit)
    // {
    //     $dineInProductsIds = OrdersDetails::where(['order_type' => '1'])->join('orders', 'orders.id', '=', 'order_details.order_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
    //     $productsList = Products::with('Restaurant')->where('products.is_active', 1)->whereIn('products.id', $dineInProductsIds);

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     }
    //     if ($limit != 'All') {
    //         $productsList->limit($limit);
    //     }
    //     $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getTopDishs($serachData, $limit, $is_pagination = 0)
    // {
    //     $dineInProductsIds = OrdersDetails::where('order_status', '!=', 'Cancel')->join('orders', 'orders.id', '=', 'order_details.order_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
    //     // $productsList = Products::with('Restaurant')->where('products.is_active',1)->whereIn('products.id',$dineInProductsIds);

    //     $productsList = Products::select('products.id', 'products.name', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.shop_type', 'products.delivery_time', 'products.delivery_hours', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id', 'restaurants.latitude', 'restaurants.longitude')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number')->whereIn('products.id', $dineInProductsIds);

    //     if (array_key_exists("latitude", $serachData)) {
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $productsList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }
    //     /*if($limit != 'All'){
    //         $productsList->limit($limit);
    //     }*/
    //     // $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getTopGiftingItems($serachData, $limit, $is_pagination = 0)
    // {
    //     $productsList = Products::select('products.id', 'products.name', 'products.long_description', 'products.category_id', 'products.main_image', 'products.products_type', 'products.price', 'products.video', 'products.out_of_stock', 'products.serve', 'products.points', 'products.extra_kilopoints', 'products.restaurant_id', 'products.product_for', 'products.buy_one_get_one', 'categories_lang.name as category', 'restaurants.main_category_id', 'restaurants.brand_id', DB::raw('(IFNULL(products.points,0) + IFNULL(products.extra_kilopoints,0)) as totalCalKP'))->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->join('categories_lang', 'categories_lang.category_id', '=', 'products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active', 1)->where('restaurants.status', 1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
    //     // $productsList = Products::with('Restaurant')->where('products.is_active',1);

    //     if (array_key_exists("slug", $serachData)) {
    //         $slug = $serachData['slug'];

    //         if ($slug == 'top-pickup-dishes') {
    //             $pickupProductsIds = OrdersDetails::where(['order_type' => '2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders', 'orders.id', '=', 'order_details.order_id')->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
    //             $productsList->whereIn('products.id', $pickupProductsIds);
    //         }
    //     }

    //     if (array_key_exists("latitude", $serachData)) {
    //         $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
    //     }

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("brand_id", $serachData)) {
    //         $brand_id = $serachData['brand_id'];
    //         $productsList->where('restaurants.brand_id', $brand_id);
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("sub_category_id", $serachData)) {

    //         if (!empty($serachData['sub_category_id'])) {
    //             $sub_category_id = $serachData['sub_category_id'];
    //             $productsList->whereIn("products.sub_category_id", $sub_category_id);
    //         }
    //     }

    //     if (array_key_exists("main_category_id", $serachData)) {
    //         $main_category_id = $serachData['main_category_id'];
    //         $productsList->where('restaurants.main_category_id', $main_category_id);
    //     }

    //     if (array_key_exists("restaurant_id", $serachData)) {
    //         $restaurant_id = $serachData['restaurant_id'];
    //         $productsList->Where("products.restaurant_id", $restaurant_id);
    //     }

    //     if (array_key_exists("dish_type", $serachData)) {

    //         if (!empty($serachData['dish_type'])) {
    //             $dish_type = $serachData['dish_type'];
    //             $productsList->Where("products.products_type", $dish_type);
    //         }
    //     }

    //     if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) {
    //         $min_price = $serachData['min_price'];
    //         $max_price = $serachData['max_price'];
    //         $productsList->whereBetween('products.price', [$min_price, $max_price]);
    //     }

    //     if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) {
    //         $min_kilo = $serachData['min_kilo'];
    //         $max_kilo = $serachData['max_kilo'];
    //         $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
    //     }

    //     if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) {
    //         $min_discount = $serachData['min_discount'];
    //         $max_discount = $serachData['max_discount'];
    //         $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
    //     }

    //     if (isset($serachData['price']) && !empty($serachData['price'])) {

    //         if ($serachData['price'] == 'LTH') {
    //             $productsList->orderBy('products.price', 'asc');
    //         }

    //         if ($serachData['price'] == 'HTL') {
    //             $productsList->orderBy('products.price', 'desc');
    //         }
    //     }

    //     /*if(array_key_exists("celebrity_id",$serachData)){    
    //         $celebrity_id = $serachData['celebrity_id']; 
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }*/

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];

    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     } else {
    //         $productsList->orderBy('totalCalKP', 'desc');
    //     }

    //     // $productsList->orderBy('products.points', 'desc');

    //     if (isset($serachData['main_category_id']) && !empty($serachData['main_category_id'])) {

    //         if ($serachData['main_category_id'] != 5 && $serachData['main_category_id'] != 6) {
    //             $productsDetail = $productsList->groupBy('products.restaurant_id');
    //         }
    //     }

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {
    //             $productsDetail = $productsList->paginate($limit);
    //         } else {
    //             $productsList->limit($limit);
    //             $productsDetail = $productsList->get();
    //         }
    //     } else {
    //         $productsDetail = $productsList->get();
    //     }
    //     /*if($limit != 'All'){
    //         $productsList->limit($limit);
    //     }*/
    //     // $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getTopRestrosBySlug($serachData, $limit, $slug, $is_pagination = 0)
    // {

    //     if ($slug == 'DineIn') {
    //         $restroIds = OrdersDetails::where(['order_type' => '1', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders', 'orders.id', '=', 'order_details.order_id')->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();
    //     } else if ($slug == 'PickUp') {
    //         $restroIds = OrdersDetails::where(['order_type' => '2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders', 'orders.id', '=', 'order_details.order_id')->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();
    //     }

    //     $restroList = Restaurant::select('restaurants.id', 'restaurants.name', 'restaurants.brand_id', 'restaurants.file_path', 'restaurants.logo', 'restaurants.tag_line', 'restaurants.address', 'restaurants.latitude', 'restaurants.longitude', 'restaurants.is_open', 'restaurants.area_name', 'restaurants.dine_in_code', 'restaurants.is_kilo_points_promotor', 'restaurants.extra_kilopoints', 'restaurants.is_featured', 'restaurants.country_code', 'restaurants.phone_number', 'restaurants.landline', 'restaurants.email', 'restaurants.min_order_amount', 'restaurants.prepration_time', 'restaurants.delivery_time', 'restaurants.cancelation_charges', 'restaurants.free_delivery_min_amount', 'restaurants.delivery_charges_per_km', 'restaurants.cost_for_two_price')->where(['restaurants.status' => 1])->whereIn('restaurants.id', $restroIds);

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $restroList->Where("restaurants.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("is_kilo_points_promotor", $serachData)) {

    //         if ($serachData['is_kilo_points_promotor'] == 'Yes') {
    //             $restroList->Where("restaurants.is_kilo_points_promotor", 1);
    //         } else if ($serachData['is_kilo_points_promotor'] == 'No') {
    //             $restroList->Where("restaurants.is_kilo_points_promotor", 0);
    //         }
    //     }

    //     if (array_key_exists("is_open", $serachData)) {

    //         if ($serachData['is_open'] == 'Yes') {
    //             $restroList->Where("restaurants.is_open", 1);
    //         } else if ($serachData['is_open'] == 'No') {
    //             $restroList->Where("restaurants.is_open", 0);
    //         }
    //     }

    //     if (array_key_exists("cost_price_min", $serachData) && array_key_exists("cost_price_max", $serachData)) {

    //         if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) {
    //             $cost_price_min = $serachData['cost_price_min'];
    //             $cost_price_max = $serachData['cost_price_max'];
    //             $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
    //         }
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $restroList->join('products', 'products.restaurant_id', '=', 'restaurants.id')->where('products.category_id', $category_id)->groupBy('products.restaurant_id');
    //     }

    //     if (array_key_exists("service_mode", $serachData)) {
    //         $service_mode = $serachData['service_mode'];
    //         $restroList->join('restaurant_modes', 'restaurant_modes.restaurant_id', '=', 'restaurants.id')->where('restaurant_modes.mode_id', $service_mode)->groupBy('restaurant_modes.restaurant_id');
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];

    //         if ($sort == 'A_TO_Z') {
    //             $restroList->orderBy('restaurants.name', 'asc');
    //         } else if ($sort == 'Z_TO_A') {
    //             $restroList->orderBy('restaurants.name', 'desc');
    //         } else if ($sort == 'KPs') {
    //             $restroList->orderBy('restaurants.extra_kilopoints', 'desc');
    //         }
    //     }
    //     $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {

    //             if (array_key_exists("sort_by", $serachData)) {
    //                 $sort = $serachData['sort_by'];

    //                 if ($sort == 'distance') {
    //                     $restroList->orderBy('distance', 'asc');
    //                 }
    //             }
    //             $restroDetail = $restroList->paginate($limit);
    //         } else {
    //             $restroList->limit($limit);
    //             $restroDetail = $restroList->get();
    //         }
    //     } else {
    //         $restroDetail = $restroList->get();
    //     }

    //     return $restroDetail;
    // }

    // public function getBOGORestros($serachData, $limit, $is_pagination = 0)
    // {
    //     $getAllBoGoRestroIds = Products::where(['is_active' => 1, 'buy_one_get_one' => 1])->groupBy('restaurant_id')->pluck('restaurant_id')->toArray();

    //     $restroList = Restaurant::select('restaurants.id', 'restaurants.name', 'restaurants.brand_id', 'restaurants.file_path', 'restaurants.logo', 'restaurants.tag_line', 'restaurants.address', 'restaurants.latitude', 'restaurants.longitude', 'restaurants.is_open', 'restaurants.area_name', 'restaurants.dine_in_code', 'restaurants.is_kilo_points_promotor', 'restaurants.extra_kilopoints', 'restaurants.is_featured', 'restaurants.country_code', 'restaurants.phone_number', 'restaurants.landline', 'restaurants.email', 'restaurants.min_order_amount', 'restaurants.prepration_time', 'restaurants.delivery_time', 'restaurants.cancelation_charges', 'restaurants.free_delivery_min_amount', 'restaurants.delivery_charges_per_km', 'restaurants.cost_for_two_price')->where(['restaurants.status' => 1])->whereIn('restaurants.id', $getAllBoGoRestroIds);

    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $restroList->Where("restaurants.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("cost_price_min", $serachData) && array_key_exists("cost_price_max", $serachData)) {

    //         if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) {
    //             $cost_price_min = $serachData['cost_price_min'];
    //             $cost_price_max = $serachData['cost_price_max'];
    //             $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
    //         }
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $restroList->join('products', 'products.restaurant_id', '=', 'restaurants.id')->where('products.category_id', $category_id)->groupBy('products.restaurant_id');
    //     }

    //     if (array_key_exists("service_mode", $serachData)) {
    //         $service_mode = $serachData['service_mode'];
    //         $restroList->join('restaurant_modes', 'restaurant_modes.restaurant_id', '=', 'restaurants.id')->where('restaurant_modes.mode_id', $service_mode)->groupBy('restaurant_modes.restaurant_id');
    //     }

    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];

    //         if ($sort == 'A_TO_Z') {
    //             $restroList->orderBy('restaurants.name', 'asc');
    //         } else if ($sort == 'Z_TO_A') {
    //             $restroList->orderBy('restaurants.name', 'desc');
    //         } else if ($sort == 'KPs') {
    //             $restroList->orderBy('restaurants.extra_kilopoints', 'desc');
    //         }
    //     }
    //     $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

    //     if ($limit != 'All') {

    //         if ($is_pagination == 1) {

    //             if (array_key_exists("sort_by", $serachData)) {
    //                 $sort = $serachData['sort_by'];

    //                 if ($sort == 'distance') {
    //                     $restroList->orderBy('distance', 'asc');
    //                 }
    //             }
    //             $restroDetail = $restroList->paginate($limit);
    //         } else {
    //             $restroList->limit($limit);
    //             $restroDetail = $restroList->get();
    //         }
    //     } else {
    //         $restroDetail = $restroList->get();
    //     }

    //     return $restroDetail;
    // }

    // public function getDishLatAndLong($serachData, $limit, $radius, $is_pagination = 0)
    // {
    //     $subQuery = "(select id, name, address, latitude, longitude ,
    //     ( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(" . $serachData['longitude'] . ")
    //     ) + sin( radians(" . $serachData['latitude'] . ") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < " . $radius . " order by `distance` asc) t";

    //     $productsList =  Products::select('products.*', 't.distance')->with('User')->with('Cart')
    //         ->join('product_assign_to_chef', 'product_assign_to_chef.product_id', '=', 'products.id')
    //         ->join('users', 'users.id', '=', 'products.celebrity_id')
    //         ->join(DB::raw($subQuery), 't.id', '=', 'product_assign_to_chef.chef_id')
    //         ->groupBy('products.id')
    //         ->where('products.is_active', 1);


    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }
    //     if ($limit != 'All') {
    //         $productsList->limit($limit);
    //     }
    //     $productsDetail = $productsList->get();
    //     return $productsDetail;
    // }

    // public function getDishLatAndLongCategory($serachData, $limit, $radius)
    // {
    //     $subQuery = "(select id, name, address, latitude, longitude ,
    //     ( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(" . $serachData['longitude'] . ")
    //     ) + sin( radians(" . $serachData['latitude'] . ") ) * sin( radians( latitude ) ) ) ) AS distance from `users` WHERE type = 2 AND STATUS  = 1 HAVING `distance` < " . $radius . " order by `distance` asc) t";

    //     $productsList =  Products::select('products.*', 't.distance')->with('User')
    //         ->join('product_assign_to_chef', 'product_assign_to_chef.product_id', '=', 'products.id')
    //         ->join('users', 'users.id', '=', 'products.celebrity_id')
    //         ->join(DB::raw($subQuery), 't.id', '=', 'product_assign_to_chef.chef_id')
    //         ->groupBy('products.id')
    //         ->where('products.is_active', 1);


    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }

    //     if (array_key_exists("celebrity_id", $serachData)) {
    //         $celebrity_id = $serachData['celebrity_id'];
    //         $productsList->Where("products.celebrity_id", $celebrity_id);
    //     }
    //     if ($limit != 'All') {
    //         $productsList->limit($limit);
    //     }
    //     $productsDetail = $productsList->paginate(1);
    //     return $productsDetail;
    // }

    // private function findNearestChaf($serachData, $limit, $radius)
    // {

    //     $subQuery = "(select id, name, address, latitude, longitude ,
    //                 ( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(" . $serachData['longitude'] . ")
    //                 ) + sin( radians(" . $serachData['latitude'] . ") ) * sin( radians( latitude ) ) ) ) AS distance from `users` WHERE type = 2 AND STATUS  = 1 HAVING `distance` < " . $radius . " order by `distance` asc) t";

    //     //        $test = "SELECT products.* from products 
    //     //        inner join product_assign_to_chef on (product_assign_to_chef.product_id = products.id) 
    //     //        inner join (select id, name, address, latitude, longitude ,
    //     //        ( 6371 * acos( cos( radians(26.922070) ) *
    //     //        cos( radians( latitude ) )
    //     //        * cos( radians( longitude ) - radians(75.778885)
    //     //        ) + sin( radians(26.922070) ) *
    //     //        sin( radians( latitude ) ) )
    //     //        ) AS distance from `users` WHERE type = 2 AND STATUS  = 1 HAVING `distance` < 10 order by `distance` asc
    //     // ) t ON (t.id =product_assign_to_chef.chef_id) 
    //     // group by products.id";

    //     $productsList =  Products::select('products.*', 't.distance')->with('User')
    //         ->join('product_assign_to_chef', 'product_assign_to_chef.product_id', '=', 'products.id')
    //         ->join('users', 'users.id', '=', 'products.celebrity_id')
    //         ->join(DB::raw($subQuery), 't.id', '=', 'product_assign_to_chef.chef_id')
    //         ->groupBy('products.id')
    //         ->where('products.is_active', 1);


    //     if (array_key_exists("search_text", $serachData)) {
    //         $search = $serachData['search_text'];
    //         $productsList->Where("products.name", 'like', '%' . $search . '%');
    //     }

    //     if (array_key_exists("category_id", $serachData)) {
    //         $category_id = $serachData['category_id'];
    //         $productsList->Where("products.category_id", $category_id);
    //     }
    //     if (array_key_exists("sort_by", $serachData)) {
    //         $sort = $serachData['sort_by'];
    //         if ($sort == A_TO_Z) {
    //             $productsList->orderBy('products.name', 'asc');
    //         } else if ($sort == Z_TO_A) {
    //             $productsList->orderBy('products.name', 'desc');
    //         }
    //     }
    //     if ($limit != 'All') {
    //         $productsList->limit($limit);
    //     }
    //     $productsDetail = $productsList->get();

    //     // $restaurants = User::selectRaw("id, name, address, latitude, longitude ,
    //     //                 ( 6371 * acos( cos( radians(?) ) *
    //     //                 cos( radians( latitude ) )
    //     //                 * cos( radians( longitude ) - radians(?)
    //     //                 ) + sin( radians(?) ) *
    //     //                 sin( radians( latitude ) ) )
    //     //                 ) AS distance", [$latitude, $longitude, $latitude])
    //     //     ->where('status', '=', 1)
    //     //     ->having("distance", "<", $radius)
    //     //     ->orderBy("distance",'asc')
    //     //     ->limit(20)
    //     //     ->get();

    //     // echo '<pre>';
    //     // print_r(DB::getQueryLog());
    //     // die;
    //     return $productsList;
    // }
    // public function getDishDetail($queryString)
    // {
    //     $productsList = Products::select('products.*', 'restaurants.main_category_id')->with('ProductImages')->join('restaurants', 'restaurants.id', '=', 'products.restaurant_id')->where('products.is_active', 1)->where("products.id", $queryString['product_id']);

    //     /*if(array_key_exists("product_id",$queryString)){    
    //         $product_id = $queryString['product_id']; 
    // 		$productsList->Where("products.id",$product_id);
    //     }*/

    //     $productsDetail = $productsList->first();
    //     return $productsDetail;
    // }

    // public function favoriteDish(Request $request)
    // {
    //     $inputData = $request->all();
    //     $userData = auth()->user();
    //     $userId =  $userData->id;

    //     $validator = Validator::make($request->all(), [
    //         'dish_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         $response['status'] = 0;
    //         $response['message'] = $errors;
    //         return response()->json($response, 200);
    //     } else {
    //         $data = Favorite::where(['type_id' => $inputData['dish_id'], 'user_id' => $userId, 'type' => 'Dish'])->first();

    //         if (!isset($data)) {
    //             $data = new Favorite;
    //             $data->type_id = $inputData['dish_id'];
    //             $data->user_id = $userId;
    //             $data->type = 'Dish';
    //             // $data->save();

    //             if ($data->save()) {
    //                 $response['status'] = 1;
    //                 $response['message'] = 'Dish favorited successfully';
    //             } else {
    //                 $response['status'] = 0;
    //                 $response['message'] = 'Error Occured.';
    //             }
    //         } else {
    //             Favorite::where(['type_id' => $inputData['dish_id'], 'user_id' => $userId, 'type' => 'Dish'])->delete();

    //             $response['status'] = 1;
    //             $response['message'] = 'Dish Unfavorited successfully';
    //         }

    //         return response()->json($response, 200);
    //     }
    // }

    // public function favoriteListing(Request $request)
    // {
    //     $inputData = $request->all();
    //     $userData = auth()->user();
    //     $userId =  $userData->id;
    //     $limit =  20;
    //     $radius = 10;
    //     $is_pagination = 1;
    //     $favIds = Favorite::where(['user_id' => $userId, 'type' => 'Dish'])->pluck('type_id')->toArray();
    //     $getDish = $this->getFavDishsList($inputData, $limit, $radius, $is_pagination, $favIds);

    //     if (count($getDish)) {

    //         foreach ($getDish as $key => $value) {
    //             /*$toppings = Topping::where(['dish_id'=>$value->id, 'status'=>1])->count();

    //                 if ($toppings) {
    //                     $value->is_topping = 1;

    //                 } else {
    //                     $value->is_topping = 0;
    //                 }*/

    //             $value->qty = (int)Cart::where(['user_id' => $userId, 'product_id' => $value->id])->sum('qty');

    //             $mandatory_price = Topping::where(['dish_id' => $value->id, 'is_mandatory' => 1, 'status' => 1])->sum('price');

    //             $value->price = $mandatory_price + $value->price;

    //             $attributes = ToppingCategory::select('toppings_category.id', 'toppings_category.name', 'toppings_category.topping_choose', 'dish_toppings.dish_id')->join('dish_toppings', 'dish_toppings.topping_category_id', '=', 'toppings_category.id')->where(['dish_id' => $value->id, 'topping_choose' => 0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
    //                 $query->where('dish_toppings.dish_id', $value->id);
    //             }))->get();

    //             $add_on = ToppingCategory::select('toppings_category.id', 'toppings_category.name', 'toppings_category.topping_choose', 'dish_toppings.dish_id')->join('dish_toppings', 'dish_toppings.topping_category_id', '=', 'toppings_category.id')->where(['dish_id' => $value->id, 'topping_choose' => 1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
    //                 $query->where('dish_toppings.dish_id', $value->id);
    //             }))->get();

    //             $product_attributes = ProductAttributeValues::select('product_attribute_values.id', 'product_attribute_values.attributes_lang_id', 'attributes_lang.name as attribute_name', 'attributes_lang.topping_choose')->where(['product_id' => $value->id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attributes_lang', 'attributes_lang.id', '=', 'product_attribute_values.attributes_lang_id')->groupBy('product_attribute_values.attributes_lang_id')->get();

    //             if ($product_attributes) {

    //                 if (count($product_attributes) > 0) {
    //                     $value->is_topping = 1;
    //                 } else {
    //                     $value->is_topping = 0;
    //                 }

    //                 foreach ($product_attributes as $k => $v) {
    //                     $v->attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id', 'product_attribute_values.id as product_attribute_values_id')->where(['product_id' => $value->id, 'product_attribute_values.attributes_lang_id' => $v->attributes_lang_id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->get();
    //                 }
    //             } else {
    //                 $value->is_topping = 0;
    //             }

    //             $value->product_attributes = $product_attributes;
    //             $value->attributes = $attributes;
    //             $value->add_on = $add_on;
    //             $value->avg_rating = number_format(0, 1);
    //             $value->distance = number_format($value->distance, 2) . ' KM';
    //         }
    //         $response['status'] = 1;
    //         $response['data'] = $getDish;
    //     } else {
    //         $response['status'] = 0;
    //         $response['message'] = 'No data found.';
    //     }
    //     return response()->json($response, 200);
    // }

    // public function getDishToppings(Request $request)
    // {
    //     $inputData = $request->all();
    //     $userData = auth()->user();
    //     $userId =  $userData->id;

    //     $validator = Validator::make($request->all(), [
    //         'dish_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         $response['status'] = 0;
    //         $response['message'] = $errors;
    //         return response()->json($response, 200);
    //     } else {
    //         $attributes = ToppingCategory::select('toppings_category.id', 'toppings_category.name', 'toppings_category.topping_choose', 'dish_toppings.dish_id')->join('dish_toppings', 'dish_toppings.topping_category_id', '=', 'toppings_category.id')->where(['dish_id' => $inputData['dish_id'], 'topping_choose' => 0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
    //             $query->where('dish_toppings.dish_id', $inputData['dish_id'])->where('dish_toppings.status', 1);
    //         }))->get();

    //         $add_on = ToppingCategory::select('toppings_category.id', 'toppings_category.name', 'toppings_category.topping_choose', 'dish_toppings.dish_id')->join('dish_toppings', 'dish_toppings.topping_category_id', '=', 'toppings_category.id')->where(['dish_id' => $inputData['dish_id'], 'topping_choose' => 1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
    //             $query->where('dish_toppings.dish_id', $inputData['dish_id'])->where('dish_toppings.status', 1);
    //         }))->get();

    //         /*$attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Change-Org-Price'])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
    //                 $query->where('dish_toppings.dish_id', $inputData['dish_id']);
    //             }))->get();

    //         $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Add-On'])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
    //                 $query->where('dish_toppings.dish_id', $inputData['dish_id']);
    //             }))->get();*/

    //         /* $attributes = Topping::select('dish_toppings_lang.name','dish_toppings.price','dish_toppings.price_reflect_on')->join('dish_toppings_lang','dish_toppings_lang.dish_topping_id','=','dish_toppings.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Change-Org-Price'])->where('dish_toppings_lang.lang', App::getLocale())->get();

    //         $add_on = Topping::select('dish_toppings_lang.name','dish_toppings.price','dish_toppings.price_reflect_on')->join('dish_toppings_lang','dish_toppings_lang.dish_topping_id','=','dish_toppings.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Add-On'])->where('dish_toppings_lang.lang', App::getLocale())->get();*/

    //         if (!isset($attributes) && !isset($add_on)) {
    //             $response['status'] = 0;
    //             $response['message'] = 'No toppings found.';
    //         } else {
    //             $response['status'] = 1;
    //             $response['data']['attributes'] = $attributes;
    //             $response['data']['add_on'] = $add_on;
    //         }

    //         return response()->json($response, 200);
    //     }
    // }

    // public function applyDiscount(Request $request)
    // {
    //     $inputData = $request->all();
    //     $userData = auth()->user();
    //     $userId =  $userData->id;

    //     $validator = Validator::make($request->all(), [
    //         'discount_code' => 'required',
    //         'parent_cart_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         $response['status'] = 0;
    //         $response['message'] = $errors;
    //         return response()->json($response, 200);
    //     } else {
    //         $data = Discount::where(['discount_code' => $inputData['discount_code']])->first();

    //         if ($data) {
    //             //Remove Old apply coupon
    //             $cartParentDetail = CartParent::where(['id' => $inputData['parent_cart_id'], 'user_id' => $userId])->first();

    //             if ($cartParentDetail) {

    //                 if ($cartParentDetail->discount_code) {
    //                     /*$response['status'] = 0;
    //                     $response['message'] = 'Please remove already applied coupon.';*/
    //                     $updateCartParent = [
    //                         'discount_code' => null,
    //                         'discount_percent' => null,
    //                         'discount_amount' => null,
    //                         'amount' => $cartParentDetail->amount + $cartParentDetail->discount_amount,
    //                     ];
    //                     $cartParentDetail->update($updateCartParent);
    //                 }
    //             }

    //             $cartParentDetail = CartParent::where(['id' => $inputData['parent_cart_id'], 'user_id' => $userId])->first();

    //             if ($cartParentDetail) {

    //                 if ($cartParentDetail->discount_code) {
    //                     $response['status'] = 0;
    //                     $response['message'] = 'Please remove already applied coupon.';
    //                 } else {
    //                     $fail = false;
    //                     $countOrderedCoupon = Orders::where(['discount_code' => $data['discount_code']])->count();

    //                     if ($data->no_of_use <= $countOrderedCoupon) {
    //                         $fail = true;
    //                         $response['status'] = 0;
    //                         $response['message'] = 'This discount coupon is no longer available.';
    //                     }

    //                     if ($data->category_type != 'Flat-Discount') {

    //                         $checkDiscountForRestro = DiscountCategories::where(['discount_id' => $data->id, 'category_id' => $cartParentDetail->restaurant_id])->first();

    //                         if (!$checkDiscountForRestro) {
    //                             $fail = true;
    //                             $response['status'] = 0;
    //                             $response['message'] = 'Not valid coupon code.';
    //                         }
    //                     }

    //                     if ($cartParentDetail->amount < $data->min_order_amount) {
    //                         $fail = true;
    //                         $response['status'] = 0;
    //                         $response['message'] = 'Minimum order amount must be greater then ' . $data->min_order_amount;
    //                     }

    //                     if (!$fail) {
    //                         $discount_amount = (($cartParentDetail->amount * $data->percentage) / 100);

    //                         if ($discount_amount > $data->max_discount_amount) {
    //                             $discount_amount = $data->max_discount_amount;
    //                         }

    //                         $updateCartParent = [
    //                             'discount_code' => $inputData['discount_code'],
    //                             'discount_type' => $data->discount_type,
    //                             'discount_percent' => $data->percentage,
    //                             'discount_amount' => $discount_amount,
    //                             'amount' => $cartParentDetail->amount - $discount_amount,
    //                         ];

    //                         if ($cartParentDetail->update($updateCartParent)) {
    //                             $response['status'] = 1;
    //                             $response['message'] = 'Coupon applied successfully.';
    //                         } else {
    //                             $response['status'] = 0;
    //                             $response['message'] = 'Error Occured.';
    //                         }
    //                     }
    //                 }
    //             } else {
    //                 $response['status'] = 0;
    //                 $response['message'] = 'Invalid cart parent id.';
    //             }
    //         } else {
    //             $response['status'] = 0;
    //             $response['message'] = 'Invalid discount code.';
    //         }
    //         return response()->json($response, 200);
    //     }
    // }

    // public function checkTableAvailable(Request $request)
    // {
    //     $inputData = $request->all();
    //     $userData = auth()->user();
    //     $userId =  $userData->id;

    //     $validator = Validator::make($request->all(), [
    //         'table_code' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         $response['status'] = 0;
    //         $response['message'] = $errors;
    //         return response()->json($response, 200);
    //     } else {
    //         $tableData = RestaurantTables::where(['table_code' => $inputData['table_code']])->first();

    //         if ($tableData) {
    //             $checkTblAvl = Orders::where(['table_code' => $inputData['table_code']])->where('order_status', '!=', 'Complete')->where('order_status', '!=', 'Cancel')->first();
    //             // dd($checkTblAvl);

    //             if (!$checkTblAvl) {
    //                 $response['status'] = 1;
    //                 $response['message'] = 'Table is available.';
    //                 $response['data'] = $tableData;
    //             } else {
    //                 $response['status'] = 0;
    //                 $response['message'] = 'This table is already booked by someone else, Please choose another one.';
    //             }
    //         } else {
    //             $response['status'] = 0;
    //             $response['message'] = 'Invalid table code.';
    //         }
    //         return response()->json($response, 200);
    //     }
    // }

    // public function removeDiscount(Request $request)
    // {
    //     $inputData = $request->all();
    //     $userData = auth()->user();
    //     $userId =  $userData->id;

    //     $validator = Validator::make($request->all(), [
    //         'discount_code' => 'required',
    //         'parent_cart_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = $validator->errors();
    //         $response['status'] = 0;
    //         $response['message'] = $errors;
    //         return response()->json($response, 200);
    //     } else {
    //         $cartParentDetail = CartParent::where(['id' => $inputData['parent_cart_id'], 'user_id' => $userId])->first();

    //         if ($cartParentDetail) {
    //             $updateCartParent = [
    //                 'discount_code' => null,
    //                 'discount_percent' => null,
    //                 'discount_amount' => null,
    //                 'amount' => $cartParentDetail->amount + $cartParentDetail->discount_amount,
    //             ];

    //             if ($cartParentDetail->update($updateCartParent)) {
    //                 $response['status'] = 1;
    //                 $response['message'] = 'Coupon removed successfully.';
    //             } else {
    //                 $response['status'] = 0;
    //                 $response['message'] = 'Error Occured.';
    //             }
    //         } else {
    //             $response['status'] = 0;
    //             $response['message'] = 'Invalid cart parent id.';
    //         }
    //         return response()->json($response, 200);
    //     }
    // }

    public function notificationList(Request $request)
    {
        $locale = App::getLocale();
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        $notificationData = Notification::select('notifications.*')
            ->leftJoin('court_booking', 'court_booking.id', '=', 'notifications.order_id')
            ->where(['notifications.user_id' => $userId])
            ->orderBy('notifications.id', 'desc')
            ->get();
        if (count($notificationData)) {
            $response['status'] = true;
            $response['data'] = $notificationData;
        } else {
            $response['status'] = false;
            $response['message'] = 'No record found.';
        }
        return response()->json($response, 200);
    }
    public function getNotificationCount(Request $request)
    {
        $userData = auth()->user();
        $userId =  $userData->id;
        $locale = App::getLocale();
        $notificationData =  Notification::where(['user_id' => $userId, 'is_read' => 0])->count();
        if ($notificationData) {
            $response['status'] = true;
            $response['data'] = $notificationData;
        } else {
            $response['status'] = true;
            $response['data'] = 0;
        }
        return response()->json($response, 200);
    }


    public function removeNotification1(Request $request)
    {
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $notificationData = Notification::where(['id' => $inputData['notification_id'], 'user_id' => $userId])->first();

            if ($notificationData) {
                $notificationData->delete();
                $response['status'] = 1;
                $response['message'] = 'Notification removed successfully.';
            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid notification id.';
            }
            return response()->json($response, 200);
        }
    }

    public function removeAllNotification(Request $request)
    {
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        $notificationData = Notification::where(['user_id' => $userId])->delete();
        $response['status'] = 1;
        $response['message'] = 'Notification removed successfully.';
        return response()->json($response, 200);
    }

    public function readAllNotification(Request $request)
    {
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        $updateData = [
            'is_read' => 1,
        ];
        Notification::where(['user_id' => $userId])->update($updateData);
        $response['status'] = 1;
        $response['message'] = 'Notification all read successfully.';
        return response()->json($response, 200);
    }
    public function removeNotification(Request $request)
    {
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        if (!isset($inputData['notification_id'])) {
            $notificationData = Notification::where(['user_id' => $userId])->delete();
            $response['status'] = true;
            $response['message'] = __("api.Notification_all_removed_successfully");
            return response()->json($response, 200);
        } else {
            $notificationData = Notification::where(['id' => $inputData['notification_id'], 'user_id' => $userId])->first();
            if ($notificationData) {
                $notificationData->delete();
                $response['status'] = true;
                $response['message'] = __("api.Notification_removed_successfully");
            } else {
                $response['status'] = false;
                $response['message'] = __("api.Invalid_notification_id");
            }
            return response()->json($response, 200);
        }
    }

    public function readNotification(Request $request)
    {
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        if (!isset($inputData['notification_id'])) {
            $updateData = [
                'is_read' => 1,
            ];
            Notification::where(['user_id' => $userId])->update($updateData);
            $response['status'] = true;
            $response['message'] = __("api.Notification_all_read_successfully");


            return response()->json($response, 200);
        } else {
            $notificationData = Notification::where(['id' => $inputData['notification_id'], 'user_id' => $userId])->first();
            if ($notificationData) {
                $updateData = [
                    'is_read' => 1,
                ];
                $notificationData->update($updateData);
                $response['status'] = true;
                $response['message'] = __("api.Notification_read_successfully");
            } else {
                $response['status'] = false;
                $response['message'] = __("api.Invalid_notification_id");
            }
            return response()->json($response, 200);
        }
    }

    public function searchUsers(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $user = User::select('id', 'name', 'mobile', 'country_code')->where(['country_code' => $input['country_code'], 'type' => 0, 'status' => 1]);
            $user->where("mobile", 'like', '%' . $input['mobile'] . '%');
            $userData = $user->get();

            if ($userData && !empty($userData)) {
                $response['status'] = 1;
                $response['data'] = $userData;
            } else {
                $response['status'] = 0;
                $response['message'] = 'User not found.';
            }
            return response()->json($response, 200);
        }
    }
    public function paymentWithCurl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'amount' => 'required',
        ]);
        $input = $request->all();
        //  dd($input);
        // Session::put('court_booking_check_out_data', $input);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $order_id = rand(11111, 99999);

            if ($input['payment_for'] == 'join_challenge') {
                $amount = $input['amount'];
            } else {
                $amount = $input['total_amount'];
            }
            $auth_user = JWTAuth::user();
            $user_data = User::where('id', $auth_user->id)->first();
            $refNumber = str_replace("+", '', $user_data->country_code.$user_data->mobile);
            $getHeaders = apache_request_headers();
            $success_url = url('handle-payment/success_api').'?cart_id='.$order_id.'&platform=mobile';
            $canceled_url = url('handle-payment/declined');
            $declined_url = url('handle-payment/cancel');

            $postfields= '{
                 "method": "create",
                 "store": 27115,
                 "authkey": "HjxMM-6gnz@Fq4J7",
                  "framed":0,
                  "language": "en",
                  "ivp_applepay":"0",
                 "order": {
                   "cartid": "'.$order_id.'",
                   "test": "'.$this->test_mode.'",
                   "amount": "'.$amount.'",
                   "currency": "AED",
                   "description": "Telr Test Payment Details",
                     "trantype": "Sale"
                 },
                 "customer": {
                  "ref": "'.$refNumber.'",  
                   "email": "'.$user_data->email.'",
                   "name": {
                     "forenames": "'.$user_data->first_name.'",
                     "surname": "'.$user_data->last_name.'"
                   },
                   "address": {
                     "line1": "Umm Suqeim 3 - Dubai - United Arab Emirates",
                     "city": "Dubai",
                     "country": "AE"
                   },
                   "phone": ""
                 },
                 "return": {
                   "authorised": "'.$success_url.'",
                   "declined": "'.$canceled_url.'",
                   "cancelled": "'.$declined_url.'"
                 }  
            }';

            $booking_data = json_encode($input);
            $userEncodedata = json_encode($auth_user);
            

            // $url_link = $telrManager->pay($order_id, $amount, 'Telr Test Payment Details', $billingParams)->redirect();
            $online_booking_data = ['order_id' => $order_id, 'booking_data' => $booking_data, 'user_data' => $userEncodedata];
            $online_booking_data_create = OnlineBookingData::create($online_booking_data);
            // $url = $url_link->getTargetUrl();

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://secure.telr.com/gateway/order.json',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$postfields,
              CURLOPT_HTTPHEADER => array(
                 'Content-Type: application/json'
              ),
            ));
            $curlResponse = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($curlResponse);

            if (empty($res)) {
                $response['status'] = false;
                $response['message'] = "Somthing went wrong.";

            } else {
                //create Transaction Data
                $bookingTrasactionData = new Transaction();
                $bookingTrasactionData->cart_id = Str::random(48);
                $bookingTrasactionData->order_id = $order_id;
                $bookingTrasactionData->store_id = $this->store_id;
                $bookingTrasactionData->test_mode = $this->test_mode;
                $bookingTrasactionData->amount = $amount;
                $bookingTrasactionData->description = "Telr Test Payment Details";
                $bookingTrasactionData->success_url = $success_url;
                $bookingTrasactionData->canceled_url = $canceled_url;
                $bookingTrasactionData->declined_url = $declined_url;
                $bookingTrasactionData->billing_fname = $user_data->first_name;
                $bookingTrasactionData->billing_sname = $user_data->last_name;
                $bookingTrasactionData->billing_address_1 = '';
                $bookingTrasactionData->billing_address_2 = '';
                $bookingTrasactionData->billing_city = null;
                $bookingTrasactionData->billing_region = null;
                $bookingTrasactionData->billing_zip = "307501";
                $bookingTrasactionData->billing_country = "ae";
                $bookingTrasactionData->billing_email = $user_data->email;
                $bookingTrasactionData->lang_code = "en";
                $bookingTrasactionData->trx_reference = $res->order->ref;
                $bookingTrasactionData->approved = Null;
                $bookingTrasactionData->status = 0;
                $bookingTrasactionData->save();

                $response['status'] = true;
                $response['data'] = $res->order->url;
                $response['refNumber'] = $res->order->ref;
            }

            return response()->json($response, 200);
            // return redirect($url);
            // return view('web.payment',compact('url','input'));
        }
    }

    public function paymentWithCurl1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'amount' => 'required',
        ]);
        $input = $request->all();
        //  dd($input);
        // Session::put('court_booking_check_out_data', $input);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $telrManager = new \TelrGateway\TelrManager();

            $order_id = rand(11111, 99999);
            // dd($court_booking_check_out_data,$total);
            if ($input['payment_for'] == 'join_challenge') {
                $amount = $input['amount'];
            } else {
                $amount = $input['total_amount'];
            }
            $auth_user = JWTAuth::user();
            $getHeaders = apache_request_headers();
            // $lancode = $request->header();
            // $access_token = Request->header('Authorization');
            // dd($auth_user);
            $booking_data = json_encode($input);
            $user_data = json_encode($auth_user);
            // dd($user_data);
            if (isset($auth_user)) {
                $user_data = User::where('id', $auth_user->id)->first();
                $first_name = $user_data->first_name ?? 'no first_name';
                $sur_name = $user_data->last_name ?? 'no last_name';
                $email = $user_data->email ?? 'no email';
            } else {
                $first_name = 'Abc';
                $sur_name = 'Xyz';
                $email = 'testTelr@gmail.com';
            }
            //  dd($first_name, $sur_name, $email);
            $billingParams = [
                'first_name' =>  $first_name,
                'sur_name' =>  $sur_name,
                'address_1' => 'Umm Suqeim 3 - Dubai - United Arab Emirates',
                'address_2' => 'Umm Suqeim 3 - Dubai - United Arab Emirates 2',
                'city' => 'Dubai',
                'zip' => 307501,
                'country' => 'United Arab Emirates',
                'email' => $email,
            ];

            $url_link = $telrManager->pay($order_id, $amount, 'Telr Test Payment Details', $billingParams)->redirect();
            $online_booking_data = ['order_id' => $order_id, 'booking_data' => $booking_data, 'user_data' => $user_data];
            $online_booking_data_create = OnlineBookingData::create($online_booking_data);
            $url = $url_link->getTargetUrl();
            // dd($url);


            if (empty($url)) {
                $response['status'] = false;
                $response['message'] = "Somthing went wrong.";
            }
            $response['status'] = true;
            $response['data'] = $url;

            return response()->json($response, 200);
            // return redirect($url);
            // return view('web.payment',compact('url','input'));
        }
    }
    public function refundPaymentWithCurl($ivp_cart, $ivp_amount, $tran_ref )
    {
        $params = array(
            //'ivp_store'      => config('telr.ivp_store', null),
            'store'      => 27115,
            //'ivp_authkey'     => 'LShF3@cZkWC-hwWt',
            'ivp_authkey'     => 'HjxMM-6gnz@Fq4J7',
            'ivp_trantype'   => 'refund',
            'ivp_tranclass'  => 'ecom',
            'ivp_desc'       => 'Product Description',
            'ivp_cart'       => $ivp_cart,
            'ivp_currency'   => 'AED',
            'ivp_amount'     =>  $ivp_amount,
            'tran_ref'       => $tran_ref,
            'ivp_test'       => config('telr.test_mode', null),
        );
        // dd($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/remote.xml");
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $results = curl_exec($ch);
        curl_close($ch);
        $results = simplexml_load_string($results);
        $results = json_encode($results);
        // echo $results;
       return $results = json_decode($results, true);
         
       
    }
    public function getCancellationCharge(Request $request)
    {
        $admin_cancel_charge = DeliveryPrice::where('id',1)->select('id','cancellation_charge')->first();
        if (isset($admin_cancel_charge)) {
            $response['status'] = true;
            $response['data'] = $admin_cancel_charge;
            return response()->json($response, 200);
        } else {
            $response['status'] = false;
            $response['message'] = __("api.something_worng");
            return response()->json($response, 200);
        }
      
        return $this->jsonResponse();
    }
}
