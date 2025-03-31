<?php

/****************************************************/
// Developer By @Inventcolabs.com
/****************************************************/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BookingChallenge;
use App\Models\CourtBooking;
use App\Models\CourtCategory;
use Illuminate\Http\Request;
use App\Models\Courts;
use App\Models\Facility;
use App\Models\FacilityCategory;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
        $this->middleware('auth:api', ['except' => ['index']]);

        define("A_TO_Z", 'a_to_z');
        define("Z_TO_A", 'z_to_a');
        $this->radius = 100;
    }
    /**
     * This function is use for mobile app to show discount coupons.
     *
     * @method Post
     */

    public function index(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'latitude.required' => __("api.latitude_required"),
            'longitude.required' => __("api.longitude_required"),
        ];
        $validator = Validator::make($input, [
            'latitude'   => 'required',
            'longitude'   => 'required',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $locale = App::getLocale();

            if (!$locale) {
                $locale = 'en';
            }
            $data['banners'] = Banner::where('status', 1)->limit(3)->orderBy('id', 'desc')->get();

            // $data['courts'] = Courts::where('status', 1)->limit(3)->orderBy('id', 'desc')->get();
            $data['courts'] = Courts::leftjoin('court_booking', 'courts.id', "=", 'court_booking.court_id')
                ->leftjoin('facilities', 'facilities.id', "=", 'courts.facility_id')
                ->leftjoin('users', 'users.id', "=", 'facilities.facility_owner_id')
                ->select('courts.*','facilities.name as facility_name','users.mobile','users.country_code','users.show_post_method', DB::raw('count(court_booking.court_id) as booking_time'))
                ->groupBy('courts.id')
                ->where('courts.is_deleted',0)
                ->orderBy(DB::raw('courts.position IS NULL, courts.position'), 'asc')
                ->orderBy('courts.is_featured', 'desc')
                ->orderBy('booking_time', 'desc')
                ->limit(4)
                ->get();
            $court_ids = Courts::pluck('facility_id')->toArray();
            $data['facilities'] = Facility::where('status', 1)->whereIn('id', $court_ids)->limit(3)->orderBy(DB::raw('position IS NULL, position'), 'asc')->orderBy('id', 'desc')->get();
            foreach ($data['facilities'] as $key => $val) {
                $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                $getDistance = json_decode($destance);
                if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                    $data['facilities'][$key]->destance = $getDistance->rows[0]->elements[0]->distance->text;
                } else {
                    $data['facilities'][$key]->destance = '';
                }
                $val->available_court = Facility::join('courts', 'facilities.id', 'courts.facility_id')
                    ->where('facilities.id', $val->id)
                    ->where('courts.status', 1)
                    ->count();
                $val->available_category = FacilityCategory::join('facilities', 'facilities.id', 'facility_categories.facility_id')
                    ->join('court_categories', 'court_categories.id', 'facility_categories.category_id')
                    ->leftjoin('court_categories_lang', 'court_categories.id', 'court_categories_lang.court_category_id')
                    ->where('facility_categories.facility_id', $val->id)
                    ->where('court_categories_lang.lang', $locale)
                    ->select('court_categories.id', 'court_categories_lang.name', 'court_categories.image')->get();
            }
            foreach ($data['courts'] as $key => $val) {
                $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                $getDistance = json_decode($destance);
                if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                    $data['courts'][$key]->destance = $getDistance->rows[0]->elements[0]->distance->text;
                } else {
                    $data['courts'][$key]->destance = '';
                }
                $val->category_image = CourtCategory::where(['status' => 1, 'id' => $val->category_id])->first()->image;
                $val->available_slot = 'pending';
            }

            //get already booked chalanges
            $alreadyBookedChalanges = BookingChallenge::select(DB::raw('count(id) as total_user'), 'court_booking_id')->groupBy('court_booking_id')->having('total_user', '>', 1)->pluck('court_booking_id')->toArray();
            $upcoming_match = CourtBooking::with('bookingTimeSlot', 'bookingChallenges.userDetails')
                ->join('courts', 'courts.id', 'court_booking.court_id')
                ->select('court_booking.*', 'courts.latitude', 'courts.longitude', 'courts.image as court_image', 'courts.address')
                ->where(['court_booking.status' => 1, 'court_booking.order_status' => 'Pending', 'court_booking.booking_type' => 'challenge'])
                ->limit(5)
                ->orderBy('court_booking.booking_date', 'asc');

            if ($alreadyBookedChalanges) {
                $upcoming_match->whereNotIn('court_booking.id', $alreadyBookedChalanges);
            }
            try {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $user = JWTAuth::parseToken()->authenticate();
                    // dd($user,'ddddddddd');
                    // $check_join_challenge = BookingChallenge::where(['court_booking_id' => $val->id, 'user_id' => $user->id])->get();
                    $upcoming_match->where('court_booking.user_id','!=', $user->id);
                    $upcoming_match->where('court_booking.challenge_type','!=', 'private');
                 
                }
            } catch (Exception $e) {
            }

            $upcoming_match = $upcoming_match->get();

            $data['upcoming_match'] = $upcoming_match;
            // dd($data['upcoming_match']);
            foreach ($data['upcoming_match'] as $key => $val) {
                $destance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request->latitude . ',' . $request->longitude . '&destinations=' . $val->latitude . ',' . $val->longitude . '&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4');
                $getDistance = json_decode($destance);
                if (!empty($getDistance->rows[0]->elements[0]->duration)) {
                    $data['upcoming_match'][$key]->destance = $getDistance->rows[0]->elements[0]->distance->text;
                } else {
                    $data['upcoming_match'][$key]->destance = '';
                }

                try {
                    if ($user = JWTAuth::parseToken()->authenticate()) {
                        $user = JWTAuth::parseToken()->authenticate();
                        $check_join_challenge = BookingChallenge::where(['court_booking_id' => $val->id, 'user_id' => $user->id])->get();
                        if (count($check_join_challenge)) {
                            $val->is_challenge = true;
                        } else {
                            $val->is_challenge = false;
                        }
                    }
                } catch (Exception $e) {
                }
            }


            if (isset($data)) {
                $response['status'] = true;
                $response['data'] = $data;
                return response()->json($response, 200);
            } else {
                $response['status'] = false;
                $response['message'] = __("api.something_worng");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }
}
