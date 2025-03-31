<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\User;
use App;
use DB;

class PermissionController extends Controller
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

    public function index()
    {
        Gate::authorize('Permission-section');
        $data['permissions'] = Permission::where('is_show', 'Yes')->orderBy('position', 'asc')->get()->toArray();
        //  $data['roles']=Role::all();
        $data['roles'] = $this->getRoles();
        $locale = App::getLocale();
        return view('permissions.permissions', $data);
    }

    public function getRoles()
    {
        Gate::authorize('Permission-section');
        // $role=Role::all();
        $role = Role::whereIn('id', [2, 1])->get();
        foreach ($role as $key => $rol) {
            $permissions = $rol->permissions->pluck('id')->toArray();
            $role[$key]['permission_ids'] = $permissions;
            // dd($permissions);
        }
        return $role->toArray();
    }


    public function user_permissions($userid = '')
    {
        Gate::authorize('Permission-section');
        Gate::authorize('Permission-user');
        $user = User::findOrFail($userid);
        $data['permissions'] = Permission::where('is_show', 'Yes')->orderBy('position', 'asc')->get()->toArray();
        $data['user_permission'] = $user->getAllPermissions()->pluck('id')->toArray();
        $data['user'] = $user;
        // $data['user_permission']=$user->getAllPermissions()->pluck('id')->toArray();
        // if($userid ==1)
        //  {
        //      return redirect('permissions');
        //  }
        if ($userid == '') {
            return redirect('permissions');
        }
        if (!User::find($userid)) {
            return redirect('permissions');
        }
        return view('permissions.user_permissions', $data);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function perm_userData()
    {
        // $user=User::select('email','name','id')->where('id', '!=', 1);
        $user = User::select('email', 'name', 'type', 'id')->whereIn('type', [1]);
        return Datatables::of($user)
            ->addColumn('action', function ($user) {
                return '<a href="' . url('admin/permissions/user_permissions/' . $user->id) . '"  title="'.__('backend.Add_Permissions').'" class="btn btn-xs btn-primary add_permissions"><i class="fa fa-edit"></i>'.__('backend.Permissions').'</a>';
            })->make(true);
    }


    public function saveRolePermission($role_id, $permission_id)
    {
        Gate::authorize('Permission-role');

        $role = Role::findOrFail($role_id);
        if ($role->givePermissionTo($permission_id)) {
            $result = array(
                'status' => true,
                'message' => __('Access given to ') . $role->name
            );
        } else {
            $result = array(
                'status' => false,
                'message' => __('Something went wrong.')
            );
        }
        return response()->json($result);
    }

    public function deleteRolePermission($role_id, $permission_id)
    {
        Gate::authorize('Permission-role');
        $role = Role::findOrFail($role_id);
        if ($role->revokePermissionTo($permission_id)) {
            $result = array(
                'status' => true,
                'message' => __('Access revoked to ') . $role->name
            );
        } else {
            $result = array(
                'status' => false,
                'message' => __('Something went wrong.')
            );
        }
        return response()->json($result);
    }

    public function saveUserPermission($user_id, $permission_id)
    {
        Gate::authorize('Permission-user');
        $user = User::findOrFail($user_id);
        if ($user->givePermissionTo($permission_id)) {
            $result = array(
                'status' => true,
                'message' => __('Access given to ') . $user->name
            );
        } else {
            $result = array(
                'status' => false,
                'message' => __('Something went wrong.')
            );
        }
        return response()->json($result);
    }

    public function deleteUserPermission($user_id, $permission_id)
    {
        
        Gate::authorize('Permission-user');
        $user = User::findOrFail($user_id);
        if ($user->revokePermissionTo($permission_id)) {
            $result = array(
                'status' => true,
                'message' => __('Access revoked to ') . $user->name
            );
        } else {
            $result = array(
                'status' => false,
                'message' => __('Something went wrong.')
            );
        }
        return response()->json($result);
    }
}
