<?php

namespace App\Http\Controllers;

use App\Models\CourtBooking;
use App\Models\CourtLang;
use App\User;
use DB, DateTime;
use App\Models\Courts;
use App\Models\Facility;
use App\Models\UserFavourates;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

		// changeRestaurantStatus();
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index()
	{		
		/*$restaurant_detail = checkBrandLogin();

		if ($restaurant_detail['status'] == false) {
			return redirect()->route('login');
		}
		$restaurant_detail = $restaurant_detail['data'];*/
		$login_user_data = auth()->user();
		// dd($login_user_data);

		/*$no_of_customers=$this->get_no_of_customers();
		$no_of_category=$this->get_no_of_category();
        $no_of_restaurant=$this->get_no_of_restaurant();
		// $no_of_celebrity=$this->get_no_of_celebrity();
		$no_of_orders=$this->get_no_of_orders();
		$no_of_delivery=$this->get_no_of_delivery();
		$new_orders=$this->get_new_orders();
		$complete_orders=$this->get_complete_orders();
		$last_year_income=$this->get_last_year_income();
		$current_year_income=$this->get_current_year_income();
		$overall_income=$this->get_overall_income();
		$no_of_categories=$this->get_no_of_categories();
		$no_of_brands=$this->get_no_of_brands();
		$no_of_products=$this->get_no_of_products();
		$no_of_gifts=$this->get_no_of_gifts();
		$get_popular_brands=$this->get_popular_brands();
		$get_popular_category=$this->get_popular_categories();
		$get_recent_products=$this->get_recent_products();
		$complete_order_list=$this->get_complete_order_list();
		$get_recent_orders=$this->get_recent_orders();
		$yearly_sale_graph_data=json_encode($this->getYearlySaleData());
		$LastFiveMonthsData=json_encode($this->getLastFiveMonthsData());*/
		// dd($driverResponse->result);

		/*$start_date = date('d M Y');
		$end_date = date('d M Y', strtotime("+7 day", $start_date));*/
		$days = 8;

		$dates = array();
		$date = new DateTime();
		for ($i = 0; $i < $days; $i++) {
			$dates[] = $date->format('d M Y');
			$date->modify('-1 day');
		}
		$BookingDataArray = array();
		$datesJson = json_encode($dates);
		$max = 20;

		if (isset($_GET['graph']) && $_GET['graph'] == 'revenue') {

			foreach ($dates as $key => $value) {
				$date = date('Y-m-d', strtotime($value));
				$getBookinData = $this->bookingDataAmountSum($date);

				if ($getBookinData && $getBookinData->total_amount) {
					$BookingDataArray[] = $getBookinData->total_amount;
				} else {
					$BookingDataArray[] = 0;
				}
			}
		} else {

			foreach ($dates as $key => $value) {
				$date = date('Y-m-d', strtotime($value));
				$getBookinData = $this->bookingDataCount($date);

				if ($getBookinData) {
					$BookingDataArray[] = $getBookinData->total_order;
				} else {
					$BookingDataArray[] = 0;
				}
			}
		}

		$maxBooking = max($BookingDataArray);

		if ($maxBooking > 20) {
			$max = $maxBooking;
		}
		$BookingDataArray = json_encode($BookingDataArray);

		$no_of_courts = $this->no_of_courts();
		$facility_owner_no_of_courts = $this->facility_owner_no_of_courts();
		$no_of_admin_commission = $this->get_sum_of_admin_commission();
		$facility_owner_no_of_admin_commission = $this->facility_owner_no_of_admin_commission();
		$no_of_category = 0;
		$no_of_orders = $this->get_no_of_orders();
		$get_most_booking_users = $this->get_most_booking_users();
		$get_most_booking_users_facility_owner = $this->get_most_booking_users_facility_owner();
		$get_most_popular_booking = $this->get_most_popular_booking();
		$get_most_popular_booking_facility_owner = $this->get_most_popular_booking_facility_owner();
		$new_orders = 0;
		$complete_orders = 0;
		$last_year_income = 0;
		$current_year_income = 0;
		$overall_income = 0;
		$no_of_players = $this->no_of_players();
		$no_of_facilities = $this->no_of_facilities();
		$no_of_facility_owners = $this->no_of_facility_owners();
		$no_of_booking = $this->no_of_booking();
		$facility_owner_no_of_booking = $this->facility_owner_no_of_booking();
		$get_popular_brands = 0;
		$get_recent_products = 0;
		$complete_order_list = 0;
		$get_recent_orders = $this->get_recent_orders();
		$get_cash_booking = $this->get_cash_booking();
		$get_cash_booking_facility_owner = $this->get_cash_booking_facility_owner();
		$get_recent_orders_facility_owner = $this->get_recent_orders_facility_owner();
		// dd($get_recent_orders);
		$yearly_sale_graph_data = json_encode(array());
		$LastFiveMonthsData = json_encode(array());
		// dashboard data
		// dd($BookingDataArray);
		if ($login_user_data->type == 1) {
			return view('home', compact('get_cash_booking_facility_owner', 'get_recent_orders_facility_owner', 'get_most_booking_users_facility_owner', 'get_most_popular_booking_facility_owner', 'no_of_courts', 'no_of_admin_commission', 'no_of_orders', 'new_orders', 'complete_orders', 'complete_order_list', 'overall_income', 'current_year_income', 'last_year_income', 'yearly_sale_graph_data', 'LastFiveMonthsData', 'no_of_players', 'no_of_facilities', 'no_of_facility_owners', 'no_of_booking', 'get_popular_brands', 'get_most_booking_users', 'get_recent_products', 'get_recent_orders', 'no_of_category', 'datesJson', 'BookingDataArray', 'max', 'facility_owner_no_of_courts', 'facility_owner_no_of_booking', 'facility_owner_no_of_admin_commission'));
		} else {
			return view('admin_home', compact('get_cash_booking', 'get_most_popular_booking', 'no_of_courts', 'no_of_admin_commission', 'no_of_orders', 'new_orders', 'complete_orders', 'complete_order_list', 'overall_income', 'current_year_income', 'last_year_income', 'yearly_sale_graph_data', 'LastFiveMonthsData', 'no_of_players', 'no_of_facilities', 'no_of_facility_owners', 'no_of_booking', 'get_popular_brands', 'get_most_booking_users', 'get_recent_products', 'get_recent_orders', 'no_of_category', 'datesJson', 'BookingDataArray', 'max', 'facility_owner_no_of_courts', 'facility_owner_no_of_booking', 'facility_owner_no_of_admin_commission'));
		}
	}

	

	public function get_no_of_customers()
	{
		$user = User::where('type', '0')->get()->count();
		return $user;
	}

	public function no_of_courts()
	{
		$courts = Courts::get()->count();
		return $courts;
	}
	public function facility_owner_no_of_courts()
	{
		$login_user_data = auth()->user();
		$courts = Courts::where('facility_owner_id', $login_user_data->id)->get()->count();
		return $courts;
	}
	public function no_of_players()
	{
		$players = User::where(['type' => 3,'is_deleted'=>0])->count();
		return $players;
	}
	public function no_of_facility_owners()
	{
		$data = User::where(['type' => 1,'is_deleted'=>0])->count();
		return $data;
	}
	public function no_of_facilities()
	{
		$data = Facility::where(['is_deleted'=>0])->count();
		return $data;
	}
	public function set_to_court_favourate(Request $request)
	{
		$auth_user = Session::get('AuthUserData');
	    $userId=$auth_user->data->id;
	
	$typeId=$request->Courtid;
	$type=$request->method;

		if($typeId && $type!=''){
			$data = new UserFavourates();
			$data->typeId= $typeId;
			$data->type=$type;
			$data->user_id=$userId;
			$data->created_at=date('Y-m-d H:i:s');
			$data->updated_at=date('Y-m-d H:i:s');
			$data->save();

			$html = '<span class="btn btn-success btn-xs" >
						<span class="fa fa-heart-o"></span>
					</span>';
			$data['status'] = 'success';
			$data['html'] = $html;
			echo json_encode($data);
		}else{
			$html = '<span class="btn btn-xs" >
						<span class="fa fa-heart-o"></span>
					</span>';
			$data['status'] = 'error';
			$data['html'] = $html;
			echo json_encode($data);
		}
		
		
	}

	public function set_to_facility_favourate(Request $request)
	{

		$auth_user = Session::get('AuthUserData');
	    $userId=$auth_user->data->id;

		$typeId=$request->Facilityid;
		$type=$request->method;
		if($typeId && $type!=''){
			$data = new UserFavourates();
			$data->typeId= $typeId;
			$data->type=$type;
			$data->user_id=$userId;
			$data->created_at=date('Y-m-d H:i:s');
			$data->updated_at=date('Y-m-d H:i:s');
			$data->save();
			return 1;

		}else{
			
			return 0;
		}
		
	}
	public function no_of_booking()
	{
		$data = CourtBooking::where('is_deleted',0)->count();
		return $data;
	}
	public function facility_owner_no_of_booking()
	{
		$login_user = auth()->user();
		$data = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
			->where('courts.facility_owner_id', $login_user->id)->get()->count();
		return $data;
	}

	public function get_no_of_category()
	{

		$login_user_data = auth()->user();
		$category = Category::count();
		return $category;
	}

	public function get_no_of_orders()
	{
		$orders = CourtBooking::select('id')->get()->count();
		return $orders;
	}

	public function get_new_orders()
	{
		$orders = CourtBooking::select('id')->where('order_status', 'Pending')->get()->count();
		return $orders;
	}

	public function get_complete_orders()
	{
		$orders = CourtBooking::select('id')->where('order_status', 'Complete')->get()->count();
		return $orders;
	}

	public function get_no_of_restaurant()
	{
		$user = User::select('id')->where('type', '4')->get()->count();
		return $user;
	}

	public function get_sum_of_admin_commission()
	{
		$total_commission = CourtBooking::sum('admin_commission_amount');
		return round($total_commission,2);
	}
	public function facility_owner_no_of_admin_commission()
	{
		$auth_user = auth()->user();
		$total_commission = CourtBooking::join('courts', 'courts.id', 'court_booking.court_id')
			->where('courts.facility_owner_id', $auth_user->id)->sum('admin_commission_amount');
		return round($total_commission,2);
	}

	public function get_no_of_delivery()
	{
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$user = CourtBooking::where('order_status', 'Complete')->where('restaurant_id', $restaurant_detail->id)->count();
		} else {
			$user = CourtBooking::where('order_status', 'Complete')->count();
		}
		return $user;
	}

	public function get_last_year_income()
	{
		$last_year = date("Y", strtotime("-1 year")) . '-01-01';
		$last_year_end = date("Y", strtotime("-1 year")) . '-12-31';
		//dd($last_year_end);
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$user = CourtBooking::whereBetween('created_at', [$last_year, $last_year_end])->where('restaurant_id', $restaurant_detail->id)->sum('amount');
		} else {
			$user = CourtBooking::whereBetween('created_at', [$last_year, $last_year_end])->sum('admin_amount');
		}
		return $user;
	}

	public function get_current_year_income()
	{
		$current_year = date("Y") . '-01-01';
		$curent_year_end = date("Y") . '-12-31';
		//dd($last_year_end);
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$user = CourtBooking::whereBetween('created_at', [$current_year, $curent_year_end])->where('restaurant_id', $restaurant_detail->id)->sum('amount');
		} else {
			$user = CourtBooking::whereBetween('created_at', [$current_year, $curent_year_end])->sum('admin_amount');
		}
		return $user;
	}

	public function get_overall_income()
	{
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$user = CourtBooking::where('order_status', 'Complete')->where('restaurant_id', $restaurant_detail->id)->sum('amount');
		} else {
			$user = CourtBooking::where('order_status', 'Complete')->sum('admin_amount');
		}
		return number_format($user, 2);
	}

	public function get_current_year_sale()
	{
		$current_year = date("Y") . '-01-01';
		$curent_year_end = date("Y") . '-12-31';
		//dd($last_year_end);
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$user = CourtBooking::whereBetween('created_at', [$current_year, $curent_year_end])->where('restaurant_id', $restaurant_detail->id)->where('order_status', 'Complete')->get()->count();
		} else {
			$user = CourtBooking::whereBetween('created_at', [$current_year, $curent_year_end])->where('order_status', 'Complete')->get()->count();
		}
		return $user;
	}


	public function get_current_month_sale()
	{
		$current_month = date("Y-m") . '-01';
		$curent_month_end = date("Y-m") . '-31';
		//dd($last_year_end);

		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$user = CourtBooking::whereBetween('created_at', [$current_month, $curent_month_end])->where('restaurant_id', $restaurant_detail->id)->where('order_status', 'Complete')->get()->count();
		} else {
			$user = CourtBooking::whereBetween('created_at', [$current_month, $curent_month_end])->where('order_status', 'Complete')->get()->count();
		}
		return $user;
	}

	public function getYearlySaleData()
	{
		$yearlyData = CourtBooking::select(DB::raw('CONVERT(YEAR(created_at), CHAR) as period'), DB::raw('ROUND(sum(amount), 2) as count'))->groupby('period')->where('order_status', 'Complete')->get();
		// echo "<pre>";print_r(json_encode($yearlyData));die;
		return $yearlyData;
	}

	public function getLastFiveMonthsData()
	{
		$newData = ['Net Income'];
		$monthsData = CourtBooking::select(DB::raw('CONVERT(MONTH(created_at), CHAR) as period'), 'id', DB::raw('ROUND(sum(amount), 2) as admin_amount'))->where("created_at", ">", Carbon::now()->subMonths(6))->groupby('period')->orderby('created_at', 'desc')->pluck('admin_amount')->toArray();
		$mergeData = array_merge($newData, $monthsData);
		// echo "<pre>";print_r(json_encode($mergeData));die;
		return $mergeData;
	}

	public function courtDataCount($date)
	{
		$monthsData = Courts::select(DB::raw('COUNT(id) as total_court'))->whereDate('created_at', '=', $date)->first();/*->pluck('total_court')->toArray()*/;
		return $monthsData;
	}

	public function bookingDataCount($date)
	{
		$auth_user = auth()->user();

		if ($auth_user->type == 0) {
			$monthsData = CourtBooking::select(DB::raw('COUNT(id) as total_order'))->whereDate('created_at', '=', $date)->first();/*->pluck('total_court')->toArray()*/;

		} else {
			$courtIds = Courts::where('facility_owner_id', $auth_user->id)->pluck('facility_owner_id')->toArray();
			$monthsData = CourtBooking::select(DB::raw('COUNT(id) as total_order'))->whereIn('court_id', $courtIds)->whereDate('created_at', '=', $date)->first();/*->pluck('total_court')->toArray()*/;
		}
		return $monthsData;
	}

	public function bookingDataAmountSum($date)
	{
		$auth_user = auth()->user();

		if ($auth_user->type == 0) {
			$monthsData = CourtBooking::select(DB::raw('SUM(admin_commission_amount) as total_amount'))->whereDate('created_at', '=', $date)->first();/*->pluck('total_court')->toArray()*/;
		} else {
			$courtIds = Courts::where('facility_owner_id', $auth_user->id)->pluck('facility_owner_id')->toArray();
			$monthsData = CourtBooking::select(DB::raw('SUM(admin_commission_amount) as total_amount'))->whereIn('court_id', $courtIds)->whereDate('created_at', '=', $date)->first();/*->pluck('total_court')->toArray()*/;

		}
		return $monthsData;
	}
	// public function bookingDataAmountSumFacilityOwner($date)
	// {
	// 	$login_user_data = auth()->user();
	// 	$monthsData = CourtBooking::select(DB::raw('SUM(admin_commission_amount) as total_amount'))->whereDate('created_at', '=', $date)->first();/*->pluck('total_court')->toArray()*/;
	// 	return $monthsData;
	// }

	public function LastEightDaysCourtData($days)
	{
		$monthsData = Courts::select(DB::raw('CONVERT(DAY(created_at), CHAR) as period'), 'id', 'created_at', DB::raw('COALESCE(COUNT(id), 0) as total_court'))->where("created_at", ">", Carbon::now()->subDays($days))->groupby('period')->orderby('created_at', 'desc')->get();/*->pluck('total_court')->toArray()*/;
		return $monthsData;
	}


	public function get_no_of_categories()
	{
		$category = Category::select('id')->where('type', '1')->get()->count();
		return $category;
	}

	public function get_no_of_brands()
	{
		$brand = Brand::select('id')->get()->count();
		return $brand;
	}

	public function get_no_of_products()
	{
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$products = Products::select('id')->where('restaurant_id', $restaurant_detail->id)->get()->count();
			return $products;
		} else {
			$products = Products::select('id')->get()->count();
			return $products;
		}
	}

	public function get_no_of_gifts()
	{
		$gift = Gift::select('id')->get()->count();
		return $gift;
	}

	public function get_popular_brands()
	{
		$brandIds = CourtBooking::select('orders.restaurant_id', 'restaurants.name', 'restaurants.brand_id', 'brands.id', 'brands.name', 'brands.file_path', 'brands.status', DB::raw('count(restaurants.brand_id) as count'))
			->join('restaurants', 'restaurants.id', '=', 'orders.restaurant_id')
			->join('brands', 'brands.id', '=', 'restaurants.brand_id')
			->groupBy('restaurants.brand_id')
			->orderby('count', 'desc')->limit('5')->get();

		return $brandIds;
	}

	public function get_most_booking_users()
	{
		$users = User::join('court_booking', 'court_booking.user_id', "=", 'users.id')
		->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
			->select('users.id','name', 'email', 'country_code', 'mobile', 'users.status', 'users.created_at', 'courts.court_name', DB::raw('max(court_booking.total_amount) as court_booking_total'))
			->with('courtBookingDetail.courtDetails')
			->groupBy('court_booking.user_id')
			->orderBy('court_booking_total', 'desc')
			->limit(5)
			->get();
		// dd($users);
		return $users;
	}
	public function get_most_booking_users_facility_owner()
	{
		$login_user = auth()->user();
		$users = User::join('court_booking', 'court_booking.user_id', "=", 'users.id')->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
			->select('users.id','name', 'email', 'country_code', 'mobile', 'users.status', 'users.created_at', 'courts.court_name', DB::raw('max(court_booking.total_amount) as court_booking_total'))
			->with('courtBookingDetail.courtDetails')
			->groupBy('court_booking.user_id')
			->orderBy('court_booking_total', 'desc')
			->where('courts.facility_owner_id', $login_user->id)
			->limit(5)
			->get();
		// dd($users);
		return $users;
	}
	public function get_most_popular_booking()
	{
		$data = CourtBooking::select('court_booking.*', DB::raw('count(court_booking.court_id) as booking_time'))
			->with('courtDetails')
			->groupBy('court_booking.court_id')
			->orderBy('booking_time', 'desc')
			->limit(5)
			->get();
		// dd($data);
		return $data;
	}
	public function get_most_popular_booking_facility_owner()
	{
		$login_user = auth()->user();
		$data = CourtBooking::with('courtDetails')
		->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
			->select('court_booking.*', 'courts.court_name', DB::raw('count(court_booking.court_id) as booking_time'))
			->groupBy('court_booking.court_id')
			->orderBy('booking_time', 'desc')
			->where('courts.facility_owner_id', $login_user->id)
			->limit(5)
			->get();
		// dd($data);
		return $data;
	}

	public function get_recent_products()
	{
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$products = Products::select('products.id', 'products.name', 'products.created_at', 'is_active', 'total_amount', 'categories.name as category_name', 'main_category.name as main_category_name')->join('categories', 'categories.id', '=', 'products.category_id')->leftJoin('main_category', 'main_category.id', '=', 'categories.main_category_id')->where('restaurant_id', $restaurant_detail->id)->orderby('products.id', 'desc')->limit('5')->get();
			return $products;
		} else {
			$products = Products::select('products.id', 'products.name', 'products.created_at', 'is_active', 'total_amount', 'categories.name as category_name', 'main_category.name as main_category_name')->join('categories', 'categories.id', '=', 'products.category_id')->leftJoin('main_category', 'main_category.id', '=', 'categories.main_category_id')->orderby('products.id', 'desc')->limit('5')->get();
			return $products;
		}
	}

	public function get_recent_orders()
	{
		$login_user_data = auth()->user();
		$court_booking = CourtBooking::select('court_booking.*', 'users.name as user_name', 'users.email', 'users.country_code', 'users.mobile', 'courts.court_name')
		->with('courtDetails')
		->join('users', 'users.id', '=', 'court_booking.user_id')
		->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
		->orderby('court_booking.id', 'desc')
		->limit('10')
		->get();
		// dd($court_booking);
		return $court_booking;
	}
	public function get_recent_orders_facility_owner()
	{
		$login_user = auth()->user();
		$court_booking = CourtBooking::select('court_booking.*', 'users.name as user_name', 'users.email', 'users.country_code', 'users.mobile', 'courts.court_name')
		->with('courtDetails')
		->join('users', 'users.id', '=', 'court_booking.user_id')
			->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
			->orderby('court_booking.id', 'desc')
			->where('courts.facility_owner_id', $login_user->id)
			->limit('10')
			->get();
		// dd($court_booking);
		return $court_booking;
	}
	public function get_cash_booking()
	{
		$login_user_data = auth()->user();
		$court_booking = CourtBooking::select('court_booking.*', 'users.name as user_name', 'users.email', 'users.country_code', 'users.mobile', 'courts.court_name')
		->with('courtDetails')
		->join('users', 'users.id', '=', 'court_booking.user_id')
			->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
			->where(['court_booking.payment_type' => 'cash', 'court_booking.order_status' => 'Pending'])
			->orderby('court_booking.id', 'desc')
			->limit('10')
			->get();
		// dd($court_booking);
		return $court_booking;
	}
	public function get_cash_booking_facility_owner()
	{
		$login_user = auth()->user();
		$court_booking = CourtBooking::select('court_booking.*', 'users.name as user_name', 'users.email', 'users.country_code', 'users.mobile', 'courts.court_name')
		->with('courtDetails')
		->join('users', 'users.id', '=', 'court_booking.user_id')
			->leftjoin('courts', 'courts.id', "=", 'court_booking.court_id')
			->where(['court_booking.payment_type' => 'cash', 'court_booking.order_status' => 'Pending'])
			->where('courts.facility_owner_id', $login_user->id)
			->orderby('court_booking.id', 'desc')
			->limit('10')
			->get();
		// dd($court_booking);
		return $court_booking;
	}

	public function get_complete_order_list()
	{
		$login_user_data = auth()->user();
		// $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
		$restaurant_detail = checkBrandLogin();
		$restaurant_detail = $restaurant_detail['data'];

		if ($login_user_data->type == 4) {
			$orders = CourtBooking::select('orders.id', 'orders.user_id', 'orders.restaurant_id', 'orders.created_at', 'orders.status', 'orders.org_amount', 'users.name')->join('users', 'users.id', '=', 'orders.user_id')->where(['orders.restaurant_id' => $restaurant_detail->id, 'orders.order_status' => 'Complete'])->orderby('orders.id', 'desc')->limit('5')->get();
			return $orders;
		} else {
			$orders = CourtBooking::select('orders.id', 'orders.user_id', 'orders.created_at', 'orders.status', 'orders.org_amount', 'users.name')->join('users', 'users.id', '=', 'orders.user_id')->where(['orders.order_status' => 'Complete'])->orderby('orders.id', 'desc')->limit('5')->get();
			return $orders;
		}
	}
	// public function get_no_of_celebrity(){
	// 	$user=User::select('id')->where('type', '3')->get()->count();
	// 	return $user;
	// }

	function switchAccount($user_id)
	{
		switchAccount($user_id);

		$result['message'] = 'Account Switch Successfully.';
		$result['status'] = 1;
		return response()->json($result);
	}
}
