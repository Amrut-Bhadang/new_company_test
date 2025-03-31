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
use App\Models\Testimonial;
use App\Models\TestimonialLang;
use App\Models\Courts;
use App\Models\Facility;
use Exception;
use Mail;

class TestimonialController extends Controller
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

        Gate::authorize('Testimonial-section');
        $columns = ['courts.court_name', 'courts.adress'];

        $testimonial = Testimonial::query();
        // dd($user->get()->toArray());
        return Datatables::of($testimonial)->editColumn('created_at', function ($testimonial) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($testimonial->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            // if (!empty($request->from_date) && !empty($request->to_date)) {
            //     $query->whereBetween(DB::raw('DATE(testimonials.created_at)'), array($request->from_date, $request->to_date));
            // }

            if ($request->has('testimonial_status')) {

                if ($request->get('testimonial_status') && $request->get('testimonial_status') == 'Active') {
                    $query->where('testimonials.status', 1);
                } else if ($request->get('testimonial_status') && $request->get('testimonial_status') == 'Deactive') {
                    $query->where('testimonials.status', 0);
                }
            }
            if ($request->has('testimonial_owner_name')) {

                if ($request->get('testimonial_owner_name')) {
                    $query->where('users.name', $request->get('testimonial_owner_name'));
                }
            }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('testimonials.title', 'like', "%{$search['value']}%");
                            // ->orWhere('testimonials.email', 'like', "%{$search['value']}%")
                            // ->orWhere('testimonials.mobile', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
    // dd('frontend');

        Gate::authorize('Testimonial-section');
        $testimonial = Testimonial::all();
        $data['roles'] = Role::all();
        $data['testimonial_owner'] = User::Where('type',1)->select('name', 'id')->get();

        return view('testimonial.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Testimonial-edit');
        //$data['roles']=Role::all();
        $data['testimonial'] = Testimonial::findOrFail($id);
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        return view('testimonial.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('create');
        Gate::authorize('Testimonial-create');

        // $data = array();
        $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['court'] = Courts::Where('status',1)->select('court_name', 'id')->get();
        $data['facility'] = Facility::Where('status',1)->select('name', 'id')->get();
        return view('testimonial.add', $data);
    }
    public function show_type_data($type, $type_id=null)
    {
        Gate::authorize('Testimonial-create');
        $data['type'] = $type;
        $data['type_id'] = $type_id;
        if($type == 'court'){
            $data['record'] = Courts::Where('status',1)->select('court_name', 'id')->get();
        }else{
            $data['record'] = Facility::Where('status',1)->select('name', 'id')->get();
        }
        return view('testimonial.type_list',$data);
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
        Gate::authorize('Testimonial-create');
        // validate
        $mesasge = [
            'title.required' => __("backend.title_required"),
            'description.required' => __("backend.description_required"),
        ];
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
   
        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        $data = new Testimonial;
                        $data->title = $input['title'][$lang];
                        $data->description = $input['description'][$lang];
                        $data->status = 1;
                        $data->save();
                    }
                    $dataLang = new TestimonialLang();
                    $dataLang->testimonial_id = $data->id;
                    $dataLang->title = $input['title'][$lang];
                    $dataLang->description = $input['description'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
                $result['message'] = __('backend.testimonial_created');
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
        Gate::authorize('Testimonial-section');
        $data['record'] = Testimonial::findOrFail($id);

        return view('testimonial.view', $data);
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
        Gate::authorize('Testimonial-edit');
        $user = Testimonial::findOrFail($id);
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
        Gate::authorize('Testimonial-edit');
        // validate
        $mesasge = [
            'title.required' => __("backend.title_required"),
            'description.required' => __("backend.description_required"),
        ];
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;
      
        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
                $file = $request->file('image');
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        $data = Testimonial::where('id', $id)->update(['title' => $input['title'][$lang],'description' => $input['description'][$lang]]);
                    }
                    $dataLang = TestimonialLang::where(['testimonial_id' => $id, 'lang' => $lang])->first();
                    if (isset($dataLang)) {
                        $dataLang = TestimonialLang::where(['testimonial_id' => $id, 'lang' => $lang])->update(['title' => $input['title'][$lang],'description' => $input['description'][$lang]]);
                    } else {
                        $dataLang = new TestimonialLang;
                        $dataLang->testimonial_id = $id;
                        $dataLang->title = $input['title'][$lang];
                        $dataLang->description = $input['description'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }
                $result['message'] = __('backend.testimonial_update_successfully');
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
        Gate::authorize('Testimonial-delete');
        return Testimonial::findOrFail($id)->delete();
    }
    

    public function changeStatus($id, $status)
    {
        $details = Testimonial::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $testimonial = Testimonial::findOrFail($id);
            if ($testimonial->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __("backend.testimonial_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.testimonial_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.testimonial_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
