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
use App\Models\PanelNotifications;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkExport;
use App\Imports\BulkImport;
use DB;
use App\Models\EmailTemplateLang;
use Mail;

class OperatorController extends Controller
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
        Gate::authorize('Operator-section');
        $columns = ['users.name','users.email','users.mobile','users.country_code'];
        $login_user_data = auth()->user();

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $user = User::select('users.*','restaurants.name as restaurant_name')->join('restaurants', 'restaurants.id', '=', 'users.restaurant_id')->where(['users.type'=>5, 'users.restaurant_id'=>$restaurant_detail->id]);

        } else {
            $user = User::select('users.*','restaurants.name as restaurant_name')->join('restaurants', 'restaurants.id', '=', 'users.restaurant_id')->where('users.type', 5);
        }
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

            if ($request->has('restaurant_id') && $request->restaurant_id) {
                $query->where('users.restaurant_id', $request->get('restaurant_id'));
            }

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
                        ->orWhere('users.mobile', 'like', "%{$search['value']}%")
                        ->orWhere('restaurants.name', 'like', "%{$search['value']}%");
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
        Gate::authorize('Operator-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;

        } else {
            $data['restaurant_id'] = '';
        }
        $data['restaurants']=Restaurant::select('name','id')->where(['status'=>1])->get();

        $user = User::where('type', '5')->get();
        $wallet_amount = 0;

        foreach ($user as $key => $value) {
            $wallet_amount += $value->total_wallet;
            
        }
        $data['roles']=Role::all();
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['wallet_amount']=$wallet_amount;
        return view('operator.listing',$data);
    }

    public function getWalletData(request $request) {
        $user=User::totalorderCount()->where('type', '5');

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
        Gate::authorize('Operator-edit');
        //$data['roles']=Role::all();
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;

        } else {
            $data['restaurant_id'] = '';
        }
        $data['restaurants']=Restaurant::select('name','id')->where(['status'=>1])->get();
        $data['users'] = User::findOrFail($id);
        $data['country']=Country::select('phonecode','name','id')->get();
        //$data['staff_roles']=$data['staff']->getRoleNames()->toArray();
        return view('operator.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Operator-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Operator-create');
         // validate
		$this->validate($request, [
            'restaurant_id'=>'required',
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
            'restaurant_id' => $request->restaurant_id,
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
            'type' => '5',
            'status' => 1,
        ]);

        $permissions = ['52','53','54','55'];

        foreach ($permissions as $key => $value) {
            $user = User::findOrFail($user->id);
            $user->givePermissionTo($value);
        }

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
            $result['message'] = 'Operator '.$user->name.' has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Operator Can`t created';
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
        Gate::authorize('Operator-section');
        $data['users'] = User::findOrFail($id);

        return view('operator.view',$data);
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
        Gate::authorize('Operator-edit');
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
        Gate::authorize('Operator-edit');
        // validate
		$this->validate($request, [
            'restaurant_id'=>'required',
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'country_code' 		=> 'required',
            'mobile'=> 'required',
            'confirm_password' => 'same:password',
            ]);

        // $input = $request->all();
        $inp=[
            'restaurant_id' => $request->restaurant_id,
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
        Gate::authorize('Operator-delete');
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
                    $result['message'] = 'Operator Account is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Operator Account is deactivate successfully';
                    $result['status'] = 1;
                }
            }else{
                $result['message'] = 'Operator Account status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function send_notification(Request $request)
    {
        $this->validate($request, [
            'user_id'=>'required|max:255',
            'title'=>'required|max:255',
            'message'=>'required',
        ]);

        $details = User::find($request->user_id);

        if(!empty($details)){
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['id'=>$details->restaurant_id])->first();
            //Panel Notification data
            $panelNotificationData = new PanelNotifications;
            $panelNotificationData->user_id = $request->user_id;
            $panelNotificationData->order_id = null;
            $panelNotificationData->user_type = 5;
            $panelNotificationData->notification_for = 'Operator-Notify';
            $panelNotificationData->notification_type = 3;
            $panelNotificationData->title = $request->title;
            $panelNotificationData->message = $request->message;
            
            if ($panelNotificationData->save()) {
                $panelData = PanelNotifications::select('panel_notifications.*','orders.random_order_id')->leftJoin('orders','orders.id','=','panel_notifications.order_id');
                $adminCount = 0;
                $restroCount = 0;

                if ($restaurant_detail->id) {
                    $panelData->where('panel_notifications.user_id', $restaurant_detail->id);
                    $restroCount = $panelData->where('panel_notifications.is_read', 0)->count();
                }
                $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();
                /*For Store*/
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$request->user_id."/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount,\n  \"restroCount\":$restroCount\n}\n",
                  CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                    "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$request->user_id."/0",
                    "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                  ),
                ));

                $responseNew = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                  // echo "cURL Error #:" . $err;
                } else {
                  // echo $responseNew;
                }
                /*For Restro End*/
            }

            $result['message'] = 'Notification sent successfully.';
            $result['status'] = 1;

        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Operator-section');
        return Excel::download(new BulkExport, 'Operator.csv');
    }

    public function importUsers()
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
