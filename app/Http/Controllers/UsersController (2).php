<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Country;
use App\Models\Orders;
use App\Models\UsersAddress;
use App\Models\Restaurant;
use App\Models\MainCategory;
use App\Models\GiftOrder;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkExport;
use App\Exports\CustomerTransExport;
use App\Exports\CustomerGiftExport;

use App\Imports\BulkImport;
use DB;
use App\Models\EmailTemplateLang;
use Mail;

class UsersController extends Controller
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

        $user=User::where('type', 0);
        // dd($user->get()->toArray());
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($user->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
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

            if ($request->has('main_category_id') && $request->main_category_id) {
                $userIds = Orders::join('restaurants', 'restaurants.id','=','orders.restaurant_id')->where('restaurants.main_category_id',$request->main_category_id)->groupBy('orders.user_id')->pluck('orders.user_id')->toArray();
                
                $query->whereIn('users.id', $userIds);
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
        $user = User::where('type', '0')->get();
        $wallet_amount = 0;

        foreach ($user as $key => $value) {
            $wallet_amount += $value->total_wallet;
            
        }
        $data['roles']=Role::all();
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['wallet_amount']=$wallet_amount;
        return view('staff.listing',$data);
    }

    public function getWalletData(request $request) {
        $user=User::totalorderCount()->where('type', '0');

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

        if ($request->has('main_category_id') && $request->main_category_id) {
            $userIds = Orders::join('restaurants', 'restaurants.id','=','orders.restaurant_id')->where('restaurants.main_category_id',$request->main_category_id)->groupBy('orders.user_id')->pluck('orders.user_id')->toArray();
            
            $user->whereIn('users.id', $userIds);
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
        $result['data'] = number_format($wallet_amount, 2);
        return response()->json($result);


    }

    public function edit_frontend($id)
    {
        Gate::authorize('Users-edit');
        //$data['roles']=Role::all();
        $data['users'] = User::findOrFail($id);
        $data['country']=Country::select('phonecode','name','id')->get();
        //$data['staff_roles']=$data['staff']->getRoleNames()->toArray();
        return view('staff.edit',$data);
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
            'type' => '0',
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
      
        if ($user->id) {
            $userNewDB = new User;
            $userNewDB->name = $request->first_name.' '.$request->last_name;
            $userNewDB->first_name = $request->first_name;
            $userNewDB->last_name = $request->last_name;
            $userNewDB->country_code = str_replace("+","",$request->country_code);
            $userNewDB->mobile = $request->mobile;
            $userNewDB->password = Hash::make($request->password);
            $userNewDB->status = 1;
            $userNewDB->type = '0';
            $userNewDB->setConnection('mysql2');

            if ($userNewDB->save()) {
                //update user data
                $updateData = [];
                $updateData['share_code'] = 'KILO-USER-'.$user->id;
                $updateData['gift_user_id'] = $userNewDB->id;
                User::where('id',$user->id)->update($updateData);
            }
            $result['message'] = 'Customer '.$user->name.' has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Customer Can`t created';
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

        return view('staff.view',$data);
    }

    public function view_orders($id){
       Gate::authorize('Users-section');
       $data['orders'] = Orders::select('orders.*', 'orders.id as order_id')->where('user_id',$id)->orderBy('id','desc')->get();

       if ($data['orders']) {

            foreach ($data['orders'] as $key => $value) {
                $timezone = 'Asia/Kolkata';

                if(isset($_COOKIE['timezone'])) {
                    $timezone = $_COOKIE['timezone'];
                }
                $tz = new \DateTimeZone($timezone);
                // $tz = new \DateTimeZone('Asia/Kolkata');
                $dt = new \DateTime($value->created_at);
                $dt->setTimezone($tz);
                $value->created_at = $dt->format('Y-m-d H:i:s');
            }
       }
       //$data['restaurant_name'] = Restaurant::select('name')->where('id',$orders->restaurant_id)->first();
       return view('staff.view_order',$data);
    }

    public function view_address($id){
        Gate::authorize('Users-section');
        $data['address'] = UsersAddress::where('user_id',$id)->get();
        return view('staff.view_address',$data);
    }

    public function showtransaction($id, Request $request){
        Gate::authorize('Users-section');
        $transaction = Orders::select('orders.id','orders.id as order_id','orders.order_status','orders.random_order_id','orders.created_at','orders.amount','orders.restaurant_name as store_name','main_category.name as service_type')->join('main_category', 'main_category.id', '=', 'orders.main_category_id')->where('orders.user_id',$id);

        if (!empty($request->from_price) && !empty($request->to_price)) {
            $transaction->whereBetween('orders.amount', array($request->from_price, $request->to_price));
        }
        // $data['total'] = $transaction->select(DB::raw('SUM(amount)' as 'total_amount'))->first();
        $data['transaction'] = $transaction->orderBy('id','desc')->get();

        if ($data['transaction']) {

            foreach ($data['transaction'] as $key => $value) {
                $timezone = 'Asia/Kolkata';

                if(isset($_COOKIE['timezone'])) {
                    $timezone = $_COOKIE['timezone'];
                }
                $tz = new \DateTimeZone($timezone);
                // $tz = new \DateTimeZone('Asia/Kolkata');
                $dt = new \DateTime($value->created_at);
                $dt->setTimezone($tz);
                $value->created_at = $dt->format('Y-m-d H:i:s');
            }
        }
        $data['total'] = $transaction->selectRaw("(select sum(amount)) as totalAvlAmount")->first();
        $data['id'] = $id;
        // dd($data);

        /*if(!empty($request->from_date) && !empty($request->to_date))
        {
            $query->whereBetween('order.created_at', array($request->from_date, $request->to_date)); 
        }*/
        return view('staff.transaction',$data);
    }

    public function exportTransUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new CustomerTransExport($slug), 'CustomerTransaction.csv',);
    }

    public function showgifttransaction($id){
        Gate::authorize('Users-section');
        $data['gift_trans'] = GiftOrder::select('gift_orders.id','gift_orders.random_order_id','gift_orders.order_status','gift_orders.created_at','gift_orders.points','gift_orders.address')->where('gift_orders.user_id',$id)->get();
        $data['id'] = $id;
        return view('staff.giftstransaction',$data);
    }
    
    public function exportGiftUsers($slug)
    {
        Gate::authorize('Users-section');
        return Excel::download(new CustomerGiftExport($slug), 'CustomerGiftTransaction.csv',);
    }

    /*public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        $export_data=array();
        $export_data[]=array('id'=>'1',
            'user_name'=>'Test',
            'email'=>'Test',
            'mobile'=>'Test',
            'address'=>'Test',
            'status'=>'Test',
            'profile_pic'=> 'Test',
            'created_at'=> 'Test'
        );

        $export_data=User::select('name','email','country_code','mobile','type','created_at','id','status')->where('type', '0')->get();
        // dd($export_data);

        return Excel::download($export_data, 'list.xlsx');
        // });
    }*/

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
            'email' => 'string|email|unique:users,email, '. $id .',id',
            'mobile'=> 'required|numeric|digits_between:7,15|unique:users,mobile, '. $id .',id,country_code,'.str_replace("+","",$request->country_code),
            'country_code' 		=> 'required',
            'confirm_password' => 'same:password',
            ]);

        // $input = $request->all();
        $inp=[
            'name' => $request->first_name . ' '.  $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            // 'password' => Hash::make($request->password),
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

        $userNewDB = User::on('mysql2')->where('id', $staff->gift_user_id)->first(); // static method

        if ($userNewDB) {
            User::on('mysql2')->where('id', $staff->gift_user_id)->update($inp);
        }
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
                    $result['message'] = 'Customer Account is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Customer Account is deactivate successfully';
                    $result['status'] = 1;
                }
            }else{
                $result['message'] = 'Customer Account status can`t be updated!!';
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
            //Notification data
            $notificationData = new Notification;
            $notificationData->user_id = $request->user_id;
            $notificationData->order_id = null;
            $notificationData->user_type = 2;
            $notificationData->notification_type = 3;
            $notificationData->notification_for = 'Admin-Notify';
            $notificationData->title = $request->title;
            $notificationData->message = $request->message;
            $notificationData->save();

            send_notification(1, $request->user_id, 'Admin-Notify', array('title'=>$request->title,'message'=>$notificationData->message,'type'=>'Admin','key'=>'event'));

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
        Gate::authorize('Users-section');
        return Excel::download(new BulkExport, 'Customer.csv');
    }

    public function importUsers()
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }

    /*public function addGuestList(Request $request)
    {
        $input = $request->all();
        if (isset($input['user_events'])) {
            $userlist = explode(',', str_replace('all,', '', $input['user_events']));
            foreach ($userlist as $userlist) {
                $eventUser  = EventUser::where('event_id', $input['event_id'])->where('user_id', $userlist)->first();
                if (!isset($eventUser)) {
                    $eventUser = new EventUser;
                    $eventUser->event_id = $input['event_id'];
                    $eventUser->user_id = $userlist;
                    $eventUser->save();
                }
            }
            return array('status'=>true);
        } elseif (!empty($input['file'])) {
            $imgRslt = file_upload($request->file('file'), 'guest_list');
            $excelData = (new UsersImport)->toArray(public_path($imgRslt[1]))[0];
            if (!empty($excelData)) {
                $n = 8;
                foreach ($excelData as $key => $user) {
                    $characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
                    $randomString = '';

                    for ($i = 0; $i < $n; $i++) {
                        $index = rand(0, strlen($characters) - 1);
                        $randomString .= $characters[$index];
                    }
                    if (!empty($user['email']) ||  !empty($user['mobile_number'])) {
                        $theUser = User::where('email', $user['email'])->whereOr('mobile', $user['mobile_number'])->first();
                        if (!isset($theUser)) {
                            $theUser = new User();
                            $theUser->name          = $user['name'] ?? null;
                            $theUser->email         = $user['email'] ?? null;
                            $theUser->country_code  = ltrim($user['country_code'], '+');
                            $theUser->mobile        = $user['mobile_number'] ?? null;
                            $theUser->password      = Hash::make('12345678');
                        }
                        $theUser->designation   = $user['designation'] ?? null;
                        $theUser->company_name  = $user['company_name']?? null;
                        $theUser->save();

                        $eventUser  = EventUser::where('event_id', $input['event_id'])->where('user_id', $theUser->id)->first();
                        if (!isset($eventUser)) {
                            $eventUser = new EventUser;
                            $eventUser->event_id = $input['event_id'];
                            $eventUser->user_id = $theUser->id;
                            $eventUser->save();
                        }
                    }
                }
                return array('status'=>true);
            }
        } else {
            return array('status'=>false);
        }
    }*/
}
