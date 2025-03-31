<?php

namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RolesResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use DB;

class RoleController extends Controller
{
    public function __construct() {
		$this->middleware('auth');
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        Gate::authorize('Role-section');
        $role=Role::select('name','created_at','id');
        return Datatables::of($role)->editColumn('created_at', function ($role) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($role->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->make(true);
	}
    public function frontend()
    {
        Gate::authorize('Role-section');
        return view('roles.listing');
    }

    public function allroles(Request $request, Role $role) {
        Gate::authorize('Role-section');
		return response()->json([ 'data' => Role::all()->pluck('name') ]);
	}
    
    public function roles_list(Request $request, Role $role) {
        Gate::authorize('Role-section');
        $term = $request->input( 'term' );
        $roles = Role::where ( 'name', 'LIKE', '%' . $term . '%' )->paginate ($request->per_page ?? 10)->setPath ( '' );
        $pagination = $roles->appends ( array (
                    'term' => $request->input ( 'term' ) 
            ) );
        // return $pagination;
        return response()->json([ 'data' => $pagination ]);
        
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Role-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Role $role)
    {
        Gate::authorize('Role-create');
        // validate
		$this->validate($request, [
			'name' => 'required|max:255',
		]);
		// create a new role
		$role = Role::create([
			'name' => $request->name,
		]);
		// return role with user object
		return response()->json($role->find($role->id));
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Gate::authorize('Role-edit');
        $role = Role::findOrFail($id);
		return response()->json($role);
    }
    public function edit_frontend($id)
    {
        Gate::authorize('Role-edit');
        $role = Role::findOrFail($id);
        return view('roles.edit',array('role'=>$role));
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
        Gate::authorize('Role-edit');
         // validate
		$this->validate($request, [
			'name' => 'required|max:255',
		]);
        // $input = $request->all();
        $inp=[
            'name' => $request->name,
        ];
        $role = Role::findOrFail($id);
        $role->update($inp);
		return response()->json($role->find($role->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('Role-delete');
        return Role::findOrFail($id)->delete();
    }

   

}
