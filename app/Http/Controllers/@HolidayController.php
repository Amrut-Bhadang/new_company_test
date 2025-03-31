<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use File,DB;
use App\Models\Restaurant;
use App\Models\Holiday;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DishToppingExport;
use App\Imports\BulkImport;

class HolidayController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        Gate::authorize('Holiday-section');
        $login_user_data = auth()->user();
        $columns = ['holiday.start_date_time','holiday.holiday_reason','holiday.end_date_time'];

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();

            $category=Holiday::select('holiday.id','holiday.restaurant_id','holiday.start_date_time','holiday.holiday_reason','holiday.end_date_time','holiday.created_at','holiday.status','restaurants.name')->where('holiday.restaurant_id',$restaurant_detail->id)->join('restaurants','restaurants.id','=','holiday.restaurant_id');

        } else {   

            $category=Holiday::select('holiday.id','holiday.restaurant_id','holiday.start_date_time','holiday.holiday_reason','holiday.end_date_time','holiday.created_at','holiday.status','restaurants.name')->join('restaurants','restaurants.id','=','holiday.restaurant_id');
        }    
            return Datatables::of($category)->editColumn('created_at', function ($category) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($category->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s'); 

        })->filter(function ($query) use ($request) {

      			if(!empty($request->from_date) && !empty($request->to_date))
      			{
              $startData = $request->from_date.' 00:00:01';
              $endData = $request->to_date.' 23:59:59';
              // $query->whereBetween('holiday.created_at', array($startData, $endData));
      				$query->whereBetween(DB::raw('DATE(holiday.created_at)'), array($request->from_date, $request->to_date));

      			}

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('holiday.start_date_time', 'like', "%{$search['value']}%");
               $query->orHaving('holiday.holiday_reason', 'like', "%{$search['value']}%");
               $query->orHaving('holiday.end_date_time', 'like', "%{$search['value']}%");
               $query->orHaving('restaurants.name', 'like', "%{$search['value']}%");
            }
            
		    })->addIndexColumn()->make(true);
        
    }
    
    public function frontend()
    {
        Gate::authorize('Holiday-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['restaurant'] = Restaurant::all();
        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;

        } else {
            $data['restaurant_id'] = '';
        }
        return view('holiday.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Holiday-edit');
        $login_user_data = auth()->user();
        $data['holiday'] = Holiday::where('id',$id)->first();

        if ($data['holiday']) {
          $data['holiday']->start_date_time = date('d-m-Y', strtotime($data['holiday']->start_date_time));
          $data['holiday']->end_date_time = date('d-m-Y', strtotime($data['holiday']->end_date_time));
        }
        $data['restaurant'] = Restaurant::all();
        $data['user_type'] = $login_user_data->type;

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;

        } else {
            $data['restaurant_id'] = '';
        }
        return view('holiday.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Holiday-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Holiday-create');
         // validate
        $mesasge = [
            'restaurant_id'=>'The Restaurant field is required.',
            'holiday_reason'=>'The Holiday Reason field is required.',
            'start_date_time'=>'The Start Date and Time field is required.',
            'end_date_time'=>'The End Date and Time field is required.',
          ];
          $this->validate($request, [
               'restaurant_id'  => 'required',
               'holiday_reason'  => 'required',
               'start_date_time'  => 'required',
               'end_date_time'  => 'required',
          ],$mesasge);
        $input = $request->all();  
        try{
              $data = new Holiday;
              $data->restaurant_id = $input['restaurant_id'];
              $data->holiday_reason = $input['holiday_reason'];
              $data->start_date_time = date('Y-m-d', strtotime($input['start_date_time']));
              $data->end_date_time = date('Y-m-d', strtotime($input['end_date_time']));

              $data->save();
            
            $result['message'] = 'Holiday has been created';
            $result['status'] = 1;
            return response()->json($result);
        } catch (Exception $e){
            $result['message'] = 'Holiday Can`t created';
            $result['status'] = 0;
            return response()->json($result);            
        }
        
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
        Gate::authorize('Holiday-section');
        $data['holiday'] = Holiday::where('id',$id)->first();
        $restro_id = $data['holiday']['restaurant_id'];
        $data['restaurant'] = Restaurant::select('name')->where('id',$restro_id)->first();
        return view('holiday.view',$data);
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
        Gate::authorize('Holiday-edit');
        $data['holiday'] = Holiday::findOrFail($id);
        $data['restaurant'] = Restaurant::select('name')->where('id',$data['restaurant_id'])->first();
    		return response()->json([
                'holiday' => $holiday
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
        Gate::authorize('Holiday-edit');
        // validate
		$mesasge = [
            'restaurant_id'=>'The Restaurant field is required.',
            'holiday_reason'=>'The Holiday Reason field is required.',
            'start_date_time'=>'The Start Date and Time field is required.',
            'end_date_time'=>'The End Date and Time field is required.',
          ];
          $this->validate($request, [
               'restaurant_id'  => 'required',
               'holiday_reason'  => 'required',
               'start_date_time'  => 'required',
               'end_date_time'  => 'required',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        try{
              $inp['restaurant_id'] = $input['restaurant_id'];
              $inp['holiday_reason'] = $input['holiday_reason'];
              $inp['start_date_time'] = date('Y-m-d', strtotime($input['start_date_time']));
              $inp['end_date_time'] = date('Y-m-d', strtotime($input['end_date_time']));

            //dd($inp['start_date_time']);
              $data = Holiday::where('id',$cate_id)->update($inp);

              $result['message'] = 'Holiday updated successfully.';
              $result['status'] = 1;
              return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Holiday Can`t be updated.';
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
        Gate::authorize('Holiday-delete');
        if(Holiday::findOrFail($id)->delete()){
            $result['message'] = 'Holiday  deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Holiday  Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {
        $details = Holiday::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = Holiday::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Holiday is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Holiday is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Holiday status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild holiday!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Holiday-section');
        return Excel::download(new DishToppingExport, 'Dish_Topping.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
