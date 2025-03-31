<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use App\Models\AdminNotificationLang;
use App\Models\EmailTemplateLang;
use App\Models\Language;
use App\Models\PanelNotificationLang;
use App\Models\PanelNotifications;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

use File,DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
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
        Gate::authorize('Notification-section');
        $columns = ['notifications.title','notifications.message'];

        $notification=AdminNotification::select('admin_notification.*');
        return Datatables::of($notification)->editColumn('created_at', function ($notification) {
        	$timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($notification->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');

        /*})->editColumn('notification_for', function ($notification) {
			$notification_type = array_flip(notificationUserType());
			$notification_type[$notification['notification_for']];
            return $notification_type[$notification['notification_for']]; 
		})

		->editColumn('notification_type', function ($notification) {
			$notification_type = array_flip(notificationType());
			$notification_type[$notification['notification_type']];
            return $notification_type[$notification['notification_type']];*/ 

        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(admin_notification.created_at)'), array($request->from_date, $request->to_date));
			}

			if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('admin_notification.title', 'like', "%{$search['value']}%");
               $query->orHaving('admin_notification.message', 'like', "%{$search['value']}%");
            }
		})
		->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Notification-section');
        /*$data['user_type'] = array_flip(notificationUserType());
        $data['notification_type'] = array_flip(notificationType());*/
        return view('notifications.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Notification-edit');
        $data['notifications'] = AdminNotification::findOrFail($id);
        return view('notifications.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Notification-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Notification-create');
         // validate
		$this->validate($request, [           
            'notification_for'=>'required',
            'title' => 'required',
            'message'=> 'required'
        ]);
    	$lang = App::getLocale();
		if($lang == null){
			$lang = 'en';
		}

		$input = $request->all();
        // dd($lang);
        $notification_for 	= $input['notification_for'];
        $title = $input['title'][$lang];
        $message = $input['message'][$lang];
        $notification_type = 'Send';
        // dd($input,$notification_for,$title,$message);
        if($notification_for == 'Players'){
			if(isset($input['select_user'])){
				$user = User::whereIn('id', $input['select_user'])->where('type', 3)->get();
			}else{
				$user = User::where('type', 3)->get();
			}
			// dd($user);
        	foreach($user as $value) {
				add_player_notification_by_admin($value['id'], $user_type = 3, $notification_type = 3, 'admin_notification', $order_id = 1, $titles = $input['title'], $messages = $input['message']);
				send_notification(1, $value['id'], $title, array('title' => $title, 'message' => $message, 'type' => 'admin_notification', 'key' => 'admin_notification'));
        	}

        } else if ($notification_for == 'FacilityOwner') {
			if(isset($input['select_user'])){
				$user = User::whereIn('id', $input['select_user'])->where('type', 1)->get();
			}else{
				$user = User::where('type', 1)->get();
			}
			  foreach ($user as $key => $value) {
				$ownerChanellId = 'pubnub_onboarding_channel_owner_'.$value->id;
				// dd($ownerChanellId);
				addNotificationByAdminForOwner($user_type = 1, $notification_type=1, 'admin_notification', $titles = $input['title'], $messages = $input['message'], $user_id = $value->id, $order_id = 1);
				send_admin_notification($message = $message, $title=$title,$channel_name=$ownerChanellId);
	          }


        } else {
        	$user = User::where('type','!=',0)->get();

        	foreach ($user as $key => $value) {

        		if ($value->type == 3){
        			// send_notification(1, $value->id, $title, array('title'=>$title,'message'=>$message));
					add_player_notification_by_admin($value['id'], $user_type = 3, $notification_type = 3,  'admin_notification', $order_id = 1, $titles = $input['title'], $messages = $input['message']);
					send_notification(1, $value['id'], $title, array('title' => $title, 'message' => $message, 'type' => 'admin_notification', 'key' => 'admin_notification'));

        		} else {
					$ownerChanellId = 'pubnub_onboarding_channel_owner_'.$value->id;
					addNotificationByAdminForOwner($user_type = 1, $notification_type=1, 'admin_notification', $titles = $input['title'], $messages = $input['message'], $user_id = $value->id, $order_id = 1);
					send_admin_notification($message = $message, $title=$title,$channel_name=$ownerChanellId);
        		}
        	}
        }
        //$user;


		$lang = Language::pluck('lang')->toArray();
		foreach ($lang as $lang) {
			if ($lang == 'en') {
				$notification = new AdminNotification();
				$notification->notification_for = $notification_for;
				$notification->notification_type= 'Send';
				$notification->title = $input['title'][$lang];
				$notification->message = $input['message'][$lang];
				$notification->save();
			}
				$notificationLang = new AdminNotificationLang();
				$notificationLang->admin_notification_id = $notification->id;
				$notificationLang->title = $input['title'][$lang];
				$notificationLang->message = $input['message'][$lang];
				$notificationLang->lang = $lang;
				$notificationLang->save();
		}



        // $notification = new AdminNotification();
        // $notification->notification_for = $notification_for;
        // $notification->notification_type= 'Send';
        // $notification->title = $title;
        // $notification->message = $message;

 
        
        if($notification){
            $result['message'] = __('backend.Notification_Send_Successfully');
            $result['status'] = 1;
        }else{
            $result['message'] = __('backend.Notification_Can_not_be_Send');
            $result['status'] = 0;
        }

        return response()->json($result);
    }

    private function __sendEmail($user_type,$title,$message)
	{
	 	$userdetails 		= $this->__getUserdetails();
	 	$celebritydetails 	= $this->__getCelebritydetails();
        
	 	switch ($user_type) {
	 		case '1':
	 			if (!empty($userdetails) && !empty($celebritydetails)) {
	 				$result = array_merge($userdetails,$celebritydetails);
	 			}
	 			break;

	 		case '3':
	 				$result = $celebritydetails;
	 			break;
	 		
	 		default:
	 				$result = $userdetails;
	 			break;
		 }

	 	if (!empty($result)) {
	 		foreach ($result as $value) {

	 			if (!empty($value['email'])) {
	
					$details = [
						'new_email'=>$value['email'],
						'title' => $title
					 ];
					//  dd($details);
					try {
						$beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
						$beautymail->send('emails.notifications', $details, function($message) use ($details)
						{
							$message
								->to($details['new_email'])
								->subject($details['title']);
						});
						 return true; 
					} catch(\Exception $e){
						dd($e->getMessage());
					}
				}
		 	}
	 	}
	 	return true;
	}

	/**
	* Function for send SMS
	*
	* @param null
	*
	* @return null. 
	*/

	private function __sendSMS($user_type,$title,$message)
	{
	 	$userdetails 		= $this->__getUserdetails();
	 	$celebritydetails 	= $this->__getCelebritydetails();
         echo $user_type;
         die;
	 	switch ($user_type) {
	 		case '1':
	 			if (!empty($userdetails) && !empty($celebritydetails)) {
	 				$result = array_merge($userdetails,$celebritydetails);
	 			}
	 			break;

	 		case '2':
	 				$result = $celebritydetails;
	 			break;
	 		
	 		default:
	 				$result = $userdetails;
	 			break;
	 	}

	 	if (!empty($result)) {
	 		foreach ($result as $value) {
	 			if (!empty($value['mobile'])) {
	 				$mobile = $value['country_code'].''.$value['mobile'];
	 				$this->sendMessages($mobile,$message);
				}				
		 	}
	 	}
	 	return true;
	}

	/**
	* Function for send PushNotification
	*
	* @param null
	*
	* @return null. 
	*/

	private function __sendPushNotification($user_type,$title,$message)
	{
	 	$userdetails 		= $this->__getUserdetails();
	 	$restaurantdetails 	= $this->__getRestaurantdetails();
         echo $user_type;
         die;
	 	switch ($user_type) {
	 		case '1':
	 			if (!empty($userdetails) && !empty($restaurantdetails)) {
	 				$result = array_merge($userdetails,$restaurantdetails);
	 			}
	 			break;

	 		case '2':
	 				$result = $restaurantdetails;
	 			break;
	 		
	 		default:
	 				$result = $userdetails;
	 			break;
	 	}

	 	if (!empty($result)) {
	 		foreach ($result as $value) {
	 			if (!empty($value['device_token'])) {
				}				
		 	}
	 	}
	 	return true;
	}

	/**
	* Function for get Users mobile number and email and Device ids;
	*
	* @param null
	*
	* @return null. 
	*/

	private function __getUserdetails()
	{
	 	$userdetails =  User::select('country_code','email','mobile','name')->where([
					    ['type', '=',0],
					    ['status', '=',1]
					])
					->get();
		if (!empty($userdetails->toArray())) {
			return $userdetails->toArray();
		}else{
			return false;
		}
	}

	/**
	* Function for get Celebrity mobile number and email and Device ids;
	* @param null
	* @return null. 
	*/

	

	public function getNotificationData() {
		$login_user_data = auth()->user();
		$userId = $login_user_data->id;
		$data = getNotificationList($userId);
		$data['notificaiton_list'] = $data['notificationData'];
		$data['notificaiton_count'] = $data['count'];
	    return view('orders.orderNotifyTopBar',$data);

    }


    public function readNotification($id) {
    	$notificationData = PanelNotifications::find($id);
    	$inp = ['is_read' => 1];

        if(!empty($notificationData)) {

            if ($notificationData->update($inp)) {

                $result['message'] = __('Notification_read_successfully');
                $result['status'] = 1;

            }else{
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
            }
        }else{
            $result['message'] = __('backend.Invaild_notification_id');
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function clearAllNotification(Request $request) {
    	if ($request->userId) {
			$ids = PanelNotifications::where('user_id', $request->userId)->pluck('id')->toArray();
			PanelNotificationLang::whereIn('panel_notification_id', $ids)->delete();
	    	PanelNotifications::where('user_id', $request->userId)->delete();
	    	// PanelNotifications::where('status', 1)->delete();
	    	$result['message'] = __('backend.Notification_deleted_successfully');
	        $result['status'] = 1;
    	} else{
            $result['message'] = __('backend.Something_went_wrong');
            $result['status'] = 0;
        }
        return response()->json($result);
    }

	public function show_type_data($type, $type_id = null)
    {
		// dd('dddd',$type, $type_id);
        $data['type'] = $type;
        $data['type_id'] = $type_id;
        if ($type == 'Players') {
            $data['record'] = User::where('type', 3)->select('id','name','country_code','mobile')->get();
        } elseif($type == 'FacilityOwner'){
			$data['record'] = User::where('type', 1)->select('id','name','country_code','mobile')->get();
		}
		else {
			$data['record'] = User::where('type', 12)->select('id','name','country_code','mobile')->get();
        }
        return view('notifications.type_list', $data);
    }

}
