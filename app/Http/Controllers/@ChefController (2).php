<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use File;
use DB;
use App\Models\Products;
use App\Models\ProductAssignTOChef;
class ChefController extends Controller
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
        Gate::authorize('Chef-section');
        $user=User::select('name','country_code','email','mobile','type','address','latitude','longitude','created_at','id','status')->where('type', '2')->whereNull('parent_chef_id');
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            return $user->created_at->format('m/d/Y h:m:s'); 
        })->editColumn('mobile', function ($user) {
           return $user->mobile = $user->country_code.' '.$user->mobile;
        })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Chef-section');
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['chefManager']=User::select('name','id')->where('type', '2')->whereNull('parent_chef_id')->get();
        return view('chef.listing',$data);
    }


    public function chef_staff($chef_id = NULL)
    {
        Gate::authorize('Chef-section');
        $data['country']=Country::select('phonecode','name','id')->get();
        if($chef_id != NULL){
            $chef_id =  $chef_id;
        }
        $data['chefManager']=User::select('name','id')->where('type', '2')->where('id', $chef_id)->get();
        return view('chef.chef-staff-listing',$data);
    }

    public function chef_staff_listing($chef_id = NULL)
    {
        Gate::authorize('Chef-section');
        $user=User::select('name','country_code','email','mobile','type','address','latitude','longitude','created_at','id','status')->where('type', '2')->where('parent_chef_id',$chef_id);
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            return $user->created_at->format('m/d/Y h:m:s'); 
        })->editColumn('mobile', function ($user) {
           return $user->mobile = $user->country_code.' '.$user->mobile;
        })->addIndexColumn()->make(true);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Chef-edit');
        //$data['roles']=Role::all();
        $data['chef'] = User::findOrFail($id);
        $data['country']=Country::select('phonecode','name','id')->get();
        //$data['staff_roles']=$data['staff']->getRoleNames()->toArray();
        return view('chef.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Chef-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Chef-create');
         // validate
		$this->validate($request, [           
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'mobile' => 'required|numeric|digits_between:7,15|unique:users,mobile,,0,country_code,'.$request->country_code,
            'country_code' 		=> 'required',
            'password' => 'required|min:6|max:20',
            'confirm_password' => 'required|same:password',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ],[
			'image.size'  => 'the file size is less than 2MB',
        ]);
        
        // create a new task based on user tasks relationship
        $inputs = [
            'name' => $request->first_name . ' '.  $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,                    
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country_code' => $request->country_code,
            'address' => $request->address,
            'latitude'=>$request->latitude,
            'longitude'=>$request->longitude,
            'food_license' => $request->food_license,
            'license_number' => $request->license_no,
            'type' => '2',
            'status' => 1,
       ];

       if($request->parent_chef_id){
           $inputs['parent_chef_id'] = $request->parent_chef_id;
       }
     
        if ($request->file('image')) {
            $extension 			=	$request->file('image')->getClientOriginalExtension();
            $newFolder     		= 	strtoupper(date('M'). date('Y')).'/';
            $folderPath			=	public_path().'/uploads/food-license/'.$newFolder; 
            if(!File::exists($folderPath)){
                File::makeDirectory($folderPath, $mode = 0777,true);
            }
            $userImageName = time().'-food-license.'.$extension;
            $image = $newFolder.$userImageName;
            if($request->file('image')->move($folderPath, $userImageName)){
                $inputs['license_image']		=	$image;
            }
        }

        $user = User::create($inputs);
        if($user->id){
            $result['message'] = 'Chef '.$user->name.' has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Chef Can`t created';
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
        Gate::authorize('Chef-section');
        $data['chef'] = User::findOrFail($id);
        return view('chef.view',$data);
    }

    public function productShow($id)
    {
        //
        Gate::authorize('Chef-section');
        $data['products'] =  Products::select('products.*','categories.name as cat_name')
                        ->join('categories','products.category_id','=','categories.id')
                        ->join('product_assign_to_chef','product_assign_to_chef.product_id','=','products.id')
                        ->where('product_assign_to_chef.chef_id',$id)
                        ->where('products.is_active',1)
                        ->groupBy('product_assign_to_chef.product_id')
                        
                        ->get();
        return view('chef.showProducts',$data);
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
        Gate::authorize('Chef-edit');
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
        Gate::authorize('Chef-edit');
        // validate
		$this->validate($request, [           
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
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
            'password' => Hash::make($request->password),
            'address' => $request->editaddress,
            'latitude'=>$request->editlatitude,
            'longitude'=>$request->editlongitude,
            'country_code' => $request->country_code,
            'food_license' => $request->food_license,
            'license_number' => $request->license_no,
        ];
        if($request->password){
            $inp['password']=Hash::make($request->password);
        }

        if ($request->file('image')) {
            $extension 			=	$request->file('image')->getClientOriginalExtension();
            $newFolder     		= 	strtoupper(date('M'). date('Y')).'/';
            $folderPath			=	public_path().'/uploads/food-license/'.$newFolder; 
            if(!File::exists($folderPath)){
                File::makeDirectory($folderPath, $mode = 0777,true);
            }
            $userImageName = time().'-food-license.'.$extension;
            $image = $newFolder.$userImageName;
            if($request->file('image')->move($folderPath, $userImageName)){
                $inp['license_image']		=	$image;
            }
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
        Gate::authorize('Chef-delete');
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
                    $result['message'] = 'Chef Account is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Chef Account is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Chef Account status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
