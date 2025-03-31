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
use App\Models\Commission;
use App\Models\CourtCategory;
use App\Models\CourtPopularTime;
use App\Models\DeliveryPrice;
use DB;
use App\Models\EmailTemplateLang;
use App\Models\Facility;
use App\Models\FacilityCategory;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Mail;

class CourtsController extends Controller
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
        Gate::authorize('Court-section');
        $columns = ['courts.court_name', 'courts.adress'];
        $login_user = auth()->user();

        $user = Courts::join('facilities', 'facilities.id', 'courts.facility_id')
            ->join('court_categories', 'court_categories.id', 'courts.category_id')
            ->leftjoin('commissions', 'commissions.court_id', 'courts.id')
            ->select('courts.*', 'courts.id as courtId','facilities.name as facility_name', 'court_categories.name as court_category_name', 'commissions.amount as admin_commission')
            ->where('courts.is_deleted', 0);
         
        if ($login_user->type == '0') {
            $user = Courts::join('facilities', 'facilities.id', 'courts.facility_id')
                ->join('court_categories', 'court_categories.id', 'courts.category_id')
                ->leftjoin('commissions', 'commissions.court_id', 'courts.id')
                ->select('courts.*', 'courts.id as courtId','facilities.name as facility_name', 'court_categories.name as court_category_name', 'commissions.amount as admin_commission')
                ->where('courts.is_deleted', 0);
        } else {
            $user = Courts::join('facilities', 'facilities.id', 'courts.facility_id')
                ->leftjoin('court_categories', 'court_categories.id', 'courts.category_id')
                ->leftjoin('commissions', 'commissions.court_id', 'courts.id')
                ->select('courts.*', 'courts.id as courtId','facilities.name as facility_name', 'court_categories.name as court_category_name', 'commissions.amount as admin_commission')
                ->where('courts.is_deleted', 0)
                ->where('courts.facility_owner_id', $login_user->id);
        }
        $user = $user->groupBy('courts.id');
        return Datatables::of($user)->editColumn('created_at', function ($user) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($user->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(courts.created_at)'), array($request->from_date, $request->to_date));
            }
            if ($request->has('court_status')) {
                if ($request->get('court_status') && $request->get('court_status') == 'Active') {
                    $query->where('courts.status', 1);
                } else if ($request->get('court_status') && $request->get('court_status') == 'Deactive') {
                    $query->where('courts.status', 0);
                }
            }
            if ($request->has('facility_id')) {
                if ($request->get('facility_id')) {
                    $query->where('courts.facility_id', $request->get('facility_id'));
                }
            }

            if ($request->url_facilityId != null) {
                $query->where('courts.facility_id', $request->url_facilityId);
            }
            if ($request->has('category_id')) {

                if ($request->get('category_id')) {
                    $query->where('courts.category_id', $request->get('category_id'));
                }
            }

            if (!empty($request->get('search'))) {
                $search = $request->get('search');
                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('courts.court_name', 'like', "%{$search['value']}%")
                            ->orWhere('courts.address', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }

    public function frontend(Request $request)
    {
        Gate::authorize('Court-section');
        $user = Courts::where('status', '1')->get();
        // dd($request->facility);
        $login_user = auth()->user();
        $data['roles'] = Role::all();
        $data['facilityId'] = $request->facility;
        if ($login_user->type == '1') {
            $data['facility'] = Facility::Where(['status' => 1, 'facility_owner_id' => $login_user->id])->select('name', 'id')->get();
        } else {
            $data['facility'] = Facility::Where(['status' => 1])->select('name', 'id')->get();
        }

        $data['category'] = CourtCategory::Where('status', 1)->select('name', 'id')->get();

        return view('court.listing', $data);
    }

    public function getWalletData(request $request)
    {
        $user = Courts::totalorderCount()->where('type', '0');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $user->whereBetween(DB::raw('DATE(users.created_at)'), array($request->from_date, $request->to_date));
        }

        if (!empty($request->from_price) && !empty($request->to_price)) {
            // $query->having('totalAvlAmount','>=',$request->from_price)->having('totalAvlAmount','<=',$request->to_price);
            $user->havingRaw('totalAvlAmount BETWEEN ? AND ?',  [$request->from_price, $request->to_price]);
        } else {

            if ($request->from_price != '' && $request->to_price != '') {
                $user->havingRaw('totalAvlAmount BETWEEN ? AND ?',  [$request->from_price, $request->to_price]);
            }
        }

        if ($request->has('main_category_id') && $request->main_category_id) {
            $userIds = Orders::join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')->where('restaurants.main_category_id', $request->main_category_id)->groupBy('orders.user_id')->pluck('orders.user_id')->toArray();

            $user->whereIn('users.id', $userIds);
        }

        if ($request->has('country')) {
            $country = array_filter($request->country);

            if (count($country) > 0) {
                $user->whereIn('users.country_code', $request->country);
            }
        }

        $data = $user->get();

        $wallet_amount = 0;

        if ($data) {

            foreach ($data as $key => $value) {
                $wallet_amount += $value->total_wallet;
            }
        }

        $result['message'] = 'Wallet amount';
        $result['status'] = 1;
        $result['data'] = number_format($wallet_amount, 2);
        return response()->json($result);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Court-edit');
        //$data['roles']=Role::all();
        $data['users'] = Courts::findOrFail($id);
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();
        $data['court_category'] = CourtCategory::Where('status', 1)->select('name', 'id')->get();
        $login_user = auth()->user();
        if ($login_user->type == '0') {
            $data['facility'] = Facility::Where(['status' => 1])->select('name', 'id')->get();
        } else {
            $data['facility'] = Facility::Where(['status' => 1, 'facility_owner_id' => $login_user->id])->select('name', 'id')->get();
        }
        $data['admin_commission'] = Commission::Where('court_id', $id)->first();
        if ($data['admin_commission']) {
            $data['admin_commission'] = $data['admin_commission']->amount;
        } else {
            $data['admin_commission'] = 0;
        }
        $data['total_court'] = Courts::all()->count() + 1;
        return view('court.edit', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Court-create');
        $data = array();
        $data['facility_owner'] = User::Where('type', 1)->select('name', 'id')->get();
        $data['court_category'] = CourtCategory::Where('status', 1)->select('name', 'id')->get();
        $login_user = auth()->user();
        if ($login_user->type == '0') {
            $data['facility'] = Facility::Where(['status' => 1])->select('name', 'id', 'address', 'latitude', 'longitude')->get();
        } else {
            $data['facility'] = Facility::Where(['status' => 1, 'facility_owner_id' => $login_user->id])->select('name', 'id')->get();
        }
        $data['admin_commission'] = DeliveryPrice::Where('id', 1)->select('common_commission_percentage')->first();
        $data['total_court'] = Courts::all()->count() + 1;
        return view('court.add', $data);
    }
    public function show_court_category_data($facility_id, $category_id = null)
    {
        Gate::authorize('Court-create');
        $data['facility_id'] = $facility_id;
        $data['category_id'] = $category_id;
        $facility_ids = FacilityCategory::where('facility_id', $facility_id)->pluck('category_id')->toArray();
        $data['record'] = CourtCategory::Where('status', 1)->whereIn('id', $facility_ids)->select('name', 'id')->get();

        return view('court.category_list', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd( $request->all());
        Gate::authorize('Court-create');
        // validate
        $mesasge = [
            'court_name.en.required' => 'The Court Name(English) field is required.',
            'court_name.ar.required' => 'The Court Name(Arabic) field is required.',
            'category_id.required' => 'The Category Id field is required.',
            // 'facility_owner_id.required' => 'The Facility Owner field is required.',
            'address.required' => 'The Address field is required.',
            // 'minimum_hour_book.required' => 'The Minimum Hour Booking field is required.',
            'hourly_price.required' => 'The Hourly Price field is required.',
            'start_time.required' => 'The Start Time field is required.',
            'popular_start_time.required' =>  __("backend.popular_start_time_required"),
            'popular_day.required' =>  __("backend.popular_day_required"),
            'end_time.required' => 'The End Time field is required.',
            'end_time.after' =>  __("backend.end_time_after"),
            'timeslot.required' => 'The Timeslot field is required.',
            'admin_commission.required' => __("backend.admin_commission_required"),
            'admin_commission.numeric' => __("backend.admin_commission_numeric"),
            'admin_commission.min' => __("backend.admin_commission_min"),
            'admin_commission.max' => __("backend.admin_commission_max"),
            'image.size'  => 'the file size is less than 5MB',

        ];
        $this->validate($request, [
            'court_name.en'  => 'required|max:255',
            'court_name.ar'  => 'required|max:255',
            'category_id' => 'required',
            // 'facility_owner_id' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_featured' => 'required',
            'hourly_price' => 'required',
            'start_time' => 'required',
            // 'popular_start_time' => 'required',
            // 'popular_day' => 'required',
            'end_time' => 'required',
            'timeslot' => 'required',
            //'admin_commission' => 'required|numeric|min:1|max:100',
            'admin_commission' => 'required|numeric|max:100',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], $mesasge);

        $input = $request->all();
        $timeslot = $input['timeslot'];
        $addTime = strtotime("+ $timeslot minutes", strtotime($input['start_time']));
        $endTime = date('H:i', $addTime);
        if ($input['end_time'] == "00:00") {
            $input['end_time'] = "24:00";
        }
        if ($endTime > $input['end_time']) {
            $result['message'] = __('backend.difference_start_time_end_time');
            $result['status'] = 0;
            return response()->json($result);
        }
        $fail = false;
        if (!$fail) {
            try {
                $facility = Facility::where('id', $input['facility_id'])->first();
                if ($facility != null) {
                    $input['facility_owner_id'] = $facility->facility_owner_id;
                }
                $login_user = Auth::user();
                $common_commission = DeliveryPrice::findOrFail(1);
                if ($login_user->type == '1') {
                    $input['admin_commission'] = $common_commission->common_commission_percentage;
                }
                $lang = Language::pluck('lang')->toArray();
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        $data = new Courts;
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file, 'court');
                            if ($result[0] == true) {
                                $data->image = $result[1];
                            }
                        }
                        $data->court_name = $input['court_name'][$lang];
                        $data->facility_id = $input['facility_id'] ? $input['facility_id'] : null;
                        $data->category_id = $input['category_id'];
                        $data->facility_owner_id = $input['facility_owner_id'] ? $input['facility_owner_id'] : null;
                        $data->address = $input['address'];
                        $data->latitude = $input['latitude'] ?? 0;
                        $data->longitude = $input['longitude'] ?? 0;
                        $data->is_featured = $input['is_featured'] ?? 0;
                        $data->hourly_price = $input['hourly_price'] ?? 0;
                        $data->start_time = $input['start_time'] ?? null;
                        $data->end_time = $input['end_time'] ?? null;
                        $data->timeslot = $input['timeslot'] ?? null;
                        $data->position = $input['position'] ?? null;
                        $data->court_size = $input['court_size'] ?? null;
                        // $data->popular_day = $input['popular_day'] ?? null;
                        // $data->popular_start_time = $input['popular_start_time'] ?? null;
                        $data->status = 1;
                        $data->save();
                    }
                    
                    $dataLang = new CourtLang;
                    $dataLang->court_id = $data->id;
                    $dataLang->court_name = $input['court_name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
                // commission insert
                if ($data) {
                    $common_commission = DeliveryPrice::findOrFail(1);
                    $commission = new Commission();
                    $commission->amount = $input['admin_commission'];
                    $commission->court_id = $data->id;
                    $commission->facility_id = $input['facility_id'] ? $input['facility_id'] : null;
                    $commission->status = 1;
                    $commission->save();
                }
                
              
                $result['message'] = __('backend.Court_created');
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

    public function book_slot(Request $request)
    {
        // dd($request->all());
        Gate::authorize('Court-create');
        $login_user_data = auth()->user();
        // validate
        $mesasge = [
            'court_id.required' => __('backend.court_id_required'),
            'start_date.required' => __('backend.start_date_required'),
            'slots.required' => __('backend.slots_required'),
            'end_date.required' => __('backend.end_date_required'),
            'end_date.after_or_equal' => __('backend.end_date_after_or_equal'),

        ];
        $this->validate($request, [
            'court_id'  => 'required',
            'start_date'  => 'required',
            'end_date'  => 'required|after_or_equal:start_date',
            'slots'  => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            try {
                /*Check already book*/
                $checkCourtAlreaydBooked = CourtBooking::where('court_id', $input['court_id'])->whereDate('court_booking.booking_date', '=', date('Y-m-d', strtotime($input['start_date'])))->first();

                if (!$checkCourtAlreaydBooked) {
                    $data = new CourtBooking;
                    $data->court_id = $input['court_id'];
                    $data->user_id = $login_user_data->id;
                    $data->booking_date = date('Y-m-d', strtotime($input['start_date']));

                    if ($input['end_date']) {
                        $data->end_booking_date = date('Y-m-d', strtotime($input['end_date']));
                    } else {
                        $data->end_booking_date = date('Y-m-d', strtotime($input['start_date']));
                    }
                    $data->status = 1;
                    $data->save();
                } else {
                    $data = (object)[];
                    $data->id = $checkCourtAlreaydBooked->id;
                }

                if ($data) {

                    if ($input['slots']) {
                        /*Check already book*/
                        $checkCourtSlotAlreaydExist = CourtBookingSlot::where('court_booking_id', $data->id)->get();

                        if (count($checkCourtSlotAlreaydExist)) {
                            //delete old booking slot
                            CourtBookingSlot::where('court_booking_id', $data->id)->delete();
                        }

                        foreach ($input['slots'] as $key => $value) {
                            $slotTime = explode('-', $value);
                            $bookingSlotData = new CourtBookingSlot;
                            $bookingSlotData->court_booking_id = $data->id;
                            $bookingSlotData->booking_date = date('Y-m-d', strtotime($input['start_date']));
                            $bookingSlotData->booking_start_time = $slotTime[0];
                            $bookingSlotData->booking_end_time = $slotTime[1];
                            $bookingSlotData->booking_start_datetime = date('Y-m-d', strtotime($input['start_date'])) . ' ' . $slotTime[0];

                            if ($input['end_date']) {
                                $bookingSlotData->booking_end_datetime = date('Y-m-d', strtotime($input['end_date'])) . ' ' . $slotTime[1];
                            } else {
                                $bookingSlotData->booking_end_datetime = date('Y-m-d', strtotime($input['start_date'])) . ' ' . $slotTime[1];
                            }
                            $bookingSlotData->save();
                        }
                    }
                    $result['message'] = __('backend.Slots_booked_successfully');
                    $result['status'] = 1;
                } else {
                    $result['message'] = __('backend.Something_went_wrong');
                    $result['status'] = 0;
                }

                return response()->json($result);
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        } else {
            $result['message'] = 'Something went wrong.';
            $result['status'] = 0;
            return response()->json($result);
        }
        return response()->json($result);
    }

    public function check_book_slot(Request $request, $court_id, $booking_date)
    {
        // dd('sssssssssssss',$court_id, $booking_date);
        Gate::authorize('Court-create');
        // $bookingSlotData = CourtBookingSlot::select('court_booking_slots.*')->join('court_booking', 'court_booking.id', '=', 'court_booking_slots.court_booking_id')->where('court_booking.court_id',$court_id)->whereDate('court_booking_slots.booking_date', '=', date('Y-m-d', strtotime($booking_date)))->get();
        $bookingSlotData = CourtBookingSlot::select('court_booking_slots.*')
            ->join('court_booking', 'court_booking.id', '=', 'court_booking_slots.court_booking_id')
            ->where('court_booking.court_id', $court_id)
            ->whereDate('court_booking_slots.booking_start_datetime', '<=', date('Y-m-d', strtotime($booking_date)))
            ->whereDate('court_booking_slots.booking_end_datetime', '>=', date('Y-m-d', strtotime($booking_date)))
            /*->whereDate('court_booking_slots.booking_date', '=', date('Y-m-d', strtotime($booking_date)))*/
            ->get();
        $slotsArray = array();

        if (count($bookingSlotData)) {

            foreach ($bookingSlotData as $key => $value) {
                $slotsArray[] = date('H-i', strtotime($value->booking_start_time)) . '-' . date('H-i', strtotime($value->booking_end_time));
            }
        }

        if (count($slotsArray)) {
            $result['status'] = 1;
        } else {
            $result['status'] = 0;
        }
        $result['message'] = __('Slots');
        $result['data'] = $slotsArray;
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
        Gate::authorize('Court-section');
        $data['record'] = Courts::join('facilities', 'facilities.id', 'courts.facility_id')
            ->join('court_categories', 'court_categories.id', 'courts.category_id')
            ->join('commissions', 'commissions.court_id', 'courts.id')
            ->select('courts.*', 'facilities.name as facility_name', 'court_categories.name as court_category_name', 'commissions.amount as admin_commission')
            ->where('courts.id', $id)->first();

        return view('court.view', $data);
    }

    public function manage_timing($id)
    {
        //
        Gate::authorize('Court-section');
        $data['id'] = $id;
        $data['record'] = Courts::findOrFail($id);

        $start_time = date('Y-m-d') . ' ' . $data['record']->start_time;
        $end_time = date('Y-m-d') . ' ' . $data['record']->end_time;
        $interval = (int)$data['record']->timeslot;

        /*$start = new DateTime($start_time);
        $end = new DateTime($end_time);
        $startTime = $start->format('H:i');
        $endTime = $end->format('H:i');
        $i=0;
        $time = [];
        while(strtotime($startTime) <= strtotime($endTime)){
            $start = $startTime;
            $end = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
            $startTime = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
            $i++;
            if(strtotime($startTime) <= strtotime($endTime)){
                $time[$i]['slot_start_time'] = $start;
                $time[$i]['slot_end_time'] = $end;
            }
        }
        $data['time_slot'] = $time;*/

        $ReturnArray = array(); // Define output
        $StartTime    = strtotime($start_time); //Get Timestamp
        $EndTime      = strtotime($end_time); //Get Timestamp

        $AddMins  = $interval * 60;
        $i = 0;

        while ($StartTime < $EndTime) //Run loop
        {
            $diff = (int)abs($EndTime - $StartTime) / 60;

            if ($diff >= $interval) {
                // echo $diff."<br/>";
                // dd($diff,$interval);
                $ReturnArray[$i]['start_time'] = date("H:i", $StartTime);
                $StartTime += $AddMins; //Endtime check
                $ReturnArray[$i]['end_time'] = date("H:i", $StartTime);
                $i++;
            } else {
                break;
            }
        }
        $data['timeslot'] = $ReturnArray;
        // dd($data['timeslot']);

        return view('court.manage_timing', $data);
    }
    public function delete_court($id, $is_delete)
    {
        $details = Courts::find($id);
        if (!empty($details)) {
            if ($is_delete == '1') {
                $inp = 1;
            } else {
                $inp = 0;
            }
            $User = Courts::findOrFail($id);
            $User->is_deleted =$inp;
            $User->update();
            if ($User) {
                if ($User['is_deleted'] == '1') {
                    $result['message'] = 'Court is deleted successfully';
                    $result['status'] = 1;
                } else {
                    $result['message'] = 'Court not deleted';
                    $result['status'] = 0;
                }
            } else {
                $result['message'] = 'Court action can`t be updated!!';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function popularTimingIndex(Request $request, $id)
    {
        // dd($request->all(), $id,'dddddddddddddddddddddd');
        Gate::authorize('Court-section');
        $columns = ['court_popular_time.court_name', 'court_popular_time.adress'];
        $login_user = auth()->user();

        $user = CourtPopularTime::where('court_id', $id);
        // dd($user->get());

        return Datatables::of($user)->editColumn('created_at', function ($user) {
            $timezone = 'Asia/Kolkata';

            if (isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            $dt = new \DateTime($user->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request, $columns) {

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(court_popular_time.created_at)'), array($request->from_date, $request->to_date));
            }
            if ($request->has('popular_time_status')) {
                if ($request->get('popular_time_status') && $request->get('popular_time_status') == 'Active') {
                    $query->where('court_popular_time.status', 1);
                } else if ($request->get('popular_time_status') && $request->get('popular_time_status') == 'Deactive') {
                    $query->where('court_popular_time.status', 0);
                }
            }
            if (!empty($request->get('search'))) {
                $search = $request->get('search');

                if (isset($search['value'])) {
                    $query->where(function ($q) use ($search) {
                        $q->where('court_popular_time.day', 'like', "%{$search['value']}%")
                            ->orWhere('court_popular_time.time', 'like', "%{$search['value']}%");
                    });
                }
            }
        })->addIndexColumn()->make(true);
    }
    public function popularTiming(Request $request, $id)
    {
        Gate::authorize('Court-section');
        $user = Courts::where('status', '1')->get();
        // dd($request->facility);
        $login_user = auth()->user();
        $data['roles'] = Role::all();
        $data['facilityId'] = $request->facility;
        if ($login_user->type == '1') {
            $data['facility'] = Facility::Where(['status' => 1, 'facility_owner_id' => $login_user->id])->select('name', 'id')->get();
        } else {
            $data['facility'] = Facility::Where(['status' => 1])->select('name', 'id')->get();
        }

        $data['category'] = CourtCategory::Where('status', 1)->select('name', 'id')->get();
        $data['id'] = $id;

        return view('court.popular_timing', $data);
    }
    public function popularTimingCreate($id)
    {
        //
        Gate::authorize('Court-section');
        $data['id'] = $id;
        $data['record'] = Courts::findOrFail($id);
        // dd('ddd',$id);

        $start_time = date('Y-m-d') . ' ' . $data['record']->start_time;
        $end_time = date('Y-m-d') . ' ' . $data['record']->end_time;
        $interval = (int)$data['record']->timeslot;
        $ReturnArray = array(); // Define output
        $StartTime    = strtotime($start_time); //Get Timestamp
        $EndTime      = strtotime($end_time); //Get Timestamp

        $AddMins  = $interval * 60;
        $i = 0;

        while ($StartTime < $EndTime) //Run loop
        {
            $diff = (int)abs($EndTime - $StartTime) / 60;

            if ($diff >= $interval) {
                $ReturnArray[$i]['start_time'] = date("H:i", $StartTime);
                $StartTime += $AddMins; //Endtime check
                $ReturnArray[$i]['end_time'] = date("H:i", $StartTime);
                $i++;
            } else {
                break;
            }
        }
        $data['timeslot'] = $ReturnArray;
        // dd($data['timeslot']);

        return view('court.popular_timing_create', $data);
    }
    public function popularTimingStore(Request $request)
    {
        Gate::authorize('Court-create');
        $login_user_data = auth()->user();
        // dd($request->all(),'dddddddddddddddddd');
        // validate
        $mesasge = [
            'court_id.required' => 'The court id field is required.',
            'day.required' => 'The day field is required.',
            'time.required' => 'The time field is required.',
        ];
        $this->validate($request, [
            'court_id'  => 'required',
            'day'  => 'required',
            'time'  => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            try {
                /*Check already book*/
                $checkAlreaydcreated = CourtPopularTime::where(['court_id' => $input['court_id'], 'day' => $input['day'], 'time' => $input['time']])->first();
                // dd($checkAlreaydcreated,'dddddd');
                if (!isset($checkAlreaydcreated)) {
                    $popular_time = new CourtPopularTime;
                    $popular_time->court_id = $input['court_id'];
                    $popular_time->day = $input['day'];
                    $popular_time->time = $input['time'];
                    $popular_time->status = 1;
                    $popular_time->save();
                    $result['message'] = __('backend.popular_time_created');
                    $result['status'] = 1;


                    return response()->json($result);
                } else {
                    $result['message'] = __('backend.popular_time_already_created');
                    $result['status'] = 0;
                    return response()->json($result);
                }
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        }
    }
    public function popularTimingEdit($court_id, $id)
    {
        //    dd('ssssssssssss');
        Gate::authorize('Court-section');
        $data['id'] = $court_id;
        $data['record'] = Courts::findOrFail($court_id);
        $data['popular_booking'] = CourtPopularTime::where(['court_id' => $court_id, 'id' => $id])->first();
        // dd('ddd',$id);

        $start_time = date('Y-m-d') . ' ' . $data['record']->start_time;
        $end_time = date('Y-m-d') . ' ' . $data['record']->end_time;
        $interval = (int)$data['record']->timeslot;
        $ReturnArray = array(); // Define output
        $StartTime    = strtotime($start_time); //Get Timestamp
        $EndTime      = strtotime($end_time); //Get Timestamp

        $AddMins  = $interval * 60;
        $i = 0;

        while ($StartTime < $EndTime) //Run loop
        {
            $diff = (int)abs($EndTime - $StartTime) / 60;

            if ($diff >= $interval) {
                // echo $diff."<br/>";
                // dd($diff,$interval);
                $ReturnArray[$i]['start_time'] = date("H:i", $StartTime);
                $StartTime += $AddMins; //Endtime check
                $ReturnArray[$i]['end_time'] = date("H:i", $StartTime);
                $i++;
            } else {
                break;
            }
        }
        $data['timeslot'] = $ReturnArray;
        // dd($data['timeslot']);

        return view('court.popular_timing_edit', $data);
    }
    public function popularTimingUpdate(Request $request, $court_id, $id)
    {
        Gate::authorize('Court-create');
        $login_user_data = auth()->user();
        $mesasge = [
            'court_id.required' => 'The court id field is required.',
            'day.required' => 'The day field is required.',
            'time.required' => 'The time field is required.',

        ];
        $this->validate($request, [
            'court_id'  => 'required',
            'day'  => 'required',
            'time'  => 'required',
        ], $mesasge);

        $input = $request->all();
        $fail = false;

        if (!$fail) {
            try {
                /*Check already book*/
                $checkAlreaydcreated = CourtPopularTime::where('id', '!=', $id)->where(['court_id' => $input['court_id'], 'day' => $input['day'], 'time' => $input['time']])->first();
                // dd($checkAlreaydcreated,'dddddd');
                if (!isset($checkAlreaydcreated)) {
                    $data = CourtPopularTime::where(['id' => $id, 'court_id' => $court_id])->update(['day' => $input['day'], 'time' => $input['time']]);
                    $result['message'] = __('backend.popular_time_updated');
                    $result['status'] = 1;


                    return response()->json($result);
                } else {
                    $result['message'] = __('backend.popular_time_already_created');
                    $result['status'] = 0;
                    return response()->json($result);
                }
            } catch (Exception $e) {
                $result['message'] = __('backend.Something_went_wrong');
                $result['status'] = 0;
                return response()->json($result);
            }
        }
    }

    public function view_orders($id)
    {
        Gate::authorize('Court-section');
        $data['orders'] = Orders::select('orders.*', 'orders.id as order_id')->where('user_id', $id)->orderBy('id', 'desc')->get();

        if ($data['orders']) {

            foreach ($data['orders'] as $key => $value) {
                $timezone = 'Asia/Kolkata';

                if (isset($_COOKIE['timezone'])) {
                    $timezone = $_COOKIE['timezone'];
                }
                $tz = new \DateTimeZone($timezone);
                // $tz = new \DateTimeZone('Asia/Kolkata');
                $dt = new \DateTime($value->created_at);
                $dt->setTimezone($tz);
                $value->created_at = $dt->format('Y-m-d H:i:s');
            }
        }
        //$data['restaurant_name'] = Restaurant::select('name')->where('id',$orders->restaurant_id)->first();
        return view('court.view_order', $data);
    }

    public function view_address($id)
    {
        Gate::authorize('Court-section');
        $data['address'] = UsersAddress::where('user_id', $id)->get();
        return view('court.view_address', $data);
    }

    public function showtransaction($id, Request $request)
    {
        Gate::authorize('Court-section');
        $transaction = Orders::select('orders.id', 'orders.id as order_id', 'orders.order_status', 'orders.random_order_id', 'orders.created_at', 'orders.amount', 'orders.restaurant_name as store_name', 'main_category.name as service_type')->join('main_category', 'main_category.id', '=', 'orders.main_category_id')->where('orders.user_id', $id);

        if (!empty($request->from_price) && !empty($request->to_price)) {
            $transaction->whereBetween('orders.amount', array($request->from_price, $request->to_price));
        }
        // $data['total'] = $transaction->select(DB::raw('SUM(amount)' as 'total_amount'))->first();
        $data['transaction'] = $transaction->orderBy('id', 'desc')->get();

        if ($data['transaction']) {

            foreach ($data['transaction'] as $key => $value) {
                $timezone = 'Asia/Kolkata';

                if (isset($_COOKIE['timezone'])) {
                    $timezone = $_COOKIE['timezone'];
                }
                $tz = new \DateTimeZone($timezone);
                // $tz = new \DateTimeZone('Asia/Kolkata');
                $dt = new \DateTime($value->created_at);
                $dt->setTimezone($tz);
                $value->created_at = $dt->format('Y-m-d H:i:s');
            }
        }
        $data['total'] = $transaction->selectRaw("(select sum(amount)) as totalAvlAmount")->first();
        $data['id'] = $id;
        // dd($data);

        /*if(!empty($request->from_date) && !empty($request->to_date))
        {
            $query->whereBetween('order.created_at', array($request->from_date, $request->to_date)); 
        }*/
        return view('court.transaction', $data);
    }

    public function exportTransUsers($slug)
    {
        //
        Gate::authorize('Court-section');
        return Excel::download(new CustomerTransExport($slug), 'CustomerTransaction.csv',);
    }

    public function showgifttransaction($id)
    {
        Gate::authorize('Court-section');
        $data['gift_trans'] = GiftOrder::select('gift_orders.id', 'gift_orders.random_order_id', 'gift_orders.order_status', 'gift_orders.created_at', 'gift_orders.points', 'gift_orders.address')->where('gift_orders.user_id', $id)->get();
        $data['id'] = $id;
        return view('court.giftstransaction', $data);
    }

    public function exportGiftUsers($slug)
    {
        Gate::authorize('Court-section');
        return Excel::download(new CustomerGiftExport($slug), 'CustomerGiftTransaction.csv',);
    }

    /*public function exportUsers($slug)
    {
        //
        Gate::authorize('Court-section');
        $export_data=array();
        $export_data[]=array('id'=>'1',
            'user_name'=>'Test',
            'email'=>'Test',
            'mobile'=>'Test',
            'address'=>'Test',
            'status'=>'Test',
            'profile_pic'=> 'Test',
            'created_at'=> 'Test'
        );

        $export_data=Courts::select('name','email','country_code','mobile','type','created_at','id','status')->where('type', '0')->get();
        // dd($export_data);

        return Excel::download($export_data, 'list.xlsx');
        // });
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        Gate::authorize('Court-edit');
        $user = Courts::findOrFail($id);
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
        Gate::authorize('Court-edit');
        // validate
        $mesasge = [
            'court_name.en.required' => 'The Court Name(English) field is required.',
            'court_name.ar.required' => 'The Court Name(Arabic) field is required.',
            'category_id.required' => 'The Category Id field is required.',
            // 'facility_owner_id.required' => 'The Facility Owner field is required.',
            'address.required' => 'The Address field is required.',
            // 'minimum_hour_book.required' => 'The Minimum Hour Booking field is required.',
            'hourly_price.required' => 'The Hourly Price field is required.',
            'start_time.required' => 'The Start Time field is required.',
            'end_time.required' => 'The End Time field is required.',
            'end_time.after' =>  __("backend.end_time_after"),
            'timeslot.required' => 'The Timeslot field is required.',
            'admin_commission.required' => __("backend.admin_commission_required"),
            'admin_commission.numeric' => __("backend.admin_commission_numeric"),
            'admin_commission.min' => __("backend.admin_commission_min"),
            'admin_commission.max' => __("backend.admin_commission_max"),
            'popular_start_time.required' =>  __("backend.popular_start_time_required"),
            'popular_day.required' =>  __("backend.popular_day_required"),
            'image.size'  => 'the file size is less than 5MB',

        ];
        $this->validate($request, [
            'court_name.en'  => 'required|max:255',
            'court_name.ar'  => 'required|max:255',
            'category_id' => 'required',
            // 'facility_owner_id' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_featured' => 'required',
            'hourly_price' => 'required',
            'start_time' => 'required',
            // 'popular_start_time' => 'required',
            // 'popular_day' => 'required',
            'end_time' => 'required',
            'timeslot' => 'required',
            'admin_commission' => 'required|numeric|min:1|max:100',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ], $mesasge);

        $input = $request->all();
        $timeslot = $input['timeslot'];
        $addTime = strtotime("+ $timeslot minutes", strtotime($input['start_time']));
        $endTime = date('H:i', $addTime);

        // $ts1 = strtotime($input['start_time']);
        // $ts2 = strtotime($input['end_time']);     
        // $seconds_diff = $ts2 - $ts1;                            
        // $time = ($seconds_diff/60);

        if ($input['end_time'] == "00:00") {
            $input['end_time'] = "24:00";
        }
        // dd($input['end_time']);
        if ($endTime > $input['end_time']) {
            $result['message'] = __('backend.difference_start_time_end_time');
            $result['status'] = 0;
            return response()->json($result);
        }
        $cate_id = $id;
        $checkCourtExist = Courts::where('id', '!=', $cate_id)->where(['court_name' => $input['court_name']['en']])->first();
        $fail = false;

        /*if ($checkCourtExist) {
          $fail = true;
          $result['message'] = 'Category already exist';
          $result['status'] = 0;
          return response()->json($result);
        }*/

        if (!$fail) {
            $file = $request->file('image');
            try {
                $facility = Facility::where('id', $input['facility_id'])->first();
                if ($facility != null) {
                    $input['facility_owner_id'] = $facility->facility_owner_id;
                }
                $login_user = Auth::user();
                $common_commission = DeliveryPrice::findOrFail(1);
                $court_commission = Commission::where('court_id', $cate_id)->first();
                if ($login_user->type == '1') {
                    $input['admin_commission'] = $court_commission->amount;
                }

                $lang = Language::pluck('lang')->toArray();
                foreach ($lang as $lang) {
                    if ($lang == 'en') {
                        if (isset($file)) {
                            $result = image_upload($file, 'court');
                            $data = Courts::where('id', $cate_id)->update(['court_name' => $input['court_name'][$lang], 'facility_id' => $input['facility_id'] ? $input['facility_id'] : null, 'category_id' => $input['category_id'], 'position' => $input['position'] ?? null, 'court_size' => $input['court_size'] ?? null, 'facility_owner_id' => $input['facility_owner_id'] ? $input['facility_owner_id'] : null, 'address' => $input['address'], 'latitude' => $input['latitude'], 'longitude' => $input['longitude'], 'hourly_price' => $input['hourly_price'], 'start_time' => $input['start_time'], 'end_time' => $input['end_time'], 'timeslot' => $input['timeslot'], 'is_featured' => $input['is_featured'], 'image' => $result[1]]);
                        } else {
                            $data = Courts::where('id', $cate_id)->update(['court_name' => $input['court_name'][$lang], 'facility_id' => $input['facility_id'] ? $input['facility_id'] : null, 'category_id' => $input['category_id'], 'position' => $input['position'] ?? null, 'court_size' => $input['court_size'] ?? null, 'facility_owner_id' => $input['facility_owner_id'] ? $input['facility_owner_id'] : null, 'address' => $input['address'], 'latitude' => $input['latitude'], 'longitude' => $input['longitude'], 'hourly_price' => $input['hourly_price'], 'start_time' => $input['start_time'], 'end_time' => $input['end_time'], 'timeslot' => $input['timeslot'], 'is_featured' => $input['is_featured'],]);
                        }
                    }
                    $dataLang = CourtLang::where(['court_id' => $cate_id, 'lang' => $lang])->first();
                    if (isset($dataLang)) {
                        $dataLang = CourtLang::where(['court_id' => $cate_id, 'lang' => $lang])->update(['court_name' => $input['court_name'][$lang]]);
                    } else {
                        $dataLang = new CourtLang;
                        $dataLang->court_id = $cate_id;
                        $dataLang->court_name = $input['court_name'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }
                if ($data) {
                    $update = Commission::where('court_id', $cate_id)->update(['amount' => $input['admin_commission']]);
                }
                $result['message'] = __('backend.court_update_successfully');
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
        Gate::authorize('Court-delete');
        return Courts::findOrFail($id)->delete();
    }

    public function changeStatus($id, $status)
    {
        $details = Courts::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $User = Courts::findOrFail($id);
            if ($User->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = 'Court is activate successfully';
                    $result['status'] = 1;
                } else {
                    $result['message'] = 'Court is deactivate successfully';
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = 'Court status can`t be updated!!';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function popularTimeChangeStatus($id, $status)
    {
        // dd($id, $status,'dddddddddddddd');
        $details = CourtPopularTime::find($id);
        if (!empty($details)) {
            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }
            $User = CourtPopularTime::findOrFail($id);
            if ($User->update($inp)) {
                if ($status == 'active') {
                    $result['message'] = __('backend.Popular_Time_activate');
                    $result['status'] = 1;
                } else {
                    $result['message'] = __('backend.Popular_Time_deactivate');
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = __('backend.Popular_Time_status_can_not_be_updated');
                $result['status'] = 0;
            }
        } else {
            $result['message'] = __('backend.Invaild_user');
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function changeIsFeatured($id, $is_featured)
    {
       
        $details = Courts::find($id);
        if (!empty($details)) {
            if ($is_featured == 'active') {
                $inp = ['is_featured' => 1];
            } else {
                $inp = ['is_featured' => 0];
            }
            $User = Courts::findOrFail($id);
            if ($User->update($inp)) {
                if ($is_featured == 'active') {
                    $result['message'] = 'Court is mark as featured successfully';
                    $result['status'] = 1;
                } else {
                    $result['message'] = 'Court is unmark as featured successfully';
                    $result['status'] = 1;
                }
            } else {
                $result['message'] = 'Court action can`t be updated!!';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function send_notification(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|max:255',
            'title' => 'required|max:255',
            'message' => 'required',
        ]);

        $details = Courts::find($request->user_id);

        if (!empty($details)) {
            //Notification data
            $notificationData = new Notification;
            $notificationData->user_id = $request->user_id;
            $notificationData->order_id = null;
            $notificationData->user_type = 2;
            $notificationData->notification_type = 3;
            $notificationData->notification_for = 'Admin-Notify';
            $notificationData->title = $request->title;
            $notificationData->message = $request->message;
            $notificationData->save();

            send_notification(1, $request->user_id, 'Admin-Notify', array('title' => $request->title, 'message' => $notificationData->message, 'type' => 'Admin', 'key' => 'event'));

            $result['message'] = 'Notification sent successfully.';
            $result['status'] = 1;
        } else {
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Court-section');
        return Excel::download(new BulkExport, 'Customer.csv');
    }

    public function importUsers()
    {
        Excel::import(new BulkImport, request()->file('file'));
        return back();
    }
}
