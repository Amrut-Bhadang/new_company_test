<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\User;
use App\Models\Restaurant;
use App\Models\RestaurantLang;
use App\Models\RestaurantMode;
use App\Models\Country;
use App\Models\Brand;
use App\Models\Modes;
use App\Models\Language;
//use App\Mail\ChangeEmailVarification;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Null_;
use DB;
use File;

class SettingController extends Controller
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function frontend()
    {
        $login_user_data = auth()->user();

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['Brand']=Brand::all();
            $data['Modes']=Modes::all();
            $data['users'] = Restaurant::findOrFail($restaurant_detail->id);
            $data['country'] = Country::select('phonecode','name','id')->get();
            $modeAssign= RestaurantMode::select('restaurant_id','id','mode_id')->where('restaurant_id',$restaurant_detail->id)->get();

            $modeAssignArr = array();
            foreach ($modeAssign as $key => $value) {
                $modeAssignArr[] = $value->mode_id;
            }

            $data['modeAssign'] = $modeAssignArr;
            
            return view('restaurant/profile', $data);

        } else {
            $data['country']=Country::select('phonecode','name','id')->get();
            return view('settings', $data);
        }
    }

    public function sendVerificationLink(Request $request){
        $this->validate($request, [   
            'current_password' => 'required|min:6|max:20',        
            'email'=>'required|email|max:255|unique:users,email',    
        ]);
        if (!(Hash::check($request->current_password, Auth::user()->password))) {
            $result=array(
                'status'=>false,
                'message'=>'Your current password does not matches with the password you provided. Please try again.'
            );
        }
        else{
            $token=$this->createNewToken();
            $user = Auth::user();
            $user->new_email = $request->email; 
            $user->new_email_token = $token;
            if($user->save()){
                if($this->_sendEmail($request->email,$token)){
                    $result=array(
                        'status'=>true,
                        'message'=>'Email varification link has been successfully send.'
                    );
                  }
                  else{
                    $result=array(
                        'status'=>false,
                        'message'=>'Error to email send.'
                    );
                  }   
            }
            else{
                $result=array(
                    'status'=>false,
                    'message'=>'Error Occured.'
                );
            }
           }
        return response()->json($result);
    }


    public function saveProfile(Request $request){
        $this->validate($request, [   
            'first_name' => 'required',        
            'last_name'=>'required',  
            'email'=>'required|email|max:255',
            'mobile'=>'required',  
            'country_code'=>'required',  
            'image' => 'image|mimes:jpeg,png,jpg,gif,svgmax:5120',
        ],[
			'image.size'  => 'the file size is less than 5MB',
        ]);

        
        $user = Auth::user();
        $user->first_name = $request->first_name; 
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '.$request->last_name;
        
        $user->country_code = $request->country_code;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'user');
            if($result[0]==true){
                $user->image = $result[1];
            }
        }
        if($user->save()){
            $result=array(
                'status'=>true,
                'message'=>'Profile updated successfully!'
            );
        }
        else{
            $result=array(
                'status'=>false,
                'message'=>'Error Occured.'
            );
        }  
        return response()->json($result);
    }

    public function updateRestroProfile(Request $request){
        
        $input = $request->all();
        // validate
        $mesasge = [
            'restaurant_name.en.required'=>'The restaurant name(English) field is required.',
            'restaurant_name.ar.required'=>'The restaurant name(Arabic) field is required.',
            'tag_line.en.required'=>'The tag line(English) field is required.',
            'tag_line.ar.required'=>'The tag line(Arabic) field is required.',
        ];
        $this->validate($request, [           
            'restaurant_name.en'=>'required|max:255',
            'restaurant_name.ar'=>'required|max:255',
            'tag_line.en'=>'required|max:255',
            'tag_line.ar'=>'required|max:255',
            // 'brand_id'=>'required',
            'email'=>'required|email|max:255',
            'country_code' => 'required',
            // 'mobile'=> 'required|numeric|digits_between:7,15|unique:restaurants,phone_number,,0,country_code,'.$request->country_code,
            'landline'=> 'required|numeric|digits_between:7,15',
            'min_order_amount'=> 'required|numeric|min:0|not_in:0',
            //'prepration_time'=> 'required|numeric',
            //'delivery_time'=> 'required|numeric',
            'admin_comission'=> 'required|numeric|min:0|not_in:0',
            /*'cancelation_charges'=> 'required',*/
            /*'free_delivery_min_amount'=> 'required',*/
            //'delivery_charges_per_km'=> 'required',
            // 'is_kilo_points_promotor'=> 'required',
            'is_featured'=> 'required',
            'payment_type'=> 'required',
            'kp_percent'=> 'required',
            /*'area_name'=> 'required',*/
            // 'password' => 'required|min:6|max:20',
            'cost_for_two_price' => 'required|numeric|min:0|not_in:0',
            // 'confirm_password' => 'same:password',
            'address'=> 'required',
            'latitude'=> 'required',
            'longitude'=> 'required',
        ],$mesasge);

        $login_user_data = auth()->user();
        $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
        $user_id = $login_user_data->id;
        $restaurant_id = $restaurant_detail->id;
        $image_path = '';
        //check email exist
        $checkEmailExist = User::where('id','!=', $user_id)->where(['status'=>1, 'email'=>$input['email']])->first();

        if ($checkEmailExist) {
            $result=array(
                'status'=>false,
                'message'=>'This email is already taken.'
            );
            return response()->json($result);

        } else {
            $inp = [];
            try{
                $lang = Language::pluck('lang')->toArray();
                foreach($lang as $lang)
                {
                    if($lang=='en')
                    {
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file,'user','image');

                            if ($result[0]==true){
                                $inp['file_path'] = $result[1];
                                $inp['file_name'] = $result[3];
                                $inp['extension'] = $result[2];

                                $image_path = $result[1];
                            }
                        }

                        if ($request->file('logo')) {
                            $file1 = $request->file('logo');
                            $result1 = image_upload($file1,'user','logo');

                            if ($result1[0]==true){
                                $inp['logo'] = $result1[1];
                            }
                        }

                        /*if($request->password){
                            $inp['password']=Hash::make($request->password);
                        }*/

                        $inp['name'] = $input['restaurant_name'][$lang];
                        $inp['tag_line'] = $input['tag_line'][$lang];
                        // $inp['brand_id'] = $input['brand_id'];
                        $inp['email'] = $input['email'];
                        $inp['address'] = $input['address'];
                        $inp['latitude'] = $input['latitude'];
                        $inp['longitude'] = $input['longitude'];
                        // $inp['password'] = Hash::make($input['password']);
                        $inp['phone_number'] = $input['mobile'];
                        $inp['country_code'] = $input['country_code'];
                        $inp['landline'] = $input['landline'];
                        $inp['min_order_amount'] = $input['min_order_amount'];
                        //$inp['prepration_time'] = $input['prepration_time'];
                        //$inp['delivery_time'] = $input['delivery_time'];
                        $inp['admin_comission'] = $input['admin_comission'];
                       /* $inp['cancelation_charges'] = $input['cancelation_charges'];
                        $inp['free_delivery_min_amount'] = $input['free_delivery_min_amount'];*/
                        //$inp['delivery_charges_per_km'] = $input['delivery_charges_per_km'];
                        /*$inp['is_kilo_points_promotor'] = $input['is_kilo_points_promotor'];
                        if($input['is_kilo_points_promotor'] == 1){
                            $inp['extra_kilopoints'] = $input['extra_kilopoint'];
                        } else {
                            $inp['extra_kilopoints'] = null;
                        }*/
                        $inp['kp_percent'] = $input['kp_percent'] ?? null;
                        $inp['is_featured'] = $input['is_featured'];
                        $inp['payment_type'] = $input['payment_type'];
                        /*$inp['area_name'] = $input['area_name'];*/
                        $inp['cost_for_two_price'] = $input['cost_for_two_price'];

                         if ($request->input('modes_id')) {
                                RestaurantMode::where('restaurant_id',$restaurant_id)->delete();
                                if ($request->input('modes_id')) {
                    
                                    foreach ($request->input("modes_id") as $key => $value) {
                                        $modeAssign = new RestaurantMode;
                                        $modeAssign->restaurant_id = $restaurant_id;
                                        $modeAssign->mode_id = $value;
                                        $modeAssign->save();
                                    }
                                }
                            }
                        $data = Restaurant::where('user_id',$user_id)->update($inp);

                        // update user data
                        $user = Auth::user();
                        $user->first_name = $input['restaurant_name'][$lang];
                        $user->email = $input['email'];
                        $user->name = $input['restaurant_name'][$lang];
                        $user->mobile = $input['mobile'];

                        if ($image_path) {
                            $user->image = $image_path;
                        }
                        $user->country_code = $input['country_code'];

                        /*if (isset($input['password'])) {
                            $user->password = Hash::make($input['password']);
                        }*/
                        $user->save();
                    }

                    $dataLang = RestaurantLang::where(['restaurant_id'=>$restaurant_id,'lang'=>$lang])->first();

                    if (isset($dataLang))
                    {
                       $dataLang = RestaurantLang::where(['restaurant_id'=>$restaurant_id,'lang'=>$lang])->update(['name'=>$input['restaurant_name'][$lang],'tag_line'=>$input['tag_line'][$lang]]);                                   
                    }
                    else
                    {
                        $dataLang = new  RestaurantLang;
                        $dataLang->restaurant_id = $restaurant_id;
                        $dataLang->name = $input['restaurant_name'][$lang];
                        $dataLang->tag_line = $input['tag_line'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }
                $result['message'] = 'Restaurant updated successfully.';
                $result['status'] = 1;
                return response()->json($result);
            }
            catch (Exception $e)
            {
                $result['message'] = 'Restaurant Can`t be updated.';
                $result['status'] = 0;
                return response()->json($result);           
            }
        }

    }


    public function emailUpdate(Request $request,$userid,$token){
        $user = User::findOrFail($userid);
        if ($user->new_email_token==$token && $userid==Auth::user()->id) {
            $user->email = $user->new_email;
            $user->new_email = Null;
            $user->new_email_token = Null;
            if($user->save()){
                $result=array(
                    'status'=>true,
                    'message'=>'Email has been successfully updated.'
                );
                $request->session()->flash('status', $result['message']);
                return redirect('settings');
            }
        }
        else{
            return abort(403,'Invalid or expired Link');
        }

    }
    
    public function changePassword(Request $request){
        $mesasge = [
            'new_password.regex' => __("backend.strong_password"),
        ];
        $this->validate($request, [   
            'current_password' => 'required|min:6|max:20',        
            'new_password' => 'required|min:6|max:20|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirm_password' => 'required|same:new_password',
        ],$mesasge);
        
        if (!(Hash::check($request->current_password, Auth::user()->password))) {
            $result=array(
                'status'=>false,
                'message'=>'Your current password does not matches with the password you provided. Please try again.'
            );
        }
        else{
            $user = Auth::user();
            $user->password = bcrypt($request->new_password);
            if( $user->save()){
                $result=array(
                    'status'=>true,
                    'message'=>"Password changed successfully !",
                    'user'=>Auth::user(),
                );
            }
        }
        return response()->json($result);
    }

    public function _sendEmail($email,$token)
    {
        $details = [
            'new_email'=>$email,
            'token'=>$token,
            'user'=>Auth::user()
         ];
        try {
            //  Mail::to($email)->send(new \App\Mail\ChangeEmailVarification($objEmail));
            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('emails.new_email_verify', $details, function($message) use ($details)
            {
                $message
                    ->to($details['new_email'])
                    ->subject('Verify your new email address');
            });
             return true; 
        } catch(\Exception $e){
            dd($e->getMessage());
        }
    }

     /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256',Str::random(40),Auth::user()->password);
    }

}
