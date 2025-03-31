<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Models\Language;

use File,DB;

class EmailTemplateController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('Email-Template-section');
        $email=EmailTemplate::select('email_templates.*');
        return Datatables::of($email)->editColumn('created_at', function ($email) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($email->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Email-Template-section');
        return view('emails.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Email-Template-edit');
        $data['emails'] = EmailTemplate::findOrFail($id);
        return view('emails.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        Gate::authorize('Email-Template-create');
         // validate
		$this->validate($request, [           
            'name'=>'required',
            'description'=>'required',
        ]);
 
        $notification = new Notification();

        $notification->name 		= $request->name;
        $notification->notification_type= $request->notification_type;
        $notification->title 			= $request->title;
        $notification->message 			= $request->message;
        
        if($notification->save()){
            $result['message'] = 'Notification Send Successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Notification Can`t be Send';
            $result['status'] = 0;
        }
        return response()->json($result);
    }


    public function show($id)
    {
       
        Gate::authorize('Email-Template-section');
        $data = EmailTemplate::findOrFail($id);
        //$data = ['abc' => $data];
        if ($data->slug == 'order_details') {
            return view('emails.orderdetails',['order_detail' => $data]);
        }

        if ($data->slug == 'forgot_password') {
            return view('emails.forgotpassword',['forgot_detail' => $data]);    
        }

        if ($data->slug == 'register') {
            return view('emails.register',['register_detail' => $data]);    
        }

        if ($data->slug == 'booking_accepted') {
            return view('emails.booking_accepted',['record' => $data]);    
        }
        if ($data->slug == 'facility') {
            return view('emails.facility_register',['record' => $data]);    
        }
    }


    public function update(Request $request, $id)
    {
        Gate::authorize('Email-Template-edit');
        // validate
        $mesasge = [
            'name.en.required'=>'The name field is required.',
            'description.en.required'=>'The Description field is required.',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'description.en'  => 'required',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        
        $file = $request->file('file');
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang)
            {
                if($lang=='en')
                {
                    
                  $data = EmailTemplate::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang],'subject'=>$input['subject'][$lang],'footer'=>$input['footer'][$lang]]);
                }
                $dataLang = EmailTemplateLang::where(['email_id'=>$cate_id,'lang'=>$lang])->first();
                if(isset($dataLang))
                {
                   $dataLang = EmailTemplateLang::where(['email_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang],'subject'=>$input['subject'][$lang],'footer'=>$input['footer'][$lang]]);   
                }
                else
                {
                $dataLang = new  EmailTemplateLang;
                $dataLang->email_id = $cate_id;
                $dataLang->name = $input['name'][$lang];
                $dataLang->description = $input['description'][$lang];
                $dataLang->subject = $input['subject'][$lang];
                $dataLang->footer = $input['footer'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
                }
            }
            $result['message'] = 'Notification Send Successfully';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Notification Can`t be Send';
            $result['status'] = 0;
            return response()->json($result);           
        }
    }

    public function changeStatus($id, $status)
    {
        $details = EmailTemplate::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = 1;
            }else{
                $inp = 0;
            }
            $emailTemplates = EmailTemplate::where('id',$id)->first();
            $emailTemplates->status =$inp;
            $emailTemplates->save();

            if($emailTemplates!=''){
                if($status == 'active'){
                    $result['message'] =  __("backend.email_template_status_success");
                    $result['status'] = 1;
                }else{
                    $result['message'] =  __("backend.email_template_status_deactivate");
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] =  __("backend.email_template_status_can`t_updated");
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild Email Template!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }




}
