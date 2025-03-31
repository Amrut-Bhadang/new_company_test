<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Models\UserOtp;
use App\Models\UserDevice;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['otpVerify', 'sendOtp', 'login', 'signUp', 'forgot_password', 'resetPassword', 'setPassword', 'socialLogin']]);
    }

    public function sendOtp(Request $request)
    {
        // dd($request->all());
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'mobile.required' => __("api.mobile_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
        ];

        $validator = Validator::make($input, [
            'type'   => 'required'
        ], $message);

        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $locale = App::getLocale();

            if ($request->type == 'register') {
                $validator = Validator::make($input, [
                    'country_code'   => 'required',
                    'mobile' => 'required|min:7|max:15',
                ], $message);
                if ($validator->fails()) {
                    $this->errorValidation($validator);
                } else {
                    $user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();
                    if (!$user) {
                        // create otp 
                        //$otps = $this->generateRandomString('N', 4);
                        $otps = '1234';
                        $message = __("api.sendOtpMessage") . $otps;
                        $otp = UserOtp::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();
                        if (!isset($otp)) {
                            $otp = new UserOtp;
                            $otp->country_code = $input['country_code'];
                            $otp->mobile = $input['mobile'];
                        }
                        $otp->otp = $otps;
                        $otp->save();

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
                         //dd($curlUrlNew);

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
                        $response = curl_exec($curl);
                        $err = curl_error($curl);
                        // echo "<pre>";print_r($curl);die;
                        curl_close($curl);
                        /*Send SMSCountry*/
                        // return response
                        $this->message  = __("api.sendOtpSuccess");
                        $this->status   = true;
                    } else {
                        $this->message  = __("api.mobile_number_already_exsits");
                        $this->status   = false;
                    }


                    $this->code     = 200;
                }
            } elseif ($request->type == 'resend') {
                // dd('resend');
                $message = [
                    'mobile.required' => __("api.mobile_required"),
                    'mobile.min' => __("api.mobile_min"),
                    'mobile.max' => __("api.mobile_max"),
                ];
                $validator = Validator::make($input, [
                    'country_code'   => 'required',
                    'mobile' => 'required|min:7|max:15',
                ], $message);
                if ($validator->fails()) {
                    $this->errorValidation($validator);
                } else {
                    // create otp 
                    //$otps = $this->generateRandomString('N', 4);
                    $otps = '1234';
                    $message = __("api.sendOtpMessage") . $otps;
                    $otp = UserOtp::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();

                    if (!isset($otp)) {
                        $otp = new UserOtp;
                        $otp->country_code = $input['country_code'];
                        $otp->mobile = $input['mobile'];
                    }
                    $otp->otp = $otps;
                    $otp->save();
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
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    // echo "<pre>";print_r($curl);die;
                    curl_close($curl);
                    /*Send SMSCountry*/
                    // return response
                    $this->message  = __("api.sendOtpSuccess");
                    $this->status   = true;
                    $this->code     = 200;
                }
            } elseif ($request->type == 'forgot') {
                $message = [
                    'mobile.required' => __("api.mobile_required"),
                    'mobile.min' => __("api.mobile_min"),
                    'mobile.max' => __("api.mobile_max"),
                    'mobile.exists' => __("api.mobile_exists"),
                ];
                $validator = Validator::make($input, [
                    'country_code'   => 'required',
                    'mobile' => 'required|min:7|max:15|exists:users,mobile',
                ], $message);
                if ($validator->fails()) {
                    $this->errorValidation($validator);
                } else {
                    $user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile'], 'type' => 3])->first();

                    if ($user) {
                        // create otp 
                        //$otps = $this->generateRandomString('N', 4);
                        $otps = '1234';
                        $message = __("api.sendOtpMessage") . $otps;
                        $otp = UserOtp::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();

                        if (!isset($otp)) {
                            $otp = new UserOtp;
                            $otp->country_code = $input['country_code'];
                            $otp->mobile = $input['mobile'];
                        }
                        $otp->otp = $otps;
                        $otp->save();
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
                        $response = curl_exec($curl);
                        $err = curl_error($curl);
                        // echo "<pre>";print_r($curl);die;
                        curl_close($curl);
                        /*Send SMSCountry*/
                        // return response
                        $this->message  = __("api.sendOtpSuccess");
                        $this->status   = true;
                        $this->code     = 200;
                    } else {
                        $this->message  = __("api.mobile_exists");
                        $this->status   = false;
                    }
                }
            } else {
                // return response
                $this->message  = __("api.not_valid_type");
                $this->status   = false;
                $this->code     = 200;
            }
        }
        return $this->jsonResponse();
    }



    public function otpVerify(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'mobile.required' => __("api.mobile_required"),
            'country_code.required' => __("api.country_code_required"),
            'otp.required' => __("api.otp_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
        ];
        $validator = Validator::make($input, [
            'country_code'   => 'required',
            'mobile' => 'required|min:7|max:15',
            'otp' => 'required',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $otp = UserOtp::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();

            if (isset($otp) && ($input['otp'] == $otp->otp  || $input['otp'] == '1234')) {
                $response['status'] = true;
                $response['message'] = __("api.otp_verify_success");
                return response()->json($response, 200);
            } else {
                $response['status'] = false;
                $response['message'] = __("api.otp_invalid_message");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }


    public function setPassword(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'mobile.required' => __("api.mobile_required"),
            'mobile.unique' => __("api.mobile_unique"),
            'country_code.required' => __("api.country_code_required"),
            'otp.required' => __("api.otp_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
            'password.required' => __("api.password_required"),
        ];
        $validator = Validator::make($input, [
            'country_code'   => 'required',
            'mobile' => 'required|min:7|max:15',
            'password'   => 'required|min:6',
            'otp' => 'required',

        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {

            $otp = UserOtp::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();
            if (isset($otp) && ($input['otp'] == $otp->otp  || $input['otp'] == '1234')) {
                $user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();
                if (!isset($user)) {
                    $user = new User;
                    $user->country_code = $input['country_code'];
                    $user->mobile = $input['mobile'];
                    $user->status = 1;
                    $user->type = 3;
                    $user->password = Hash::make($request->input('password'));
                    $user->save();
                } else {
                    $this->message  = __("api.mobile_number_already_exsits");
                    $this->status   = false;
                }
                $credentials = ['country_code' => $input['country_code'], 'mobile' => $input['mobile'], 'password' => $input['password']];

                if (!$token = JWTAuth::attempt($credentials)) {
                    $response['status'] = false;
                    $response['message'] = __("api.invalid_user_login");
                    return response()->json($response, 200);
                }
                if (isset($input['device_token'])) {
                    $data = UserDevice::where(['user_id' => $user->id])->first();
                    if (!isset($data)) {
                        $data = new UserDevice;
                        $data->user_id = $user->id;
                        $data->device_type = $input['device_type'];
                        $data->device_token = $input['device_token'];
                        $data->save();
                    }
                    $data->device_type = $input['device_type'];
                    $data->device_token = $input['device_token'];
                    $data->save();
                }
                // send notification start
                $user_id =  $user->id;
                if (isset($user_id)) {
                    $locale = App::getLocale();
                    // App::SetLocale('ar');
                    $notification_title = 'notification_create_user_title';
                    $notification_message = 'notification_create_user_message';
                    send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'create_user', $order_id = $user_id, $title = $notification_title, $message = $notification_message);
                    App::SetLocale($locale);
                    $notification_title = __('api.notification_create_user_title');
                    $notification_message = __('api.notification_create_user_message');
                    send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'create_user', 'key' => 'create_user'));
                    // panal notification start
                    $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                    send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);

                    $locale = App::getLocale();
                    $admin_notification_title = 'admin_notification_create_user_title';
                    $admin_notification_message = 'admin_notification_create_user_message';
                    $login_user_data = auth()->user();
                    $adminChanellId = 'pubnub_onboarding_channel_admin_1';
                    add_admin_notification($user_type = 0, $notification_type = 0, $notification_for = 'create_user', $title = $admin_notification_title, $message = $admin_notification_message, $user_id = 1, $order_id = $user_id);
                    App::SetLocale($locale);
                    $admin_notification_title = __('backend.admin_notification_create_user_title');
                    $admin_notification_message = __('backend.admin_notification_create_user_message');
                    send_admin_notification($message = $admin_notification_message, $title = $admin_notification_title, $channel_name = $adminChanellId);
                    //panal notification end
                }
                // send notification end

                $response['status'] = true;
                $response['token'] = $token;
                $response['data'] = new UserResource($user);
                $response['message'] = __("api.login_successfully");
                return response()->json($response, 200);
                // return response
                // $response['status'] = true;
                // $response['message'] = __("api.set_password_message");
                // return response()->json($response, 200);

            } else {
                $response['status'] = false;
                $response['message'] = __("api.otp_invalid_message");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }

    public function resetPassword(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'mobile.required' => __("api.mobile_required"),
            'mobile.unique' => __("api.mobile_unique"),
            'country_code.required' => __("api.country_code_required"),
            'otp.required' => __("api.otp_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
            'password.required' => __("api.password_required"),
        ];
        $validator = Validator::make($input, [
            'country_code'   => 'required',
            'mobile' => 'required|min:7|max:15',
            'password'   => 'required|min:6',
            'otp' => 'required',

        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {

            $otp = UserOtp::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();

            if (isset($otp) && ($input['otp'] == $otp->otp  || $input['otp'] == '1234')) {
                $user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();
                if (!isset($user)) {
                    $user = new User;
                    $user->country_code = $input['country_code'];
                    $user->mobile = $input['mobile'];
                }
                $user->password = Hash::make($request->input('password'));
                $user->save();
                // return response
                $this->message  = __("api.reset_password_message");
                $this->status   = true;
                $this->code     = 200;
            } else {
                $response['status'] = false;
                $response['message'] = __("api.otp_invalid_message");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }
    public function login(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'mobile.required' => __("api.mobile_required"),
            'country_code.required' => __("api.country_code_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
            'password.required' => __("api.password_required"),
            'password.min' => __("api.password_min"),
        ];
        $validator = Validator::make($input, [
            'country_code'   => 'required',
            'mobile' => 'required|min:7|max:15',
            'password'   => 'required',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])
            ->where(function ($query) {
                $query->where('type','=',3)
                ->orWhere('is_facility_owner','=',1);
            })
            ->first();
            if (isset($user)) {
                if ($user->status == 1 && empty($user->deleted_at)) {
                    $credentials = ['country_code' => $input['country_code'], 'mobile' => $input['mobile'], 'password' => $input['password']];
                    if (Hash::check($input['password'], $user->password)) {
                        if (!$token = JWTAuth::attempt($credentials)) {
                            $response['status'] = false;
                            $response['message'] = __("api.invalid_user_login");
                            return response()->json($response, 200);
                        }
                        if (isset($input['device_token'])) {
                            $data = UserDevice::where(['user_id' => $user->id])->first();

                            if (!isset($data)) {
                                $data = new UserDevice;
                                $data->user_id = $user->id;
                                $data->device_type = $input['device_type'];
                                $data->device_token = $input['device_token'];
                                $data->save();
                            }
                            $data->device_type = $input['device_type'];
                            $data->device_token = $input['device_token'];
                            $data->save();
                        }
                        //Notification send
                        // send_notification(1, $user->id, 'Welcome Back', array('title'=>'Welcome Back','message'=>'You are successfully login.','type'=>'Login','key'=>'Login'));
                        //End Notification send
                        $response['status'] = true;
                        $response['token'] = $token;
                        $response['data'] = new UserResource($user);
                        $response['message'] = __("api.login_successfully");
                        return response()->json($response, 200);
                    } else {
                        $response['status'] = false;
                        $response['message'] = __("api.incorrect_password_message");
                        return response()->json($response, 200);
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = __("api.user_deactive_or_deleted_message");
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = false;
                $response['message'] = __("api.invalid_user_login");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }

    public function delete_account(Request $request){
        $input =  $request->all();
        $user_id = auth()->user()->id;

        $user = User::where(['id'=>$user_id])->first();

        if(isset($user)){
            $user->deleted_at = date('Y-m-d H:i:s');
            $user->status = 0;

            if ($user->save()){
                auth()->logout();
                $response['status'] = 1;
                $response['message'] = 'Your account is deleted successfully.';
                return response()->json($response, 200);

            } else{
                $response['status'] = 0;
                $response['message'] = 'Error Occured.';
                return response()->json($response, 200);
            }  

        } else{
            $response['status'] = 0;
            $response['message'] = 'Invalid User details.';
            return response()->json($response, 200);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::user();
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'first_name.required' => __("api.first_name_required"),
            'last_name.required' => __("api.last_name_required"),
            'email.required' => __("api.email_required"),
            'gender.required' => __("api.gender_required"),
            'image.required' => __("api.image_required"),
            'email.unique' => __("api.email_unique"),
            'dob.required' => __("api.dob_required"),
            'dob.date_format' => __("api.dob_date_format"),
            'dob.before' => __("api.dob_before"),

        ];
        $validator = Validator::make($input, [
            'first_name'   => 'required',
            // 'last_name'   => 'required',
            'email'   => 'email|unique:users,email,' . $user->id,
             'gender'   => 'required',
            'dob'   => 'date_format:Y-m-d|before:today',
            // 'image'   => 'required',

        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            // image upload
            if ($request->file('image')) {
                $file = $request->file('image');
                $result = image_upload($file, 'user', 'image');
                if ($result[0] == true) {
                    $user->image = $result[1];
                    $updateNewDBData['image'] = $result[1];
                }
            }
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->name = $request->input('first_name') . ' ' . $request->input('last_name');
            $user->email = $request->input('email');
            $user->gender = $request->input('gender');
            $user->dob = $request->input('dob');
            $user->is_profile_updated = 1;
            $user->image_type = 'local';


            if ($user->update()) {
                $userData = User::where(['id' => $user->id])->first();
                $data =  new UserResource($userData);
                $response['status'] = true;
                $response['message'] = __("api.profile_update_message");
                $response['data'] = $data;
            } else {
                $response['status'] = false;
                $response['message'] = __("api.something_worng");
            }
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function updateProfileMobile(Request $request)
    {
        $user = JWTAuth::user();
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'mobile.required' => __("api.mobile_required"),
            'country_code.required' => __("api.country_code_required"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),
        ];
        $validator = Validator::make($input, [
            'country_code'   => 'required',
            'mobile' => 'required|min:7|max:15',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $is_user = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();
            if (!isset($is_user)) {
                $user->country_code = $request->input('country_code');
                $user->mobile = $request->input('mobile');
                if ($user->update()) {
                    $userData = User::where(['id' => $user->id])->first();
                    $data =  new UserResource($userData);
                    $response['status'] = true;
                    $response['message'] = __("api.mobile_update_message");
                    $response['data'] = $data;
                } else {
                    $response['status'] = false;
                    $response['message'] = __("api.something_worng");
                }
            } else {
                $response['message']  = __("api.mobile_number_already_exsits");
                $response['status']  = false;
            }
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    public function userProfile()
    {
        $user = JWTAuth::user();
        if ($user) {

            $data =  new UserResource($user);
            $response['status'] = true;
            $response['data'] = $data;
            return response()->json($response, 200);
        } else {
            $response['status'] = false;
            $response['message'] = __("api.something_worng");
            return response()->json($response, 200);
        }
        return response()->json();
    }
    public function changePassword(Request $request)
    {
        $user = JWTAuth::user();
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'old_password.required' => __("api.old_password_required"),
            'new_password.required' => __("api.new_password_required"),
            'new_password.min' => __("api.new_password_min"),
            'old_password.min' => __("api.old_password_min"),
        ];
        $validator = Validator::make($input, [
            'old_password'   => 'required',
            'new_password'   => 'required|min:6',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {

            $user = JWTAuth::user();
            if (isset($user)) {

                if (Hash::check($input['old_password'], $user->password)) {
                    $user->password = Hash::make($input['new_password']);

                    if ($user->save()) {

                        $response['status'] = true;
                        $response['message'] = __("api.password_change_success");
                        return response()->json($response, 200);
                    } else {
                        $response['status'] = false;
                        $response['message'] = __("api.something_worng");
                        return response()->json($response, 200);
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = __("api.old_password_incorrect");
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = false;
                $response['message'] = __("api.invalid_user");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }

    public function socialLogin(Request $request)
    {
        // dd('socialLogin',$request->all());

        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $user = User::where(['social_type' => $input['social_type'], 'social_id' => $input['social_id']])->first();
        // dd($user);
        $message = [
            'social_type.required' => __("api.social_type_required"),
            'social_id.required' => __("api.social_id_required"),
            // 'first_name.required' => __("api.first_name_required"),
            // 'last_name.required' => __("api.last_name_required"),
            'email.unique' => __("api.email_unique"),
            'mobile.min' => __("api.mobile_min"),
            'mobile.max' => __("api.mobile_max"),

        ];
        if ($user !== null) {
            $validator = Validator::make($input, [
                'social_type'   => 'required',
                'social_id' => 'required',
                // 'first_name'   => 'required',
                // 'last_name'   => 'required',
                'email'   => 'unique:users,email,' . $user->id,
                'mobile' => 'min:7|max:15',
            ], $message);
        } else {
            $validator = Validator::make($input, [
                'social_type'   => 'required',
                'social_id' => 'required',
                // 'first_name'   => 'required',
                // 'last_name'   => 'required',
                'email'   => 'unique:users,email,',
                'mobile' => 'min:7|max:15',
            ], $message);
        }

        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            // dd($input);
            $user = User::where(['social_type' => $input['social_type'], 'social_id' => $input['social_id']])->first();
            if (!isset($user)) {
                $user = new User;
                $user->social_type = $input['social_type'];
                $user->social_id = $input['social_id'];
                $user->password = Hash::make($input['social_id']);
                $user->first_name = $request->first_name ? $input['first_name'] : null;
                $user->last_name = $request->last_name ? $input['last_name'] : null;
                $user->name = $request->first_name ? $input['first_name'] . ' ' . $input['last_name'] : '';
                $user->email = $request->email ? $input['email'] : null;
                $user->image = $request->image ? $input['image'] : null;
                $user->mobile = $request->mobile ? $input['mobile'] : null;
                $user->image_type = 'url';
                $user->status = 1;
                $user->type = 3;

                $user->save();
            }
            $credentials = ['social_id' => $input['social_id'], 'password' => $input['social_id']];

            if (!$token = JWTAuth::attempt($credentials)) {
                $response['status'] = false;
                $response['message'] = __("api.invalid_user_login");
                return response()->json($response, 200);
            }

            $response['status'] = true;
            $response['token'] = $token;
            $response['data'] = new UserResource($user);
            $response['message'] = __("api.login_successfully");
            return response()->json($response, 200);
        }
        return $this->jsonResponse();
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = JWTAuth::user();
        $data = UserDevice::where(['user_id' => $user->id])->first();
       
        if (isset($data)) {
            $data->device_type = null;
            $data->device_token = null;
            $data->save();
        }
        auth()->logout();
        $response['status'] = true;
        $response['message'] = __("api.user_logout_message");
        return response()->json($response, 200);
    }

    public function generateRandomString($type = null, $length = 6)
    {
        if ($type == 'N') {
            $string = '0123456789';
        } else if ($type == 'A') {
            $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        return substr(str_shuffle(str_repeat($string, ceil($length / strlen($string)))), 1, $length);
    }
}
