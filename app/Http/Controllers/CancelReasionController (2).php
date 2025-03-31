<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\OrderCancelReasions;
use App\Models\Language;
use File,DB;
use Maatwebsite\Excel\Facades\Excel;

class CancelReasionController extends Controller
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
        // Gate::authorize('Gift-Brand-section');
        $columns = ['order_cancel_reasions.reasion'];

        $record=OrderCancelReasions::select('*');
        return Datatables::of($record)->editColumn('created_at', function ($record) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($record->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(order_cancel_reasions.created_at)'), array($request->from_date, $request->to_date));
			}

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('order_cancel_reasions.reasion', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        // Gate::authorize('Gift-Brand-section');
        return view('cancel_reasion.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Gift-Brand-edit');
        $data['brand'] = OrderCancelReasions::select('*')->where('id', $id)->first();
        return view('cancel_reasion.edit',$data);
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
            // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			// 'image.size'  => 'the file size is less than 5MB',
        ],$mesasge);

        $input = $request->all();  
	    try{
	        $lang = Language::pluck('lang')->toArray();
	        /*$data = new GiftBrand;
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
*/

            $data = new OrderCancelReasions;
            $data->reasion = $input['name']['en'];
            $data->reasion_ar = $input['name']['ar'];
            $data->save();
	    	$result['message'] = 'Reasion has been created';
            $result['status'] = 1;
            return response()->json($result);

	    } catch (Exception $e){
            $result['message'] = 'Reasion Can`t created';
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
        // Gate::authorize('Gift-Brand-section');
        $data['brand'] = OrderCancelReasions::findOrFail($id);
        return view('cancel_reasion.view',$data);
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
        // Gate::authorize('Gift-Brand-edit');
        $brand = OrderCancelReasions::findOrFail($id);
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
        // Gate::authorize('Gift-Brand-edit');
        // validate

        $mesasge = [
	        'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
	    ];
		$this->validate($request, [
            'name.en'=>'required|max:255',
            'name.ar'=>'required|max:255', 
            // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ],[
			// 'image.size'  => 'the file size is less than 5MB',
        ],$mesasge);

        $input = $request->all();
        $cate_id = $id;
        // $file = $request->file('image');

        try{
            $lang = Language::pluck('lang')->toArray();
            OrderCancelReasions::where('id',$cate_id)->update(['reasion'=>$input['name']['en'], 'reasion_ar'=>$input['name']['ar']]);
            /*foreach($lang as $lang)
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
            }*/
            $result['message'] = 'Reasion updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Reasion Can`t be updated.';
            $result['status'] = 0;
            return response()->json($result);           
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
