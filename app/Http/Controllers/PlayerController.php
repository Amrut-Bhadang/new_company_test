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
use App\Exports\PlayerExport;
use App\Imports\BulkImport;
use App\Models\Country;
use DB;
use App\Models\EmailTemplateLang;
use App\Models\Player;
use Exception;
use Mail;

class PlayerController extends Controller
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

        Gate::authorize('Player-section');
        $columns = ['courts.court_name', 'courts.adress'];

        //$player = User::where('type', 3)->orWhere('is_facility_owner',1);
        $player = User::where('type', 3)->where('is_deleted',0)->orderBy('id','DESC');
        return Datatables::of($player)->editColumn('created_at', function ($player) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($player->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(users.created_at)'), array($request->from_date, $request->to_date));
            }
            // if ($request->has('player_status')) {

            //     if ($request->get('player_status') && $request->get('player_status') == 'Active') {
            //         $query->where('users.status', 1);
            //     } else if ($request->get('player_status') && $request->get('player_status') == 'Deactive') {
            //         $query->where('users.status', 0);
            //     }
            // }
           
            if ($request->has('player_status')) {

                    if ($request->get('player_status')!='' && $request->get('player_status') == 'Active') {
                        $query->where('users.status', 1);
                    } else if ($request->get('player_status')!='' && $request->get('player_status') == 'Deactive') {
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
        Gate::authorize('Player-section');
        $player = User::all();
       
        $data['roles'] = Role::all();
        return view('player.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Player-edit');
        $data['player'] = User::findOrFail($id);
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('player.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Player-create');

        // $data = array();
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('player.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Player-create');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'email.required' => __("backend.email_required"),
            'email.email' => __("backend.email_email"),
            'email.unique' => __("backend.email_unique"),
            'country_code.required' => __("backend.country_code_required"),
            'gender.required' => __("backend.gender_required"),
            'mobile.required' => __("backend.mobile_required"),
            'mobile.digits_between' => __("backend.mobile_digits_between"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'email' => 'required|email|unique:users,email',
            'country_code' => 'required',
            'gender' => 'required',
            'mobile' => 'required|digits_between:8,12',

        ], $mesasge);

        $input = $request->all();
        $fail = false;
        $player = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->first();

    if (!$player) {
        if (!$fail) {
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
                $data->first_name = $input['name'];
                $data->email = $input['email'];
                $data->mobile = $input['mobile'];
                $data->gender = $input['gender'];
                $data->country_code = $input['country_code'];
                $data->status = 1;
                $data->type = 3;
                $data->password = Hash::make('123456');//New dev
                $data->save();
              

                $result['message'] = __('backend.player_created');
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
    } else {
        $result['message']  = __("backend.mobile_number_already_exist");
        $result['status']   = false;
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
        Gate::authorize('Player-section');
        $data['record'] = User::findOrFail($id);

        return view('player.view', $data);
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
        Gate::authorize('Player-edit');
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
        // dd('update');
        Gate::authorize('Player-edit');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'email.required' => __("backend.email_required"),
            'email.email' => __("backend.email_email"),
            'email.unique' => __("backend.email_unique"),
            'country_code.required' => __("backend.country_code_required"),
            'gender.required' => __("backend.gender_required"),
            'mobile.required' => __("backend.mobile_required"),
            'mobile.digits_between' => __("backend.mobile_digits_between"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'email' => 'required|email|unique:users,email,'.$id,
            'country_code' => 'required',
            'gender' => 'required',
            'mobile' => 'required|digits_between:8,12',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
        $player = User::where(['country_code' => $input['country_code'], 'mobile' => $input['mobile']])->whereNotIn('id', [$id])->first();

    if (!$player) {
        if (!$fail) {
            $file = $request->file('image');
            try {
                        if (isset($file)) {
                            $result = image_upload($file, 'user');
                            $data = User::where('id', $id)->update(['name' => $input['name'], 'email' => $input['email'], 'mobile' => $input['mobile'], 'country_code' => $input['country_code'], 'gender' => $input['gender'],'image' => $result[1],'image_type'=>'local']);
                        } else {
                            $data = User::where('id', $id)->update(['name' => $input['name'], 'email' => $input['email'], 'mobile' => $input['mobile'], 'gender' => $input['gender'], 'country_code' => $input['country_code']]);
                        }                   
              
                $result['message'] = __('backend.player_update_successfully');
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
    } else {
        $result['message']  = __("backend.mobile_number_already_exist");
        $result['status']   = false;
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
        Gate::authorize('Player-delete');
        return Courts::findOrFail($id)->delete();
    }

    public function delete_users($id, $is_delete)
    {
        $details = User::find($id);
        if (!empty($details)) {
            $CourtBooking =CourtBooking:: where('user_id',$details->id)->first();
            if($CourtBooking!=''){
                $result['message'] = 'Player cant not delete because order already placed';
                $result['status'] = 0;
            }else{
                if ($is_delete == '1') {
                    $inp = 1;
                } else {
                    $inp = 0;
                }
                $User = User::findOrFail($id);
                $User->is_deleted =$inp;
                $User->deleted_at =date('Y-m-d H:i:s');
                $User->update();
                if ($User) {
                    if ($User['is_deleted'] == '1') {
                        $result['message'] = 'Player is deleted successfully';
                        $result['status'] = 1;
                    } else {
                        $result['message'] = 'Player not deleted';
                        $result['status'] = 0;
                    }
                } else {
                    $result['message'] = 'Player action can`t be updated!!';
                    $result['status'] = 0;
                }
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
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
           
            $player = User::findOrFail($id);
            if ($player->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.player_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.player_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.player_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function is_facility_owner($id, $status)
    {
        // dd($id, $status);
        $details = User::find($id);
        if (!empty($details)) {
            if ($status == 'yes') {
                $inp = ['is_facility_owner' => 1, 'type' => 1];
                  // set permissions
                  $restro_permissions = ['148', '149', '150', '151', '163', '164', '165', '166'];
                  foreach ($restro_permissions as $key => $value) {
                      $user = User::findOrFail($id);
                      $user->givePermissionTo($value);
                  }
              // end permissions
            } else {
                $inp = ['is_facility_owner' => 0, 'type' => 3];
            }
            $player = User::findOrFail($id);
            if ($player->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.player_is_facility_owner_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.player_is_facility_owner_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.player_is_facility_owner_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function exportPlayers($slug)
    {
        Gate::authorize('Player-section');
        return Excel::download(new PlayerExport, 'players.csv');
    }
}
