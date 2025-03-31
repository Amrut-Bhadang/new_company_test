<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use File;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Restaurant;
use App\Models\RestaurantLang;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\Brand;
use App\Models\Orders;
use App\Models\Products;
use App\Models\RestaurantMode;
use App\Models\Modes;
use App\Models\RestaurantsTiming;
use App\Models\RestaurantImages;
use App\Models\Category;
use App\Models\RestaurantTables;
use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RestaurantExport;
use App\Exports\RestaurantTransExport;
use App\Imports\BulkImport;
use DB;

class RestaurantController extends Controller
{
    public function __construct() {
		$this->middleware('auth');
        changeRestaurantStatus();
	}

     // Google Chart API URL
    private $googleChartAPI = 'http://chart.apis.google.com/chart';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        Gate::authorize('Restaurant-section');
        $columns = ['restaurants.name','restaurants.email','restaurants.phone_number'];

        $restaurant=Restaurant::select('user_id','name','email','country_code','phone_number','address','created_at','id','status');
        return Datatables::of($restaurant)->editColumn('created_at', function ($restaurant) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($restaurant->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s'); 
        })->editColumn('phone_number', function ($restaurant) {
            return $restaurant->phone_number = $restaurant->country_code.' '.$restaurant->phone_number;

         })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(restaurants.created_at)'), array($request->from_date, $request->to_date));
			}

            if ($request->has('main_category_id') && !empty($request->get('main_category_id'))) {
                /*$main_category_id = array_filter($request->main_category_id);
                if(count($main_category_id) > 0) {
                    $query->whereIn('restaurants.main_category_id', $request->get('main_category_id'));
                }*/
                $query->where('restaurants.main_category_id', $request->get('main_category_id'));
            }

            if ($request->has('brand_id') && !empty($request->get('brand_id'))) {
                $query->where('restaurants.brand_id', $request->get('brand_id'));
            }

            if(!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('restaurants.name', 'like', "%{$search['value']}%");
               $query->orHaving('restaurants.email', 'like', "%{$search['value']}%");
               $query->orHaving('restaurants.phone_number', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Restaurant-section');
        $data['Brand']=Brand::all();
        $data['Modes']=Modes::where('status', 1)->get();
        $data['main_category']=MainCategory::select('*')->where(['status'=>1])->get();
        $data['country']=Country::select('phonecode','name','id')->get();
        return view('restaurant.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Restaurant-edit');
        //$data['roles']=Role::all();
        $data['Brand']=Brand::all();
        $data['Modes']=Modes::where('status', 1)->get();
        $data['main_category']=MainCategory::all();
        $data['users'] = Restaurant::findOrFail($id);
        $data['country']=Country::select('phonecode','name','id')->get();
        $modeAssign= RestaurantMode::select('restaurant_id','id','mode_id','mode_type')->where('restaurant_id',$id)->get();

        $modeAssignArr = array();
        $modeTypeAssignArr = array();
        foreach ($modeAssign as $key => $value) {
            $modeAssignArr[] = $value->mode_id;
            $modeTypeAssignArr[] = json_decode($value->mode_type);
        }
        $modetype= array();

        foreach($modeTypeAssignArr as $modeTypeAssignArr1)
        {
            if ($modeTypeAssignArr1) {

                foreach($modeTypeAssignArr1 as $values){
                    $modetype[] = $values;
                }
            }
        }

        $data['modeAssign'] = $modeAssignArr;
        $data['modeTypeAssign'] = $modetype;

        //$data['staff_roles']=$data['staff']->getRoleNames()->toArray();
        return view('restaurant.edit',$data);
    }

    public function show_brands($main_category_id, $brand_id = '') {

      if ($main_category_id) {
        $brands = Brand::where(['main_category_id'=>$main_category_id])->get();
        $data['records'] = $brands;
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      // echo "<pre>"; print_r($data['records']); die;
      $data['brand_id'] = $brand_id;
      return view('restaurant.showBrands',$data);

    }

    public function show_brands_for_list($main_category_id) {

      if ($main_category_id) {
        $brands = Brand::where(['main_category_id'=>$main_category_id])->get();
        $data['records'] = $brands;
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }

      return view('restaurant.showBrandsForList',$data);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Restaurant-create');
        $data['Brand']=Brand::all();
        $data['Modes']=Modes::where('status', 1)->get();
        $data['main_category']=MainCategory::all();
        $data['country']=Country::select('phonecode','name','id')->get();
        return view('restaurant.add',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Restaurant-create');
         // validate
        $input = $request->all();
        // dd($input);

        $mesasge = [
            'restaurant_name.en.required'=>'The restaurant name(English) field is required.',
            'restaurant_name.en.required'=>'The restaurant name(Arabic) field is required.',
            'tag_line.ar.required'=>'The tag line(English) field is required.',
            'tag_line.ar.required'=>'The tag line(Arabic) field is required.',
        ];
		$this->validate($request, [           
            'restaurant_name.en'=>'required|max:255',
            'restaurant_name.ar'=>'required|max:255',
            'tag_line.en'=>'required|max:255',
            'tag_line.ar'=>'required|max:255',
            'brand_id'=>'required',
            'email'=>'required|email|max:255|unique:restaurants|unique:users',
            'country_code' => 'required',
            'mobile'=> 'required|numeric|digits_between:7,15|unique:restaurants,phone_number,,0,country_code,'.$request->country_code,
            'landline'=> 'nullable|numeric|digits_between:7,15',
            'min_order_amount'=> 'nullable|numeric|min:0|not_in:0',
            //'prepration_time'=> 'required|numeric|min:0|not_in:0',
            //'delivery_time'=> 'required|numeric|min:0|not_in:0',
            'admin_comission'=> 'required|numeric|min:0|not_in:0',
            /*'cancelation_charges'=> 'required|numeric|min:0|not_in:0',*/
            /*'free_delivery_min_amount'=> 'required|numeric|min:0|not_in:0',*/
            /*'delivery_charges_per_km'=> 'required|numeric|min:0|not_in:0',*/
            // 'is_kilo_points_promotor'=> 'required',
            // 'buy_one_get_one'=> 'required',
            'extra_kilopoint'=> 'nullable',
            'kp_percent'=> 'required',
            'is_featured' => 'required',
            // 'payment_type' => 'required',
            'main_category_id' => 'required',
            'restro_valid_upto' => 'required',
            /*'area_name'=> 'required',*/
            // 'password' => 'required|min:6|max:20',
            // 'confirm_password' => 'required|same:password',
            'cost_for_two_price' => 'nullable|numeric|min:0|not_in:0',
            'address'=> 'required',
            'latitude'=> 'required',
            'longitude'=> 'required',
		],$mesasge);
		// create a new task based on user tasks relationship 
        $fail = false;
        $mesasge = '';

        /*if ($input['is_kilo_points_promotor'] == '1') {
          if (empty($input['extra_kilopoint'])) {
                $fail = true;
                $message = 'Extra kilopoints field is required.';
          } else if ($input['extra_kilopoint'] < 1) {
                $fail = true;
                $message = 'Extra kilopoints should be greater than zero.';
          } 
        }*/
        if ($input['main_category_id'] == '5' || $input['main_category_id'] == '6') {
            $checkAlreadyStoreExist = Restaurant::where('main_category_id','=', $input['main_category_id'])->first();

            if ($checkAlreadyStoreExist) {
                $fail = true;
                $message = 'This service should have only one store.';
            }
        }
        $restro_permissions = ['24','25','26','27','32','33','34','35','52','53','54','55','64','65','66','67','80','81','82','83','124','125','126','127','140','141','142','143'];

        if(!$fail){
           try{
            $lang = Language::pluck('lang')->toArray();
            $image_path = '';
            $data = new Restaurant;

            foreach($lang as $lang){

                if($lang=='en')
                {
                    if ($request->file('document')) {
                        $docResult = file_upload($request->file('document'), 'user');

                        if ($docResult[0]==true){
                            $data->document = $docResult[1];
                        }
                    }

                    if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'user','image');

                        if ($result[0]==true){
                            $data->file_path = $result[1];
                            $data->file_name = $result[3];
                            $data->extension = $result[2];

                            $image_path = $result[1];
                        }
                    }

                    if ($request->file('logo')) {
                        $file = $request->file('logo');
                        $result = image_upload($file,'user','logo');

                        if ($result[0]==true){
                            $data->logo = $result[1];
                        }
                    }

                    // create a new task based on user tasks relationship
                    $user = User::create([
                        'name' => $input['restaurant_name'][$lang],
                        'first_name' => $input['restaurant_name'][$lang],
                        'last_name' => '',
                        'mobile' => $input['mobile'],                    
                        'email' => $input['email'],
                        'country_code' => str_replace("+","",$input['country_code']),
                        // 'password' => Hash::make($input['password']),
                        'address' => $input['address'],
                        'latitude'=> $input['latitude'],
                        'longitude'=> $input['longitude'],
                        'email_verified_at'=> time(),
                        'image' => $image_path,
                        'type' => '4',
                        'status' => 1,
                    ]);

                    // dd($data);

                    if ($user->id) {

                        foreach ($restro_permissions as $key => $value) {
                            $user = User::findOrFail($user->id);
                            $user->givePermissionTo($value);
                        }
                    }

                    $data->user_id = $user->id;
                    $data->name = $input['restaurant_name'][$lang];
                    $data->tag_line = $input['tag_line'][$lang];
                    $data->brand_id = $input['brand_id'];
                    $data->main_category_id = $input['main_category_id'];
                    $data->email = $input['email'];
                    $data->address = $input['address'];
                    $data->latitude = $input['latitude'];
                    $data->longitude = $input['longitude'];
                    // $data->password = Hash::make($input['password']);
                    $data->phone_number = $input['mobile'];
                    $data->country_code = str_replace("+","",$input['country_code']);
                    $data->landline = $input['landline'] ?? null;
                    $data->min_order_amount = $input['min_order_amount'];
                    //$data->prepration_time = $input['prepration_time'];
                    //$data->delivery_time = $input['delivery_time'];
                    $data->admin_comission = $input['admin_comission'];
                    /*$data->cancelation_charges = $input['cancelation_charges'];
                    $data->free_delivery_min_amount = $input['free_delivery_min_amount'];*/
                    /*$data->delivery_charges_per_km = $input['delivery_charges_per_km'];*/
                    /*$data->is_kilo_points_promotor = $input['is_kilo_points_promotor'];
                    $data->buy_one_get_one = $input['buy_one_get_one'];*/
                    $data->extra_kilopoints = $input['extra_kilopoint'] ?? null;
                    $data->kp_percent = $input['kp_percent'] ?? null;
                    $data->restro_valid_upto = date('Y-m-d', strtotime($input['restro_valid_upto']));
                    $data->video = $input['video'] ?? null;

                    /*if($input['is_kilo_points_promotor'] == 1) {
                        $data->extra_kilopoints = $input['extra_kilopoint'];
                    } else {
                        $data->extra_kilopoints = null;
                    } */ 

                    $data->is_featured = $input['is_featured'];
                    /*$data->area_name = $input['area_name'];*/
                    $data->cost_for_two_price = $input['cost_for_two_price'] ?? null;
                    // $data->payment_type = $input['payment_type'];

                    if ($data->save()) {

                        if ($request->input('modes_id')) {

                            foreach ($request->input("modes_id") as $key => $value) {
                                $modeAssign = new RestaurantMode;
                                $modeAssign->restaurant_id = $data->id;
                                $modeAssign->mode_id = $value;

                                if($value==1)
                                {
                                    $modeAssign->mode_type = json_encode($request->input('payment_mode'));
                                }
                                else
                                {
                                    $modeAssign->mode_type = json_encode($request->input('pickup_mode'));                                
                                }
                                $modeAssign->save();
                            }
                        }
                    }
                }
                $dataLang = new  RestaurantLang;
                $dataLang->restaurant_id = $data->id;
                $dataLang->name = $input['restaurant_name'][$lang];
                $dataLang->tag_line = $input['tag_line'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();


            }

            $result['message'] = 'Restaurant has been created';
            $result['status'] = 1;
            return response()->json($result);

        } catch (Exception $e){
            $result['message'] = 'Restaurant Can`t created';
            $result['status'] = 0;
            return response()->json($result);            
        } 

        } else {
            $result['message'] = $message;
            $result['status'] = 0;
            return response()->json($result);
        }

        
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
        Gate::authorize('Restaurant-section');
        $data['users'] = Restaurant::findOrFail($id);
 
        return view('restaurant.view',$data);
    }

    public function showmenu($id)
    {
        //
        Gate::authorize('Restaurant-section');
        $data['menu'] = Products::select('products.name','products.products_type','products.category_id','products.id','products.created_at','categories.name as cat_name')->join('categories','categories.id','=','products.category_id')->where('products.restaurant_id',$id)->get();
         
        //$data['category'] = Category::select('name')->whereIn('id',$menu['category_id'])->get();
        //dd($category);
        return view('restaurant.menu',$data);
         

        //$data['menues'] = Products::where('restaurant_id',$id)->get();
 
    }

    public function showtables($id)
    {
        //
        // Gate::authorize('Restaurant-section');
        $login_user_data = auth()->user();
        $userId = $login_user_data->id;
        $restroId = $id;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['user_id'=>$login_user_data->id])->first();
            $restroId = $restaurant_detail->id;
        }
        $data['records'] = RestaurantTables::where('restaurant_id',$restroId)->get();
        $data['restro_id'] = $restroId;
         
        //$data['category'] = Category::select('name')->whereIn('id',$menu['category_id'])->get();
        //dd($category);
        return view('restaurant.table',$data);
         

        //$data['menues'] = Products::where('restaurant_id',$id)->get();
 
    }

    public function showtransaction($id){
        Gate::authorize('Restaurant-section');
        $data['transaction'] = Orders::select('orders.id','orders.id as order_id','orders.address','orders.amount','orders.order_status','orders.created_at','users.name','users.country_code','users.mobile')->join('users','users.id','=','orders.user_id')->where('orders.restaurant_id',$id)->get();

        $total_order_kp = array_sum(array_column($data['transaction']->toArray(),'total_order_kp'));

        $data['id']  = $id;
        $data['totalOrderKp']  = $total_order_kp;
        return view('restaurant.transaction',$data);
    }


    public function deliver_order($id){
        Gate::authorize('Users-section');
        $data['orders'] = Orders::where('restaurant_id',$id)->get();
        $data['customers']=User::select('name','id')->where(['type'=>0,'status'=>1])->get();
        $data['products'] = Products::where('is_active',1)->get();
        $data['order_status'] = 'Pending';
        return view('restaurant.deliver_order',$data);
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
        Gate::authorize('Restaurant-edit');
        $user = Restaurant::findOrFail($id);
       // $user['role_names']=$user->getRoleNames();
		return response()->json([
            'user' => $user
		]);
    }

    public function set_time($id)
    {
        //
        Gate::authorize('Restaurant-edit');
        $time = RestaurantsTiming::where('restro_id',$id)->get();
        $restroTime = array();

        if ($time) {

            foreach ($time as $key => $value) {
                $restroTime[$value->day]['start_time'] = $value->start_time;
                $restroTime[$value->day]['end_time'] = $value->end_time;
                $restroTime[$value->day]['is_close'] = $value->is_close;
            }
        }
        $data['time'] = $restroTime;
        $data['restro_id'] = $id;
       // $user['role_names']=$user->getRoleNames();
        return view('restaurant.set_time',$data);
    }


    public function time_update(Request $request, $id)
    {
       Gate::authorize('Restaurant-edit');
       $input = $request->all();
       
       if (isset($input['day'])) {
            RestaurantsTiming::where('restro_id',$id)->delete();

            foreach ($input['day'] as $key => $value) {
                $timing = new RestaurantsTiming;
                $timing->restro_id = $id;
                $timing->day = $key;
                $is_close = 'No';

                if (isset($value['is_close'])) {
                    $timing->is_close = $value['is_close'];
                    $is_close = $value['is_close'];

                }

                if ($is_close == 'Yes') {
                    $timing->start_time = Null;
                    $timing->end_time = Null;

                } else {
                    $timing->start_time = $value['open_time'];
                    $timing->end_time = $value['close_time'];
                }
                $timing->save();
            }
            $result['message'] = 'Restaurant Time Service updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
       }
       /*$restro_time = RestaurantsTiming::where('restro_id',$id)->first();
       if(isset($restro_time)) {

       }*/
    }

    public function table_update(Request $request, $id)
    {
       // Gate::authorize('Restaurant-edit');
       $input = $request->all();
       
       if (isset($input['table_count'])) {

            if ($input['table_count'] > 10) {
                $result['message'] = 'Only 10 table can be added in single request.';
                $result['status'] = 0;

            } else {

                $startCount = 1;
                $stopCount = $input['table_count'];
                $checkAlreadyTables = RestaurantTables::where('restaurant_id',$id)->orderBy('table_no', 'desc')->first();

                if ($checkAlreadyTables) {
                    $startCount = $checkAlreadyTables->table_no + 1;
                    $stopCount = $input['table_count'] + $checkAlreadyTables->table_no;
                }

                for ($i=$startCount; $i <= $stopCount; $i++) {
                    $table = new RestaurantTables;
                    $table->restaurant_id = $id;
                    $table->table_no = $i;
                    $code = 'DIN$TBL@'.$id.'#'.$i;
                    $table->table_code = $code;

                    $PNG_WEB_DIR = public_path('uploads/qrcode/temp/');
                    $fileName = time().'-'.$i.'-table-code.png';
                    $timestamp = $PNG_WEB_DIR.$fileName;
                    $qrcodeImage = $this->qrCode('500', $timestamp, $id, $i, $code);

                    /*$PNG_WEB_DIR = public_path('uploads/qrcode/temp/');
                    //dd($PNG_WEB_DIR);
                    $timestamp = time();
                    include "public/uploads/qrcode/qrlib.php";
                    $filename = $PNG_WEB_DIR.$timestamp.'-code.png';
                    $newfilename = url('uploads/qrcode/temp').'/'.$timestamp.'-code.png';
                    $code = 'DINEIN-TABLE-NO-'.$id.'-'.$i;
                    $data = QRcode::png($code, $filename, 'L', 4, 2);*/

                    $table->qr_code = $fileName;
                    $table->save();
                }
                $result['message'] = 'Restaurant Tables updated successfully.';
                $result['status'] = 1;
            }
            return response()->json($result);
       }
       /*$restro_time = RestaurantsTiming::where('restro_id',$id)->first();
       if(isset($restro_time)) {

       }*/
    }

    public function qrCode($size, $filename, $restaurant_id, $table_no, $code) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->googleChartAPI);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$size}x{$size}&cht=qr&chl=" . urlencode($code));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $img = curl_exec($ch);
        curl_close($ch);
    
        if($img) {
            if($filename) {
                if(!preg_match("#\.png$#i", $filename)) {
                    $filename .= ".png";
                }

                // print_r($filename);die;
                
                return file_put_contents($filename, $img);
            } else {
                header("Content-type: image/png");
                print $img;
                return true;
            }
        }
        return false;
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
        Gate::authorize('Restaurant-edit');
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
            'brand_id'=>'required',
            'main_category_id'=>'required',
            'email'=>'required|email|max:255',
            'country_code' => 'required',
            // 'mobile'=> 'required|numeric|digits_between:7,15|unique:restaurants,phone_number,,0,country_code,'.$request->country_code,
            'landline'=> 'nullable|numeric|digits_between:7,15',
            'min_order_amount'=> 'nullable|numeric|min:0|not_in:0',
            //'prepration_time'=> 'required|numeric|min:0|not_in:0',
            //'delivery_time'=> 'required|numeric|min:0|not_in:0',
            'admin_comission'=> 'required|numeric|min:0|not_in:0',
            /*'cancelation_charges'=> 'required|numeric|min:0|not_in:0',
            'free_delivery_min_amount'=> 'required|numeric|min:0|not_in:0',*/
            /*'delivery_charges_per_km'=> 'required|numeric|min:0|not_in:0',*/
            /*'is_kilo_points_promotor'=> 'required',
            'buy_one_get_one'=> 'required',*/
            'extra_kilopoint'=> 'nullable',
            'kp_percent'=> 'required',
            'restro_valid_upto'=> 'required',
            'is_featured'=> 'required',
            // 'payment_type'=> 'required',
            /*'area_name'=> 'required',*/
            'cost_for_two_price' => 'nullable|numeric|min:0|not_in:0',
            // 'password' => 'required|min:6|max:20',
            'confirm_password' => 'same:password',
            'address'=> 'required',
            'latitude'=> 'required',
            'longitude'=> 'required',
        ],$mesasge);

        // dd($request->input('payment_mode'));

        $restaurant_detail = Restaurant::select('name','id','user_id')->where(['id'=>$id])->first();
        //check email exist
        $checkEmailExist = User::where('id','!=', $restaurant_detail->user_id)->where(['status'=>1, 'email'=>$input['email']])->first();

        if ($checkEmailExist) {
            $result['message'] = 'This email is already taken.';
            $result['status'] = 0;
            return response()->json($result);

        } else {
            $checkNumberExistExist = User::where('id','!=', $restaurant_detail->user_id)->where(['country_code'=>$input['country_code'],'mobile'=>$input['mobile'],'status'=>1])->first();

            if ($checkNumberExistExist) {
                $result['message'] = 'This number is already taken.';
                $result['status'] = 0;
                return response()->json($result);

            } else {
                $cate_id = $id;
                $inp = [];
                $image_path = '';
                $document_path = '';

                $fail = false;
                $mesasge = '';

                if ($input['main_category_id'] == '5' || $input['main_category_id'] == '6') {
                    $checkAlreadyStoreExist = Restaurant::where('main_category_id','=', $input['main_category_id'])->where('id', '!=', $id)->first();

                    if ($checkAlreadyStoreExist) {
                        $fail = true;
                        $message = 'This service should have only one store.';
                    }
                }

                /*if ($input['is_kilo_points_promotor'] == '1') {
                  if (empty($input['extra_kilopoint'])) {
                        $fail = true;
                        $message = 'Extra kilopoints field is required.';
                  } else if ($input['extra_kilopoint'] < 1) {
                        $fail = true;
                        $message = 'Extra kilopoints should be greater than zero.';
                  } 
                }*/

                if(!$fail){
                    try{
                        $lang = Language::pluck('lang')->toArray();
                        foreach($lang as $lang)
                        {
                            if($lang=='en')
                            {
                                if ($request->file('document')) {
                                    $docResult = file_upload($request->file('document'), 'user');

                                    if ($docResult[0]==true){
                                        $inp['document'] = $docResult[1];
                                    }
                                }

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

                                if($request->password){
                                    $inp['password'] = Hash::make($request->password);
                                }

                                $inp['name'] = $input['restaurant_name'][$lang];
                                $inp['tag_line'] = $input['tag_line'][$lang];
                                $inp['brand_id'] = $input['brand_id'];
                                $inp['main_category_id'] = $input['main_category_id'];
                                $inp['email'] = $input['email'];
                                $inp['address'] = $input['address'];
                                $inp['latitude'] = $input['latitude'];
                                $inp['longitude'] = $input['longitude'];

                                if ($input['password']) {
                                    $inp['password'] = Hash::make($input['password']);
                                }
                                $inp['phone_number'] = $input['mobile'];
                                $inp['country_code'] = str_replace("+","",$input['country_code']);
                                $inp['landline'] = $input['landline'] ?? null;
                                $inp['min_order_amount'] = $input['min_order_amount'];
                                //$inp['prepration_time'] = $input['prepration_time'];
                                //$inp['delivery_time'] = $input['delivery_time'];
                                $inp['admin_comission'] = $input['admin_comission'];
                                /*$inp['cancelation_charges'] = $input['cancelation_charges'];
                                $inp['free_delivery_min_amount'] = $input['free_delivery_min_amount'];*/
                                /*$inp['delivery_charges_per_km'] = $input['delivery_charges_per_km'];*/
                                /*$inp['is_kilo_points_promotor'] = $input['is_kilo_points_promotor'];
                                $inp['buy_one_get_one'] = $input['buy_one_get_one'];*/
                                $inp['extra_kilopoints'] = $input['extra_kilopoint'] ?? null;
                                $inp['kp_percent'] = $input['kp_percent'] ?? null;
                                $inp['restro_valid_upto'] = date('Y-m-d', strtotime($input['restro_valid_upto']));
                                $inp['video'] = $input['video'] ?? null;
                                /*if($input['is_kilo_points_promotor'] == 1){
                                    $inp['extra_kilopoints'] = $input['extra_kilopoint'];
                                } else {
                                    $inp['extra_kilopoints'] = null;
                                }*/
                                $inp['is_featured'] = $input['is_featured'];
                                // $inp['payment_type'] = $input['payment_type'];
                                /*$inp['area_name'] = $input['area_name'];*/
                                $inp['cost_for_two_price'] = $input['cost_for_two_price'] ?? null;
                                $data = Restaurant::where('id',$cate_id)->update($inp);

                                if ($request->input('modes_id')) {
                                    RestaurantMode::where('restaurant_id',$cate_id)->delete();
                                    if ($request->input('modes_id')) {
                        
                                        foreach ($request->input("modes_id") as $key => $value) {
                                            $modeAssign = new RestaurantMode;
                                            $modeAssign->restaurant_id = $cate_id;
                                            $modeAssign->mode_id = $value;
                                            
                                            if($value==1)
                                            {
                                                $modeAssign->mode_type = json_encode($request->input('payment_mode'));
                                            }
                                            else
                                            {
                                                $modeAssign->mode_type = json_encode($request->input('pickup_mode'));                                
                                            }
                                            $modeAssign->save();
                                        }
                                    }
                                }

                                $userData = [
                                    'name' => $input['restaurant_name'][$lang],
                                    'first_name' => $input['restaurant_name'][$lang],
                                    'last_name' => '',
                                    'mobile' => $input['mobile'],                    
                                    'email' => $input['email'],
                                    'country_code' => str_replace("+","",$input['country_code']),
                                    'address' => $input['address'],
                                    'latitude'=> $input['latitude'],
                                    'longitude'=> $input['longitude'],
                                ];

                                if ($input['password']) {
                                    $userData['password'] = Hash::make($input['password']);
                                }

                                if ($image_path) {
                                    $userData['image'] = $image_path;
                                }
                                User::where('id',$restaurant_detail->user_id)->update($userData);
                            }

                            $dataLang = RestaurantLang::where(['restaurant_id'=>$cate_id,'lang'=>$lang])->first();

                            if (isset($dataLang))
                            {
                               $dataLang = RestaurantLang::where(['restaurant_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['restaurant_name'][$lang],'tag_line'=>$input['tag_line'][$lang]]);                                   
                               // $dataLang = RestaurantLang::where(['restaurant_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['restaurant_name'][$lang]]);                                   
                            }
                            else
                            {
                                $dataLang = new  RestaurantLang;
                                $dataLang->restaurant_id = $cate_id;
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
                } else {
                    $result['message'] = $message;
                    $result['status'] = 0;
                    return response()->json($result);
                }
                    
            }
        }
    }

    public function imageView(Request $request)
    {
        Gate::authorize('Restaurant-section');
        $id = $request->segment(3);
        $data['restaurant'] = Restaurant::findOrFail($id);
        $data['restaurantImage'] = RestaurantImages::select('image','id')->where('restaurant_id',$data['restaurant']->id)->get();
        
        return response()->json($data);;
    }

    public function restaurantImagesDelete(Request $request)
    {
        Gate::authorize('Restaurant-edit');
        $id = $request->segment(3);
        $details = RestaurantImages::find($id); 
        if(!empty($details)){ 
            if(RestaurantImages::findOrFail($id)->delete()){
                $result['message'] = 'Restaurant Image is deleted successfully';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Restaurant Image can`t be deleted!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild Images!!';
            $result['status'] = 0;
        }
        return response()->json($result);;
    }

    public function addMoreImages(Request $request, $id){
        Gate::authorize('Restaurant-edit');
        $data['restaurants'] = Restaurant::findOrFail($id);
        if ($request->file('addMoremultipalImage')) {
            $i = 1;
            foreach ($request->file("addMoremultipalImage") as $file) {
                $modelRestaurantImages = new RestaurantImages();
                $extension  = $file->getClientOriginalExtension();
                $newFolder  = strtoupper(date('M') . date('Y')) . '/';
                // $folderPath =   public_path().'/uploads/user/'.$newFolder;
                $folderPath =   public_path().'/uploads/user/';

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777, true);
                }
                $productImageName = time() . $i . '-user.' . $extension;
                // $image = $newFolder . $productImageName;
                $image = $productImageName;
                if ($file->move($folderPath, $productImageName)) {
                    $modelRestaurantImages->image = $image;
                }
                $i++;
                $modelRestaurantImages->restaurant_id = $data['restaurants']->id;
                $modelRestaurantImages->save();
            }   
             
            $data['restaurantImage'] = RestaurantImages::select('image','id')->where('restaurant_id',$data['restaurants']->id)->get();

            if ($data['restaurants']->id) {
                $result['message'] = 'Restaurant Images added successfully';
                $result['status'] = 1;
                $result['data'] = $data;

            } else {
                $result['message'] = 'Restaurant can`t be Added!!';
                $result['status'] = 0;
            }

        } else {
            $result['message'] = 'At least One Image is  required!!';
            $result['status'] = 0;
        }
        return response()->json($result);
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
        Gate::authorize('Restaurant-delete');
        return Restaurant::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = Restaurant::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $User = Restaurant::findOrFail($id);
            if($User->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Restaurant Account is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Restaurant Account is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Restaurant Account status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeRestroOnOff(Request $request)
    {
        $details = Restaurant::where('user_id', $request->restroId)->first();

        if(!empty($details)){
            $inp = ['is_on' => $request->status];
            $User = Restaurant::where('user_id', $request->restroId)->first();

            if($User->update($inp)){

                if($request->status == 1){
                    $result['message'] = 'Restaurant is on successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Restaurant is off successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Restaurant Account status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function makeMainRestro($id, $status)
    {
        $details = Restaurant::find($id);

        if (!empty($details)) {
            //remove main restro for the brand
            $removeData = ['is_main_branch' => 0];
            Restaurant::where('brand_id', $details->brand_id)->update($removeData);

            if ($status == 'Yes') {
                $inp = ['is_main_branch' => 1];

            } else {
                $inp = ['is_main_branch' => 0];
            }
            $User = Restaurant::findOrFail($id);

            if ($User->update($inp)) {

                if ($status == 'Yes') {
                    $result['message'] = 'This restaurant is now main branch.';
                    $result['status'] = 1;

                } else {
                    $result['message'] = 'This restaurant is not main branch now.';
                    $result['status'] = 1; 
                }

            } else {
                $result['message'] = 'Restaurant can`t be updated!!';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild Restaurant!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new RestaurantExport, 'Restaurant.csv');
    } 
    
    public function exportTransUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new RestaurantTransExport($slug), 'RestaurantTransaction.csv');
    } 


    
    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }

    
}
