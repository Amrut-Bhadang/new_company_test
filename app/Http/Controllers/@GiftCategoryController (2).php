<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\GiftCategory;
use App\Models\GiftSubCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File;
use App\Models\GiftSubCategoryLang;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftCategoryExport;
use App\Imports\BulkImport;
use DB;

class GiftCategoryController extends Controller
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
        Gate::authorize('GiftCategory-section');
        $columns = ['gift_sub_categories.name'];

        $category=GiftSubCategory::select('name','created_at','id','status');
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
  				$query->whereBetween(DB::raw('DATE(gift_sub_categories.created_at)'), array($request->from_date, $request->to_date));
  			}

        if (!empty($request->get('search'))) {
           $search = $request->get('search');
           $query->having('gift_sub_categories.name', 'like', "%{$search['value']}%");
        }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
      Gate::authorize('GiftCategory-section');
      $data['categories_list'] = GiftCategory::select('name','id')->get();
      return view('giftcategory.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('GiftCategory-edit');
        $data['categories_list'] = GiftCategory::select('name','id')->get();
        $data['category'] = GiftSubCategory::findOrFail($id);
        return view('giftcategory.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('GiftCategory-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('GiftCategory-create');
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
               'category_id'=>'required',
               'image' => 'image|required|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
        $input = $request->all();  
        try{
            $lang = Language::pluck('lang')->toArray();
            $category = GiftSubCategory::where('category_id', $input['category_id'])->where('name', $input['name']['en'])->first();

            if (!$category) {

              foreach($lang as $lang){
                  if($lang=='en')
                  {
                      $data = new GiftSubCategory;
                      if ($request->file('image')) {
                          $file = $request->file('image');
                          $result = image_upload($file,'gift-category');
                          if($result[0]==true){
                              $data->image = $result[1];
                          }
                      }
                      $data->name = $input['name'][$lang];
                      // $data->description = $input['description'][$lang];
                      $data->category_id= $input['category_id'];
                      $data->status= 1;
                      $data->save();
                  }
                  $dataLang = new  GiftSubCategoryLang;
                  $dataLang->gift_sub_category_id = $data->id;
                  $dataLang->name = $input['name'][$lang];
                  // $dataLang->description = $input['description'][$lang];
                  $dataLang->lang = $lang;
                  $dataLang->save();
              }
              $result['message'] = 'Gift Sub-category has been created';
              $result['status'] = 1;
              return response()->json($result);

            } else {
                $result['message'] = 'Gift Sub-category already exist.';
                $result['status'] = 0;
                return response()->json($result);
            }
        } catch (Exception $e){
            $result['message'] = 'Gift Sub-category Can`t created';
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
        $data['category'] = GiftSubCategory::findOrFail($id);
        // dd($data['category']->toArray());
        return view('giftcategory.view',$data);
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
        Gate::authorize('GiftCategory-edit');
        $Category = GiftSubCategory::findOrFail($id);
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
        Gate::authorize('GiftCategory-edit');
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
               'category_id'=>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        
        $file = $request->file('image');
        try{
            $lang = Language::pluck('lang')->toArray();
            $category = GiftSubCategory::where('id','!=',$cate_id)->where('category_id', $input['category_id'])->where('name', $input['name']['en'])->first();

            if (!$category) {

              foreach($lang as $lang)
              {
                  if($lang=='en')
                  {
                      if(isset($file))
                      {
                          $result = image_upload($file,'gift-category');
                          $data = GiftSubCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'category_id'=>$input['category_id'],'image'=>$result[1]]);
                      }
                      else
                      {
                          $data = GiftSubCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'category_id'=>$input['category_id']]);
                      }                    
                  }
                  $dataLang = GiftSubCategoryLang::where(['gift_sub_category_id'=>$cate_id,'lang'=>$lang])->first();
                  if(isset($dataLang))
                  {
                     $dataLang = GiftSubCategoryLang::where(['gift_sub_category_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);                                   
                  }
                  else
                  {
                      $dataLang = new  GiftSubCategoryLang;
                      $dataLang->gift_sub_category_id = $cate_id;
                      $dataLang->name = $input['name'][$lang];
                      // $dataLang->description = $input['description'][$lang];
                      $dataLang->lang = $lang;
                      $dataLang->save();
                  }
              }
              $result['message'] = 'Gift Sub-category updated successfully.';
              $result['status'] = 1;
              return response()->json($result);

            } else {
                $result['message'] = 'Gift Sub-category already exist.';
                $result['status'] = 0;
                return response()->json($result);
            }
        }
        catch (Exception $e)
        {
            $result['message'] = 'Gift Sub-category Can`t be updated.';
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
        //
        Gate::authorize('GiftCategory-delete');
        return GiftSubCategory::findOrFail($id)->delete();
    }
    public function changeStatus($id, $status)
    {

        $details = GiftSubCategory::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = GiftSubCategory::findOrFail($id);
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
        return Excel::download(new GiftCategoryExport, 'Gift_Categroy.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
