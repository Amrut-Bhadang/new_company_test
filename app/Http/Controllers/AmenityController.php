<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Courts;
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
use App\Models\Amenity;
use App\Models\AmenityLang;
use Exception;
use Mail;

class AmenityController extends Controller
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

        Gate::authorize('Amenity-section');
        $columns = ['courts.court_name', 'courts.adress'];

        $amenity = Amenity::query();
        // dd($user->get()->toArray());
        return Datatables::of($amenity)->editColumn('created_at', function ($amenity) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($amenity->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            // if (!empty($request->from_date) && !empty($request->to_date)) {
            //     $query->whereBetween(DB::raw('DATE(amenities.created_at)'), array($request->from_date, $request->to_date));
            // }

            if ($request->has('amenity_status')) {

                if ($request->get('amenity_status') && $request->get('amenity_status') == 'Active') {
                    $query->where('amenities.status', 1);
                } else if ($request->get('amenity_status') && $request->get('amenity_status') == 'Deactive') {
                    $query->where('amenities.status', 0);
                }
            }
            if ($request->has('amenity_owner_name')) {

                if ($request->get('amenity_owner_name')) {
                    $query->where('users.name', $request->get('amenity_owner_name'));
                }
            }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('amenities.name', 'like', "%{$search['value']}%");
                            // ->orWhere('amenities.email', 'like', "%{$search['value']}%")
                            // ->orWhere('amenities.mobile', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
    // dd('frontend');

        Gate::authorize('Amenity-section');
        $amenity = Amenity::all();
        $data['roles'] = Role::all();
        $data['amenity_owner'] = User::Where('type',1)->select('name', 'id')->get();

        return view('amenity.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Amenity-edit');
        //$data['roles']=Role::all();
        $data['amenity'] = Amenity::findOrFail($id);
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('amenity.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('create');
        Gate::authorize('Amenity-create');

        // $data = array();
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['amenity_owner'] = User::Where('type',1)->select('name', 'id')->get();

        return view('amenity.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Amenity-create');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], $mesasge);
        $checkAmenity = Amenity::where('name',$request->name['en'])->first();
        if(isset($checkAmenity) && $checkAmenity !=''){
            $result['message'] = __('backend.Duplicate_name');
            $result['status'] = 0;
            return response()->json($result);
        }
        $input = $request->all();
        $fail = false;
   
        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        $data = new Amenity;
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file, 'amenity');
                            if ($result[0] == true) {
                                $data->image = $result[1];
                            }
                        }
                      
                        $data->name = $input['name'][$lang];
                        $data->status = 1;
                        $data->save();
                    }
                    $dataLang = new AmenityLang();
                    $dataLang->amenity_id = $data->id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
                $result['message'] = __('backend.amenity_created');
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
        Gate::authorize('Amenity-section');
        $data['record'] = Amenity::findOrFail($id);

        return view('amenity.view', $data);
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
        Gate::authorize('Amenity-edit');
        $user = Amenity::findOrFail($id);
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
        // dd('update');
        Gate::authorize('Amenity-edit');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
        //Check the duplication of the record
        $checkAmenities = Amenity::where('name',$request->name['en'])->where('id','!=',$id)->first();
        if(isset($checkAmenities) && $checkAmenities !=''){
            $result['message'] = __('backend.Duplicate_name');
            $result['status'] = 0;
            return response()->json($result);
        }else{
      
        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
                $file = $request->file('image');
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        if (isset($file)) {
                            $result = image_upload($file, 'amenity');
                            $data = Amenity::where('id', $id)->update(['name' => $input['name'][$lang], 'image' => $result[1]]);
                        } else {
                            $data = Amenity::where('id', $id)->update(['name' => $input['name'][$lang],]);

                        } 
                       
                    }
                    $dataLang = AmenityLang::where(['amenity_id' => $id, 'lang' => $lang])->first();
                    if (isset($dataLang)) {
                        $dataLang = AmenityLang::where(['amenity_id' => $id, 'lang' => $lang])->update(['name' => $input['name'][$lang]]);
                    } else {
                        $dataLang = new AmenityLang;
                        $dataLang->amenity_id = $id;
                        $dataLang->name = $input['name'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }
                $result['message'] = __('backend.amenity_update_successfully');
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
        Gate::authorize('Amenity-delete');
        return Courts::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = Amenity::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $amenity = Amenity::findOrFail($id);
            if ($amenity->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.amenity_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.amenity_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.amenity_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
