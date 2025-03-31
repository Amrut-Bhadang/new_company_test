<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Models\Category;
use App\Models\Gift;
use App\Models\Products;
use App\Models\Media;
use App\Models\UsersAddress;
use JWTAuth;
use App\Models\UserOtp;
use App\Models\Restaurant;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Topping;
use App\Models\CartParent;
use App\Models\Orders;
use App\Models\Discount;
use App\Models\OrdersDetails;
use App\Models\OrderToppings;
use App\Models\Notification;
use App\Models\PanelNotifications;
use App\Models\OrderSplitBills;
use App\Models\CartSplitBills;
use App\Models\CartSplitBillProduct;
use App\Models\OrderSplitBillProduct;
use App\Models\OrderCancelReasions;
use App\Models\UserWallets;
use App\Models\Rating;
use App\Models\ProductAttributeValues;
use App\Models\EmailTemplateLang;
use App\Models\UserKiloPoints;
use Illuminate\Support\Facades\Schema;
use DB;
use App;
use Mail;
class  CartController extends Controller {

    public function __construct() {       
        $this->middleware('auth:api');
    }

    public function add_to_cart(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $product_change_array = (object)[];

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'qty' => 'required',
            'amount' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            $errors 	=	$validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);    

        } else {
            //$input['latitude'] = $input['latitude'] ??  ''; 
            //$input['longitude'] = $input['longitude'] ??  ''; 

            //dd($userData->id);
            $productDetail = Products::select('id','restaurant_id','price','points','extra_kilopoints','product_for')->where(['products.id'=>$input['product_id']])->first();

            if ($productDetail) {
                // $cart = Cart::select('cart.*','products.restaurant_id')->where(['user_id'=>$userId])->join('products','products.id','=','cart.product_id')->get();
                $cart_parent = CartParent::where('user_id',$userId)->first();
                 //dd($cart_parent);
                if ($cart_parent) {
                    $checkSameRestro = Cart::select('cart.*','products.restaurant_id')
                    ->where(['user_id'=>$userId, 'products.restaurant_id'=>$productDetail->restaurant_id])
                    ->join('products','products.id','=','cart.product_id')
                    ->get();

                    if (!count($checkSameRestro)) {
                        $response['status'] = 0;
                        $response['message'] = 'Please choose same restaurant to add product in cart.';
                        $response['is_same_restro'] = 0;
                        // return response()->json($response, 200);

                    } else {
                        $checkSameProduct = Cart::select('cart.*','products.restaurant_id')->where(['user_id'=>$userId, 'cart.product_id'=>$input['product_id']])->join('products','products.id','=','cart.product_id')->first();

                        if ($checkSameProduct) {
                                      
                            if (isset($input['cart_id'])) {

                                if ($input['qty'] == 0) {
                                   Cart::where('user_id',$userId)->where('id',$input['cart_id'])->delete();
                                   CartDetail::where('cart_id',$input['cart_id'])->delete();
                                   $response['status'] = 1;
                                   $response['message'] = 'Your product removed from cart.';

                                   //Update Cart Parent
                                    $getCartTotalAmount = Cart::where(['user_id'=>$userId])->sum('amount');

                                    $inpCartParent = [
                                        'amount'=>$getCartTotalAmount+$input['amount'],
                                        'org_amount'=>$getCartTotalAmount+$input['amount'],
                                    ];
                                    // updated by yogi
                                    $cardItem =  Cart::where('user_id',$userId)->get();

                                    if(!count($cardItem)){
                                        CartParent::where('user_id',$userId)->delete();

                                    } else {
                                        $cartParentData = CartParent::where('user_id',$userId)->firstOrFail();
                                        $cartParentData->update($inpCartParent);
                                    }

                                    $productTotalQty = Cart::where('user_id',$userId)->where('product_id',$productDetail->id)->sum('qty');
                                    $product_change_array = [
                                        'qty'=>$productTotalQty,
                                        'product_id'=>$productDetail->id
                                    ];

                                } else {
                                    //Update Cart Parent
                                    $getCartTotalAmount =  Cart::where(['user_id'=>$userId])->where('id','!=',$input['cart_id'])->sum('amount');
                                    $cartParentData = CartParent::where('user_id',$userId)->firstOrFail();
                                    $inpCartParent = [
                                        'amount'=>$getCartTotalAmount+$input['amount'],
                                        'org_amount'=>$getCartTotalAmount+$input['amount'],
                                    ];

                                    //Discount Calculation At the end shifted

                                    /*if ($cartParentData->discount_code) {
                                        $discountData = Discount::where(['discount_code'=>$cartParentData->discount_code])->first();
                                        $cartTotalAmount = $getCartTotalAmount+$input['amount'];

                                        if ($discountData) {
                                            $discount_amount = (($cartTotalAmount*$discountData->percentage)/100);

                                            if ($discount_amount > $discountData->max_discount_amount) {
                                                $discount_amount = $discountData->max_discount_amount;
                                            }

                                            $inpCartParent['discount_amount'] = $discount_amount;
                                            $inpCartParent['amount'] = $cartTotalAmount-$discount_amount;

                                        } else {
                                            $inpCartParent['discount_code'] = null;
                                            $inpCartParent['discount_percent'] = null;
                                            $inpCartParent['discount_amount'] = null;
                                        }
                                    }*/

                                    // dd($inpCartParent);

                                    if ($cartParentData->update($inpCartParent)) {
                                        //Update Cart Qty
                                        // $totalPoints = ($productDetail->points + $productDetail->extra_kilopoints) * $input['qty'];
                                        $totalPoints = ($productDetail->points) * $input['qty'];

                                        $inp = [
                                            'qty'=>$input['qty'],
                                            'amount'=>$input['amount'],
                                            'points'=>$totalPoints,
                                        ];

                                        $Cart = Cart::where('user_id',$userId)->where('id',$input['cart_id'])->first();
                                        
                                        if ($Cart) {
                                            $Cart->update($inp);
                                            $productTotalQty = Cart::where('user_id',$userId)->where('product_id',$productDetail->id)->sum('qty');

                                            $product_change_array = [
                                                'qty'=>$productTotalQty,
                                                'product_id'=>$productDetail->id
                                            ];
                                            $response['status'] = 1;
                                            $response['message'] = 'Your product qty updated successfully.';

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
                                //Update Cart Parent
                                $getCartTotalAmount =  Cart::where(['user_id'=>$userId])->sum('amount');

                                $inpCartParent = [
                                    'amount'=>$getCartTotalAmount+$input['amount'],
                                    'org_amount'=>$getCartTotalAmount+$input['amount'],
                                ];

                                $cartParentData = CartParent::where('user_id',$userId)->first();

                                if ($cartParentData->update($inpCartParent)) {
                                    //New record add when cart is empty
                                    $data = new Cart();
                                    $data->user_id = $userId;
                                    $data->parent_cart_id = $cartParentData->id;
                                    $data->product_id = $input['product_id'];
                                    $data->qty = $input['qty'];
                                    $data->amount = $input['amount'];
                                    $data->product_price = $productDetail->price;
                                    // $data->points = $productDetail->points;
                                    // $data->points = ($productDetail->points + $productDetail->extra_kilopoints) * $input['qty'];
                                    $data->points = ($productDetail->points) * $input['qty'];

                                    if ($data->save()) {

                                        if (isset($input['product_attribute_values_id']) && !empty($input['product_attribute_values_id'])) {
                                            $attributeValues = explode(",", $input['product_attribute_values_id']);

                                            foreach ($attributeValues as $key => $value) {
                                                $attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id','product_attribute_values.attributes_lang_id')->where(['product_attribute_values.id' => $value])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->first();

                                                if ($attributeValues) {
                                                    $cartDetailData = new CartDetail();
                                                    $cartDetailData->parent_cart_id = $cartParentData->id;
                                                    $cartDetailData->cart_id = $data->id;
                                                    $cartDetailData->product_attribute_values_id = $value;
                                                    $cartDetailData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                                    $cartDetailData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                                    $cartDetailData->attribute_value_name = $attributeValues->attribute_value_name;
                                                    $cartDetailData->price = $attributeValues->price;
                                                    $cartDetailData->save();
                                                }
                                            }
                                        }

                                        /*if (isset($input['dish_topping_id']) && !empty($input['dish_topping_id'])) {
                                            $dish_toppings = explode(",", $input['dish_topping_id']);

                                            foreach ($dish_toppings as $key => $value) {
                                                $toppingDetail = Topping::where(['id'=>$value])->first();

                                                if ($toppingDetail) {
                                                    $cartDetailData = new CartDetail();
                                                    $cartDetailData->parent_cart_id = $cartParentData->id;
                                                    $cartDetailData->cart_id = $data->id;
                                                    $cartDetailData->dish_topping_id = $value;
                                                    $cartDetailData->topping_name = $toppingDetail->topping_name;
                                                    $cartDetailData->price = $toppingDetail->price;
                                                    $cartDetailData->save();
                                                }
                                            }
                                        }*/
                                        $productTotalQty = Cart::where('user_id',$userId)->where('product_id',$productDetail->id)->sum('qty');

                                        $product_change_array = [
                                            'qty'=>$productTotalQty,
                                            'product_id'=>$productDetail->id
                                        ];

                                        $response['status'] = 1;
                                        $response['message'] = 'Your product added in cart.';
                                        // return response()->json($response, 200);

                                    } else {
                                        $response['status'] = 0;
                                        $response['message'] = 'Error Occured.';
                                        // return response()->json($response, 200);
                                    }

                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Error Occured.';
                                }
                            }

                        } else {
                            //Update Cart Parent
                            $getCartTotalAmount =  Cart::where(['user_id'=>$userId])->sum('amount');

                            $inpCartParent = [
                                'amount'=>$getCartTotalAmount+$input['amount'],
                                'org_amount'=>$getCartTotalAmount+$input['amount'],
                            ];

                            $cartParentData = CartParent::where('user_id',$userId)->firstOrFail();

                            if ($cartParentData->update($inpCartParent)) {
                                //New record add when cart is empty
                                $data = new Cart();
                                $data->user_id = $userId;
                                $data->parent_cart_id = $cartParentData->id;
                                $data->product_id = $input['product_id'];
                                $data->qty = $input['qty'];
                                $data->amount = $input['amount'];
                                $data->product_price = $productDetail->price;
                                // $data->points = $productDetail->points;
                                // $data->points = ($productDetail->points + $productDetail->extra_kilopoints) * $input['qty'];
                                $data->points = ($productDetail->points) * $input['qty'];

                                if ($data->save()) {

                                    /*if (isset($input['dish_topping_id']) && !empty($input['dish_topping_id'])) {
                                        $dish_toppings = explode(",", $input['dish_topping_id']);

                                        foreach ($dish_toppings as $key => $value) {
                                            $toppingDetail = Topping::where(['id'=>$value])->first();

                                            if ($toppingDetail) {
                                                $cartDetailData = new CartDetail();
                                                $cartDetailData->parent_cart_id = $cartParentData->id;
                                                $cartDetailData->cart_id = $data->id;
                                                $cartDetailData->dish_topping_id = $value;
                                                $cartDetailData->topping_name = $toppingDetail->topping_name;
                                                $cartDetailData->price = $toppingDetail->price;
                                                $cartDetailData->save();
                                            }
                                        }
                                    }*/

                                    if (isset($input['product_attribute_values_id']) && !empty($input['product_attribute_values_id'])) {
                                        $attributeValues = explode(",", $input['product_attribute_values_id']);

                                        foreach ($attributeValues as $key => $value) {
                                            $attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id','product_attribute_values.attributes_lang_id')->where(['product_attribute_values.id' => $value])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->first();

                                            if ($attributeValues) {
                                                $cartDetailData = new CartDetail();
                                                $cartDetailData->parent_cart_id = $cartParentData->id;
                                                $cartDetailData->cart_id = $data->id;
                                                $cartDetailData->product_attribute_values_id = $value;
                                                $cartDetailData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                                $cartDetailData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                                $cartDetailData->attribute_value_name = $attributeValues->attribute_value_name;
                                                $cartDetailData->price = $attributeValues->price;
                                                $cartDetailData->save();
                                            }
                                        }
                                    }

                                    $productTotalQty = Cart::where('user_id',$userId)->where('product_id',$productDetail->id)->sum('qty');

                                    $product_change_array = [
                                        'qty'=>$productTotalQty,
                                        'product_id'=>$productDetail->id
                                    ];

                                    $response['status'] = 1;
                                    $response['message'] = 'Your product added in cart.';
                                    // return response()->json($response, 200);

                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Error Occured.';
                                    // return response()->json($response, 200);
                                }

                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Error Occured.';
                            }
                        }
                    }

                } else {         
                    //New cart parent created here only
                    $cartParent = new CartParent();
                    $cartParent->user_id = $userId;
                    $cartParent->amount = $input['amount'];
                    $cartParent->org_amount = $input['amount'];
                    $cartParent->restaurant_id = $productDetail->restaurant_id;

                    if ($cartParent->save()) {
                        //New record add when cart is empty
                        $data = new Cart();
                        $data->user_id = $userId;
                        $data->parent_cart_id = $cartParent->id;
                        $data->product_id = $input['product_id'];
                        $data->qty = $input['qty'];
                        $data->amount = $input['amount'];
                        $data->product_price = $productDetail->price;
                        // $data->points = $productDetail->points;
                        // $data->points = ($productDetail->points + $productDetail->extra_kilopoints) * $input['qty'];
                        $data->points = ($productDetail->points) * $input['qty'];

                        if ($data->save()) {

                            if (isset($input['product_attribute_values_id']) && !empty($input['product_attribute_values_id'])) {
                                $attributeValues = explode(",", $input['product_attribute_values_id']);

                                foreach ($attributeValues as $key => $value) {
                                    $attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id','product_attribute_values.attributes_lang_id')->where(['product_attribute_values.id' => $value])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->first();

                                    if ($attributeValues) {
                                        $cartDetailData = new CartDetail();
                                        $cartDetailData->parent_cart_id = $cartParent->id;
                                        $cartDetailData->cart_id = $data->id;
                                        $cartDetailData->product_attribute_values_id = $value;
                                        $cartDetailData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                        $cartDetailData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                        $cartDetailData->attribute_value_name = $attributeValues->attribute_value_name;
                                        $cartDetailData->price = $attributeValues->price;
                                        $cartDetailData->save();
                                    }
                                }
                            }

                            $productTotalQty = Cart::where('user_id',$userId)->where('product_id',$productDetail->id)->sum('qty');

                            $product_change_array = [
                                'qty'=>$productTotalQty,
                                'product_id'=>$productDetail->id
                            ];

                            $response['status'] = 1;
                            $response['message'] = 'Your product added in cart.';
                            // return response()->json($response, 200);

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Error Occured.';
                            // return response()->json($response, 200);
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }
                }

                $query = Cart::where('user_id',$userId)->get();
                $totalQty = 0;
                $totalAmount = 0.00;
                $totalKiloPoints = 0;

                if (count($query)) {
                    $getRestro = Cart::select('products.restaurant_id')->where(['user_id'=>$userId])->join('products','products.id','=','cart.product_id')->first();

                    $restroDetail = Restaurant::select('*')->where(['id'=>$getRestro->restaurant_id])->selectRaw("( 6371 * acos( cos( radians(" . $input['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $input['longitude'] . ") ) +  sin( radians(" . $input['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();

                    if ($restroDetail) {
                        $restroDetail['avg_rating'] = number_format(5, 1);
                        $restroDetail['distance'] = number_format($restroDetail->distance, 2).' KM';
                        $productsCategory = Products::select('products.category_id','products.restaurant_id','categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$restroDetail->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->with(array('dishes' => function ($query) use ($restroDetail) {
                            $query->where('products.restaurant_id', $restroDetail->id);
                        }))->get();

                        if ($productsCategory) {

                            foreach ($productsCategory as $k => $v) {
                                $category_arr[] = $v->name;
                            }
                        }


                        if (!empty($category_arr)) {
                            // $value->restroList['category'] = implode(", ", $category_arr);
                            $restroDetail['category'] = implode(", ", $category_arr);;

                        } else {
                            $restroDetail['category'] = '';
                        }

                        // $restroDetail['productsCategory'] = $productsCategory;
                    }

                    foreach ($query as $key => $value) {
                        // $totalAmount +=$data->amount * $data->qty;
                        $productDetail = Products::where(['products.id'=>$value->product_id])->first();
                        $totalAmount += $value->amount;
                        $totalQty += $value->qty;
                        // $totalKiloPoints += ($productDetail->points + $productDetail->extra_kilopoints) * $value->qty;
                        $totalKiloPoints += ($productDetail->points) * $value->qty;

                        $product_toppings = CartDetail::select('product_attribute_values_id','attribute_value_name','price')->where('cart_id',$value->id)->get();

                        if (count($product_toppings)) {
                            $value->is_topping = 1;

                        } else {
                            $value->is_topping = 0;
                        }
                        $value->products = $productDetail;
                        $value->products->toppings = $product_toppings;
                    }

                    $cartParentDetail = CartParent::where(['id'=>$query[0]->parent_cart_id, 'user_id'=>$userId])->first();

                    if ($cartParentDetail->discount_percent) {

                        $discountData = Discount::where(['discount_code'=>$cartParentDetail->discount_code])->first();
                        $cartTotalAmount = $totalAmount;

                        if ($discountData) {
                            $discount_amount = (($cartTotalAmount*$discountData->percentage)/100);

                            if ($cartTotalAmount < $discountData->min_order_amount) {
                                $updateCartParent['discount_code'] = null;
                                $updateCartParent['discount_percent'] = null;
                                $updateCartParent['discount_amount'] = null;
                                $discount_amount = 0;

                            } else {

                                if ($discount_amount > $discountData->max_discount_amount) {
                                    $discount_amount = $discountData->max_discount_amount;
                                }

                                $updateCartParent['discount_amount'] = $discount_amount;
                                $updateCartParent['amount'] = $cartTotalAmount-$discount_amount;
                                $totalAmount = $totalAmount-$discount_amount;
                            }


                        } else {
                            $updateCartParent['discount_code'] = null;
                            $updateCartParent['discount_percent'] = null;
                            $updateCartParent['discount_amount'] = null;
                        }
                        // $discount_amount = (($cartParentDetail->amount*$cartParentDetail->discount_percent)/100);

                        // $updateCartParent = [
                        //     'discount_amount' => $discount_amount,
                        // ];
                        $cartParentDetail->update($updateCartParent);
                    }

                    $response['data']['items'] = $query;
                    $response['data']['restroDetail'] = $restroDetail;
                    $response['data']['parent_cart_id'] = $query[0]->parent_cart_id;
                    $response['data']['totalQty'] =  $totalQty;
                    $response['data']['totalAmount'] =  $totalAmount;
                    $response['data']['org_amount'] =  $cartParentDetail->org_amount;
                    $response['data']['address_id'] =  $cartParentDetail->address_id;
                    $response['data']['order_type'] =  $cartParentDetail->order_type;
                    $response['data']['discount_code'] = $cartParentDetail->discount_code;
                    $response['data']['discount_type'] = $cartParentDetail->discount_type;
                    $response['data']['discount_amount'] =  $cartParentDetail->discount_amount;
                    $response['data']['product_change_array'] = $product_change_array;
                    $response['data']['totalKiloPoints'] =  (string)$totalKiloPoints;

                    $taxPercentage = getCountryTaxByLatLong($input['latitude'], $input['longitude']);
                    $tax_amount = (($totalAmount*$taxPercentage)/100);
                    $response['data']['tax_amount'] =  $tax_amount;
                    $response['data']['taxPercentage'] =  $taxPercentage;

                    if ($cartParentDetail->order_type == 3) {
                        $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$restroDetail->latitude.','.$restroDetail->longitude.'&destinations='. $input['latitude'].','. $input['longitude'].'&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                        $googleData = json_decode($googleData);

                        if ($googleData->rows[0]->elements[0]->status == 'OK'){
                            $metter = $googleData->rows[0]->elements[0]->distance->value ?? 0;  
                            $total_km = ($metter/1000);

                            if ($total_km < 2) {
                                $shipping_charges = 10;

                            } else {
                                // $shipping_charges = $total_km * $restroDetail->delivery_charges_per_km;
                                $shipping_charges = $total_km * getShippingCharge();
                            }
                            // $response['data']['shipping_charges'] = number_format($shipping_charges, 2);
                            $response['data']['shipping_charges'] =   (string)$shipping_charges;

                        } else {
                            $response['data']['shipping_charges'] = '0';
                        }

                    } else {
                        $response['data']['shipping_charges'] = '0';
                    }

                } else {
                    $response['data'] = [];
                    $response['data']['totalQty'] =  $totalQty;
                    $response['data']['totalAmount'] =  $totalAmount;
                    $response['data']['address_id'] =  '';
                    $response['data']['order_type'] =  '';
                    $response['data']['discount_amount'] =  0;
                    $response['data']['shipping_charges'] = '0';
                    $response['data']['product_change_array'] = $product_change_array;
                }
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Product not found.';
                return response()->json($response, 200);
            }
        }
    }

    /*public function add_to_cart(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $validator = Validator::make($request->all(), [
            'product_id'          => 'required',
            'qty'            => 'required',
            'amount'            => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $input = $request->all();
            $cart = Cart::select('*')->where(['user_id'=>$userId,'product_id'=>$input['product_id']])->get();

            if (count($cart)) {

                if ($input['qty'] == 0) {
                   Cart::where('user_id',$userId)->where('product_id',$input['product_id'])->delete();

                } else {
                    $inp = [
                        'qty'=>$input['qty'],
                    ];
                    $Cart = Cart::where('user_id',$userId)->where('product_id',$input['product_id'])->firstOrFail();
                    $Cart->update($inp);
                }
                
            } else {

                if ($input['qty'] == 0) {
                    Cart::where('user_id',$userId)->where('product_id',$input['product_id'])->delete();

                } else {
                    $chef = $this->getChefLatAndLong($input);
                    //return response()->json($chef, 201);
                    if(count($chef)){
                        $cartCount = Cart::where('user_id',$userId)->get();
                        if(count($cartCount)){
                            $input['chef_id'] = $chef;
                            $secondCart = $this->getChefLatAndLong($input);
                            return response()->json($secondCart, 200);

                            if($cartCount[0]->id === $chef['id']){
                                $data = new Cart();
                                $data->user_id          = $userId;
                                $data->product_id    = $input['product_id'];
                                $data->qty    = $input['qty'];
                                $data->amount    = $input['amount'];
                                $data->chef_id  = $chef['id'];
                                $data->save();
                            }else{
                                $response['status'] = 0;
                                $response['message'] = 'You can`t added this dish.';
                                return response()->json($response, 200);
                            }
                            
                        }else{
                            $chef = implode(',',$chef);
                            $data = new Cart();
                            $data->user_id          = $userId;
                            $data->product_id    = $input['product_id'];
                            $data->qty    = $input['qty'];
                            $data->amount    = $input['amount'];
                            $data->chef_id  = $chef;
                            $data->save();  
                        }
                        
                        
                    }else{
                        $response['status'] = 0;
                        $response['message'] = 'You can`t added this dish.';
                        return response()->json($response, 200);
                    }
                }
                
            }
            $sumOfAllQty = Cart::select('qty','amount')->where(['user_id'=>$userId])->get();
            $totalQty = 0;
            $totalAmount = 0.00;

            if (count($sumOfAllQty)) {

                foreach($sumOfAllQty as $data){
                    $totalAmount +=$data->amount * $data->qty;
                    $totalQty += $data->qty;
                }
                $response['status'] = 1;
                $response['message'] = 'Add to cart successfully.';
                $response['totalQty'] =  $totalQty;
                $response['totalAmount'] =  $totalAmount;
                $response['product_qty'] =  $input['qty'];
                return response()->json($response, 200);

            } else {
                $response['status'] = 1;
                $response['message'] = 'Cart Empty.';
                return response()->json($response, 200);
            }
            
        }
    }*/

    public function cart_list(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->get();
        $shipping_charges = '0';
        $total_km = 0;
        $totalQty = 0;
        $totalAmount = 0.00;
        $totalKiloPoints = 0;
        $restaurantDistanceByStrightView = 0;
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

            if (count($query)) {
                $cartParentDetail = CartParent::where(['id'=>$query[0]->parent_cart_id, 'user_id'=>$userId])->first();

                if ($cartParentDetail) {
                    $discount_amount = $cartParentDetail->discount_amount;

                    if (isset($input['order_type'])) {
                        $cartParentDetail->order_type = $input['order_type'];
                        $order_type = $input['order_type'];

                        if ($input['order_type'] == 1) {

                            if (isset($input['dine_option'])) {
                                $cartParentDetail->dine_option = $input['dine_option'];
                            }
                        }

                        if ($input['order_type'] == 2) {

                            if (isset($input['pick_type'])) {
                                $cartParentDetail->pick_type = $input['pick_type'];

                                if ($input['pick_type'] == 'later') {
                                    $cartParentDetail->pick_datetime = $input['pick_datetime'];
                                }
                            } 

                            if (isset($input['pickup_option'])) {
                                $cartParentDetail->pickup_option = $input['pickup_option'];
                                if($input['pickup_option'] == 'Inside-The-Car') {
                                    $cartParentDetail->car_color = $input['car_color'];
                                    $cartParentDetail->car_number = $input['car_number'];
                                    $cartParentDetail->car_brand = $input['car_brand'];
                                }
                            }
                        }
                    }

                    if (isset($input['restaurant_table_id'])) {
                        $cartParentDetail->restaurant_table_id = $input['restaurant_table_id'];
                        $cartParentDetail->table_code = $input['table_code'];
                    }

                    if (isset($input['address_id'])) {
                        $user_address = UsersAddress::where(['id'=>$input['address_id']])->first();

                        if ($user_address) {
                            $cartParentDetail->latitude = $user_address->latitude;
                            $cartParentDetail->longitude = $user_address->longitude;
                            $cartParentDetail->building_number = $user_address->building_number;
                            $cartParentDetail->building_name = $user_address->building_name;
                            $cartParentDetail->landmark = $user_address->landmark;
                            $cartParentDetail->address = $user_address->address;
                            $cartParentDetail->address_type = $user_address->address_type;
                        }
                        $cartParentDetail->address_id = $input['address_id'];
                        $address_id = $input['address_id'];
                    }

                    if ($cartParentDetail->update()) {
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

                $getRestro = Cart::select('products.restaurant_id')->where(['user_id'=>$userId])->join('products','products.id','=','cart.product_id')->first();

                $restroDetail = Restaurant::select('*')->where(['id'=>$getRestro->restaurant_id])->selectRaw("( 6371 * acos( cos( radians(" . $input['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $input['longitude'] . ") ) +  sin( radians(" . $input['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();
                $restaurantDistanceByStrightView = $restroDetail->distance;

                if ($restroDetail) {
                    $restroDetail['avg_rating'] = number_format(5, 1);
                    $restroDetail['distance'] = number_format($restroDetail->distance, 2).' KM';
                    $productsCategory = Products::select('products.category_id','products.restaurant_id','categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$restroDetail->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->with(array('dishes' => function ($query) use ($restroDetail) {
                        $query->where('products.restaurant_id', $restroDetail->id);
                    }))->get();

                    if ($productsCategory) {

                        foreach ($productsCategory as $k => $v) {
                            $category_arr[] = $v->name;
                        }
                    }

                    if (!empty($category_arr)) {
                        // $value->restroList['category'] = implode(", ", $category_arr);
                        $restroDetail['category'] = implode(", ", $category_arr);;

                    } else {
                        $restroDetail['category'] = '';
                    }

                    $taxPercentage = getCountryTaxByLatLong($input['latitude'], $input['longitude']);
                    $tax_amount = (($cartParentDetail->amount*$taxPercentage)/100);
                    //Update Tax Data
                    $cartParentDetail->tax_amount =  $tax_amount;
                    $cartParentDetail->taxPercentage =  $taxPercentage;
                    // dd($cartParentDetail->toArray());

                    if ($cartParentDetail->order_type == 3) {
                        $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$restroDetail->latitude.','.$restroDetail->longitude.'&destinations='. $input['latitude'].','. $input['longitude'].'&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                        $googleData = json_decode($googleData);

                        if (isset($googleData->rows) && isset($googleData->rows[0]) && $googleData->rows[0]->elements[0]->status == 'OK'){
                            $metter = $googleData->rows[0]->elements[0]->distance->value ?? 0;  
                            $total_km = ($metter/1000);

                            if ($total_km < 2) {
                                $shipping_charges = 10;

                            } else {
                                // $shipping_charges = $total_km * $restroDetail->delivery_charges_per_km;
                                $shipping_charges = $total_km * getShippingCharge();
                            }

                            if ($total_km > 0) {
                                $restroDetail['distance'] = number_format($total_km, 2).' KM';
                            }

                            //update shipping charges
                            $cartParentDetail->shipping_charges = number_format($shipping_charges, 2);
                            $cartParentDetail->update();

                        } else {

                            if ($restaurantDistanceByStrightView) {
                                $shipping_charges = $restaurantDistanceByStrightView * getShippingCharge();

                                //update shipping charges
                                $cartParentDetail->shipping_charges = number_format($shipping_charges, 2);
                                $cartParentDetail->update();
                            }
                        }

                    } else {
                        //update shipping charges
                        $cartParentDetail->shipping_charges = null;
                        $cartParentDetail->update();
                    }
                }

                foreach ($query as $key => $value) {
                    // $totalAmount +=$data->amount * $data->qty;
                    $productDetail = Products::where(['products.id'=>$value->product_id])->first();
                    // $totalAmount += $value->amount;
                    $totalQty += $value->qty;
                    // $totalKiloPoints += ($productDetail->points + $productDetail->extra_kilopoints) * $value->qty;
                    $totalKiloPoints += ($productDetail->points) * $value->qty;

                    $product_toppings = CartDetail::select('product_attribute_values_id','attribute_value_name','price')->where('cart_id',$value->id)->get();

                    if (count($product_toppings)) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }
                    $value->products = $productDetail;
                    $value->products->toppings = $product_toppings;
                }

                $response['status'] = 1;
                $response['data']['items'] = $query;
                $response['data']['restroDetail'] = $restroDetail;
                $response['data']['parent_cart_id'] = $query[0]->parent_cart_id;
                $response['data']['totalQty'] =  $totalQty;
                // $response['data']['totalAmount'] =  $totalAmount;
                $response['data']['totalAmount'] =  $cartParentDetail->amount;
                $response['data']['org_amount'] =  $cartParentDetail->org_amount;
                $response['data']['address_id'] =  $cartParentDetail->address_id;
                $response['data']['order_type'] =  (int)$cartParentDetail->order_type;
                $response['data']['discount_code'] = $cartParentDetail->discount_code;
                $response['data']['discount_amount'] = $cartParentDetail->discount_amount;
                $response['data']['discount_type'] = $cartParentDetail->discount_type;
                $response['data']['cart_status'] = $cartParentDetail->cart_status;
                $response['data']['totalKiloPoints'] =  (string)$totalKiloPoints;
                $response['data']['restaurant_table_id'] = $cartParentDetail->restaurant_table_id;
                $response['data']['table_code'] = $cartParentDetail->table_code ;
                $response['data']['tax_amount'] =  $cartParentDetail->tax_amount;

                if ($shipping_charges > 0) {
                    // $response['data']['shipping_charges'] = number_format($shipping_charges, 2);
                    $response['data']['shipping_charges'] = (string)$shipping_charges;

                } else {
                    $response['data']['shipping_charges'] = (string)$shipping_charges;
                }

                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                $response['data'] = [];
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['totalAmount'] =  $totalAmount;
                $response['data']['org_amount'] =  $totalAmount;
                $response['data']['tax_amount'] =  $totalAmount;
                $response['data']['address_id'] =  $address_id;
                $response['data']['order_type'] =  (int)$order_type;
                $response['data']['discount_code'] = '';
                $response['data']['discount_amount'] = 0;
                $response['data']['totalKiloPoints'] =  (string)$totalKiloPoints;
                $response['data']['shipping_charges'] = '0';
                $response['data']['restaurant_table_id'] = '';
                $response['data']['table_code'] = '';
                return response()->json($response, 200);
            }
        }
    }

    /*public function update_shop_cart(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->get();
        $totalQty = 0;
        $totalKiloPoints = 0;
        $totalAmount = 0.00;
        $address_id = '';
        $order_type = '';
        $discount_amount = 0;
        $shipping_charges = 0;
        $total_km = 0;        
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (count($query)) {
                $cartParentDetail = CartParent::where(['id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

                if ($cartParentDetail) {
                    $discount_amount = $cartParentDetail->discount_amount;

                    if (isset($input['order_type'])) {
                        $cartParentDetail->order_type = $input['order_type'];
                        $order_type = $input['order_type'];
                    }

                    if (isset($input['address_id'])) {
                        $cartParentDetail->address_id = $input['address_id'];
                        $address_id = $input['address_id'];
                    }

                    if ($cartParentDetail->update()) {
                        $response['status'] = 1;
                        $response['message'] = 'Data updated successfully.';

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid cart parent id.';
                }

                $getRestro = Cart::select('products.restaurant_id')->where(['user_id'=>$userId])->join('products','products.id','=','cart.product_id')->first();
                $restroDetail = Restaurant::select('*')->where(['id'=>$getRestro->restaurant_id])->selectRaw("( 6371 * acos( cos( radians(" . $input['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $input['longitude'] . ") ) +  sin( radians(" . $input['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();

                if ($restroDetail) {
                    $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$restroDetail->latitude.','.$restroDetail->longitude.'&destinations='. $input['latitude'].','. $input['longitude'].'&key=AIzaSyDVBREanDQF283-XQcI-vWGu3FCUVaz9C8');
                    $googleData = json_decode($googleData);

                    if ($googleData->rows[0]->elements[0]->status == 'OK') {
                        $metter = $googleData->rows[0]->elements[0]->distance->value ?? 0;  
                        $total_km = ($metter/1000);
                        $shipping_charges = $total_km * $restroDetail->delivery_charges_per_km;
                    }
                    $restroDetail['avg_rating'] = number_format(5, 1);

                    if ($total_km > 0) {
                        $restroDetail['distance'] = number_format($total_km, 2).' KM';

                    } else {
                        $restroDetail['distance'] = number_format($restroDetail->distance, 2).' KM';
                    }
                    $productsCategory = Products::select('products.category_id','products.restaurant_id','categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$restroDetail->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->with(array('dishes' => function ($query) use ($restroDetail) {
                        $query->where('products.restaurant_id', $restroDetail->id);
                    }))->get();

                    if ($productsCategory) {

                        foreach ($productsCategory as $k => $v) {
                            $category_arr[] = $v->name;
                        }
                    }


                    if (!empty($category_arr)) {
                        // $value->restroList['category'] = implode(", ", $category_arr);
                        $restroDetail['category'] = implode(", ", $category_arr);;

                    } else {
                        $restroDetail['category'] = '';
                    }
                }

                foreach ($query as $key => $value) {
                    // $totalAmount +=$data->amount * $data->qty;
                    $productDetail = Products::where(['products.id'=>$value->product_id])->first();
                    $totalAmount += $value->amount;
                    $totalQty += $value->qty;
                    $totalKiloPoints += $productDetail->points;

                    $product_toppings = CartDetail::select('dish_topping_id','topping_name','price')->where('cart_id',$value->id)->get();

                    if (count($product_toppings)) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }
                    $value->products = $productDetail;
                    $value->products->toppings = $product_toppings;
                }
                $response['data']['items'] = $query;
                $response['data']['restroDetail'] = $restroDetail;
                $response['data']['parent_cart_id'] = $query[0]->parent_cart_id;
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['totalAmount'] =  $totalAmount;
                $response['data']['totalKiloPoints'] =  $totalKiloPoints;
                $response['data']['address_id'] =  $address_id;
                $response['data']['order_type'] =  $order_type;
                $response['data']['discount_amount'] = $discount_amount;

                if ($cartParentDetail->order_type == 3) {
                    $response['data']['shipping_charges'] = $shipping_charges;

                } else {
                    $response['data']['shipping_charges'] = 0;
                }
                $response['data']['total_km'] = $total_km;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                $response['data'] = [];
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['totalAmount'] =  $totalAmount;
                $response['data']['totalKiloPoints'] =  $totalKiloPoints;
                $response['data']['address_id'] =  $address_id;
                $response['data']['order_type'] =  $order_type;
                $response['data']['discount_amount'] = $discount_amount;
                $response['data']['shipping_charges'] = $shipping_charges;
                $response['data']['total_km'] = $total_km;
                return response()->json($response, 200);
            }
        }
    }*/

    public function update_shop_cart(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->get();
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
            'is_wallet_use' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $contact_detail = json_decode($input['contact_detail'], JSON_UNESCAPED_SLASHES);

            if (count($query)) {
                $validationError = false;

                if ($input['payment_method'] == 'Spilt-Bill') {

                    if (!isset($input['spilt_bill_count'])) {
                        $validationError = true;
                        $status = 0;
                        $message = 'Spilt-Bill count field is required.';
                    }

                    if (!isset($input['contact_detail'])) {
                        $validationError = true;
                        $status = 0;
                        $message = 'Contact detail field is required.';
                    }

                    if (!isset($input['split_type'])) {
                        $validationError = true;
                        $status = 0;
                        $message = 'Split type field is required.';
                    }
                }

                if ($validationError) {
                    $response['status'] = $status;
                    $response['message'] = $message;
                    return response()->json($response, 200);

                } else {
                    $cartParentDetail = CartParent::where(['id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

                    if ($cartParentDetail) {

                        if ($cartParentDetail->cart_status == 'Pending') {
                            $cartParentDetail->spilt_bill_count = $input['spilt_bill_count'];
                            $cartParentDetail->split_type = $input['split_type'];
                            $cartParentDetail->is_wallet_use = $input['is_wallet_use'];
                            $cartParentDetail->cart_status = 'Ready-For-Checkout';

                            if ($cartParentDetail->update()) {
                                //add split detail users
                                //if payment_method is Spilt-Bill (order_split_bills -> tbl)
                                if (isset($input['contact_detail']) && !empty($input['contact_detail']) && $input['payment_method'] == 'Spilt-Bill') {
                                    //delete old contact_detail
                                    CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();
                                    Notification::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();
                                    CartSplitBillProduct::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();

                                    foreach ($contact_detail as $k_contact => $v_contact) {
                                        $SplitBillData = new CartSplitBills;
                                        $SplitBillData->parent_cart_id = $input['parent_cart_id'];
                                        $SplitBillData->user_id = $v_contact['user_id'];
                                        $SplitBillData->contact_name = $v_contact['contact_name'];
                                        $SplitBillData->contact_number = $v_contact['contact_number'];
                                        $SplitBillData->amount = $v_contact['amount'];

                                        if (isset($v_contact['product_id'])) {
                                            $SplitBillData->product_id = $v_contact['product_id'];
                                        }
                                        
                                        if ($SplitBillData->save()) {
                                            //Notification data

                                            if ($cartParentDetail->user_id != $v_contact['user_id']) {
                                                $notificationData = new Notification;
                                                $notificationData->user_id = $v_contact['user_id'];
                                                $notificationData->parent_cart_id = $input['parent_cart_id'];
                                                $notificationData->notification_for = 'Spilt-Bill';
                                                $notificationData->user_type = 2;
                                                $notificationData->notification_type = 3;
                                                $notificationData->title = 'Cart Product Request';
                                                $notificationData->message = 'Cart #'.$input['parent_cart_id'].' your friend send you a request to pay product amount QAR'.$v_contact['amount'].'.';
                                                $notificationData->save();
                                                send_notification(1, $v_contact['user_id'], 'Cart Product Request', array('title'=>'Cart Product Request','message'=>$notificationData->message,'type'=>'Dish','key'=>'event'));
                                            }
                                            //End Notification

                                            //insert cart_split_bill_product
                                            if (isset($v_contact['product_id'])) {
                                                $product_ids = explode(",",$v_contact['product_id']);

                                                if ($product_ids) {

                                                    foreach ($product_ids as $val_product) {
                                                        $ProductDetail = Cart::where(['parent_cart_id'=>$input['parent_cart_id'], 'product_id'=>$val_product])->first();

                                                        if ($ProductDetail) {
                                                            $splitProducts = new CartSplitBillProduct;
                                                            $splitProducts->parent_cart_id = $input['parent_cart_id'];
                                                            $splitProducts->user_id = $v_contact['user_id'];
                                                            $splitProducts->cart_split_bill_id = $SplitBillData->id;
                                                            $splitProducts->product_id = $val_product;
                                                            $splitProducts->amount = $ProductDetail->amount;
                                                            $splitProducts->product_price = $ProductDetail->product_price;
                                                            $splitProducts->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $response['status'] = 1;
                                $response['message'] = 'Your cart updated successfully.';

                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Error Occured.';
                            }

                        } else {
                            $response['status'] = 0;
                            $response['cart_status'] = $cartParentDetail->cart_status;
                            $response['message'] = 'You can not edit cart.';
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Invalid cart parent id.';
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

    /*public function actionPaymentRequest(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
            'action' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $splitBillData = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

            if ($splitBillData) {

                if ($input['action'] == 'Decline') {
                    $splitBillData->payment_status = 'Decline';
                    
                    if ($splitBillData->update()) {
                        $response['status'] = 1;
                        $response['message'] = 'Your request declined successfully.';

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }

                } else if ($input['action'] == 'Accept') {

                    if (isset($input['transaction_id'])) {
                        $splitBillData->payment_status = 'Accept';
                        $splitBillData->transaction_id = $input['transaction_id'];
                        $splitBillData->update();
                        $response['status'] = 1;
                        $response['message'] = 'Your request accepted successfully.';

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Transaction Id is required.';
                    }

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid action.';
                }
                //checkAllActionCompleted
                $cartParentDetail = CartParent::where(['id'=>$input['parent_cart_id']])->first();
                $checkAllActionCompleted = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id'], 'payment_status'=>'Pending'])->where('user_id', '!=', $cartParentDetail->user_id)->first();

                if (!$checkAllActionCompleted) {
                    send_notification(0, $cartParentDetail->user_id, 'All Slip Action Done', array('title'=>'All Slip Action Done','message'=>'Split action is done','type'=>'Dish','key'=>'event'));
                }
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'You did not linked with this cart.';
                return response()->json($response, 200);
            }
        }
    }*/

    public function actionPaymentRequest(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
            'action' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $splitBillData = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

            if ($splitBillData) {

                if ($input['action'] == 'Decline') {
                    $splitBillData->payment_status = 'Decline';
                    
                    if ($splitBillData->update()) {
                        $response['status'] = 1;
                        $response['message'] = 'Your request declined successfully.';

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }

                } else if ($input['action'] == 'Accept') {
                    $splitBillData->payment_type = $input['payment_method'];
                    $splitBillData->transaction_id = $input['transaction_id'] ?? null;
                    $splitBillData->payment_status = 'Accept';

                    /*if (isset($input['transaction_id'])) {
                        $splitBillData->transaction_id = $input['transaction_id'];

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Transaction Id is required.';
                    }*/
                    $splitBillData->update();

                    if (isset($input['payment_method']) && $input['payment_method'] == 'Wallet') {
                        //Debit from wallet balance
                        $walletData = new UserWallets;
                        $walletData->user_id = $userId;
                        $walletData->transaction_type = 'DR';
                        $walletData->amount = $splitBillData->amount;
                        $walletData->comment = 'Money debit for split bill payment.';
                        $walletData->save();
                    }
                    $response['status'] = 1;
                    $response['message'] = 'Your request accepted successfully.';

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid action.';
                }
                //checkAllActionCompleted
                $cartParentDetail = CartParent::where(['id'=>$input['parent_cart_id']])->first();
                $checkAllActionCompleted = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id'], 'payment_status'=>'Pending'])->where('user_id', '!=', $cartParentDetail->user_id)->first();

                if (!$checkAllActionCompleted) {
                    send_notification(0, $cartParentDetail->user_id, 'All Slip Action Done', array('title'=>'All Slip Action Done','message'=>'Split action is done','type'=>'Dish','key'=>'event'));
                }
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'You did not linked with this cart.';
                return response()->json($response, 200);
            }
        }
    }

    public function getSplitBillUserData(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $splitBillData = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

            if ($splitBillData) {
                $response['status'] = 1;
                $response['data'] = $splitBillData;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid Parent Cart Id.';
                return response()->json($response, 200);
            }
        }
    }

    public function getSplitBillPaymentStatus(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $splitBillData = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id']])->get();

            if (count($splitBillData)) {
                $response['status'] = 1;
                $response['data'] = $splitBillData;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid Parent Cart Id.';
                return response()->json($response, 200);
            }
        }
    }

    public function addContactDetails(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->get();
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'contact_name' => 'required',
            'contact_number' => 'required',
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {

            if (count($query)) {
                $cartParentDetail = CartParent::where(['id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

                if ($cartParentDetail) {
                    $cartParentDetail->contact_name = $input['contact_name'];
                    $cartParentDetail->contact_number = $input['contact_number'];

                    if ($cartParentDetail->update()) {
                        $response['status'] = 1;
                        $response['message'] = 'Data updated successfully.';

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                    }

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid cart parent id.';
                }
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                return response()->json($response, 200);
            }
        }
    }

    public function checkout(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->get();
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
            'is_wallet_use' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

            if (count($query)) {
                $validationError = false;

                if ($input['payment_method'] == 'Spilt-Bill') {

                    if (!isset($input['contact_detail'])) {
                        $validationError = true;
                        $status = 0;
                        $message = 'Contact detail field is required.';
                    }
                }

                if ($validationError) {
                    $response['status'] = $status;
                    $response['message'] = $message;
                    return response()->json($response, 200);

                } else {

                    if ($input['payment_method'] == 'Spilt-Bill') {
                        $contact_detail = json_decode($input['contact_detail'], JSON_UNESCAPED_SLASHES);
                    }

                    $cartParentDetail = CartParent::where(['id'=>$input['parent_cart_id'], 'user_id'=>$userId])->first();

                    if ($cartParentDetail) {
                        $restroDetail = Restaurant::where(['id'=>$cartParentDetail->restaurant_id])->first();
                        $discount_type = Discount::select('category_type')->where(['discount_code'=>$cartParentDetail->discount_code])->first();

                        $orderData = new Orders();
                        $orderData->user_id = $userId;
                        $orderData->random_order_id = substr(str_shuffle($str_result), 0, 5);
                        $orderData->restaurant_id = $cartParentDetail->restaurant_id;
                        $orderData->address_id = $cartParentDetail->address_id;
                        $orderData->latitude = $cartParentDetail->latitude;
                        $orderData->longitude = $cartParentDetail->longitude;
                        $orderData->building_number = $cartParentDetail->building_number;
                        $orderData->building_name = $cartParentDetail->building_name;
                        $orderData->landmark = $cartParentDetail->landmark;
                        $orderData->address = $cartParentDetail->address;
                        $orderData->address_type = $cartParentDetail->address_type;
                        $orderData->order_type = $cartParentDetail->order_type;
                        $orderData->pick_type = $cartParentDetail->pick_type;
                        $orderData->pick_datetime = $cartParentDetail->pick_datetime;
                        $orderData->car_color = $cartParentDetail->car_color;
                        $orderData->pickup_option = $cartParentDetail->pickup_option;
                        $orderData->car_number = $cartParentDetail->car_number;
                        $orderData->car_brand = $cartParentDetail->car_brand;
                        $orderData->discount_code = $cartParentDetail->discount_code;
                        $orderData->discount_type = $discount_type->category_type ?? '';
                        $orderData->discount_percent = $cartParentDetail->discount_percent;
                        $orderData->discount_amount = $cartParentDetail->discount_amount;
                        $orderData->shipping_charges = $cartParentDetail->shipping_charges;
                        $orderData->tax_amount = $cartParentDetail->tax_amount;
                        $orderData->taxPercentage = $cartParentDetail->taxPercentage;
                        $orderData->amount = $cartParentDetail->amount;
                        $orderData->org_amount = $cartParentDetail->org_amount;
                        $orderData->dine_option = $cartParentDetail->dine_option;

                        if ($restroDetail) {
                            $orderData->admin_commission = $restroDetail->admin_comission;
                            // $orderData->admin_amount = (($cartParentDetail->amount*$restroDetail->admin_comission)/100);
                            $orderData->admin_amount = (($cartParentDetail->org_amount*$restroDetail->admin_comission)/100);

                            if ($restroDetail->main_category_id == 2) {
                                $orderData->is_kp_transfer = 'Yes';

                            } else {
                                $transferTime = getKPTransferTime();
                                $orderData->is_kp_transfer = 'No';
                                $orderData->kp_transfer_time = $transferTime;

                            }
                        }
                        $orderData->contact_name = $cartParentDetail->contact_name;
                        $orderData->contact_number = $cartParentDetail->contact_number;
                        // $orderData->order_status = 'Pending';

                        if ($restroDetail->main_category_id == 2) {
                            $orderData->order_status = 'Accepted';

                        } else {
                            $orderData->order_status = 'Pending';
                        }
                        $orderData->payment_type = $input['payment_method'];
                        $orderData->spilt_bill_count = $cartParentDetail->spilt_bill_count;
                        $orderData->split_type = $cartParentDetail->split_type;
                        $orderData->restaurant_table_id = $cartParentDetail->restaurant_table_id;
                        $orderData->table_code = $cartParentDetail->table_code;
                        $orderData->is_wallet_use = $input['is_wallet_use'];

                        if ($input['is_wallet_use'] == 'Yes') {
                            $totalCR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'CR'])->sum('amount');
                            $totalDR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'DR'])->sum('amount');
                            $available_balance = $totalCR-$totalDR;

                            if ($available_balance > 0) {

                                if ($input['payment_method'] == 'Spilt-Bill') {

                                    if (isset($contact_detail) && $contact_detail[0]['amount']) {

                                        if ($available_balance >= $contact_detail[0]['amount']) {
                                            $reduceAmount = $contact_detail[0]['amount'];

                                        } else {
                                            $reduceAmount = $available_balance;
                                        }
                                    }

                                } else {

                                    if ($available_balance >= $cartParentDetail->amount) {
                                        $reduceAmount = $cartParentDetail->amount;

                                    } else {
                                        $reduceAmount = $available_balance;
                                    }
                                }
                            }

                            $orderData->wallet_amount_used = $reduceAmount;
                        }

                        if ($orderData->save()) {
                            //update
                            // Orders::where(['id'=>$orderData->id])->update(['random_order_id' => substr(str_shuffle($str_result), 0, 5)]);
                            //order detail insert
                            $cartProductData = Cart::where(['parent_cart_id'=>$input['parent_cart_id']])->get();

                            if (count($cartProductData)) {

                                foreach ($cartProductData as $key => $value) {
                                    $orderDetailData = new OrdersDetails();
                                    $orderDetailData->order_id = $orderData->id;
                                    $orderDetailData->product_id  = $value->product_id;
                                    $orderDetailData->user_id   = $userId;
                                    $orderDetailData->qty   = $value->qty;
                                    $orderDetailData->amount   = $value->amount;
                                    $orderDetailData->product_price   = $value->product_price;
                                    $orderDetailData->points   = $value->points;

                                    if ($orderDetailData->save()) {
                                        $cartToppingData = CartDetail::where(['cart_id'=>$value->id])->get();

                                        if (count($cartToppingData)) {

                                            foreach ($cartToppingData as $k => $v) {
                                                $orderToppingData = new OrderToppings();
                                                $orderToppingData->order_detail_id = $orderDetailData->id;
                                                $orderToppingData->product_attribute_values_id = $v->product_attribute_values_id;
                                                $orderToppingData->attributes_lang_id = $v->attributes_lang_id;
                                                $orderToppingData->attribute_value_lang_id = $v->attribute_value_lang_id;
                                                $orderToppingData->attribute_value_name = $v->attribute_value_name;
                                                // $orderToppingData->dish_topping_id  = $v->dish_topping_id;
                                                // $orderToppingData->topping_name   = $v->topping_name;
                                                $orderToppingData->price   = $v->price;
                                                $orderToppingData->save();
                                            }
                                        }
                                    }
                                }
                            }

                            if ($reduceAmount > 0) {
                                //Debit from wallet balance
                                $walletData = new UserWallets;
                                $walletData->user_id = $userId;
                                $walletData->transaction_type = 'DR';
                                $walletData->amount = $reduceAmount+$orderData->shipping_charges+$orderData->tax_amount;
                                $walletData->comment = 'Money debit for order purchase ORD-'.$orderData->random_order_id;
                                $walletData->save();
                            }

                            //if payment_method is Spilt-Bill (order_split_bills -> tbl)
                            //Old Checkout split data from order_split_bills
                            if ($input['payment_method'] == 'Spilt-Bill') {
                                $cartSplitData = CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id']])->where('user_id','!=',$userId)->get();

                                if (count($cartSplitData)) {

                                    foreach ($cartSplitData as $k_contact => $v_contact) {
                                        $SplitBillData = new OrderSplitBills;
                                        $SplitBillData->order_id = $orderData->id;
                                        $SplitBillData->user_id = $v_contact->user_id;
                                        $SplitBillData->contact_name = $v_contact->contact_name;
                                        $SplitBillData->contact_number = $v_contact->contact_number;
                                        $SplitBillData->amount = $v_contact->amount;
                                        $SplitBillData->payment_status = $v_contact->payment_status;
                                        $SplitBillData->payment_type = $v_contact->payment_type;
                                        $SplitBillData->transaction_id = $v_contact->transaction_id;
                                        $SplitBillData->product_id = $v_contact->product_id;
                                        
                                        if ($SplitBillData->save()) {
                                            //insert order_split_bill_product
                                            $cartSplitProductData = CartSplitBillProduct::where(['cart_split_bill_id'=>$v_contact->id])->get();

                                            if ($cartSplitProductData) {
                                                
                                                foreach ($cartSplitProductData as $k => $v) {
                                                    $splitProducts = new OrderSplitBillProduct;

                                                    if ($v_contact->payment_status == 'Accept') {
                                                        $productDetail = Products::select('id','restaurant_id','price','points')->where(['products.id'=>$v->product_id])->first();
                                                        $pointsGet = 0;

                                                        if ($cartParentDetail->split_type == 'Dish-wise') {
                                                            $pointsGet = $productDetail->points;

                                                        } else {
                                                            $points = OrdersDetails::where('order_id', $orderData->id)->sum('points');

                                                            if ($points > 0) {
                                                                $pointsGet = $points/$cartParentDetail->spilt_bill_count;
                                                            }
                                                        }
                                                        $splitProducts->points = $pointsGet;
                                                        //insert in user_kilo_points table
                                                        $userPoints = new UserKiloPoints;
                                                        $userPoints->order_id = $orderData->id;
                                                        $userPoints->user_id = $v_contact->user_id;
                                                        $userPoints->points = $pointsGet;
                                                        $userPoints->save();

                                                        //insert In KiloPointsDB
                                                        $getUserDetail = User::where(['id'=>$v_contact->user_id])->first();

                                                        if ($getUserDetail && $getUserDetail->gift_user_id) {
                                                            $headerData = [
                                                                "accept: application/json",
                                                                "accept-language: ar",
                                                                "cache-control: no-cache",
                                                                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                                                                "gift-access-key: '".$getUserDetail->gift_access_key."'",
                                                                "gift-secret-key: '".$getUserDetail->gift_secret_key."'"
                                                            ];
                                                            $apiResponse = giftApis('updateKPInGift',['order_id' =>$orderData->id,'points' => $pointsGet,'platform' => 'KP','type' => 'CR','comment' => 'Order #'.$orderData->id.' placed.'], 'Header', $headerData);
                                                        }
                                                        /*$userKiloPointsNewDB = new UserKiloPoints;
                                                        $userKiloPointsNewDB->order_id = $orderData->id;
                                                        $userKiloPointsNewDB->user_id = $v_contact->user_id;
                                                        $userKiloPointsNewDB->points = $pointsGet;
                                                        $userKiloPointsNewDB->comment = 'Order #'.$orderData->id.' placed.';
                                                        $userKiloPointsNewDB->type = 'CR';
                                                        $userKiloPointsNewDB->setConnection('mysql2');
                                                        $userKiloPointsNewDB->save();*/

                                                    }
                                                    $splitProducts->order_id = $orderData->id;
                                                    $splitProducts->user_id = $v_contact->user_id;
                                                    $splitProducts->order_split_bill_id = $SplitBillData->id;
                                                    $splitProducts->product_id = $v->product_id;
                                                    $splitProducts->amount = $v->amount;
                                                    $splitProducts->product_price = $v->product_price;
                                                    $splitProducts->save();
                                                }
                                            }
                                        }
                                    }
                                }

                                //insert contact_detail data for checkout user
                                if (isset($contact_detail) && !empty($contact_detail)) {

                                    foreach ($contact_detail as $k_split => $v_split) {
                                        $orderSplitBillData = new OrderSplitBills;
                                        $orderSplitBillData->order_id = $orderData->id;
                                        $orderSplitBillData->user_id = $userId;
                                        $orderSplitBillData->contact_name = $userData->name;
                                        $orderSplitBillData->contact_number = $userData->mobile;
                                        $orderSplitBillData->amount = $v_split['amount'];
                                        $orderSplitBillData->payment_status = $v_split['payment_status'];
                                        $orderSplitBillData->transaction_id = $v_split['transaction_id'];
                                        
                                        if ($orderSplitBillData->save()) {

                                            //insert cart_split_bill_product
                                            if (isset($v_split['product_id'])) {
                                                $product_ids = explode(",",$v_split['product_id']);

                                                if ($product_ids) {

                                                    foreach ($product_ids as $val_product) {
                                                        $ProductDetail = Cart::where(['parent_cart_id'=>$input['parent_cart_id'], 'product_id'=>$val_product])->first();

                                                        if ($ProductDetail) {
                                                            $orderSplitProducts = new OrderSplitBillProduct;

                                                            $productDetail = Products::select('id','restaurant_id','price','points')->where(['products.id'=>$val_product])->first();

                                                            if ($productDetail) {
                                                                $getTotalPoints = OrdersDetails::where('order_id', $orderData->id)->sum('points');
                                                                $getDistributPoints = OrderSplitBillProduct::where('order_id', $orderData->id)->sum('points');
                                                                $pointsGet = $getTotalPoints-$getDistributPoints;
                                                                $orderSplitProducts->points = $pointsGet;

                                                                //insert in user_kilo_points table
                                                                $userPoints = new UserKiloPoints;
                                                                $userPoints->order_id = $orderData->id;
                                                                $userPoints->user_id = $userId;
                                                                $userPoints->points = $pointsGet;
                                                                $userPoints->save();

                                                                //insert In KiloPointsDB
                                                                $getUserDetail = User::where(['id'=>$userId])->first();

                                                                if ($getUserDetail && $getUserDetail->gift_user_id) {
                                                                    $headerData = [
                                                                        "accept: application/json",
                                                                        "accept-language: ar",
                                                                        "cache-control: no-cache",
                                                                        "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                                                                        "gift-access-key: '".$getUserDetail->gift_access_key."'",
                                                                        "gift-secret-key: '".$getUserDetail->gift_secret_key."'"
                                                                    ];
                                                                    $apiResponse = giftApis('updateKPInGift',['order_id' =>$orderData->id,'points' => $pointsGet,'platform' => 'KP','platform' => 'KP','type' => 'CR','comment' => 'Order #'.$orderData->id.' placed.'], 'Header', $headerData);
                                                                }
                                                                /*$userKiloPointsNewDB = new UserKiloPoints;
                                                                $userKiloPointsNewDB->order_id = $orderData->id;
                                                                $userKiloPointsNewDB->user_id = $userId;
                                                                $userKiloPointsNewDB->points = $pointsGet;
                                                                $userKiloPointsNewDB->comment = 'Order #'.$orderData->id.' placed.';
                                                                $userKiloPointsNewDB->type = 'CR';
                                                                $userKiloPointsNewDB->setConnection('mysql2');
                                                                $userKiloPointsNewDB->save();*/
                                                            }

                                                            $orderSplitProducts->order_id = $orderData->id;
                                                            $orderSplitProducts->user_id = $userId;
                                                            $orderSplitProducts->order_split_bill_id = $orderSplitBillData->id;
                                                            $orderSplitProducts->product_id = $val_product;
                                                            $orderSplitProducts->amount = $ProductDetail->amount;
                                                            $orderSplitProducts->product_price = $ProductDetail->product_price;
                                                            $orderSplitProducts->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            } else {
                                $getTotalPoints = OrdersDetails::where('order_id', $orderData->id)->sum('points');
                                //insert in user_kilo_points table
                                $userPoints = new UserKiloPoints;
                                $userPoints->order_id = $orderData->id;
                                $userPoints->user_id = $userId;
                                $userPoints->points = $getTotalPoints;
                                $userPoints->save();

                                //insert In KiloPointsDB
                                $getUserDetail = User::where(['id'=>$userId])->first();

                                if ($getUserDetail->gift_user_id) {
                                    $headerData = [
                                        "accept: application/json",
                                        "accept-language: ar",
                                        "cache-control: no-cache",
                                        "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                                        "gift-access-key: '".$getUserDetail->gift_access_key."'",
                                        "gift-secret-key: '".$getUserDetail->gift_secret_key."'"
                                    ];
                                    $apiResponse = giftApis('updateKPInGift',['order_id' =>$orderData->id,'points' => $getTotalPoints,'platform' => 'KP','type' => 'CR','comment' => 'Order #'.$orderData->id.' placed.'], 'Header', $headerData);
                                }

                                /*$userKiloPointsNewDB = new UserKiloPoints;
                                $userKiloPointsNewDB->order_id = $orderData->id;
                                $userKiloPointsNewDB->user_id = $userId;
                                $userKiloPointsNewDB->points = $getTotalPoints;
                                $userKiloPointsNewDB->comment = 'Order #'.$orderData->id.' placed.';
                                $userKiloPointsNewDB->type = 'CR';
                                $userKiloPointsNewDB->setConnection('mysql2');
                                $userKiloPointsNewDB->save();*/
                            }

                            //Notification data
                            $notificationData = new Notification;
                            $notificationData->user_id = $userId;
                            $notificationData->order_id = $orderData->id;
                            $notificationData->user_type = 2;
                            $notificationData->notification_for = 'Order-Placed';
                            $notificationData->notification_type = 3;
                            $notificationData->title = 'Order Placed';

                            if ($cartParentDetail->order_type == 1) {
                                $notificationData->message = 'Order #'.$orderData->random_order_id.' is going to ready and serve you on table soon.';

                            } else if ($cartParentDetail->order_type == 2) {
                                $pickupText = '';
                                $store = 'Restaurant';

                                if ($restroDetail->main_category_id != 2) {
                                    $store = 'Store';
                                }

                                if ($orderData->pick_type == 'now') {
                                    $pickupText = ' now from '.$store.'.';

                                    if ($orderData->car_number && !empty($orderData->car_number)) {
                                        $pickupText = ' now in your Car.';
                                    }

                                } else if ($orderData->pick_type == 'later') {
                                    $pickupText = ' later from '.$store.'.';

                                    if ($orderData->car_number && !empty($orderData->car_number)) {
                                        $pickupText = ' later in your Car.';
                                    }

                                } else {
                                    $pickupText = ' from '.$store.'.';

                                    if ($orderData->car_number && !empty($orderData->car_number)) {
                                        $pickupText = ' from Car.';
                                    }
                                }
                                $notificationData->message = 'Order #'.$orderData->random_order_id.' Pick-up'.$pickupText;

                            } else if ($cartParentDetail->order_type == 3) {
                                $notificationData->message = 'Order #'.$orderData->random_order_id.' ready to delivery soon.';

                            } else {
                                $notificationData->message = 'Order #'.$orderData->random_order_id.' placed successfully.';
                            }
                            $notificationData->save();

                            //Panel Notification data
                            $panelNotificationData = new PanelNotifications;
                            $panelNotificationData->user_id = $orderData->restaurant_id;
                            $panelNotificationData->order_id = $orderData->id;
                            $panelNotificationData->user_type = 4;
                            $panelNotificationData->notification_for = 'Order-Placed';
                            $panelNotificationData->notification_type = 3;
                            $panelNotificationData->title = 'Order Placed';

                            /*if ($cartParentDetail->order_type == 1) {
                                $panelNotificationData->message = 'Order #'.$orderData->random_order_id.' is going to ready and serve you on table soon.';

                            } else if ($cartParentDetail->order_type == 2) {
                                $panelNotificationData->message = 'Order #'.$orderData->random_order_id.' Pick-up at your car or the cafe door.';

                            } else if ($cartParentDetail->order_type == 3) {
                                $panelNotificationData->message = 'Order #'.$orderData->random_order_id.' ready to delivery soon.';

                            } else {
                                $panelNotificationData->message = 'Order #'.$orderData->random_order_id.' placed successfully.';
                            }*/
                           
                            //End Notification

                            //Send Email For Order
                            /*$orders = Orders::select('orders.status as order_status','users.name as user_name','users.mobile as user_mobile', 'orders.shipping_charges  as orders_shipping_charges', 'orders.transaction_id  as transaction_id','orders.shipping_charges  as shipping_charges','orders.payment_type  as payment_type','orders.order_status  as order_status','users.country_code','users.email as user_email','user_address.address as user_address','user_address.latitude','user_address.longitude','orders.created_at','orders.id','orders.amount','orders.user_id')
                                ->leftjoin('users','users.id','=','orders.user_id')
                                ->leftjoin('user_address','user_address.id','=','orders.address_id')
                                ->leftjoin('transaction','transaction.id','=','orders.transaction_id')
                                ->where('orders.id', $orderData->id)
                                ->first();*/


                            //Sent In English
                            /*$email = EmailTemplateLang::where('email_id', 5)->where('lang', 'en')->select(['name', 'subject', 'description','footer'])->first();
                            $order_data = date('d M Y', strtotime($orders->created_at));
                            $description = $email->description;
                            $description = str_replace("[NAME]", $orders->user_name, $description);
                            $description = str_replace("[ORDER_DATE]", $order_data, $description);
                            $description = str_replace("[ORDER_ID]", $orders->id, $description);
                            $description = str_replace("[ORDER_STATUS]", $orders->order_status, $description);
                            $description = str_replace("[USERNAME]", $orders->user_name, $description);
                            $description = str_replace("[PRICE]", $orders->amount, $description);

                            $name = $email->name;
                            $name = str_replace("[NAME]", $orders->user_name, $name);

                            $record=(object)[];
                            $record->description = $description;
                            $record->footer = $email->footer;
                            $record->name = $name;
                            $record->subject = $email->subject;

                            Mail::send('emails.order', compact('record'), function($message)use($orders, $email) {
                                $message->to($orders->user_email, config('app.name'))->subject($email->subject);
                                $message->from('support@contactless.com',config('app.name'));
                            });*/

                            //Sent In Arabic
                            /*$email = EmailTemplateLang::where('email_id', 5)->where('lang', 'ar')->select(['name', 'subject', 'description','footer'])->first();
                            $order_data = date('d M Y', strtotime($orders->created_at));
                            $description = $email->description;
                            $description = str_replace("[NAME]", $orders->user_name, $description);
                            $description = str_replace("[ORDER_DATE]", $order_data, $description);
                            $description = str_replace("[ORDER_ID]", $orders->id, $description);
                            $description = str_replace("[ORDER_STATUS]", $orders->order_status, $description);
                            $description = str_replace("[USERNAME]", $orders->user_name, $description);
                            $description = str_replace("[PRICE]", $orders->amount, $description);

                            $name = $email->name;
                            $name = str_replace("[NAME]", $orders->user_name, $name);

                            $record=(object)[];
                            $record->description = $description;
                            $record->footer = $email->footer;
                            $record->name = $name;
                            $record->subject = $email->subject;

                            Mail::send('emails.order', compact('record'), function($message)use($orders, $email) {
                                $message->to($orders->user_email, config('app.name'))->subject($email->subject);
                                $message->from('support@contactless.com',config('app.name'));
                            });*/

                            CartParent::where(['id'=>$input['parent_cart_id']])->delete();
                            Cart::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();
                            CartDetail::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();
                            CartSplitBills::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();
                            CartSplitBillProduct::where(['parent_cart_id'=>$input['parent_cart_id']])->delete();

                            $checkFirstOrder = Orders::where(['user_id'=>$userId, ])->count();

                            if ($checkFirstOrder == 1) {
                                $checkReferralUser = User::where(['id'=>$userId])->first();

                                if ($checkReferralUser->referral_code != '') {
                                    $getReferredUserData = User::where(['share_code'=>$checkReferralUser->referral_code])->first();

                                    if ($getReferredUserData) {
                                        $getTotalPoints = getUserReferralKP($cartParentDetail->org_amount);
                                        //insert in user_kilo_points table
                                        $userPoints = new UserKiloPoints;
                                        $userPoints->order_id = $orderData->id;
                                        $userPoints->user_id = $getReferredUserData->id;
                                        $userPoints->points = $getTotalPoints;
                                        $userPoints->save();

                                        //insert In KiloPointsDB
                                        $getUserDetail = User::where(['id'=>$getReferredUserData->id])->first();

                                        if ($getUserDetail && $getUserDetail->gift_user_id) {
                                            $headerData = [
                                                "accept: application/json",
                                                "accept-language: ar",
                                                "cache-control: no-cache",
                                                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                                                "gift-access-key: '".$getUserDetail->gift_access_key."'",
                                                "gift-secret-key: '".$getUserDetail->gift_secret_key."'"
                                            ];
                                            $apiResponse = giftApis('updateKPInGift',['order_id' =>null,'points' => $getTotalPoints,'platform' => 'KP','type' => 'CR','is_refund' => 'Yes','is_kp_transfer' => 'No','comment' => 'Congratulations! You have received referral bonus KP'], 'Header', $headerData);
                                        }

                                        /*$userKiloPointsNewDB = new UserKiloPoints;
                                        $userKiloPointsNewDB->order_id = null;
                                        $userKiloPointsNewDB->user_id = $getReferredUserData->id;
                                        $userKiloPointsNewDB->points = $getTotalPoints;
                                        $userKiloPointsNewDB->comment = 'Congratulations! You have received referral bonus KP.';
                                        $userKiloPointsNewDB->type = 'CR';
                                        $userKiloPointsNewDB->is_refund = 'Yes';
                                        $userKiloPointsNewDB->setConnection('mysql2');
                                        $userKiloPointsNewDB->save();*/

                                        //Notification data
                                        $notificationData = new Notification;
                                        $notificationData->user_id = $getReferredUserData->id;
                                        $notificationData->order_id = null;
                                        $notificationData->user_type = 2;
                                        $notificationData->notification_for = 'Refferal-Received';
                                        $notificationData->notification_type = 3;
                                        $notificationData->title = 'Refferal KP';
                                        $notificationData->message = 'Congratulations! You have received referral bonus KP.';
                                        $notificationData->save();
                                    }
                                }
                            }

                            $data = array();
                            $data['order_id'] = $orderData->id;
                            $data['random_order_id'] = $orderData->random_order_id;
                            $response['status'] = 1;
                            $response['message'] = 'Order placed successfully.';
                            $response['data'] = $data;
                            send_notification(1, $userId, 'Order Placed', array('title'=>'Order Placed','message'=>$notificationData->message,'type'=>'Dish','key'=>'event'));

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

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart Empty.';
                return response()->json($response, 200);
            }
        }
    }

    public function cart_destroy(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $query = Cart::where('user_id',$userId)->pluck('id')->toArray();

        if ($query) {
            CartParent::where('user_id',$userId)->delete();
            Cart::where('user_id',$userId)->delete();
            CartDetail::whereIn('cart_id',$query)->delete();
            $response['status'] = 1;
            $response['message'] = 'Your cart removed successfully.';
            return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['message'] = 'Cart Empty.';
            return response()->json($response, 200);
        }
    }

    public function order_list(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
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
            //Change KP Status
            changeKPTransferStatus();
            $splitOrdersIds = OrderSplitBills::select('orders.*')->where('order_split_bills.user_id',$userId)->join('orders','orders.id','=','order_split_bills.order_id')->where('payment_status', 'Accept')->orderBy('id', 'desc')->pluck('orders.id')->toArray();

            /*if ($input['type'] == 'All') {
                $query = Orders::where('user_id',$userId)->orderBy('id', 'desc')->orWhereIn('id', $splitOrdersIds)->get()->toArray();

            } else if ($input['type'] == 'Pending') {
                $query = Orders::where(['user_id' => $userId])->orWhereIn('id', $splitOrdersIds)->where(['order_status' => $input['type']])->orderBy('id', 'desc')->get()->toArray();

            } else if ($input['type'] == 'Complete') {
                $query = Orders::where(['user_id' => $userId])->orWhereIn('id', $splitOrdersIds)->where(['order_status' => $input['type']])->orderBy('id', 'desc')->get()->toArray();

            } else if ($input['type'] == 'Cancel') {
                $query = Orders::where(['user_id' => $userId])->orWhereIn('id', $splitOrdersIds)->where(['order_status' => $input['type']])->orderBy('id', 'desc')->get()->toArray();

            } else {
                $query = Orders::where('user_id',$userId)->orderBy('id', 'desc')->orWhereIn('id', $splitOrdersIds)->get()->toArray();
            }*/

            $order_type = $input['type'];

            if (isset($input['main_category_id'])) {
                $data = Orders::select('orders.*', 'restaurants.main_category_id')->where('main_category_id', $input['main_category_id'])->where(function($query) use ($userId, $splitOrdersIds){
                    $query->where('orders.user_id', $userId);
                    $query->orWhereIn('orders.id', $splitOrdersIds);
                })
                ->where(function($query) use ($order_type, $input) {

                    if ($order_type != 'All') {

                        if ($order_type == 'Pending') {
                            $query->where('order_status', 'Pending')->orWhere('order_status', 'Accepted')->orWhere('order_status', 'Prepare');

                        } else {
                            $query->where('order_status', $order_type);
                        }
                    }
                })
                ->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')
                ->orderBy('orders.id', 'desc')
                ->get();

            } else {
                $data = Orders::select('orders.*', 'restaurants.main_category_id')->where(function($query) use ($userId, $splitOrdersIds){
                    $query->where('orders.user_id', $userId);
                    $query->orWhereIn('orders.id', $splitOrdersIds);
                })
                ->where(function($query) use ($order_type) {

                    if ($order_type != 'All') {

                        if ($order_type == 'Pending') {
                            $query->where('order_status', 'Pending')->orWhere('order_status', 'Accepted')->orWhere('order_status', 'Prepare');

                        } else {
                            $query->where('order_status', $order_type);
                        }
                    }
                })
                ->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')
                ->orderBy('orders.id', 'desc')
                ->get();
            }

            
            // dd($splitOrders);

            // $ordersList = array_merge($query, $splitOrders);

            if (count($data)) {

                foreach ($data as $key => $value) {

                    if ($value->user_id == $userId) {
                        $value->is_your_order = 'Yes';

                    } else {
                        $value->is_your_order = 'No';
                    }

                    if ($value->payment_type == 'Spilt-Bill') {

                        if ($value->split_type == 'Dish-wise') {
                            $OrderSplitAmount = OrderSplitBillProduct::where(['order_id'=>$value->id, 'user_id'=>$userId])->sum('amount');

                            if ($OrderSplitAmount) {
                                $value->amount = $OrderSplitAmount;
                            }
                        }
                    }
                }
                // dd($getOrderProducts->toArray());
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


    public function edit_order(Request $request) {
        $userData = auth()->user();
        $userId =  $userData->id;

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'products' => 'required',
            'amount' => 'required',
        ]);
        $input = $request->all();

        if($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $products = json_decode($input['products'], JSON_UNESCAPED_SLASHES);
            $query = Orders::where(['id'=>$input['order_id']])->first();

            if ($query) {

                if($query->order_type == 1 && $query->dine_option == 'pay-on-finish') {
                    $getOrderTotalAmount = OrdersDetails::where('order_id',$input['order_id'])->sum('amount');

                    $inpOrderAmount = [
                        'amount' => $getOrderTotalAmount+$input['amount'],
                        'org_amount' => $getOrderTotalAmount+$input['amount'],
                    ];

                    $orderDetailData = Orders::where('id',$input['order_id'])->firstOrFail();

                    if ($orderDetailData->update($inpOrderAmount)) {

                        foreach ($products as $key => $value) {
                            //dd($value['product_id']);
                            $productDetail = Products::select('id','restaurant_id','price','points')->where(['products.id'=>$value['product_id']])->first();

                            if($productDetail){

                                $checkSameRestro = Orders::select('orders.*')
                                ->where(['id'=>$input['order_id'], 'orders.restaurant_id'=>$productDetail->restaurant_id])
                                ->first();

                                if (!$checkSameRestro) {
                                    $response['status'] = 0;
                                    $response['message'] = 'Please choose same store to add product in order.';
                                    $response['is_same_restro'] = 0;
                                    return response()->json($response, 200);

                                } else {
                                    $data = new OrdersDetails();
                                    $data->order_id = $input['order_id'];
                                    $data->product_id = $value['product_id'];
                                    $data->user_id = $userId;
                                    $data->qty = $value['qty'];
                                    $data->amount = $value['amount'];
                                    $data->product_price = $productDetail->price;
                                    $data->points = $productDetail->points;

                                    if ($data->save()) {

                                        if (isset($value['toppings']) && !empty($value['toppings'])) {
                                            // $dish_toppings = explode(",", $value['toppings']);
                                            $attributeValues = explode(",", $value['toppings']);

                                            foreach ($attributeValues as $key => $value) {
                                                $attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id','product_attribute_values.attributes_lang_id')->where(['product_attribute_values.id' => $value])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->first();

                                                if ($attributeValues) {
                                                    $cartDetailData = new OrderToppings();
                                                    $cartDetailData->order_detail_id = $data->id;
                                                    $cartDetailData->product_attribute_values_id = $value;
                                                    $cartDetailData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                                    $cartDetailData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                                    $cartDetailData->attribute_value_name = $attributeValues->attribute_value_name;
                                                    $cartDetailData->price = $attributeValues->price;
                                                    $cartDetailData->save();
                                                }
                                            }
                                            //dd($dish_toppings);
                                            /*foreach ($dish_toppings as $keyv => $values) {
                                                $toppingDetail = Topping::where(['id'=>$values])->first();

                                                if ($toppingDetail) {
                                                    $cartDetailData = new OrderToppings();
                                                    $cartDetailData->order_detail_id = $data->id;
                                                    $cartDetailData->dish_topping_id = $values;
                                                    $cartDetailData->topping_name = $toppingDetail->topping_name;
                                                    $cartDetailData->price = $toppingDetail->price;
                                                    $cartDetailData->save();
                                                }
                                            }*/
                                        }

                                        /*$response['status'] = 1;
                                        $response['message'] = 'Order edit successfully.';
                                        return response()->json($response, 200);*/

                                    } else {
                                        /*$response['status'] = 0;
                                        $response['message'] = 'Error Occured.';
                                        return response()->json($response, 200);*/
                                    }

                                }
                            } else {
                                /*$response['status'] = 0;
                                $response['message'] = 'Product not found.';
                                return response()->json($response,200);*/
                            }
                        }

                        $response['status'] = 1;
                        $response['message'] = 'Order edit successfully.';
                        return response()->json($response, 200);
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Error Occured.';
                        return response()->json($response, 200);
                    }

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'You are not able to edit this order.';
                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid Order Id.';
                return response()->json($response, 200);
            }
        }
    }

    public function checkout_payonfinish(Request $request) {
        $userData = auth()->user();
        $userId = $userData->id;
        //dd($userId);
        $reduceAmount = 0;
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'payment_type' => 'required',
            'is_wallet_use' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);
        } else {
            $fail = false;
            $message = '';

            if($input['payment_type'] != 'Cash') {
                if(empty($input['transaction_id'])) {
                    $fail = true;
                    $message = 'The transaction id field is required.';
                }
            }

            if ($input['payment_type'] == 'Spilt-Bill') {

                if (!isset($input['contact_detail'])) {
                    $fail = true;
                    $message = 'Contact detail field is required.';
                }
            }

            if(!$fail) {

                if ($input['payment_type'] == 'Spilt-Bill') {
                    $contact_detail = json_decode($input['contact_detail'], JSON_UNESCAPED_SLASHES);
                }

                 $query = Orders::select('orders.*')->where('id',$input['order_id'])->first();

                 if ($query) {
                    $transaction_id ='';
                    if ($input['payment_type'] == 'Cash') {
                        $transaction_id = null;
                    } else {
                        $transaction_id = $input['transaction_id'];
                    }

                    $inpOrder = [
                        'is_edit_order' => 1,
                        'payment_type' => $input['payment_type'],
                        'transaction_id' => $transaction_id,
                    ];

                    if ($input['is_wallet_use'] == 'Yes') {
                        $totalCR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'CR'])->sum('amount');
                        $totalDR =  UserWallets::where(['user_id'=>$userId,'transaction_type'=>'DR'])->sum('amount');
                        $available_balance = $totalCR-$totalDR;

                        if ($available_balance > 0) {

                            if ($input['payment_type'] == 'Spilt-Bill') {

                                if (isset($contact_detail) && $contact_detail[0]['amount']) {

                                    if ($available_balance >= $contact_detail[0]['amount']) {
                                        $reduceAmount = $contact_detail[0]['amount'];

                                    } else {
                                        $reduceAmount = $available_balance;
                                    }
                                }

                            } else {
                                $totalAmount = $query->amount+$query->shipping_charges+$query->tax_amount;

                                if ($available_balance >= $totalAmount) {
                                    $reduceAmount = $totalAmount;

                                } else {
                                    $reduceAmount = $available_balance;
                                }
                            }
                        }

                        $inpOrder['wallet_amount_used'] = $reduceAmount;
                        $inpOrder['is_wallet_use'] = 'Yes';
                    }

                    //dd($inpOrder);
                    $orderDetailData = Orders::where('id',$input['order_id'])->firstOrFail();

                    if ($orderDetailData->update($inpOrder)) {

                        if ($reduceAmount > 0) {
                            //Debit from wallet balance
                            $walletData = new UserWallets;
                            $walletData->user_id = $userId;
                            $walletData->transaction_type = 'DR';
                            $walletData->amount = $reduceAmount;
                            $walletData->comment = 'Money debit for order purchase ORD-'.$query->random_order_id;
                            $walletData->save();
                        }

                        $response['status'] = 1;
                        $response['message'] = 'Order has been placed successfully.';
                        return response()->json($response, 200);
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Order can`t placed.';
                        return response()->json($response, 200);
                    }


                 } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid Order Id.';
                    return response()->json($response, 200);
                 }
            } else {
                $response['status'] = 0;
                $response['message'] = $message;
                return response()->json($response, 200);
            }
             
        }
    }

    public function order_detail(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
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
            // $splitOrdersIds = OrderSplitBills::select('orders.*')->where('order_split_bills.user_id',$userId)->join('orders','orders.id','=','order_split_bills.order_id')->where('payment_status', 'Accept')->orderBy('id', 'desc')->pluck('orders.id')->toArray();

            $query = Orders::select('orders.*','order_cancel_reasions.reasion','restaurants.main_category_id')->where(['orders.id'=>$input['order_id']])->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->leftjoin('order_cancel_reasions','order_cancel_reasions.id','=','orders.cancel_reasion_id')->first();

            if ($query) {

                if ($query->payment_type == 'Spilt-Bill') {
                    $getOrderProducts = array();

                    if ($query->split_type == 'Dish-wise') {
                        $OrderSplitBills = OrderSplitBills::select('order_split_bill_products.product_id')->join('order_split_bill_products','order_split_bill_products.order_split_bill_id','=','order_split_bills.id')->where(['order_split_bills.order_id'=>$query->id, 'order_split_bills.user_id'=>$userId])->groupBy('order_split_bill_products.product_id')->where('payment_status', 'Accept')->get();

                    } else {
                        $OrderSplitBills = OrderSplitBills::select('order_split_bills.amount')->where(['order_split_bills.order_id'=>$query->id, 'order_split_bills.user_id'=>$userId])->where('payment_status', 'Accept')->get();
                    }

                    if ($OrderSplitBills) {

                        if ($query->split_type == 'Dish-wise') {

                            foreach ($OrderSplitBills as $key => $value) {
                                $getOrderProducts[] = OrdersDetails::where(['order_id'=>$query->id, 'product_id'=>$value->product_id])->first();   
                            }

                        } else {
                            $orderDetails = OrdersDetails::where(['order_id'=>$query->id])->get();
                            $getOrderProducts = $orderDetails;
                        }
                    }
                    // dd($getOrderProducts);

                } else {
                    $getOrderProducts = OrdersDetails::where(['order_id'=>$query->id])->get();
                }

                $getRestro = OrdersDetails::select('products.restaurant_id')->where(['order_id'=>$query->id])->join('products','products.id','=','order_details.product_id')->first();

                if($query->latitude && !empty($query->latitude)) {

                    $restroDetail = Restaurant::select('*')->where(['id'=>$getRestro->restaurant_id])->selectRaw("( 6371 * acos( cos( radians(" . $query->latitude . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $query->longitude . ") ) +  sin( radians(" . $query->latitude . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();

                } else if(isset($input['latitude']) && !empty($input['latitude'])) {

                    $restroDetail = Restaurant::select('*')->where(['id'=>$getRestro->restaurant_id])->selectRaw("( 6371 * acos( cos( radians(" . $input['latitude'] . ") ) *cos( radians(restaurants.latitude) ) * cos( radians(restaurants.longitude) - radians(" . $input['longitude'] . ") ) +  sin( radians(" . $input['latitude'] . ") ) * sin( radians(restaurants.latitude) ) ) )  AS distance")->first();
                } else {
                    $restroDetail = Restaurant::select('*')->where(['id'=>$getRestro->restaurant_id])->first();
                }


                if ($restroDetail) {
                    $restroDetail['avg_rating'] = number_format(5, 1);
                    $restroDetail['distance'] = number_format($restroDetail->distance, 2).' KM';
                    $productsCategory = Products::select('products.category_id','products.restaurant_id','categories_lang.name')->join('categories_lang','categories_lang.category_id','=','products.category_id')->where(['products.is_active'=>1, 'products.restaurant_id'=>$restroDetail->id])->where('categories_lang.lang', App::getLocale())->groupBy('products.category_id')->with(array('dishes' => function ($query) use ($restroDetail) {
                        $query->where('products.restaurant_id', $restroDetail->id);
                    }))->get();

                    if ($productsCategory) {

                        foreach ($productsCategory as $k => $v) {
                            $category_arr[] = $v->name;
                        }
                    }

                    if (!empty($category_arr)) {
                        // $value->restroList['category'] = implode(", ", $category_arr);
                        $restroDetail['category'] = implode(", ", $category_arr);

                    } else {
                        $restroDetail['category'] = '';
                    }

                    // $restroDetail['productsCategory'] = $productsCategory;
                }

                foreach ($getOrderProducts as $key => $value) {
                    // $totalAmount +=$data->amount * $data->qty;
                    $productDetail = Products::where(['products.id'=>$value->product_id])->first();
                    $totalAmount += $value->amount;
                    $totalQty += $value->qty;
                    $totalKiloPoints += $value->points;

                    $product_toppings = OrderToppings::select('product_attribute_values_id','attribute_value_name','price')->where('order_detail_id',$value->id)->get();

                    if (count($product_toppings)) {
                        $value->is_topping = 1;

                    } else {
                        $value->is_topping = 0;
                    }
                    $value->products = $productDetail;
                    $value->products->toppings = $product_toppings;
                }
                $response['status'] = 1;
                $response['data']['items'] = $getOrderProducts;
                $response['data']['restroDetail'] = $restroDetail;
                $response['data']['totalQty'] =  $totalQty;
                $response['data']['totalAmount'] =  $totalAmount;
                $response['data']['address_id'] =  $query->address_id;
                $response['data']['order_type'] =  $query->order_type;
                $response['data']['reasion'] =  $query->reasion;
                $response['data']['discount_code'] = $query->discount_code;
                $response['data']['discount_type'] = $query->discount_type;
                $response['data']['discount_amount'] = $query->discount_amount;
                $response['data']['shipping_charges'] = $query->shipping_charges;
                $response['data']['tax_amount'] = $query->tax_amount;
                $response['data']['totalKiloPoints'] =  (string)$totalKiloPoints;
                $response['data']['order_status'] =  $query->order_status;
                $response['data']['pick_type'] =  $query->pick_type;
                $response['data']['pick_datetime'] =  $query->pick_datetime;
                $response['data']['address'] =  $query->address;
                $response['data']['landmark'] =  $query->landmark;
                $response['data']['building_name'] =  $query->building_name;
                $response['data']['building_number'] =  $query->building_number;
                $response['data']['address_type'] =  $query->address_type;
                $response['data']['created_at'] =  $query->created_at;
                $response['data']['is_rate'] =  $query->is_rate;
                $response['data']['pdf'] =  $query->pdf;
                $response['data']['pickup_option'] =  $query->pickup_option;
                $response['data']['car_color'] =  $query->car_color;
                $response['data']['car_number'] =  $query->car_number;
                $response['data']['car_brand'] =  $query->car_brand;
                $response['data']['dine_option'] =  $query->dine_option;
                $response['data']['is_arrived'] =  $query->is_arrived;
                $response['data']['is_edit_order'] =  $query->is_edit_order;
                $response['data']['payment_type'] =  $query->payment_type;
                $response['data']['restaurant_table_id'] =  $query->restaurant_table_id;
                $response['data']['table_code'] =  $query->table_code;
                $response['data']['main_category_id'] =  $query->main_category_id;

                if ($query->user_id == $userId) {
                    $response['data']['is_your_order'] =  'Yes';

                } else {
                    $response['data']['is_your_order'] =  'No';
                }

                /*if ($query->order_type == 3) {
                    $googleData = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$restroDetail->latitude.','.$restroDetail->longitude.'&destinations='. $input['latitude'].','. $input['longitude'].'&key=AIzaSyDVBREanDQF283-XQcI-vWGu3FCUVaz9C8');
                    $googleData = json_decode($googleData);

                    if ($googleData->rows[0]->elements[0]->status == 'OK'){
                        $metter = $googleData->rows[0]->elements[0]->distance->value ?? 0;  
                        $total_km = ($metter/1000);
                        $shipping_charges = $total_km * $restroDetail->delivery_charges_per_km;
                        $response['data']['shipping_charges'] = number_format($shipping_charges, 2);

                    } else {
                        $response['data']['shipping_charges'] = 0;
                    }

                } else {
                    $response['data']['shipping_charges'] = 0;
                }*/
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Invalid order id.';
                return response()->json($response, 200);
            }
        }
    }

    public function customer_arrive(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
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
            $query = Orders::where(['id'=>$input['order_id']])->first();

            if ($query) {
                Orders::where(['id'=>$input['order_id']])->update(['is_arrived' => 1]);
                $restroDetail = Restaurant::where(['id'=>$query->restaurant_id])->first();

                //Panel Notification data
                $panelNotificationData = new PanelNotifications;
                $panelNotificationData->user_id = $query->restaurant_id;
                $panelNotificationData->order_id = $input['order_id'];
                $panelNotificationData->user_type = 4;
                $panelNotificationData->notification_for = 'Customer-Arrived';
                $panelNotificationData->notification_type = 3;
                $panelNotificationData->title = 'Customer Arrived';

                if ($restroDetail->main_category_id == 2) {
                    $panelNotificationData->message = 'Customer has been arrived on restaurant to get their order, Order No. #'.$query->random_order_id;

                } else {
                    $panelNotificationData->message = 'Customer has been arrived on store to get their order, Order No. #'.$query->random_order_id;
                }
                
                if ($panelNotificationData->save()) {
                    $panelData = PanelNotifications::select('panel_notifications.*','orders.random_order_id')->leftJoin('orders','orders.id','=','panel_notifications.order_id');
                    $adminCount = 0;
                    $restroCount = 0;

                    if ($query->restaurant_id) {
                        $panelData->where('panel_notifications.user_id', $query->restaurant_id);
                        $restroCount = $panelData->where('panel_notifications.is_read', 0)->count();
                    }
                    $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$query->restaurant_id."/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
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
                        "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$query->restaurant_id."/0",
                        "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                      ),
                    ));

                    $responseNew = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                      // echo "cURL Error #:" . $err;
                    } else {
                      // echo $responseNew;
                    }

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

    public function rate_restro(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
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
            $getRestro = OrdersDetails::select('products.restaurant_id')->where(['order_details.order_id'=>$input['order_id'], 'order_details.user_id'=>$userId])->join('products','products.id','=','order_details.product_id')->first();

            if ($getRestro) {
                $checkAlreadyRated = Rating::where(['order_id'=>$input['order_id'], 'user_id'=>$userId])->first();

                if ($checkAlreadyRated) {
                    $response['status'] = 0;
                    $response['message'] = 'You already rated this order.';
                    return response()->json($response, 200);

                } else {
                    $data = new Rating();
                    $data->user_id = $userId;
                    $data->order_id = $input['order_id'];
                    $data->restaurant_id = $getRestro->restaurant_id;
                    $data->rating = $input['rating'];

                    if (isset($input['reveiw'])) {
                        $data->reveiw = $input['reveiw'];
                    }

                    if ($data->save()) {
                        $response['status'] = 1;
                        $response['message'] = 'Thank you for your valuable feedback.';
                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Rating not submit.';
                        return response()->json($response, 200);
                    }
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Error Occured.';
                return response()->json($response, 200);
            }
        }
    }

    public function rate_product(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'reveiw' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $getProduct = OrdersDetails::select('products.restaurant_id','order_details.product_id')->where(['order_details.product_id'=>$input['product_id'], 'order_details.user_id'=>$userId])->join('products','products.id','=','order_details.product_id')->first();

            if ($getProduct) {
                $checkAlreadyRated = Rating::where(['product_id'=>$input['product_id'], 'user_id'=>$userId])->first();

                if ($checkAlreadyRated) {
                    $response['status'] = 0;
                    $response['message'] = 'You already rated this product.';
                    return response()->json($response, 200);

                } else {
                    $data = new Rating();
                    $data->user_id = $userId;
                    $data->product_id = $input['product_id'];

                    if (isset($input['reveiw'])) {
                        $data->reveiw = $input['reveiw'];
                    }

                    if ($data->save()) {
                        $response['status'] = 1;
                        $response['message'] = 'Thank you for your valuable feedback.';
                        return response()->json($response, 200);

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Rating not submit.';
                        return response()->json($response, 200);
                    }
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Error Occured.';
                return response()->json($response, 200);
            }
        }
    }

    public function orderRatingList(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $rateList = Rating::where(['order_id'=>$input['order_id']])->with('user')->get();

            if (count($rateList)) {
                $response['status'] = 1;
                $response['data'] = $rateList;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Rating not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function productRatingList(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $rateList = Rating::where(['product_id'=>$input['product_id']])->with('user')->get();

            if (count($rateList)) {
                $response['status'] = 1;
                $response['data'] = $rateList;
                return response()->json($response, 200);

            } else {
                $response['status'] = 0;
                $response['message'] = 'Rating not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function cancel_order(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
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
            /*$response['status'] = 0;
            $response['message'] = "This service is removed now.";
            return response()->json($response, 200);*/

            $order_detail = Orders::where(['id'=>$input['order_id'], 'user_id'=>$userId])->first();

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
                            $walletData->amount = $order_detail->amount+$order_detail->shipping_charges+$order_detail->tax_amount;
                            $walletData->comment = 'Your order cancelled, ORD-'.$order_detail->random_order_id;
                            $walletData->save();
                        }

                        //Notification data
                        $notificationData = new Notification;
                        $notificationData->user_id = $userId;
                        $notificationData->order_id = $input['order_id'];
                        $notificationData->user_type = 2;
                        $notificationData->notification_type = 3;
                        $notificationData->notification_for = 'Order-Cancel';
                        $notificationData->title = 'Order Cancel';
                        $notificationData->message = 'Order #'.$order_detail->random_order_id.' is cancelled by you.';
                        $notificationData->save();

                        //Panel Notification data
                        $panelNotificationData = new PanelNotifications;
                        $panelNotificationData->user_id = $order_detail->restaurant_id;
                        $panelNotificationData->order_id = $input['order_id'];
                        $panelNotificationData->user_type = 4;
                        $panelNotificationData->notification_for = 'Order-Cancel';
                        $panelNotificationData->notification_type = 3;
                        $panelNotificationData->title = 'Order Cancel';
                        $panelNotificationData->message = 'Order #'.$order_detail->random_order_id.' is cancelled by customer.';
                        
                        if ($panelNotificationData->save()) {
                            $panelData = PanelNotifications::select('panel_notifications.*','orders.random_order_id')->leftJoin('orders','orders.id','=','panel_notifications.order_id');
                            $adminCount = 0;
                            $restroCount = 0;

                            if ($order_detail->restaurant_id) {
                                $panelData->where('panel_notifications.user_id', $order_detail->restaurant_id);
                                $restroCount = $panelData->where('panel_notifications.is_read', 0)->count();
                            }
                            $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                              CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$order_detail->restaurant_id."/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
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
                                "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$order_detail->restaurant_id."/0",
                                "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                              ),
                            ));

                            $responseNew = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);

                            if ($err) {
                              // echo "cURL Error #:" . $err;
                            } else {
                              // echo $responseNew;
                            }

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

                        send_notification(1, $userId, 'Order Cancel', array('title'=>'Order Cancel','message'=>$notificationData->message,'type'=>'Dish','key'=>'event'));
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

    public function reorder(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
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
            $order_detail = Orders::where(['id'=>$input['order_id'], 'user_id'=>$userId])->first();

            if ($order_detail) {
                //old cart destroy for the user
                $cartDataIds = Cart::where('user_id',$userId)->pluck('id')->toArray();

                if ($cartDataIds) {
                    CartParent::where('user_id',$userId)->delete();
                    Cart::where('user_id',$userId)->delete();
                    CartDetail::whereIn('cart_id',$cartDataIds)->delete();
                }
                //New cart parent created here only
                $cartParent = new CartParent();
                $cartParent->user_id = $order_detail->user_id;
                $cartParent->restaurant_id = $order_detail->restaurant_id;

                if ($cartParent->save()) {
                    $getOrderProducts = OrdersDetails::where(['order_id'=>$order_detail->id])->get();

                    foreach ($getOrderProducts as $key => $value) {
                        $productDetail = Products::where(['products.id'=>$value->product_id])->first();
                        $totalAmount += $productDetail->price * $value->qty;

                        $cart = new Cart();
                        $cart->user_id = $order_detail->user_id;
                        $cart->parent_cart_id = $cartParent->id;
                        $cart->product_id = $value->product_id;
                        $cart->qty = $value->qty;
                        $cart->amount = $productDetail->price * $value->qty;
                        $cart->product_price = $productDetail->price;
                        $cart->points = $productDetail->points * $value->qty;

                        if ($cart->save()) {
                            $dish_toppings = OrderToppings::where(['order_detail_id'=>$value->id])->get();

                            if ($dish_toppings) {

                                foreach ($dish_toppings as $k => $v) {
                                    /*$toppingDetail = Topping::where(['id'=>$v->dish_topping_id])->first();

                                    if ($toppingDetail) {
                                        $cartDetailData = new CartDetail();
                                        $cartDetailData->parent_cart_id = $cartParent->id;
                                        $cartDetailData->cart_id = $cart->id;
                                        $cartDetailData->dish_topping_id = $v->dish_topping_id;
                                        $cartDetailData->topping_name = $toppingDetail->topping_name;
                                        $cartDetailData->price = $toppingDetail->price;
                                        $cartDetailData->save();

                                        //add topping price in total amount
                                        $totalAmount += $toppingDetail->price;
                                    }*/
                                    $attributeValues = ProductAttributeValues::select('product_attributes.*', 'attribute_value_lang.name as attribute_value_name', 'attribute_value_lang.id as attribute_value_lang_id','product_attribute_values.id as product_attribute_values_id','product_attribute_values.attributes_lang_id')->where(['product_attribute_values.id' => $v->product_attribute_values_id])->join('product_attributes', 'product_attributes.id', '=', 'product_attribute_values.product_attributes_id')->join('attribute_value_lang', 'attribute_value_lang.id', '=', 'product_attribute_values.attribute_value_lang_id')->first();

                                    if ($attributeValues) {
                                        $cartDetailData = new CartDetail();
                                        $cartDetailData->parent_cart_id = $cartParent->id;
                                        $cartDetailData->cart_id = $cart->id;
                                        $cartDetailData->product_attribute_values_id = $v->product_attribute_values_id;
                                        $cartDetailData->attributes_lang_id = $attributeValues->attributes_lang_id;
                                        $cartDetailData->attribute_value_lang_id = $attributeValues->attribute_value_lang_id;
                                        $cartDetailData->attribute_value_name = $attributeValues->attribute_value_name;
                                        $cartDetailData->price = $attributeValues->price;
                                        $cartDetailData->save();

                                        //add topping price in total amount
                                        $totalAmount += $attributeValues->price;
                                    }
                                }
                            }

                        }
                    }

                    if ($totalAmount > 0) {
                        $cartParentData = CartParent::where('id', $cartParent->id)->firstOrFail();

                        $inpCartParent = [
                            'amount'=>$totalAmount,
                            'org_amount'=>$totalAmount,
                        ];
                        $cartParentData->update($inpCartParent);
                    }

                    $response['status'] = 1;
                    $response['message'] = 'Your order is added in cart.';

                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Error Occured in cart parent.';
                }

            } else {
                $response['status'] = 0;
                $response['message'] = 'Order not found.';
            }
            return response()->json($response, 200);
        }
    }

   public function getChefLatAndLong($serachData){


    $latitude = $serachData['latitude'];
    $longitude = $serachData['longitude'];
    $radius = 10;
    $Chef = User::selectRaw("id ,
                        ( 6371 * acos( cos( radians(?) ) *
                        cos( radians( latitude ) )
                        * cos( radians( longitude ) - radians(?)
                        ) + sin( radians(?) ) *
                        sin( radians( latitude ) ) )
                        ) AS distance", [$latitude, $longitude, $latitude])
            ->whereHas('ProductAssignTOChef', function($query) use ($serachData){
                $query->where('product_id',$serachData['product_id']);
                if(isset($serachData['chef_id'])){
                    $query->whereIn('chef_id',$serachData['chef_id']);
                }
            })
            ->where('status', '=', 1)
            ->having("distance", "<", $radius)
            ->orderBy("distance",'asc')
            ->pluck('id')->toArray();
        //$chefData = $Chef->makeHidden(['distance','total_dish','designation','country']);
        return $Chef;

    }

    public function payByOther(Request $request){
        $userData = auth()->user();
        $userId = $userData->id;
        $input = $request->all();
        $avlBal = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = CartParent::where(['user_id'=>$userId, 'id'=>$input['parent_cart_id']])->first();

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
                    $user = User::where(['country_code'=>$input['country_code'],'mobile'=>$input['number'],'type'=>0])->first();

                    if ($user) {
                        $query->payment_type = $input['payment_method'];
                        $query->paybyother_name = $input['name'];
                        $query->paybyother_country_code = $input['country_code'];
                        $query->paybyother_number = $input['number'];

                        if ($query->update()) {
                            $payment_link = url('dish/payByOther/'.$query->id);
                            $notificationData = new Notification;
                            $notificationData->user_id = $user->id;
                            $notificationData->parent_cart_id = $input['parent_cart_id'];
                            $notificationData->notification_for = 'Pay-By-Other';
                            $notificationData->user_type = 2;
                            $notificationData->notification_type = 3;
                            $notificationData->title = 'Dish Payment By Other';
                            $notificationData->message = 'Your frined request you to pay dish order payment.';
                            $notificationData->payment_link = $payment_link;
                            $notificationData ->save();
                            send_notification(1, $user->id, 'Dish Payment By Other', array('title'=>'Dish Payment By Other','message'=>$notificationData->message,'type'=>'Dish','key'=>'event'));

                            $response['status'] = 1;
                            $response['message'] = 'Request send successfully.';

                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Error Occured.';
                        }

                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'This user is not register and SMS service not available now so please check with registered user only.';
                    }
                    return response()->json($response, 200);
                } 

            } else {
                $response['status'] = 0;
                $response['message'] = 'Cart not found.';
                return response()->json($response, 200);
            }
        }
    }

    public function payByOtherPaymentStatus(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $validator = Validator::make($request->all(), [
            'parent_cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors     =   $validator->errors();
            $response['status'] = 0;
            $response['message'] = $errors;
            return response()->json($response, 200);

        } else {
            $query = CartParent::where(['user_id'=>$userId, 'id'=>$input['parent_cart_id']])->first();

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

    public function cancelReasion(Request $request){
        $userData = auth()->user();
        $userId =  $userData->id;
        $input = $request->all();
        $reduceAmount = 0;
        $status = 0;
        $message = '';

        $reasions = OrderCancelReasions::where(['status'=>1])->get();

        if (count($reasions)) {
            $response['status'] = 1;
            $response['data'] = $reasions;
            $response['message'] = 'Record found.';
            return response()->json($response, 200);

        } else {
            $response['status'] = 0;
            $response['message'] = 'Reasion not found.';
            return response()->json($response, 200);
        }
    }

}