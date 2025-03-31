<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use App\Models\GiftBrand;
use App\Models\GiftLike;
use App\Models\GiftCategory;
use App\Models\GiftSubCategory;
use App\Models\Gift;
use App\Models\DeliveryPrice;
use App\Models\Restaurant;
use App\Models\Brand;
use App\Models\Products;
use App\Models\GiftBanner;
use App\Models\Media;
use App\Models\UsersAddress;
use App\Models\UserKiloPoints;
use App\Models\OrderCancelReasions;
use App\Models\GiftAttributeValueLang;
use App\Models\GiftProductAttributes;
use App\Models\GiftProductAttributeValues;
use App\Models\GiftAttributesLang;
use App\Models\GiftCartTopping;
use App\Models\GiftOrderToppings;
use App\Models\PanelNotifications;
use App\Models\GiftUserPlatforms;
use JWTAuth;
use App\Models\UserOtp;
use App\Models\UserWallets;
use App\Models\Topping;
use App\Models\ToppingCategory;
use App\Models\Discount;
use App\Models\GiftCart;
use App\Models\GiftCartDetail;
use App\Models\GiftVarient;
use App\Models\GiftNotification;
use App\Models\GiftOrder;
use App\Models\GiftOrderDetails;
use App\Models\GiftRate;
use App\Models\GiftView;
use App\Models\Orders;
use App\Models\EmailTemplateLang;
use Mail;
use DB,Session;
use App;
use App\Http\Resources\CategoryResource;

class GiftAppController extends Controller {

    public function __construct() {       
        // $this->middleware('auth:giftAuthorization');
        // $this->middleware('auth:api', ['except' => ['registerUser','getUserAvailablePoints']]);
        define("A_TO_Z",'a_to_z');
        define("Z_TO_A",'z_to_a');
    }

    public function getHomeData(Request $request){
        $serachData = $request->all();
        $limit = 10;
        $radius = 10;
        $is_featured = 1;
        $userAuth = giftAuthUserId();

        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $getBanner = $this->getBanner();
            $getGiftCategory = $this->getGiftCategory($serachData, $limit);
            $getGiftSubCategory = $this->getGiftSubCategory($serachData, $limit);
            $getGift = $this->getGift($serachData, $limit, $is_featured);
            $getFastDeliveryGifts = $this->getFastDeliveryGifts($serachData, $limit);
            $getTopItems =  $this->getTopItems($serachData, $limit);
            $notificationCount = $this->getNotificationCount($request);

            if (count($getBanner)) {
                $data['banner'] = $getBanner;

            } else {
                $data['banner'] = [];
            }

            if (count($getGiftCategory)) {
                $data['giftCategory'] = $getGiftCategory;

            } else {
                $data['giftCategory'] = [];
            }

            if (count($getGiftSubCategory)) {
                $data['giftSubCategory'] = $getGiftSubCategory;

            } else {
                $data['giftSubCategory'] = [];
            }

            if (count($getGift)) {
                $data['gift'] = $getGift;

            } else {
                $data['gift'] = [];
            }

            if (count($getFastDeliveryGifts)) {
                $data['fastDeliveryGifts'] = $getFastDeliveryGifts;

            } else {
                $data['fastDeliveryGifts'] = [];
            }

            if (count($getTopItems)) {
                $data['topItems'] = $getTopItems;

            } else {
                $data['topItems'] = [];
            }

            $data['notificationCount'] = $notificationCount;
            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response, 200);
        }
    }

    public function getNotificationCount($request){
        $userData = giftAuthUserId();
        $userId =  $userData[0];
        return GiftNotification::where(['user_id'=>$userId, 'is_read'=>0])->count();
    }

    public function getGiftProductsData(Request $request){

        $serachData = $request->all();
        $limit = 10;
        $radius = 10;
        $is_featured = 0;
        $is_pagination = 1;

        $validator = Validator::make($request->all(), [
            'longitude'=>'required',
            'latitude' => 'required',
            // 'gift_category_id' => 'required',
            // 'gift_sub_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $min_max_point = Gift::select(\DB::raw("MIN(points) AS min, MAX(points) AS max"))->first();
            $min_kilo = $min_max_point->min;
            $max_kilo = $min_max_point->max;

            if (isset($serachData['gift_category_id']) && !empty($serachData['gift_category_id'])) {
                $getGiftSubCategory = $this->getGiftSubCategory($serachData, $limit);

            } else {
                $getGiftSubCategory = array();
            }
            $getGift = $this->getGift($serachData, $limit, $is_featured, $is_pagination);

            if (isset($serachData['slug']) && !empty($serachData['slug'])) {

                if ($serachData['slug'] == 'fast_delivery') {
                    $getGift = $this->getFastDeliveryGifts($serachData, $limit, $is_pagination);

                } else if ($serachData['slug'] == 'top_selling') {
                    $getGift = $this->getTopItems($serachData, $limit, $is_featured, $is_pagination);

                } else if ($serachData['slug'] == 'feature') {
                    $getGift = $this->getGift($serachData, $limit, 1, $is_pagination);
                }
            }

            if (count($getGiftSubCategory)) {
                $data['giftSubCategory'] = $getGiftSubCategory;

            } else {
                $data['giftSubCategory'] = [];
            }

            if (count($getGift)) {
                $points = array_column($getGift->toArray()['data'], 'points');
                $min_kilo = min($points);
                $max_kilo = max($points);

                $data['gift'] = $getGift;
                $response['status'] = 1;

            } else {
                $response['status'] = 0;
                $data['gift'] = (object)[];
            }

            $response['data'] = $data;
            $response['min_kilo'] = $min_kilo;
            $response['max_kilo'] = $max_kilo;
            return response()->json($response, 200);
        }
    }

    public function getGiftDetail(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $serachData = $request->all();
        $limit = 10;
        $radius = 10;
        $is_featured = 1;

        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude'=>'required',
            'gift_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $in_cart = 0;
            $ViewData = GiftView::where(['gift_id'=>$serachData['gift_id'], 'user_id'=> $userId])->first();

            if (!isset($ViewData)) {
                $ViewData = new GiftView;
                $ViewData->gift_id = $serachData['gift_id'];
                $ViewData->user_id = $userId;
                $ViewData->save();
            }

            $gift_detail = Gift::where(['id'=>$serachData['gift_id']])->with('gift_images')->with('variants')->first();

            if ($gift_detail) {
                $attrVals = [];
                $attrValsNew = [];
                $selected_show_attribute = GiftProductAttributes::select('gift_attributes.*')->where(['gift_id' => $gift_detail->id])->orderBy('price', 'ASC')->first();

                if ($selected_show_attribute) {
                    $gift_detail->points = $selected_show_attribute->points;
                    $attrVals = GiftProductAttributeValues::select('gift_attribute_values.*')->where(['gift_attributes_id' => $selected_show_attribute->id])->pluck('attribute_value_lang_id')->toArray();
                    $attrValsNew = GiftProductAttributeValues::select('gift_attribute_values.*')->where(['gift_attributes_id' => $selected_show_attribute->id])->pluck('gift_attribute_values.id')->toArray();

                    if ($userId) {
                        $gift_cart = GiftCart::where('user_id',$userId)->first();

                        if ($gift_cart) {
                            $checkGiftAddedInCart = GiftCartTopping::where('gift_cart_id',$gift_cart->id)->whereIn('gift_attribute_values_id', $attrValsNew)->get();

                            if (count($checkGiftAddedInCart)) {
                                $in_cart = 1;
                            }
                        }
                    }
                }
                $gift_detail->in_cart = $in_cart;

                $getGiftProductAttributeIds = GiftProductAttributeValues::join('gift_attributes','gift_attributes.id','=','gift_attribute_values.gift_attributes_id')->where(['gift_id' => $gift_detail->id])->groupBy('gift_attribute_values.attributes_lang_id')->pluck('gift_attribute_values.attributes_lang_id')->toArray();

                if ($getGiftProductAttributeIds) {
                    $gift_attributes = GiftAttributesLang::select('attributes_lang.name as attribute_name','attributes_lang.id as attributes_lang_id','attributes_lang.is_color')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.category_id'=>$gift_detail->category_id, 'attributes.sub_category_id'=>$gift_detail->sub_category_id])->whereIn('attributes_lang.id', $getGiftProductAttributeIds)->get();

                } else {
                    $gift_attributes = GiftAttributesLang::select('attributes_lang.name as attribute_name','attributes_lang.id as attributes_lang_id','attributes_lang.is_color')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.category_id'=>$gift_detail->category_id, 'attributes.sub_category_id'=>$gift_detail->sub_category_id])->get();
                }

                if ($gift_attributes) {

                    foreach ($gift_attributes as $key => $value) {
                        $getGiftProductAttributeValueIds = GiftProductAttributeValues::join('gift_attributes','gift_attributes.id','=','gift_attribute_values.gift_attributes_id')->where(['gift_id' => $gift_detail->id, 'gift_attribute_values.attributes_lang_id' => $value->attributes_lang_id])->groupBy('gift_attribute_values.attribute_value_lang_id')->pluck('gift_attribute_values.attribute_value_lang_id')->toArray();

                        $attributeValues = GiftAttributeValueLang::select('attribute_value_lang.name as attribute_value_name','attribute_value_lang.id as attribute_value_lang_id','attribute_value_lang.color_code')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where('attribute_values.attributes_lang_id', $value->attributes_lang_id)->whereIn('attribute_value_lang.id', $getGiftProductAttributeValueIds)->get();

                        if (count($attributeValues) < 1) {
                            unset($gift_attributes[$key]);
                        }
                          
                        foreach ($attributeValues as $k => $v) {

                            if (in_array($v->attribute_value_lang_id, $attrVals)) {
                                $v->is_selected = 1;

                            } else {
                                $v->is_selected = 0;
                            }
                        }
                        $value->attributeValues = $attributeValues;

                    }
                }
                $gift_detail->gift_attributes_new = $gift_attributes;
                $gift_detail->gift_attributes_value_id = implode(',', $attrValsNew);
                $data = $gift_detail;

            } else {
                $data = (object)[];
            }

            $response['status'] = 1;
            $response['data'] = $data;
            return response()->json($response, 200);
        }
    }

    public function getAvailablePoints(Request $request) {
        /*$user_id = auth()->user()->id;*/
        $userData = giftAuthUserId();
        $user_id =  $userData[0];

        /*$getOrderCompleted = Orders::where(['user_id' => $user_id, 'order_status' => 'Complete', 'is_kp_transfer' => 'Yes'])->pluck('id')->toArray();*/
        // $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR'])->whereIn('order_id', $getOrderCompleted)->sum('points');
        /*$totalCROrder = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR'])->whereIn('order_id', $getOrderCompleted)->sum('points');
        $totalCRRefund = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR','is_refund'=>'Yes'])->sum('points');
        $totalCR = $totalCROrder + $totalCRRefund;*/
        $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR','is_kp_transfer'=>'Yes'])->sum('points');
        $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'DR'])->sum('points');
        $available_balance = $totalCR-$totalDR;
        $data['available_kiloPoints'] = (float)number_format($available_balance, 2, '.', '');

        $response['status'] = 1;
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function getUserAvailablePoints(Request $request) {

        /*$getOrderCompleted = Orders::where(['user_id' => $user_id, 'order_status' => 'Complete', 'is_kp_transfer' => 'Yes'])->pluck('id')->toArray();*/
        // $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR'])->whereIn('order_id', $getOrderCompleted)->sum('points');
        /*$totalCROrder = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR'])->whereIn('order_id', $getOrderCompleted)->sum('points');
        $totalCRRefund = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR','is_refund'=>'Yes'])->sum('points');
        $totalCR = $totalCROrder + $totalCRRefund;*/
        $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$request->userId,'type'=>'CR','is_kp_transfer'=>'Yes'])->sum('points');
        $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$request->userId,'type'=>'DR'])->sum('points');
        $available_balance = $totalCR-$totalDR;
        $data['available_kiloPoints'] = (float)number_format($available_balance, 2, '.', '');

        $response['status'] = 1;
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function getKPTransactionList(Request $request) {
        /*$user_id = auth()->user()->id;*/
        $userData = giftAuthUserId();
        $user_id =  $userData[0];
        /*$getOrderCompleted = Orders::where(['user_id' => $user_id, 'order_status' => 'Complete', 'is_kp_transfer' => 'Yes'])->pluck('id')->toArray();*/
        // $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR'])->whereIn('order_id', $getOrderCompleted)->sum('points');
        /*$totalCROrder = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR'])->whereIn('order_id', $getOrderCompleted)->sum('points');
        $totalCRRefund = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR','is_refund'=>'Yes'])->sum('points');
        $totalCR = $totalCROrder + $totalCRRefund;*/
        $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'CR','is_kp_transfer'=>'Yes'])->sum('points');
        $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user_id,'type'=>'DR'])->sum('points');
        $available_balance = $totalCR-$totalDR;
        $data['available_kiloPoints'] = (float)number_format($available_balance, 2, '.', '');

        $userKPTransactionList =  UserKiloPoints::on('mysql2')->select('type','points','comment','order_id','created_at')->where(['user_id'=>$user_id])->orderBy('id', 'desc')->get();

        if (count($userKPTransactionList)) {
            $data['transaction_list'] = $userKPTransactionList;

        } else {
            $data['transaction_list'] = array();
        }

        $response['status'] = 1;
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function getBanner(){
        $bannerList = GiftBanner::select('*');
        $bannerDetail = $bannerList->get();
        return $bannerDetail;
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
        
        if (count($getGiftCategory)) {
            $data['giftCategory'] = $getGiftCategory;

        } else {
            $data['giftCategory'] = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function getAllGiftSubCategory(Request $request){
        $serachData = $request->all();
        $limit =  'All';
        $getGiftSubCategory = $this->getGiftSubCategory($serachData, $limit);

        if (count($getGiftSubCategory)) {
            $data['giftSubCategory'] = $getGiftSubCategory;

        } else {
            $data['giftSubCategory'] = [];
        }

        $response['status'] = 1;
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function getGift($serachData, $limit, $is_featured, $is_pagination = 0) {
        $giftList = Gift::where(['is_active'=>1]);

        if (array_key_exists("slug",$serachData)){    
            $slug = $serachData['slug'];

            if ($slug == 'fast-delivery') {
                $giftList->where(['is_ready'=>'Yes']);

            } else if ($slug == 'top-items') {
                $topGifts = GiftOrderDetails::where('order_status', '!=', 'Cancel')->join('gift_orders','gift_orders.id','=','gift_order_details.gift_order_id')->groupBy('gift_order_details.gift_id')->pluck('gift_id')->toArray();
                $giftList->whereIn('gifts.id', $topGifts);
            }
        }

        if (array_key_exists("search_text",$serachData)){
			$search = $serachData['search_text'];
			$giftList->Where("gifts.name",'like','%'.$search.'%');
        }

        if ($is_featured == 1) {
            $giftList->where('is_featured', 'Yes');
        }

        if (array_key_exists("gift_category_id", $serachData)) {
            $category_id = $serachData['gift_category_id'];
			$giftList->where("gifts.category_id", $category_id);
        }

        if (array_key_exists("brand_id", $serachData)) {
            $brand_id = $serachData['brand_id'];
            $giftList->where("gifts.brand_id", $brand_id);
        }

        if (array_key_exists("gift_sub_category_id", $serachData)) {
            $sub_category_id = $serachData['gift_sub_category_id'];
            $giftList->where("gifts.sub_category_id", $sub_category_id);
        }

        if (array_key_exists("sort_by", $serachData)){
			$sort = $serachData['sort_by'];

			if ($sort == 'A_TO_Z') {
				$giftList->orderBy('gifts.name', 'asc');

			} else if ($sort == 'Z_TO_A') {
				$giftList->orderBy('gifts.name', 'desc');

			} else if ($sort == 'KPs') {
                $giftList->orderBy('gifts.points', 'asc');
            }
        }

        if (array_key_exists("gift_offer", $serachData)) {

            if ($serachData['gift_offer'] == 'Yes') {
                $giftList->where('gifts.discount', '>', 0)->orderBy('gifts.discount', 'desc');
            }
        }

        if (array_key_exists("featured", $serachData)) {

            if ($serachData['featured'] == 'Yes') {
                $giftList->where('gifts.is_featured', 'Yes');
            }
        }

        if (array_key_exists("kilopoints_ihave", $serachData)) {

            if ($serachData['kilopoints_ihave'] == 'Yes') {
                /*$userId = auth()->user()->id;*/
                $userData = giftAuthUserId();
                $userId =  $userData[0];
                $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'CR'])->sum('points');
                $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'DR'])->sum('points');
                $available_balance = $totalCR-$totalDR;
                $giftList->whereBetween('gifts.points', [0, $available_balance]);
            }
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $giftList->whereBetween('gifts.points', [$min_kilo, $max_kilo]);
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $giftDetail = $giftList->paginate($limit);

            } else {
                $giftList->limit($limit);
                $giftDetail = $giftList->get();
            }

        }  else {
            $giftDetail = $giftList->get();
        }
        return $giftDetail;
    }

    public function getFavGift($serachData, $limit, $is_featured, $is_pagination = 0, $giftIds = 0) {

        if ($giftIds) {
            $giftList = Gift::where(['is_active'=>1])->whereIn('id', $giftIds);

        } else {
            $giftList = Gift::where(['is_active'=>1]);
        }

        if (array_key_exists("slug",$serachData)){    
            $slug = $serachData['slug'];

            if ($slug == 'fast-delivery') {
                $giftList->where(['is_ready'=>'Yes']);

            } else if ($slug == 'top-items') {
                $topGifts = GiftOrderDetails::where('order_status', '!=', 'Cancel')->join('gift_orders','gift_orders.id','=','gift_order_details.gift_order_id')->groupBy('gift_order_details.gift_id')->pluck('gift_id')->toArray();
                $giftList->whereIn('gifts.id', $topGifts);
            }
        }

        if (array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $giftList->Where("gifts.name",'like','%'.$search.'%');
        }

        if ($is_featured == 1) {
            $giftList->where('is_featured', 'Yes');
        }

        if (array_key_exists("gift_category_id", $serachData)) {
            $category_id = $serachData['gift_category_id'];
            $giftList->where("gifts.category_id", $category_id);
        }

        if (array_key_exists("brand_id", $serachData)) {
            $brand_id = $serachData['brand_id'];
            $giftList->where("gifts.brand_id", $brand_id);
        }

        if (array_key_exists("gift_sub_category_id", $serachData)) {
            $sub_category_id = $serachData['gift_sub_category_id'];
            $giftList->where("gifts.sub_category_id", $sub_category_id);
        }

        if (array_key_exists("sort_by", $serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {
                $giftList->orderBy('gifts.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $giftList->orderBy('gifts.name', 'desc');

            } else if ($sort == 'KPs') {
                $giftList->orderBy('gifts.points', 'asc');
            }
        }

        if (array_key_exists("gift_offer", $serachData)) {

            if ($serachData['gift_offer'] == 'Yes') {
                $giftList->where('gifts.discount', '>', 0)->orderBy('gifts.discount', 'desc');
            }
        }

        if (array_key_exists("featured", $serachData)) {

            if ($serachData['featured'] == 'Yes') {
                $giftList->where('gifts.is_featured', 'Yes');
            }
        }

        if (array_key_exists("kilopoints_ihave", $serachData)) {

            if ($serachData['kilopoints_ihave'] == 'Yes') {
                /*$userId = auth()->user()->id;*/
                $userData = giftAuthUserId();
                $userId =  $userData[0];
                $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'CR'])->sum('points');
                $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'DR'])->sum('points');
                $available_balance = $totalCR-$totalDR;
                $giftList->whereBetween('gifts.points', [0, $available_balance]);
            }
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $giftList->whereBetween('gifts.points', [$min_kilo, $max_kilo]);
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $giftDetail = $giftList->paginate($limit);

            } else {
                $giftList->limit($limit);
                $giftDetail = $giftList->get();
            }

        }  else {
            $giftDetail = $giftList->get();
        }
        return $giftDetail;
    }

    public function getFastDeliveryGifts($serachData, $limit, $is_pagination = 0) {
        $giftList = Gift::where(['is_active'=>1, 'is_ready'=>'Yes']);

        if (array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $giftList->Where("gifts.name",'like','%'.$search.'%');
        }

        if (array_key_exists("gift_category_id", $serachData)) {
            $category_id = $serachData['gift_category_id'];
            $giftList->where("gifts.category_id", $category_id);
        }

        if (array_key_exists("brand_id", $serachData)) {
            $brand_id = $serachData['brand_id'];
            $giftList->where("gifts.brand_id", $brand_id);
        }

        if (array_key_exists("gift_sub_category_id", $serachData)) {
            $sub_category_id = $serachData['gift_sub_category_id'];
            $giftList->where("gifts.sub_category_id", $sub_category_id);
        }

        if (array_key_exists("sort_by", $serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {
                $giftList->orderBy('gifts.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $giftList->orderBy('gifts.name', 'desc');

            } else if ($sort == 'KPs') {
                $giftList->orderBy('gifts.points', 'asc');
            }
        }

        if (array_key_exists("gift_offer", $serachData)) {

            if ($serachData['gift_offer'] == 'Yes') {
                $giftList->where('gifts.discount', '>', 0)->orderBy('gifts.discount', 'desc');
            }
        }

        if (array_key_exists("featured", $serachData)) {

            if ($serachData['featured'] == 'Yes') {
                $giftList->where('gifts.is_featured', 'Yes');
            }
        }

        if (array_key_exists("kilopoints_ihave", $serachData)) {

            if ($serachData['kilopoints_ihave'] == 'Yes') {
                /*$userId = auth()->user()->id;*/
                $userData = giftAuthUserId();
                $userId =  $userData[0];
                $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'CR'])->sum('points');
                $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'DR'])->sum('points');
                $available_balance = $totalCR-$totalDR;
                $giftList->whereBetween('gifts.points', [0, $available_balance]);
            }
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $giftList->whereBetween('gifts.points', [$min_kilo, $max_kilo]);
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $giftDetail = $giftList->paginate($limit);

            } else {
                $giftList->limit($limit);
                $giftDetail = $giftList->get();
            }

        }  else {
            $giftDetail = $giftList->get();
        }
        return $giftDetail;
    }

    public function getTopItems($serachData, $limit, $is_featured = 0, $is_pagination = 0)
    {
        $topGifts = GiftOrderDetails::where('order_status', '!=', 'Cancel')->join('gift_orders','gift_orders.id','=','gift_order_details.gift_order_id')->groupBy('gift_order_details.gift_id')->pluck('gift_id')->toArray();
        $giftList = Gift::where('gifts.is_active',1)->whereIn('gifts.id', $topGifts);
       
        if (array_key_exists("search_text",$serachData)){
            $search = $serachData['search_text'];
            $giftList->Where("gifts.name",'like','%'.$search.'%');
        }

        if ($is_featured == 1) {
            $giftList->where('is_featured', 'Yes');
        }

        if (array_key_exists("gift_category_id", $serachData)) {
            $category_id = $serachData['gift_category_id'];
            $giftList->where("gifts.category_id", $category_id);
        }

        if (array_key_exists("brand_id", $serachData)) {
            $brand_id = $serachData['brand_id'];
            $giftList->where("gifts.brand_id", $brand_id);
        }

        if (array_key_exists("gift_sub_category_id", $serachData)) {
            $sub_category_id = $serachData['gift_sub_category_id'];
            $giftList->where("gifts.sub_category_id", $sub_category_id);
        }

        if (array_key_exists("sort_by", $serachData)){
            $sort = $serachData['sort_by'];

            if ($sort == 'A_TO_Z') {
                $giftList->orderBy('gifts.name', 'asc');

            } else if ($sort == 'Z_TO_A') {
                $giftList->orderBy('gifts.name', 'desc');

            } else if ($sort == 'KPs') {
                $giftList->orderBy('gifts.points', 'asc');
            }
        }

        if (array_key_exists("gift_offer", $serachData)) {

            if ($serachData['gift_offer'] == 'Yes') {
                $giftList->where('gifts.discount', '>', 0)->orderBy('gifts.discount', 'desc');
            }
        }

        if (array_key_exists("featured", $serachData)) {

            if ($serachData['featured'] == 'Yes') {
                $giftList->where('gifts.is_featured', 'Yes');
            }
        }

        if (array_key_exists("kilopoints_ihave", $serachData)) {

            if ($serachData['kilopoints_ihave'] == 'Yes') {
                /*$userId = auth()->user()->id;*/
                $userData = giftAuthUserId();
                $userId =  $userData[0];
                $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'CR'])->sum('points');
                $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'DR'])->sum('points');
                $available_balance = $totalCR-$totalDR;
                $giftList->whereBetween('gifts.points', [0, $available_balance]);
            }
        }

        if (!empty($serachData['min_kilo']) && !empty($serachData['max_kilo'])) { 
            $min_kilo = $serachData['min_kilo'];
            $max_kilo = $serachData['max_kilo'];
            $giftList->whereBetween('gifts.points', [$min_kilo, $max_kilo]);
        }

        if ($limit != 'All') {

            if ($is_pagination == 1) {
                $giftDetail = $giftList->paginate($limit);

            } else {
                $giftList->limit($limit);
                $giftDetail = $giftList->get();
            }

        }  else {
            $giftDetail = $giftList->get();
        }
        return $giftDetail;
    }

    public function getGiftCategory($serachData,$limit){
        $giftCategoryList = GiftCategory::where(['type'=>2,'status'=>1]);

        if (array_key_exists("search_text",$serachData)) {
			$search = $serachData['search_text'];
			$giftCategoryList->Where("name",'like','%'.$search.'%');
        }

        if ($limit != 'All') {
            $giftCategoryList->limit($limit);
        }
        $giftDetail= $giftCategoryList->get();
        return $giftDetail;
    }

    public function getGiftSubCategory($serachData, $limit) {
        $giftCategoryList = GiftSubCategory::where(['status'=>1]);

        if (array_key_exists("search_text",$serachData)) {
            $search = $serachData['search_text'];
            $giftCategoryList->Where("name",'like','%'.$search.'%');
        }

        if (array_key_exists("gift_category_id", $serachData)) {
            $gift_category_id = $serachData['gift_category_id'];
            $giftCategoryList->Where("category_id", $gift_category_id);
        }

        if ($limit != 'All') {
            $giftCategoryList->limit($limit);
        }
        $giftDetail= $giftCategoryList->get();
        return $giftDetail;
    }

    public function likeGift(Request $request){
        $inputData = $request->all();
        /*$userData = auth()->user();
        $userId =  $userData->id;*/

        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $validator = Validator::make($request->all(), [
            'gift_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $data = GiftLike::where(['gift_id'=>$inputData['gift_id'], 'user_id'=> $userId])->first();

            if (!isset($data)) {
                $data = new GiftLike;
                $data->gift_id = $inputData['gift_id'];
                $data->user_id = $userId;

                if ($data->save()) {
                    $response['status'] = 1;
                    $response['message'] = 'Gift like successfully';

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                }

            } else {
                GiftLike::where(['gift_id'=>$inputData['gift_id'], 'user_id'=> $userId])->delete();
                $response['status'] = 1;
                $response['message'] = 'Gift Unliked successfully';
            }

            return response()->json($response, 200);
        }
    }

    public function favoriteGifts(Request $request){
        $inputData = $request->all();
        /*$userData = auth()->user();
        $userId = $userData->id;*/

        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $limit = 10;
        $is_featured = 0;
        $is_pagination = 1;
        
        $giftIds = GiftLike::where(['user_id'=> $userId])->pluck('gift_id')->toArray();

        if ($giftIds) {
            $min_max_point = Gift::select(\DB::raw("MIN(points) AS min, MAX(points) AS max"))->first();

            if (isset($serachData['gift_category_id']) && !empty($serachData['gift_category_id'])) {
                $getGiftSubCategory = $this->getGiftSubCategory($serachData, $limit);

            } else {
                $getGiftSubCategory = array();
            }
            $getGift = $this->getFavGift($inputData, $limit, $is_featured, $is_pagination, $giftIds);

            if (count($getGiftSubCategory)) {
                $data['giftSubCategory'] = $getGiftSubCategory;

            } else {
                $data['giftSubCategory'] = [];
            }

            if (count($getGift)) {
                $data['gift'] = $getGift;
                $response['status'] = 1;

            } else {
                $response['status'] = 0;
                $data['gift'] = (object)[];
            }

            $response['data'] = $data;
            $response['min_kilo'] = $min_max_point->min;
            $response['max_kilo'] = $min_max_point->max;

            // $gift_detail = Gift::whereIn('id', $data)->with('gift_images')->with('variants')->get();

            /*$response['status'] = 1;
            $response['data'] = $gift_detail;*/
            // return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['message'] = 'Favorite gift not found.';
        }

        return response()->json($response, 200);
    }

    public function getGiftBrand(Request $request){
        $serachData = $request->all();
        $gifts = [];

        if (isset($serachData['gift_category_id']) || isset($serachData['gift_sub_category_id'])) {
            $gifts = Gift::select('brand_id');

            if (isset($serachData['gift_category_id']) && !empty($serachData['gift_category_id'])) {
                $gifts->where('category_id', $serachData['gift_category_id']);
            }

            if (isset($serachData['gift_sub_category_id']) && !empty($serachData['gift_sub_category_id'])) {
                $gifts->where('sub_category_id', $serachData['gift_sub_category_id']);
            }
           $gifts = $gifts->groupBy('brand_id')->pluck('brand_id')->toArray();
        }

        $getBrands = GiftBrand::where('status', 1);

        if ($gifts) {
            $getBrands->whereIn('id', $gifts);
        }

        // $getBrands = GiftBrand::where('status', 1)->get();
        $getBrands = $getBrands->get();
        
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

    public function add_cart(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/

        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $input = $request->all();
        $shipping_charges = '0';
        $free_ship_point = 0;

        $validator = Validator::make($request->all(), [
            'gift_id' => 'required',
            'qty' => 'required',
            'points' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);    

        } else {
            $giftDetail = Gift::where(['id'=>$input['gift_id']])->first();

            if ($giftDetail) {
                $gift_cart = GiftCart::where('user_id',$userId)->first();

                if ($gift_cart) {
                    $checkSameGift = GiftCartDetail::select('gift_cart_details.*')->where(['user_id'=>$userId, 'gift_cart_details.gift_id'=>$input['gift_id']])->join('gifts','gifts.id','=','gift_cart_details.gift_id')->first();

                    if ($checkSameGift) {
                                  
                        if (isset($input['gift_cart_detail_id'])) {

                            if ($input['qty'] == 0) {
                                GiftCartDetail::where(['id'=>$input['gift_cart_detail_id']])->delete();
                                GiftCartTopping::where(['gift_cart_detail_id'=>$input['gift_cart_detail_id']])->delete();
                                $response['status'] = 1;
                                $response['message'] = 'Your gift removed from cart.';

                                //Update Gift Cart
                                $getCartTotalPoints = GiftCartDetail::where(['user_id'=>$userId])->sum('points');

                                $updateGiftCart = [
                                    'points'=>$getCartTotalPoints,
                                ];

                                $gift_cart = GiftCart::where('user_id',$userId)->firstOrFail();
                                $gift_cart->update($updateGiftCart);

                            } else {
                                //Update Gift Cart Detail
                                $upd = [
                                    'qty'=>$input['qty'],
                                    'points'=>$input['points'],
                                ];
                                $cartDetailData = GiftCartDetail::where('user_id',$userId)->where('id',$input['gift_cart_detail_id'])->first();

                                if ($cartDetailData) {
                                    $cartDetailData->update($upd);

                                    //Update Gift Cart
                                    $getCartTotalPoints = GiftCartDetail::where(['user_id'=>$userId])->sum('points');

                                    $updateGiftCart = [
                                        'points'=>$getCartTotalPoints,
                                    ];

                                    $gift_cart = GiftCart::where('user_id',$userId)->firstOrFail();
                                    $gift_cart->update($updateGiftCart);

                                    $response['status'] = 1;
                                    $response['message'] = ' Item update into cart.';

                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Error Occured.';
                                }
                            }

                        } else {
                            /*$response['status'] = 0;
                            $response['message'] = 'You already add this gift.';*/
                            
                            //Update Gift Cart
                            $getCartTotalPoints = GiftCartDetail::where(['user_id'=>$userId])->sum('points');

                            $updateGiftCart = [
                                'points'=>$getCartTotalPoints+$input['points'],
                            ];

                            $gift_cart = GiftCart::where('user_id',$userId)->firstOrFail();

                            if ($gift_cart->update($updateGiftCart)) {
                                //New record add when cart is empty
                                $inputCartDetail = new GiftCartDetail();
                                $inputCartDetail->user_id = $userId;
                                $inputCartDetail->gift_cart_id = $gift_cart->id;
                                $inputCartDetail->gift_id = $input['gift_id'];
                                $inputCartDetail->qty = $input['qty'];
                                $inputCartDetail->points = $input['points'];

                                if (isset($input['gift_varient_id'])) {
                                    $gift_varient = GiftVarient::where('id',$input['gift_varient_id'])->first();

                                    if ($gift_varient) {
                                        $inputCartDetail->gift_varient_id = $input['gift_varient_id'];
                                        $inputCartDetail->varient_name = $gift_varient->name;

                                    } else {
                                        $inputCartDetail->gift_varient_id = $input['gift_varient_id'];
                                    }
                                }

                                if ($inputCartDetail->save()) {

                                    if (isset($input['gift_attribute_values_id']) && !empty($input['gift_attribute_values_id'])) {
                                        $attributeValues = explode(",", $input['gift_attribute_values_id']);

                                        foreach ($attributeValues as $key => $value) {
                                            $attributeValues = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','gift_attribute_values.id as gift_attribute_values_id','gift_attribute_values.attributes_lang_id')->where(['gift_attribute_values.id' => $value])->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->first();

                                            if ($attributeValues) {
                                                $cartToppingData = new GiftCartTopping();
                                                $cartToppingData->gift_cart_id = $gift_cart->id;
                                                $cartToppingData->gift_cart_detail_id = $inputCartDetail->id;
                                                $cartToppingData->gift_attribute_values_id = $value;
                                                $cartToppingData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                                $cartToppingData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                                $cartToppingData->attribute_value_name = $attributeValues->attribute_value_name;
                                                $cartToppingData->points = $attributeValues->points;
                                                $cartToppingData->save();
                                            }
                                        }
                                    }
                                    $response['status'] = 1;
                                    $response['message'] = 'Item added into cart.';

                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Error Occured.';
                                }

                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Error Occured.';
                            }
                        }

                    } else {
                        //Update Gift Cart
                        $getCartTotalPoints = GiftCartDetail::where(['user_id'=>$userId])->sum('points');

                        $updateGiftCart = [
                            'points'=>$getCartTotalPoints+$input['points'],
                        ];

                        $gift_cart = GiftCart::where('user_id',$userId)->firstOrFail();

                        if ($gift_cart->update($updateGiftCart)) {
                            //New record add when cart is empty
                            $inputCartDetail = new GiftCartDetail();
                            $inputCartDetail->user_id = $userId;
                            $inputCartDetail->gift_cart_id = $gift_cart->id;
                            $inputCartDetail->gift_id = $input['gift_id'];
                            $inputCartDetail->qty = $input['qty'];
                            $inputCartDetail->points = $input['points'];

                            if (isset($input['gift_varient_id'])) {
                                $gift_varient = GiftVarient::where('id',$input['gift_varient_id'])->first();

                                if ($gift_varient) {
                                    $inputCartDetail->gift_varient_id = $input['gift_varient_id'];
                                    $inputCartDetail->varient_name = $gift_varient->name;

                                } else {
                                    $inputCartDetail->gift_varient_id = $input['gift_varient_id'];
                                }
                            }

                            if ($inputCartDetail->save()) {

                                if (isset($input['gift_attribute_values_id']) && !empty($input['gift_attribute_values_id'])) {
                                    $attributeValues = explode(",", $input['gift_attribute_values_id']);

                                    foreach ($attributeValues as $key => $value) {
                                        $attributeValues = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','gift_attribute_values.id as gift_attribute_values_id','gift_attribute_values.attributes_lang_id')->where(['gift_attribute_values.id' => $value])->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->first();

                                        if ($attributeValues) {
                                            $cartToppingData = new GiftCartTopping();
                                            $cartToppingData->gift_cart_id = $gift_cart->id;
                                            $cartToppingData->gift_cart_detail_id = $inputCartDetail->id;
                                            $cartToppingData->gift_attribute_values_id = $value;
                                            $cartToppingData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                            $cartToppingData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                            $cartToppingData->attribute_value_name = $attributeValues->attribute_value_name;
                                            $cartToppingData->points = $attributeValues->points;
                                            $cartToppingData->save();
                                        }
                                    }
                                }
                                $response['status'] = 1;
                                $response['message'] = 'Item added into cart.';

                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Error Occured.';
                            }

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Error Occured.';
                        }
                    }

                } else {         
                    //New gift cart created here only
                    $gift_cart = new GiftCart();
                    $gift_cart->user_id = $userId;
                    $gift_cart->points = $input['points'];

                    if ($gift_cart->save()) {
                        //New record add when cart is empty
                        $inputCartDetail = new GiftCartDetail();
                        $inputCartDetail->user_id = $userId;
                        $inputCartDetail->gift_cart_id = $gift_cart->id;
                        $inputCartDetail->gift_id = $input['gift_id'];
                        $inputCartDetail->qty = $input['qty'];
                        $inputCartDetail->points = $input['points'];

                        if (isset($input['gift_varient_id'])) {
                            $gift_varient = GiftVarient::where('id',$input['gift_varient_id'])->first();

                            if ($gift_varient) {
                                $inputCartDetail->gift_varient_id = $input['gift_varient_id'];
                                $inputCartDetail->varient_name = $gift_varient->name;

                            } else {
                                $inputCartDetail->gift_varient_id = $input['gift_varient_id'];
                            }
                        }

                        if ($inputCartDetail->save()) {

                            if (isset($input['gift_attribute_values_id']) && !empty($input['gift_attribute_values_id'])) {
                                $attributeValues = explode(",", $input['gift_attribute_values_id']);

                                foreach ($attributeValues as $key => $value) {
                                    $attributeValues = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','gift_attribute_values.id as gift_attribute_values_id','gift_attribute_values.attributes_lang_id')->where(['gift_attribute_values.id' => $value])->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->first();

                                    if ($attributeValues) {
                                        $cartToppingData = new GiftCartTopping();
                                        $cartToppingData->gift_cart_id = $gift_cart->id;
                                        $cartToppingData->gift_cart_detail_id = $inputCartDetail->id;
                                        $cartToppingData->gift_attribute_values_id = $value;
                                        $cartToppingData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                        $cartToppingData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                        $cartToppingData->attribute_value_name = $attributeValues->attribute_value_name;
                                        $cartToppingData->points = $attributeValues->points;
                                        $cartToppingData->save();
                                    }
                                }
                            }
                            $response['status'] = 1;
                            $response['message'] = 'Item added into cart.';

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Error Occured.';
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }
                }

                $query = GiftCartDetail::where('user_id',$userId)->get();
                $totalQty = 0;
                $totalPoints = 0.00;

                if (count($query)) {

                    foreach ($query as $key => $value) {
                        $giftDetail = Gift::where(['id'=>$value->gift_id])->first();
                        $totalPoints += $value->points;
                        $totalQty += $value->qty;
                        $value->gifts = $giftDetail;
                        $product_toppings = GiftCartTopping::select('gift_attribute_values_id','attribute_value_name','points')->where('gift_cart_detail_id', $value->id)->get();

                        if (count($product_toppings)) {
                            $value->is_topping = 1;

                        } else {
                            $value->is_topping = 0;
                        }
                        $value->gifts->toppings = $product_toppings;
                    }

                    $giftCartData = GiftCart::where(['id'=>$query[0]->gift_cart_id, 'user_id'=>$userId])->first();
                    $response['data']['items'] = $query;
                    $response['data']['gift_cart_id'] = $query[0]->gift_cart_id;
                    $response['data']['totalQty'] =  $totalQty;
                    $response['data']['totalPoints'] =  $totalPoints;
                    $response['data']['address_id'] =  $giftCartData->address_id;
                    $adminDeliveryPrice = DeliveryPrice::get();

                    if (count($adminDeliveryPrice)) {
                       $free_ship_point = $adminDeliveryPrice[0]->free_ship_point;

                        if ($totalPoints < $free_ship_point) {
                            $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$adminDeliveryPrice[0]->latitude.','.$adminDeliveryPrice[0]->longitude.'&destinations='. $input['latitude'].','. $input['longitude'].'&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');

                            $googleData = json_decode($googleData);

                            if ($googleData->rows[0]->elements[0]->status == 'OK') {
                                $metter = $googleData->rows[0]->elements[0]->distance->value ?? 0;  
                                $total_km = ($metter/1000);
                                $shipping_charges = $total_km * $adminDeliveryPrice[0]->gift_charge_per_km;

                                $taxPercentage = getCountryTaxByLatLong($input['latitude'], $input['longitude']);
                                $tax_amount = (($shipping_charges*$taxPercentage)/100);
                                $response['data']['tax_amount'] =  $tax_amount;
                                $response['data']['taxPercentage'] =  $taxPercentage;

                                if ($total_km > 0) {
                                    $response['data']['total_km'] = number_format($total_km, 2).' KM';
                                }

                                if ($giftCartData) {
                                    //update shipping charges
                                    $giftCartData->shipping_charges = number_format($shipping_charges, 2);
                                    $giftCartData->update();
                                }
                            }
                            //Calculate Charges
                            $response['data']['shipping_charges'] = $shipping_charges; 

                        } else {
                            $response['data']['shipping_charges'] = 0;
                        }
                        $response['data']['free_ship_point'] = $free_ship_point;
                        $response['data']['warehouse_address'] = getWareHouseAddress();
                    }

                } else {
                    $response['data'] = [];
                    $response['data']['totalQty'] =  $totalQty;
                    $response['data']['totalPoints'] =  $totalPoints;
                    $response['data']['address_id'] =  '';
                    $response['data']['shipping_charges'] = '0';
                    $response['data']['free_ship_point'] = $free_ship_point;
                    $response['data']['warehouse_address'] = getWareHouseAddress();
                }
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Gift not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function gift_cart_list(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $shipping_charges = 0;
        $total_km = 0;
        $totalQty = 0;
        $free_ship_point = 0;
        $totalPoints = 0.00;
        $address_id = '';
        $order_type = '';
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = GiftCartDetail::where('user_id',$userId)->get();

            if (count($query)) {
                $gift_cart = GiftCart::where(['id'=>$query[0]->gift_cart_id, 'user_id'=>$userId])->first();

                if ($gift_cart) {

                    if (isset($input['order_type'])) {
                        $gift_cart->order_type = $input['order_type'];
                        $order_type = $input['order_type'];

                        if ($input['order_type'] == 2) {

                            if (isset($input['pick_type'])) {
                                $gift_cart->pick_type = $input['pick_type'];

                                if ($input['pick_type'] == 'later') {
                                    $gift_cart->pick_datetime = $input['pick_datetime'];
                                }
                            } 

                            if (isset($input['pickup_option'])) {
                                $gift_cart->pickup_option = $input['pickup_option'];
                                if($input['pickup_option'] == 'Inside-The-Car') {
                                    $gift_cart->car_color = $input['car_color'];
                                    $gift_cart->car_number = $input['car_number'];
                                    $gift_cart->car_brand = $input['car_brand'];
                                }
                            }
                        }
                    }

                    if (isset($input['address_id'])) {
                        $user_address = UsersAddress::where(['id'=>$input['address_id']])->first();

                        if ($user_address) {
                            $gift_cart->latitude = $user_address->latitude;
                            $gift_cart->longitude = $user_address->longitude;
                            $gift_cart->building_number = $user_address->building_number;
                            $gift_cart->building_name = $user_address->building_name;
                            $gift_cart->landmark = $user_address->landmark;
                            $gift_cart->address = $user_address->address;
                            $gift_cart->address_type = $user_address->address_type;
                        }
                        $gift_cart->address_id = $input['address_id'];
                        $address_id = $input['address_id'];
                    }

                    if (isset($input['address'])) {
                        $gift_cart->latitude = $input['latitude'] ?? null;
                        $gift_cart->longitude = $input['longitude'] ?? null;
                        $gift_cart->building_number = $input['building_number'] ?? null;
                        $gift_cart->building_name = $input['building_name'] ?? null;
                        $gift_cart->landmark = $input['landmark'] ?? null;
                        $gift_cart->address = $input['address'] ?? null;
                        $gift_cart->address_type = $input['address_type'] ?? null;
                    }

                    if ($gift_cart->update()) {
                        $response['status'] = 1;
                        // $response['message'] = 'Data updated successfully.';

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Cart not found.';
                }

                foreach ($query as $key => $value) {
                    $giftDetail = Gift::where(['id'=>$value->gift_id])->first();
                    $totalPoints += $value->points;
                    $totalQty += $value->qty;
                    $value->gifts = $giftDetail;

                    $product_toppings = GiftCartTopping::select('gift_attribute_values_id','attribute_value_name','points')->where('gift_cart_detail_id', $value->id)->get();

                    if (count($product_toppings)) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }
                    $value->gifts->toppings = $product_toppings;
                }
                $giftCartData = GiftCart::where(['id'=>$query[0]->gift_cart_id, 'user_id'=>$userId])->first();
                $adminDeliveryPrice = DeliveryPrice::get();

                if (isset($input['order_type']) && $input['order_type'] == 3) {

                    if (count($adminDeliveryPrice)) {
                       $free_ship_point = $adminDeliveryPrice[0]->free_ship_point;

                        if ($totalPoints < $free_ship_point) {
                            $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$adminDeliveryPrice[0]->latitude.','.$adminDeliveryPrice[0]->longitude.'&destinations='. $input['latitude'].','. $input['longitude'].'&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');

                            if (isset($user_address)) {
                                $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$adminDeliveryPrice[0]->latitude.','.$adminDeliveryPrice[0]->longitude.'&destinations='. $user_address->latitude.','. $user_address->longitude.'&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                            }


                            $googleData = json_decode($googleData);

                            if ($googleData->rows[0]->elements[0]->status == 'OK') {
                                $metter = $googleData->rows[0]->elements[0]->distance->value ?? 0;  
                                $total_km = ($metter/1000);
                                $shipping_charges = $total_km * $adminDeliveryPrice[0]->gift_charge_per_km;

                                $taxPercentage = getCountryTaxByLatLong($input['latitude'], $input['longitude']);
                                $tax_amount = (($shipping_charges*$taxPercentage)/100);
                                //Update Tax Data
                                $giftCartData->tax_amount =  $tax_amount;
                                $giftCartData->taxPercentage =  $taxPercentage;
                                $response['data']['tax_amount'] = $tax_amount;

                                if ($total_km > 0) {
                                    $response['data']['total_km'] = number_format($total_km, 2).' KM';
                                }

                                if ($giftCartData) {
                                    //update shipping charges
                                    $giftCartData->shipping_charges = $shipping_charges;
                                    $giftCartData->update();
                                }
                            }
                            //Calculate Charges
                            $response['data']['shipping_charges'] = $shipping_charges; 

                        } else {
                            $response['data']['shipping_charges'] = 0;
                        }
                    }

                } else {
                    $response['data']['tax_amount'] = 0;
                    $response['data']['shipping_charges'] = 0;
                    $response['data']['total_km'] = 0;
                }

                $response['status'] = 1;
                $response['data']['items'] = $query;
                $response['data']['gift_cart_id'] = $query[0]->gift_cart_id;
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['totalPoints'] =  $totalPoints;
                $response['data']['free_ship_point'] = $free_ship_point;
                $response['data']['warehouse_address'] = getWareHouseAddress();

                if ($giftCartData) {
                    $response['data']['address_id'] = $giftCartData->address_id;

                } else {
                    $response['data']['address_id'] = '';
                }

                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                $response['data'] = [];
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['totalPoints'] =  $totalPoints;
                $response['data']['address_id'] =  '';
                $response['data']['shipping_charges'] = 0;
                $response['data']['tax_amount'] = 0;
                $response['data']['free_ship_point'] = $free_ship_point;
                $response['data']['warehouse_address'] = getWareHouseAddress();
                return response()->json($response, 200);
            }
        }
    }

    public function payByOther(Request $request){
        /*$userData = auth()->user();
        $userId = $userData->id;*/

        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $input = $request->all();
        $avlBal = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = GiftCart::where(['user_id'=>$userId, 'id'=>$input['cart_id']])->first();

            if ($query) {
                $validationError = false;

                if ($input['payment_method'] == 'Pay-By-Other') {

                    if (!isset($input['country_code']) && !isset($input['number']) && !isset($input['name'])) {
                        $validationError = true;
                        $status = 0;
                        $message = 'Please fill all mandate fields.';
                    }
                }

                if ($validationError) {
                    $response['status'] = $status;
                    $response['message'] = $message;
                    return response()->json($response, 200);

                } else {
                    $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'CR'])->sum('points');
                    $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'DR'])->sum('points');
                    $available_balance = $totalCR-$totalDR;

                    if ($query->points <= $available_balance) {
                        $user = User::where(['country_code'=>$input['country_code'],'mobile'=>$input['number'],'type'=>0])->first();

                        if ($user) {
                            $query->payment_type = $input['payment_method'];
                            $query->paybyother_name = $input['name'];
                            $query->paybyother_country_code = $input['country_code'];
                            $query->paybyother_number = $input['number'];

                            if ($query->save()) {
                                //send notification
                                $payment_link = url('gift/payByOther/'.$query->id);
                                $notificationData = new GiftNotification;
                                $notificationData->user_id = $user->id;
                                $notificationData->user_type = 2;
                                $notificationData->notification_type = 3;
                                $notificationData->notification_for = 'Gift Shipping Fee Payment';
                                $notificationData->title = 'Gift Shipping Fee Payment';
                                $notificationData->message = 'Your frined request you to pay gift shipping fees.';
                                $notificationData->payment_link = $payment_link;
                                $notificationData ->save();
                                send_notification(1, $user->id, 'Gift Shipping Fee Payment', array('title'=>'Order Placed','message'=>$notificationData->message,'type'=>'Gift','key'=>'event'));
                                //sent mail to payment user
                                /*$current_data = date('d, M Y', strtotime(today()));
                                $email = EmailTemplateLang::where('email_id', 1)->where('lang', 'en')->select(['name', 'subject', 'description','footer'])->first();
                                $payment_link = url('gift/payByOther/'.$orderdata->id);
                                $description = $email->description;
                                $description = str_replace("[NAME]", $input['name'], $description);
                                $description = str_replace("[ORDER_DATE]", $current_data, $description);
                                // $description = str_replace("[ORDER_ID]", $orderdata->id, $description);
                                $description = str_replace("[PAYMENT_LINK]", $payment_link, $description);
                                $description = str_replace("[REQ_USER_EMAIL]", $userData->email, $description);

                                $order_detail=(object)[];
                                $order_detail->description = $description;
                                $order_detail->footer = $email->footer;

                                Mail::send('emails.order_details', compact('order_detail'), function($message)use($input, $email) {
                                    $message->to($input['email'], config('app.name'))->subject($email->subject);
                                    $message->from('support@contactless.com',config('app.name'));
                                });*/

                                $response['status'] = 1;
                                $response['message'] = 'Request sent successfully.';

                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Error Occured.';
                            }

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'This user is not register and SMS service not available now so please check with registered user only.';
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Your available balance is low.';
                    }
                    return response()->json($response, 200);
                } 

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                return response()->json($response, 200);
            }
        }
    }

    public function payByOtherPaymentStatus(Request $request){
       /*$userData = auth()->user();
        $userId =  $userData->id;*/

        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = GiftCart::where(['user_id'=>$userId, 'id'=>$input['cart_id']])->first();

            if ($query) {
                $response['status'] = 0;
                $response['message'] = 'Payment not received.';
                $response['data'] = $query;
                return response()->json($response, 200);

            } else {
                $response['status'] = 1;
                $response['message'] = 'Payment Received.';
                return response()->json($response, 200);
            }
        }
    }

    public function gift_checkout(Request $request){
        /*$userData = auth()->user();
        $userId = $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];
        $platform =  $userData[2];

        $input = $request->all();
        $avlBal = 0;
        $status = 0;
        $reduceAmount = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'is_wallet_use' => 'nullable',
            'payment_method' => 'nullable',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $query = GiftCart::where(['user_id'=>$userId, 'id'=>$input['cart_id']])->first();

            if ($query) {
                $validationError = false;

                if ($query->order_type == 3) {

                    if (!isset($input['is_wallet_use'])) {
                        $validationError = true;
                        $status = 0;
                        $message = 'Wallet user field is required.';
                    }

                    if ($input['payment_method'] == 'Card') {

                        if (!isset($input['transaction_id'])) {
                            $validationError = true;
                            $status = 0;
                            $message = 'Transaction Id field is required.';
                        }
                    }
                }

                if (isset($input['is_wallet_use']) && $input['is_wallet_use'] == 'Yes') {
                    $totalBalCR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'CR'])->sum('amount');
                    $totalBalDR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'DR'])->sum('amount');
                    $avlBal = $totalBalCR-$totalBalDR;

                    if ($avlBal > 0) {

                        if ($query->shipping_charges > $avlBal) {
                            $validationError = true;
                            $status = 0;
                            $message = 'Your waller balance is low.';
                        }

                    } else {
                        $validationError = true;
                        $status = 0;
                        $message = 'Your waller is empty.';
                    }
                }

                if ($validationError) {
                    $response['status'] = $status;
                    $response['message'] = $message;
                    return response()->json($response, 200);

                } else {
                    $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'CR'])->sum('points');
                    $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$userId,'type'=>'DR'])->sum('points');
                    $available_balance = $totalCR-$totalDR;

                    if ($query->points <= $available_balance) {
                        // $giftCartDetail = GiftCart::where(['id'=>$input['cart_id'],'user_id',$userId])->first();
                        $orderdata = new GiftOrder;
                        $orderdata->user_id = $userId;
                        $orderdata->platform = $platform;
                        $orderdata->random_order_id = substr(str_shuffle($str_result), 0, 5);
                        $orderdata->points = $query->points;
                        $orderdata->address_id = $query->address_id;
                        $orderdata->longitude = $query->longitude;
                        $orderdata->latitude = $query->latitude;
                        $orderdata->building_number = $query->building_number;
                        $orderdata->building_name = $query->building_name;
                        $orderdata->landmark = $query->landmark;
                        $orderdata->address = $query->address;
                        $orderdata->address_type = $query->address_type;
                        $orderdata->contact_name = $query->contact_name;
                        $orderdata->contact_number = $query->contact_number;

                        if ($query->order_type == 3) {
                            $orderdata->shipping_charges = $query->shipping_charges;
                            $orderdata->tax_amount = $query->tax_amount;
                            $orderdata->taxPercentage = $query->taxPercentage;
                        }
                        $orderdata->order_type = $query->order_type;
                        $orderdata->pick_type = $query->pick_type;
                        $orderdata->pick_datetime = $query->pick_datetime;
                        $orderdata->car_color = $query->car_color;
                        $orderdata->pickup_option = $query->pickup_option;
                        $orderdata->car_number = $query->car_number;
                        $orderdata->car_brand = $query->car_brand;
                        $orderdata->is_wallet_use = $input['is_wallet_use'] ?? 'No';
                        $orderdata->payment_type = $input['payment_method'] ?? null;
                        $orderdata->order_status = 'Pending';
                        $orderdata->wallet_amount_used = $query->wallet_amount_used;

                        if (isset($input['is_wallet_use']) && $input['is_wallet_use'] == 'Yes') {

                            if ($avlBal > 0) {
                                $totalAmount = $query->shipping_charges+$query->tax_amount;

                                if ($avlBal >= $totalAmount) {
                                    $reduceAmount = $totalAmount;

                                } else {
                                    $reduceAmount = $avlBal;
                                }
                            }

                            $orderdata->wallet_amount_used = $reduceAmount;
                        }

                        if ($orderdata->save()) {

                            if ($reduceAmount > 0) {
                                //Debit from wallet balance
                                $walletData = new UserWallets;
                                $walletData->user_id = $userId;
                                $walletData->transaction_type = 'DR';
                                $walletData->amount = $reduceAmount;
                                $walletData->comment = 'Money debit for gift order purchase ORD-'.$orderdata->random_order_id;
                                $walletData->save();
                            }
                            $cartGiftData = GiftCartDetail::where('gift_cart_id',$input['cart_id'])->get();

                            if(count($cartGiftData)) {

                                foreach($cartGiftData as $key => $value) {
                                    $orderdetailData = new GiftOrderDetails;
                                    $orderdetailData->user_id = $userId;
                                    $orderdetailData->gift_order_id = $orderdata->id;
                                    $orderdetailData->gift_id = $value->gift_id;
                                    $orderdetailData->qty = $value->qty;
                                    $orderdetailData->points = $value->points;
                                    $orderdetailData->gift_varient_id = $value->gift_varient_id;
                                    $orderdetailData->varient_name = $value->varient_name;

                                    if ($orderdetailData->save()) {
                                        $cartToppingData = GiftCartTopping::where(['gift_cart_detail_id'=>$value->id])->get();

                                        if (count($cartToppingData)) {

                                            foreach ($cartToppingData as $k => $v) {
                                                $orderToppingData = new GiftOrderToppings();
                                                $orderToppingData->order_detail_id = $orderdetailData->id;
                                                $orderToppingData->gift_attribute_values_id = $v->gift_attribute_values_id;
                                                $orderToppingData->attributes_lang_id = $v->attributes_lang_id;
                                                $orderToppingData->attribute_value_lang_id = $v->attribute_value_lang_id;
                                                $orderToppingData->attribute_value_name = $v->attribute_value_name;
                                                $orderToppingData->points = $v->points;
                                                $orderToppingData->save();
                                            }
                                        }
                                    }
                                }
                            }

                            //insert In KiloPointsDB
                            $userKiloPointsNewDB = new UserKiloPoints;
                            $userKiloPointsNewDB->order_id = $orderdata->id;
                            $userKiloPointsNewDB->user_id = $userId;
                            $userKiloPointsNewDB->points = $query->points;
                            $userKiloPointsNewDB->type = 'DR';
                            $userKiloPointsNewDB->comment = 'Gift #'.$orderdata->random_order_id.' Order Placed.';
                            $userKiloPointsNewDB->setConnection('mysql2');
                            $userKiloPointsNewDB->save();

                            $notificationData = new GiftNotification;
                            $notificationData->user_id = $userId;
                            $notificationData->order_id = $orderdata->id;
                            $notificationData->user_type = 2;
                            $notificationData->notification_type = 3;
                            $notificationData->notification_for = 'Gift Buy';
                            $notificationData->title = 'Order Placed';
                            $notificationData->message = 'Gift #'.$orderdata->random_order_id.' Order Placed Successfully.';
                            $notificationData ->save();

                            GiftCart::where('id',$input['cart_id'])->delete();
                            GiftCartDetail::where('gift_cart_id',$input['cart_id'])->delete();

                            $data = array();
                            $data['order_id'] = $orderdata->id;
                            $data['random_order_id'] = $orderdata->random_order_id;
                            $response['status'] = 1;
                            $response['message'] = 'Order Placed Successfully.';
                            $response['data'] = $data;
                            send_notification(1, $userId, 'Order Placed', array('title'=>'Order Placed Successfully','message'=>$notificationData->message,'type'=>'Gift','key'=>'event'));

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Error Occured.';
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Your available balance is low.';
                    }
                    return response()->json($response, 200);
                } 

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                return response()->json($response, 200);
            }
        }
    }

    public function gift_cart_destroy(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $query = GiftCart::where('user_id', $userId)->first();

        if ($query) {
            GiftCart::where('user_id', $userId)->delete();
            GiftCartDetail::where('user_id', $userId)->delete();
            GiftCartTopping::where(['gift_cart_id'=>$query->id])->delete();

            $response['status'] = 1;
            $response['message'] = 'Your cart removed successfully.';
            return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['message'] = 'Gift Cart Empty.';
            return response()->json($response, 200);
        }
    }

    public function gift_order_list(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {            
            $order_type = $input['type'];

            $data = GiftOrder::where(function($query) use ($userId){
                $query->where('user_id', $userId);
            })
            ->where(function($query) use ($order_type) {

                if ($order_type != 'All') {
                    $query->where('order_status', $order_type);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

            if (count($data)) {
                $response['status'] = 1;
                $response['data'] = $data;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'No order found.';
                return response()->json($response, 200);
            }
        }
    }

    public function gift_order_detail(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $totalQty = 0;
        $totalKiloPoints = 0;
        $address_id = '';
        $order_type = '';
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = GiftOrder::where(['id'=>$input['order_id']])->first();

            if ($query) {
                $reasion = '';

                if ($query->cancel_reasion_id && !empty($query->cancel_reasion_id)) {
                    $reasionData = OrderCancelReasions::where(['id'=>$query->cancel_reasion_id])->first();

                    if ($reasionData) {
                        $reasion = $reasionData->reasion;
                    }
                }
                $getOrderProducts = GiftOrderDetails::where(['gift_order_id'=>$query->id])->get();

                foreach ($getOrderProducts as $key => $value) {
                    $productDetail = Gift::where(['id'=>$value->gift_id])->first();
                    $totalQty += $value->qty;
                    $totalKiloPoints += $value->points;
                    $value->products = $productDetail;

                    $product_toppings = GiftOrderToppings::select('gift_attribute_values_id','attribute_value_name','points')->where('order_detail_id',$value->id)->get();

                    if (count($product_toppings)) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }
                    $value->products->toppings = $product_toppings;
                }
                $response['status'] = 1;
                $response['data']['items'] = $getOrderProducts;
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['order_type'] =  $query->order_type;
                $response['data']['reasion'] =  $reasion;
                $response['data']['discount_code'] = $query->discount_code;
                $response['data']['discount_amount'] = $query->discount_amount;
                $response['data']['shipping_charges'] = $query->shipping_charges;
                $response['data']['tax_amount'] = $query->tax_amount;
                $response['data']['totalKiloPoints'] =  (string)$totalKiloPoints;
                $response['data']['order_status'] =  $query->order_status;
                $response['data']['address_id'] =  $query->address_id;
                $response['data']['address'] =  $query->address;
                $response['data']['landmark'] =  $query->landmark;
                $response['data']['building_name'] =  $query->building_name;
                $response['data']['building_number'] =  $query->building_number;
                $response['data']['address_type'] =  $query->address_type;
                $response['data']['created_at'] =  $query->created_at;
                $response['data']['is_rate'] =  $query->is_rate;
                $response['data']['order_type'] =  $query->order_type;
                $response['data']['pick_type'] =  $query->pick_type;
                $response['data']['pick_datetime'] =  $query->pick_datetime;
                $response['data']['pickup_option'] =  $query->pickup_option;
                $response['data']['car_color'] =  $query->car_color;
                $response['data']['car_number'] =  $query->car_number;
                $response['data']['car_brand'] =  $query->car_brand;
                $response['data']['payment_type'] =  $query->payment_type;
                $response['data']['is_arrived'] =  $query->is_arrived;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'No order found.';
                return response()->json($response, 200);
            }
        }
    }

    public function cancel_gift_order(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'reasion_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $order_detail = GiftOrder::where(['id'=>$input['order_id'], 'user_id'=>$userId])->first();

            if ($order_detail) {

                if ($order_detail->order_status == 'Pending') {
                    $order_detail->order_status = 'Cancel';
                    $order_detail->cancel_reasion_id = $input['reasion_id'] ?? '';
                    $order_detail->cancel_by = $userId;

                    if ($order_detail->save()) {
                        //CR in wallet

                        if ($order_detail->payment_type != 'Cash') {
                            $walletData = new UserWallets;
                            $walletData->user_id = $userId;
                            $walletData->transaction_type = 'CR';
                            $walletData->amount = $order_detail->shipping_charges + $order_detail->tax_amount;
                            $walletData->comment = 'Your gift order cancelled, ORD-'.$order_detail->id;
                            $walletData->save();
                        }

                        //insert In KiloPointsDB
                        $userKiloPointsNewDB = new UserKiloPoints;
                        $userKiloPointsNewDB->order_id = $order_detail->id;
                        $userKiloPointsNewDB->user_id = $userId;
                        $userKiloPointsNewDB->points = $order_detail->points;
                        $userKiloPointsNewDB->type = 'CR';
                        $userKiloPointsNewDB->is_refund = 'Yes';
                        $userKiloPointsNewDB->comment = 'Gift #'.$order_detail->random_order_id.' Order cancelled by you.';
                        $userKiloPointsNewDB->setConnection('mysql2');
                        $userKiloPointsNewDB->save();

                        //Notification data
                        $notificationData = new GiftNotification;
                        $notificationData->user_id = $userId;
                        $notificationData->order_id = $input['order_id'];
                        $notificationData->user_type = 2;
                        $notificationData->notification_type = 3;
                        $notificationData->notification_for = 'Gift-Order-Cancel';
                        $notificationData->title = 'Gift Order Cancel';
                        $notificationData->message = 'Gift #'.$order_detail->random_order_id.' is cancelled by you.';
                        $notificationData->save();
                        send_notification(1, $userId, 'Gift Order Cancel', array('title'=>'Gift Order Cancel','message'=>$notificationData->message,'type'=>'Gift','key'=>'event'));
                        //End Notification
                        $response['status'] = 1;
                        $response['message'] = 'Your order cancelled successfully.';
                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Error Occured.";
                    }

                } else {

                    if ($order_detail->order_status == 'Cancel') {
                        $response['status'] = 0;
                        $response['message'] = "This order is already cancelled.";
                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 0;
                        $response['message'] = "You can't cancel this order now.";
                        return response()->json($response, 200);
                    }
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Order not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function reorder_gift(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $input = $request->all();
        $totalAmount = 0;

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $order_detail = GiftOrder::where(['id'=>$input['order_id'], 'user_id'=>$userId])->first();

            if ($order_detail) {
                //old cart destroy for the user
                GiftCart::where('user_id', $userId)->delete();
                GiftCartDetail::where('user_id', $userId)->delete();

                //New gift cart created here only
                $gift_cart = new GiftCart();
                $gift_cart->user_id = $userId;
                $gift_cart->points = $order_detail->points;
                $gift_cart->address_id = $order_detail->address_id;
                $gift_cart->latitude = $order_detail->latitude;
                $gift_cart->longitude = $order_detail->longitude;
                $gift_cart->building_number = $order_detail->building_number;
                $gift_cart->building_name = $order_detail->building_name;
                $gift_cart->landmark = $order_detail->landmark;
                $gift_cart->address = $order_detail->address;
                $gift_cart->address_type = $order_detail->address_type;
                $gift_cart->shipping_charges = $order_detail->shipping_charges;
                $gift_cart->contact_name = $order_detail->contact_name;
                $gift_cart->contact_number = $order_detail->contact_number;

                if ($gift_cart->save()) {
                    $getOrderGifts = GiftOrderDetails::where(['gift_order_id'=>$order_detail->id])->get();
                    //New record add when cart is empty

                    if (count($getOrderGifts)) {

                        foreach ($getOrderGifts as $key => $value) {
                            $inputCartDetail = new GiftCartDetail();
                            $inputCartDetail->user_id = $userId;
                            $inputCartDetail->gift_cart_id = $gift_cart->id;
                            $inputCartDetail->gift_id = $value->gift_id;
                            $inputCartDetail->qty = $value->qty;
                            $inputCartDetail->points = $value->points;
                            $inputCartDetail->gift_varient_id = $value->gift_varient_id;
                            $inputCartDetail->varient_name = $value->varient_name;
                            $inputCartDetail->save();
                        }
                    }
                    $response['status'] = 1;
                    $response['message'] = 'Your order is added in cart.';

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured.';
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Order not found.';
            }
            return response()->json($response, 200);
        }
    }

    public function gift_customer_arrive(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $totalQty = 0;
        $totalAmount = 0.00;
        $totalKiloPoints = 0;
        $address_id = '';
        $order_type = '';
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = GiftOrder::where(['id'=>$input['order_id']])->first();

            if ($query) {
                GiftOrder::where(['id'=>$input['order_id']])->update(['is_arrived' => 1]);

                //Panel Notification data
                $panelNotificationData = new PanelNotifications;
                $panelNotificationData->user_id = 1;
                $panelNotificationData->order_id = $input['order_id'];
                $panelNotificationData->user_type = 1;
                $panelNotificationData->notification_for = 'Customer-Arrived';
                $panelNotificationData->notification_type = 3;
                $panelNotificationData->title = 'Customer Arrived';
                $panelNotificationData->message = 'Customer has been arrived on store to get their gift, Order No. #'.$query->random_order_id;
                
                if ($panelNotificationData->save()) {
                    $panelData = PanelNotifications::select('panel_notifications.*','orders.random_order_id')->leftJoin('orders','orders.id','=','panel_notifications.order_id');
                    $adminCount = 0;
                    $restroCount = 0;

                    $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();
                    $restroCount = $adminCount;

                    /*Admin Notification*/
                    $curl_admin = curl_init();

                    curl_setopt_array($curl_admin, array(
                      CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount,\n  \"restroCount\":$restroCount\n}\n",
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",
                        "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/0",
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
                $response['message'] = 'Customer Arrived.';
                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid order id.';
                return response()->json($response, 200);
            }
        }
    }

    public function notificationList(Request $request){
        $inputData = $request->all();
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $notificationData = GiftNotification::where(['user_id'=>$userId])->orderBy('id', 'desc')->get();

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
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $notificationData = GiftNotification::where(['id'=>$inputData['notification_id'], 'user_id'=>$userId])->first();

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
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $notificationData = GiftNotification::where(['user_id'=>$userId])->delete();
        $response['status'] = 1;
        $response['message'] = 'Notification removed successfully.';
        return response()->json($response, 200);
    }

    public function readAllNotification(Request $request){
        $inputData = $request->all();
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $updateData = [
            'is_read'=>1,
        ];
        GiftNotification::where(['user_id'=>$userId])->update($updateData);
        $response['status'] = 1;
        $response['message'] = 'Notification all read successfully.';
        return response()->json($response, 200);
    }

    public function readNotification(Request $request){
        $inputData = $request->all();
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $notificationData = GiftNotification::where(['id'=>$inputData['notification_id'], 'user_id'=>$userId])->first();

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

    public function rate_gift(Request $request){
        /*$userData = auth()->user();
        $userId =  $userData->id;*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $getOrderProducts = GiftOrderDetails::where(['gift_order_details.gift_order_id'=>$input['order_id'], 'gift_order_details.user_id'=>$userId])->get();

            if (count($getOrderProducts)) {
                $checkAlreadyRated = GiftRate::where(['order_id'=>$input['order_id'], 'user_id'=>$userId])->first();

                if ($checkAlreadyRated) {
                    $response['status'] = 0;
                    $response['message'] = 'You already rated this order.';
                    return response()->json($response, 200);

                } else {

                    foreach ($getOrderProducts as $key => $value) {
                        $data = new GiftRate();
                        $data->order_id = $input['order_id'];
                        $data->user_id = $userId;
                        $data->gift_id = $value->gift_id;
                        $data->rating = $input['rating'];

                        if (isset($input['reveiw'])) {
                            $data->reveiw = $input['reveiw'];
                        }
                        $data->save();
                    }

                    $response['status'] = 1;
                    $response['message'] = 'Thank you for your valuable feedback.';
                    return response()->json($response, 200);
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'No order found.';
                return response()->json($response, 200);
            }
        }
    }

    public function checkGiftAvailbility(Request $request){
        $serachData = $request->all();
        /*$userData = auth()->user();*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $validator = Validator::make($request->all(), [
            'gift_id' => 'required',
            'attributes_lang_id' => 'required',
            'attribute_value_lang_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $getDish = Gift::where(['id'=>$serachData['gift_id']])->first();

            if ($getDish) {
                $attributesLangIds = explode(",", $serachData['attributes_lang_id']);
                $attributesValueLangIds = explode(",", $serachData['attribute_value_lang_id']);
                $countIds = count($attributesValueLangIds);
                $in_cart = 0;

                /*$product_attributes = Products::select('products.*')->where(['product_id' => $serachData['product_id']])->join('product_attributes', 'products.id', '=', 'product_attributes.product_id')->get()->toArray();*/

                $product_attributes = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id', 'gift_attribute_values.attributes_lang_id','gift_attribute_values.gift_attributes_id', DB::raw('count(gift_attribute_values.gift_attributes_id) as total'), DB::raw('group_concat(gift_attribute_values.id) as gift_attribute_values_id'))->where(['gift_id' => $serachData['gift_id']])->whereIn('gift_attribute_values.attribute_value_lang_id', $attributesValueLangIds)->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->groupBy('gift_attribute_values.gift_attributes_id')->having('total', $countIds)->first();

                if ($product_attributes) {
                    $gift_attribute_values_id = explode(",", $product_attributes->gift_attribute_values_id);

                    if ($userId) {
                        $gift_cart = GiftCart::where('user_id',$userId)->first();

                        if ($gift_cart) {
                            $checkGiftAddedInCart = GiftCartTopping::where('gift_cart_id',$gift_cart->id)->whereIn('gift_attribute_values_id', $gift_attribute_values_id)->get();

                            if (count($checkGiftAddedInCart)) {
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

                /*$data['product_attributes'] = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id', 'gift_attribute_values.attributes_lang_id','gift_attribute_values.gift_attributes_id', DB::raw('count(gift_attribute_values.gift_attributes_id) as total'), DB::raw('group_concat(gift_attribute_values.id) as gift_attribute_values_id'))->where(['gift_id' => $serachData['gift_id']])->whereIn('gift_attribute_values.attribute_value_lang_id', $attributesValueLangIds)->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->groupBy('gift_attribute_values.gift_attributes_id')->get();*/

                // $data['product_attributes'] = GiftProductAttributes::select('gift_attributes.*')->where(['gift_id' => $serachData['gift_id']])->get();

                // if ($data['product_attributes']) {

                //     foreach ($data['product_attributes'] as $k => $v) {
                //         $v->selected_attr_values = GiftProductAttributeValues::where(['gift_attributes_id' => $v->id])->get();
                //     }
                // }

                // $response['status'] = 1;
                // $response['data'] = $data;

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid item selection.';
            }
            return response()->json($response, 200);
        }
    }

    public function checkGiftAvailbilityNew(Request $request){
        $serachData = $request->all();
        /*$userData = auth()->user();*/
        $userData = giftAuthUserId();
        $userId =  $userData[0];

        $validator = Validator::make($request->all(), [
            'gift_id' => 'required',
            'attributes_lang_id' => 'required',
            'attribute_value_lang_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $getDish = Gift::where(['id'=>$serachData['gift_id']])->first();

            if ($getDish) {
                $attributesLangIds = explode(",", $serachData['attributes_lang_id']);
                $attributesValueLangIds = explode(",", $serachData['attribute_value_lang_id']);
                $countIds = count($attributesValueLangIds);
                $in_cart = 0;

                /*$product_attributes = Products::select('products.*')->where(['product_id' => $serachData['product_id']])->join('product_attributes', 'products.id', '=', 'product_attributes.product_id')->get()->toArray();*/

                /*$product_attributes = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id', 'gift_attribute_values.attributes_lang_id','gift_attribute_values.gift_attributes_id', DB::raw('count(gift_attribute_values.gift_attributes_id) as total'), DB::raw('group_concat(gift_attribute_values.id) as gift_attribute_values_id'))->where(['gift_id' => $serachData['gift_id']])->whereIn('gift_attribute_values.attribute_value_lang_id', $attributesValueLangIds)->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->groupBy('gift_attribute_values.gift_attributes_id')->having('total', $countIds)->first();

                if ($product_attributes) {
                    $gift_attribute_values_id = explode(",", $product_attributes->gift_attribute_values_id);

                    if ($userId) {
                        $gift_cart = GiftCart::where('user_id',$userId)->first();

                        if ($gift_cart) {
                            $checkGiftAddedInCart = GiftCartTopping::where('gift_cart_id',$gift_cart->id)->whereIn('gift_attribute_values_id', $gift_attribute_values_id)->get();

                            if (count($checkGiftAddedInCart)) {
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
                }*/
                $data['product_attributes'] = GiftProductAttributeValues::select('gift_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id', 'gift_attribute_values.attributes_lang_id','gift_attribute_values.gift_attributes_id', DB::raw('count(gift_attribute_values.gift_attributes_id) as total'), DB::raw('group_concat(gift_attribute_values.id) as gift_attribute_values_id'))->where(['gift_id' => $serachData['gift_id']])->where('gift_attribute_values.attribute_value_lang_id','!=', $serachData['attribute_value_lang_id'])->where('gift_attribute_values.attributes_lang_id','!=', $serachData['attributes_lang_id'])->join('gift_attributes', 'gift_attributes.id', '=', 'gift_attribute_values.gift_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'gift_attribute_values.attribute_value_lang_id')->groupBy('gift_attribute_values.attribute_value_lang_id')->get();
                // dd($data['product_attributes']->toArray());

                // $data['product_attributes'] = GiftProductAttributes::select('gift_attributes.*')->where(['gift_id' => $serachData['gift_id']])->get();

                /*if ($data['product_attributes']) {

                    foreach ($data['product_attributes'] as $k => $v) {
                        $v->selected_attr_values = GiftProductAttributeValues::where(['gift_attributes_id' => $v->id])->get();
                    }
                }*/

                $response['status'] = 1;
                $response['data'] = $data;

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid item selection.';
            }
            return response()->json($response, 200);
        }
    }

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
                'platform' => 'required|in:KP,Shopya',
                'callback_url' => 'required',
            ],$message);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {  
            $user = User::on('mysql2')->where(['country_code'=>$input['country_code'],'mobile'=>$input['mobile'],'type'=>0])->first();

            if (isset($user)) {
                $totalCR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user->id,'type'=>'CR'])->sum('points');
                $totalDR = UserKiloPoints::on('mysql2')->where(['user_id'=>$user->id,'type'=>'DR'])->sum('points');
                $available_balance = $totalCR-$totalDR;
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

                //Update User Data
                $newUser = User::on('mysql2')->updateOrCreate([
                    //Add unique field combo to match here
                    //For example, perhaps you only want one entry per user:
                    'id' => $user->id
                ],$newuserUpdateData);

                //Update Platform Data
                $newUserPlatform = GiftUserPlatforms::updateOrCreate([
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
                $data['available_kp'] = $available_balance;
                $response['status'] = 1;
                $response['message'] ='User is already registered';
                $response['data'] = $data;
                return response()->json($response, 200);

            } else {
                $userNewDB = new User;
                $userNewDB->name = $input['name'] ?? null;
                $userNewDB->email = $input['email'] ?? null;
                $userNewDB->country_code = $input['country_code'];
                $userNewDB->mobile = $input['mobile'];
                $userNewDB->status = 1;
                $userNewDB->type = 0;
                $userNewDB->setConnection('mysql2');

                if ($userNewDB->save()) {
                    $randomPassword = Str::orderedUuid();
                    $tokenData = encryptPass($userNewDB->id.'|~@#|'.$input['mobile'].'|~@#|'.$input['platform']);
                    $secretKey = encryptPass($userNewDB->id.'|~@#|'.$input['mobile'].'|~@#|'.$input['platform'].'|~@#|'.$randomPassword);

                    $userPlatformData = new GiftUserPlatforms;
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
                    $data['available_kp'] = 0;
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

    public function updateKPInGift(Request $request)
    {
        $input =  $request->all();
        $validator = Validator::make($input, [ 
            'points' => 'required',
            'platform' => 'required',
            'order_id' => 'required',
            'type' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $userData = giftAuthUserId();
            $userId =  $userData[0];

            $user = User::on('mysql2')->where(['id'=>$userId])->first();

            if (isset($user)) {
                $userKiloPointsNewDB = new UserKiloPoints;
                $userKiloPointsNewDB->order_id = $input['order_id'];
                $userKiloPointsNewDB->user_id = $userId;
                $userKiloPointsNewDB->points = $input['points'];
                $userKiloPointsNewDB->comment = $input['comment'];
                $userKiloPointsNewDB->type = $input['type'];
                $userKiloPointsNewDB->platform = $input['platform'];
                $userKiloPointsNewDB->is_refund = $input['is_refund'] ?? 'No';
                $userKiloPointsNewDB->is_kp_transfer = $input['is_kp_transfer'] ?? 'Yes';
                $userKiloPointsNewDB->setConnection('mysql2');
                
                if ($userKiloPointsNewDB->save()) {
                    $response['status'] = 1;
                    $response['message'] ='Points has been updated.';
                    $response['data'] = array();
                    return response()->json($response, 200);

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Technical error, please try again.';
                    return response()->json($response, 200);
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'User not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function updateKPOrderStatus(Request $request)
    {
        $input =  $request->all();
        $validator = Validator::make($input, [
            'platform' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $userData = giftAuthUserId();
            $userId =  $userData[0];

            $user = User::on('mysql2')->where(['id'=>$userId])->first();

            if (isset($user)) {
                $updateOrderKP = [
                    'is_kp_transfer'=>'Yes',
                ];
                UserKiloPoints::on('mysql2')->where(['user_id'=>$userId, 'order_id'=>$input['order_id'], 'platform'=>$input['platform']])->update($updateOrderKP);
                $response['message'] ='KP status has been updated.';
                $response['data'] = array();
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'User not found.';
                return response()->json($response, 200);
            }
        }
    }
}