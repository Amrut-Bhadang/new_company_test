<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\GiftBrand;
use App\Models\GiftBrandLang;
use App\Models\Restaurant;
use App\Models\RestaurantLang;
use App\Models\Language;
use App\Models\Gift;
use File,DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftBrandExport;
use App\Imports\BulkImport;

class GiftBrandController extends Controller
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
        Gate::authorize('Gift-Brand-section');
        $columns = ['gift_brand.name'];

        $brand=GiftBrand::select('file_path','name','created_at','id');
        return Datatables::of($brand)->editColumn('created_at', function ($brand) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($brand->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(gift_brand.created_at)'), array($request->from_date, $request->to_date));
			}

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('gift_brand.name', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Gift-Brand-section');
        return view('gift_brand.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Gift-Brand-edit');
        $data['brand'] = GiftBrand::select('*')->where('id', $id)->first();
        return view('gift_brand.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Gift-Brand-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Gift-Brand-create');
         // validate

        $mesasge = [
	        'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
	    ];
		$this->validate($request, [
            'name.en'=>'required|max:255',
            'name.ar'=>'required|max:255', 
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			'image.size'  => 'the file size is less than 5MB',
        ],$mesasge);

        $input = $request->all();  
	    try{
	        $lang = Language::pluck('lang')->toArray();
	        $data = new GiftBrand;
	        foreach($lang as $lang){

	            if($lang=='en')
	            {
	               
	                if ($request->file('image')) {
	                    $file = $request->file('image');
	                    $result = image_upload($file,'brand','image');

	                    if ($result[0]==true){
	                        $data->file_path = $result[1];
	                        $data->file_name = $result[3];
	                    }
	                }
	                $data->name = $input['name'][$lang];
	                $data->save();
	            }
	            $dataLang = new  GiftBrandLang;
	            $dataLang->brand_id = $data->id;
	            $dataLang->name = $input['name'][$lang];
	            $dataLang->lang = $lang;
	            $dataLang->save();
	    	}

	    	$result['message'] = 'Brand has been created';
            $result['status'] = 1;
            return response()->json($result);

	    } catch (Exception $e){
            $result['message'] = 'Brand Can`t created';
            $result['status'] = 0;
            return response()->json($result);            
        }

       /*$inputs = [
            'name'=>$request->brand_name,
            'brand_type'=>$request->brand_type,
            'type'=>'Image',
       ];
       $result = [];
        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'brand');
            if($result[0]==true){
                $inputs['file_path'] = $result[1];
                $inputs['file_name'] = $result[3];
                // $inputs['extension'] = $result[2];
            }
        }
		// create a new task based on Brand tasks relationship
        $banner = Brand::create($inputs);
        if($banner->id){
            $result['message'] = 'Brand has been created';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Brand Can`t created';
            $result['status'] = 0;
        }
        return response()->json($result);*/
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
        Gate::authorize('Gift-Brand-section');
        $data['brand'] = GiftBrand::findOrFail($id);
        return view('gift_brand.view',$data);
    }

    public function brand_gifts_list($id)
    {
        //
        Gate::authorize('Gift-Brand-section');
        $data['gift'] = Gift::where('brand_id', $id)->get();
        return view('gift_brand.gifts', $data);
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
        Gate::authorize('Gift-Brand-edit');
        $brand = GiftBrand::findOrFail($id);
		return response()->json([
            'user' => $brand
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
        Gate::authorize('Gift-Brand-edit');
        // validate

        $mesasge = [
	        'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
	    ];
		$this->validate($request, [
            'name.en'=>'required|max:255',
            'name.ar'=>'required|max:255', 
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			'image.size'  => 'the file size is less than 5MB',
        ],$mesasge);

        $input = $request->all();
        $cate_id = $id;
        $file = $request->file('image');

        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang)
            {
                if ($lang=='en')
                {
                    if(isset($file))
                    {
                        $result = image_upload($file,'brand','image');
                        $data = GiftBrand::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'file_path'=>$result[1],'file_name'=>$result[3]]);
                    }
                    else
                    {
                    	$data = GiftBrand::where('id',$cate_id)->update(['name'=>$input['name'][$lang]]);
                    }                    
                }
                $dataLang = GiftBrandLang::where(['brand_id'=>$cate_id,'lang'=>$lang])->first();

                if(isset($dataLang))
                {
                   $dataLang = GiftBrandLang::where(['brand_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);

                }
                else
                {
                    $dataLang = new  GiftBrandLang;
                    $dataLang->brand_id = $cate_id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
            }
            $result['message'] = 'Brand updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Brand Can`t be updated.';
            $result['status'] = 0;
            return response()->json($result);           
        }
            
        // $input = $request->all();
        $inp = [
            'name'=>$request->brand_name,
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $result = image_upload($file,'brand');

            if ($result[0]==true){   
                $inp['file_path'] = $result[1];
                $inp['file_name'] = $result[3];
                // $inp['extension'] = $result[2];              
            } 
        }
        $brand = GiftBrand::findOrFail($id);

        if ($brand->update($inp)){
            $result['message'] = 'Brand updated successfully';
            $result['status'] = 1;

        } else {
            $result['message'] = 'Brand Can`t updated';
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
        Gate::authorize('Gift-Brand-delete');
        if(GiftBrand::findOrFail($id)->delete()){
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
        return Excel::download(new GiftBrandExport, 'Gift-Brand.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
    
}
