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
use App\Models\Brand;
use App\Models\Products;
use App\Models\ProductTags;
use App\Models\UsersAddress;
use App\Models\UsersCar;
use App\Models\Faq;
use App\Models\BrandCategory;
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

use JWTAuth;
use App\Models\UserPlatforms;
use App\Models\FaqRequest;
use App\Models\MainCategory;
use App\Models\Modes;
use App\Models\Language;
use App\Models\UserDevice;
use DB,Session;
use App;
use App\Http\Resources\CategoryResource;

class CommonApiController extends Controller {

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

    public function registerUser(Request $request)
    {
        $input =  $request->all();
            $message = [
                'mobile.required' => 'Mobile Number is required.',
                'mobile.min' => 'The Mobile number must be at least 7 characters',
                'mobile.max' => 'The Mobile number may not be greater than 15 characters.'
            ];
            $validator = Validator::make($input, [ 
                'name' => 'nullable|max:255',
                'email' => 'nullable|email|max:255',
                'country_code' => 'required',
                'mobile' => 'required|min:7|max:15',
                'password' => 'required|min:6|max:20',
                'device_type' => 'required',
                'device_token' => 'required',
                'platform' => 'required|in:KP,Shopya',
                'callback_url' => 'nullable',
                'user_type' => 'required',
            ],$message);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {  
            $user = User::where(['country_code'=>$input['country_code'],'mobile'=>$input['mobile'],'type'=>$input['user_type']])->first();

            if (isset($user)) {
                $randomPassword = Str::orderedUuid();
                $tokenData = encryptPass($user->id.'|~@#|'.$input['mobile'].'|~@#|'.$input['platform']);
                $secretKey = encryptPass($user->id.'|~@#|'.$input['mobile'].'|~@#|'.$input['platform'].'|~@#|'.$randomPassword);
                $newuserUpdateData = [];

                if (isset($input['first_name'])) {
                    $newuserUpdateData['name'] = $input['first_name'].' '.$input['last_name'] ?? '';
                    $newuserUpdateData['first_name'] = $input['first_name'];
                }

                if (isset($input['last_name'])) {
                    $newuserUpdateData['last_name'] = $input['last_name'];
                }

                if (isset($input['email'])) {
                    $newuserUpdateData['email'] = $input['email'];
                }
                $password = $input['country_code'].'-'.$input['mobile'];

                $newuserUpdateData['password'] = Hash::make($password);

                //Update User Data
                $newUser = User::updateOrCreate([
                    //Add unique field combo to match here
                    //For example, perhaps you only want one entry per user:
                    'id' => $user->id
                ],$newuserUpdateData);

                //update user device data
                if (isset($input['device_token'])) {
                    $data = UserDevice::where(['user_id'=>$user->id])->first();

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

                //Update Platform Data
                $newUserPlatform = UserPlatforms::updateOrCreate([
                    //Add unique field combo to match here
                    //For example, perhaps you only want one entry per user:
                    'user_id' => $user->id,
                    'platform' => $input['platform'],
                ],[
                    'platform_token' => $secretKey,
                    'uuid' => $randomPassword,
                    'callback_url' => $input['callback_url'] ?? null,
                ]);


                $data['user_id'] = $user->id;
                $data['access_key'] = $tokenData;
                $data['secret_key'] = $secretKey;
                $data['is_already_registered'] = 1;
                $response['status'] = 1;
                $response['message'] ='User is already registered';
                $response['data'] = $data;
                return response()->json($response, 200);

            } else {
                $userNewDB = new User;

                if (isset($input['first_name'])) {
                    $userNewDB->name = $input['first_name'].' '.$input['last_name'] ?? '';
                    $userNewDB->first_name = $input['first_name'];
                }

                if (isset($input['last_name'])) {
                    $userNewDB->last_name = $input['last_name'];
                }

                if (isset($input['email'])) {
                    $userNewDB->email = $input['email'];
                }

                $password = $input['country_code'].'-'.$input['mobile'];

                $userNewDB->country_code = $input['country_code'];
                $userNewDB->mobile = $input['mobile'];
                $userNewDB->password = Hash::make($password);
                $userNewDB->status = 1;
                $userNewDB->type = 0;

                if ($userNewDB->save()) {
                    $randomPassword = Str::orderedUuid();
                    $tokenData = encryptPass($userNewDB->id.'|~@#|'.$input['mobile'].'|~@#|'.$input['platform']);
                    $secretKey = encryptPass($userNewDB->id.'|~@#|'.$input['mobile'].'|~@#|'.$input['platform'].'|~@#|'.$randomPassword);

                    // Insert device token
                    $data = new UserDevice;
                    $data->user_id = $userNewDB->id;
                    $data->device_type = $input['device_type'];   
                    $data->device_token = $input['device_token'];
                    $data->save();

                    //update user data
                    $updateData = [];
                    $updateData['share_code'] = 'KILO-USER-'.$userNewDB->id;
                    User::where('id',$userNewDB->id)->update($updateData);

                    //Insert user platform data
                    $userPlatformData = new UserPlatforms;
                    $userPlatformData->user_id = $userNewDB->id;
                    $userPlatformData->platform = $input['platform'];
                    $userPlatformData->platform_token = $secretKey;
                    $userPlatformData->uuid = $randomPassword;
                    $userPlatformData->callback_url = $input['callback_url'] ?? null;
                    $userPlatformData->save();

                    $data['user_id'] = $userNewDB->id;
                    $data['access_key'] = $tokenData;
                    $data['secret_key'] = $secretKey;
                    $data['is_already_registered'] = 0;
                    $response['status'] = 1;
                    $response['message'] = 'User registered successfully.';
                    $response['data'] = $data;
                    return response()->json($response, 200);

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Technical error, Please try again.';
                    return response()->json($response, 200);
                }

            }
        }
    }

    /**
    * This function is use for mobile app to show popup for discount/info.
    *
    * @method Get
    */

    public function getModes()
    {
        $modes =  Modes::get();

        if (count($modes)) {
            $response['status'] = 1;
            $response['data'] = $modes;
            return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['message'] = 'Modes Not Found.';
            return response()->json($response, 200);
        }
    }

    public function getMainCategory()
    {
        $main_category = MainCategory::select('*')->where('status',1)->orderBy('position', 'ASC')->get();

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

    public function getLanguage()
    {
        $records = Language::select('*')->get();

        if (count($records)) {
            $response['status'] = 1;
            $response['data'] = $records;
            return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['data'] = array();
            $response['message'] = 'Language Not Found.';
            return response()->json($response, 200);
        }
    }
}