<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Modes;
use App\Models\Restaurant;
use App\Models\OrdersDetails;
use App\Models\CashReceived;
use App\Models\MainCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SettlementExport;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\QueryDataTable;
use DB;

class SettlementController extends Controller
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
        Gate::authorize('Settlement-section');
        $columns = ['orders.id','orders.order_status'];

        $orders=Orders::select('orders.order_status','orders.admin_amount','orders.created_at','orders.id as order_id','orders.restaurant_id','modes.name as order_type','orders.amount','orders.org_amount','orders.admin_commission','orders.shipping_charges','orders.discount_amount','orders.discount_type')
                    ->join('restaurants','restaurants.id','=','orders.restaurant_id')
                    ->leftjoin('modes','modes.id','=','orders.order_type')
                    ->where('orders.order_status', 'Complete')
                    ->groupBy('orders.id');
        //dd($orders);
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

                if ($request->has('main_category_id')) {

                    if ($request->get('main_category_id')) {
                        $query->where('restaurants.main_category_id', $request->get('main_category_id'));
                    }
                }
                
                if (!empty(($request->get('restaurant_id')))) { 
                    $query->where('orders.restaurant_id', $request->get('restaurant_id'));
                }

                if (!empty(($request->get('order_status')))) { 
                    $query->where('orders.order_status', $request->get('order_status'));
                }

                if (!empty(($request->get('order_type')))) { 
                    $query->where('orders.order_type', $request->get('order_type'));
                }
               
                if ($request->has('customer_id')) {
                    $customer_id = array_filter($request->customer_id);
                    if(count($customer_id) > 0) {
                        $query->whereIn('users.id', $request->get('customer_id'));
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
                   $query->orHaving('orders.id', 'like', "%{$search['value']}%");
                   $query->orHaving('orders.order_status', 'like', "%{$search['value']}%");
                   $query->orHaving('modes.name', 'like', "%{$search['value']}%");
                   $query->orHaving('orders.amount', 'like', "%{$search['value']}%");
                }
        })->addIndexColumn()->make(true);
    }


    
    public function frontend()
    {
        Gate::authorize('Settlement-section');
        //$status = $request->segment(3);
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['restaurant']=Restaurant::select('name','id')->where(['status'=>1])->get();
        $data['products'] = Products::where('is_active',1)->get();
        $data['modes'] = Modes::select('id','name')->where(['status'=>1])->get();

        // $orderTotalCOD =  Orders::select('orders.*','')->where('payment_type','Cash')->where('orders.order_status', 'Complete')->sum('orders.amount');
        $orderTotalCOD =  Orders::select('orders.*','')->where('orders.order_status', 'Complete');
        $CashRecevied = CashReceived::Select('cash_received')->sum('cash_received.total_amount');
        /*$ordtotcod = (float)number_format($orderTotalCOD, 2, '.', '');
        $cashrec = (float)number_format($CashRecevied,2, '.', '');
        $pendAmou = $ordtotcod - $cashrec;
        $data['orderTotalCOD'] = $ordtotcod;
        $data['CashRecevied'] = $cashrec;*/
        $totalAMT = $orderTotalCOD->sum('orders.amount');
        $cashAMT = $orderTotalCOD->sum('orders.admin_amount');

        $shipping_charges = $orderTotalCOD->sum('orders.shipping_charges');
        $discount_amount = $orderTotalCOD->where('discount_type', 'Flat-Discount')->sum('orders.discount_amount');

        // echo $discount_amount;die;

        $totalAMTWithShipping = $totalAMT + $shipping_charges;
        $totalAdminCommWithoutAdminDis = $cashAMT - $discount_amount;

        $data['orderTotalCOD'] = (float)number_format($totalAMTWithShipping,2, '.', '');
        $data['CashRecevied'] = (float)number_format($totalAdminCommWithoutAdminDis,2, '.', '');
        $pendAmou = $data['orderTotalCOD'] - $data['CashRecevied'];
        $data['pendAmount'] = $pendAmou;
        
        return view('settlement.listing', $data);
    }

    public function saveCashRecevied(Request $request)
    {
        Gate::authorize('Settlement-create');

        $this->validate($request, [           
            'restaurant_id'=>'required',
            'restaurant_name'=>'required',
            'received_amount'=>'required'
        ]);
        $orderTotalCOD =  Orders::select('orders.*')->where('orders.restaurant_id',$request->restaurant_id)->where('orders.order_status', 'Complete');

        if (!empty(($request->order_type))) { 
            $orderTotalCOD->where('orders.order_type', $request->order_type);
        }

        $totalAMT = $orderTotalCOD->sum('orders.amount');

        $CashRecevied = CashReceived::Select('cash_received.*')->where('cash_received.restaurant_id',$request->restaurant_id)->sum('cash_received.total_amount');
        $penddingAmount = $totalAMT - $CashRecevied;

        // echo $totalAMT.'-'.$CashRecevied.'='.$penddingAmount;die;

        if ($penddingAmount < $request->received_amount) {
            $result['message'] = 'Received Amount less than Pending Amount!';
            $result['status'] = 0;
            return response()->json($result);
        }
        $inputs = [
            'restaurant_id' => $request->restaurant_id,
            'restaurant_name' => $request->restaurant_name,
            'total_amount' => $request->received_amount,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ];
        // dd($request->received_amount);

        $received = CashReceived::create($inputs);
        if($received->id){
            $result['message'] = 'Received Amount';
            $result['status'] = 1;
            $orderTotalCOD =  Orders::select('orders.*')->where('orders.restaurant_id',$request->restaurant_id)->where('orders.order_status', 'Complete')->sum('orders.amount');
            $CashRecevied = CashReceived::Select('cash_received.*')->where('cash_received.restaurant_id',$request->restaurant_id)->sum('cash_received.total_amount');
            // $data['orderTotalCOD'] = (float)number_format($orderTotalCOD,2, '.', '');
            $data['orderTotalCOD'] = (float)number_format($totalAMT,2, '.', '');
            $data['CashRecevied'] = (float)number_format($CashRecevied,2, '.', '');
            $data['penddingAmount'] = $totalAMT - $CashRecevied;

            $result['data'] = $data;


        }else{
            $result['message'] = 'Amount Can`t be Received';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    /*public function cashReceived(Request $request){
        Gate::authorize('Settlement-section');

        if($request->get('restaurant_id')){
            $orderTotalCOD =  Orders::select('orders.*')->where('orders.restaurant_id',$request->get('restaurant_id'))->sum('orders.amount');
            $CashRecevied = CashReceived::select('cash_received.*')->where('cash_received.restaurant_id',$request->get('restaurant_id'))->sum('cash_received.total_amount');
        }else{
            $orderTotalCOD =  Orders::select('orders.*')->sum('orders.amount');
            $CashRecevied = CashReceived::Select('cash_received.*')->sum('cash_received.total_amount');
        }
        

        $data['orderTotalCOD'] = (float)number_format($orderTotalCOD,2, '.', '');
        $data['CashRecevied'] = (float)number_format($CashRecevied,2, '.', '');
        
        return response()->json($data);
    }*/

    public function cashReceived(Request $request){
        Gate::authorize('Settlement-section');
        $orderTotalCOD =  Orders::select('orders.*')->where('orders.order_status', 'Complete')->join('restaurants','restaurants.id','=','orders.restaurant_id');
        $CashRecevied = CashReceived::Select('cash_received.*');

        if ($request->has('main_category_id')) {

            if ($request->get('main_category_id')) {
                $orderTotalCOD->where('restaurants.main_category_id', $request->get('main_category_id'));
            }
        }
        
        if (!empty(($request->get('restaurant_id')))) { 
            $orderTotalCOD->where('orders.restaurant_id', $request->get('restaurant_id'));
        }


        if($request->get('restaurant_id')){
            $orderTotalCOD->where('orders.restaurant_id',$request->get('restaurant_id'));
            $CashRecevied->where('cash_received.restaurant_id',$request->get('restaurant_id'));
        }
        
        if (!empty(($request->get('order_type')))) { 
            $orderTotalCOD->where('orders.order_type', $request->get('order_type'));
        }

        $totalAMT = $orderTotalCOD->sum('orders.amount');
        $cashAMT = $orderTotalCOD->sum('orders.admin_amount');

        $data['orderTotalCOD'] = (float)number_format($totalAMT,2, '.', '');
        $data['CashRecevied'] = (float)number_format($cashAMT,2, '.', '');
        
        return response()->json($data);
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
        Gate::authorize('Settlement-section');
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

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new SettlementExport, 'Settlement.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
