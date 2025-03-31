<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Country;
use App\Models\Orders;
use App\Models\UsersAddress;
use App\Models\Restaurant;
use App\Models\GiftOrder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkExport;
use App\Imports\BulkImport;
use DB;
use App\Models\EmailTemplateLang;
use Mail;

class SubadminController extends Controller
{
    public function __construct() {
		$this->middleware('auth');
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        Gate::authorize('Users-section');
        $columns = ['users.name','users.email','users.mobile','users.country_code'];

        $user=User::where('type', 2);
        // dd($user->get()->toArray());
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($user->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->editColumn('mobile', function ($user) {
            return $user->mobile = $user->country_code.' '.$user->mobile;

         })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date)) {
				$query->whereBetween(DB::raw('DATE(users.created_at)'), array($request->from_date, $request->to_date));
			}

            if(!empty($request->from_price) && !empty($request->to_price)) {
                // $query->having('totalAvlAmount','>=',$request->from_price)->having('totalAvlAmount','<=',$request->to_price);
                $query->totalorderCount()->havingRaw('totalAvlAmount BETWEEN ? AND ?',  [$request->from_price, $request->to_price]);

            } else {

                if ($request->from_price != '' && $request->to_price != '') {
                    $query->totalorderCount()->havingRaw('totalAvlAmount BETWEEN ? AND ?',  [$request->from_price, $request->to_price]);
                }
            }


            /*$query->whereHas('getTotalOrderAttribute', function($q) {
                dd($q);
                // $q->whereBetween('content', 'like', 'foo%');
            });*/

            if ($request->has('country')) {
                $country = array_filter($request->country);
                if(count($country) > 0) {
                    $query->whereIn('users.country_code', $request->get('country'));
                }
            }

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               if(isset($search['value']))
               {
                    $query->where(function($q)use($search){
                    $q->where('users.name', 'like', "%{$search['value']}%")
                        ->orWhere('users.email', 'like', "%{$search['value']}%")
                        ->orWhere('users.mobile', 'like', "%{$search['value']}%");
                    });
                }
               //$query->where('users.name', 'like', "%{$search['value']}%");
              // $query->orWhere('users.email', 'like', "%{$search['value']}%");
              // $query->orWhere('users.mobile', 'like', "%{$search['value']}%");
            }

            
		})->addIndexColumn()->make(true);
    }

    public function frontend()
    {
        Gate::authorize('Users-section');
        $user = User::where('type', '2')->get();
        $wallet_amount = 0;

        foreach ($user as $key => $value) {
            $wallet_amount += $value->total_wallet;
            
        }
        $data['roles']=Role::all();
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['wallet_amount']=$wallet_amount;
        return view('subadmin.listing',$data);
    }

    public function getWalletData(request $request) {
        $user=User::totalorderCount()->where('type', '2');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $user->whereBetween(DB::raw('DATE(users.created_at)'), array($request->from_date, $request->to_date));
        }

        if(!empty($request->from_price) && !empty($request->to_price)) {
            // $query->having('totalAvlAmount','>=',$request->from_price)->having('totalAvlAmount','<=',$request->to_price);
            $user->havingRaw('totalAvlAmount BETWEEN ? AND ?',  [$request->from_price, $request->to_price]);

        } else {

            if ($request->from_price != '' && $request->to_price != '') {
                $user->havingRaw('totalAvlAmount BETWEEN ? AND ?',  [$request->from_price, $request->to_price]);
            }
        }

        if ($request->has('country')) {
            $country = array_filter($request->country);

            if(count($country) > 0) {
                $user->whereIn('users.country_code', $request->country);
            }
        }

        $data = $user->get();

        $wallet_amount = 0;

        if ($data) {

            foreach ($data as $key => $value) {
                $wallet_amount += $value->total_wallet;
                
            }            
        }

        $result['message'] = 'Wallet amount';
        $result['status'] = 1;
        $result['data'] = $wallet_amount;
        return response()->json($result);


    }

    public function edit_frontend($id)
    {
        Gate::authorize('Users-edit');
        //$data['roles']=Role::all();
        $data['users'] = User::findOrFail($id);
        $data['country']=Country::select('phonecode','name','id')->get();
        //$data['staff_roles']=$data['staff']->getRoleNames()->toArray();
        return view('subadmin.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Users-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Users-create');
         // validate
		$this->validate($request, [
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'mobile'=> 'required|numeric|digits_between:7,15|unique:users,mobile,,0,country_code,'.str_replace("+","",$request->country_code),
            'country_code' => 'required',
            'password' => 'required|min:6|max:20',
            'confirm_password' => 'required|same:password',
		]);
		// create a new task based on user tasks relationship
		$user = User::create([
            'name' => $request->first_name . ' '.  $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'country_code' => str_replace("+","",$request->country_code),
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'latitude'=>$request->latitude,
            'longitude'=>$request->longitude,
            'type' => '2',
            'status' => 1,
        ]);

        $email = EmailTemplateLang::where('email_id', 3)->where('lang', 'en')->select(['name', 'subject', 'description','footer'])->first();
        $description = $email->description;
        $description = str_replace("[NAME]", $request->first_name, $description);

        $name = $email->name;
        $name = str_replace("[NAME]", $request->first_name, $name);

        $register_detail=(object)[];
        $register_detail->description = $description;
        $register_detail->footer = $email->footer;
        $register_detail->name = $name;
        $register_detail->subject = $email->subject;

        Mail::send('emails.register', compact('register_detail'), function($message)use($user, $email) {
            $message->to($user->email, config('app.name'))->subject($email->subject);
            $message->from('support@contactless.com',config('app.name'));
        });

        if($user->id){
            $result['message'] = 'Subadmin '.$user->name.' has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Subadmin Can`t created';
            $result['status'] = 0;
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
        Gate::authorize('Users-section');
        $data['users'] = User::findOrFail($id);

        return view('subadmin.view',$data);
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
        Gate::authorize('Users-edit');
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
        Gate::authorize('Users-edit');
        // validate
		$this->validate($request, [
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'country_code' 		=> 'required',
            'mobile'=> 'required',
            'confirm_password' => 'same:password',
            ]);

        // $input = $request->all();
        $inp=[
            'name' => $request->first_name . ' '.  $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->editaddress,
            'latitude'=>$request->editlatitude,
            'longitude'=>$request->editlongitude,
            'country_code' => str_replace("+","",$request->country_code),
        ];
        if($request->password){
            $inp['password']=Hash::make($request->password);
        }
        $staff = User::findOrFail($id);
       // $staff->getRoleNames();
        $staff->update($inp);
        //$staff->syncRoles($request->roles);
		return response()->json($staff->find($staff->id));
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
        Gate::authorize('Users-delete');
        return User::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = User::find($id);
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $User = User::findOrFail($id);
            if($User->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Subadmin Account is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Subadmin Account is deactivate successfully';
                    $result['status'] = 1;
                }
            }else{
                $result['message'] = 'Subadmin Account status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new BulkExport, 'Subadmin.csv');
    }

    public function importUsers()
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
