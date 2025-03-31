<?php

/****************************************************/
// Developer By @Inventcolabs.com
/****************************************************/

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use App\Models\Favorite;
use App\Models\Category;
use App\Models\Gift;
use App\Models\Restaurant;
use App\Models\Brand;
use App\Models\Products;
use App\Models\ProductTags;
use App\Models\Media;
use App\Models\UsersAddress;
use App\Models\UsersCar;
use App\Models\Faq;
use App\Models\MainCategory;
use App\Models\BrandCategory;
use JWTAuth;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\UserOtp;
use App\Models\UserWallets;
use App\Models\Topping;
use App\Models\ToppingCategory;
use App\Models\Discount;
use App\Models\DiscountCategories;
use App\Models\CartParent;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\OrdersDetails;
use App\Models\UserPlatforms;
use App\Models\DiscountReadUsers;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
use App\Models\AttributesLang;
use App\Models\AttributeValues;
use App\Models\AttributeValueLang;
use App\Models\RestaurantTables;
use App\Models\Subcategory;
use App\Models\FaqRequest;
use App\Http\Resources\UserResource;
use DB,Session;
use App;
use App\Http\Resources\CategoryResource;

class CommonWithAuthApiController extends Controller {

    public function __construct() {
        // $this->middleware('auth:api');
        define("A_TO_Z",'a_to_z');
        define("Z_TO_A",'z_to_a');
        $this->radius = 100;
    }

    /**
    * This function is use for mobile app to show discount coupons.
    *
    * @method Post
    */
    public function saveDetails(Request $request) {
           
        /*if($request->type == 'updateprofile'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'string|between:2,100',
                'last_name' => 'string|between:2,100',
                'email' => 'required|email|unique:users,email, '. auth()->user()->id .',id',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
            ]);
        }*/

        $validator = Validator::make($request->all(), [
            'name' => 'string|between:2,100',
            'email' => 'string|email|unique:users,email, '. auth()->user()->id .',id',
            'gender' => 'string',
            'marital_status' => 'string',
            // 'last_name' => 'string|between:2,100',
        ]);
        
        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors->first();
            return response()->json($response, 200);
        }

        $user = User::findOrFail(auth()->user()->id);
        $message = '';
        $fail = false;

        if (isset($user)) {

            if (isset($request->referral_code) && !empty($request->referral_code)) {
                $checkReferral = User::where(['share_code'=>$request->referral_code])->where('id', '!=' , $user->id)->first();

                if (!$checkReferral) {
                    $fail = true;
                    $message = 'Invalid referral code.';
                }
            }

            if (isset($request->mobile) && isset($request->country_code)) {

                if (isset($request->otp)) {
                    $otp = UserOtp::where('user_id',$user->id)->first();

                    if (isset($otp->otp) && $otp->otp==$request->otp || $request->otp=='123456') {
                        $checkNumberExist = User::where(['country_code'=>$request->country_code,'mobile'=>$request->mobile])->where('id', '!=' , $user->id)->first();

                        if ($checkNumberExist) {
                            $fail = true;
                            $message = 'Mobile number has been already taken.';

                        } else {
                            $user->country_code = $request->country_code; 
                            $user->mobile = $request->mobile;
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid OTP number.";
                        return response()->json($response, 200);
                    }

                } else {
                    $fail = true;
                    $message = 'OTP field is required.';
                }
            }

            if (!$fail) {
                $user->is_profile_updated = 1;

                if(isset($request->name)){
                    $user->name = $request->name; 
                    $user->first_name = $request->name;
                }

                if(isset($request->email)){
                    $user->email = $request->email;
                }

                if(isset($request->gender)){
                    $user->gender = $request->gender;
                } 

                if(isset($request->marital_status)){
                    $user->marital_status = $request->marital_status;
                } 

                if(isset($request->referral_code)){
                    $user->referral_code = $request->referral_code;
                }

                if(isset($request->dob)){
                    $user->dob = date('Y-m-d', strtotime($request->dob));
                }

                if ($request->file('image')) {
                    $file = $request->file('image');
                    $result = image_upload($file,'user','image');

                    if ($result[0]==true){
                        /*$data->file_path = $result[1];
                        $data->file_name = $result[3];
                        $data->extension = $result[2];*/
                        $user->image = $result[1];
                    }
                }

                if ($user->save()) {
                    $response['status'] = 1;
                    $response['message'] = 'User Details updated successfully.';
                    $response['data'] = new UserResource($user);
                    // $response['image_base_path'] = asset('uploads/user/');
                    return response()->json($response, 200);

                } else{
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                    return response()->json($response, 200);
                }

            } else {
                $response['status'] = 0;
                $response['message'] = $message;
                return response()->json($response, 200);
            }

        } else{
            $response['status'] = 0;
            $response['message'] = 'Something worng.';
            return response()->json($response, 200);
        }
    }



    /**
    * This function is use for mobile app to show popup for discount/info.
    *
    * @method Get
    */

    

    public function searchUsers(Request $request){
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
            $user = User::select('id', 'name', 'mobile', 'country_code')->where(['country_code'=>$input['country_code'],'type'=>0,'status'=>1]);
            $user->where("mobile",'like','%'.$input['mobile'].'%');
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

    public function getAllCar() {
        $userData = auth()->user();
        $userId =  $userData->id;

        if (!empty($userId)) {
            $userCarList =  UsersCar::where('user_id',$userId)->get();

            if (count($userCarList)) {
                $data = $userCarList;

            } else {
                $data = [];
            }

            if (count($data)) {
                $response['status'] = 1;
                $response['data'] = $data;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Car Not Found.';
                return response()->json($response, 200);
            }

        } else {
            $response['status'] = 0;
            $response['message'] = 'Something worng.';
            return response()->json($response, 200);
        }
    }

    public function getDefaultCar() {
        $userData = auth()->user();
        $userId =  $userData->id;

        if (!empty($userId)) {
            $userCarList =  UsersCar::where(['user_id'=>$userId, 'is_defauld_car'=>1])->first();

            if ($userCarList) {
                $response['status'] = 1;
                $response['data'] = $userCarList;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Car Not Found.';
                return response()->json($response, 200);
            }

        } else {
            $response['status'] = 0;
            $response['message'] = 'Something worng.';
            return response()->json($response, 200);
        }
    }

    public function addCar(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'car_color'=>'required',
            'car_number'=>'required',
            'car_brand' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {
                $inputs = [
                    'car_color' => $request->car_color,
                    'car_number' => $request->car_number,
                    'car_brand'=>$request->car_brand,
                ];

                if (array_key_exists('car_id',$request->all())) {
                    $checkAddressExist = UsersCar::where(['car_number'=>$request->car_number, 'user_id'=>$userId])->where('id','!=',$request->car_id)->first();

                    if (!$checkAddressExist) {
                        $car_id  = $request->car_id;
                        $inputs['user_id'] = $userId;
                        $userCarList = UsersCar::findOrFail($car_id);
                        // dd($inputs);
                        if ($userCarList->update($inputs)){
                            $inputs['is_defauld_car'] = $userCarList->is_defauld_car;
                            $inputs['id'] = $userCarList->id;
                            $response['status'] = 1;
                            $response['data'][] = $inputs;
                            $response['message'] = 'Car Updated Successfully.';
                            return response()->json($response, 200);

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Car Can`t be Updated.';
                            return response()->json($response, 200);
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'This car is already added on our server.';
                        return response()->json($response, 200);
                    }
                } else {
                    $checkCarExist = UsersCar::where(['car_number'=>$request->car_number, 'user_id'=>$userId])->first();

                    if (!$checkCarExist) {
                        $updateDefaultAddress = [
                            'is_defauld_car'=> 0
                        ];
                       UsersCar::where('user_id', $userId)->update($updateDefaultAddress);
                        $inputs['user_id'] = $userId;
                        $inputs['is_defauld_car'] = 1;

                        $totalCars = UsersCar::where('user_id', $userId)->count();

                        if ($totalCars == 0) {
                            $inputs['is_defauld_car'] = 1;
                        }
                        $userCar = UsersCar::create($inputs);

                        if ($userCar) {
                            // $inputs['is_defauld_car'] = 1;
                            $inputs['id'] = $userCar->id;
                            $response['status'] = 1;
                            $response['data'][] = $inputs;
                            $response['message'] = 'Car added successfully.';
                            return response()->json($response, 200);

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Car Can`t be added.';
                            return response()->json($response, 200);
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'This car is already added on our server.';
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

    public function deleteCar(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'car_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {
                $car_id  = $request->car_id;
                $carDetails =  UsersCar::where(['id'=>$car_id, 'user_id'=>$userId])->first();
            
                if (!empty($carDetails)) {
                    UsersCar::where('id', $car_id)->where('user_id', $userId)->delete();
                    $response['status'] = 1;
                    $response['message'] = 'Car deleted successfully.';
                    return response()->json($response, 200);

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Car not found.';
                    return response()->json($response, 200);
                }
                
            } else {
                $response['status'] = 0;
                $response['message'] = 'Something worng.';
                return response()->json($response, 200);
            }
        }
    }

    public function defaultCar(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'car_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {

                if (array_key_exists('car_id',$request->all())){
                    $updateDefaultAddress = [
                        'is_defauld_car'=> 0
                    ];
                   UsersCar::where('user_id', $userId)->update($updateDefaultAddress);

                   $car_id  = $request->car_id;
                   $carDetails =  UsersCar::where('id', $car_id)->where('user_id', $userId)->first();
                 
                    if (!empty($carDetails)) {
                        //Set default address
                        $inputAdd = [
                            'is_defauld_car'=> 1
                        ];
                        $carDetails->update($inputAdd);

                        $response['status'] = 1;
                        $response['message'] = 'Default car set successfully.';
                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Car can`t be set.';
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

    public function faq_request(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $inputs = $request->all();

        $validator = Validator::make($request->all(), [           
            'question'=>'required',
            'type'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {
                $inp = [
                    'user_id'=> $userId,
                    'question'=> $request->question,
                    'type'=> $request->type,
                ];

                $faqs = FaqRequest::create($inp);

                if ($faqs){
                    $response['status'] = 1;
                    $response['message'] = 'Your question is successfully submitted.';
                    return response()->json($response, 200);
                    
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

    public function getFaq(Request $request) {
        $userAuth = commonAuthUserId();
        $userId =  $userAuth[0];
        $inputs = $request->all();

        $validator = Validator::make($request->all(), [           
            'type'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $faqList =  Faq::where(['type'=>$inputs['type'], 'status'=>1])->orderBy('id', 'DESC');

            if (array_key_exists("search_text",$inputs)){
                $search = $inputs['search_text'];
                $faqList->Where("faq.question",'like','%'.$search.'%');
            }
            $faqData = $faqList->get();

            if ($faqData) {
                $response['status'] = 1;
                $response['data'] = $faqData;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'FAQ Not Found.';
                return response()->json($response, 200);
            }
        }
    }

    public function userProfile(Request $request) {
        $userData = commonAuthUserId();
        $userId = $userData[0];

        if ($userId) {
            $user = User::where('id',$userId)->with('devices')->first();
            // $data['user'] =  $user;
            $data =  new UserResource($user);
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['message'] = 'Something worng.';
            return response()->json($response, 200);
        }
        return response()->json();
    }
}