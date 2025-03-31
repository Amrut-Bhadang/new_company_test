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
use Illuminate\Support\Facades\App;
use Mail;
use Illuminate\Http\Response;

class CashSettlementController extends Controller
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
        Gate::authorize('Cash-settlement-section');
        $login_user_data = auth()->user();
        $columns = ['orders.created_at'];
        $locale = App::getLocale();
        if($locale == null){
            $locale = 'en';
        }
        // dd($locale,'local');
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
                'users.name as user_name',
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
                    'users.name as user_name',
                    'facilities_lang.name as facility_name')
                ->where(['facilities_lang.lang'=>$locale,'courts_lang.lang'=>$locale]);
                // dd($orders->get(),'ddddddddddd');
                
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
                    'users.name as user_name',
                    'facilities_lang.name as facility_name'
                )
                ->where(['courts.facility_owner_id'=> $login_user->id,'facilities_lang.lang'=>$locale,'courts_lang.lang'=>$locale]);
        }
        // if ($status) {
        //     $orders->where('court_booking.order_status', $status);
        // }
        $orders->where('court_booking.order_status', 'Completed');
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

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(court_booking.created_at)'), array($request->from_date, $request->to_date));
            }

            if ($request->has('court_id')) {
                $court_id = array_filter($request->court_id);
                if (count($court_id) > 0) {
                    $query->whereIn('court_booking.court_id', $request->get('court_id'));
                }
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
                $query->orHaving('court_booking.order_status', 'like', "%{$search['value']}%");
                $query->orHaving('court_booking.payment_type', 'like', "%{$search['value']}%");
                $query->orHaving('courts_lang.court_name', 'like', "%{$search['value']}%");
            }
        return $query;

        })->addIndexColumn()->make(true);
    }

    public function frontend(Request $request, $status = null)
    {
        Gate::authorize('Cash-settlement-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['courts'] = Courts::select('id', 'court_name')->where(['status' => 1])->get();
        $data['facilities'] = Facility::select('name', 'id')->where(['status' => 1])->get();
        $data['players'] = User::select('name', 'mobile', 'country_code', 'id')->where(['status' => 1, 'type' => 3])->get();
       $order = CourtBooking::all();
    //    $data['total_amount'] = $order->sum('total_amount');
    //    $data['paid_amount'] = $order->sum('total_amount');
    //    $data['admin_commission_amount'] = $order->sum('admin_commission_amount');
       $order = $this->index($request)->original['data'];
       $data['total_amount'] = 0;
       $data['paid_amount'] = 0;
       $data['admin_commission_amount'] = 0;
       if ($order) {
           foreach ($order as $key => $value) {
            $data['total_amount'] += $value['total_amount'];
               if ($value['booking_type'] == 'challenge') {
                $data['paid_amount'] += $value['total_amount'] / 2;
                 } else {
                    $data['paid_amount'] += $value['total_amount'];
                 }
                 $data['admin_commission_amount'] += $value['admin_commission_amount'];
           }
       }


        $data['order_status'] = $status;
        return view('cash_settlement.listing', $data);
    }
    public function show_court_data($facility_id, $court_id = null)
    {

        Gate::authorize('Cash-settlement-section');
        $facility_ids = explode(',', $facility_id);
        $data['record'] = Courts::Where('status', 1)->whereIn('facility_id', $facility_ids)->select('court_name', 'id')->get();
        return view('cash_settlement.court_list', $data);
    }

    public function show($id)
    {
        Gate::authorize('Cash-settlement-section');
        $locale = App::getLocale();
        $data['record'] = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
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
                'facilities_lang.name as facility_name'
            )
            ->where(['court_booking.id'=> $id,'facilities_lang.lang'=>$locale,'courts_lang.lang'=>$locale])
            ->first();
        return view('cash_settlement.view', $data);
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
        $htmlview = view('cash_settlement.orderdetails', $data);
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
        Gate::authorize('Cash-settlement-section');
        return Excel::download(new BulkOrderExport, 'cash_settlement.csv');
    }
    public function changeOrderStatus($id, $status)
    {
        // dd($id, $status);
        $details = CourtBooking::find($id);
        if (!empty($details)) {
            if ($status == 'Accepted') {
                $inp = ['order_status' => 'Accepted'];
            } else {
                $inp = ['order_status' => 'Cancelled'];
            }
            $User = CourtBooking::findOrFail($id);
            // dd($User);
            if ($User->update($inp)) {
                if ($status == 'Accepted') {
                    $result['message'] = __("backend.booking_status_accepted");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.booking_status_cancelled");
                    $result['status'] = 1;
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
    public function getAmountData(request $request) {
        $data = $this->index($request)->original['data'];
        $total_amount = 0;
        $paid_amount = 0;
        $admin_commission_amount = 0;
        if ($data) {
            foreach ($data as $key => $value) {
                $total_amount += $value['total_amount'];
                if ($value['booking_type'] == 'challenge') {
                     $paid_amount += $value['total_amount'] / 2;
                  } else {
                     $paid_amount += $value['total_amount'];
                  }
                $admin_commission_amount += $value['admin_commission_amount'];
            }
        }
        $data_total = [];
        $data_total['total_amount']=number_format($total_amount, 2);
        $data_total['paid_amount']=number_format($paid_amount, 2);
        $data_total['admin_commission_amount']=number_format($admin_commission_amount, 2);

        $result['message'] = 'Wallet amount';
        $result['status'] = 1;
        $result['data'] = $data_total;
        return response()->json($result);
    }
}
