<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\MainCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File,DB;
use App\Models\MainCategoryLang;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MainCategoryExport;
use App\Imports\BulkImport;

class MainCategoryController extends Controller
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
        Gate::authorize('Main-Category-section');
        $columns = ['main_category.name'];
        // dd($request->all());
        $category=MainCategory::select('name','position','total_stores','created_at','id','status','image');
        return Datatables::of($category)->editColumn('created_at', function ($category) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($category->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y');
        })->filter(function ($query) use ($request,$columns) {

    			if(!empty($request->from_date) && !empty($request->to_date))
    			{
    				$query->whereBetween(DB::raw('DATE(main_category.created_at)'), array($request->from_date, $request->to_date));
    			}

          if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('main_category.name', 'like', "%{$search['value']}%");
            }
    		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Main-Category-section');
        return view('main_category.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Main-Category-edit');
        $data['category'] = MainCategory::findOrFail($id);
        return view('main_category.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Main-Category-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Main-Category-create');
         // validate
        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'The file size is less than 5MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'position' => 'required|unique:main_category',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
        $input = $request->all();  
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {
                    $data = new MainCategory;
                    if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'category');
                        if($result[0]==true){
                            $data->image = $result[1];
                        }
                    }
                    $data->name = $input['name'][$lang];
                    $data->position = $input['position'];
                    $data->status= 1;
                    $data->save();
                }
                $dataLang = new  MainCategoryLang;
                $dataLang->main_cat_id = $data->id;
                $dataLang->name = $input['name'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
            }
            $result['message'] = 'Service has been created';
            $result['status'] = 1;
            return response()->json($result);
        } catch (Exception $e){
            $result['message'] = 'Service Can`t created';
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
        Gate::authorize('Main-Category-section');
        $data['category'] = MainCategory::findOrFail($id);
        //dd($data);
        return view('main_category.view',$data);
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
        Gate::authorize('Main-Category-edit');
        $Category = MainCategory::findOrFail($id);
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
        Gate::authorize('Main-Category-edit');
        // validate
		$mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'position' => 'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
            
        $input = $request->all();
        $file = $request->file('image');
        $cate_id = $id;
        try{

            $checkPositionExist = MainCategory::where(['position'=>$input['position']])->where('id', '!=', $cate_id)->first();

            if ($checkPositionExist) {
              $result['message'] = 'The position has already been taken.';
              $result['status'] = 0;

            } else {
              $lang = Language::pluck('lang')->toArray();
              foreach($lang as $lang)
              {
                  if($lang=='en')
                  {
                    if(isset($file))
                      {
                          $result = image_upload($file,'category');
                          $data = MainCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'position'=>$input['position'],'image'=>$result[1]]);  
                      }
                      else
                      {
                          $data = MainCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'position'=>$input['position']]);
                      }
                                        
                  }
                  $dataLang = MainCategoryLang::where(['main_cat_id'=>$cate_id,'lang'=>$lang])->first();
                  if(isset($dataLang))
                  {
                     $dataLang = MainCategoryLang::where(['main_cat_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);                                   
                  }
                  else
                  {
                      $dataLang = new  MainCategoryLang;
                      $dataLang->main_cat_id = $cate_id;
                      $dataLang->name = $input['name'][$lang];
                      $dataLang->lang = $lang;
                      $dataLang->save();
                  }
              }
              $result['message'] = 'Service updated successfully.';
              $result['status'] = 1;
            }
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Service Can`t be updated.';
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
        Gate::authorize('Main-Category-delete');
        if(MainCategory::findOrFail($id)->delete()){
            $result['message'] = 'Service deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Service Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {
        $details = MainCategory::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = MainCategory::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Service is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Service is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Service status can`t be updated!!';
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
        Gate::authorize('Users-section');
        return Excel::download(new MainCategoryExport, 'main_category.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
