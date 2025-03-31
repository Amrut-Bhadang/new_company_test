<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\DeliveryPrice;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

use File,DB;

class PriceSettingsController extends Controller
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
        Gate::authorize('AdminSetting-section');
    }
    
    public function frontend()
    {
        Gate::authorize('AdminSetting-section');
        $deliveryPrice = DeliveryPrice::all();

        $data['deliveryPrice'] = $deliveryPrice;

        
        return view('price-settings.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('AdminSetting-edit');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('AdminSetting-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('AdminSetting-create');
        $this->validate($request, [           
            'per_prices'=>'required|numeric|min:0|not_in:0',
            'type'=>'required',
        ]);

       $inputs = [
            'prices' => $request->per_prices,
            'type'=>$request->type
       ];

        $prices = DeliveryPrice::create($inputs);
        if($prices->id){
            $result['message'] = 'Admin Settings has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Admin Settings Can`t created';
            $result['status'] = 0;
        }

        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('AdminSetting-edit');
        // validate
        $mesasge = [
            'common_commission_percentage.required' => __("backend.common_commission_percentage_required"),
            'common_commission_percentage.numeric' => __("backend.common_commission_percentage_numeric"),
            'cancellation_charge.required' => __("backend.cancellation_charge_required"),
            'cancellation_charge.numeric' => __("backend.cancellation_charge_numeric"),
            'common_commission_percentage.min' => __("backend.common_commission_percentage_min"),
            'common_commission_percentage.max' => __("backend.common_commission_percentage_max"),
            'cancellation_charge.min' => __("backend.cancellation_charge_min"),
            'cancellation_charge.max' => __("backend.cancellation_charge_max"),
        ];
		$this->validate($request, [           
            'common_commission_percentage'=>'required|numeric|min:1|max:100',
            'cancellation_charge'=>'required|numeric|min:1|max:100',
        ], $mesasge);
            
        // $input = $request->all();
        $inp=[
            
            'common_commission_percentage'=>$request->common_commission_percentage,
            'cancellation_charge'=>$request->cancellation_charge,
        ];
      
        $prices = DeliveryPrice::findOrFail($id);

        if ($prices->update($inp)) {
            $result['message'] = 'Admin Settings has been updated successfully';
            $result['status'] = 1;

        } else {
            $result['message'] = 'Admin Settings Can`t updated';
            $result['status'] = 0;
        }
		return response()->json($result);
    }
    
    public function destroy($id)
    {
        Gate::authorize('AdminSetting-delete');
        if(DeliveryPrice::findOrFail($id)->delete()){
            $result['message'] = 'Admin Settings deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Admin Settings Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
