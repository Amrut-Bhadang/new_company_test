<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Gift;
use App\Models\GiftOrder;
use App\Models\GiftUserPlatforms;
use App\Models\UserAddress;
use App\Models\GiftOrderDetails;
use App\Models\OrderCancelReasions;
use App\Models\Notification;
use App\Models\GiftNotification;
use App\Models\PanelNotifications;
use App\Models\GiftTopping;
use App\Models\GiftOrderToppings;
use App\Models\UserWallets;
use App\Models\UserKiloPoints;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\QueryDataTable;
use DB;

class GiftOrdersController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $status)
    {
        //dd($status);
        Gate::authorize('Order-section');
        $columns = ['gift_orders.address'];

        $orders=GiftOrder::select('gift_orders.random_order_id','gift_orders.order_status','gift_orders.address','users.name as user_name','gift_orders.points','gift_orders.created_at','gift_orders.id as gift_order_id','gift_order_details.gift_id')
                    ->join('gift_order_details','gift_orders.id','=','gift_order_details.gift_order_id')
                    // ->leftjoin('users','users.parent_user_id','=','gift_orders.user_id')
                    ->leftjoin('users','users.id','=','gift_orders.user_id')
                    ->where('gift_orders.order_status', $status)->groupBy('gift_orders.id');

        // dd($orders->get()->toArray());

 
       // $orders = Gift::select(
        // "gifts.name",
        // )->join('gift_order_details','gift_order_details.gift_id','=','gifts.id')
        // ->join('gift_orders','gift_orders.id','=','gift_order_details.gift_order_id')
        // ->join('gift_order_details','gift_order_details.user_id','=','users.parent_user_id') ->where('gift_orders.order_status', $status)->groupBy('gift_orders.id')->get();
        //    return $orders;


        return Datatables::of($orders)
        ->editColumn('created_at', function ($orders) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($orders->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');  
        })
        ->filter(function ($query) use ($request,$columns) {
                /*if (!is_null($request->get('name')) || $request->get('name') == 1 || $request->get('name') == 2 || $request->get('name') == 3 ) {
                    $query->where('gifts.name', 'like', "%{$request->get('name')}%");
                }*/

                /*if (!is_null($request->get('payment_mode'))){
                    $query->where('transaction.payment_type', '=', $request->get('payment_mode'));
                }*/

                if ($request->has('gift_id')) { 
                    $gift_id = array_filter($request->gift_id);
                    if(count($gift_id) > 0) {
                        $query->whereIn('gift_order_details.gift_id', $request->get('gift_id'));
                    }
                }

                if ($request->has('customer_id')) {
                    $customer_id = array_filter($request->customer_id);
                    if(count($customer_id) > 0) {
                        $query->whereIn('users.id', $request->get('customer_id'));
                    }
                }
                if(!empty($request->from_date) && !empty($request->to_date))
                {
                    $query->whereBetween(DB::raw('DATE(gift_orders.created_at)'), array($request->from_date, $request->to_date));
                }

                if (!empty($request->get('search'))) {
                   $search = $request->get('search');
                   $query->having('users.name', 'like', "%{$search['value']}%");
                   $query->orhaving('gift_orders.random_order_id', 'like', "%{$search['value']}%");
                   $query->orhaving('gift_orders.id', 'like', "%{$search['value']}%");
                }
                
        })->addIndexColumn()->make(true);
    }



    public function frontend($status)
    {
        Gate::authorize('Order-section');
        //$status = $request->segment(3);
        $data['customers']=User::on('mysql2')->select('name','id','mobile','country_code')->where(['type'=>0,'status'=>1])->get();
        $data['gifts'] = Gift::where('is_active',1)->get();
        $data['OrderCancelReasions'] = OrderCancelReasions::where(['status'=>1])->get();
        $data['order_status'] = $status;

        return view('gift_orders.listing', $data);
    }


    public function changeOrderStatus($id, $status)
    {
        //dd($status);
        $result['status'] = 1;
        $order_details = GiftOrder::find($id);
        if(!empty($order_details)){
            if($status == 'Pending'){
                $inp = ['order_status' => 'Accepted'];
                $result['message'] = 'Order is Accpeted successfully';
            }elseif($status == 'Complete'){
                $inp = ['order_status' => 'Complete'];
                $result['message'] = 'Order is Completed successfully';
            }else{
                $inp = ['order_status' => 'Cancel'];
                $result['message'] = 'Order is cancelled successfully';
            }
            $Orders = GiftOrder::findOrFail($id);

            if($Orders->update($inp)){

                //Callback method call
                $userDetail = GiftUserPlatforms::where(['user_id'=>$order_details->user_id, 'platform'=>$order_details->platform])->first();

                if ($userDetail && $userDetail->callback_url) {
                    $orderTokenId = encryptPass($order_details->id.'|~@#|'.$order_details->random_order_id.'|~@#|'.$order_details->user_id.'|~@#|'.$inp['order_status']);
                    $url = $userDetail->callback_url.'?order_token='.$orderTokenId;

                    //Gift Callback
                    $apiResponse = CallbackApis($url, 'Register');
                }
                /*if($status == 'Complete'){
                    $result['message'] = 'Order is Completed successfully';
                    $result['status'] = 1;
                }else if($status == 'Accpeted'){
                    $result['message'] = 'Order is Accpeted successfully';
                    $result['status'] = 1;
                }else if($status == 'Cancel'){
                    $result['message'] = 'Order is cancelled successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Order is Accpeted successfully';
                    $result['status'] = 1;
                }*/
            }else{
                $result['message'] = 'Order status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild Order!!';
            $result['status'] = 0;
        }
         return response()->json($result);
    }
    public function show($id)
    {
        if(!empty($id)){
            $data['GiftOrder'] = GiftOrder::find($id);

            if ($data['GiftOrder']) {

                if ($data['GiftOrder']->cancel_reasion_id) {
                    $CancelDetail = OrderCancelReasions::select('reasion')->where('id', $data['GiftOrder']->cancel_reasion_id)->first();

                    if ($CancelDetail) {
                        $data['GiftOrder']->reasion = $CancelDetail->reasion;

                    } else {
                        $data['GiftOrder']->reasion = '';
                    }

                } else {
                    $data['GiftOrder']->reasion = '';
                }
            }
            // dd($data['GiftOrder']);

            $data['GiftOrderDetails'] = GiftOrderDetails::select('gift_order_details.*','gifts.sku_code')->where('gift_order_id',$id)->join('gifts','gifts.id','=','gift_order_details.gift_id')->get();

            if ($data['GiftOrderDetails']) {

                foreach ($data['GiftOrderDetails'] as $key => $value) {
                    $product_attrs = '';
                    $productAttr = GiftOrderToppings::select('attribute_value_name')->where(['order_detail_id' => $value->id])->pluck('attribute_value_name')->toArray();

                    // dd($productAttr);

                    if ($productAttr) {
                        $product_attrs = implode(", ",array_unique($productAttr));
                    } else {
                        $product_attrs = '';
                    }

                    $value->product_attrs = $product_attrs;
                }

            }
            return view('gift_orders.view',$data);
        }
    }

    public function cancelOrderStatus(Request $request)
    {
        //dd($status);
        $result['status'] = 1;
        $order_details = GiftOrder::find($request->order_id);

        if(!empty($order_details)){
            $inp = ['order_status' => 'Cancel', 'cancel_reasion_id' => $request->reasion_id];
            $result['message'] = 'Order is cancelled successfully';

            if (GiftOrder::where('id',$request->order_id)->update($inp)) {
                //CR in wallet

                if ($order_details->payment_type != 'Cash') {
                    $walletData = new UserWallets;
                    $walletData->user_id = $order_details->user_id;
                    $walletData->transaction_type = 'CR';
                    $walletData->amount = $order_details->shipping_charges + $order_details->tax_amount;
                    $walletData->comment = 'Your gift order cancelled, ORD-'.$order_details->id;
                    $walletData->save();
                }

                //insert In KiloPointsDB
                $userKiloPointsNewDB = new UserKiloPoints;
                $userKiloPointsNewDB->order_id = $order_details->id;
                $userKiloPointsNewDB->user_id = $order_details->user_id;
                $userKiloPointsNewDB->points = $order_details->points;
                $userKiloPointsNewDB->type = 'CR';
                $userKiloPointsNewDB->is_refund = 'Yes';
                $userKiloPointsNewDB->comment = 'Gift #'.$order_details->random_order_id.' Order cancelled by you.';
                $userKiloPointsNewDB->setConnection('mysql2');
                $userKiloPointsNewDB->save();

                //Callback method call
                $userDetail = GiftUserPlatforms::where(['user_id'=>$order_details->user_id, 'platform'=>$order_details->platform])->first();

                if ($userDetail && $userDetail->callback_url) {
                    $orderTokenId = encryptPass($request->order_id.'|~@#|'.$order_details->random_order_id.'|~@#|'.$order_details->user_id.'|~@#|Cancelled');
                    $url = $userDetail->callback_url.'?order_token='.$orderTokenId;

                    //Gift Callback
                    $apiResponse = CallbackApis($url, 'Register');
                }

                //Notification data
                $notificationData = new GiftNotification;
                $notificationData->user_id = $order_details->user_id;
                $notificationData->order_id = $order_details->id;
                $notificationData->user_type = 2;
                $notificationData->notification_for = 'Order-Cancel';
                $notificationData->notification_type = 3;
                $notificationData->title = 'Order Cancel';
                $notificationData->message = 'Your order is cancelled by store.';
                $notificationData->save();
                // send_notification(1, $order_details->user_id, 'Order Cancel', array('title'=>'Order Cancel','message'=>$notificationData->message,'type'=>'Gift','key'=>'event'));
                $result['message'] = 'Order status has been updated!!';
                $result['status'] = 1;
                
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
}
