<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use File;
use App\User;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Models\MainCategory;
use App\Models\Products;
use App\Models\GiftAttributes;
use App\Models\GiftAttributesLang;
use App\Models\GiftCategory;
use App\Models\GiftCategoryLang;
use App\Models\GiftSubCategory;
use App\Models\GiftSubCategoryLang;
use App\Models\Language;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DishExport;
use App\Imports\BulkImport;

use Carbon\Carbon;
class GiftAttributeController extends Controller
{
    public function __construct() {
		$this->middleware('auth');
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return $request['from_date'];

        Gate::authorize('Gift-Attribute-section');
        $login_user_data = auth()->user();
        $columns = ['gift_categories.name'];

        $attributes=GiftAttributes::select('attributes.*','gift_categories.name as category_name','gift_sub_categories.name as sub_category_name')
                ->join('gift_categories', 'gift_categories.id', '=', 'attributes.category_id')
                ->join('gift_sub_categories', 'gift_sub_categories.id', '=', 'attributes.sub_category_id');
        return Datatables::of($attributes)->editColumn('created_at', function ($attributes) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($attributes->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(attributes.created_at)'), array($request->from_date, $request->to_date));
            }

            if ($request->has('category_id')) {
                $category_id = array_filter($request->category_id);
                if(count($category_id) > 0) {
                    $query->whereIn('attributes.category_id', $request->get('category_id'));
                }
            }

            if ($request->has('sub_category_id')) {
                $sub_category_id = array_filter($request->sub_category_id);
                if(count($sub_category_id) > 0) {
                    $query->whereIn('attributes.sub_category_id', $request->get('sub_category_id'));
                }
            }
           
            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('gift_categories.name', 'like', "%{$search['value']}%");
               $query->orHaving('gift_sub_categories.name', 'like', "%{$search['value']}%");
            }            
        })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Gift-Attribute-section');
        $login_user_data = auth()->user();
        $data = array();
        $data['user_type'] = $login_user_data->type;
        $data['category']=GiftCategory::select('name','id')->where(['status'=>1])->get();
        $data['sub_category']=GiftSubCategory::select('name','id')->where(['status'=>1])->get();
        $data['restaurant_id'] = '';
        $data['category_id'] = '';
        return view('gift_attribute.listing', $data);
    }

    public function show_category($category_id, $sub_category_id='') {

      if ($category_id) {
        $data['records'] = GiftSubCategory::select('id','name')->where(['status'=>1, 'category_id'=>$category_id])->get();
        $data['category_id'] = $category_id;

      } else {
        $data['records'] = array();
        $data['category_id'] = '';
      }     

      $data['sub_category_id'] = $sub_category_id;
      return view('gift_attribute.sub_category',$data);

    }

    public function show_category_byIds($category_id) {

        $category_ids = explode(",",$category_id);

        if ($category_id) {
            $data['records'] = GiftSubCategory::select('id','name')->where(['status'=>1])->whereIn('category_id', $category_ids)->get();
            $data['category_id'] = $category_id;

        } else {
            $data['records'] = array();
            $data['category_id'] = '';
        }
        return view('gift_attribute.filtered_category',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Gift-Attribute-create');
        $login_user_data = auth()->user();
        $data['category'] = GiftCategory::select('name','id')->where(['status'=>1])->get();
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['restaurant_id'] = '';
        $data['category_id'] = '';
        return view('gift_attribute.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Gift-Attribute-create');
         // validate

        $mesasge = [
            'attributes.en.required'=>'The Attribute(English) field is required.',
            'attributes.ar.required'=>'The Attribute(Arabic) field is required.',
          ];
          $this->validate($request, [
                'category_id' => 'required',
                'sub_category_id' => 'required',
                'attributes.en.*' => 'required',
                
          ],$mesasge);
        // create a new task based on Content tasks relationship
        $input = $request->all();
        $login_user_data = auth()->user();
        $added_by = $login_user_data->id;

        $lang = Language::pluck('lang')->toArray();
        $checkAttributeExist = GiftAttributes::select('id')->where(['category_id'=>$input['category_id'], 'sub_category_id'=>$input['sub_category_id']])->first();

        if (!$checkAttributeExist) {
            $data = new GiftAttributes;
            $data->category_id=$input['category_id'];
            $data->sub_category_id=$input['sub_category_id'];
            $data->added_by = $added_by;

            if ($data->save()) {

                if ($request->input('attributes')) {

                    foreach($request->input("attributes")['en'] as $key => $attributesName) {

                        if ($attributesName) {
                            $dataLang = new GiftAttributesLang();
                            $dataLang->attribute_id = $data->id;
                            $dataLang->topping_choose = $request->input("attributes")['topping_choose'][$key];
                            $dataLang->is_color = $request->input("attributes")['is_color'][$key];
                            $dataLang->name = $attributesName;
                            $dataLang->lang = $request->input("attributes")['ar'][$key];
                            $dataLang->save();
                        }          
                    }
                }

                $result['message'] = 'Attribute has been created';
                $result['status'] = 1;

            } else {
                $result['message'] = 'Attribute Can`t created';
                $result['status'] = 0;
            }
        } else {
            $result['message'] = 'Attribute already exist with same categories.';
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
        Gate::authorize('Gift-Attribute-section');
        $attributes=GiftAttributes::select('attributes.*','gift_categories.name as category_name','gift_sub_categories.name as sub_category_name')
                ->join('gift_categories', 'gift_categories.id', '=', 'attributes.category_id')
                ->join('gift_sub_categories', 'gift_sub_categories.id', '=', 'attributes.sub_category_id')
                ->where('attributes.id',$id)
                ->first();

        $data['attributes'] = $attributes;
        $data['attribute_names'] = GiftAttributesLang::select('name')
                                    ->where('attribute_id',$attributes->id)
                                    ->get();

        return view('gift_attribute.view',$data);
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
        Gate::authorize('Product-edit');
        $Product = Products::findOrFail($id);
		return response()->json([
            'product' => $Product
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit_frontend($id)
    {
        Gate::authorize('Product-edit');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;

        $attributes=GiftAttributes::select('attributes.*','gift_categories.name as category_name','gift_sub_categories.name as sub_category_name')
                ->join('gift_categories', 'gift_categories.id', '=', 'attributes.category_id')
                ->join('gift_sub_categories', 'gift_sub_categories.id', '=', 'attributes.sub_category_id')
                ->where('attributes.id',$id)
                ->first();

        $data['attributes'] = $attributes;
        $data['attribute_names'] = GiftAttributesLang::select('*')
                                    ->where('attribute_id',$attributes->id)
                                    ->get();
        $data['category']=GiftCategory::select('name','id')->where(['status'=>1])->get();
        return view('gift_attribute.edit',$data);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('Product-edit');
        // validate
        $mesasge = [
            'attributes.en.required'=>'The Attribute(English) field is required.',
            'attributes.ar.required'=>'The Attribute(Arabic) field is required.',
        ];
        $this->validate($request, [
            // 'main_category_id' => 'required',
            'attributes.en.*' => 'required',
                
        ],$mesasge);
        // create a new task based on Content tasks relationship

        $input = $request->all();
        $data = GiftAttributes::findOrFail($id);
        // $data->category_id = $input['main_category_id'];

        if ($data->save()) {
            // dd($request->input("old_attributes"));

            if ($request->input('old_attributes')) {
                $old_attributes_ids = [];
                $i = 0;

                foreach($request->input("old_attributes")['en'] as $key => $oldAttributesName) {
                    $old_attributes_ids[] = $key;

                    if ($oldAttributesName) {
                        $dataLang = GiftAttributesLang::where(['id'=>$key])->first();
                        $dataLang->attribute_id = $data->id;
                        $dataLang->topping_choose = $request->input("old_attributes")['topping_choose'][$key][0];
                        $dataLang->is_color = $request->input("old_attributes")['is_color'][$key][0];
                        $dataLang->name = $oldAttributesName[0];
                        $dataLang->lang = $request->input("old_attributes")['ar'][$key][0];
                        $dataLang->save();
                    }   
                    $i++;
                }

                if ($old_attributes_ids) {
                    // echo "<pre>"; print_r($old_attributes_ids); die;
                    GiftAttributesLang::where('attribute_id', $data->id)->whereNotIn('id', $old_attributes_ids)->delete();
                }
            }

            if ($request->input('attributes')) {
                // GiftAttributesLang::where('attribute_id',$id)->delete();

                foreach($request->input("attributes")['en'] as $key => $attributesName) {

                    if ($attributesName) {
                        $dataLang = new GiftAttributesLang();
                        $dataLang->attribute_id = $data->id;
                        $dataLang->topping_choose = $request->input("attributes")['topping_choose'][$key];
                        $dataLang->is_color = $request->input("attributes")['is_color'][$key];
                        $dataLang->name = $attributesName;
                        $dataLang->lang = $request->input("attributes")['ar'][$key];
                        $dataLang->save();
                    }          
                }
            }

            $result['message'] = 'Attribute updated';
            $result['status'] = 1;

        } else {
            $result['message'] = 'Attribute Can`t created';
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
        Gate::authorize('Product-delete');
        $details = Products::find($id); 
        if(!empty($details)){
            $eventModel	=	Products::where('id',$id)->update(array('is_deleted'=>1,'deleted_at'=>date("Y-m-d h:i:s")));
            if($eventModel){
                $result['message'] = 'Product is deleted successfully';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Product can`t be deleted!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {
        $details = GiftAttributes::find($id); 
        if (!empty($details)) {

            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }

            $attribute = GiftAttributes::where('id',$id)->update($inp);

            if($attribute){
                if($status == 'active'){
                    $result['message'] = 'Attribute is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Attribute is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Attribute status can`t be updated!!';
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
        return Excel::download(new DishExport, 'Dish.csv');
    }

    public function fetch_data(Request $request)
    {
             
       if($request->from_date != '' && $request->to_date != '') {
            $from = Carbon::parse($request->from_date)->toDateString();
            $to = Carbon::parse($request->to_date)->toDateString();

           $data = DB::table('products')->whereBetween('created_at',[$from, $to])->get();
       }else {
           $data = DB::table('products')->orderBy('created_at', 'desc')->get();
       }

       return json_encode($data);
    }

    public function import(){
      $data['main_category'] = GiftCategory::select('name','id')->where(['status'=>1])->get();
      return view('gift_attribute.import',$data);
    }

    public function importData(Request $request) 
    {
        $input = $request->all();
        $catgory_id = '';
        $lang = Language::pluck('lang')->toArray();

        if (!empty($input['file']) && !empty($input['main_category_id'])) {
            $imgRslt = file_upload($request->file('file'), 'attribute');
            $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];

            if (!empty($excelData)) {
                $n = 8;
                foreach ($excelData as $key => $record) {
                    $lang = Language::pluck('lang')->toArray();

                    if ($record['category_name'] && !empty($record['category_name'])) {
                        $cat = GiftSubCategory::select('id')->where(['name'=>$record['category_name'], 'main_category_id'=>$input['main_category_id']])->first();

                        if ($cat) {
                            $catgory_id = $cat->id;
                        } else {
                            foreach($lang as $lang) {

                                if ($lang=='en') {
                                    $categoryData = new Category;
                                    $categoryData->main_category_id = $input['main_category_id'];
                                    $categoryData->name = $record['category_name'];
                                    $categoryData->description = $record['category_name'];
                                    $categoryData->type = 1;
                                    $categoryData->status= 1;
                                    $categoryData->save();

                                    $catgory_id = $categoryData->id;
                                }
                                $dataLang = new CategoryLang;
                                $dataLang->category_id = $categoryData->id;
                                $dataLang->name = $record['category_name'];
                                $dataLang->description = $record['category_name'];
                                $dataLang->lang = $lang;
                                $dataLang->save();
                            }
                        }
                    }

                    if ($record['nameen']) {

                        $checkAttributeExist = GiftAttributes::select('id')->where(['main_category_id'=>$input['main_category_id'], 'category_id'=>$catgory_id])->first();

                        if (!$checkAttributeExist) {
                            $data = new GiftAttributes;
                            $data->main_category_id = $input['main_category_id'];
                            $data->category_id = $catgory_id;

                            if ($data->save()) {
                                //English
                                $dataLang = new GiftAttributesLang();
                                $dataLang->attribute_id = $data->id;
                                $dataLang->topping_choose = isset($record['selection_type']) && $record['selection_type'] == 'Single' ? 0 : 1;
                                $dataLang->is_color = isset($record['is_color']) && $record['is_color'] == 'No' ? 0 : 1;
                                $dataLang->name = $record['nameen'];
                                $dataLang->lang = $record['namear'];
                                $dataLang->save();
                            }

                        } else {
                            //English
                            $dataLang = new GiftAttributesLang();
                            $dataLang->attribute_id = $checkAttributeExist->id;
                            $dataLang->topping_choose = isset($record['selection_type']) && $record['selection_type'] == 'Single' ? 0 : 1;
                            $dataLang->is_color = isset($record['is_color']) && $record['is_color'] == 'No' ? 0 : 1;
                            $dataLang->name = $record['nameen'];
                            $dataLang->lang = $record['namear'];
                            $dataLang->save();
                        }
                    }
                }
                return back();
            }
        } else {
            return back();
        }
    }

}
