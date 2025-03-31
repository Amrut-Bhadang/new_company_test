<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\GiftBanner;
use App\Models\GiftCategory;
use App\Models\GiftSubCategory;
use App\Models\Products;
use App\Models\Restaurant;
use App\Models\Gift;
use File,DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BannerExport;
use App\Imports\BulkImport;

class GiftBannerController extends Controller
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
        Gate::authorize('Gift-Banner-section');
        $columns = ['gift_categories.name'];

        $banner=GiftBanner::select('gift_banner.id','gift_banner.gift_category_id','gift_banner.created_at','gift_banner.file_path','gift_categories.name')->leftJoin('gift_categories','gift_categories.id','=','gift_banner.gift_category_id')->groupBy('gift_banner.id');
        return Datatables::of($banner)->editColumn('created_at', function ($banner) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($banner->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s'); 
        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(gift_banner.created_at)'), array($request->from_date, $request->to_date));
			}

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('gift_banner.id', 'like', "%{$search['value']}%");
               $query->orHaving('gift_categories.name', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Gift-Banner-section');
        // $data['gifts'] = Gift::all();
        $data['categories_list'] = GiftCategory::select('name','id')->get();
        return view('gift_banner.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Gift-Banner-edit');
        $banner = GiftBanner::select('*')->where('id', $id)->first();
        $data['banner'] = $banner;
        $data['categories_list'] = GiftCategory::select('name','id')->get();

        return view('gift_banner.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Gift-Banner-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Gift-Banner-create');
         // validate
		$this->validate($request, [
            //'category_type'  => 'required',
            'gift_category_id'  => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			'image.size'  => 'the file size is less than 5MB',
        ]);

        $requested_data = $request->all();
        $inputs = [
            //'category_type'=>$requested_data['category_type'],
            'gift_category_id'=>$requested_data['gift_category_id'],
        ];
       $result = [];
        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'banner');
            if($result[0]==true){
                $inputs['file_path'] = $result[1];
                $inputs['file_name'] = $result[3];
                $inputs['extension'] = $result[2];
            }
        }
		// create a new task based on Category tasks relationship
        $banner = GiftBanner::create($inputs);
        if($banner->id){
            $result['message'] = 'Banner has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Banner Can`t created';
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
        Gate::authorize('Gift-Banner-section');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_category($category_id, $sub_category_id = '')
    {
        Gate::authorize('Gift-Banner-section');
        $category=GiftSubCategory::select('name','id')->where('category_id', $category_id)->get();
        //dd($category);
        $data['record'] = $category;

        if ($sub_category_id) {
            $data['sub_category_id'] = $sub_category_id;
        } else {
            $data['sub_category_id'] = '';
        }
        return view('gift_banner.category_list',$data);
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
        Gate::authorize('Gift-Banner-edit');
        $Media = GiftBanner::findOrFail($id);
		return response()->json([
            'user' => $Media
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
        Gate::authorize('Gift-Banner-edit');
        // validate
		$this->validate($request, [
            'gift_category_id' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			'image.size'  => 'the file size is less than 5MB',
        ]);
            
        // $input = $request->all();
        $requested_data = $request->all();

        $inp = [
            'gift_category_id'=>$requested_data['gift_category_id'],
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'banner');
            if($result[0]==true){   
                $inp['file_path'] = $result[1];
                $inp['file_name'] = $result[3];
                $inp['extension'] = $result[2];              
            } 
        }
        $Media = GiftBanner::findOrFail($id);
        if($Media->update($inp)){
            $result['message'] = 'Banner updated successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Banner Can`t updated';
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
        Gate::authorize('Gift-Banner-delete');
        if(GiftBanner::findOrFail($id)->delete()){
            $result['message'] = 'Banner deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Banner Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new BannerExport, 'Banner.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
    
}
