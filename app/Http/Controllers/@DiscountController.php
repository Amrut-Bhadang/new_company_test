<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Discount;
use App\Models\DiscountLang;
use App\Models\Category;
use App\Models\Products;
use App\Models\Restaurant;
use App\Models\Gift;
use App\Models\Language;
use App\Models\Country;
use App\Models\DiscountCountries;
use App\Models\DiscountCategories;
use App\Models\PanelNotifications;
use File,DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DiscountExport;
use App\Exports\DiscountAppliedExport;
use App\Imports\BulkImport;

class DiscountController extends Controller
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

        Gate::authorize('Discount-section');
        $login_user_data = auth()->user();
        $columns = ['discount.discount_code','discount.category_type'];

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $discountIds = DiscountCategories::where(['category_id'=>$restaurant_detail->id, 'category_type'=>'Restaurant'])->join('discount','discount.id','=','discount_categories.discount_id')->groupBy('discount_id')->pluck('discount_id')->toArray();
            $discount=Discount::select('category_type','id','discount_code','percentage','valid_from','valid_upto','no_of_use','no_of_use_per_user','added_by','status','created_at')->whereIn('id', $discountIds);

        } else {
            $discount=Discount::select('category_type','id','discount_code','percentage','valid_from','valid_upto','no_of_use','no_of_use_per_user','status','created_at');
        }


        return Datatables::of($discount)->editColumn('created_at', function ($discount) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($discount->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(discount.created_at)'), array($request->from_date, $request->to_date));
			}

            if ($request->has('restaurant_id')) {
                $restaurant_id = array_filter($request->restaurant_id);

                if (count($restaurant_id) > 0) {
                    $getDiscountIds = DiscountCategories::whereIn('category_id', $request->get('restaurant_id'))->pluck('discount_id')->toArray();
                    $query->whereIn('discount.id', $getDiscountIds);
                }
            }

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('discount.discount_code', 'like', "%{$search['value']}%");
               $query->orHaving('discount.category_type', 'like', "%{$search['value']}%");
            }

		})->addIndexColumn()->make(true);
    }



    public function frontend()
    {
        Gate::authorize('Discount-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;

        } else {
            $data['restaurant_id'] = '';
        }
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['restaurants']=Restaurant::select('name','id')->where(['status'=>1])->get();
        return view('discount.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Discount-edit');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;

        } else {
            $data['restaurant_id'] = '';
        }
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['discount'] = Discount::findOrFail($id);
        $data['DiscountCategories'] = DiscountCategories::where('discount_id',$id)->pluck('category_id')->toArray();
        $data['DiscountCountries'] = DiscountCountries::where('discount_id',$id)->pluck('country_id')->toArray();
        //dd($data['DiscountCategories']);
        return view('discount.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Discount-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Discount-create');
        $login_user_data = auth()->user();
         // validate
        $requested_data = $request->all();

        if($requested_data['category_type'] != 'Info') {

            if ($login_user_data->type == 4) {
                $this->validate($request, [
                    'title.en'  => 'required',
                    'title.ar'  => 'required',
                    'max_discount_amount'  => 'required',
                    'min_order_amount'  => 'required',
                    'category_type'  => 'required',
                    'discount_code'  => 'required',
                    'percentage'  => 'required',
                    'valid_from'  => 'required',
                    'valid_upto'  => 'required',
                    'no_of_use'  => 'required',
                    'no_of_use_per_user'  => 'required',
                    'description'  => 'required',
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                ],[
                ]);

            } else {
        		$this->validate($request, [
                    'title.en'  => 'required',
                    'title.ar'  => 'required',
                    'max_discount_amount'  => 'required',
                    'min_order_amount'  => 'required',
                    'category_type'  => 'required',
                    'discount_code'  => 'required',
                    'percentage'  => 'required',
                    'valid_from'  => 'required',
                    'valid_upto'  => 'required',
                    'no_of_use'  => 'required',
                    'no_of_use_per_user'  => 'required',
                    'country_ids'  => 'required',
                    'description'  => 'required',
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                ],[
                ]);
            }

        } else {
            $this->validate($request, [
                'title.en'  => 'required',
                'title.ar'  => 'required',
                'category_type'  => 'required',
                'valid_from'  => 'required',
                'valid_upto'  => 'required',
                'description'  => 'required',
                'country_ids'  => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ],[
            ]);
        }

        $fail = false;

        
        if($requested_data['category_type'] == 'Restaurant'){
            if(!isset($requested_data['category_id'])) {
                $result['message'] = 'Category Id field is required.';
                $result['status'] = 1;
                $fail = true;
            }
        }        

        if (!$fail) {
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {

                    if($requested_data['category_type'] != 'Info') {
                        $inputs = [
                            'title'=>$requested_data['title'][$lang],
                            'category_type'=>$requested_data['category_type'],
                            'max_discount_amount'=>$requested_data['max_discount_amount'],
                            'min_order_amount'=>$requested_data['min_order_amount'],
                            'discount_code'=>$requested_data['discount_code'],
                            'percentage'=>$requested_data['percentage'],
                            'valid_from'=>$requested_data['valid_from'],
                            'valid_upto'=>$requested_data['valid_upto'],
                            'no_of_use'=>$requested_data['no_of_use'],
                            'no_of_use_per_user'=>$requested_data['no_of_use_per_user'],
                            'description'=>$requested_data['description'][$lang],
                        ];

                    } else {
                        $inputs = [
                            'title'=>$requested_data['title'][$lang],
                            'category_type'=>$requested_data['category_type'],
                            'valid_from'=>$requested_data['valid_from'],
                            'valid_upto'=>$requested_data['valid_upto'],
                            'description'=>$requested_data['description'][$lang],
                        ];
                    }
                    $result = [];

                    if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'discount');
                        if($result[0]==true){
                            $inputs['image'] = $result[1];
                        }
                    }
                    $inputs['added_by'] = $login_user_data->id;
                        // dd($inputs);
                    $discount = Discount::create($inputs);

                    if($discount->id) {

                        if(isset($requested_data['category_id'])) {

                            foreach($requested_data['category_id'] as $key => $value) {
                                $catAssign = new DiscountCategories;
                                $catAssign->discount_id = $discount->id;
                                $catAssign->category_id = $value;
                                $catAssign->save();

                                //Panel Notification data
                                $panelNotificationData = new PanelNotifications;
                                $panelNotificationData->user_id = $value;
                                $panelNotificationData->product_id = null;
                                $panelNotificationData->user_type = 4;
                                $panelNotificationData->notification_for = 'Discount-Added';
                                $panelNotificationData->notification_type = 3;
                                $panelNotificationData->title = 'Discount Added';
                                $panelNotificationData->message = 'Discount Code '.$requested_data['discount_code'].' added in restaurant.';
                                
                                if ($panelNotificationData->save()) {
                                    $panelData = PanelNotifications::select('panel_notifications.*');
                                    $adminCount = 0;
                                    $restroCount = 0;

                                    if ($value) {
                                        $panelData->where('panel_notifications.user_id', $value);
                                        $restroCount = $panelData->where('panel_notifications.is_read', 0)->count();
                                    }
                                    $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();

                                    $curl = curl_init();

                                    curl_setopt_array($curl, array(
                                      CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$value."/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
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
                                        "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$value."/0",
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

                                    if (count($requested_data['category_id']) == $key+1) {
                                        /*Admin Notification*/
                                        $curl_admin = curl_init();

                                        curl_setopt_array($curl_admin, array(
                                          CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
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
                                            "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/0",
                                            "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                                          ),
                                        ));

                                        $responseNew = curl_exec($curl_admin);
                                        $err = curl_error($curl_admin);

                                        curl_close($curl_admin);

                                        if ($err) {
                                          // echo "cURL Error #:" . $err;
                                        } else {
                                          // echo $responseNew;
                                        }
                                        /*Admin Notification End*/
                                    }
                                }
                            }
                        }
                    }
                }
                $dataLang = new  DiscountLang;
                $dataLang->discount_id = $discount->id;
                $dataLang->title = $requested_data['title'][$lang];
                $dataLang->description = $requested_data['description'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
             }   
            if($discount->id){

                //Discount Country set for Restro and Admin
                if ($login_user_data->type == 4) {
                    $restaurant_detail = Restaurant::where(['user_id'=>$login_user_data->id])->first();

                    if ($restaurant_detail) {
                        $countryData = new DiscountCountries;
                        $countryData->discount_id = $discount->id;
                        $countryData->country_id = getCountryIdByLatLong($restaurant_detail->latitude, $restaurant_detail->longitude);
                        $countryData->save();
                    }


                } else {

                    foreach ($requested_data['country_ids'] as $k => $v) {
                        $countryData = new DiscountCountries;
                        $countryData->discount_id = $discount->id;
                        $countryData->country_id = $v;
                        $countryData->save();
                    }
                }
                $result['message'] = 'Discount has been created';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Discount Can`t created';
                $result['status'] = 0;
            }
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
        Gate::authorize('Discount-section');
        $discount = Discount::findOrFail($id);
        $discountCategories = DiscountCategories::where('discount_id',$id)->pluck('category_id')->toArray();
        if ($discount->category_type == 'Category') {
            $data['record'] = Category::whereIn('id',$discountCategories)->pluck('name')->toArray();

        } else if ($discount->category_type == 'Dish') {
            $data['record'] = Products::whereIn('id',$discountCategories)->pluck('name')->toArray();

        } else if ($discount->category_type == 'Restaurant') {
            $data['record'] = Restaurant::whereIn('id',$discountCategories)->pluck('name')->toArray();

        } else {
            $data['record'] = array();
        }

        $data['discount'] = $discount;
        $data['discount']['category_lists'] = implode(", ",$data['record']);
        // dd($data['discount']);
        return view('discount.view',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_category($category_type, $category_id='')
    {
        Gate::authorize('Discount-section');

        if ($category_type == 'Category') {
            $data['record'] = Category::select('name','id')->get();

        } else if ($category_type == 'Dish') {
            $data['record'] = Products::select('name','id')->get();

        } else if ($category_type == 'Restaurant') {
            $data['record'] = Restaurant::select('name','id')->get();

        } else if ($category_type == 'Gift') {
            $data['record'] = Gift::select('name','id')->get();

        } else {
            $data['record'] = array();
        }
        $data['category_type'] = $category_type;

        if ($category_id) {
            $data['category_id'] = $category_id;

        } else {
            $data['category_id'] = '';
        }
        // dd($category_id);

        $data['DiscountCategories'] = DiscountCategories::where('discount_id',$category_id)->pluck('category_id')->toArray();
        return view('discount.category_list',$data);
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
        Gate::authorize('Discount-edit');
        $data['discount'] = Discount::findOrFail($id);
        $data['DiscountCategories'] = DiscountCategories::where('discount_id',$id)->pluck('category_id')->toArray();
        //dd($data['DiscountCategories']);
		return view('discount.edit',$data);
    }

    public function show_user($id)
    {
        Gate::authorize('Discount-section');
       /* $data = Order::select('orders.id','orders.plan_id','orders.order_status','orders.price','orders.months','orders.transaction_id','orders.created_at','users.name')->join('users','users.id','=','orders.user_id')->join('gift_pack_subscription','gift_pack_subscription.id','=','orders.plan_id')->where('gift_pack_subscription.id',$id)->where('gift_pack_subscription.subscription_for','Gift')->orderBy('id','DESC')->get();*/

        $data = Discount::select('users.name','users.email','orders.amount','orders.id','orders.random_order_id','orders.discount_percent','orders.order_status','orders.created_at','discount.discount_code')->join('orders','orders.discount_code','=','discount.discount_code')->join('users','users.id','=','orders.user_id')->where('discount.id',$id)->where('orders.order_status','!=','Cancel')->orderBy('orders.id', 'DESC')->get();
       //dd($data->toSql());
        return view('discount.user_details',['user_details' => $data, 'discount_id' => $id]);
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
        Gate::authorize('Discount-edit');
        $login_user_data = auth()->user();
        // validate
        $requested_data = $request->all();
        $cate_id = $id;

        if ($requested_data['category_type_edit'] != 'Info') {

            if ($login_user_data->type == 4) {
                $this->validate($request, [
                    'title.en'  => 'required',
                    'title.ar'  => 'required',
                    'max_discount_amount'  => 'required',
                    'min_order_amount'  => 'required',
                    'category_type_edit'  => 'required',
                    'discount_code'  => 'required',
                    'percentage'  => 'required',
                    'valid_from'  => 'required',
                    'valid_upto'  => 'required',
                    'no_of_use'  => 'required',
                    'no_of_use_per_user'  => 'required',
                    'description'  => 'required',
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                ],[
                ]);

            } else {
                $this->validate($request, [
                    'title.en'  => 'required',
                    'title.ar'  => 'required',
                    'max_discount_amount'  => 'required',
                    'min_order_amount'  => 'required',
                    'category_type_edit'  => 'required',
                    'discount_code'  => 'required',
                    'percentage'  => 'required',
                    'valid_from'  => 'required',
                    'valid_upto'  => 'required',
                    'no_of_use'  => 'required',
                    'country_ids'  => 'required',
                    'no_of_use_per_user'  => 'required',
                    'description'  => 'required',
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                ],[
                ]);
            }

        } else {
            $this->validate($request, [
                'title.en'  => 'required',
                'title.ar'  => 'required',
                'category_type_edit'  => 'required',
                'valid_from'  => 'required',
                'valid_upto'  => 'required',
                'description'  => 'required',
                'country_ids'  => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ],[
            ]);
        }

        // $input = $request->all();
        $fail = false;

        if($requested_data['category_type_edit'] == 'Restaurant'){
            if(!isset($requested_data['category_id'])) {
                $result['message'] = 'Category Id field is required.';
                $result['status'] = 1;
                $fail = true;
            }
        }

        if(!$fail) {
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {

                    if ($requested_data['category_type_edit'] != 'Info') {
                        $inp = [
                            'title'=>$requested_data['title'][$lang],
                            'category_type'=>$requested_data['category_type_edit'],
                            'max_discount_amount'=>$requested_data['max_discount_amount'],
                            'min_order_amount'=>$requested_data['min_order_amount'],
                            'discount_code'=>$requested_data['discount_code'],
                            'percentage'=>$requested_data['percentage'],
                            'valid_from'=>$requested_data['valid_from'],
                            'valid_upto'=>$requested_data['valid_upto'],
                            'no_of_use'=>$requested_data['no_of_use'],
                            'no_of_use_per_user'=>$requested_data['no_of_use_per_user'],
                            'description'=>$requested_data['description'][$lang],
                        ];

                    } else {
                        $inp = [
                            'title'=>$requested_data['title'][$lang],
                            'category_type'=>$requested_data['category_type_edit'],
                            'max_discount_amount'=>null,
                            'min_order_amount'=>null,
                            'discount_code'=>null,
                            'percentage'=>null,
                            'valid_from'=>$requested_data['valid_from'],
                            'valid_upto'=>$requested_data['valid_upto'],
                            'no_of_use'=>null,
                            'no_of_use_per_user'=>null,
                            'description'=>$requested_data['description'][$lang],
                        ];
                    }

                    if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'discount');
                        if($result[0]==true){   
                            $inp['image'] = $result[1];              
                        } 
                    }
                    $inp['added_by'] = $login_user_data->id;

                    $discount = Discount::findOrFail($id);

                    if (isset($requested_data['category_id'])) {
                        DiscountCategories::where('discount_id',$cate_id)->delete();
                        if ($requested_data['category_id']) {

                            foreach ($requested_data['category_id'] as $key => $value) {
                                $catAssign = new DiscountCategories;
                                $catAssign->discount_id = $cate_id;
                                $catAssign->category_id = $value;
                                $catAssign->save();
                            }
                        }
                    }
                }   
                $dataLang = DiscountLang::where(['discount_id'=>$cate_id,'lang'=>$lang])->first();
                if(isset($dataLang)){
                    $dataLang = DiscountLang::where(['discount_id'=>$cate_id,'lang'=>$lang])->update(['title'=>$requested_data['title'][$lang], 'description'=>$requested_data['description'][$lang]]);
                } else {
                    $dataLang = new  DiscountLang;
                    $dataLang->discount_id = $discount->id;
                    $dataLang->title = $requested_data['title'][$lang];
                    $dataLang->description = $requested_data['description'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();  
                }
            }

            if($discount->update($inp)){
                DiscountCountries::where('discount_id',$cate_id)->delete();

                //Discount Country set for Restro and Admin
                if ($login_user_data->type == 4) {
                    $restaurant_detail = Restaurant::where(['user_id'=>$login_user_data->id])->first();

                    if ($restaurant_detail) {
                        $countryData = new DiscountCountries;
                        $countryData->discount_id = $cate_id;
                        $countryData->country_id = getCountryIdByLatLong($restaurant_detail->latitude, $restaurant_detail->longitude);
                        $countryData->save();
                    }


                } else {

                    foreach ($requested_data['country_ids'] as $k => $v) {
                        $countryData = new DiscountCountries;
                        $countryData->discount_id = $cate_id;
                        $countryData->country_id = $v;
                        $countryData->save();
                    }
                }
                $result['message'] = 'Discount updated successfully';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Discount Can`t updated';
                $result['status'] = 0;
            }
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
        Gate::authorize('Discount-delete');
        if(Media::findOrFail($id)->delete()){
            $result['message'] = 'Banner deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Banner Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {

        $details = Discount::find($id);
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = Discount::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Discount code is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Discount code is deactivate successfully';
                    $result['status'] = 1;
                }
            }else{
                $result['message'] = 'Discount code status can`t be updated!!';
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
        Gate::authorize('Discount-section');
        return Excel::download(new DiscountExport, 'Discount.csv');
    }

    public function exportAppliedUsers($id)
    {
        //
        Gate::authorize('Discount-section');
        return Excel::download(new DiscountAppliedExport($id), 'DiscountAppliedUsers.csv');
    }

    public function importUsers()
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }

}
