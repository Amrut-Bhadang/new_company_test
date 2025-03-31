<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\CourtLang;
use App\Models\Language;
use App\Models\CourtBooking;
use App\Models\CourtBookingSlot;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BulkExport;
use App\Imports\BulkImport;
use App\Models\Country;
use DB;
use App\Models\EmailTemplateLang;
use App\Models\Banner;
use App\Models\BannerLang;
use App\Models\Courts;
use App\Models\Facility;
use Exception;
use Mail;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        // dd('index');

        Gate::authorize('Banner-section');
        $columns = ['courts.court_name', 'courts.adress'];

        $banner = Banner::query();
        // dd($user->get()->toArray());
        return Datatables::of($banner)->editColumn('created_at', function ($banner) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($banner->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            // if (!empty($request->from_date) && !empty($request->to_date)) {
            //     $query->whereBetween(DB::raw('DATE(banners.created_at)'), array($request->from_date, $request->to_date));
            // }

            if ($request->has('banner_status')) {

                if ($request->get('banner_status') && $request->get('banner_status') == 'Active') {
                    $query->where('banners.status', 1);
                } else if ($request->get('banner_status') && $request->get('banner_status') == 'Deactive') {
                    $query->where('banners.status', 0);
                }
            }
            if ($request->has('banner_owner_name')) {

                if ($request->get('banner_owner_name')) {
                    $query->where('users.name', $request->get('banner_owner_name'));
                }
            }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('banners.title', 'like', "%{$search['value']}%");
                        // ->orWhere('banners.email', 'like', "%{$search['value']}%")
                        // ->orWhere('banners.mobile', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
        // dd('frontend');

        Gate::authorize('Banner-section');
        $banner = Banner::all();
        $data['roles'] = Role::all();
        $data['banner_owner'] = User::Where('type', 1)->select('name', 'id')->get();

        return view('banner.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Banner-edit');
        //$data['roles']=Role::all();
        $data['banner'] = Banner::findOrFail($id);
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('banner.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('create');
        Gate::authorize('Banner-create');

        // $data = array();
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['court'] = Courts::Where('status', 1)->select('court_name', 'id')->get();
        $data['facility'] = Facility::Where('status', 1)->select('name', 'id')->get();
        return view('banner.add', $data);
    }
    public function show_type_data($type, $type_id = null)
    {
        Gate::authorize('Banner-create');
        $data['type'] = $type;
        $data['type_id'] = $type_id;
        if ($type == 'court') {
            $data['record'] = Courts::Where('status', 1)->select('court_name', 'id')->get();
        } else {
            $data['record'] = Facility::Where('status', 1)->select('name', 'id')->get();
        }
        return view('banner.type_list', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        Gate::authorize('Banner-create');
        // validate
        $mesasge = [
            'title.required' => __("backend.title_required"),
            'image.required' => __("backend.image_required"),
            'type.required' => __("backend.type_required"),
            'type_id.required' => __("backend.type_id_required"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'title' => 'required|max:255',
            'type' => 'required',
            'type_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        $data = new Banner;
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file, 'banner');
                            if ($result[0] == true) {
                                $data->image = $result[1];
                            }
                        }
                        $data->title = $input['title'][$lang];
                        $data->type = $input['type'];
                        $data->type_id = $input['type_id'];
                        $data->status = 1;
                        $data->save();
                    }
                    $dataLang = new BannerLang();
                    $dataLang->banner_id = $data->id;
                    $dataLang->title = $input['title'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
                $result['message'] = __('backend.banner_created');
                $result['status'] = 1;
                return response()->json($result);
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        } else {
            $result['message'] = __('backend.Something_went_wrong');
            $result['status'] = 0;
            return response()->json($result);
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
        //    dd('show');
        Gate::authorize('Banner-section');
        $data['record'] = Banner::findOrFail($id);

        return view('banner.view', $data);
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
        Gate::authorize('Banner-edit');
        $user = Banner::findOrFail($id);
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
        // dd($request->all());
        Gate::authorize('Banner-edit');
        // validate
        $mesasge = [
            'title.required' => __("backend.title_required"),
            'type.required' => __("backend.type_required"),
            'type_id.required' => __("backend.type_id_required"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'title' => 'required|max:255',
            'type' => 'required',
            'type_id' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
                $file = $request->file('image');
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        if (isset($file)) {
                            $result = image_upload($file, 'banner');
                            $data = Banner::where('id', $id)->update(['title' => $input['title'][$lang], 'type' => $input['type'], 'type_id' => $input['type_id'], 'image' => $result[1]]);
                        } else {
                            $data = Banner::where('id', $id)->update(['title' => $input['title'][$lang], 'type' => $input['type'], 'type_id' => $input['type_id']]);
                        }
                    }
                    $dataLang = BannerLang::where(['banner_id' => $id, 'lang' => $lang])->first();
                    if (isset($dataLang)) {
                        $dataLang = BannerLang::where(['banner_id' => $id, 'lang' => $lang])->update(['title' => $input['title'][$lang]]);
                    } else {
                        $dataLang = new BannerLang;
                        $dataLang->banner_id = $id;
                        $dataLang->title = $input['title'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }
                $result['message'] = __('backend.banner_update_successfully');
                $result['status'] = 1;
                return response()->json($result);
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        } else {
            $result['message'] = __('backend.Something_went_wrong');
            $result['status'] = 0;
            return response()->json($result);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //    dd('destroy');
        Gate::authorize('Banner-delete');
        return Banner::findOrFail($id)->delete();
    }


    public function changeStatus($id, $status)
    {
        $details = Banner::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $banner = Banner::findOrFail($id);
            if ($banner->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.banner_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.banner_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.banner_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
