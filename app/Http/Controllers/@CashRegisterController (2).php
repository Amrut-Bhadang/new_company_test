<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\CashReceived;
use App\User;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
// use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashRegisterExport;
use App\Imports\BulkImport;

use File, DB;

class CashRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('Category-section');
        $cash = CashRegister::select('cash_register.*', 'orders.created_at as orders_date')
            ->leftjoin('orders', 'orders.id', '=', 'cash_register.order_id');

        return Datatables::of($cash)->editColumn('created_at', function ($cash) {
            return $cash->created_at->format('m/d/Y h:m:s');
        })->filterColumn('created_at', function ($query) use ($request) {
            if (!empty($request->driver_name) && $request->driver_name != "") {
                $query->where('cash_register.driver_id', $request->driver_name);
            }
            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(cash_register.created_at)'), array($request->from_date, $request->to_date));
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
        Gate::authorize('Category-section');
        $orderTotalCOD =  CashRegister::select('cash_register.*', '')->sum('cash_register.total_amount');
        $CashRecevied = CashReceived::Select('cash_received')->sum('cash_received.total_amount');

        $data['orderTotalCOD'] = $orderTotalCOD;
        $data['CashRecevied'] = $CashRecevied;
        return view('cash-register.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Category-edit');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Category-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Category-create');

        $this->validate($request, [
            'driver_id' => 'required',
            'driver_name' => 'required',
            'received_amount' => 'required'
        ]);


        $orderTotalCOD =  CashRegister::select('cash_register.*')->where('cash_register.driver_id', $request->driver_id)->sum('cash_register.total_amount');
        $CashRecevied = CashReceived::Select('cash_received.*')->where('cash_received.driver_id', $request->driver_id)->sum('cash_received.total_amount');
        $penddingAmount = $orderTotalCOD - $CashRecevied;

        if ($penddingAmount <= $request->received_amount) {
            $result['message'] = 'Recevied Amount is Less Then Pending Amount!';
            $result['status'] = 0;
            return response()->json($result);
        }
        $inputs = [
            'driver_id' => $request->driver_id,
            'driver_name' => $request->driver_name,
            'total_amount' => $request->received_amount,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $received = CashReceived::create($inputs);
        if ($received->id) {
            $result['message'] = 'Recevied Amount';
            $result['status'] = 1;
            $orderTotalCOD =  CashRegister::select('cash_register.*')->where('cash_register.driver_id', $request->driver_id)->sum('cash_register.total_amount');
            $CashRecevied = CashReceived::Select('cash_received.*')->where('cash_received.driver_id', $request->driver_id)->sum('cash_received.total_amount');
            $data['orderTotalCOD'] = $orderTotalCOD;
            $data['CashRecevied'] = $CashRecevied;
            $data['penddingAmount'] = $orderTotalCOD - $CashRecevied;

            $result['data'] = $data;
        } else {
            $result['message'] = 'Amount Can`t be Recevied';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function cashReceived(Request $request)
    {
        Gate::authorize('Category-section');

        if ($request->get('driver_name')) {
            $orderTotalCOD =  CashRegister::select('cash_register.*')->where('cash_register.driver_id', $request->get('driver_name'))->sum('cash_register.total_amount');
            $CashRecevied = CashReceived::Select('cash_received.*')->where('cash_received.driver_id', $request->get('driver_name'))->sum('cash_received.total_amount');
        } else {
            $orderTotalCOD =  CashRegister::select('cash_register.*')->sum('cash_register.total_amount');
            $CashRecevied = CashReceived::Select('cash_received.*')->sum('cash_received.total_amount');
        }


        $data['orderTotalCOD'] = $orderTotalCOD;
        $data['CashRecevied'] = $CashRecevied;

        return response()->json($data);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new CashRegisterExport, 'CashRegister.csv');
    }

    public function importUsers()
    {
        Excel::import(new BulkImport, request()->file('file'));
        return back();
    }
}
