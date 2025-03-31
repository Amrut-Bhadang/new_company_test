<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Web\HomeController;
use App\Models\Courts;
use App\Models\OnlineBookingData;
use App\User;
use Illuminate\Support\Facades\Session;
use App\Models\CourtBooking;
use App\Models\CourtBookingSlot;
use App\Models\Facility;
use App\Models\Commission;

class PaymentController extends Controller
{
   public $home_service;
   public function __construct($home_service = '')
   {
      // $home_service = new HomeController();
      $this->home_service = new HomeController();
      $this->store_id = '27115';
      $this->test_mode = '0';
   }
   public function payment(Request $request)
   {
   // dd( url('handle-payment/success'));
      $order_id = rand(11111, 99999);
      $court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
      $auth_user = Session::get('AuthUserData');
      $user_data = User::where('id', $auth_user->data->id)->first();
      $userJsondata = json_encode($user_data);
      $refNumber = str_replace("+", '', $user_data->country_code.$user_data->mobile);
      $success_url = url('handle-payment/success');
      $canceled_url = url('handle-payment/declined');
      $declined_url = url('handle-payment/cancel');

      if ($court_booking_check_out_data['payment_for'] == 'join_challenge') {
         $total = (float)$court_booking_check_out_data['amount'];
      } else {
         $total = (float)$court_booking_check_out_data['total_amount'];
      }
      
  
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
           "amount": "'.$total.'",
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
             "line1": "Dubai",
             "city": "Dubai",
             "country": "AE"
           },
           "phone": "'.$user_data->mobile.'"
         },
         "return": {
           "authorised": "'.$success_url.'",
           "declined": "'.$canceled_url.'",
           "cancelled": "'.$declined_url.'"
         }  
      }';
        //dd($postfields);
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
         $response = curl_exec($curl);
         curl_close($curl);
         $res = json_decode($response);
         if(isset($res) && $res==''){
             $response['status'] = false;
            $response['message'] = __("api.something_worng");
         }else{

         
           Session::put('court_booking_ref_number', $res->order->ref);
       
           //create Transaction Data
           $bookingTrasactionData = new Transaction();
           $bookingTrasactionData->cart_id = Str::random(48);
           $bookingTrasactionData->order_id = $order_id;
           $bookingTrasactionData->store_id = $this->store_id;
           $bookingTrasactionData->test_mode = $this->test_mode;
           $bookingTrasactionData->amount = $total;
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
           //return redirect(url($res->order->url));
          
           //getting the Facility  Details
           $facilityDetails = Commission::where('facility_id',$court_booking_check_out_data['facility_id'])->where('court_id',$court_booking_check_out_data['court_id'])->where('status','1')->first();
           if(isset($facilityDetails) && $facilityDetails!=''){
              $FacilityAdminCommsision = $facilityDetails->amount;
              $FacilityAdminCommsisionAmt =(($total * $facilityDetails->amount)/100);
           }else{
              $FacilityAdminCommsision = 0;
              $FacilityAdminCommsisionAmt = 0;
           }
           
          
           //For Adding into court booking
           $newCourtBooking = new CourtBooking();
           $newCourtBooking->user_id = $user_data->id;
           $newCourtBooking->court_id = $court_booking_check_out_data['court_id'];
           $newCourtBooking->booking_type = $court_booking_check_out_data['booking_type'];
           $newCourtBooking->booking_date = $court_booking_check_out_data['booking_date'];
           $newCourtBooking->end_booking_date =$court_booking_check_out_data['booking_date'];
           $newCourtBooking->facility_id =$court_booking_check_out_data['facility_id'];
           $newCourtBooking->hourly_price = $court_booking_check_out_data['hourly_price'];
           $newCourtBooking->total_amount = $court_booking_check_out_data['total_amount'];
           $newCourtBooking->admin_commission_percentage = $FacilityAdminCommsision;
           $newCourtBooking->admin_commission_amount = $FacilityAdminCommsisionAmt;
           $newCourtBooking->transaction_id =$court_booking_check_out_data['transaction_id'];
           $newCourtBooking->payment_type = $court_booking_check_out_data['payment_type'];
           $newCourtBooking->challenge_type = 'public';
           $newCourtBooking->payment_received_status = $court_booking_check_out_data['payment_received_status'];
           $newCourtBooking->joiner_payment_status = $court_booking_check_out_data['payment_received_status'];
           $newCourtBooking->order_status = 'Pending';
           $newCourtBooking->status = '1';
           $newCourtBooking->created_at = date('Y-m-d H:i:s');
           $newCourtBooking->updated_at = date('Y-m-d H:i:s');
           $newCourtBooking->save();
           
           //Store into Cort Booking Slot
           $court_booking_id = $newCourtBooking->id;
           $booking_start_time =$court_booking_check_out_data['booking_time_slot'][0]['start_time'];
           $booking_total_time =$court_booking_check_out_data['timeslot'];
           $time = strtotime($booking_start_time);
           $booking_end_time = date("H:i", strtotime("+ $booking_total_time minutes", $time));
           $booking_start_datetime = $court_booking_check_out_data['booking_date'].''.$booking_start_time; 
           $booking_end_datetime = $court_booking_check_out_data['booking_date'].''.$booking_end_time; 


           $newCourtSlotBooking = new CourtBookingSlot();
           $newCourtSlotBooking->court_booking_id = $court_booking_id;
           $newCourtSlotBooking->booking_date = $court_booking_check_out_data['booking_date'];
           $newCourtSlotBooking->booking_start_time = $booking_start_time;
           $newCourtSlotBooking->booking_end_time = $booking_end_time;
           $newCourtSlotBooking->booking_start_datetime =$booking_start_datetime;
           $newCourtSlotBooking->booking_end_datetime =$booking_end_datetime;
           $newCourtSlotBooking->status = '1';
           $newCourtSlotBooking->created_at = date('Y-m-d H:i:s');
           $newCourtSlotBooking->updated_at = date('Y-m-d H:i:s');
           $newCourtSlotBooking->save();
         
           return redirect(url('thank-you?slug=booking_online/'.$court_booking_id));
        }
   }

   public function payment1(Request $request)
   {
      $telrManager = new \TelrGateway\TelrManager();
      $order_id = rand(11111, 99999);
      $court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;

      if ($court_booking_check_out_data['payment_for'] == 'join_challenge') {
         $total = (float)$court_booking_check_out_data['amount'];
      } else {
         $total = (float)$court_booking_check_out_data['total_amount'];
      }
      // dd($court_booking_check_out_data,$total);
      $auth_user = Session::get('AuthUserData');
      if (isset($auth_user)) {
         $user_data = User::where('id', $auth_user->data->id)->first();
         $first_name = $user_data->first_name ?? 'no first_name';
         $sur_name = $user_data->last_name ?? 'no last_name';
         $email = $user_data->email ?? 'no email';
      } else {
         $first_name = 'Abc';
         $sur_name = 'Xyz';
         $email = 'testTelr@gmail.com';
      }

      $billingParams = [
         'ref' =>  '123456',
         "framed"=> 0,
         'first_name' =>  $first_name,
         'sur_name' =>  $sur_name,
         'address_1' => '',
         'address_2' => '',
         'city' => '',
         'zip' => 307501,
         'country' => 'ae',
         // 'country' => 'United Arab Emirates',
         'email' => $email,
      ];

      $url_link = $telrManager->pay($order_id, $total, 'Telr Test Payment Details', $billingParams)->redirect();
      //    dd($url_link);
      $url = $url_link->getTargetUrl();
      return redirect(url($url));
      // Display Result Of Response
      // dd($url);
      // return redirect($url);
      //    return view('web.payment',compact('url'));
   }

   public function paymentSuccess(Request $request)
   {
      $ref_number = Session::get('court_booking_ref_number');
      $store = '"27115"';
      $authkey = '"HjxMM-6gnz@Fq4J7"';
      $ref  = '"'.$ref_number.'"';   /*Transaction refrence no generated in Create Order API*/
      $court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
      $auth_user = Session::get('AuthUserData');
      $user_data = User::where('id', $auth_user->data->id)->first();
      $userJsondata = json_encode($user_data);

      $data = '{
        "method": "check",
        "store":'.$store.',
        "authkey":'.$authkey.',
         "order": {
          "ref":'.$ref.'
        }
      }';

      // dd($data);

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
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      $res = json_decode($response);

      
      $transaction = $res;

      if ($transaction) {
         //create Online Booking Data
         $bookingJsonData = [
            "transaction_id" => $transaction->order->transaction->ref,
            "court_id" => $court_booking_check_out_data['court_id'] ?? '',
            "court_booking_id" => $court_booking_check_out_data['court_booking_id'] ?? '',
            "facility_id" => $court_booking_check_out_data['facility_id']  ?? '',
            "payment_type" => "online",
            "timeslot" => $court_booking_check_out_data['timeslot'] ?? null,
            "court_name" => $court_booking_check_out_data['court_name'],
            "hourly_price" => $court_booking_check_out_data['hourly_price'] ?? null,
            "booking_date" => $court_booking_check_out_data['booking_date'] ?? null,
            "total_amount" => $court_booking_check_out_data['total_amount'] ?? null,
            "booking_type" => $court_booking_check_out_data['booking_type'] ?? null,
            "booking_time_slot" => $court_booking_check_out_data['booking_time_slot'] ?? null,
            "amount" => $court_booking_check_out_data['amount'] ?? null,
            "payment_for" => $court_booking_check_out_data['payment_for'],
            "challenge_type" => $court_booking_check_out_data['challenge_type'] ?? null,
            "payment_received_status" => $court_booking_check_out_data['payment_received_status'],
            "user_token" => $auth_user->token
         ];

         $online_booking_data = ['order_id' => $transaction->order->cartid, 'booking_data' => json_encode($bookingJsonData), 'user_data' => $userJsondata];
         $online_booking_data_create = OnlineBookingData::create($online_booking_data);
      }

      //Card Details
      $card_last_4 = $transaction->order->card->last4;
      $card_holder_name = $transaction->order->customer->name->forenames . " " . $transaction->order->customer->name->surname;

      //Queries
      $paymentDetails = Transaction::where('order_id', $transaction->order->cartid)->firstOrFail();

      if ($paymentDetails) {
         Transaction::where('order_id', $transaction->order->cartid)->update(['response' => json_encode($transaction)]);
      }

      // Display Result Of Response
      //    dump('paymentSuccess :: ',$transaction);
      //    dump('transaction Response :: ',$transaction);
      //    dd('payment Details :: ',$paymentDetails); 
      if ($transaction) {
         $online_booking_data = OnlineBookingData::where('order_id', $transaction->order->cartid)->first();

         if (isset($online_booking_data)) {
            $booking_data = json_decode($online_booking_data->booking_data, true);
            // $booking_data['transaction_id'] = $transaction->order->transaction->ref ?? '';
            $booking_data['transaction_id'] = $ref_number ?? '';
            $user_datas = json_decode($online_booking_data->user_data);
            $user_data['status'] = true;
            $user_data['token'] = $booking_data['user_token'];
            $user_data['data'] = $user_datas;

            session()->put('AuthUserData', (object)$user_data);
            $auth_user = Session::get('AuthUserData');
            // dd($booking_data,$user_data,$auth_user);
            if ($booking_data['payment_for'] == 'join_challenge') {
               $parms = $booking_data;
               // dd($parms);
               $result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
               // dd($result);
               $online_booking_data->delete();
               return redirect(url('thank-you?slug=booking_online'));
            } else {
               $court = Courts::where('id', $booking_data['court_id'])->first();
               $booking_data['hourly_price'] = $court->hourly_price;
               $booking_data['timeslot'] = $court->timeslot;
               $book_court = $this->home_service->bookCourt_check_out($booking_data);
               // Session::put('online_booking_data', '');
               $online_booking_data->delete();
               return redirect(url('thank-you?slug=booking_online'));
            }
         } else {
            $court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
            $court_booking_check_out_data['transaction_id'] = $transaction->order->transaction->ref ?? '';

            if ($court_booking_check_out_data['payment_for'] == 'join_challenge') {
               $parms = $court_booking_check_out_data;
               $result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
               return redirect(url('thank-you?slug=booking_online'));
            } else {
               $book_court = $this->home_service->bookCourt_check_out($court_booking_check_out_data);
               Session::put('court_booking_check_out_data', '');
               return redirect(url('thank-you?slug=booking_online'));
            }
         }


         // return response()->json($book_court, 200);
      } else {
         $response['status'] = false;
         $response['message'] = __("api.something_worng");
         // return response()->json($response, 200);

      }


      //    return redirect(route('home'));
   }

   public function paymentSuccessApi(Request $request)
   {
      if ($request->cart_id && $request->platform == 'mobile') {
         $transactionData = Transaction::where('order_id', $request->cart_id)->firstOrFail();
         $ref_number = $transactionData->trx_reference ?? $request->cart_id;

         $online_booking_data = OnlineBookingData::where('order_id', $transactionData->order_id)->first();

         if (isset($online_booking_data)) {
            $booking_data = json_decode($online_booking_data->booking_data, true);
            $booking_data['transaction_id'] = $transaction->trx_reference ?? '';
            $user_datas = json_decode($online_booking_data->user_data);
            $user_data['status'] = true;
            $user_data['token'] = $booking_data['user_token'];
            $user_data['data'] = $user_datas;

            session()->put('AuthUserData', (object)$user_data);
            $auth_user = Session::get('AuthUserData');
         }

         $store = '"27115"';
         $authkey = '"HjxMM-6gnz@Fq4J7"';
         $ref  = '"'.$ref_number.'"';   /*Transaction refrence no generated in Create Order API*/
         $user_data = User::where('id', $auth_user->data->id)->first();
         $userJsondata = json_encode($user_data);

         $data = '{
           "method": "check",
           "store":'.$store.',
           "authkey":'.$authkey.',
            "order": {
             "ref":'.$ref.'
           }
         }';

         // dd($data);

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
           CURLOPT_POSTFIELDS =>$data,
           CURLOPT_HTTPHEADER => array(
             'Content-Type: application/json'
           ),
         ));

         $response = curl_exec($curl);
         curl_close($curl);
         $res = json_decode($response);

         
         $transaction = $res;

         //Card Details
         $card_last_4 = $transaction->order->card->last4;
         $card_holder_name = $transaction->order->customer->name->forenames . " " . $transaction->order->customer->name->surname;

         //Queries
         $paymentDetails = Transaction::where('order_id', $transaction->order->cartid)->firstOrFail();

         if ($paymentDetails) {
            Transaction::where('order_id', $transaction->order->cartid)->update(['response' => json_encode($transaction)]);
         }

         if ($transaction) {
            $online_booking_data = OnlineBookingData::where('order_id', $transaction->order->cartid)->first();

            if (isset($online_booking_data)) {
               $booking_data = json_decode($online_booking_data->booking_data, true);
               // $booking_data['transaction_id'] = $transaction->order->transaction->ref ?? '';
               $booking_data['transaction_id'] = $ref_number ?? '';
               $user_datas = json_decode($online_booking_data->user_data);
               $user_data['status'] = true;
               $user_data['token'] = $booking_data['user_token'];
               $user_data['data'] = $user_datas;

               session()->put('AuthUserData', (object)$user_data);
               $auth_user = Session::get('AuthUserData');
               // dd($booking_data,$user_data,$auth_user);
               if ($booking_data['payment_for'] == 'join_challenge') {
                  $parms = $booking_data;
                  // dd($parms);
                  $result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
                  // dd($result);
                  $online_booking_data->delete();
                  return redirect(url('thank-you?slug=booking_online'));
               } else {
                  $court = Courts::where('id', $booking_data['court_id'])->first();
                  $booking_data['hourly_price'] = $court->hourly_price;
                  $booking_data['timeslot'] = $court->timeslot;
                  $book_court = $this->home_service->bookCourt_check_out($booking_data);
                  // Session::put('online_booking_data', '');
                  $online_booking_data->delete();
                  return redirect(url('thank-you?slug=booking_online'));
               }
            } else {
               $court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
               $court_booking_check_out_data['transaction_id'] = $transaction->order->transaction->ref ?? '';

               if ($court_booking_check_out_data['payment_for'] == 'join_challenge') {
                  $parms = $court_booking_check_out_data;
                  $result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
                  return redirect(url('thank-you?slug=booking_online'));
               } else {
                  $book_court = $this->home_service->bookCourt_check_out($court_booking_check_out_data);
                  Session::put('court_booking_check_out_data', '');
                  return redirect(url('thank-you?slug=booking_online'));
               }
            }


            // return response()->json($book_court, 200);
         } else {
            $response['status'] = false;
            $response['message'] = __("api.something_worng");
            // return response()->json($response, 200);

         }

      } else {
         $response['status'] = false;
         $response['message'] = __("api.something_worng");
         return response()->json($response, 200);
      }
      //    return redirect(route('home'));
   }

   public function paymentSuccess1(Request $request)
   {
      // print_r($request->all());
      // Store Transaction Details 
      $telrManager = new \TelrGateway\TelrManager();
      //    dd($telrManager);
      $transaction = $telrManager->handleTransactionResponse($request);
      // print_r($transaction);die;

      //Card Details
      $card_last_4 = $transaction->response['order']['card']['last4'];
      $card_holder_name = $transaction->response['order']['customer']['name']['forenames'] . " " . $transaction->response['order']['customer']['name']['surname'];

      //Queries
      $paymentDetails = Transaction::where('cart_id', $request->cart_id)->firstOrFail();

      // Display Result Of Response
      //    dump('paymentSuccess :: ',$transaction);
      //    dump('transaction Response :: ',$transaction->response);
      //    dd('payment Details :: ',$paymentDetails); 
      if ($transaction) {
         $online_booking_data = OnlineBookingData::where('order_id', $transaction->order_id)->first();
         if (isset($online_booking_data)) {
            $booking_data = json_decode($online_booking_data->booking_data, true);
            $booking_data['transaction_id'] = $transaction->trx_reference ?? '';
            $user_datas = json_decode($online_booking_data->user_data);
            $user_data['status'] = true;
            $user_data['token'] = $booking_data['user_token'];
            $user_data['data'] = $user_datas;

            session()->put('AuthUserData', (object)$user_data);
            $auth_user = Session::get('AuthUserData');
            // dd($booking_data,$user_data,$auth_user);
            if ($booking_data['payment_for'] == 'join_challenge') {
               $parms = $booking_data;
               // dd($parms);
               $result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
               // dd($result);
               $online_booking_data->delete();
               return redirect(url('thank-you?slug=booking_online'));
            } else {
               $court = Courts::where('id', $booking_data['court_id'])->first();
               $booking_data['hourly_price'] = $court->hourly_price;
               $booking_data['timeslot'] = $court->minimum_hour_book;
               $book_court = $this->home_service->bookCourt_check_out($booking_data);
               // Session::put('online_booking_data', '');
               $online_booking_data->delete();
               return redirect(url('thank-you?slug=booking_online'));
            }
         } else {
            $court_booking_check_out_data = Session::get('court_booking_check_out_data') ?? null;
            $court_booking_check_out_data['transaction_id'] = $transaction->trx_reference ?? '';

            if ($court_booking_check_out_data['payment_for'] == 'join_challenge') {
               $parms = $court_booking_check_out_data;
               $result = ApiCurlMethod('join-challenge', $parms, 'Bearer');
               return redirect(url('thank-you?slug=booking_online'));
            } else {
               $book_court = $this->home_service->bookCourt_check_out($court_booking_check_out_data);
               Session::put('court_booking_check_out_data', '');
               return redirect(url('thank-you?slug=booking_online'));
            }
         }


         // return response()->json($book_court, 200);
      } else {
         $response['status'] = false;
         $response['message'] = __("api.something_worng");
         // return response()->json($response, 200);

      }


      //    return redirect(route('home'));
   }
   public function paymentCancel(Request $request)
   {
      //$telrManager = new \TelrGateway\TelrManager();
      //$transaction = $telrManager->handleTransactionResponse($request);

      // Display Result Of Response

      // dd('paymentCancel :: ',$transaction);

      return redirect(route('web.home'));
   }
   public function paymentDeclined(Request $request)
   {
      //$telrManager = new \TelrGateway\TelrManager();
      //$transaction = $telrManager->handleTransactionResponse($request);

      // Display Result Of Response
      //  dd('paymentDeclined :: ',$transaction);

      return redirect(route('web.home'));
   }
   public function paymentWithCurl(Request $request)
   {
      $unique_cart_id = rand(11111, 55555);

      $params = array(
         'ivp_method' => 'create',
         'ivp_framed' => 2,
         'ivp_store' => "27115",
         'ivp_authkey' => "HjxMM-6gnz@Fq4J7",
         'ivp_cart' => $unique_cart_id,
         'ivp_test' => $this->test_mode,
         'ivp_amount' => '100.00',
         'ivp_currency' => env('TELR_CURRENCY', 'AED'),
         'ivp_desc' => 'Product Description',
         'return_auth' => 'http://localhost/teamna/handle-payment/success?id=' . $unique_cart_id,
         'return_can' => 'http://localhost/teamna/handle-payment/cancel?id=' . $unique_cart_id,
         'return_decl' => 'http://localhost/teamna/handle-payment/declined?id=' . $unique_cart_id,
      );
      //  dd($params);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/order.json");
      curl_setopt($ch, CURLOPT_POST, count($params));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
      $results = curl_exec($ch);
      //  dd($results);

      curl_close($ch);

      $results = json_decode($results, true);
      //    dd($results);
      $ref = trim($results['order']['ref']);
      $url = trim($results['order']['url']);
      dd($ref, $url);
      if (empty($ref) || empty($url)) {
         # Failed to create order
         dd("something went wrong");
      }
      // return redirect($url);
      return view('web.payment', compact('url'));
   }
}
