<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Info;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File,DB;
use App\Models\InfoLang;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Imports\BulkImport;

class InfoController extends Controller
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
        Gate::authorize('Info-section');
        $category=Info::select('name','created_at','id','status','description','slug');
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
        })->filterColumn('created_at',function ($query) use ($request) {
			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(categories.created_at)'), array($request->from_date, $request->to_date));
			}
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Info-section');
        return view('info.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Info-edit');
        $data['category'] = Info::findOrFail($id);
        return view('info.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Category-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Category-create');
         // validate
        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'description.en'=>'required',
               'description.ar'=>'required',
          ],$mesasge);
        $input = $request->all();  
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {
                    $data = new Category;
                    if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'category');
                        if($result[0]==true){
                            $data->image = $result[1];
                        }
                    }
                    $data->name = $input['name'][$lang];
                    $data->description = $input['description'][$lang];
                    $data->type = 1;
                    // $data->type= 1;
                    $data->status= 1;
                    $data->save();
                }
                $dataLang = new  CategoryLang;
                $dataLang->category_id = $data->id;
                $dataLang->name = $input['name'][$lang];
                $dataLang->description = $input['description'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
            }
            $result['message'] = 'Food Category has been created';
            $result['status'] = 1;
            return response()->json($result);
        } catch (Exception $e){
            $result['message'] = 'Food Category Can`t created';
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
        Gate::authorize('Category-section');
        $data['category'] = Category::findOrFail($id);
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
        Gate::authorize('Category-edit');
        $Category = Category::findOrFail($id);
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
        Gate::authorize('Category-edit');
        // validate
		$mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 2MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'description.en'=>'required',
               'description.ar'=>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
                        $data = Category::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang],'image'=>$result[1]]);
                    }
                    else
                    {
                        $data = Category::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang]]);
                    }                    
                }
                $dataLang = CategoryLang::where(['category_id'=>$cate_id,'lang'=>$lang])->first();
                if(isset($dataLang))
                {
                   $dataLang = CategoryLang::where(['category_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang]]);                                   
                }
                else
                {
                    $dataLang = new  CategoryLang;
                    $dataLang->category_id = $cate_id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->description = $input['description'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
            }
            $result['message'] = 'Food Category updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Food Category Can`t be updated.';
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
        Gate::authorize('Category-delete');
        if(Category::findOrFail($id)->delete()){
            $result['message'] = 'Food Category deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Food Category Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function changeStatus($id, $status)
    {
        $details = Category::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = Category::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Food Category is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Food Category is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Food Category status can`t be updated!!';
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
        return Excel::download(new CategoryExport, 'category.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
