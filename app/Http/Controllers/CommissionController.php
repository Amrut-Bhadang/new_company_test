<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Courts;
use App\Models\CourtLang;
use App\Models\Language;
use App\Models\CourtBooking;
use App\Models\CourtBookingSlot;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkExport;
use App\Imports\BulkImport;
use App\Models\Amenity;
use App\Models\Country;
use DB;
use App\Models\EmailTemplateLang;
use App\Models\Commission;
use App\Models\Facility;
use App\Models\FacilityAmenity;
use Exception;
use Mail;

class CommissionController extends Controller
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
    public function index(request $request)
    {
    // dd('index');

        Gate::authorize('Commission-section');
        $columns = ['courts.court_name', 'courts.adress'];

        $commission = Commission::join('courts','courts.id','commissions.court_id')
        ->join('facilities','facilities.id','commissions.facility_id')
        ->select('commissions.*','courts.court_name as court_name','facilities.name as facility_name');
        // dd($user->get()->toArray());
        return Datatables::of($commission)->editColumn('created_at', function ($commission) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($commission->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            // if (!empty($request->from_date) && !empty($request->to_date)) {
            //     $query->whereBetween(DB::raw('DATE(commissions.created_at)'), array($request->from_date, $request->to_date));
            // }

            if ($request->has('commission_status')) {

                if ($request->get('commission_status') && $request->get('commission_status') == 'Active') {
                    $query->where('commissions.status', 1);
                } else if ($request->get('commission_status') && $request->get('commission_status') == 'Deactive') {
                    $query->where('commissions.status', 0);
                }
            }
            if ($request->has('commission_owner_name')) {

                if ($request->get('commission_owner_name')) {
                    $query->where('courts.name', $request->get('commission_owner_name'));
                }
            }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('commissions.amount', 'like', "%{$search['value']}%");
                            // ->orWhere('commissions.email', 'like', "%{$search['value']}%")
                            // ->orWhere('commissions.mobile', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
    // dd('frontend');

        Gate::authorize('Commission-section');
        $commission = Commission::all();
        $data['roles'] = Role::all();
        $data['court'] = Courts::Where('status',1)->select('court_name', 'id')->get();

        return view('commission.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Commission-edit');
        //$data['roles']=Role::all();
        $data['commission'] = Commission::findOrFail($id);
        $data['court'] = Courts::Where('status',1)->select('court_name', 'id')->get();
        $data['facility'] = Facility::Where('status',1)->select('name', 'id')->get();

        return view('commission.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Commission-create');

        // $data = array();
        $data['court'] = Courts::Where('status',1)->select('court_name', 'id')->get();
        $data['facility'] = Facility::Where('status',1)->select('name', 'id')->get();
        return view('commission.add', $data);
    }
    public function show_court_data($facility_id, $court_id=null)
    {
        Gate::authorize('Commission-create');
        $data['facility_id'] = $facility_id;
        $data['court_id'] = $court_id;
        $data['record'] = Courts::Where(['status'=>1, 'facility_id'=>$facility_id])->select('court_name', 'id')->get();
        return view('commission.court_list',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        Gate::authorize('Commission-create');
        // validate
        $mesasge = [
            'amount.required' => __("backend.amount_required"),
            'amount.numeric' => __("backend.amount_numeric"),
            'amount.min' => __("backend.amount_min"),
            'amount.max' => __("backend.amount_max"),
            'court_id.required' => __("backend.court_id_required"),
            'court_id.unique' => __("backend.court_id_unique"),
            'facility_id.required' => __("backend.facility_id_required"),
        ];
        $this->validate($request, [
            'amount' => 'required|numeric|min:1|max:100',
            'court_id' => 'required|unique:commissions,court_id',
            'facility_id' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
   
        if (!$fail) {
            try {
                $data = new Commission();
                $data->amount = $input['amount'];
                $data->court_id = $input['court_id'];
                $data->facility_id = $input['facility_id'];
                $data->status = 1;
              
                $data->save();

                $result['message'] = __('backend.commission_created');
                $result['status'] = 1;
                return response()->json($result);
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        } else {
            $result['message'] = __('backend.Something_went_wrong');
            $result['status'] = 0;
            return response()->json($result);
        }
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    //    dd('show');
        Gate::authorize('Commission-section');
        $data['record'] = Commission::join('courts','courts.id','commissions.court_id')
        ->join('facilities','facilities.id','commissions.facility_id')
        ->select('commissions.*','courts.court_name as court_name','facilities.name as facility_name')
        ->where('commissions.id',$id)->first();

        return view('commission.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        Gate::authorize('Commission-edit');
        $user = Commission::findOrFail($id);
        // $user['role_names']=$user->getRoleNames();
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd('update');
        Gate::authorize('Commission-edit');
        // validate
        $mesasge = [
            'amount.required' => __("backend.amount_required"),
            'amount.numeric' => __("backend.amount_numeric"),
            'amount.min' => __("backend.amount_min"),
            'amount.max' => __("backend.amount_max"),
            'court_id.required' => __("backend.court_id_required"),
            'court_id.unique' => __("backend.court_id_unique"),
            'facility_id.required' => __("backend.facility_id_required"),
        ];
        $this->validate($request, [
            'amount' => 'required|numeric|min:1|max:100',
            'court_id' => 'required|unique:commissions,court_id,'.$id,
            'facility_id' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
      
        if (!$fail) {
            try {
                $data = Commission::where('id', $id)->update(['amount' => $input['amount'],'facility_id' => $input['facility_id'], 'court_id' => $input['court_id']]);
                $result['message'] = __('backend.commission_update_successfully');
                $result['status'] = 1;
                return response()->json($result);
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        } else {
            $result['message'] = __('backend.Something_went_wrong');
            $result['status'] = 0;
            return response()->json($result);
        }
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Gate::authorize('Commission-delete');
        return Courts::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = Commission::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $commission = Commission::findOrFail($id);
            if ($commission->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.commission_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.commission_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.commission_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
