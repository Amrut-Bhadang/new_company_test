<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Country;
use App\Models\Genres;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Models\Products;
class CelebrityController extends Controller
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
        Gate::authorize('Celebrity-section');
        $user=User::select('users.name','users.email','users.mobile','users.type','users.created_at','users.id','users.status','celebrity_categories.name as genres')
                    ->leftJoin('celebrity_categories','celebrity_categories.id','=','users.genres')
                    ->where('type', '3');
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            return $user->created_at->format('m/d/Y h:m:s'); 
        })->editColumn('mobile', function ($user) {
            return $user->mobile = $user->country_code.' '.$user->mobile;
         })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Celebrity-section');
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['genres']=Genres::select('id','name','description')->get();
        return view('celebrity.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Celebrity-edit');
        //$data['roles']=Role::all();
        $data['celebrity'] = User::findOrFail($id);
        $data['country']=Country::select('phonecode','name','id')->get();
        $data['genres']=Genres::select('id','name','description')->get();
        //$data['staff_roles']=$data['staff']->getRoleNames()->toArray();
        return view('celebrity.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('celebrity-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Celebrity-create');
         // validate
		$this->validate($request, [           
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'mobile' => 'required|numeric|digits_between:7,15|unique:users,mobile,,0,country_code,'.$request->country_code,
            'country_code' 		=> 'required',
            'password' => 'required|min:6|max:20',
            'genres' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        
		// create a new task based on user tasks relationship
		$user = User::create([
            'name' => $request->first_name . ' '.  $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,                    
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'genres' => $request->genres,
            'country_code'=>$request->country_code,
            'latitude'=>$request->latitude,
            'longitude'=>$request->longitude,
            'type' => '3',
            'status' => 1,
        ]);

        if($user->id){
            $result['message'] = 'Celebrity '.$user->name.' has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Celebrity Can`t created';
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
        Gate::authorize('Celebrity-section');
        $data['celebrity'] = User::select('users.*','celebrity_categories.name as genres')
                            ->leftJoin('celebrity_categories','celebrity_categories.id','=','users.genres')
                            ->where('users.id',$id)->first();
        return view('celebrity.view',$data);
    }


    public function productShow($id)
    {
        //
        Gate::authorize('Chef-section');
        $data['products'] =  Products::select('products.*','categories.name as cat_name')
                        ->join('categories','products.category_id','=','categories.id')
                        ->where('products.celebrity_id',$id)
                        ->where('products.is_active',1)
                        ->get();
        return view('celebrity.showProducts',$data);
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
        Gate::authorize('Celebrity-edit');
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
        Gate::authorize('Celebrity-edit');
        // validate
		$this->validate($request, [           
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'country_code' 		=> 'required',
            'mobile'=> 'required',
            'genres' => 'required',
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
            'address' => $request->address,
            'address' => $request->editaddress,
            'latitude'=>$request->editlatitude,
            'longitude'=>$request->editlongitude,
            'genres' => $request->genres,
            'country_code' => $request->country_code,
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
        Gate::authorize('Celebrity-delete');
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
                    $result['message'] = 'Celebrity Account is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Celebrity Account is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Celebrity Account status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

}
