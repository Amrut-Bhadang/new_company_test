<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Courts;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacilityOwnerExport;
use App\Models\Country;
use App\Models\EmailTemplateLang;
use App\Models\PasswordReset as ModelsPasswordReset;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class FacilityOwnerController extends Controller
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
        Gate::authorize('Facility_owner-section');
        $columns = ['courts.court_name', 'courts.adress'];
        $user = User::where('type', 1);
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
                $query->whereBetween(DB::raw('DATE(users.created_at)'), array($request->from_date, $request->to_date));
            }
            if ($request->has('category_id') && $request->category_id) {
                $query->whereIn('users.category_id', $request->category_id);
            }
            if ($request->has('facility_owner_status')) {
                if ($request->get('facility_owner_status') && $request->get('facility_owner_status') == 'Active') {
                    $query->where('users.status', 1);
                } else if ($request->get('facility_owner_status') && $request->get('facility_owner_status') == 'Deactive') {
                    $query->where('users.status', 0);
                }
            }
            if (!empty($request->get('search'))) {
                $search = $request->get('search');
                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', "%{$search['value']}%")
                            ->orWhere('users.email', 'like', "%{$search['value']}%")
                            ->orWhere('users.mobile', 'like', "%{$search['value']}%");
                           
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }
    public function frontend()
    {
        Gate::authorize('Facility_owner-section');
        $user = User::where('type', '1')->get();
        $data['roles'] = Role::all();
        return view('facility_owner.listing', $data);
    }
    public function edit_frontend($id)
    {
        Gate::authorize('Facility_owner-edit');
        //$data['roles']=Role::all();
        $data['users'] = User::findOrFail($id);
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('facility_owner.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Facility_owner-create');
        // $data = array();
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('facility_owner.add', $data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Facility_owner-create');
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'email.required' => __("backend.email_required"),
            'email.email' => __("backend.email_email"),
            'email.unique' => __("backend.email_unique"),
            'country_code.required' => __("backend.country_code_required"),
            //'gender.required' => __("backend.gender_required"),
            'mobile.digits_between' => __("backend.mobile_digits_between"),
            'image.size'  =>  __("backend.image_size"),
        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'email' => 'required|email|unique:users,email',
            'country_code' => 'required',
            //'gender' => 'required',
            //'mobile' => 'required|digits_between:8,12|unique:users,mobile',
            'mobile' => 'required|digits_between:8,12',
            'show_post_method' => 'required',
        ], $mesasge);
        $input = $request->all();
       // $fail = false;
       // if (!$fail) {
            try {
                $data = new User;
                
                if ($request->file('image')) {
                    $file = $request->file('image');
                    $result = image_upload($file, 'user');
                    if ($result[0] == true) {
                        $data->image = $result[1];
                    }
                }
                $data->name = $input['name'];
                $data->email = $input['email'];
                $data->mobile = $input['mobile'];
                $data->country_code = $input['country_code'];
               // $data->gender = $input['gender'];//new dev
                $data->show_post_method = $input['show_post_method'] ?? null;
                $data->status = 1;
                $data->type = 1;
                $data->save();
                // set permissions
                    $restro_permissions = ['148', '149', '150', '151', '163', '164', '165', '166'];
                    foreach ($restro_permissions as $key => $value) {
                        $user = User::findOrFail($data->id);
                        $user->givePermissionTo($value);
                    }
                // end permissions
                // forgot password start
                $facility_owner = $data;
                $token = Str::random(60);
                $email =  $facility_owner->email;
                $user = ModelsPasswordReset::where('email', $email)->first();
                if (!isset($user)) {
                    $user = new ModelsPasswordReset;
                    $user->email = $email;
                    $user->token = $token;
                    $user->save();
                } else {
                    ModelsPasswordReset::where('email', $email)->update(['token' => $token]);
                }
                $user = ModelsPasswordReset::where('email', $email)->first();
                // send email start
                $email = EmailTemplateLang::where('email_id', 3)->where('lang', 'en')->select(['name', 'subject', 'description', 'footer'])->first();
                $description = $email->description;
                $description = str_replace("[NAME]", $facility_owner->name, $description);
                $name = $email->name;
                $name = str_replace("[NAME]", $facility_owner->name, $name);
                $record = (object)[];
                $record->description = $description;
                $record->footer = $email->footer;
                $record->name = $name;
                $record->subject = $email->subject;
                $record->user_email = $user->email;
                $record->user_token = $user->token;
                Mail::send('emails.welcome', compact('record'), function ($message) use ($facility_owner, $email) {
                    $message->to($facility_owner->email, config('app.name'))->subject($email->subject);
                    //$message->from('dev.inventcolabs@gmail.com', config('app.name'));
                    $message->from('amrutofficial05@gmail.com', config('app.name'));
                    //dd($message);
                });
                // send email end
                // forgot password end
              
                $result['message'] = __('backend.Facility_owner_created');
                $result['status'] = 1;
                return response()->json($result);
            } catch (Exception $e) {
                $result['message'] = __('backend.Facility_owner_created');
                $result['status'] = 1;
                return response()->json($result);
              
            }
        // } else {
        //     $result['message'] = __('backend.Something_went_wrong');
        //     $result['status'] = 0;
        //     return response()->json($result);
        // }
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
        Gate::authorize('Facility_owner-section');
        $data['record'] = User::findOrFail($id);
        return view('facility_owner.view', $data);
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
        Gate::authorize('Facility_owner-edit');
        $user = User::findOrFail($id);
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
        Gate::authorize('Facility_owner-edit');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'email.required' => __("backend.email_required"),
            'email.email' => __("backend.email_email"),
            'email.unique' => __("backend.email_unique"),
            'country_code.required' => __("backend.country_code_required"),
            // 'gender.required' => __("backend.gender_required"),
            'mobile.required' => __("backend.mobile_required"),
            'mobile.digits_between' => __("backend.mobile_digits_between"),
            'image.size'  =>  __("backend.image_size"),
        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'email' => 'required|email|unique:users,email,' . $id,
            'country_code' => 'required',
            // 'gender' => 'required',
            'mobile' => 'required|digits_between:7,15|unique:users,mobile,' . $id,
            'show_post_method' => 'required',
        ], $mesasge);
        $input = $request->all();
        $fail = false;
        if (!$fail) {
            $file = $request->file('image');
            try {
                if (isset($file)) {
                    $result = image_upload($file, 'user');
                    $data = User::where('id', $id)->update(['name' => $input['name'], 'email' => $input['email'], 'mobile' => $input['mobile'], 'country_code' => $input['country_code'], 'gender' => $input['gender'], 'show_post_method' => $input['show_post_method'] ?? null, 'image' => $result[1]]);
                } else {
                    $data = User::where('id', $id)->update(['name' => $input['name'], 'email' => $input['email'], 'mobile' => $input['mobile'], 'country_code' => $input['country_code'], 'gender' => $input['gender'],'show_post_method' => $input['show_post_method'] ?? null]);
                }
                $result['message'] = __('backend.facility_owner_update_successfully');
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
        Gate::authorize('Facility_owner-delete');
        return Courts::findOrFail($id)->delete();
    }
    public function changeStatus($id, $status)
    {
        $details = User::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $User = User::findOrFail($id);
            if ($User->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.Facility_Owner_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.Facility_Owner_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.Facility_Owner_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function exportFacilityOwner($slug)
    {
        Gate::authorize('Facility_owner-section');
        return Excel::download(new FacilityOwnerExport, 'facility_owners.csv');
    }
}