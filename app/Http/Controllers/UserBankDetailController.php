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
use App\Exports\FacilityOwnerExport;
use App\Imports\BulkImport;
use App\Models\Country;
use DB;
use App\Models\EmailTemplateLang;
use App\Models\PasswordReset as ModelsPasswordReset;
use App\Models\UserBankDetail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Auth;
use Mail;
use Illuminate\Support\Str;


class UserBankDetailController extends Controller
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
        Gate::authorize('User_bank_detail-section');
        $columns = ['courts.court_name', 'courts.adress'];
        $login_user = Auth::user();
        if ($login_user->type == '0') {
        $user = UserBankDetail::query();
    } else {
        $user = UserBankDetail:: where('user_id', $login_user->id);
    }
        // dd($user->get()->toArray());
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($user->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(user_bank_detail.created_at)'), array($request->from_date, $request->to_date));
            }

            if ($request->has('category_id') && $request->category_id) {
                $query->whereIn('user_bank_detail.category_id', $request->category_id);
            }
            if ($request->has('bank_detail_status')) {

                if ($request->get('bank_detail_status') && $request->get('bank_detail_status') == 'Active') {
                    $query->where('user_bank_detail.status', 1);
                } else if ($request->get('bank_detail_status') && $request->get('bank_detail_status') == 'Deactive') {
                    $query->where('user_bank_detail.status', 0);
                }
            }
            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('user_bank_detail.name', 'like', "%{$search['value']}%")
                            ->orWhere('user_bank_detail.email', 'like', "%{$search['value']}%")
                            ->orWhere('user_bank_detail.mobile', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
        Gate::authorize('User_bank_detail-section');
        $user = UserBankDetail::query();
        $data['roles'] = Role::all();
        return view('user_bank_detail.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('User_bank_detail-edit');
        //$data['roles']=Role::all();
        $data['users'] = UserBankDetail::findOrFail($id);
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();

        return view('user_bank_detail.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('User_bank_detail-create');
        // $data = array();
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();
        return view('user_bank_detail.add', $data);
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
        Gate::authorize('User_bank_detail-create');
        // validate
        $mesasge = [
            'bank_name.required' => __("backend.bank_name_required"),
            'bank_code.required' => __("backend.bank_code_required"),
            'bank_address.required' => __("backend.bank_address_required"),
            'account_type.required' => __("backend.account_type_required"),
            'account_number.required' => __("backend.account_number_required"),
            'account_number.unique' => __("backend.account_number_unique"),
            'account_holder_name.required' => __("backend.account_holder_name_required"),
            'user_id.required' => __("backend.user_id_required"),
            'image.size'  =>  __("backend.image_size"),
        ];
        $this->validate($request, [
            'bank_name' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'bank_code' => 'required|max:255',
            'bank_address' => 'required|max:255',
            'account_type' => 'required|max:255',
            'account_number' => 'required|max:255|unique:user_bank_detail,account_number',
            'account_holder_name' => 'required|max:255',
            'user_id' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
        if (!$fail) {
            try {

                $data = new UserBankDetail();
                if ($request->file('image')) {
                    $file = $request->file('image');
                    $result = image_upload($file, 'bank_detail');
                    if ($result[0] == true) {
                        // dd($result);
                        $data->passbook_image = $result[1];
                    }
                }
                $data->bank_name = $input['bank_name'];
                $data->bank_code = $input['bank_code'];
                $data->bank_address = $input['bank_address'];
                $data->account_type = $input['account_type'];
                $data->account_number = $input['account_number'];
                $data->account_holder_name = $input['account_holder_name'];
                $data->user_id = $input['user_id'];
                $data->status = 1;
                $data->save();
             
                $result['message'] = __('backend.Bank_detail_created');
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
        //
        Gate::authorize('User_bank_detail-section');
        $data['record'] = UserBankDetail::findOrFail($id);
        return view('user_bank_detail.view', $data);
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
        Gate::authorize('User_bank_detail-edit');
        $user = UserBankDetail::findOrFail($id);
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
        // dd($request->all(),'dddddddddddddd');
        Gate::authorize('User_bank_detail-edit');
        // validate
        $mesasge = [
            'bank_name.required' => __("backend.bank_name_required"),
            'bank_code.required' => __("backend.bank_code_required"),
            'bank_address.required' => __("backend.bank_address_required"),
            'account_type.required' => __("backend.account_type_required"),
            'account_number.required' => __("backend.account_number_required"),
            'account_number.unique' => __("backend.account_number_unique"),
            'account_holder_name.required' => __("backend.account_holder_name_required"),
            'user_id.required' => __("backend.user_id_required"),
            'image.size'  =>  __("backend.image_size"),
        ];
        $this->validate($request, [
            'bank_name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'bank_code' => 'required|max:255',
            'bank_address' => 'required|max:255',
            'account_type' => 'required|max:255',
            'account_number' => 'required|max:255|unique:user_bank_detail,account_number,'.$id,
            'account_holder_name' => 'required|max:255',
            'user_id' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            $file = $request->file('image');
            try {
                if (isset($file)) {
                    $result = image_upload($file, 'bank_detail');
                    $data = UserBankDetail::where('id', $id)->update(['bank_name' => $input['bank_name'], 'bank_code' => $input['bank_code'], 'bank_address' => $input['bank_address'], 'account_type' => $input['account_type'], 'account_number' => $input['account_number'], 'account_holder_name' => $input['account_holder_name'], 'user_id' => $input['user_id'], 'passbook_image' => $result[1]]);
                } else {
                    $data = UserBankDetail::where('id', $id)->update(['bank_name' => $input['bank_name'], 'bank_code' => $input['bank_code'], 'bank_address' => $input['bank_address'], 'account_type' => $input['account_type'], 'account_number' => $input['account_number'], 'account_holder_name' => $input['account_holder_name'], 'user_id' => $input['user_id']]);
                }



                $result['message'] = __('backend.bank_detail_update_successfully');
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
        Gate::authorize('User_bank_detail-delete');
        return Courts::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = UserBankDetail::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $User = UserBankDetail::findOrFail($id);
            if ($User->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.Bank_detail_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.Bank_detail_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.Bank_detail_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function exportUserBankDetail($slug)
    {
        Gate::authorize('User_bank_detail-section');
        return Excel::download(new FacilityOwnerExport, 'user_bank_detail.csv');
    }
}
