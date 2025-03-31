<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\GiftCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File,DB;
use App\Models\GiftCategoryLang;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftCategoriesExport;
use App\Imports\BulkImport;

class GiftCategoriesController extends Controller
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
        Gate::authorize('Gift-Category-section');
        $columns = ['gift_categories.name'];

        $category=GiftCategory::select('name','created_at','id','status','type');
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
				$query->whereBetween(DB::raw('DATE(gift_categories.created_at)'), array($request->from_date, $request->to_date));
			}

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('gift_categories.name', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Gift-Category-section');
        return view('gift_category.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Gift-Category-edit');
        $data['category'] = GiftCategory::findOrFail($id);
        return view('gift_category.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Gift-Category-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Gift-Category-create');
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
               'image' => 'image|required|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
        $input = $request->all();  
        try{
            $lang = Language::pluck('lang')->toArray();
            $category = GiftCategory::where('name', $input['name']['en'])->first();

            if (!$category) {

                foreach($lang as $lang){
                    if($lang=='en')
                    {
                        $data = new GiftCategory;
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file,'category');
                            if($result[0]==true){
                                $data->image = $result[1];
                            }
                        }
                        $data->name = $input['name'][$lang];
                        $data->type = 2;
                        // $data->type= 1;
                        $data->status= 1;
                        $data->save();
                    }
                    $dataLang = new  GiftCategoryLang;
                    $dataLang->gift_category_id = $data->id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
                $result['message'] = 'Gift Category has been created';
                $result['status'] = 1;
                return response()->json($result);

            } else {
                $result['message'] = 'Gift Category already exist.';
                $result['status'] = 0;
                return response()->json($result);
            }
        } catch (Exception $e){
            $result['message'] = 'Gift Category Can`t created';
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
        Gate::authorize('Gift-Category-section');
        $data['category'] = GiftCategory::findOrFail($id);
        return view('gift_category.view',$data);
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
        Gate::authorize('Gift-Category-edit');
        $Category = GiftCategory::findOrFail($id);
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
        Gate::authorize('Gift-Category-edit');
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
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        
        $file = $request->file('image');
        try{
            $lang = Language::pluck('lang')->toArray();
            $category = GiftCategory::where('id','!=',$cate_id)->where('name', $input['name']['en'])->first();

            if (!$category) {
                foreach($lang as $lang)
                {
                    if($lang=='en')
                    {
                        if(isset($file))
                        {
                            $result = image_upload($file,'category');
                            $data = GiftCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'image'=>$result[1]]);
                        }
                        else
                        {
                            $data = GiftCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang]]);
                        }                    
                    }
                    $dataLang = GiftCategoryLang::where(['gift_category_id'=>$cate_id,'lang'=>$lang])->first();
                    if(isset($dataLang))
                    {
                       $dataLang = GiftCategoryLang::where(['gift_category_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);                                   
                    }
                    else
                    {
                        $dataLang = new  GiftCategoryLang;
                        $dataLang->gift_category_id = $cate_id;
                        $dataLang->name = $input['name'][$lang];
                        $dataLang->lang = $lang;
                        $dataLang->save();
                    }
                }
                $result['message'] = 'Gift Category updated successfully.';
                $result['status'] = 1;
                return response()->json($result);
            } else {
                $result['message'] = 'Gift Category already exist.';
                $result['status'] = 0;
                return response()->json($result);
            }
        }
        catch (Exception $e)
        {
            $result['message'] = 'Gift Category Can`t be updated.';
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
        Gate::authorize('Gift-Category-delete');
        if(GiftCategory::findOrFail($id)->delete()){
            $result['message'] = 'Gift Category deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Gift Category Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function changeStatus($id, $status)
    {
        $details = GiftCategory::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = GiftCategory::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Gift Category is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Gift Category is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Gift Category status can`t be updated!!';
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
        return Excel::download(new GiftCategoriesExport, 'giftcategory.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
