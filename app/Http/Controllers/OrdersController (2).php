<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Courts;
use App\Models\OrdersDetails;
use App\Models\OrderCancelReasions;
use App\Models\Notification;
use App\Models\PanelNotifications;
use App\Models\UserWallets;
use App\Models\OrderBookingAmountLogs;
use App\Models\DeliveryPrice;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\QueryDataTable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkOrderExport;
use App\Models\CourtBooking;
use DB;
use App\Models\EmailTemplateLang;
use App\Models\Facility;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*changeKPTransferStatus();
        changeRestaurantStatus();*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $status = null)
    {
        //dd($status);
        Gate::authorize('Order-section');
        $login_user_data = auth()->user();
        $columns = ['orders.created_at'];
        $locale = App::getLocale();
        if($locale == null){
            $locale = 'en';
        }
        // $orders=Orders::select('orders.*', 'courts.court_name')->join('courts', 'courts.id','=','orders.court_id');
        $orders = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
            ->join('court_booking_slots', 'court_booking_slots.court_booking_id', 'court_booking.id')
            ->join('users', 'users.id', 'court_booking.user_id')
            ->join('facilities', 'facilities.id', 'courts.facility_id')
            ->join('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
            ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
            ->select(
                'court_booking.*',
                'courts_lang.court_name',
                'courts.image as court_image',
                'courts.latitude',
                'courts.longitude',
                'courts.address',
                'court_booking_slots.booking_start_time',
                'court_booking_slots.booking_end_time',
                //'court_booking.booking_date',
                //'court_booking.booking_type',
                'users.name as user_name',
                'users.country_code',
                'users.mobile',
                'users.email as user_email',
                'facilities_lang.name as facility_name'
            );
        $login_user = auth()->user();
        if ($login_user->type == '0') {
            $orders = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
                ->join('court_booking_slots', 'court_booking_slots.court_booking_id', 'court_booking.id')
                ->join('users', 'users.id', 'court_booking.user_id')
                ->join('facilities', 'facilities.id', 'courts.facility_id')
                ->join('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
                ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
                ->select(
                    'court_booking.*',
                    'courts_lang.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                    'court_booking_slots.booking_start_time',
                    'court_booking_slots.booking_end_time',
                    //'court_booking.booking_type',
                    'users.name as user_name',
                    'users.country_code',
                    'users.mobile',
                    'users.email as user_email',
                    'facilities_lang.name as facility_name')
                ->where(['facilities_lang.lang'=>$locale,'courts_lang.lang'=>$locale,'court_booking.is_deleted'=> 0]);
        } else {
            $orders = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
                ->join('court_booking_slots', 'court_booking_slots.court_booking_id', 'court_booking.id')
                ->join('users', 'users.id', 'court_booking.user_id')
                ->join('facilities', 'facilities.id', 'courts.facility_id')
                ->join('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
                ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
                ->select(
                    'court_booking.*',
                    'courts_lang.court_name',
                    'courts.image as court_image',
                    'courts.latitude',
                    'courts.longitude',
                    'courts.address',
                    'court_booking_slots.booking_start_time',
                    'court_booking_slots.booking_end_time',
                    //'court_booking.booking_type',
                    'users.name as user_name',
                    'users.country_code',
                    'users.mobile',
                    'users.email as user_email',
                    'facilities_lang.name as facility_name'
                )
                ->where(['courts.facility_owner_id'=> $login_user->id,'court_booking.is_deleted'=> 0,'facilities_lang.lang'=>$locale,'courts_lang.lang'=>$locale]);
        }
        if ($status) {
            $orders->where('court_booking.order_status', $status);
        }
        $orders->groupBy('court_booking.id');
        /*$orders=Orders::select('orders.order_status','users.name as user_name','user_address.address as user_address','orders.amount','orders.created_at','orders.id as order_id')
        ->selectRaw('products.name as product_name')
        ->leftjoin('order_details','orders.id','=','order_details.order_id')
        ->leftjoin('products','order_details.product_id','=','products.id')
        ->leftjoin('users','users.id','=','orders.user_id')
        ->leftjoin('user_address','user_address.id','=','orders.address_id')
        ->leftjoin('transaction','transaction.id','=','orders.transaction_id')
        ->where('orders.order_status', $status)->groupBy('orders.id')
        ->get();*/
        /*if($chef_id != 'All'){
            $orders->where('orders.chef_id',$chef_id);
        }*/
        return Datatables::of($orders)->editColumn('created_at', function ($orders) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($orders->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->editColumn('booking_date', function ($orders) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($orders->booking_date);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            /*if (!is_null($request->get('name')) || $request->get('name') == 1 || $request->get('name') == 2 || $request->get('name') == 3 ) {
                    $query->where('products.name', 'like', "%{$request->get('name')}%");
                }

                if (!is_null($request->get('payment_mode'))){
                    $query->where('transaction.payment_type', '=', $request->get('payment_mode'));
                }
               
                if ($request->has('customer_id')) {
                    $customer_id = array_filter($request->customer_id);
                    if(count($customer_id) > 0) {
                        $query->whereIn('users.id', $request->get('customer_id'));
                    }
                }
                if ($request->has('restaurant_id')) {
                    $restaurant_id = array_filter($request->restaurant_id);
                    if(count($restaurant_id) > 0) {
                        $query->whereIn('orders.restaurant_id', $request->get('restaurant_id'));
                    }
                }
                */

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(court_booking.created_at)'), array($request->from_date, $request->to_date));
            }
            if ($request->has('court_id')) {
                $court_id = array_filter($request->court_id);
                if (count($court_id) > 0) {
                    $query->whereIn('court_booking.court_id', $request->get('court_id'));
                }
            }

            if (isset($request->challenge_type) && $request->challenge_type !='') {
                      $query->where('court_booking.booking_type', $request->challenge_type);
            }

            if ($request->has('order_status')) {

                if ($request->get('order_status')) {
                    $query->where('court_booking.order_status', $request->get('order_status'));
                }
            }
            if ($request->has('payment_type')) {

                if ($request->get('payment_type')) {
                    $query->where('court_booking.payment_type', $request->get('payment_type'));
                }
            }

            if ($request->has('facility_id')) {
                $facility_id = array_filter($request->facility_id);

                if (count($facility_id) > 0) {
                    $query->whereIn('facilities.id', $request->get('facility_id'));
                }
            }
            if ($request->has('player_id')) {
                $player_id = array_filter($request->player_id);

                if (count($player_id) > 0) {
                    $query->whereIn('court_booking.user_id', $request->get('player_id'));
                }
            }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');
                /*$query->having('products.name', 'like', "%{$search['value']}%");
                   $query->orHaving('users.name', 'like', "%{$search['value']}%");
                   $query->orHaving('restaurants.name', 'like', "%{$search['value']}%");
                   $query->orHaving('court_booking.id', 'like', "%{$search['value']}%");*/
                //    $query->orHaving('users.user_name', 'like', "%{$search['value']}%");
                $query->orHaving('users.name', 'like', "%{$search['value']}%");
                $query->orHaving('court_booking.order_status', 'like', "%{$search['value']}%");
                $query->orHaving('court_booking.payment_type', 'like', "%{$search['value']}%");
                $query->orHaving('court_booking.booking_date', 'like', "%{$search['value']}%");
                $query->orHaving('courts_lang.court_name', 'like', "%{$search['value']}%");

                /*if (strtolower($search['value']) == 'uae') {
                        $query->orHaving('court_booking.ordered_currency_code', 'like', "%AED%");

                    } else if (strtolower($search['value']) == 'qatar') {
                        $query->orHaving('court_booking.ordered_currency_code', 'like', "%QAR%");

                    } else {
                        $query->orHaving('court_booking.ordered_currency_code', 'like', "%{$search['value']}%");
                    }*/
            }
        })->addIndexColumn()->make(true);
    }



    public function frontend($status = null)
    {
        Gate::authorize('Order-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['courts'] = Courts::select('id', 'court_name')->where(['status' => 1])->get();
        if($login_user_data->type == 0){

            $data['facilities'] = Facility::select('name', 'id')->where(['status' => 1])->get();
            $data['players'] = User::select('name', 'mobile', 'country_code', 'id')->where(['status' => 1, 'type' => 3])->get();
        }else{
            $data['facilities'] = Facility::select('name', 'id')->where(['status' => 1,'facility_owner_id'=> $login_user_data->id])->get();
            $player_ids = CourtBooking::join('courts','courts.id','court_booking.court_id')
                ->select(
                    'court_booking.id as court_booking_id',
                    'court_booking.user_id',
                    'courts.id as court_id',
                    'courts.facility_owner_id',
                )
                ->where('courts.facility_owner_id',$login_user_data->id)
                ->pluck('user_id')->toArray();
                $data['players'] = User::select('name', 'mobile', 'country_code', 'id')->whereIn('id',$player_ids)->where(['status' => 1, 'type' => 3])->get();
        }
        // $data['products'] = Products::where('is_active',1)->get();

        $data['order_status'] = $status;
        // $data['OrderCancelReasions'] = OrderCancelReasions::where(['status'=>1])->get();
        /*if($chef_id != 'All'){
            $data['chef_id'] = $chef_id;
        }else{
            $data['chef_id'] = '';
        }*/

        return view('orders.listing', $data);
    }
    public function show_court_data($facility_id, $court_id = null)
    {

        Gate::authorize('Order-section');
        $facility_ids = explode(',', $facility_id);
        $data['record'] = Courts::Where('status', 1)->whereIn('facility_id', $facility_ids)->select('court_name', 'id')->get();
        return view('orders.court_list', $data);
    }
    public function show_restro_byMainCatIds($main_category_id)
    {

        $main_category_ids = explode(",", $main_category_id);

        if ($main_category_id) {
            $data['records'] = Restaurant::select('*')->whereIn('main_category_id', $main_category_ids)->get();
            $data['main_category_id'] = $main_category_id;
        } else {
            $data['records'] = array();
            $data['main_category_id'] = '';
        }

        return view('orders.restroFilter', $data);
    }


    // public function changeOrderStatus($id, $status)
    // {
    //     //dd($status);
    //     $result['status'] = 1;
    //     $order_details = Orders::select('orders.*', 'restaurants.main_category_id', 'restaurants.name')->where('orders.id', $id)->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->first();
    //     if (!empty($order_details)) {
    //         $vendorType = $order_details->main_category_id == 2 ? "Restaurant" : "Store";

    //         if ($status == 'Pending') {
    //             $inp = ['order_status' => 'Accepted'];
    //             $result['message'] = 'Order #' . $order_details->random_order_id . ' is Accpeted by ' . $order_details->name;
    //         } elseif ($status == 'Accepted') {
    //             $inp = ['order_status' => 'Prepare'];
    //             $result['message'] = 'Order #' . $order_details->random_order_id . ' Ready for Delivery.';
    //         } elseif ($status == 'Complete') {
    //             $inp = ['order_status' => 'Complete'];
    //             $result['message'] = 'Order #' . $order_details->random_order_id . ' is Completed by ' . $order_details->name;
    //         } else {
    //             $inp = ['order_status' => 'Cancel'];
    //             $result['message'] = 'Order #' . $order_details->random_order_id . ' is cancelled by ' . $order_details->name;
    //         }
    //         $Orders = Orders::findOrFail($id);

    //         if ($Orders->update($inp)) {
    //             /*$orders = Orders::select('orders.status as order_status','users.name as user_name','users.mobile as user_mobile', 'orders.shipping_charges  as orders_shipping_charges', 'orders.transaction_id  as transaction_id','orders.shipping_charges  as shipping_charges','orders.payment_type  as payment_type','orders.order_status  as order_status',
    //                  'users.country_code','users.email as user_email','user_address.address as user_address','user_address.latitude','user_address.longitude','orders.created_at','orders.id','orders.amount','orders.user_id')
    //                             ->leftjoin('users','users.id','=','orders.user_id')
    //                             ->leftjoin('user_address','user_address.id','=','orders.address_id')
    //                             ->leftjoin('transaction','transaction.id','=','orders.transaction_id')
    //                             ->where('orders.id', $id)
    //                             ->first();*/

    //             $orders = Orders::select('orders.status as order_status', 'users.name as user_name', 'users.mobile as user_mobile', 'orders.shipping_charges  as orders_shipping_charges', 'orders.transaction_id  as transaction_id', 'orders.shipping_charges  as shipping_charges', 'orders.payment_type  as payment_type', 'orders.order_status  as order_status', 'users.country_code', 'users.email as user_email', 'user_address.address as user_address', 'user_address.latitude', 'user_address.longitude', 'orders.created_at', 'orders.id', 'orders.amount', 'order_cancel_reasions.reasion', 'orders.random_order_id', 'orders.discount_amount', 'orders.discount_code', 'orders.tax_amount', 'orders.order_type', 'orders.pickup_option', 'orders.car_number', 'orders.car_brand', 'orders.car_color', 'orders.id as order_id')
    //                 ->leftjoin('users', 'users.id', '=', 'orders.user_id')
    //                 ->leftjoin('user_address', 'user_address.id', '=', 'orders.address_id')
    //                 ->leftjoin('transaction', 'transaction.id', '=', 'orders.transaction_id')
    //                 ->leftjoin('order_cancel_reasions', 'order_cancel_reasions.id', '=', 'orders.cancel_reasion_id')
    //                 ->where('orders.id', $id)
    //                 ->first();

    //             if ($status == 'Complete') {
    //                 $getUserDetail = User::where(['id' => $order_details->user_id])->first();

    //                 if ($getUserDetail->gift_user_id) {
    //                     $headerData = [
    //                         "accept: application/json",
    //                         "accept-language: ar",
    //                         "gift-access-key: '" . $getUserDetail->gift_access_key . "'",
    //                         "gift-secret-key: '" . $getUserDetail->gift_secret_key . "'"
    //                     ];
    //                     $apiResponse = giftApis('updateKPOrderStatus', ['order_id' => $order_details->id, 'is_kp_transfer' => 'Yes', 'platform' => 'KP'], 'Header', $headerData);
    //                 }
    //                 $data['orders'] = $orders;
    //                 /*$data['orders_details'] = OrdersDetails::select('order_details.*','products.*')
    //                         ->join('products','products.id','=','order_details.product_id')
    //                         ->where('order_details.order_id',$orders->id)
    //                         ->get();*/

    //                 $data['orders_details'] = OrdersDetails::select('order_details.*', 'order_details.points as order_product_kp', 'order_details.id as order_detail_id', 'products.*')
    //                     ->join('products', 'products.id', '=', 'order_details.product_id')
    //                     ->where('order_details.order_id', $orders->id)
    //                     ->get();

    //                 if ($data['orders_details']) {

    //                     foreach ($data['orders_details'] as $key => $value) {
    //                         $product_attrs = '';
    //                         $productAttr = OrderToppings::select('attribute_value_name')->where(['order_detail_id' => $value->order_detail_id])->pluck('attribute_value_name')->toArray();

    //                         // dd($productAttr);

    //                         if ($productAttr) {
    //                             $product_attrs = implode(", ", array_unique($productAttr));
    //                         } else {
    //                             $product_attrs = '';
    //                         }

    //                         $value->product_attrs = $product_attrs;
    //                     }
    //                 }

    //                 $options = new Options();
    //                 $options->set('defaultFont', 'Courier');
    //                 $options->set('isRemoteEnabled', TRUE);
    //                 $options->set('debugKeepTemp', TRUE);
    //                 $options->set('isHtml5ParserEnabled', TRUE);
    //                 $options->set('chroot', '/');
    //                 $options->setIsRemoteEnabled(true);

    //                 $dompdf = new Dompdf($options);
    //                 $dompdf->set_option('isRemoteEnabled', TRUE);

    //                 // $dompdf = new Dompdf();
    //                 $htmlview = view('orders.orderdetails', $data);
    //                 $dompdf->loadHtml($htmlview);
    //                 // dd($dompdf);
    //                 $dompdf->setPaper('A4', 'landscape');
    //                 $dompdf->render();
    //                 $file_name = time() . '.pdf';
    //                 $data = Orders::where(['id' => $id])->update(['pdf' => $file_name]);
    //                 $output = $dompdf->output();
    //                 $data = file_put_contents(public_path('uploads/order_detail_pdf/') . $file_name, $output);
    //             }

    //             //Notification data
    //             $notificationData = new Notification;
    //             $notificationData->user_id = $orders->user_id;
    //             $notificationData->order_id = $orders->id;
    //             $notificationData->user_type = 2;
    //             $notificationData->notification_for = 'Order-Placed';
    //             $notificationData->notification_type = 3;
    //             $notificationData->title = 'Order Status Change';
    //             $notificationData->message = $result['message'];
    //             $notificationData->save();
    //             send_notification(1, $orders->user_id, 'Order Status Change', array('title' => 'Order Status Change', 'message' => $notificationData->message, 'type' => 'Dish', 'key' => 'event'));

    //             //Send Email For Order
    //             /*$email = EmailTemplateLang::where('email_id', 5)->where('lang', 'en')->select(['name', 'subject', 'description','footer'])->first();
    //             $description = $email->description;
    //             $description = str_replace("[NAME]", $orders->user_name, $description);
    //             $description = str_replace("[ORDER_DATE]", date('d M Y', $orders->created_at), $description);
    //             $description = str_replace("[ORDER_ID]", $orders->id, $description);
    //             $description = str_replace("[ORDER_STATUS]", $orders->order_status, $description);
    //             $description = str_replace("[USERNAME]", $orders->user_name, $description);

    //             $name = $email->name;
    //             $name = str_replace("[NAME]", $orders->user_name, $name);

    //             $order_detail=(object)[];
    //             $order_detail->description = $description;
    //             $order_detail->footer = $email->footer;
    //             $order_detail->name = $name;
    //             $order_detail->subject = $email->subject;

    //             Mail::send('emails.order', compact('order_detail'), function($message)use($orders, $email) {
    //                 $message->to($orders->user_email, config('app.name'))->subject($email->subject);
    //                 $message->from('support@contactless.com',config('app.name'));
    //             });*/

    //             /*if($status == 'Complete'){
    //                 $result['message'] = 'Order is Completed successfully';
    //                 $result['status'] = 1;
    //             }else if($status == 'Accpeted'){
    //                 $result['message'] = 'Order is Accpeted successfully';
    //                 $result['status'] = 1;
    //             }else if($status == 'Cancel'){
    //                 $result['message'] = 'Order is cancelled successfully';
    //                 $result['status'] = 1;
    //             }else{
    //                 $result['message'] = 'Order is Prepare successfully';
    //                 $result['status'] = 1;
    //             }*/
    //         } else {
    //             $result['message'] = 'Order status can`t be updated!!';
    //             $result['status'] = 0;
    //         }
    //     } else {
    //         $result['message'] = 'Invaild Order!!';
    //         $result['status'] = 0;
    //     }
    //     return response()->json($result);
    // }

    public function cash_confirm($id)
    {
        //dd($status);
        $result['status'] = 1;
        $order_details = Orders::select('orders.*')->where('orders.id', $id)->first();

        if (!empty($order_details)) {
            $inp = ['order_status' => 'Accepted'];

            $Orders = Orders::findOrFail($id);

            if ($Orders->update($inp)) {
                $result['message'] = __('backend.order_status_change');
                $result['status'] = 1;
            } else {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __('backend.Something_went_wrong');
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function cancelOrderStatus(Request $request)
    {
        //dd($status);
        $result['status'] = 1;
        $order_details = Orders::find($request->order_id);

        if (!empty($order_details)) {
            $inp = ['order_status' => 'Cancel', 'cancel_reasion_id' => $request->reasion_id];
            $result['message'] = 'Order is cancelled successfully';

            $Orders = Orders::findOrFail($request->order_id);

            if (Orders::where('id', $request->order_id)->update($inp)) {

                if ($Orders->payment_type != 'Cash') {
                    $walletData = new UserWallets;
                    $walletData->user_id = $userId;
                    $walletData->transaction_type = 'CR';
                    $walletData->amount = $Orders->amount + $Orders->tax_amount + $Orders->shipping_charges;
                    $walletData->comment = 'Your order cancelled, ORD-' . $Orders->random_order_id;
                    $walletData->save();
                }

                //Notification data
                $notificationData = new Notification;
                $notificationData->user_id = $order_details->user_id;
                $notificationData->order_id = $order_details->id;
                $notificationData->user_type = 2;
                $notificationData->notification_for = 'Order-Cancel';
                $notificationData->notification_type = 3;
                $notificationData->title = 'Order Cancel';
                $notificationData->message = 'Your order is cancelled by store.';
                $notificationData->save();
                send_notification(1, $order_details->user_id, 'Order Cancel', array('title' => 'Order Cancel', 'message' => $notificationData->message, 'type' => 'Dish', 'key' => 'event'));
            } else {
                $result['message'] = 'Order status can`t be updated!!';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild Order!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function show($id)
    {
        Gate::authorize('Order-section');
        $locale = App::getLocale();

        if (!$locale) {
            $locale = 'en';
        }
        $data['record'] = CourtBooking::with('bookingChallenges.userDetails')
            ->join('courts', 'courts.id', 'court_booking.court_id')
            ->join('court_booking_slots', 'court_booking_slots.court_booking_id', 'court_booking.id')
            ->join('users', 'users.id', 'court_booking.user_id')
            ->join('facilities', 'facilities.id', 'courts.facility_id')
            ->join('facilities_lang', 'facilities.id', 'facilities_lang.facility_id')
            ->join('courts_lang', 'courts.id', 'courts_lang.court_id')
            ->select(
                'court_booking.*',
                'courts_lang.court_name',
                'courts.image as court_image',
                'courts.latitude',
                'courts.longitude',
                'courts.address',
                'court_booking_slots.booking_start_time',
                'court_booking_slots.booking_end_time',
                'users.name as user_name',
                'users.country_code',
                'users.mobile',
                'users.email as user_email',
                'users.email as user_email',
                'users.email as user_email',
                'facilities_lang.name as facility_name'
            )
            ->where(['court_booking.id'=> $id,'facilities_lang.lang'=>$locale,'courts_lang.lang'=>$locale])
            ->first();


            $data['alllogdata'] =OrderBookingAmountLogs::where('booking_id',$data['record']->id)->get();
            //print_R($data['alllogdata'][0]->id);exit;
          
            $logsResult=OrderBookingAmountLogs::where('booking_id',$data['record']->id)->first();
	            if(isset($logsResult) && $logsResult!=''){
		            $user=User::where('id',$logsResult->action_by_id)->first();
		            $data['logAdminDeductPer']=$logsResult->admin_comm_percentage;
		            $data['logAdminDeductedAmt']=$logsResult->amt_after_admin_comm_amount;
		            $data['actionByUsename']=$user->name;
		        }else{
		        	$data['logAdminDeductPer']='';
		            $data['logAdminDeductedAmt']='';
		            $data['actionByUsename']='';
		        }
        return view('orders.view', $data);
    }

    public function pdf($id)
    {

        $orders = Orders::select(
            'orders.status as order_status',
            'users.name as user_name',
            'users.mobile as user_mobile',
            'orders.shipping_charges  as orders_shipping_charges',
            'orders.transaction_id  as transaction_id',
            'orders.shipping_charges  as shipping_charges',
            'orders.payment_type  as payment_type',
            'orders.order_status  as order_status',
            'users.country_code',
            'users.email as user_email',
            'user_address.address as user_address',
            'user_address.latitude',
            'user_address.longitude',
            'orders.created_at',
            'orders.id',
            'orders.amount'
        )
            ->leftjoin('users', 'users.id', '=', 'orders.user_id')
            ->leftjoin('user_address', 'user_address.id', '=', 'orders.address_id')
            ->leftjoin('transaction', 'transaction.id', '=', 'orders.transaction_id')
            ->where('orders.id', $id)
            ->first();

        $data['orders'] = $orders;
        $data['orders_details'] = OrdersDetails::select('order_details.*', 'products.*')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->where('order_details.order_id', $orders->id)
            ->get();

        $dompdf = new Dompdf();
        $htmlview = view('orders.orderdetails', $data);
        $dompdf->loadHtml($htmlview);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $file_name = time() . '.pdf';
        $data = Orders::where(['id' => $id])->update(['pdf' => $file_name]);
        $output = $dompdf->output();
        $data = file_put_contents(public_path('uploads/order_detail_pdf/') . $file_name, $output);
    }

    public function exportOrders(Request $request)
    {
        // dd($request->all());
        Gate::authorize('Order-section');
        return Excel::download(new BulkOrderExport, 'Order.csv');
    }
    public function changeOrderStatus($id, $status)
    {
        $details = CourtBooking::find($id);
        if (!empty($details)) {
            if ($status == 'Accepted') {
                $inp = 'Accepted';
            } else {
                $inp ='Cancelled';
            }
            $User = CourtBooking::find($id);
            $User->order_status=$inp;
        	$User->update();
        	
            //Save to order canceltion logs
        	$login_user_data = auth()->user();
        	$userIds = $login_user_data->id;

			$deliveryPrice = DeliveryPrice::first();
            $logs= new OrderBookingAmountLogs();
            $logs->booking_id =$id;
            $logs->actual_amount =$User->total_amount;
            $logs->amount_type ='debit';
            $logs->payment_type =$User->payment_type;
            $logs->admin_comm_percentage = $deliveryPrice->cancellation_charge;
            $logs->admin_comm_amount =(($User->total_amount*$deliveryPrice->cancellation_charge)/100);
            $logs->amt_after_admin_comm_amount =$User->total_amount -(($User->total_amount*$deliveryPrice->cancellation_charge)/100);
            $logs->joiner_comm_percentage =(($booking_Datas->total_amount*$cancellation_joiner_charge)/100);
            $logs->amt_after_joiner_comm_amount =$booking_Datas->total_amount -(($booking_Datas->total_amount*$cancellation_joiner_charge)/100);
            $logs->action_by_id =$userIds;
            $logs->reason ='Not available';
            $logs->created_at =date('Y-m-d H:i:s');
         	$logs->save();	

			if ($User) {
                if ($status == 'Accepted') {
                    $result['message'] = __("backend.booking_status_accepted");
                    $result['status'] = 1;
                // send notification start
                    $user_id =  $User->user_id;
                    $locale = App::getLocale();
                    $notification_title = 'notification_booking_accepted_by_admin_title';
                    $notification_message = 'notification_booking_accepted_by_admin_message';
                    send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'booking_accepted_by_admin', $order_id = $User->id, $title = $notification_title, $message = $notification_message);
                    App::SetLocale($locale);
                    $notification_title = __('api.notification_booking_accepted_by_admin_title');
                    $notification_message = __('api.notification_booking_accepted_by_admin_message');
                    send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'booking_accepted_by_admin', 'key' => 'booking_accepted_by_admin'));
                    // panal notification start
                    $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                    send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);
                    //panal notification end
                // send notification end
                try{
                $booking = CourtBooking::findOrFail($id);
                if($booking->payment_type == 'cash'){
                 // send email
                 $user = User::where('id', $user_id)->first();
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
                } else {
                    $result['message'] = __("backend.booking_status_cancelled");
                    $result['status'] = 1;
                // send notification start
                    $user_id =  $User->user_id;
                    $locale = App::getLocale();
                    $notification_title = 'notification_booking_cancelled_by_admin_title';
                    $notification_message = 'notification_booking_cancelled_by_admin_message';
                    send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'booking_cancelled_by_admin', $order_id = $User->id, $title = $notification_title, $message = $notification_message);
                    App::SetLocale($locale);
                    $notification_title = __('api.notification_booking_cancelled_by_admin_title');
                    $notification_message = __('api.notification_booking_cancelled_by_admin_message');
                    send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'booking_cancelled_by_admin', 'key' => 'booking_cancelled_by_admin'));
                    // panal notification start
                    $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                    send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);
                    //panal notification end
                // send notification end
                }
            } else {
                $result['message'] = __("backend.booking_status_can_not_update");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function changePaymentStatus($id, $status)
    {
        // dd($id, $status);
        $details = CourtBooking::find($id);
        if (!empty($details)) {
            if ($status == 'Received') {
                $inp = ['payment_received_status' => 'Received','order_status' => 'Accepted'];
            } else {
                $inp = ['payment_received_status' => 'NotReceived','order_status' => 'Cancelled'];
            }
            $User = CourtBooking::findOrFail($id);
            // dd($User);
            if ($User->update($inp)) {
                if ($status == 'Received') {
                    $result['message'] = __("backend.payment_status_received_message");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.payment_status_not_received_message");
                    $result['status'] = 1;
                // send notification start
                    $user_id =  $User->user_id;
                    $locale = App::getLocale();
                    $notification_title = 'notification_post_payment_not_received_title';
                    $notification_message = 'notification_post_payment_not_received_message';
                    send_notification_add($user_id, $user_type = 3, $notification_type = 3, $notification_for = 'post_payment_not_received', $order_id = $User->id, $title = $notification_title, $message = $notification_message);
                    App::SetLocale($locale);
                    $notification_title = __('api.notification_post_payment_not_received_title');
                    $notification_message = __('api.notification_post_payment_not_received_message');
                    send_notification(1, $user_id, $notification_title, array('title' => $notification_title, 'message' => $notification_message, 'type' => 'post_payment_not_received', 'key' => 'post_payment_not_received'));
                    // panal notification start
                    $playerChanellId = 'pubnub_onboarding_channel_player_' . $user_id;
                    send_admin_notification($message = $notification_message, $title = $notification_title, $channel_name = $playerChanellId);
                    //panal notification end
                // send notification end
                }
            } else {
                $result['message'] = __("backend.payment_status_can_not_update");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function delete_orders($id, $is_delete)
    {
        $details = CourtBooking::find($id);
        if (!empty($details)) {
            if ($is_delete == '1') {
                $inp = 1;
            } else {
                $inp = 0;
            }
            $User = CourtBooking::findOrFail($id);
            $User->is_deleted =$inp;
            $User->update();
            if ($User) {
                if ($User['is_deleted'] == '1') {
                    $result['message'] = 'Court Booking is deleted successfully';
                    $result['status'] = 1;
                } else {
                    $result['message'] = 'Court Booking not deleted';
                    $result['status'] = 0;
                }
            } else {
                $result['message'] = 'Court Booking action can`t be updated!!';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeJoinerPaymentStatus($id, $status)
    {
        // dd($id, $status);
        $details = CourtBooking::find($id);
        if (!empty($details)) {
            if ($status == 'Received') {
                $inp = ['joiner_payment_status' => 'Received'];
            } else {
                $inp = ['joiner_payment_status' => 'NotReceived'];
            }
            $User = CourtBooking::findOrFail($id);
            // dd($User);
            if ($User->update($inp)) {
                if ($status == 'Received') {
                    $result['message'] = __("backend.payment_status_received_message");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.payment_status_not_received_message");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.payment_status_can_not_update");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
