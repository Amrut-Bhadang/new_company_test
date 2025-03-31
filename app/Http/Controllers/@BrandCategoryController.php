<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\BrandCategory;
use App\Models\MainCategory;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File,DB;
use App\Models\BrandCategoryLang;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Imports\BulkImport;

class BrandCategoryController extends Controller
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
        Gate::authorize('Brand-section');
        $login_user_data = auth()->user();
        $columns = ['brand_category.name'];
        $category=BrandCategory::select('name','created_at','id','status');
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
        })->filter(function ($query) use ($request,$columns) {

    			if(!empty($request->from_date) && !empty($request->to_date))
    			{
    				$query->whereBetween(DB::raw('DATE(brand_category.created_at)'), array($request->from_date, $request->to_date));
    			}

          /*if ($request->has('main_category_id') && $request->main_category_id) {
              $main_category_id = array_filter($request->main_category_id);
              if(count($main_category_id) > 0) {
                  $query->whereIn('brand_category.main_category_id', $request->get('main_category_id'));
              }
          }*/

          if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('brand_category.name', 'like', "%{$search['value']}%");
            }
    		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Brand-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['user_id'] = $login_user_data->id;
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();

        /*if ($login_user_data->type == 4) {
          $restaurant_detail = Restaurant::select('name','id','user_id','brand_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
          $data['main_category']=MainCategory::select('name','id')->where(['status'=>1, 'id'=>$restaurant_detail->main_category_id])->get();
          $data['restaurant_id'] = $restaurant_detail->id;
          $data['main_category_id'] = $restaurant_detail->main_category_id;

        } else {
          $data['restaurant_id'] = '';
          $data['main_category_id'] = '';
        }*/
        // $data['main_category']=MainCategory::all();
        return view('brand_category.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Brand-edit');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['category'] = BrandCategory::findOrFail($id);
        $data['main_category']=MainCategory::all();

        /*if ($login_user_data->type == 4) {
          $restaurant_detail = Restaurant::select('name','id','user_id','brand_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
          $data['main_category']=MainCategory::select('name','id')->where(['status'=>1, 'id'=>$restaurant_detail->main_category_id])->get();
          $data['restaurant_id'] = $restaurant_detail->id;
          $data['main_category_id'] = $restaurant_detail->main_category_id;

        } else {
          $data['restaurant_id'] = '';
          $data['main_category_id'] = '';
        }*/
        return view('brand_category.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Brand-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Brand-create');
         // validate
        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            // 'description.en.required'=>'The description(English) field is required.',
            // 'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',

          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               // 'description.en'=>'required',
               // 'description.ar'=>'required',
               // 'main_category_id'=>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
        $input = $request->all();
        $login_user_data = auth()->user();
        $added_by = $login_user_data->id;

        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {
                    $data = new BrandCategory;
                    if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'brand');
                        if($result[0]==true){
                            // $data->image = $result[1];
                        }
                    }
                    $data->name = $input['name'][$lang];
                    // $data->description = $input['description'][$lang];
                    // $data->main_category_id = $input['main_category_id'];
                    // $data->added_by = $added_by;
                    $data->status= 1;
                    $data->save();
                }
                $dataLang = new  BrandCategoryLang;
                $dataLang->brand_category_id = $data->id;
                $dataLang->name = $input['name'][$lang];
                // $dataLang->description = $input['description'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
            }
            $result['message'] = 'Brand Category has been created';
            $result['status'] = 1;
            return response()->json($result);
        } catch (Exception $e){
            $result['message'] = 'Brand Category Can`t created';
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
        Gate::authorize('Brand-section');
        $data['category'] = BrandCategory::findOrFail($id);
        $data['main_category']=MainCategory::select('name')->where('id',$data['category']->main_category_id)->first();
        return view('category.view',$data);
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
        Gate::authorize('Brand-edit');
        $Category = BrandCategory::findOrFail($id);
    		return response()->json([
          'user' => $Category
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
        Gate::authorize('Brand-edit');
        // validate
		$mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            // 'description.en.required'=>'The description(English) field is required.',
            // 'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               // 'main_category_id'=>'required',
               // 'description.en'=>'required',
               // 'description.ar'=>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        
        $file = $request->file('image');
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang)
            {
                if($lang=='en')
                {
                    if(isset($file))
                    {
                        $result = image_upload($file,'category');
                        $data = BrandCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'image'=>$result[1]]);
                    }
                    else
                    {
                        $data = BrandCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang]]);
                    }                    
                }
                $dataLang = BrandCategoryLang::where(['brand_category_id'=>$cate_id,'lang'=>$lang])->first();

                if (isset($dataLang)) {
                  $dataLang = BrandCategoryLang::where(['brand_category_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);                                   

                } else {
                  $dataLang = new BrandCategoryLang;
                  $dataLang->brand_category_id = $cate_id;
                  $dataLang->name = $input['name'][$lang];
                  // $dataLang->description = $input['description'][$lang];
                  $dataLang->lang = $lang;
                  $dataLang->save();
                }
            }
            $result['message'] = 'Brand Category updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Brand Category Can`t be updated.';
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
        Gate::authorize('Brand-delete');
        if(BrandCategory::findOrFail($id)->delete()){
            $result['message'] = 'Category deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Category Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function changeStatus($id, $status)
    {
        $details = BrandCategory::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = BrandCategory::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Brand Category is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Brand Category is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Brand Category status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Brand-section');
        return Excel::download(new CategoryExport, 'category.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }

    public function import(){
      $login_user_data = auth()->user();
      $data['user_type'] = $login_user_data->type;
      $data['main_category'] = MainCategory::select('name','id')->where(['status'=>1])->get();

      if ($login_user_data->type == 4) {
        $restaurant_detail = Restaurant::select('name','id','user_id','brand_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1, 'id'=>$restaurant_detail->main_category_id])->get();
        $data['restaurant_id'] = $restaurant_detail->id;
        $data['main_category_id'] = $restaurant_detail->main_category_id;

      } else {
        $data['restaurant_id'] = '';
        $data['main_category_id'] = '';
      }
      return view('category.import',$data);
    }

    public function importData(Request $request) 
    {
        $input = $request->all();
        $lang = Language::pluck('lang')->toArray();

        if (!empty($input['file']) && !empty($input['main_category_id'])) {
            $imgRslt = file_upload($request->file('file'), 'category');
            $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];
            $login_user_data = auth()->user();
            $added_by = $login_user_data->id;

            if (!empty($excelData)) {
                $n = 8;
                foreach ($excelData as $key => $record) {
                    $lang = Language::pluck('lang')->toArray();

                    if ($record['nameen']) {
                      $cat = BrandCategory::select('id')->where(['name'=>$record['nameen'], 'main_category_id'=>$input['main_category_id']])->first();

                      if (!$cat) {
                        $data = new BrandCategory;
                        $data->name = $record['nameen'] ?? null;
                        $data->main_category_id = $input['main_category_id'];
                        $data->added_by = $added_by;
                        $data->type = 1;
                        $data->status= 1;

                        if ($data->save()) {
                            //English
                            $dataLang = new BrandCategoryLang;
                            $dataLang->category_id = $data->id;
                            $dataLang->name = $record['nameen'] ?? null;
                            $dataLang->lang = 'en';
                            $dataLang->save();

                            //Arabic
                            $dataLang = new BrandCategoryLang;
                            $dataLang->category_id = $data->id;
                            $dataLang->name = $record['namear'] ?? null;
                            $dataLang->lang = 'ar';
                            $dataLang->save();
                        }
                      }
                    }
                }
                return back();
            }
        } else {
            return back();
        }
        /*Excel::import(new BulkImport,request()->file('file'));
        return back();*/
    }
}
