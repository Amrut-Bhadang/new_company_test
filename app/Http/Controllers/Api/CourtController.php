<?php

/****************************************************/
// Developer By @Inventcolabs.com
/****************************************************/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Models\Favorite;
use DB,Session,JWTAuth,App,Validator;

class CourtController extends Controller {

    /**
    * This function is use for mobile app to show discount coupons.
    *
    * @method Post
    */

    public function getDiscountCode(Request $request) {
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
            $query = CartParent::where(['id'=>$input['parent_cart_id']])->first();
            $date = new \DateTime();
            $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime(date('Y-m-d H:i:s'));
            $dt->setTimezone($tz);
            $dateNew = $dt->format('Y-m-d H:i:s');
            $dateNewFormat = $dt->format('Y-m-d');

            if ($query) {
                $discountIds = DiscountCategories::where(['category_id'=>$query->restaurant_id, 'category_type'=>'Restaurant'])->join('discount','discount.id','=','discount_categories.discount_id')->groupBy('discount_id')->pluck('discount_id')->toArray();

                $getDiscount = Discount::where(['status'=>1, 'category_type'=>'Flat-Discount'])->orWhereIn('id', $discountIds)->where('valid_upto', '>=', $dateNewFormat)->get();
                $newDataDiscount = [];

                if ($getDiscount) {

                    foreach ($getDiscount as $key => $value) {
                        $countOrderedCoupon = Orders::where(['discount_code'=>$value->discount_code])->count();

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
        $getDiscount = Discount::where(['status'=>1])->where('category_type', '!=', 'Info')->where('valid_upto', '>=', $dateNewFormat)->inRandomOrder()->first();

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

    /**
    * This function is use for mobile app to show old dashboard data.
    *
    * @method Post
    */

    public function getAllData(Request $request){

        $serachData = $request->all();
        $limit = 10;
        $radius = 10;
        $getCategory =$this->getCategory($serachData,$limit);
        $getDish = $this->getDishLatAndLong($serachData,$limit,$radius);
        $getCelebrity = $this->getCelebrity($serachData,$limit);
        // $getHostDish =  $this->getDish($serachData, $limit);
        $getHostDish =  $this->getHotDish($serachData, $limit);
        $getBanner = $this->getBanner();
        $cartCount = $this->getCart();
        
        if(count($getCategory)){
            $data['category'] = $getCategory;
            $data['categoryImageBaseUrl'] = asset('uploads/category').'/';
        }else{
            $data['category'] = [];
        }

        if(count($getDish)){
            $data['dish'] = $getDish;
            $data['dishImageBaseUrl'] = asset('uploads/product').'/';
        }else{
            $data['dish'] = [];
        }

        if(count($getCelebrity)){
            $data['celebrity'] = $getCelebrity;
            $data['celebrityImageBaseUrl'] = asset('uploads/user').'/';
        }else{
            $data['celebrity'] = [];
        }

        if(count($getHostDish)){
            $data['hotDish'] = $getHostDish;
            $data['hotDishImageBaseUrl'] = asset('uploads/product').'/';
        }else{
            $data['hotDish'] = [];
        }

        if(count($getBanner)){
            $data['banner'] = $getBanner;
            $data['bannerImageBaseUrl'] = asset('uploads/banner').'/';
        }else{
            $data['banner'] = [];
        }
        $data['cartCount'] = $cartCount;
        $response['status'] = 1;
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    /**
    * This function is use for mobile app to show latest dashboard data.
    *
    * @method Post
    */

    public function getHomeData(Request $request){

        $serachData = $request->all();
        $limit = 10;
        $radius = 10;
        $is_featured = 1;

        $validator = Validator::make($request->all(), [
            'longitude'=>'required',
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
            $main_category_name = MainCategory::select('id','name')->where('id',$serachData['main_category_id'])->first();
            $getCategory = $this->getCategory($serachData, $limit);
            $getFeaturedRestro =$this->getRestro($serachData, $limit, $is_featured);
            $getBrands = $this->getBrands($serachData, $limit);
            $getAllBrandCategory = BrandCategory::select('id','name')->where('status', 1)->get();
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
                    $value->distance = number_format($value->distance, 2).' KM';
                }
                $data['featuredRestro'] = $getFeaturedRestro;
                // $data['restroImageBaseUrl'] = asset('uploads/user').'/';

            } else {
                $data['featuredRestro'] = [];
            }
            $getTopGiftingItems = $this->getTopGiftingItems($serachData, $limit);

            if (count($getTopGiftingItems)){

                foreach ($getTopGiftingItems as $key => $value) {
                    $value->distance = number_format($value->distance, 2).' KM';
                }
                $data['topGiftingItems'] = $getTopGiftingItems;

            } else{
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
                    $value->distance = number_format($value->distance, 2).' KM';
                }
                $data['topDineInRestros'] = $getTopDineInRestro;

            } else {
                $data['topDineInRestros'] = [];
            }

            if (count($getTopPickupsRestro)) {

                foreach ($getTopPickupsRestro as $key => $value) {
                    $value->distance = number_format($value->distance, 2).' KM';
                }
                $data['topPickupsRestro'] = $getTopPickupsRestro;

            } else {
                $data['topPickupsRestro'] = [];
            }

            if (count($getBOGORestro)) {

                foreach ($getBOGORestro as $key => $value) {
                    $value->distance = number_format($value->distance, 2).' KM';
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

    public function getBanner($serachData){
        $bannerList = Media::select('category_type','category_id','link','file_path')->where(['medias.table_name'=>'Banner']);

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $bannerList->where('main_category_id',$main_category_id);
        }

        $bannerDetail = $bannerList->get();

        return $bannerDetail;
    }

    /**
    * This function is use for mobile app to show Services (Main Category).
    *
    * @method Get
    */

    public function getMainCategory(){
        $main_category = MainCategory::select('id','name','image','mix_order')->where('status',1)->orderBy('position', 'ASC')->get();
        if(count($main_category)){
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

    public function getCart(){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->get();
        $totalQty = 0;
        if(count($query)){
            foreach($query as $data){
                $totalQty += $data->qty;
            }
        }else{
            $totalQty = 0;
        }
        return $totalQty;
    }

    /**
    * This function is use for mobile app to show user addresses.
    *
    * @method Get
    */

    public function getAllAddress() {
        $userData = auth()->user();
        $userId =  $userData->id;

        if (!empty($userId)) {
            $userAddressList =  UsersAddress::where('user_id',$userId)->orderBy('id', 'DESC')->get();

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

    public function addAddress(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $validator = Validator::make($request->all(), [           
            'address'=>'required',
            'longitude'=>'required',
            'latitude' => 'required',
            'address_type'=>'required',
            'building_number'=>'required',
            'building_name'=>'required',
            ]);

        if ($validator->fails()) {
            $errors 	=	$validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {
                $inputs = [
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                    'address_type'=>$request->address_type,
                    'building_number' =>$request->building_number,
                    'building_name' =>$request->building_name,
                    'landmark' =>$request->landmark,
                    'address'=>$request->address,
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

                if (array_key_exists('address_id',$request->all())) {

                    // $checkAddressExist = UsersAddress::where(['latitude'=>$request->latitude, 'longitude'=>$request->longitude, 'user_id'=>$userId])->where('id','!=',$request->address_id)->first();
                    $checkAddressExist = UsersAddress::where(['address_type'=>$request->address_type, 'user_id'=>$userId])->where('id','!=',$request->address_id)->first();

                    if (!$checkAddressExist) {
                        $address_id  = $request->address_id;
                        $inputs['user_id'] = $userId;
                        $userAdderssList = UsersAddress::findOrFail($address_id);
                        // dd($inputs);
                        if ($userAdderssList->update($inputs)){
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
                    $checkAddressExist = UsersAddress::where(['address_type'=>$request->address_type, 'user_id'=>$userId])->first();

                    if (!$checkAddressExist) {
                        $userHaveAddress = UsersAddress::where(['user_id'=>$userId])->first();

                        if (!$userHaveAddress) {
                            $inputs['is_defauld_address'] = 1;

                            $inputAddress = [
                                'address'=> $request->address,
                                'latitude'=>$request->latitude,
                                'longitude' =>$request->longitude,
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

    public function deleteAddress(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'address_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {
                $address_id  = $request->address_id;
                $addressDetails =  UsersAddress::where(['id'=>$address_id, 'user_id'=>$userId])->first();
            
                if (!empty($addressDetails)) {
                    UsersAddress::where('id', $address_id)->where('user_id', $userId)->delete();
                    $defaultAddress  = UsersAddress::where('user_id', $userId)->get()->count();
                    $inp = ['address' => '','latitude'=>'','longitude'=>''];

                    if ($defaultAddress == 0){
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

    public function getDefaultAddress() {
        $userData = auth()->user();
        $userId =  $userData->id;

        if (!empty($userId)) {
            $userAddressList =  UsersAddress::where(['user_id'=>$userId, 'is_defauld_address'=>1])->first();

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

    public function wallet_list() {
        $userData = auth()->user();
        $userId =  $userData->id;
        $User = User::findOrFail($userId);
        $taxQARData = Tax::select('tax.*','countries.name','countries.phonecode','countries.sortname','currency.currency_code')->join('countries', 'countries.id', '=', 'tax.country_id')->leftJoin('currency', 'currency.id', '=', 'tax.currency_id')->where('currency.currency_code', 'QAR')->first();
        $response['data']['usd_to_qar'] = $taxQARData->difference_amount;

        $taxAEDData = Tax::select('tax.*','countries.name','countries.phonecode','countries.sortname','currency.currency_code')->join('countries', 'countries.id', '=', 'tax.country_id')->leftJoin('currency', 'currency.id', '=', 'tax.currency_id')->where('currency.currency_code', 'AED')->first();
        $response['data']['usd_to_aed'] = $taxAEDData->difference_amount;

        if (!empty($userId)) {
            $totalCR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'CR'])->sum('amount');
            $totalDR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'DR'])->sum('amount');
            $userWalletList =  UserWallets::select('transaction_type','amount','comment','transaction_id','created_at')->where(['user_id'=>$userId])->orderBy('id', 'desc')->get();

            if (count($userWalletList)) {
                $response['status'] = 1;
                $available_balance = $totalCR-$totalDR;
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

    function saveOrder(Request $request) {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'app_type'=>'required',
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

    public function add_money(Request $request) {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'amount'=>'required|numeric|not_in:0',
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
                    $totalCR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'CR'])->sum('amount');
                    $totalDR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'DR'])->sum('amount');
                    $response['status'] = 1;
                    $response['message'] = 'Amount added successfully.';
                    $response['data']['available_balance'] = (float)number_format($totalCR-$totalDR, 2, '.', '');
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

    public function update_transaction(Request $request) {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'amount'=>'required|numeric|not_in:0',
            'transaction_type'=>'required',
            'comment'=>'required',
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
                    $totalCR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'CR'])->sum('amount');
                    $totalDR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'DR'])->sum('amount');
                    $response['status'] = 1;
                    $response['message'] = 'Amount added successfully.';
                    $response['data']['available_balance'] = (float)number_format($totalCR-$totalDR, 2, '.', '');
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

    public function defaultAddress(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [           
            'address_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (!empty($userId)) {

                if (array_key_exists('address_id',$request->all())){
                    $updateDefaultAddress = [
                        'is_defauld_address'=> 0
                    ];
                   UsersAddress::where('user_id', $userId)->update($updateDefaultAddress);

                   $address_id  = $request->address_id;
                   $addressDetails =  UsersAddress::where('id', $address_id)->where('user_id', $userId)->first();
                 
                    if (!empty($addressDetails)) {
                        $inputs = [
                            'address'=> $addressDetails->address,
                            'latitude'=>$addressDetails->latitude,
                            'longitude' =>$addressDetails->longitude
                        ];
                        $usersList = User::findOrFail($userId);
                        $usersList->update($inputs);

                        //Set default address
                        $inputAdd = [
                            'is_defauld_address'=> 1
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

    public function getAllCategory(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $getCategory =$this->getCategory($serachData,$limit);

        
        if(count($getCategory)){
            $data = $getCategory;

        } else {
            $data = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        // $response['imageBaseUrl'] = asset('uploads/category').'/';
        
        return response()->json($response, 200);
    }

    public function getSubCategory(Request $request){
        $serachData = $request->all();
        $limit =  'All';

        $validator = Validator::make($request->all(), [           
            'category_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            $data = Subcategory::select('id','main_category_id','category_id','name','parent_id','parent_id')->where(['category_id'=>$request->category_id, 'status'=>1])->where('parent_id', '=', 0)->get();

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

    function childCategory($childs) {

        foreach ($childs as $key => $child) {
            
            if (count($child->childs)) {
                $this->childCategory($child->childs);
            }
        }
    }

    /**
    * This function is use for mobile app to get all restaurants.
    *
    * @method Post
    */

    public function getAllRestro(Request $request){
        $serachData = $request->all();
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $limit = 20;
            $is_featured = false;
            $is_pagination = 1;
            $min_price = Restaurant::select(\DB::raw("MIN(cost_for_two_price) AS min, MAX(cost_for_two_price) AS max"))->where('restaurants.main_category_id',$serachData['main_category_id'])->first();
            $getRestro = $this->getRestro($serachData, $limit, $is_featured, $is_pagination);

            if (count($getRestro)) {

                foreach ($getRestro as $key => $value) {
                    $category_arr = [];
                    $productsCategory = Products::select('categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$value->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->get();

                    foreach ($productsCategory as $k => $v) {
                        $category_arr[] = $v->name;
                    }

                    if (!empty($category_arr)) {
                        $value->category = implode(", ", $category_arr);

                    } else {
                        $value->category = '';
                    }
                    $value->distance = number_format($value->distance, 2).' KM';
                    // $value->avg_rating = number_format(5, 1);
                }
                $data = $getRestro;
                $response['status'] = 1;
                $response['data'] = $data;
                $response['min_price'] = $min_price->min;
                $response['max_price'] = $min_price->max;

            } else {
                $response['status'] = 0;
                $response['message'] = 'No restaurant found.';
                $response['min_price'] = $min_price->min;
                $response['max_price'] = $min_price->max;
            }
            return response()->json($response, 200);
        }
    }

    /**
    * This function is use for mobile app to get restaurant detail by id.
    *
    * @method Post
    */

    public function restroDetail(Request $request){
        $serachData = $request->all();
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
            'restro_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $limit = 20;
            $is_featured = false;
            $is_pagination = 1;
            $category_arr = [];

            if ($serachData['main_category_id']) {
                $restroDetail = Restaurant::select('*')->where(['id'=>$serachData['restro_id'],'main_category_id'=>$serachData['main_category_id'], 'status'=>1])->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();
            } else {
                $restroDetail = Restaurant::select('*')->where(['id'=>$serachData['restro_id'], 'status'=>1])->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();
            }

            if ($restroDetail) {
                $getMostOrderDishIds = OrdersDetails::select(\DB::raw("COUNT(order_details.id) AS total_count, order_details.product_id"))->where(['restaurant_id'=>$restroDetail->id])->join('orders','orders.id','=','order_details.order_id')->join('product_attributes','product_attributes.product_id','=','order_details.product_id')->groupBy('order_details.product_id')->orderBy('total_count', 'desc')->pluck('order_details.product_id')->toArray();

                if ($getMostOrderDishIds) {
                    $restroDetail['most_order_dishes'] = Products::where(['products.is_active'=>1,'restaurant_id'=>$restroDetail->id])->whereIn('products.id',$getMostOrderDishIds)->orderByRaw("field(products.id,".implode(',',$getMostOrderDishIds).")")->get();
                } else {
                    $restroDetail['most_order_dishes'] = array();
                }

                $restroDetail['most_kp'] = Products::where('products.is_active',1)->where(['restaurant_id'=>$restroDetail->id])->orderBy("points", 'DESC')->limit(10)->get();

                $restroDetail['most_extra_kp'] = Products::where('extra_kilopoints', '>=', 1)->where(['restaurant_id'=>$restroDetail->id, 'products.is_active'=>1])->orderBy("extra_kilopoints", 'DESC')->limit(10)->get();

                 //$restroDetail['avg_rating'] = number_format(5, 1);
                $restroDetail['distance'] = number_format($restroDetail->distance, 2).' KM';
                $productsCategory = Products::select('products.category_id','products.restaurant_id','categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->join('categories','categories.id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$restroDetail->id, 'categories.status'=>1])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->with(array('dishes' => function ($query) use ($restroDetail) {
                        $query->where('products.restaurant_id', $restroDetail->id);
                }))->get();
                

                if ($productsCategory) {

                    foreach ($productsCategory as $k => $v) {
                        $dish_new = [];

                        if ($v->dishes) {

                            foreach ($v->dishes as $k_new => $v_new) {
                                // echo "<pre>"; print_r($v_new->product_attributes->toArray());die;

                                if (empty($v_new->product_attributes->toArray())) {
                                    // unset($v->dishes[$k_new]);
                                } else {
                                    // echo "<pre>"; print_r($v_new->product_attributes->toArray());die;
                                    array_push($dish_new, $v_new);
                                }
                            }

                            $v->dishes = $dish_new;
                        }                     

                        $category_arr[] = $v->name;
                    }
                }


                if (!empty($category_arr)) {
                    // $value->restroList['category'] = implode(", ", $category_arr);
                    $restroDetail['category'] = implode(", ", $category_arr);

                } else {
                    $restroDetail['category'] = '';
                }

                $restroDetail['productsCategory'] = $productsCategory;

                $data = $restroDetail;
                $response['status'] = 1;
                $response['data'] = $data; 

            } else {
                $response['status'] = 0;
                $response['message'] = 'No restaurant found.'; 
            }
            return response()->json($response, 200);
        }
    }

    /**
    * This function is use for mobile app to get all brands.
    *
    * @method Post
    */

    public function getAllBrand(Request $request){
        $serachData = $request->all();
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $limit = 20;
            $is_featured = false;
            $is_pagination = 1;
            $getBrands = $this->getBrands($serachData, $limit, $is_pagination);

            foreach ($getBrands as $key => $value) {
                $restroList = Restaurant::select('restaurants.id','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open')->where(['restaurants.status' => 1, 'restaurants.brand_id' => $value->id])->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->orderBy('distance', 'asc')->get();
                $value->outlets = count($restroList);
                $category_arr = [];

                if (count($restroList)) {
                    $value->restroList = $restroList[0];
                    $productsCategory = Products::select('categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$restroList[0]->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->get();

                    foreach ($productsCategory as $k => $v) {
                        $category_arr[] = $v->name;
                    }

                    if (!empty($category_arr)) {
                        // $value->restroList['category'] = implode(", ", $category_arr);
                        $value->restroList['category'] = $category_arr;

                    } else {
                        $value->restroList['category'] = [];
                    }
                    
                    $value->restroList['distance'] = number_format($restroList[0]->distance, 2).' KM';
                    $value->restroList['is_open'] = getRestroTimeStatus($restroList[0]->id);

                } else {
                    $value->restroList = (object)[];
                }
            }
            
            if (count($getBrands)){
                $data = $getBrands;
                $response['status'] = 1;
                $response['data'] = $data; 

            } else {
                $response['status'] = 0;
                $response['message'] = 'No brand found.'; 
            }       
            return response()->json($response, 200);
        }
    }

    /**
    * This function is use for mobile app to get brand detail by id.
    *
    * @method Post
    */

    public function getBrandDetail(Request $request){
        $serachData = $request->all();

        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
            'brand_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $limit = 20;
            $is_pagination = 1;
            $brandDetail = Brand::select('id', 'name', 'brand_type', 'file_path')->where(['id'=>$serachData['brand_id'], 'status'=>1])->first();

            if ($brandDetail) {
                $getBrandRestro = $this->getBrandRestro($serachData, $limit, $is_pagination);
                
                if (count($getBrandRestro)){

                    foreach ($getBrandRestro as $key => $value) {
                        $value->is_open = getRestroTimeStatus($value->id);
                        $category_arr = [];
                        $productsCategory = Products::select('categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$value->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->get();

                        foreach ($productsCategory as $k => $v) {
                            $category_arr[] = $v->name;
                        }

                        if (!empty($category_arr)) {
                            // $value->restroList['category'] = implode(", ", $category_arr);
                            $value->category = $category_arr;

                        } else {
                            $value->category = [];
                        }
                        $value->distance = number_format($value->distance, 2).' KM';
                    }
                    $data = $brandDetail;
                    $data['outlets'] = count($getBrandRestro);
                    $data['restroList'] = $getBrandRestro;
                    $response['status'] = 1;
                    $response['data'] = $data; 

                } else {
                    $response['status'] = 1;
                    $response['message'] = 'No restaurant found.';
                    $data = $brandDetail;
                    $data['outlets'] = 0;
                    $data['restroList'] = (object)[];
                    $response['data'] = $data; 
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'No brand detail found.'; 
            }

            return response()->json($response, 200);
        }
    }

    /**
    * This function is use for mobile app to get all items.
    *
    * @method Post
    */

    public function getAllDish(Request $request){
        $serachData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $limit =  20;
            $radius = 10;
            $is_pagination = 1;
            // $getDish = $this->getDishLatAndLong($serachData, $limit, $radius, $is_pagination);

            if(array_key_exists("main_category_id",$serachData)) {
                $main_category_id = $serachData['main_category_id'];

                $min_price = Products::select(\DB::raw("MIN(price) AS min, MAX(price) AS max"))->where('main_category_id',$main_category_id)->first();
                $min_max_point = Products::select(\DB::raw("MIN(points) AS min, MAX(points) AS max"))->where('main_category_id',$main_category_id)->first();
                $min_max_discount = Products::select(\DB::raw("MIN(discount_price) AS min, MAX(discount_price) AS max"))->where('main_category_id',$main_category_id)->first(); 
                
            } else {
                $min_price = Products::select(\DB::raw("MIN(price) AS min, MAX(price) AS max"))->first();
                $min_max_point = Products::select(\DB::raw("MIN(points) AS min, MAX(points) AS max"))->first();
                $min_max_discount = Products::select(\DB::raw("MIN(discount_price) AS min, MAX(discount_price) AS max"))->first();
            }
            $getDish = $this->getDishsList($serachData, $limit, $radius, $is_pagination);

            if (isset($serachData['slug']) && !empty($serachData)) {

                if ($serachData['slug'] == 'pick') {
                    $getDish =  $this->getTopPickupsDish($serachData, $limit, $is_pagination);

                } else if ($serachData['slug'] == 'top') {
                    $getDish =  $this->getDish($serachData, $limit, $radius, $is_pagination);

                } else if ($serachData['slug'] == 'fire') {
                    $getDish =  $this->getDish($serachData, $limit, $radius, $is_pagination);

                } else if ($serachData['slug'] == 'top_gift') {
                    $getDish =  $this->getTopGiftingItems($serachData, $limit, $is_pagination);
                }
            }

            if (count($getDish)) {

                foreach ($getDish as $key => $value) {
                    /*$toppings = Topping::where(['dish_id'=>$value->id, 'status'=>1])->count();

                    if ($toppings) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }*/

                    $value->qty = (int)Cart::where(['user_id'=>$userId, 'product_id'=>$value->id])->sum('qty');

                    $mandatory_price = Topping::where(['dish_id'=>$value->id,'is_mandatory'=>1, 'status'=>1])->sum('price');

                    $value->price = $mandatory_price + $value->price;


                    if ($value->product_for != 'dish') {

                        $selected_show_attribute = ProductAttributes::select('product_attributes.*')->where(['product_id' => $value->id])->orderBy('price', 'ASC')->first();

                        if ($selected_show_attribute) {
                            $value->price = $selected_show_attribute->price;
                            $value->points = $selected_show_attribute->points;
                        }
                    }

                    $attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id','dish_toppings.status')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$value->id,'topping_choose'=>0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
                            $query->where(['dish_toppings.dish_id'=>$value->id]);
                        }))->get();

                    // $attributes_new = ProductAttributes::select('product_attributes.*','dish_toppings.main_category_id','dish_toppings.category_id','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.id','=','product_attributes.dish_topping_id')->where('dish_toppings.dish_id', $value->id)->get();

                    /*$product_attributes = AttributesLang::select('attributes_lang.id', 'attributes_lang.name', 'attributes_lang.topping_choose', 'attributes.main_category_id', 'attributes.category_id' )->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.category_id'=>$value->category_id,'topping_choose'=>0])->get();

                    if ($product_attributes) {

                        foreach ($product_attributes as $k => $v) {
                            $v->attributeValues = AttributeValueLang::select('attribute_value_lang.id','attribute_value_lang.name')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where(['attribute_values.attributes_lang_id'=>$v->id])->get();
                        }
                    }*/

                    // $product_attributes = ProductAttributes::select('product_attributes.*', 'attributes_lang.name as attribute_name')->where(['product_id' => $value->id])->leftJoin('attributes_lang', 'attributes_lang.id', '=', 'product_attributes.attributes_lang_id')->with('AttributeValues')->get();

                    $product_attributes = ProductAttributeValues::select('product_attribute_values.id','product_attribute_values.attributes_lang_id','attributes_lang.name as attribute_name','attributes_lang.topping_choose')->where(['product_id' => $value->id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attributes_lang', 'attributes_lang.id', '=', 'product_attribute_values.attributes_lang_id')->groupBy('product_attribute_values.attributes_lang_id')->get();

                    if ($product_attributes) {

                        if (count($product_attributes) > 0) {
                            $value->is_topping = 1;
                        } else {
                            $value->is_topping = 0;
                        }

                        foreach ($product_attributes as $k => $v) {
                            $v->attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id')->where(['product_id' => $value->id, 'product_attribute_values.attributes_lang_id' => $v->attributes_lang_id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->get();
                        }

                    } else {
                        $value->is_topping = 0;
                    }
                    

                    $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id','dish_toppings.status')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$value->id,'topping_choose'=>1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
                            $query->where('dish_toppings.dish_id', $value->id);
                        }))->get();

                    $value->product_attributes = $product_attributes;
                    $value->attributes = $attributes;
                    $value->add_on = $add_on;
                    $value->avg_rating = number_format(0, 1);
                    $value->distance = number_format($value->distance, 2).' KM';
                }
                $response['status'] = 1;
                $response['data'] = $getDish;
                $response['min_price'] = $min_price->min;
                $response['max_price'] = $min_price->max;
                $response['min_kilo'] = $min_max_point->min;
                $response['max_kilo'] = $min_max_point->max;
                $response['min_discount'] = $min_max_discount->min;
                $response['max_discount'] = $min_max_discount->max;

            } else {
                $response['status'] = 0;
                $response['message'] = 'No dish found.';
                $response['min_price'] = $min_price->min;
                $response['max_price'] = $min_price->max;
                $response['min_kilo'] = $min_max_point->min;
                $response['max_kilo'] = $min_max_point->max;
                $response['min_discount'] = $min_max_discount->min;
                $response['max_discount'] = $min_max_discount->max;
            }

            return response()->json($response, 200);
        }
    }

    /**
    * This function is use for mobile app to get all celebrity category.
    *
    * @method Post
    * @notuse
    */


    public function getAllCelebrityCategory(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $radius = 10;

        $getCelebrityCategory = $this->getCelebrityCategory($serachData);
        $serachData['category_id'] = $getCelebrityCategory[0]->id;
        
        $getCelebrityDetails =$this->getCelebrityDetail($serachData);
        $response['status'] = 1;
        $response['celebrityDetails'] = $getCelebrityDetails;
        $response['getCelebrityCategory'] = $getCelebrityCategory;
        $response['celebrityBaseUrl'] = asset('uploads/user').'/';
        return response()->json($response, 200);
    }

    /**
    * This function is use for mobile app to get all celebrity items.
    *
    * @method Post
    * @notuse
    */

    public function getAllCelebrityDish(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $radius = 10;
        $response['product'] = $this->getDishLatAndLongCategory($serachData,$limit,$radius);
        $response['productBaseUrl'] = asset('uploads/product').'/';
        return response()->json($response, 200);
    }

    /**
    * This function is use for mobile app to get all celebrity items.
    *
    * @global function for this class.
    * @notuse
    */

    public function getCelebrityCategory($serachData){

        $productsList =  Category::select('categories.*')
            ->join('products','products.category_id','=','categories.id')
            ->groupBy('categories.id')
            ->where('products.is_active',1)
            ->where('products.celebrity_id', $serachData['celebrity_id']);
           

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }
        if(array_key_exists("sort_by",$serachData)){
			$sort = $serachData['sort_by'];
			if($sort == A_TO_Z){  
				$productsList->orderBy('categories.name', 'asc');
			}else if($sort == Z_TO_A){
				$productsList->orderBy('categories.name', 'desc');
			}
        }
        $productsDetail = $productsList->get();
        return $productsDetail;
    }

    /**
    * This function is use for mobile app to get all celebrity items.
    *
    * @method Get
    * @notuse
    */

    public function getAllCelebrity(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $getCelebrity =$this->getCelebrity($serachData,$limit);
        
        if(count($getCelebrity)){
            $data['celebrity'] = $getCelebrity;
        }else{
            $data['celebrity'] = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        $response['imageBaseUrl'] = asset('uploads/user').'/';
        return response()->json($response, 200);
    }


    public function getAllGift(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $getGift =$this->getGift($serachData,$limit);
        
        if(count($getGift)){
            $data['gift'] = $getGift;
        }else{
            $data['gift'] = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        $response['imageBaseUrl'] = asset('uploads/gift').'/';
        return response()->json($response, 200);
    }

    public function getAllGiftCategory(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $getGiftCategory =$this->getGiftCategory($serachData,$limit);
        
        if(count($getGiftCategory)){
            $data['giftCategory'] = $getGiftCategory;
        }else{
            $data['giftCategory'] = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        $response['imageBaseUrl'] = asset('uploads/gift-category').'/';
        return response()->json($response, 200);
    }
    public function getCelebrityDetails(Request $request){
        $queryString = $request->all();
        $limit =  'All';
        $getCelebrity =$this->getCelebrityDetail($queryString);
        $getCategory = $this->getCelebrityCategory($queryString);
        $getDish = $this->getDish($queryString,$limit);
        
        if(count($getCelebrity)){
            $data['celebrity'] = $getCelebrity;
        }else{
            $data['celebrity'] = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        $response['category'] = $getCategory;
        $response['dish'] = $getDish;
        $response['celebrityBaseUrl'] = asset('uploads/user').'/';
        $response['categoryBaseUrl'] = asset('uploads/category').'/';
        $response['dishBaseUrl'] = asset('uploads/product').'/';
        return response()->json($response, 200);
    }


    public function getProductDetails(Request $request){
        $serachData = $request->all();
        $userData = auth()->user();
        $userId = '';

        if ($userData) {
            $userId = $userData->id;
        }

        $validator = Validator::make($request->all(), [
            'main_category_id' => 'required',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $in_cart = 0;
            $getDish = $this->getDishDetail($serachData);

            if ($getDish) {
                $getDish->is_open = getRestroTimeStatus($getDish->restaurant_id);
                // echo "<pre>"; print_r($getDish->toArray());die;
                /*$toppings = Topping::where(['dish_id'=>$getDish->id, 'status'=>1])->count();

                if ($toppings) {
                    $getDish->is_topping = 1;

                } else {
                    $getDish->is_topping = 0;
                }*/

                $mandatory_price = Topping::where(['dish_id'=>$getDish->id,'is_mandatory'=>1, 'status'=>1])->sum('price');

                $getDish->price = $mandatory_price + $getDish->price;

                $getDish->varients = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id','dish_toppings.status')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$getDish->id])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($getDish) {
                        $query->where(['dish_toppings.dish_id'=>$getDish->id]);
                    }))->get();

                $selected_product_attributes = ProductAttributeValues::select('product_attribute_values.id','product_attribute_values.attributes_lang_id','attributes_lang.name as attribute_name','attributes_lang.topping_choose')->where(['product_id' => $getDish->id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attributes_lang', 'attributes_lang.id', '=', 'product_attribute_values.attributes_lang_id')->groupBy('product_attribute_values.attributes_lang_id')->get();

                if ($selected_product_attributes) {

                    if (count($selected_product_attributes) > 0) {
                        $getDish->is_topping = 1;
                    } else {
                        $getDish->is_topping = 0;
                    }
                    /*foreach ($product_attributes as $k => $v) {
                        $v->attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id','product_attribute_values.attributes_lang_id')->where(['product_id' => $getDish->id, 'product_attribute_values.attributes_lang_id' => $v->attributes_lang_id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->groupBy('attribute_value_lang.id')->get();
                    }*/

                } else {
                    $getDish->is_topping = 0;
                }
                $attrVals = [];
                $attrValsNew = [];

                $selected_show_attribute = ProductAttributes::select('product_attributes.*')->where(['product_id' => $getDish->id])->orderBy('price', 'ASC')->first();

                if ($selected_show_attribute) {

                    if ($selected_show_attribute->discount_price > 0) {
                        $getDish->price = $selected_show_attribute->price;
                        $getDish->discount_price = $selected_show_attribute->discount_price;

                    } else {
                        $getDish->price = $selected_show_attribute->price;
                        $getDish->discount_price = $selected_show_attribute->price;
                    }
                    // $getDish->discount_price = $selected_show_attribute->discount_price;
                    $getDish->points = $selected_show_attribute->points;
                    $attrVals = ProductAttributeValues::select('product_attribute_values.*')->where(['product_attributes_id' => $selected_show_attribute->id])->pluck('attribute_value_lang_id')->toArray();

                    $attrValsNew = ProductAttributeValues::select('product_attribute_values.*')->where(['product_attributes_id' => $selected_show_attribute->id])->pluck('product_attribute_values.id')->toArray();

                    if ($userId) {
                        $cart_parent = CartParent::where('user_id',$userId)->first();

                        if ($cart_parent) {
                            $checkProductAddedInCart = CartDetail::where('parent_cart_id',$cart_parent->id)->whereIn('product_attribute_values_id', $attrValsNew)->get();

                            if (count($checkProductAddedInCart)) {
                                $in_cart = 1;
                            }
                        }
                    }

                } else {

                    if ($userId) {
                        $cart_parent = CartParent::where('user_id',$userId)->first();

                        if ($cart_parent) {
                            $checkProductAddedInCart = Cart::where('parent_cart_id',$cart_parent->id)->where('product_id', $getDish->id)->first();

                            if ($checkProductAddedInCart) {
                                $in_cart = 1;
                            }
                        }
                    }
                }

                $getDish->in_cart = $in_cart;
                $getDish->product_attribute_values_id = implode(',', $attrValsNew);


                $product_attributes = AttributesLang::select('attributes_lang.name as attribute_name','attributes_lang.id as attributes_lang_id','attributes_lang.is_color')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.main_category_id'=>$getDish->main_category_id, 'attributes.category_id'=>$getDish->category_id])->get();

                foreach ($product_attributes as $key => $value) {
                    $attributeValues = AttributeValueLang::select('attribute_value_lang.name as attribute_value_name','attribute_value_lang.id as attribute_value_lang_id','attribute_value_lang.color_code')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where('attribute_values.attributes_lang_id', $value->attributes_lang_id)->get();

                    if (count($attributeValues) < 1) {
                        unset($product_attributes[$key]);
                    }

                    
                    foreach ($attributeValues as $k => $v) {

                        /*$v->product_attribute_values_id = ProductAttributeValues::select('product_attribute_values.*')->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->where(['product_id' => $getDish->id,'product_attribute_values.attributes_lang_id' => $value->attributes_lang_id,'product_attribute_values.attribute_value_lang_id' => $v->attribute_value_lang_id])->get();*/

                        if (in_array($v->attribute_value_lang_id, $attrVals)) {
                            $v->is_selected = 1;

                        } else {
                            $v->is_selected = 0;
                        }
                    }
                    $value->attributeValues = $attributeValues;

                }
                $getDish->product_attributes_new = $product_attributes;
                // print($varients);die;

                /*$attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id','dish_toppings.status')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$getDish->id,'topping_choose'=>0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($getDish) {
                        $query->where(['dish_toppings.dish_id'=>$getDish->id]);
                    }))->get();

                $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id','dish_toppings.status')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$getDish->id,'topping_choose'=>1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($getDish) {
                        $query->where('dish_toppings.dish_id', $getDish->id);
                    }))->get();

                $getDish->attributes = $attributes;
                $getDish->add_on = $add_on;*/
            }

            $response['status'] = 1;
            $response['data'] = $getDish;
            return response()->json($response, 200);
        }
    }

    public function checkProductAvailbility(Request $request){
        $serachData = $request->all();
        $userData = auth()->user();

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'attributes_lang_id' => 'required',
            'attribute_value_lang_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $getDish = $this->getDishDetail($serachData);

            if ($getDish) {
                $attributesLangIds = explode(",", $serachData['attributes_lang_id']);
                $attributesValueLangIds = explode(",", $serachData['attribute_value_lang_id']);
                $countIds = count($attributesValueLangIds);
                $in_cart = 0;

                /*$product_attributes = Products::select('products.*')->where(['product_id' => $serachData['product_id']])->join('product_attributes', 'products.id', '=', 'product_attributes.product_id')->get()->toArray();*/

                $product_attributes = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id', 'product_attribute_values.attributes_lang_id','product_attribute_values.product_attributes_id', DB::raw('count(product_attribute_values.product_attributes_id) as total'), DB::raw('group_concat(product_attribute_values.id) as product_attribute_values_id'))->where(['product_id' => $serachData['product_id']])->whereIn('product_attribute_values.attribute_value_lang_id', $attributesValueLangIds)->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->groupBy('product_attribute_values.product_attributes_id')->having('total', $countIds)->first();

                if ($product_attributes) {

                    if ($product_attributes->discount_price > 0) {
                        $product_attributes->price = $product_attributes->price;
                        $product_attributes->discount_price = $product_attributes->discount_price;

                    } else {
                        $product_attributes->price = $product_attributes->price;
                        $product_attributes->discount_price = $product_attributes->price;
                    }

                    $product_attribute_values_id = explode(",", $product_attributes->product_attribute_values_id);

                    if ($userData && $userData->id) {
                        $userId =  $userData->id;
                        $cart_parent = CartParent::where('user_id',$userId)->first();

                        if ($cart_parent) {
                            $checkProductAddedInCart = CartDetail::where('parent_cart_id',$cart_parent->id)->whereIn('product_attribute_values_id', $product_attribute_values_id)->get();

                            if (count($checkProductAddedInCart)) {
                                $in_cart = 1;
                            }
                        }
                    }
                    $product_attributes->in_cart = $in_cart;
                    $response['status'] = 1;
                    $response['data'] = $product_attributes;

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Product is out of stock.';
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid dish selection.';
            }
            return response()->json($response, 200);
        }
    }
    
    public function getCelebrityDetail($queryString){
        
        $usersList = User::select('users.*')->where(['users.type'=>3,'users.status'=>1]);
       
        if(array_key_exists("celebrity_id",$queryString)){
            $celebrity_id = $queryString['celebrity_id'];
			$usersList->where("users.id",$celebrity_id);
        }
        $usersDetail = $usersList->get();

        return $usersDetail;
    }

    public function getCategory($serachData,$limit) {
        $getAllDishCatIds = Products::where(['is_active'=>1])->groupBy('category_id')->pluck('category_id')->toArray();
        $categoryList = Category::select('id', 'name', 'description', 'image', 'main_category_id')->where(['type'=>1, 'status'=>1])->whereIn('id', $getAllDishCatIds);

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $categoryList->where('main_category_id',$main_category_id);
        }

        if(array_key_exists("search_text",$serachData)){
			$search = $serachData['search_text'];
			$categoryList->Where("categories.name",'like','%'.$search.'%');
        }
        if($limit != 'All'){
            $categoryList->limit($limit);
        }
        $categoryDetail= $categoryList->get();
        return $categoryDetail;
    }

    public function getBrands($serachData, $limit, $is_pagination = 0, $slug=''){
        // $getRestroBrandIds = Restaurant::where(['status'=>1])->groupBy('brand_id')->pluck('brand_id')->toArray();
        $getRestroBrandIds = Restaurant::select('restaurants.brand_id')->join('products','products.restaurant_id','=','restaurants.id')->where(['restaurants.status'=>1])->groupBy('restaurants.brand_id')->pluck('brand_id')->toArray();
        $brandList = Brand::select('id', 'name', 'brand_type', 'brand_category', 'file_path')->where(['status'=>1])->whereIn('id', $getRestroBrandIds);

        if ($slug && !empty($slug)) {
            $brandList->Where("brands.brand_category", $slug);            
        }

        if (array_key_exists("brand_category",$serachData)){
            $brand_category = $serachData['brand_category'];
            $brandList->Where("brands.brand_category", $brand_category);
        }

        if (array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $brandList->Where("brands.name",'like','%'.$search.'%');
        }

        if (array_key_exists("brand_category",$serachData)){
            $brand_category = $serachData['brand_category'];
            $brandList->Where("brands.brand_category",$brand_category);
        }

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $brandList->where('main_category_id',$main_category_id);
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $brandDetail = $brandList->paginate($limit);

            } else {
                $brandList->limit($limit);
                $brandDetail = $brandList->get();
            }

        } else {
            $brandDetail = $brandList->get();
        }
        
        return $brandDetail;
    }
    

    public function getGift($serachData,$limit){
        $giftList = Gift::where(['is_active'=>1]);
        if(array_key_exists("search_text",$serachData)){
			$search = $serachData['search_text'];
			$giftList->Where("gifts.name",'like','%'.$search.'%');
        }
        if($limit != 'All'){
            $giftList->limit($limit);
        }
        if(array_key_exists("category_id",$serachData)){
            $category_id = $serachData['category_id'];
			$giftList->where("gifts.category_id",$category_id);
        }
        if(array_key_exists("sort_by",$serachData)){
			$sort = $serachData['sort_by'];
			if($sort == A_TO_Z){  
				$giftList->orderBy('gifts.name', 'asc');
			}else if($sort == Z_TO_A){
				$giftList->orderBy('gifts.name', 'desc');
			}
        }
        $giftDetail= $giftList->get();
        return $giftDetail;
    }


    public function getGiftCategory($serachData,$limit){
        $giftCategoryList = Category::where(['type'=>2,'status'=>1]);
        if(array_key_exists("search_text",$serachData)){
			$search = $serachData['search_text'];
			$giftCategoryList->Where("categories.name",'like','%'.$search.'%');
        }
        if($limit != 'All'){
            $giftCategoryList->limit($limit);
        }
        $giftDetail= $giftCategoryList->get();
        return $giftDetail;
    }
    public function getCelebrity($serachData,$limit){
        
        $usersList = User::select('users.*','celebrity_categories.name as genres_name')->leftjoin('celebrity_categories','celebrity_categories.id','=','users.genres')->where(['users.type'=>3,'users.status'=>1]);
        
        if(array_key_exists("search_text",$serachData)){
			$search = $serachData['search_text'];
			$usersList->Where("users.name",'like','%'.$search.'%');
        }
        if($limit != 'All'){
            $usersList->limit($limit);
        }
        if(array_key_exists("sort_by",$serachData)){
			$sort = $serachData['sort_by'];
			if($sort == A_TO_Z){  
				$usersList->orderBy('users.name', 'asc');
			}else if($sort == Z_TO_A){
				$usersList->orderBy('users.name', 'desc');
			}
        }
        $usersDetail = $usersList->get();

        return $usersDetail;
    }

    public function getRestro($serachData, $limit, $is_featured, $is_pagination = 0) {
        // $subQuery = "(select ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$this->radius." order by `distance` asc) t";
        $getProductRestroIds = '';

        if (array_key_exists("buy_one_get_one",$serachData)) {

            if (!empty($serachData['buy_one_get_one'])) {
                $getProductRestroIds = Products::where(['is_active'=>1, 'buy_one_get_one'=>1])->groupBy('restaurant_id')->pluck('restaurant_id')->toArray();
            }

        } else {
            $getProductRestroIds = Products::where('products.is_active', 1)->groupBy('restaurant_id')->pluck('restaurant_id')->toArray();
        }

        
        $restroList = Restaurant::select('restaurants.id','restaurants.main_category_id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.extra_kilopoints','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km','restaurants.cost_for_two_price','restaurants.video')->where(['restaurants.status'=>1])->whereIn('restaurants.id', $getProductRestroIds);

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $restroList->where('restaurants.main_category_id',$main_category_id);
        }

        if (array_key_exists("slug",$serachData)) {  
            $slug = $serachData['slug'];

            if ($slug == 'DineIn' || $slug == 'dine') {
                $restroIds = OrdersDetails::where(['order_type'=>'1'])->join('orders','orders.id','=','order_details.order_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();

            } else if ($slug == 'PickUp' || $slug == 'pickup') {
                $restroIds = OrdersDetails::where(['order_type'=>'2'])->join('orders','orders.id','=','order_details.order_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();
            }

            if ($slug == 'featured') {
                $restroList->where('is_featured',1);
            }

            if (isset($restroIds) && !empty($restroList)) {
                $restroList->whereIn('restaurants.id',$restroIds);
            }
        }
        
        if(array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $restroList->Where("restaurants.name",'like','%'.$search.'%');
        }

        if (array_key_exists("is_kilo_points_promotor",$serachData)) {

            if ($serachData['is_kilo_points_promotor'] == 'Yes') {
                $restroList->Where("restaurants.is_kilo_points_promotor", 1);

            } else if ($serachData['is_kilo_points_promotor'] == 'No') {
                $restroList->Where("restaurants.is_kilo_points_promotor", 0);
            }
        }

        if (array_key_exists("is_open",$serachData)) {

            if ($serachData['is_open'] == 'Yes') {
                $restroList->Where("restaurants.is_open", 1);

            } else if ($serachData['is_open'] == 'No') {
                $restroList->Where("restaurants.is_open", 0);
            }
        }

        if (array_key_exists("cost_price_min",$serachData) && array_key_exists("cost_price_max",$serachData)) {

            if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) { 
                $cost_price_min = $serachData['cost_price_min'];
                $cost_price_max = $serachData['cost_price_max'];
                $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
            }
        }

        if (array_key_exists("category_id",$serachData)) {
            $category_id = $serachData['category_id'];
            $restroList->join('products','products.restaurant_id','=','restaurants.id')->where('products.category_id',$category_id)->groupBy('products.restaurant_id');
        }

        if (array_key_exists("service_mode",$serachData)) {
            $service_mode = $serachData['service_mode'];
            $restroList->join('restaurant_modes','restaurant_modes.restaurant_id','=','restaurants.id')->where('restaurant_modes.mode_id',$service_mode)->groupBy('restaurant_modes.restaurant_id');
        }

        if ($is_featured == 1) {
            $restroList->where('is_featured',1);
        }

        if (array_key_exists("is_featured",$serachData)){
            $is_featured = $serachData['is_featured'];

            if ($is_featured == 'Yes') {
                $restroList->where('is_featured',1);
            }
        }
        //defalut set latest UP

        if (array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {  
                $restroList->orderBy('restaurants.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $restroList->orderBy('restaurants.name', 'desc');

            } else if ($sort == 'KPs') {
                $restroList->orderBy('restaurants.extra_kilopoints', 'desc');

            } else if ($sort == 'distance') {
                $restroList->orderBy('distance', 'asc');

            } else if ($sort == 'rating') {
                $restroList->orderBy('avg_rating', 'desc');

            } else {
                // $restroList->orderBy('restaurants.id', 'desc');
                $restroList->orderBy('restaurants.is_featured', 'desc');
            }
        }
        $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

        if ($limit != 'All') {

            if ($is_pagination == 1) {

                /*if (array_key_exists("sort_by",$serachData)){
                    $sort = $serachData['sort_by'];

                    if ($sort == 'distance') {
                        $restroList->orderBy('distance', 'asc');
                    } 
                }*/
                $restroDetail = $restroList->paginate($limit);

            } else {
                $restroList->limit($limit);
                $restroDetail = $restroList->get();
            }

        } else {
            $restroDetail = $restroList->get();
        }

        return $restroDetail;
    }

    public function getTopGiftingRestro($serachData, $limit, $is_featured = 0, $is_pagination = 0) {
       /* $subQuery = "(select ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";*/
        
        // $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km','restaurants.cost_for_two_price')->where(['restaurants.status'=>1, 'restaurants.is_kilo_points_promotor'=>1]);
        $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km','restaurants.cost_for_two_price')->where(['restaurants.status'=>1]);
        
        if(array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $restroList->Where("restaurants.name",'like','%'.$search.'%');
        }

        if (array_key_exists("is_kilo_points_promotor",$serachData)) {

            if ($serachData['is_kilo_points_promotor'] == 'Yes') {
                $restroList->Where("restaurants.is_kilo_points_promotor", 1);

            } else if ($serachData['is_kilo_points_promotor'] == 'No') {
                $restroList->Where("restaurants.is_kilo_points_promotor", 0);
            }
        }

        if (array_key_exists("is_open",$serachData)) {

            if ($serachData['is_open'] == 'Yes') {
                $restroList->Where("restaurants.is_open", 1);

            } else if ($serachData['is_open'] == 'No') {
                $restroList->Where("restaurants.is_open", 0);
            }
        }

        if (array_key_exists("cost_price_min",$serachData) && array_key_exists("cost_price_max",$serachData)) {

            if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) { 
                $cost_price_min = $serachData['cost_price_min'];
                $cost_price_max = $serachData['cost_price_max'];
                $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
            }
        }

        if (array_key_exists("category_id",$serachData)){
            $category_id = $serachData['category_id'];
        }

        if ($is_featured == 1) {
            $restroList->where('is_featured',1);
        }

        if (array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {  
                $restroList->orderBy('restaurants.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $restroList->orderBy('restaurants.name', 'desc');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

                if (array_key_exists("sort_by",$serachData)){
                    $sort = $serachData['sort_by'];

                    if ($sort == 'distance') {
                        $restroList->orderBy('distance', 'asc');
                    } 
                }
                $restroDetail = $restroList->paginate($limit);

            } else {
                $restroList->limit($limit);
                $restroDetail = $restroList->get();
            }

        } else {
            $restroDetail = $restroList->get();
        }

        return $restroDetail;
    }

    public function getBrandRestro($serachData, $limit, $is_pagination = 0) {
       /* $subQuery = "(select ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";*/

        $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km')->join('products','products.restaurant_id','=','restaurants.id')->where(['restaurants.status'=>1, 'restaurants.brand_id'=>$serachData['brand_id']])->groupBy('products.restaurant_id');
        // $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km')->where(['restaurants.status'=>1, 'restaurants.brand_id'=>$serachData['brand_id']]);
        
        if (array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $restroList->Where("restaurants.name",'like','%'.$search.'%');
        }

        if (array_key_exists("sort_by",$serachData)) {
            $sort = $serachData['sort_by'];

            if ($sort == A_TO_Z) {  
                $restroList->orderBy('restaurants.name', 'asc');

            } else if($sort == Z_TO_A) {
                $restroList->orderBy('restaurants.name', 'desc');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
                $restroDetail = $restroList->paginate($limit);

            } else {
                $restroList->limit($limit);
                $restroDetail = $restroList->get();
            }

        } else {
            $restroDetail = $restroList->get();
        }

        return $restroDetail;
    }

    public function getDishsList($serachData,$limit, $radius = 10, $is_pagination = 0){
        $tag_ids = '';

        if (array_key_exists("tag_ids",$serachData)) {

            if (!empty($serachData['tag_ids'])) {
                $id_array = explode(",", $serachData['tag_ids']);

                $productTagData = ProductTags::whereIn('id', $id_array)->groupBy('product_id')->pluck('tag')->toArray();
                $tag_ids = ProductTags::whereIn('tag', $productTagData)->groupBy('product_id')->pluck('product_id')->toArray();
            }
        }

        if ($tag_ids) {
            $productsList = Products::select('products.id','products.name','products.long_description','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.shop_type','products.delivery_time','products.delivery_hours','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id','restaurants.latitude','restaurants.longitude')->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->priceFilter($serachData)->KPFilter($serachData)->whereIn('products.id', $tag_ids)->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');

        } else {
            $productsList = Products::select('products.id','products.name','products.long_description','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.shop_type','products.delivery_time','products.delivery_hours','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id','restaurants.latitude','restaurants.longitude')->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->priceFilter($serachData)->KPFilter($serachData)->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
        }

       
        if (array_key_exists("latitude",$serachData)) { 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }  

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $productsList->where('restaurants.main_category_id',$main_category_id);
        }

        if (array_key_exists("brand_id",$serachData)) {
            $brand_id = $serachData['brand_id'];
            $productsList->where('restaurants.brand_id',$brand_id);
        }

        if(array_key_exists("search_text",$serachData)){    

            if (!empty($serachData['search_text'])) {
                $search = $serachData['search_text']; 
                $productsList->Where("products.name",'like','%'.$search.'%');
            }
        }

        if(array_key_exists("restaurant_id",$serachData)){

            if (!empty($serachData['restaurant_id'])) {
                $restaurant_id = $serachData['restaurant_id']; 
                $productsList->Where("products.restaurant_id", $restaurant_id);
            }
        }

        if(array_key_exists("buy_one_get_one",$serachData)){

            if (!empty($serachData['buy_one_get_one'])) {
                $buy_one_get_one = $serachData['buy_one_get_one']; 
                $productsList->Where("products.buy_one_get_one", $buy_one_get_one);
            }
        }

        if(array_key_exists("category_id",$serachData)){

            if (!empty($serachData['category_id'])) {
                $category_id = $serachData['category_id']; 
                $productsList->Where("products.category_id", $category_id);
            }
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if(array_key_exists("restaurant_id",$serachData)){    
            $restaurant_id = $serachData['restaurant_id']; 
            $productsList->Where("products.restaurant_id", $restaurant_id);
        }

        if(array_key_exists("dish_type",$serachData)){

            if (!empty($serachData['dish_type'])) {
                $dish_type = $serachData['dish_type']; 

                if ($dish_type != 'Both') {
                    $productsList->Where("products.products_type", $dish_type);
                }
            }
        }

        /*if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) { 
            $min_price = $serachData['min_price'];
            $max_price = $serachData['max_price'];
            $productsList->whereBetween('products.price', [$min_price, $max_price]);
        }*/

        /*if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
        }*/

        if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) { 
            $min_discount = $serachData['min_discount'];
            $max_discount = $serachData['max_discount'];
            $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
        }

        if (isset($serachData['price']) && !empty($serachData['price'])) { 
            
            if ($serachData['price'] == 'LTH') {
                $productsList->orderBy('products.price', 'asc');
            }

            if ($serachData['price'] == 'HTL') {
                $productsList->orderBy('products.price', 'desc');
            }
        }

        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];
            if ($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');

            } else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');

            } else if($sort == 'Newest'){
                $productsList->orderBy('products.id', 'desc');
            }
        }
        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }
        return $productsDetail;
    }

    public function getFavDishsList($serachData,$limit, $radius = 10, $is_pagination = 0, $favIds=null){
        $productsList = Products::select('products.id','products.name','products.long_description','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.points','products.restaurant_id','products.buy_one_get_one','categories_lang.name as category')->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where('categories_lang.lang', App::getLocale())->whereIn('products.id',$favIds)->where('products.is_active',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');

        if (array_key_exists("main_category_id",$serachData)) {
                $productsList = $productsList->where(['restaurants.main_category_id' => $serachData['main_category_id']]);
            }
       
        if (array_key_exists("latitude",$serachData)){ 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }  
        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];
            if($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');
            }else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');
            }
        }
        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }
        return $productsDetail;
    }

    public function getDish($serachData,$limit, $radius = 10, $is_pagination = 0) {
        $productsList = Products::select('products.id','products.name','products.long_description','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id',DB::raw('(IFNULL(products.points,0) + IFNULL(products.extra_kilopoints,0)) as totalCalKP'))->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
        // $productsList = Products::with('Restaurant')->where('products.is_active',1);
       
        if (array_key_exists("slug",$serachData)){    
            $slug = $serachData['slug'];

            if ($slug == 'top-pickup-dishes') {
                $pickupProductsIds = OrdersDetails::where(['order_type'=>'2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders','orders.id','=','order_details.order_id')->join('restaurants','restaurants.id','=','orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
                $productsList->whereIn('products.id',$pickupProductsIds);
            }
        }

        if (array_key_exists("latitude",$serachData)){ 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }  

        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if (array_key_exists("brand_id",$serachData)) {
            $brand_id = $serachData['brand_id'];
            $productsList->where('restaurants.brand_id',$brand_id);
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
			$productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $productsList->where('restaurants.main_category_id',$main_category_id);
        }

        if(array_key_exists("restaurant_id",$serachData)){    
            $restaurant_id = $serachData['restaurant_id']; 
            $productsList->Where("products.restaurant_id", $restaurant_id);
        }

        if(array_key_exists("dish_type",$serachData)){

            if (!empty($serachData['dish_type'])) {
                $dish_type = $serachData['dish_type'];

                if ($dish_type != 'Both') {
                    $productsList->Where("products.products_type", $dish_type);
                }
            }
        }

        if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) { 
            $min_price = $serachData['min_price'];
            $max_price = $serachData['max_price'];
            $productsList->whereBetween('products.price', [$min_price, $max_price]);
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
        }

        if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) { 
            $min_discount = $serachData['min_discount'];
            $max_discount = $serachData['max_discount'];
            $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
        }

        if (isset($serachData['price']) && !empty($serachData['price'])) { 
            
            if ($serachData['price'] == 'LTH') {
                $productsList->orderBy('products.price', 'asc');
            }

            if ($serachData['price'] == 'HTL') {
                $productsList->orderBy('products.price', 'desc');
            }
        }

        if(array_key_exists("celebrity_id",$serachData)){    
            $celebrity_id = $serachData['celebrity_id']; 
			$productsList->Where("products.celebrity_id", $celebrity_id);
        }

        if (array_key_exists("slug",$serachData)){    
            $slug = $serachData['slug'];

            if ($slug == 'top_gift') {
                $productsList->orderBy('totalCalKP', 'desc');
            }
        }

        if(array_key_exists("sort_by",$serachData)){
			$sort = $serachData['sort_by'];
			if($sort == A_TO_Z){  
				$productsList->orderBy('products.name', 'asc');

			} else if($sort == Z_TO_A){
				$productsList->orderBy('products.name', 'desc');

			} else if($sort == 'top_gift'){
                $productsList->orderBy('totalCalKP', 'desc');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }
        /*if($limit != 'All'){
            $productsList->limit($limit);
        }*/
        // $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getHotDish($serachData,$limit, $radius = 10, $is_pagination = 0) {
        $productsList = Products::select('products.id','products.name','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id')->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
        // $productsList = Products::with('Restaurant')->where('products.is_active',1);
       
        if (array_key_exists("slug",$serachData)){    
            $slug = $serachData['slug'];

            if ($slug == 'top-pickup-dishes') {
                $pickupProductsIds = OrdersDetails::where(['order_type'=>'2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders','orders.id','=','order_details.order_id')->join('restaurants','restaurants.id','=','orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
                $productsList->whereIn('products.id',$pickupProductsIds);
            }
        }

        /*if (array_key_exists("latitude",$serachData)){ 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }*/  

        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if (array_key_exists("brand_id",$serachData)) {
            $brand_id = $serachData['brand_id'];
            $productsList->where('restaurants.brand_id',$brand_id);
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $productsList->where('restaurants.main_category_id',$main_category_id);
        }

        if(array_key_exists("restaurant_id",$serachData)){    
            $restaurant_id = $serachData['restaurant_id']; 
            $productsList->Where("products.restaurant_id", $restaurant_id);
        }

        if(array_key_exists("dish_type",$serachData)){

            if (!empty($serachData['dish_type'])) {
                $dish_type = $serachData['dish_type']; 
                $productsList->Where("products.products_type", $dish_type);
            }
        }

        if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) { 
            $min_price = $serachData['min_price'];
            $max_price = $serachData['max_price'];
            $productsList->whereBetween('products.price', [$min_price, $max_price]);
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
        }

        if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) { 
            $min_discount = $serachData['min_discount'];
            $max_discount = $serachData['max_discount'];
            $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
        }

        if (isset($serachData['price']) && !empty($serachData['price'])) { 
            
            if ($serachData['price'] == 'LTH') {
                $productsList->orderBy('products.price', 'asc');
            }

            if ($serachData['price'] == 'HTL') {
                $productsList->orderBy('products.price', 'desc');
            }
        }

        if(array_key_exists("celebrity_id",$serachData)){    
            $celebrity_id = $serachData['celebrity_id']; 
            $productsList->Where("products.celebrity_id", $celebrity_id);
        }
        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];
            if($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');
            }else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');
            }
        }

        if (isset($serachData['main_category_id']) && !empty($serachData['main_category_id'])) {

            if ($serachData['main_category_id'] != 5 && $serachData['main_category_id'] != 6) {
                $productsDetail = $productsList->groupBy('products.restaurant_id');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }
        /*if($limit != 'All'){
            $productsList->limit($limit);
        }*/
        // $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getTopPickupsDish($serachData, $limit, $is_pagination = 0){
        $pickupProductsIds = OrdersDetails::where(['order_type'=>'2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders','orders.id','=','order_details.order_id')->join('restaurants','restaurants.id','=','orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
        // $productsList = Products::with('Restaurant')->where('products.is_active',1)->whereIn('products.id',$pickupProductsIds);

        $productsList = Products::select('products.id','products.name','products.long_description','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.shop_type','products.delivery_time','products.delivery_hours','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id','restaurants.latitude','restaurants.longitude')->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number')->whereIn('products.id',$pickupProductsIds);

        if (array_key_exists("latitude",$serachData)) { 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }
       
        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if (array_key_exists("brand_id",$serachData)) {
            $brand_id = $serachData['brand_id'];
            $productsList->where('restaurants.brand_id',$brand_id);
        }

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $productsList->where('restaurants.main_category_id',$main_category_id);
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if(array_key_exists("restaurant_id",$serachData)){    
            $restaurant_id = $serachData['restaurant_id']; 
            $productsList->Where("products.restaurant_id", $restaurant_id);
        }

        if(array_key_exists("dish_type",$serachData)){

            if (!empty($serachData['dish_type'])) {
                $dish_type = $serachData['dish_type']; 
                $productsList->Where("products.products_type", $dish_type);
            }
        }

        if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) { 
            $min_price = $serachData['min_price'];
            $max_price = $serachData['max_price'];
            $productsList->whereBetween('products.price', [$min_price, $max_price]);
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
        }

        if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) { 
            $min_discount = $serachData['min_discount'];
            $max_discount = $serachData['max_discount'];
            $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
        }

        if (isset($serachData['price']) && !empty($serachData['price'])) { 
            
            if ($serachData['price'] == 'LTH') {
                $productsList->orderBy('products.price', 'asc');
            }

            if ($serachData['price'] == 'HTL') {
                $productsList->orderBy('products.price', 'desc');
            }
        }

        if(array_key_exists("celebrity_id",$serachData)){    
            $celebrity_id = $serachData['celebrity_id']; 
            $productsList->Where("products.celebrity_id", $celebrity_id);
        }
        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];
            if($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');
            }else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }

        /*if($limit != 'All'){
            $productsList->limit($limit);
        }*/
        // $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getTopDineInDish($serachData, $limit){
        $dineInProductsIds = OrdersDetails::where(['order_type'=>'1'])->join('orders','orders.id','=','order_details.order_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
        $productsList = Products::with('Restaurant')->where('products.is_active',1)->whereIn('products.id',$dineInProductsIds);
       
        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if(array_key_exists("celebrity_id",$serachData)){    
            $celebrity_id = $serachData['celebrity_id']; 
            $productsList->Where("products.celebrity_id", $celebrity_id);
        }
        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];
            if($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');
            }else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');
            }
        }
        if($limit != 'All'){
            $productsList->limit($limit);
        }
        $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getTopDishs($serachData, $limit, $is_pagination = 0){
        $dineInProductsIds = OrdersDetails::where('order_status', '!=', 'Cancel')->join('orders','orders.id','=','order_details.order_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
        // $productsList = Products::with('Restaurant')->where('products.is_active',1)->whereIn('products.id',$dineInProductsIds);

        $productsList = Products::select('products.id','products.name','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.shop_type','products.delivery_time','products.delivery_hours','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id','restaurants.latitude','restaurants.longitude')->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number')->whereIn('products.id',$dineInProductsIds);

        if (array_key_exists("latitude",$serachData)) { 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }
       
        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $productsList->where('restaurants.main_category_id',$main_category_id);
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if(array_key_exists("celebrity_id",$serachData)){    
            $celebrity_id = $serachData['celebrity_id']; 
            $productsList->Where("products.celebrity_id", $celebrity_id);
        }
        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];
            if($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');
            }else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }
        /*if($limit != 'All'){
            $productsList->limit($limit);
        }*/
        // $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getTopGiftingItems($serachData, $limit, $is_pagination = 0){
        $productsList = Products::select('products.id','products.name','products.long_description','products.category_id','products.main_image','products.products_type','products.price','products.video','products.out_of_stock','products.serve','products.points','products.extra_kilopoints','products.restaurant_id','products.product_for','products.buy_one_get_one','categories_lang.name as category','restaurants.main_category_id','restaurants.brand_id',DB::raw('(IFNULL(products.points,0) + IFNULL(products.extra_kilopoints,0)) as totalCalKP'))->join('restaurants','restaurants.id','=','products.restaurant_id')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where('categories_lang.lang', App::getLocale())->where('products.is_active',1)->where('restaurants.status',1)->with('Restaurant:id,name,file_path,logo,tag_line,address,latitude,longitude,is_kilo_points_promotor,country_code,phone_number');
        // $productsList = Products::with('Restaurant')->where('products.is_active',1);
       
        if (array_key_exists("slug",$serachData)){    
            $slug = $serachData['slug'];

            if ($slug == 'top-pickup-dishes') {
                $pickupProductsIds = OrdersDetails::where(['order_type'=>'2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders','orders.id','=','order_details.order_id')->join('restaurants','restaurants.id','=','orders.restaurant_id')->groupBy('order_details.product_id')->pluck('product_id')->toArray();
                $productsList->whereIn('products.id',$pickupProductsIds);
            }
        }

        if (array_key_exists("latitude",$serachData)){ 
            $productsList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");
        }  

        if(array_key_exists("search_text",$serachData)){    
            $search = $serachData['search_text']; 
            $productsList->Where("products.name",'like','%'.$search.'%');
        }

        if (array_key_exists("brand_id",$serachData)) {
            $brand_id = $serachData['brand_id'];
            $productsList->where('restaurants.brand_id',$brand_id);
        }

        if(array_key_exists("category_id",$serachData)){    
            $category_id = $serachData['category_id']; 
            $productsList->Where("products.category_id", $category_id);
        }

        if(array_key_exists("sub_category_id",$serachData)){

            if (!empty($serachData['sub_category_id'])) {
                $sub_category_id = $serachData['sub_category_id']; 
                $productsList->whereIn("products.sub_category_id", $sub_category_id);
            }
        }

        if (array_key_exists("main_category_id",$serachData)) {
            $main_category_id = $serachData['main_category_id'];
            $productsList->where('restaurants.main_category_id',$main_category_id);
        }

        if(array_key_exists("restaurant_id",$serachData)){    
            $restaurant_id = $serachData['restaurant_id']; 
            $productsList->Where("products.restaurant_id", $restaurant_id);
        }

        if(array_key_exists("dish_type",$serachData)){

            if (!empty($serachData['dish_type'])) {
                $dish_type = $serachData['dish_type']; 
                $productsList->Where("products.products_type", $dish_type);
            }
        }

        if (!empty($serachData['min_price']) && !empty($serachData['max_price'])) { 
            $min_price = $serachData['min_price'];
            $max_price = $serachData['max_price'];
            $productsList->whereBetween('products.price', [$min_price, $max_price]);
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $productsList->whereBetween('products.points', [$min_kilo, $max_kilo]);
        }

        if (!empty($serachData['min_discount']) && !empty($serachData['max_discount'])) { 
            $min_discount = $serachData['min_discount'];
            $max_discount = $serachData['max_discount'];
            $productsList->whereBetween('products.discount_price', [$min_discount, $max_discount]);
        }

        if (isset($serachData['price']) && !empty($serachData['price'])) { 
            
            if ($serachData['price'] == 'LTH') {
                $productsList->orderBy('products.price', 'asc');
            }

            if ($serachData['price'] == 'HTL') {
                $productsList->orderBy('products.price', 'desc');
            }
        }

        /*if(array_key_exists("celebrity_id",$serachData)){    
            $celebrity_id = $serachData['celebrity_id']; 
            $productsList->Where("products.celebrity_id", $celebrity_id);
        }*/

        if(array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];

            if($sort == A_TO_Z){  
                $productsList->orderBy('products.name', 'asc');
            }else if($sort == Z_TO_A){
                $productsList->orderBy('products.name', 'desc');
            }
        } else {
            $productsList->orderBy('totalCalKP', 'desc');
        }

        // $productsList->orderBy('products.points', 'desc');

        if (isset($serachData['main_category_id']) && !empty($serachData['main_category_id'])) {

            if ($serachData['main_category_id'] != 5 && $serachData['main_category_id'] != 6) {
                $productsDetail = $productsList->groupBy('products.restaurant_id');
            }
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $productsDetail = $productsList->paginate($limit);

            } else {
                $productsList->limit($limit);
                $productsDetail = $productsList->get();
            }

        }  else {
            $productsDetail = $productsList->get();
        }
        /*if($limit != 'All'){
            $productsList->limit($limit);
        }*/
        // $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getTopRestrosBySlug($serachData, $limit, $slug, $is_pagination = 0) {

        if ($slug == 'DineIn') {
            $restroIds = OrdersDetails::where(['order_type'=>'1', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders','orders.id','=','order_details.order_id')->join('restaurants','restaurants.id','=','orders.restaurant_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();

        } else if ($slug == 'PickUp') {
            $restroIds = OrdersDetails::where(['order_type'=>'2', 'restaurants.main_category_id' => $serachData['main_category_id']])->join('orders','orders.id','=','order_details.order_id')->join('restaurants','restaurants.id','=','orders.restaurant_id')->groupBy('orders.restaurant_id')->pluck('restaurant_id')->toArray();

        }

        $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.extra_kilopoints','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km','restaurants.cost_for_two_price')->where(['restaurants.status'=>1])->whereIn('restaurants.id',$restroIds);

        if (array_key_exists("search_text",$serachData)) {
            $search = $serachData['search_text'];
            $restroList->Where("restaurants.name",'like','%'.$search.'%');
        }

        if (array_key_exists("is_kilo_points_promotor",$serachData)) {

            if ($serachData['is_kilo_points_promotor'] == 'Yes') {
                $restroList->Where("restaurants.is_kilo_points_promotor", 1);

            } else if ($serachData['is_kilo_points_promotor'] == 'No') {
                $restroList->Where("restaurants.is_kilo_points_promotor", 0);
            }
        }

        if (array_key_exists("is_open",$serachData)) {

            if ($serachData['is_open'] == 'Yes') {
                $restroList->Where("restaurants.is_open", 1);

            } else if ($serachData['is_open'] == 'No') {
                $restroList->Where("restaurants.is_open", 0);
            }
        }

        if (array_key_exists("cost_price_min",$serachData) && array_key_exists("cost_price_max",$serachData)) {

            if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) { 
                $cost_price_min = $serachData['cost_price_min'];
                $cost_price_max = $serachData['cost_price_max'];
                $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
            }
        }

        if (array_key_exists("category_id",$serachData)) {
            $category_id = $serachData['category_id'];
            $restroList->join('products','products.restaurant_id','=','restaurants.id')->where('products.category_id',$category_id)->groupBy('products.restaurant_id');
        }

        if (array_key_exists("service_mode",$serachData)) {
            $service_mode = $serachData['service_mode'];
            $restroList->join('restaurant_modes','restaurant_modes.restaurant_id','=','restaurants.id')->where('restaurant_modes.mode_id',$service_mode)->groupBy('restaurant_modes.restaurant_id');
        }

        if (array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {  
                $restroList->orderBy('restaurants.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $restroList->orderBy('restaurants.name', 'desc');

            } else if ($sort == 'KPs') {
                $restroList->orderBy('restaurants.extra_kilopoints', 'desc');
            }
        }
        $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

        if ($limit != 'All') {

            if ($is_pagination == 1) {

                if (array_key_exists("sort_by",$serachData)){
                    $sort = $serachData['sort_by'];

                    if ($sort == 'distance') {
                        $restroList->orderBy('distance', 'asc');
                    } 
                }
                $restroDetail = $restroList->paginate($limit);

            } else {
                $restroList->limit($limit);
                $restroDetail = $restroList->get();
            }

        } else {
            $restroDetail = $restroList->get();
        }

        return $restroDetail;
    }

    public function getBOGORestros($serachData, $limit, $is_pagination = 0) {
        $getAllBoGoRestroIds = Products::where(['is_active'=>1, 'buy_one_get_one'=>1])->groupBy('restaurant_id')->pluck('restaurant_id')->toArray();

        $restroList = Restaurant::select('restaurants.id','restaurants.name','restaurants.brand_id','restaurants.file_path','restaurants.logo','restaurants.tag_line','restaurants.address','restaurants.latitude','restaurants.longitude','restaurants.is_open','restaurants.area_name','restaurants.dine_in_code','restaurants.is_kilo_points_promotor','restaurants.extra_kilopoints','restaurants.is_featured','restaurants.country_code','restaurants.phone_number','restaurants.landline','restaurants.email','restaurants.min_order_amount','restaurants.prepration_time','restaurants.delivery_time','restaurants.cancelation_charges','restaurants.free_delivery_min_amount','restaurants.delivery_charges_per_km','restaurants.cost_for_two_price')->where(['restaurants.status'=>1])->whereIn('restaurants.id',$getAllBoGoRestroIds);

        if (array_key_exists("search_text",$serachData)) {
            $search = $serachData['search_text'];
            $restroList->Where("restaurants.name",'like','%'.$search.'%');
        }

        if (array_key_exists("cost_price_min",$serachData) && array_key_exists("cost_price_max",$serachData)) {

            if (!empty($serachData['cost_price_min']) && !empty($serachData['cost_price_max'])) { 
                $cost_price_min = $serachData['cost_price_min'];
                $cost_price_max = $serachData['cost_price_max'];
                $restroList->whereBetween('restaurants.cost_for_two_price', [$cost_price_min, $cost_price_max]);
            }
        }

        if (array_key_exists("category_id",$serachData)) {
            $category_id = $serachData['category_id'];
            $restroList->join('products','products.restaurant_id','=','restaurants.id')->where('products.category_id',$category_id)->groupBy('products.restaurant_id');
        }

        if (array_key_exists("service_mode",$serachData)) {
            $service_mode = $serachData['service_mode'];
            $restroList->join('restaurant_modes','restaurant_modes.restaurant_id','=','restaurants.id')->where('restaurant_modes.mode_id',$service_mode)->groupBy('restaurant_modes.restaurant_id');
        }

        if (array_key_exists("sort_by",$serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {  
                $restroList->orderBy('restaurants.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $restroList->orderBy('restaurants.name', 'desc');

            } else if ($sort == 'KPs') {
                $restroList->orderBy('restaurants.extra_kilopoints', 'desc');
            }
        }
        $restroList->selectRaw("( 6371 * acos( cos( radians(" . $serachData['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $serachData['longitude'] . ") ) +  sin( radians(" . $serachData['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance");

        if ($limit != 'All') {

            if ($is_pagination == 1) {

                if (array_key_exists("sort_by",$serachData)){
                    $sort = $serachData['sort_by'];

                    if ($sort == 'distance') {
                        $restroList->orderBy('distance', 'asc');
                    } 
                }
                $restroDetail = $restroList->paginate($limit);

            } else {
                $restroList->limit($limit);
                $restroDetail = $restroList->get();
            }

        } else {
            $restroDetail = $restroList->get();
        }

        return $restroDetail;
    }

    public function getDishLatAndLong($serachData, $limit, $radius, $is_pagination = 0){
        $subQuery = "(select id, name, address, latitude, longitude ,
        ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")
        ) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `restaurants` WHERE status = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";

        $productsList =  Products::select('products.*','t.distance')->with('User')->with('Cart')
            ->join('product_assign_to_chef','product_assign_to_chef.product_id','=','products.id')
            ->join('users','users.id' ,'=', 'products.celebrity_id')
            ->join(DB::raw($subQuery),'t.id','=','product_assign_to_chef.chef_id')
            ->groupBy('products.id')
            ->where('products.is_active',1);
            
        
            if(array_key_exists("search_text",$serachData)){    
                $search = $serachData['search_text']; 
                $productsList->Where("products.name",'like','%'.$search.'%');
            }
    
            if(array_key_exists("category_id",$serachData)){    
                $category_id = $serachData['category_id']; 
                $productsList->Where("products.category_id", $category_id);
            }

            if(array_key_exists("celebrity_id",$serachData)){    
                $celebrity_id = $serachData['celebrity_id']; 
                $productsList->Where("products.celebrity_id", $celebrity_id);
            }
            if($limit != 'All'){
                $productsList->limit($limit);
            }
            $productsDetail = $productsList->get();
        return $productsDetail;
    }

    public function getDishLatAndLongCategory($serachData,$limit,$radius){
        $subQuery = "(select id, name, address, latitude, longitude ,
        ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")
        ) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `users` WHERE type = 2 AND STATUS  = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";

        $productsList =  Products::select('products.*','t.distance')->with('User')
            ->join('product_assign_to_chef','product_assign_to_chef.product_id','=','products.id')
            ->join('users','users.id' ,'=', 'products.celebrity_id')
            ->join(DB::raw($subQuery),'t.id','=','product_assign_to_chef.chef_id')
            ->groupBy('products.id')
            ->where('products.is_active',1);

        
            if(array_key_exists("search_text",$serachData)){    
                $search = $serachData['search_text']; 
                $productsList->Where("products.name",'like','%'.$search.'%');
            }
    
            if(array_key_exists("category_id",$serachData)){    
                $category_id = $serachData['category_id']; 
                $productsList->Where("products.category_id", $category_id);
            }

            if(array_key_exists("celebrity_id",$serachData)){    
                $celebrity_id = $serachData['celebrity_id']; 
                $productsList->Where("products.celebrity_id", $celebrity_id);
            }
            if($limit != 'All'){
                $productsList->limit($limit);
            }
            $productsDetail = $productsList->paginate(1);
        return $productsDetail;
    }

    private function findNearestChaf($serachData,$limit, $radius)
    {

        $subQuery = "(select id, name, address, latitude, longitude ,
                    ( 6371 * acos( cos( radians(".$serachData['latitude'].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".$serachData['longitude'].")
                    ) + sin( radians(".$serachData['latitude'].") ) * sin( radians( latitude ) ) ) ) AS distance from `users` WHERE type = 2 AND STATUS  = 1 HAVING `distance` < ".$radius." order by `distance` asc) t";

        //        $test = "SELECT products.* from products 
        //        inner join product_assign_to_chef on (product_assign_to_chef.product_id = products.id) 
        //        inner join (select id, name, address, latitude, longitude ,
        //        ( 6371 * acos( cos( radians(26.922070) ) *
        //        cos( radians( latitude ) )
        //        * cos( radians( longitude ) - radians(75.778885)
        //        ) + sin( radians(26.922070) ) *
        //        sin( radians( latitude ) ) )
        //        ) AS distance from `users` WHERE type = 2 AND STATUS  = 1 HAVING `distance` < 10 order by `distance` asc
        // ) t ON (t.id =product_assign_to_chef.chef_id) 
        // group by products.id";

        $productsList =  Products::select('products.*','t.distance')->with('User')
            ->join('product_assign_to_chef','product_assign_to_chef.product_id','=','products.id')
            ->join('users','users.id' ,'=', 'products.celebrity_id')
            ->join(DB::raw($subQuery),'t.id','=','product_assign_to_chef.chef_id')
            ->groupBy('products.id')
            ->where('products.is_active',1);

        
            if(array_key_exists("search_text",$serachData)){    
                $search = $serachData['search_text']; 
                $productsList->Where("products.name",'like','%'.$search.'%');
            }
    
            if(array_key_exists("category_id",$serachData)){    
                $category_id = $serachData['category_id']; 
                $productsList->Where("products.category_id", $category_id);
            }
            if(array_key_exists("sort_by",$serachData)){
                $sort = $serachData['sort_by'];
                if($sort == A_TO_Z){  
                    $productsList->orderBy('products.name', 'asc');
                }else if($sort == Z_TO_A){
                    $productsList->orderBy('products.name', 'desc');
                }
            }
            if($limit != 'All'){
                $productsList->limit($limit);
            }
            $productsDetail = $productsList->get();

        // $restaurants = User::selectRaw("id, name, address, latitude, longitude ,
        //                 ( 6371 * acos( cos( radians(?) ) *
        //                 cos( radians( latitude ) )
        //                 * cos( radians( longitude ) - radians(?)
        //                 ) + sin( radians(?) ) *
        //                 sin( radians( latitude ) ) )
        //                 ) AS distance", [$latitude, $longitude, $latitude])
        //     ->where('status', '=', 1)
        //     ->having("distance", "<", $radius)
        //     ->orderBy("distance",'asc')
        //     ->limit(20)
        //     ->get();

        // echo '<pre>';
        // print_r(DB::getQueryLog());
        // die;
        return $productsList;
    }
    public function getDishDetail($queryString){
        $productsList = Products::select('products.*', 'restaurants.main_category_id')->with('ProductImages')->join('restaurants','restaurants.id','=','products.restaurant_id')->where('products.is_active',1)->where("products.id",$queryString['product_id']);
       
        /*if(array_key_exists("product_id",$queryString)){    
            $product_id = $queryString['product_id']; 
			$productsList->Where("products.id",$product_id);
        }*/

        $productsDetail = $productsList->first();
        return $productsDetail;
    }

    public function favoriteDish(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'dish_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $data = Favorite::where(['type_id'=>$inputData['dish_id'], 'user_id'=> $userId, 'type'=>'Dish'])->first();

            if (!isset($data)) {
                $data = new Favorite;
                $data->type_id = $inputData['dish_id'];
                $data->user_id = $userId;
                $data->type = 'Dish';
                // $data->save();

                if ($data->save()) {
                    $response['status'] = 1;
                    $response['message'] = 'Dish favorited successfully';

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                }

            } else {
                Favorite::where(['type_id'=>$inputData['dish_id'], 'user_id'=> $userId, 'type'=>'Dish'])->delete();

                $response['status'] = 1;
                $response['message'] = 'Dish Unfavorited successfully';
            }

            return response()->json($response, 200);
        }
    }

    public function favoriteListing(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        $limit =  20;
        $radius = 10;
        $is_pagination = 1;
        $favIds = Favorite::where(['user_id'=> $userId, 'type'=>'Dish'])->pluck('type_id')->toArray();
        $getDish = $this->getFavDishsList($inputData, $limit, $radius, $is_pagination, $favIds);

        if (count($getDish)) {

            foreach ($getDish as $key => $value) {
                 /*$toppings = Topping::where(['dish_id'=>$value->id, 'status'=>1])->count();

                    if ($toppings) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }*/

                    $value->qty = (int)Cart::where(['user_id'=>$userId, 'product_id'=>$value->id])->sum('qty');

                    $mandatory_price = Topping::where(['dish_id'=>$value->id,'is_mandatory'=>1, 'status'=>1])->sum('price');

                    $value->price = $mandatory_price + $value->price;

                    $attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$value->id,'topping_choose'=>0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
                            $query->where('dish_toppings.dish_id', $value->id);
                        }))->get();

                    $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$value->id,'topping_choose'=>1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($value) {
                            $query->where('dish_toppings.dish_id', $value->id);
                        }))->get();

                    $product_attributes = ProductAttributeValues::select('product_attribute_values.id','product_attribute_values.attributes_lang_id','attributes_lang.name as attribute_name','attributes_lang.topping_choose')->where(['product_id' => $value->id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attributes_lang', 'attributes_lang.id', '=', 'product_attribute_values.attributes_lang_id')->groupBy('product_attribute_values.attributes_lang_id')->get();

                    if ($product_attributes) {

                        if (count($product_attributes) > 0) {
                            $value->is_topping = 1;
                        } else {
                            $value->is_topping = 0;
                        }

                        foreach ($product_attributes as $k => $v) {
                            $v->attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id')->where(['product_id' => $value->id, 'product_attribute_values.attributes_lang_id' => $v->attributes_lang_id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->get();
                        }

                    } else {
                        $value->is_topping = 0;
                    }

                    $value->product_attributes = $product_attributes;
                    $value->attributes = $attributes;
                    $value->add_on = $add_on;
                $value->avg_rating = number_format(0, 1);
                $value->distance = number_format($value->distance, 2).' KM';
            }
            $response['status'] = 1;
            $response['data'] = $getDish;

        } else {
            $response['status'] = 0;
            $response['message'] = 'No data found.';
        }
        return response()->json($response, 200);
    }

    public function getDishToppings(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'dish_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$inputData['dish_id'],'topping_choose'=>0])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
                    $query->where('dish_toppings.dish_id', $inputData['dish_id'])->where('dish_toppings.status', 1);
                }))->get();

            $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','toppings_category.topping_choose','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$inputData['dish_id'],'topping_choose'=>1])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
                    $query->where('dish_toppings.dish_id', $inputData['dish_id'])->where('dish_toppings.status', 1);
                }))->get();

            /*$attributes = ToppingCategory::select('toppings_category.id','toppings_category.name','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Change-Org-Price'])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
                    $query->where('dish_toppings.dish_id', $inputData['dish_id']);
                }))->get();

            $add_on = ToppingCategory::select('toppings_category.id','toppings_category.name','dish_toppings.dish_id')->join('dish_toppings','dish_toppings.topping_category_id','=','toppings_category.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Add-On'])->groupBy('toppings_category.id')->with(array('toppings' => function ($query) use ($inputData) {
                    $query->where('dish_toppings.dish_id', $inputData['dish_id']);
                }))->get();*/

           /* $attributes = Topping::select('dish_toppings_lang.name','dish_toppings.price','dish_toppings.price_reflect_on')->join('dish_toppings_lang','dish_toppings_lang.dish_topping_id','=','dish_toppings.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Change-Org-Price'])->where('dish_toppings_lang.lang', App::getLocale())->get();

            $add_on = Topping::select('dish_toppings_lang.name','dish_toppings.price','dish_toppings.price_reflect_on')->join('dish_toppings_lang','dish_toppings_lang.dish_topping_id','=','dish_toppings.id')->where(['dish_id'=>$inputData['dish_id'],'price_reflect_on'=>'Add-On'])->where('dish_toppings_lang.lang', App::getLocale())->get();*/

            if (!isset($attributes) && !isset($add_on)) {
                $response['status'] = 0;
                $response['message'] = 'No toppings found.';

            } else {
                $response['status'] = 1;
                $response['data']['attributes'] = $attributes;
                $response['data']['add_on'] = $add_on;
            }

            return response()->json($response, 200);
        }
    }

    public function applyDiscount(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'discount_code' => 'required',
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $data = Discount::where(['discount_code'=>$inputData['discount_code']])->first();

            if ($data) {
                //Remove Old apply coupon
                $cartParentDetail = CartParent::where(['id'=>$inputData['parent_cart_id'], 'user_id'=>$userId])->first();

                if ($cartParentDetail) {

                    if ($cartParentDetail->discount_code) {
                        /*$response['status'] = 0;
                        $response['message'] = 'Please remove already applied coupon.';*/
                        $updateCartParent = [
                            'discount_code' => null,
                            'discount_percent' => null,
                            'discount_amount' => null,
                            'amount' => $cartParentDetail->amount + $cartParentDetail->discount_amount,
                        ];
                        $cartParentDetail->update($updateCartParent);
                    }
                }

                $cartParentDetail = CartParent::where(['id'=>$inputData['parent_cart_id'], 'user_id'=>$userId])->first();

                if ($cartParentDetail) {

                    if ($cartParentDetail->discount_code) {
                        $response['status'] = 0;
                        $response['message'] = 'Please remove already applied coupon.';

                    } else {
                        $fail = false;
                        $countOrderedCoupon = Orders::where(['discount_code'=>$data['discount_code']])->count();

                        if ($data->no_of_use <= $countOrderedCoupon) {
                            $fail = true;
                            $response['status'] = 0;
                            $response['message'] = 'This discount coupon is no longer available.';
                        }

                        if ($data->category_type != 'Flat-Discount') {

                            $checkDiscountForRestro = DiscountCategories::where(['discount_id'=>$data->id, 'category_id'=>$cartParentDetail->restaurant_id])->first();

                            if (!$checkDiscountForRestro) {
                                $fail = true;
                                $response['status'] = 0;
                                $response['message'] = 'Not valid coupon code.';
                            }
                        }

                        if ($cartParentDetail->amount < $data->min_order_amount) {
                            $fail = true;
                            $response['status'] = 0;
                            $response['message'] = 'Minimum order amount must be greater then '.$data->min_order_amount;
                        }

                        if (!$fail) {
                            $discount_amount = (($cartParentDetail->amount*$data->percentage)/100);

                            if ($discount_amount > $data->max_discount_amount) {
                                $discount_amount = $data->max_discount_amount;
                            }

                            $updateCartParent = [
                                'discount_code' => $inputData['discount_code'],
                                'discount_type' => $data->discount_type,
                                'discount_percent' => $data->percentage,
                                'discount_amount' => $discount_amount,
                                'amount' => $cartParentDetail->amount - $discount_amount,
                            ];

                            if ($cartParentDetail->update($updateCartParent)) {
                                $response['status'] = 1;
                                $response['message'] = 'Coupon applied successfully.';

                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Error Occured.';
                            }
                        }
                    }

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid cart parent id.';
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid discount code.';
            }
            return response()->json($response, 200);
        }
    }

    public function checkTableAvailable(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'table_code' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $tableData = RestaurantTables::where(['table_code'=>$inputData['table_code']])->first();

            if ($tableData) {
                $checkTblAvl = Orders::where(['table_code'=>$inputData['table_code']])->where('order_status', '!=', 'Complete')->where('order_status', '!=', 'Cancel')->first();
                // dd($checkTblAvl);

                if (!$checkTblAvl) {
                    $response['status'] = 1;
                    $response['message'] = 'Table is available.';
                    $response['data'] = $tableData;

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'This table is already booked by someone else, Please choose another one.';
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid table code.';
            }
            return response()->json($response, 200);
        }
    }

    public function removeDiscount(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'discount_code' => 'required',
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $cartParentDetail = CartParent::where(['id'=>$inputData['parent_cart_id'], 'user_id'=>$userId])->first();

            if ($cartParentDetail) {
                $updateCartParent = [
                    'discount_code' => null,
                    'discount_percent' => null,
                    'discount_amount' => null,
                    'amount' => $cartParentDetail->amount + $cartParentDetail->discount_amount,
                ];

                if ($cartParentDetail->update($updateCartParent)) {
                    $response['status'] = 1;
                    $response['message'] = 'Coupon removed successfully.';

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid cart parent id.';
            }
            return response()->json($response, 200);
        }
    }

    public function notificationList(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $notificationData = Notification::select('notifications.*', 'orders.random_order_id')->leftJoin('orders','orders.id','=','notifications.order_id')->where(['notifications.user_id'=>$userId])->orderBy('notifications.id', 'desc')->get();

        if ($notificationData) {
            $response['status'] = 1;
            $response['data'] = $notificationData;

        } else {
            $response['status'] = 0;
            $response['message'] = 'No record found.';
        }
        return response()->json($response, 200);        
    }

    public function removeNotification(Request $request){
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
            $notificationData = Notification::where(['id'=>$inputData['notification_id'], 'user_id'=>$userId])->first();

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

    public function removeAllNotification(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;
        $notificationData = Notification::where(['user_id'=>$userId])->delete();
        $response['status'] = 1;
        $response['message'] = 'Notification removed successfully.';
        return response()->json($response, 200);
    }

    public function readAllNotification(Request $request){
        $inputData = $request->all();
        $userData = auth()->user();
        $userId =  $userData->id;

        $updateData = [
            'is_read'=>1,
        ];
        Notification::where(['user_id'=>$userId])->update($updateData);
        $response['status'] = 1;
        $response['message'] = 'Notification all read successfully.';
        return response()->json($response, 200);
    }

    public function readNotification(Request $request){
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
            $notificationData = Notification::where(['id'=>$inputData['notification_id'], 'user_id'=>$userId])->first();

            if ($notificationData) {
                $updateData = [
                    'is_read'=>1,
                ];
                $notificationData->update($updateData);
                $response['status'] = 1;
                $response['message'] = 'Notification removed successfully.';

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid notification id.';
            }
            return response()->json($response, 200);
        }
    }

    public function getCategoryRestroList(Request $request){
        $serachData = $request->all();
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
            'category_id'=>'required',
            // 'main_category_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $limit = 20;
            $is_featured = false;
            $is_pagination = 0;
            $min_price = Restaurant::select(\DB::raw("MIN(cost_for_two_price) AS min, MAX(cost_for_two_price) AS max"))->first();
            $getRestro = $this->getRestro($serachData, $limit, $is_featured, $is_pagination);

            if (count($getRestro)) {

                foreach ($getRestro as $key => $value) {
                        $dish = Products::where(['products.is_active'=>1, 'products.restaurant_id'=>$value->id])->get();

                        if ($dish) {
                            $dish_new = [];

                            foreach ($dish as $k_new => $v_new) {

                                /*if (empty($v_new->product_attributes->toArray())) {
                                    unset($dish[$k_new]);
                                } else {
                                    array_push($dish_new, $v_new);
                                }*/
                                array_push($dish_new, $v_new);
                            }
                        }
                        // $value->dish = array_values($dish);
                        $value->dish = $dish_new;
                        $value->distance = number_format($value->distance, 2).' KM';
                }
                $data = $getRestro;
                $response['status'] = 1;
                $response['data'] = $data;
                $response['min_price'] = $min_price->min;
                $response['max_price'] = $min_price->max;

            } else {
                $response['status'] = 0;
                $response['message'] = 'No restaurant found.';
                $response['min_price'] = $min_price->min;
                $response['max_price'] = $min_price->max;
            }
            return response()->json($response, 200);
        }
    }

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
                $response['message'] = 'Adderss Not Found.';
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
                    //Panel Notification data
                    $panelNotificationData = new PanelNotifications;
                    $panelNotificationData->user_id = $userId;
                    $panelNotificationData->order_id = null;
                    $panelNotificationData->user_type = 0;
                    $panelNotificationData->notification_for = 'FAQ-Request';
                    $panelNotificationData->notification_type = 3;
                    $panelNotificationData->title = 'FAQ-Request';
                    $panelNotificationData->message = 'New FAQ Request From User '.$userData->name.'<br/> Question: '.$request->question;

                    if ($panelNotificationData->save()) {
                        $panelData = PanelNotifications::select('panel_notifications.*');
                        $adminCount = 0;
                        $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();

                        /*Admin Notification*/
                        $curl_admin = curl_init();

                        curl_setopt_array($curl_admin, array(
                          CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_main_admin_1/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "POST",
                          CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount\n}\n",
                          CURLOPT_HTTPHEADER => array(
                            "cache-control: no-cache",
                            "content-type: application/json",
                            "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_main_admin_1/0",
                            "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                          ),
                        ));

                        $responseNew = curl_exec($curl_admin);
                        $err = curl_error($curl_admin);

                        curl_close($curl_admin);

                        if ($err) {
                          // echo "cURL Error #:" . $err;
                        } else {
                          // echo $responseNew;
                        }
                        /*Admin Notification End*/
                    }
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
        $userData = auth()->user();
        $userId =  $userData->id;
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

    public function getProductTags(Request $request) {
        $userData = auth()->user();
        $userId =  $userData->id;
        $inputs = $request->all();

        $validator = Validator::make($request->all(), [           
            'search_tag'=>'nullable',
            'main_category_id'=>'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $locale = App::getLocale();
            $tags = ProductTags::select('product_tags.id','product_tags.tag','products.main_category_id')->join('products','products.id','=','product_tags.product_id')->where(['status'=>1, 'lang'=>$locale, 'products.main_category_id'=>$inputs['main_category_id']])->groupBy('tag');

            if (array_key_exists("search_tag",$inputs)){
                $search = $inputs['search_tag'];
                $tags->Where("tag",'like','%'.$search.'%');
            }
            $tagData = $tags->get();

            if (count($tagData)) {
                $response['status'] = 1;
                $response['data'] = $tagData;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Tag Not Found.';
                return response()->json($response, 200);
            }
        }
    }

}