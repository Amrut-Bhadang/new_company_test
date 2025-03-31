<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\User;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\BrandLang;
use App\Models\Restaurant;
use App\Models\RestaurantLang;
use App\Models\Language;
use File,DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrandExport;
use App\Imports\BulkImport;

class BrandController extends Controller
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
        Gate::authorize('Brand-section');
        $columns = ['brands.name'];

        $brand=Brand::select('file_path','name','brand_type','created_at','id');
        return Datatables::of($brand)->editColumn('created_at', function ($brand) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($brand->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(brands.created_at)'), array($request->from_date, $request->to_date));
			}

            if ($request->has('main_category_id')) {
                $main_category_id = array_filter($request->main_category_id);
                if(count($main_category_id) > 0) {
                    $query->whereIn('brands.main_category_id', $request->get('main_category_id'));
                }
            }

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('brands.name', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Brand-section');
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['main_category']=MainCategory::select('*')->where(['status'=>1])->get();
        $data['brand_category']=BrandCategory::select('*')->where(['status'=>1])->get();
        return view('brand.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Brand-edit');
        $data['main_category']=MainCategory::all();
        $data['brand'] = Brand::select('*')->where('id', $id)->first();
        $data['brand_category']=BrandCategory::select('*')->where(['status'=>1])->get();
        $data['country']=Country::select('phonecode','name','id')->get();
        return view('brand.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Brand-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Brand-create');
         // validate

        $mesasge = [
	        'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'main_category_id.required'=>'The Main Category field is required.',
	    ];
		$this->validate($request, [
            'name.en'=>'required|max:255',
            'name.ar'=>'required|max:255', 
            'main_category_id'=>'required',
            'brand_category'=>'required',
            'email'=>'required|email|max:255|unique:users',
            'mobile'=> 'required|numeric|digits_between:7,15|unique:users,mobile,,0,country_code,'.str_replace("+","",$request->country_code),
            'country_code' => 'required',
            'password' => 'required|min:6|max:20',
            'confirm_password' => 'required|same:password',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			'image.size'  => 'The file size is less than 5MB',
        ],$mesasge);

        $input = $request->all();

	    try{
	        $lang = Language::pluck('lang')->toArray();
	        $data = new Brand;
	        foreach($lang as $lang){

	            if($lang=='en')
	            {
                    // create a new task based on user tasks relationship
                    $user = User::create([
                        'name' => $input['name'][$lang],
                        'first_name' => $input['name'][$lang],
                        'last_name' => '',
                        'mobile' => $input['mobile'],                    
                        'email' => $input['email'],
                        'country_code' => str_replace("+","",$input['country_code']),
                        'password' => Hash::make($input['password']),
                        /*'address' => $input['address'],
                        'latitude'=> $input['latitude'],
                        'longitude'=> $input['longitude'],*/
                        'email_verified_at'=> time(),
                        // 'image' => $image_path,
                        'type' => '4',
                        'status' => 1,
                    ]);
	               
	                if ($request->file('image')) {
	                    $file = $request->file('image');
	                    $result = image_upload($file,'brand','image');

	                    if ($result[0]==true){
	                        $data->file_path = $result[1];
	                        $data->file_name = $result[3];
	                    }
	                }
                    $data->name = $input['name'][$lang];
	                $data->main_category_id = $input['main_category_id'];
                    $data->brand_category = $input['brand_category'];
                    $data->user_id = $user->id;
                    $data->country_code = $input['country_code'];
                    $data->mobile = $input['mobile'];
                    $data->email = $input['email'];
                    $data->password = Hash::make($input['password']);
	                $data->brand_type='Restaurant';
	                $data->type='Image';
	                $data->save();

	            }
	            $dataLang = new  BrandLang;
	            $dataLang->brand_id = $data->id;
	            $dataLang->name = $input['name'][$lang];
	            $dataLang->lang = $lang;
	            $dataLang->save();
	    	}

	    	$result['message'] = 'Brand has been created';
            $result['status'] = 1;
            return response()->json($result);

	    } catch (Exception $e){
            $result['message'] = 'Brand Can`t created';
            $result['status'] = 0;
            return response()->json($result);            
        }

       /*$inputs = [
            'name'=>$request->brand_name,
            'brand_type'=>$request->brand_type,
            'type'=>'Image',
       ];
       $result = [];
        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'brand');
            if($result[0]==true){
                $inputs['file_path'] = $result[1];
                $inputs['file_name'] = $result[3];
                // $inputs['extension'] = $result[2];
            }
        }
		// create a new task based on Brand tasks relationship
        $banner = Brand::create($inputs);
        if($banner->id){
            $result['message'] = 'Brand has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Brand Can`t created';
            $result['status'] = 0;
        }
        return response()->json($result);*/
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
        Gate::authorize('Brand-section');
        $data['brand'] = Brand::select('brands.*', 'brand_category.name as brand_category')->leftjoin('brand_category', 'brand_category.id', '=', 'brands.brand_category')->findOrFail($id);
        return view('brand.view',$data);
    }

    public function restaurant_list($id)
    {
        //
        Gate::authorize('Brand-section');
        $data['restaurants'] = Restaurant::where('brand_id', $id)->get();
        return view('brand.restaurants', $data);
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
        Gate::authorize('Brand-edit');
        $brand = Brand::findOrFail($id);
		return response()->json([
            'user' => $brand
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
        Gate::authorize('Brand-edit');
        $brandData = Brand::where('id',$id)->first();
        // validate

        $mesasge = [
	        'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'main_category_id.required'=>'The Main Category field is required.',
	    ];
		$this->validate($request, [
            'name.en'=>'required|max:255',
            'name.ar'=>'required|max:255', 
            'main_category_id'=>'required',
            'brand_category'=>'required',
            // 'email' => 'string|email|unique:users,email, '. $brandData->user_id .',id',
            // 'mobile'=> 'required|numeric|digits_between:7,15|unique:users,mobile, '. $brandData->user_id .',id,country_code,'.str_replace("+","",$request->country_code),
            'country_code'      => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			'image.size'  => 'the file size is less than 5MB',
        ],$mesasge);

        $input = $request->all();
        $cate_id = $id;
        $file = $request->file('image');
        $lang = Language::pluck('lang')->toArray();
        //update user data
        // create a new task based on user tasks relationship
        $userDetails = User::where('email', $input['email'])->first();

        if (!$userDetails) {
            $userDetails = User::create([
                'name' => $input['name']['en'],
                'first_name' => $input['name']['en'],
                'last_name' => '',
                'mobile' => $input['mobile'],                    
                'email' => $input['email'],
                'country_code' => str_replace("+","",$input['country_code']),
                'password' => Hash::make($input['password']),
                /*'address' => $input['address'],
                'latitude'=> $input['latitude'],
                'longitude'=> $input['longitude'],*/
                'email_verified_at'=> time(),
                // 'image' => $image_path,
                'type' => '4',
                'status' => 1,
            ]);

        } else {

            $inpUserData=[
                'name' => $input['name']['en'],
                'first_name' => $input['name']['en'],
                'last_name' => '',
                'mobile' => $input['mobile'],
                'email' => $input['email'],
                'country_code' => str_replace("+","",$input['country_code']),
            ];

            if($request->password){
                $inpUserData['password'] = Hash::make($input['password']);
            }
            $staff = User::findOrFail($userDetails->id);
            $staff->update($inpUserData);
        }

        try{
            // dd($userDetails->id);

            foreach ($lang as $lang) {

                if ($lang=='en') {

                    if (isset($file)) {
                        $result = image_upload($file,'brand','image');

                        $inp=[
                            'name' => $input['name'][$lang],
                            'main_category_id' => $input['main_category_id'],
                            'brand_category' => $input['brand_category'],
                            'brand_type' => 'Restaurant',
                            'user_id' => $userDetails->id,
                            'email' => $input['email'],
                            'mobile' => $input['mobile'],
                            'country_code' => $input['country_code'],
                            'type' => 'Image',
                            'file_path' => $result[1],
                            'file_name' => $result[3],
                        ];

                        if ($input['password']) {
                            $inp['password']=Hash::make($input['password']);
                        }
                        $data = Brand::where('id',$cate_id)->update($inp);

                    } else {

                        $inp=[
                            'name' => $input['name'][$lang],
                            'main_category_id' => $input['main_category_id'],
                            'brand_category' => $input['brand_category'],
                            'user_id' => $userDetails->id,
                            'brand_type' => 'Restaurant',
                            'email' => $input['email'],
                            'mobile' => $input['mobile'],
                            'country_code' => $input['country_code'],
                        ];

                        if ($input['password']) {
                            $inp['password']=Hash::make($input['password']);
                        }
                    	$data = Brand::where('id',$cate_id)->update($inp);
                    }                    
                }

                $dataLang = BrandLang::where(['brand_id'=>$cate_id,'lang'=>$lang])->first();

                if (isset($dataLang)) {
                   $dataLang = BrandLang::where(['brand_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);

                } else {
                    $dataLang = new  BrandLang;
                    $dataLang->brand_id = $cate_id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
            }
            $result['message'] = 'Brand updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Brand Can`t be updated.';
            $result['status'] = 0;
            return response()->json($result);           
        }
            
        // $input = $request->all();
        $inp = [
            'name'=>$request->brand_name,
            'brand_type'=>$request->brand_type,
            'main_category_id'=>$request->main_category_id,
            'type'=>'Image',
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'brand');

            if ($result[0]==true){   
                $inp['file_path'] = $result[1];
                $inp['file_name'] = $result[3];
                // $inp['extension'] = $result[2];              
            } 
        }
        $brand = Brand::findOrFail($id);

        if ($brand->update($inp)){
            $result['message'] = 'Brand updated successfully';
            $result['status'] = 1;

        } else {
            $result['message'] = 'Brand Can`t updated';
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
        Gate::authorize('Brand-delete');
        if(Brand::findOrFail($id)->delete()){
            $result['message'] = 'Banner deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Banner Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new BrandExport, 'Vendor.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
    
}
