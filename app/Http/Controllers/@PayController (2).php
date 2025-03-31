<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Orders;
use App\Models\Cart;
use App\Models\CartParent;
use App\Models\CartDetail;
use App\Models\GiftCart;
use App\Models\Discount;
use App\Models\GiftCartDetail;
use App\Models\Products;
use App\Models\Restaurant;
use App\Models\OrdersDetails;
use App\Models\UserKiloPoints;
use App\Models\GiftNotification;
use App\Models\Notification;
use App\Models\GiftOrder;
use App\Models\GiftOrderDetails;
use App\Models\OrderToppings;
use App\Models\CartSplitBills;
use App\Models\CartSplitBillProduct;
use App\Models\Content;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\QueryDataTable;
use DB;

class PayController extends Controller
{
    public function __construct() {
	}
    
    public function payByOther($id)
    {
        $id = $id;
        $cartParent = CartParent::select('cart_parent.cart_status as order_status','users.name as user_name','users.mobile as user_mobile', 'cart_parent.shipping_charges  as orders_shipping_charges','cart_parent.shipping_charges as shipping_charges','cart_parent.payment_type as payment_type','cart_parent.cart_status as cart_status',
         'users.country_code','users.email as user_email','user_address.address as user_address','user_address.latitude','user_address.longitude','cart_parent.created_at','cart_parent.id','cart_parent.amount')
                    ->leftjoin('users','users.id','=','cart_parent.user_id')
                    ->leftjoin('user_address','user_address.id','=','cart_parent.address_id')
                    ->where('cart_parent.id', $id)
                    ->where('cart_parent.payment_type', 'Pay-By-Other')
                    ->first();

        $data['orders']= $cartParent;
        // dd($cartParent);

        if (isset($cartParent->id)) {
            $data['orders_details'] = Cart::select('cart.*','products.*')
            ->join('products','products.id','=','cart.product_id')
            ->where('cart.parent_cart_id',$cartParent->id)
            ->get();
            return view('paybyother', $data);  

        } else {
            return view('notfound');
        }
    } 

    public function payByOthergift($id)
    {
        $id = $id;
        $cartParent = GiftCart::select('gift_cart.user_id','gift_cart.address as user_address','gift_cart.status','gift_cart.shipping_charges as orders_shipping_charges','gift_cart.shipping_charges as shipping_charges','gift_cart.payment_type as payment_type','gift_cart.created_at','gift_cart.id','gift_cart.points')
                    ->where('gift_cart.id', $id)
                    ->where('gift_cart.payment_type', 'Pay-By-Other')
                    ->first();

            // echo "<pre>"; print_r($cartParent);die;
        if ($cartParent) {
            $user_data = User::where('id', $cartParent->user_id)->first();

            if ($user_data) {
                $cartParent['user_name'] = $user_data->name;
                $cartParent['user_mobile'] = $user_data->mobile;
                $cartParent['country_code'] = $user_data->country_code;
                $cartParent['user_email'] = $user_data->email;
            }

        }

        $data['orders']= $cartParent;
        // dd($cartParent);

        if (isset($cartParent->id)) {
            $data['orders_details'] = GiftCartDetail::select('gift_cart_details.*','gifts.name')
            ->join('gifts','gifts.id','=','gift_cart_details.gift_id')
            ->where('gift_cart_details.gift_cart_id',$cartParent->id)
            ->get();
            return view('paybyothergift', $data);  

        } else {
            return view('notfound');
        }
    }

    public function thankyou()
    {
        return view('thankyou');
    }

    public function purchaseGift($id)
    {
        $query = GiftCart::where(['id'=>$id])->first();
        $url = url('gift/payByOther').'/'.$id;
        $urlData = GiftNotification::where('payment_link', $url)->first();

        if ($urlData) {
            $urlData->payment_link = null;
            $urlData->update();
        }die('here');  

        if ($query) {
            $orderdata = new GiftOrder;
            $orderdata->user_id = $query->user_id;
            $orderdata->shipping_charges = $query->shipping_charges;
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
            $orderdata->is_wallet_use = 'No';
            $orderdata->payment_type = 'Pay-By-Other';
            $orderdata->order_status = 'Pending';
            $orderdata->wallet_amount_used = $query->wallet_amount_used;

            if ($orderdata->save()) {
                $cartGiftData = GiftCartDetail::where('gift_cart_id', $id)->get();

                if(count($cartGiftData)) {

                    foreach($cartGiftData as $key => $value) {
                        $orderdetailData = new GiftOrderDetails;
                        $orderdetailData->user_id = $query->user_id;
                        $orderdetailData->gift_order_id = $orderdata->id;
                        $orderdetailData->gift_id = $value->gift_id;
                        $orderdetailData->qty = $value->qty;
                        $orderdetailData->points = $value->points;
                        $orderdetailData->gift_varient_id = $value->gift_varient_id;
                        $orderdetailData->varient_name = $value->varient_name;
                        $orderdetailData->save();
                    }
                }

                //insert In KiloPointsDB
                $userKiloPointsNewDB = new UserKiloPoints;
                $userKiloPointsNewDB->order_id = $orderdata->id;
                $userKiloPointsNewDB->user_id = $query->user_id;
                $userKiloPointsNewDB->points = $query->points;
                $userKiloPointsNewDB->type = 'DR';
                $userKiloPointsNewDB->comment = 'Gift #'.$orderdata->id.' Purchased.';
                $userKiloPointsNewDB->setConnection('mysql2');
                $userKiloPointsNewDB->save();

                $notificationData = new GiftNotification;
                $notificationData->user_id = $query->user_id;
                $notificationData->order_id = $orderdata->id;
                $notificationData->user_type = 2;
                $notificationData->notification_type = 3;
                $notificationData->notification_for = 'Gift Buy';
                $notificationData->title = 'Gift Purchased';
                $notificationData->message = 'Gift #'.$orderdata->id.' Purchased Successfully.';
                $notificationData ->save();
                send_notification(1, $query->user_id, 'Gift Purchased', array('title'=>'Gift Purchased','message'=>$notificationData->message,'type'=>'Gift','key'=>'event'));

                GiftCart::where('id', $id)->delete();
                GiftCartDetail::where('gift_cart_id', $id)->delete();
                $url = url('gift/payByOther').'/'.$id;
                $urlData = GiftNotification::where('payment_link', $url)->first();

                if ($urlData) {
                    $urlData->payment_link = null;
                    $urlData->update();
                }

                $result['status'] = 1;
                $result['message'] = 'Order Placed Successfully.';

            } else {
                $result['status'] = 0;
                $result['message'] = 'Error Occured.';
            }

        } else {
            $result['message'] = 'Invalid request.';
            $result['status'] = 0;
        }
         return response()->json($result);
    }

    public function purchaseDish($id)
    {
        $cartParentDetail = CartParent::where(['id'=>$id])->first();
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($cartParentDetail) {
            $restroDetail = Restaurant::where(['id'=>$cartParentDetail->restaurant_id])->first();
            $discount_type = Discount::select('category_type')->where(['discount_code'=>$cartParentDetail->discount_code])->first();

            $orderData = new Orders();
            $orderData->user_id = $cartParentDetail->user_id;
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
            $orderData->order_status = 'Accepted';
            $orderData->payment_type = 'Pay-By-Other';
            $orderData->spilt_bill_count = $cartParentDetail->spilt_bill_count;
            $orderData->split_type = $cartParentDetail->split_type;
            $orderData->restaurant_table_id = $cartParentDetail->restaurant_table_id;
            $orderData->table_code = $cartParentDetail->table_code;
            $orderData->is_wallet_use = 'No';

            if ($orderData->save()) {
                //order detail insert
                $cartProductData = Cart::where(['parent_cart_id'=>$id])->get();

                if (count($cartProductData)) {

                    foreach ($cartProductData as $key => $value) {
                        $orderDetailData = new OrdersDetails();
                        $orderDetailData->order_id = $orderData->id;
                        $orderDetailData->product_id  = $value->product_id;
                        $orderDetailData->user_id   = $cartParentDetail->user_id;
                        $orderDetailData->qty   = $value->qty;
                        $orderDetailData->amount   = $value->amount;
                        $orderDetailData->product_price   = $value->product_price;
                        $orderDetailData->points   = $value->points;

                        if ($orderDetailData->save()) {
                            $cartToppingData = CartDetail::where(['cart_id'=>$value->id])->get();

                            if (count($cartToppingData)) {

                                /*foreach ($cartToppingData as $k => $v) {
                                    $orderToppingData = new OrderToppings();
                                    $orderToppingData->order_detail_id = $orderDetailData->id;
                                    $orderToppingData->dish_topping_id  = $v->dish_topping_id;
                                    $orderToppingData->topping_name   = $v->topping_name;
                                    $orderToppingData->price   = $v->price;
                                    $orderToppingData->save();
                                }*/

                                foreach ($cartToppingData as $k => $v) {
                                    $orderToppingData = new OrderToppings();
                                    $orderToppingData->order_detail_id = $orderDetailData->id;
                                    $orderToppingData->product_attribute_values_id = $v->product_attribute_values_id;
                                    $orderToppingData->attributes_lang_id = $v->attributes_lang_id;
                                    $orderToppingData->attribute_value_lang_id = $v->attribute_value_lang_id;
                                    $orderToppingData->attribute_value_name = $v->attribute_value_name;
                                    $orderToppingData->price   = $v->price;
                                    $orderToppingData->save();
                                }
                            }
                        }
                    }
                }
                
                $getTotalPoints = OrdersDetails::where('order_id', $orderData->id)->sum('points');
                //insert in user_kilo_points table
                $userPoints = new UserKiloPoints;
                $userPoints->order_id = $orderData->id;
                $userPoints->user_id = $cartParentDetail->user_id;
                $userPoints->points = $getTotalPoints;
                $userPoints->save();

                //insert In KiloPointsDB
                $userKiloPointsNewDB = new UserKiloPoints;
                $userKiloPointsNewDB->order_id = $orderData->id;
                $userKiloPointsNewDB->user_id = $cartParentDetail->user_id;
                $userKiloPointsNewDB->points = $getTotalPoints;
                $userKiloPointsNewDB->comment = 'Order #'.$orderData->id.' placed.';
                $userKiloPointsNewDB->type = 'CR';
                $userKiloPointsNewDB->setConnection('mysql2');
                $userKiloPointsNewDB->save();

                //Payer Notification Data
                $getPayerData = User::where(['country_code'=>$cartParentDetail->paybyother_country_code,'mobile'=>$cartParentDetail->paybyother_number,'type'=>0])->first();

                if ($getPayerData) {
                    //Notification data
                    $notificationPayerData = new Notification;
                    $notificationPayerData->user_id = $getPayerData->id;
                    $notificationPayerData->order_id = $orderData->id;
                    $notificationPayerData->user_type = 2;
                    $notificationPayerData->notification_for = 'Order-Placed';
                    $notificationPayerData->notification_type = 3;
                    $notificationPayerData->title = 'Order Placed';
                    $notificationPayerData->message = 'Order #'.$orderData->random_order_id.' placed successfully.';
                    $notificationPayerData->save();
                    send_notification(1, $getPayerData->id, 'Order Placed', array('title'=>'Order Placed','message'=>$notificationPayerData->message,'type'=>'Dish','key'=>'event'));
                    //End Notification
                }
                

                //Notification data
                $notificationData = new Notification;
                $notificationData->user_id = $cartParentDetail->user_id;
                $notificationData->order_id = $orderData->id;
                $notificationData->user_type = 2;
                $notificationData->notification_for = 'Order-Placed';
                $notificationData->notification_type = 3;
                $notificationData->title = 'Order Placed';


                if ($cartParentDetail->order_type == 1) {
                    $notificationData->message = 'Order #'.$orderData->random_order_id.' is going to ready and serve you on table soon.';

                } else if ($cartParentDetail->order_type == 2) {
                    $pickupText = '';

                    if ($orderData->pick_type == 'now') {
                        $pickupText = ' now from Restaurant.';

                        if ($orderData->car_number && !empty($orderData->car_number)) {
                            $pickupText = ' now in your Car.';
                        }

                    } else if ($orderData->pick_type == 'later') {
                        $pickupText = ' later from Restaurant.';

                        if ($orderData->car_number && !empty($orderData->car_number)) {
                            $pickupText = ' later in your Car.';
                        }

                    } else {
                        $pickupText = ' from Restaurant.';

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


                /*if ($cartParentDetail->order_type == 1) {
                    $notificationData->message = 'Order #'.$orderData->id.' is going to ready and serve you on table soon.';

                } else if ($cartParentDetail->order_type == 2) {
                    $notificationData->message = 'Order #'.$orderData->id.' Pick-up at your car or the cafe door.';

                } else if ($cartParentDetail->order_type == 3) {
                    $notificationData->message = 'Order #'.$orderData->id.' ready to delivery soon.';

                } else {
                    $notificationData->message = 'Order #'.$orderData->id.' placed successfully.';
                }*/
                $notificationData->save();
                send_notification(1, $cartParentDetail->user_id, 'Order Placed', array('title'=>'Order Placed','message'=>$notificationData->message,'type'=>'Dish','key'=>'event'));
                //End Notification

                CartParent::where(['id'=>$id])->delete();
                Cart::where(['parent_cart_id'=>$id])->delete();
                CartDetail::where(['parent_cart_id'=>$id])->delete();
                CartSplitBills::where(['parent_cart_id'=>$id])->delete();
                CartSplitBillProduct::where(['parent_cart_id'=>$id])->delete();

                $url = url('dish/payByOther').'/'.$id;
                $urlData = Notification::where('payment_link', $url)->first();

                if ($urlData) {
                    $urlData->payment_link = null;
                    $urlData->update();
                }

                $result['status'] = 1;
                $result['message'] = 'Order placed successfully.';

            } else {
                $result['status'] = 0;
                $result['message'] = 'Error Occured.';
            }

        } else {
            $result['message'] = 'Invalid request.';
            $result['status'] = 0;
        }
         return response()->json($result);
    }

    public function page($slug) {
        $data = array();
        $data['title'] = '404 No Data Found';
        $data['content'] = [];

        if ($slug == 'privacy-policy') {
            $data['title'] = 'Privacy Policy';
            $data['content'] = Content::where('slug','private-policy')->first();

        } else if ($slug == 'terms-of-use') {
            $data['title'] = 'Terms and Conditions';
            $data['content'] = Content::where('slug','terms-of-use')->first();
        }
        // dd($data);
        return view('staticpage',$data);
    }
}
