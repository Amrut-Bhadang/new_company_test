<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Restaurant;
use App\Models\OrdersDetails;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\QueryDataTable;
use DB;
class EarningController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd($status);
        Gate::authorize('Earning-section');
        $columns = ['orders.id','orders.address'];

        $orders=Orders::select('orders.order_status','users.name as user_name','user_address.address as user_address','orders.address','orders.amount','orders.admin_amount','orders.org_amount','orders.admin_commission','orders.shipping_charges','orders.discount_amount','orders.discount_type','orders.created_at','orders.id as order_id','orders.restaurant_id')
                    ->selectRaw('products.name as product_name')
                    ->leftjoin('order_details','orders.id','=','order_details.order_id')
                    ->leftjoin('products','order_details.product_id','=','products.id')
                    ->leftjoin('users','users.id','=','orders.user_id')
                    ->leftjoin('user_address','user_address.id','=','orders.address_id')
                    ->leftjoin('transaction','transaction.id','=','orders.transaction_id')
                    ->where('orders.order_status','Complete')->groupBy('orders.id');
                    //dd($orders->admin_amount);
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
                if (!is_null($request->get('name')) || $request->get('name') == 1 || $request->get('name') == 2 || $request->get('name') == 3 ) {
                    $query->where('products.name', 'like', "%{$request->get('name')}%");
                }

                if (!is_null($request->get('payment_mode'))){
                    $query->where('transaction.payment_type', '=', $request->get('payment_mode'));
                }
                /*if ($request->has('chef_id')) {
                    $chef_id = array_filter($request->chef_id);
                    if(count($chef_id) > 0) {
                        $query->whereIn('orders.chef_id', $request->get('chef_id'));
                    }  
                }*/
                if ($request->has('order_id')) {
                    $product_id = array_filter($request->product_id);
                    if(count($product_id) > 0) {
                        $query->whereIn('orders.id', $request->get('order_id'));
                    }  
                }
               /* if ($request->has('celebrity_id')) {
                    $celebrity_id = array_filter($request->celebrity_id);
                    if(count($celebrity_id) > 0) {
                        $query->whereIn('products.celebrity_id', $request->get('celebrity_id'));
                    }  
                }*/
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
                
                if ($request->has('product_id')) {
                    $product_id = array_filter($request->product_id);
                    if(count($product_id) > 0) {
                        $query->whereIn('products.id', $request->get('product_id'));
                    }
                }

                if(!empty($request->from_date) && !empty($request->to_date))
                {
                    $query->whereBetween(DB::raw('DATE(orders.created_at)'), array($request->from_date, $request->to_date));
                }
                
                if (!empty($request->get('search'))) {
                   $search = $request->get('search');
                   // $query->having('orders.address', 'like', "%{$search['value']}%");
                   $query->having('products.name', 'like', "%{$search['value']}%");
                   $query->orHaving('users.name', 'like', "%{$search['value']}%");
                   $query->orHaving('orders.id', 'like', "%{$search['value']}%");
                   $query->orHaving('orders.order_status', 'like', "%{$search['value']}%");
                }
        })->addIndexColumn()->make(true);
    }


    
    public function frontend()
    {
        Gate::authorize('Order-section');
        //$status = $request->segment(3);
        $data['customers']=User::select('name','id','mobile')->where(['type'=>0,'status'=>1])->get();
        $data['restaurants']=Restaurant::select('name','id','phone_number','country_code','email')->where(['status'=>1])->get();
        $data['products'] = Products::where('is_active',1)->get();
        
        
        return view('earning.listing', $data);
    }


    public function changeOrderStatus($id, $status)
    {
        //dd($status);
        $order_details = Orders::find($id);
        if(!empty($order_details)){
            if($status == 'Pending'){
                $inp = ['order_status' => 'Accepted'];
            }elseif($status == 'Complete'){
                $inp = ['order_status' => 'Complete'];
            }else{
                $inp = ['order_status' => 'Cancel'];
            }
            $Orders = Orders::findOrFail($id);
            if($Orders->update($inp)){
                if($status == 'Complete'){
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
                }
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
        //
        Gate::authorize('Earning-section');
        $orders = Orders::select('orders.status as order_status','users.name as user_name','users.mobile as user_mobile', 'orders.shipping_charges  as orders_shipping_charges', 'orders.transaction_id  as transaction_id','orders.shipping_charges  as shipping_charges','orders.payment_type  as payment_type','orders.order_status  as order_status',
         'users.country_code','users.email as user_email','user_address.address as user_address','user_address.latitude','user_address.longitude','orders.created_at','orders.id','orders.amount')
                    ->leftjoin('users','users.id','=','orders.user_id')
                    ->leftjoin('user_address','user_address.id','=','orders.address_id')
                    ->leftjoin('transaction','transaction.id','=','orders.transaction_id')
                    ->where('orders.id', $id)
                    ->first();



        $data['orders']= $orders;
        //$chef_details= User::select('users.*')->where('users.id', $orders->chef_id)->first();
        //$data['chef_details']= $chef_details;
        $data['orders_details'] = OrdersDetails::select('order_details.*','products.*')
                                ->join('products','products.id','=','order_details.product_id')
                                ->where('order_details.order_id',$orders->id)
                                ->get();
        return view('orders.view',$data);
    }
}
