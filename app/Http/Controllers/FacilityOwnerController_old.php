<?php

namespace App\Http\Controllers;

use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\User;
use App\Models\Courts;
use App\Models\Language;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacilityExport;
use App\Models\Amenity;
use App\Models\CourtCategory;
use App\Models\EmailTemplateLang;
use App\Models\Facility;
use App\Models\FacilityAmenity;
use App\Models\FacilityCategory;
use App\Models\FacilityLang;
use App\Models\FacilityRule;
use App\Models\FacilityRuleLang;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FacilityOwnerController extends Controller
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
        Gate::authorize('Facility-section');
        $columns = ['courts.court_name', 'courts.adress'];
        $login_user = Auth::user();
        if ($login_user->type == '0') {
            $facility = Facility::join('users', 'users.id', 'facilities.facility_owner_id')
                ->select('facilities.*', 'users.name as facility_owner_name');
        } else {
            $facility = Facility::join('users', 'users.id', 'facilities.facility_owner_id')
                ->select('facilities.*', 'users.name as facility_owner_name')
                ->where('facility_owner_id', $login_user->id);
        }


        return Datatables::of($facility)->editColumn('created_at', function ($facility) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($facility->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            // if (!empty($request->from_date) && !empty($request->to_date)) {
            //     $query->whereBetween(DB::raw('DATE(facilities.created_at)'), array($request->from_date, $request->to_date));
            // }

            if ($request->has('facility_status')) {

                if ($request->get('facility_status') && $request->get('facility_status') == 'Active') {
                    $query->where('facilities.status', 1);
                } else if ($request->get('facility_status') && $request->get('facility_status') == 'Deactive') {
                    $query->where('facilities.status', 0);
                }
            }
            if ($request->has('facility_owner_id')) {

                if ($request->get('facility_owner_id')) {
                    $query->where('users.id', $request->get('facility_owner_id'));
                }
            }
            //For Amenities
            // if ($request->has('amenities_id')) {

            //     if ($request->get('amenities_id')) {
            //         $query->where('users.id', $request->get('amenities_id'));
            //     }
            // }
           
            //For Sports
            // if ($request->has('category_id')) {

            //     if ($request->get('category_id')) {
                    
            //         //$categories = CourtCategory::Where('name','like' "%{($request->get('sports_id')}%")->select('name', 'id')->first();
            //         //$query->where('users.id', $request->get('sports_id'));
            //     }
            // }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        //for checking the amenities value
                        if($search['value'] == 'Active' || $search['value'] == 'active'){$status = 1;}else{$status=0;}
                        $q->where('facilities.name', 'like', "%{$search['value']}%")
                        ->orWhere('facilities.address', 'like', "%{$search['value']}%")
                        ->orWhere('facilities.position', 'like', "%{$search['value']}%")
                        ->orWhere('facilities.status', 'like', "%{$status}%");
                       
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend()
    {
        // dd('frontend');

        Gate::authorize('Facility-section');
        $facility = Facility::all();
        $data['roles'] = Role::all();
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();
        $data['amenities'] = Amenity::Where('status', 1)->select('name', 'id')->where('status','1')->orderBY('name','ASC')->get();
        $data['categories'] = CourtCategory::Where('status', 1)->select('name', 'id')->orderBY('name','ASC')->get();


        return view('facility.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Facility-edit');
        //$data['roles']=Role::all();
        $data['facility'] = Facility::findOrFail($id);
        // $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();
        $data['amenities'] = Amenity::Where('status', 1)->select('name', 'id')->get();
        $data['facility_amenities'] = FacilityAmenity::where('facility_id', $id)->pluck('amenity_id')->toArray();
        $data['categories'] = CourtCategory::Where('status', 1)->select('name', 'id')->get();
        $data['facility_categories'] = FacilityCategory::where('facility_id', $id)->pluck('category_id')->toArray();
        $data['facility_rules'] = FacilityRule::where('facility_id', $id)->get();
        $data['total_facility'] = Facility::all()->count()+1;

        return view('facility.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Facility-create');

        // $data = array();
        // $data['country'] = Country::select('phonecode', 'name', 'id')->get();
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();
        $data['amenities'] = Amenity::Where('status', 1)->select('name', 'id')->where('status','1')->orderBY('name','ASC')->get();
        $data['categories'] = CourtCategory::Where('status', 1)->select('name', 'id')->orderBY('name','ASC')->get();
        $data['total_facility'] = Facility::all()->count()+1;

        return view('facility.add', $data);
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
        Gate::authorize('Facility-create');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'address.required' => __("backend.address_required"),
            'amenity_id.required' => __("backend.amenity_id_required"),
            'rules.required' => __("backend.rules_required"),
            'category_id.required' => __("backend.category_id_required"),
           // 'image.size'  =>  __("backend.image_size"),//Amrut

        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            //'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',//Amrut
            'address' => 'required',
            'amenity_id' => 'required',
            'category_id' => 'required',
            'rules' => 'required',
            'position' => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            try {
                $lang = Language::pluck('lang')->toArray();
               
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        $data = new Facility();
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file, 'facility');
                            if ($result[0] == true) {
                                // dd($result);
                                $data->image = $result[1];
                            }
                        }
                        $data->name = $input['name'][$lang];
                        $data->facility_owner_id = $input['facility_owner_id'];
                        $data->address = $input['address'];
                        $data->latitude = $input['latitude'];
                        $data->longitude = $input['longitude'];
                        $data->position = $input['position'] ?? null;
                        $data->status = 1;
                        $data->save();

                        if ($data) {
                            // add aminity
                            foreach ($input['amenity_id'] as $amenity_id) {
                                $facility_amenity = new FacilityAmenity();
                                $facility_amenity->facility_id = $data->id;
                                $facility_amenity->amenity_id = $amenity_id;
                                $facility_amenity->save();
                            }
                            // add category
                            foreach ($input['category_id'] as $category_id) {
                                $facility_category = new FacilityCategory();
                                $facility_category->facility_id = $data->id;
                                $facility_category->category_id = $category_id;
                                $facility_category->save();
                            }
                            // add rules
                            foreach ($input['rules'] as $rules => $value) {
                                $facility_rules = new FacilityRule();
                                $facility_rules->facility_id = $data->id;
                                $facility_rules->rules = $value['en'];
                                $facility_rules->save();
                                // add rules lang en
                                $facility_rules_lang = new FacilityRuleLang();
                                $facility_rules_lang->facility_rule_id = $facility_rules->id;
                                $facility_rules_lang->rules = $value['en'];
                                $facility_rules_lang->lang = 'en';
                                $facility_rules_lang->save();
                                // add rules lang ar
                                $facility_rules_lang = new FacilityRuleLang();
                                $facility_rules_lang->facility_rule_id = $facility_rules->id;
                                $facility_rules_lang->rules = $value['ar'];
                                $facility_rules_lang->lang = 'ar';
                                $facility_rules_lang->save();
                            }
                        }
                    }
                    $dataLang = new FacilityLang();
                    $dataLang->facility_id = $data->id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }

                // send email
                $facility_owner = User::where('id', $data->facility_owner_id)->first();
                $email = EmailTemplateLang::where('email_id', 6)->where('lang', 'en')->select(['name', 'subject', 'description', 'footer'])->first();
                $description = $email->description;
                $description = str_replace("[NAME]", $facility_owner->name, $description);
                $description = str_replace("[FACILITY_NAME]", $data->name, $description);
                dd($description);

                $name = $email->name;
                $name = str_replace("[NAME]", $facility_owner->name, $name);
                
                $record = (object)[];
                $record->description = $description;
                $record->footer = $email->footer;
                $record->name = $name;
                $record->subject = $email->subject;
                Mail::send('emails.facility_register', compact('record'), function ($message) use ($facility_owner, $email) {
                    $message->to($facility_owner->email, config('app.name'))->subject($email->subject);
                    $message->from('dev.inventcolabs@gmail.com', config('app.name'));
                });
                // send email
                $result['message'] = __('backend.facility_created');
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
        Gate::authorize('Facility-section');
        $data['record'] = Facility::with('facilityRules')
            ->join('users', 'users.id', 'facilities.facility_owner_id')
            ->select('facilities.*', 'users.name as facility_owner_name')
            ->where('facilities.id', $id)->first();
        return view('facility.view', $data);
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
        Gate::authorize('Facility-edit');
        $user = Facility::findOrFail($id);
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
        // dd('update', $request->all(), $id);
        Gate::authorize('Facility-edit');
        // validate
        $mesasge = [
            'name.required' => __("backend.name_required"),
            'address.required' => __("backend.address_required"),
            'amenity_id.required' => __("backend.amenity_id_required"),
            'rules.required' => __("backend.rules_required"),
            'category_id.required' => __("backend.category_id_required"),
            'image.size'  =>  __("backend.image_size"),

        ];
        $this->validate($request, [
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'address' => 'required',
            'amenity_id' => 'required',
            'category_id' => 'required',
            'position' => 'required',
            // 'rules' => 'required',

        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            $file = $request->file('image');
            try {
                $lang = Language::pluck('lang')->toArray();
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        if (isset($file)) {
                            $result = image_upload($file, 'facility');
                            $data = Facility::where('id', $id)->update(['name' => $input['name'][$lang], 'facility_owner_id' => $input['facility_owner_id'], 'position' => $input['position'], 'address' => $input['address'], 'latitude' => $input['latitude'], 'longitude' => $input['longitude'], 'image' => $result[1]]);
                        } else {
                            $data = Facility::where('id', $id)->update(['name' => $input['name'][$lang], 'facility_owner_id' => $input['facility_owner_id'], 'position' => $input['position'], 'address' => $input['address'], 'latitude' => $input['latitude'], 'longitude' => $input['longitude']]);
                        }

                        if ($data) {
                            // update facility amenities
                            $facility_amenities = FacilityAmenity::where('facility_id', $id)->delete();
                            foreach ($input['amenity_id'] as $amenity_id) {
                                $facility_amenity = new FacilityAmenity();
                                $facility_amenity->facility_id = $id;
                                $facility_amenity->amenity_id = $amenity_id;
                                $facility_amenity->save();
                            }
                            // update facility categories
                            $facility_categories = FacilityCategory::where('facility_id', $id)->delete();
                            foreach ($input['category_id'] as $category_id) {
                                $facility_category = new FacilityCategory();
                                $facility_category->facility_id = $id;
                                $facility_category->category_id = $category_id;
                                $facility_category->save();
                            }
                            // update rules
                            if ($request->rules != null) {
                                $rules_lang_ids = FacilityRule::where('facility_id', $id)->pluck('id')->toArray();
                                $facility_rules_delete_lang = FacilityRuleLang::whereIn('facility_rule_id',  $rules_lang_ids)->delete();
                                $facility_rules_delete = FacilityRule::where('facility_id', $id)->delete();
                                foreach ($input['rules'] as $rules => $value) {
                                    $facility_rules = new FacilityRule();
                                    $facility_rules->facility_id = $id;
                                    $facility_rules->rules = $value['en'];
                                    $facility_rules->save();

                                    // add rules lang en
                                    $facility_rules_lang = new FacilityRuleLang();
                                    $facility_rules_lang->facility_rule_id = $facility_rules->id;
                                    $facility_rules_lang->rules = $value['en'];
                                    $facility_rules_lang->lang = 'en';
                                    $facility_rules_lang->save();
                                    // add rules lang ar
                                    $facility_rules_lang = new FacilityRuleLang();
                                    $facility_rules_lang->facility_rule_id = $facility_rules->id;
                                    $facility_rules_lang->rules = $value['ar'];
                                    $facility_rules_lang->lang = 'ar';
                                    $facility_rules_lang->save();
                                }
                            }
                        }
                    }
                    $dataLang = FacilityLang::where(['facility_id' => $id, 'lang' => $lang])->first();
                    if (isset($dataLang)) {
                        $dataLang = FacilityLang::where(['facility_id' => $id, 'lang' => $lang])->update(['name' => $input['name'][$lang]]);
                    } else {
                        $dataLang = new FacilityLang;
                        $dataLang->facility_id = $id;
                        $dataLang->name = $input['name'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }

                $result['message'] = __('backend.facility_update_successfully');
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
        //
        Gate::authorize('Facility-delete');
        return Courts::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = Facility::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $facility = Facility::findOrFail($id);

            if ($facility->update($inp)) {
                Courts::where(['facility_id' => $id])->update($inp);
                if ($status == 'active') {
                    $result['message'] = __("backend.facility_status_success");
                    $result['status'] = 1;
                } else {
                    $result['message'] = __("backend.facility_status_deactivate");
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __("backend.facility_status_can`t_updated");
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function exportFacility($slug)
    {
        Gate::authorize('Facility-section');
        return Excel::download(new FacilityExport, 'facilities.csv');
    }
}
