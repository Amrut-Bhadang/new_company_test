<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Discount;
use App\Models\Category;
use App\Models\Products;
use App\Models\Restaurant;
use App\Models\Gift;
use App\Models\DiscountCategories;
use App\Models\Orders;
use App\Models\OrderReport;
use File,DB;

class OrderReportController extends Controller
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

        Gate::authorize('Order-Report-section');
        $report=OrderReport::select('order_id','id','message','is_reply','replied_message','status','created_at');
        return Datatables::of($report)->editColumn('created_at', function ($report) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($report->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s'); 
        })->filterColumn('created_at',function ($query) use ($request) {
			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(order_reports.created_at)'), array($request->from_date, $request->to_date));
			}
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Order-Report-section');
        return view('order_report.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Order-Report-edit');
        $data['discount'] = Discount::findOrFail($id);
        $data['DiscountCategories'] = DiscountCategories::where('discount_id',$id)->pluck('category_id')->toArray();
        //dd($data['DiscountCategories']);
        return view('order_report.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Order-Report-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Order-Report-create');
         // validate
		$this->validate($request, [
            'category_id'  => 'required',
            'category_type'  => 'required',
            'discount_code'  => 'required',
            'percentage'  => 'required',
            'valid_upto'  => 'required',
            'no_of_use_per_user'  => 'required',
        ],[
        ]);

        $requested_data = $request->all();
        $inputs = [
            'category_type'=>$requested_data['category_type'],
            'discount_code'=>$requested_data['discount_code'],
            'percentage'=>$requested_data['percentage'],
            'valid_upto'=>$requested_data['valid_upto'],
            'no_of_use_per_user'=>$requested_data['no_of_use_per_user'],
            
        ];
        //dd($inputs);
       
        $discount = OrderReport::create($inputs);
        if($discount->id) {
            if($requested_data['category_id']) {
                foreach($requested_data['category_id'] as $key => $value) {

                    $catAssign = new DiscountCategories;
                    $catAssign->discount_id = $discount->id;
                    $catAssign->category_id = $value;
                    $catAssign->save();
                }
            }
        }
        if($discount->id){
            $result['message'] = 'Discount has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Discount Can`t created';
            $result['status'] = 0;
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
        //
        Gate::authorize('Order-Report-section');
        $data['category'] = OrderReport::findOrFail($id);
        $data['order'] = Orders::select('order_no')->where('id',$data['category']->order_id)->first();
        //dd($data);
        return view('order_report.view',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_category($category_type, $category_id='')
    {
        Gate::authorize('Order-Report-section');

        if ($category_type == 'Category') {
            $data['record'] = Category::select('name','id')->get();

        } else if ($category_type == 'Dish') {
            $data['record'] = Products::select('name','id')->get();

        } else if ($category_type == 'Restaurant') {
            $data['record'] = Restaurant::select('name','id')->get();

        } else if ($category_type == 'Gift') {
            $data['record'] = Gift::select('name','id')->get();
        }
        $data['category_type'] = $category_type;

        if ($category_id) {
            $data['category_id'] = $category_id;

        } else {
            $data['category_id'] = '';
        }
        // dd($category_id);

        $data['DiscountCategories'] = DiscountCategories::where('discount_id',$category_id)->pluck('category_id')->toArray();
        return view('discount.category_list',$data);
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
        Gate::authorize('Order-Report-edit');
        $data['discount'] = Discount::findOrFail($id);
        $data['DiscountCategories'] = DiscountCategories::where('discount_id',$id)->pluck('category_id')->toArray();
        //dd($data['DiscountCategories']);
		return view('discount.edit',$data);
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
        Gate::authorize('Order-Report-edit');
        // validate
        $cate_id = $id;
		$this->validate($request, [
            'replied_message'  => 'required',
        ],[
        ]);
            
        // $input = $request->all();
        $requested_data = $request->all();

        $data = OrderReport::findOrFail($id);

        $inp = [
            'replied_message'=>$requested_data['replied_message'],
            'is_reply'=>1,
        ];
    
        if($data->update($inp)){
            $result['message'] = 'Reply updated successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Reply Can`t updated';
            $result['status'] = 0;
        }
		return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('Order-Report-delete');
        if(Media::findOrFail($id)->delete()){
            $result['message'] = 'Banner deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Banner Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {

        $details = OrderReport::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = OrderReport::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Discount code is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Discount code is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Discount code status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    
}
