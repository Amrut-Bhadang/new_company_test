<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use File;
use App\User;
use App\Models\Restaurant;
use App\Models\MainCategory;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Models\Products;
use App\Models\AttributeValues;
use App\Models\AttributeValueLang;
use App\Models\AttributesLang;
use App\Models\Language;
use App\Models\ProductLang;
use App\Models\ProductImages;
use App\Models\ProductIngredients;
use App\Models\ProductAssignTOChef;
use App\Models\ProductIngredientLang;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DishExport;
use App\Imports\BulkImport;

use Carbon\Carbon;
class AttributeValueController extends Controller
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

        Gate::authorize('Attribute-section');
        $login_user_data = auth()->user();
        $columns = ['attributes_lang.name'];

        /*if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();

            $attribute_value=AttributeValues::select('attribute_values.*','attributes_lang.name as attributes_name','main_category.name as main_category_name','categories.name as category_name')
                    ->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')
                    ->join('main_category', 'main_category.id', '=', 'attribute_values.main_category_id')
                    ->join('categories', 'categories.id', '=', 'attribute_values.category_id')->where('attribute_values.added_by', $login_user_data->id);

        } else {
            $attribute_value=AttributeValues::select('attribute_values.*','attributes_lang.name as attributes_name','main_category.name as main_category_name','categories.name as category_name')
                    ->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')
                    ->join('main_category', 'main_category.id', '=', 'attribute_values.main_category_id')
                    ->join('categories', 'categories.id', '=', 'attribute_values.category_id');
        }*/

        $attribute_value=AttributeValues::select('attribute_values.*','attributes_lang.name as attributes_name','main_category.name as main_category_name','categories.name as category_name')
                    ->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')
                    ->join('main_category', 'main_category.id', '=', 'attribute_values.main_category_id')
                    ->join('categories', 'categories.id', '=', 'attribute_values.category_id');
        
        return Datatables::of($attribute_value)->editColumn('created_at', function ($attribute_value) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($attribute_value->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween(DB::raw('DATE(attribute_values.created_at)'), array($request->from_date, $request->to_date));
            }

            if ($request->has('main_category_id')) {
                $main_category_id = array_filter($request->main_category_id);
                if(count($main_category_id) > 0) {
                    $query->whereIn('attribute_values.main_category_id', $request->get('main_category_id'));
                }
            }

            if ($request->has('category_id')) {
                $category_id = array_filter($request->category_id);
                if(count($category_id) > 0) {
                    $query->whereIn('attribute_values.category_id', $request->get('category_id'));
                }
            }

            if ($request->has('attributes_lang_id')) {
                $attributes_lang_id = array_filter($request->attributes_lang_id);
                if(count($attributes_lang_id) > 0) {
                    $query->whereIn('attribute_values.attributes_lang_id', $request->get('attributes_lang_id'));
                }
            }
           
            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               // $query->having('attributes.name', 'like', "%{$search['value']}%");
               $query->having('attributes_lang.name', 'like', "%{$search['value']}%");
               $query->orHaving('main_category.name', 'like', "%{$search['value']}%");
               $query->orHaving('categories.name', 'like', "%{$search['value']}%");
            }            
        })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Attribute-section');
        $login_user_data = auth()->user();
        $data = array();
        $data['user_type'] = $login_user_data->type;
        $data['user_id'] = $login_user_data->id;
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['category']=Category::select('name','id')->where(['type'=>1, 'status'=>1])->get();
        $data['attributes'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where('attributes.status', 1)->get();

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','brand_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['main_category']=MainCategory::select('name','id')->where(['status'=>1, 'id'=>$restaurant_detail->main_category_id])->get();
            $data['restaurant_id'] = $restaurant_detail->id;
            $data['main_category_id'] = $restaurant_detail->main_category_id;

        } else {
            $data['restaurant_id'] = '';
            $data['main_category_id'] = '';
        }
        return view('attribute_value.listing', $data);
    }

    public function show_category($main_category_id, $category_id='') {

      if ($main_category_id) {
        $data['records'] = Category::select('id','name')->where(['status'=>1, 'type'=>1, 'main_category_id'=>$main_category_id])->get();
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      // echo "<pre>"; print_r($data['records']); die;
      $data['category_id'] = $category_id;
      return view('attribute_value.category',$data);

    }

    public function show_attributes($main_category_id, $category_id) {

      if ($main_category_id) {
        $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id','attributes_lang.is_color')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where('attributes.main_category_id', $main_category_id)->where('attributes.category_id', $category_id)->get();
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      return view('attribute_value.attribute_list',$data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Attribute-create');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['attributes_lang']=AttributesLang::select('attributes_lang.*')->join('attributes', 'attributes.id', '=', 'attributes_lang.attribute_id')->where(['status'=>1])->get();

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','brand_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['main_category']=MainCategory::select('name','id')->where(['status'=>1, 'id'=>$restaurant_detail->main_category_id])->get();
            $data['restaurant_id'] = $restaurant_detail->id;
            $data['main_category_id'] = $restaurant_detail->main_category_id;

        } else {
            $data['restaurant_id'] = '';
            $data['main_category_id'] = '';
        }
        return view('attribute_value.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Attribute-create');
         // validate

        $mesasge = [
            'attributes.en.required'=>'The Attribute(English) field is required.',
            'attributes.ar.required'=>'The Attribute(Arabic) field is required.',
          ];
          $this->validate($request, [
                'main_category_id' => 'required',
                'category_id' => 'required',
                'attributes_lang_id' => 'required',
                'attributes.en.*' => 'required',
                
          ],$mesasge);
        // create a new task based on Content tasks relationship
        $input = $request->all();
        $login_user_data = auth()->user();
        $added_by = $login_user_data->id;

        $checkAttributeValueExist = AttributeValues::select('id')->where(['main_category_id'=>$input['main_category_id'], 'category_id'=>$input['category_id'], 'attributes_lang_id'=>$input['attributes_lang_id']])->first();

        if ($checkAttributeValueExist) {
            $result['message'] = 'Attribute value already exist with same categories';
            $result['status'] = 0;

        } else {
            $lang = Language::pluck('lang')->toArray();
            $data = new AttributeValues;
            $data->main_category_id=$input['main_category_id'];
            $data->category_id=$input['category_id'];
            $data->attributes_lang_id=$input['attributes_lang_id'];
            $data->added_by = $added_by;
            
            if ($data->save()) {

                if ($request->input('attributes')) {

                    foreach($request->input("attributes")['en'] as $key => $attributesName) {

                        if ($attributesName) {
                            $dataLang = new AttributeValueLang();
                            $dataLang->attribute_value_id = $data->id;
                            $dataLang->name = $attributesName;
                            $dataLang->lang = $request->input("attributes")['ar'][$key];

                            if (isset($request->input("attributes")['color_code']) && $request->input("is_color") == 1) {
                                $dataLang->color_code = $request->input("attributes")['color_code'][$key];

                            } else {
                                $dataLang->color_code = null;
                            }
                            $dataLang->save();
                        }          
                    }
                }

                $result['message'] = 'Attribute value has been created';
                $result['status'] = 1;

            } else {
                $result['message'] = 'Attribute Can`t created';
                $result['status'] = 0;
            }
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
        Gate::authorize('Attribute-section');
        $attribute_value=AttributeValues::select('attribute_values.*','attributes_lang.name as attributes_name')
                ->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')
                ->where('attribute_values.id',$id)
                ->first();
                

        $data['attribute_value'] = $attribute_value;
        $data['attribute_value_langs'] = AttributeValueLang::select('name')
                                    ->where('attribute_value_id',$attribute_value->id)
                                    ->get();

        return view('attribute_value.view',$data);
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

        $attribute_value=AttributeValues::select('attribute_values.*','attributes_lang.name as attributes_name','attributes_lang.is_color')
                ->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')
                ->where('attribute_values.id',$id)
                ->first();

        $data['attribute_value'] = $attribute_value;
        $data['attribute_value_langs'] = AttributeValueLang::select('*')
                                    ->where('attribute_value_id',$attribute_value->id)
                                    ->get();

        $data['attributes_lang']=AttributesLang::select('attributes_lang.*')->join('attributes', 'attributes.id', '=', 'attributes_lang.attribute_id')->where(['status'=>1])->get();
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        return view('attribute_value.edit',$data);
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
            // 'attributes_lang_id' => 'required',
            'attributes.en.*' => 'required',
                
        ],$mesasge);
        // create a new task based on Content tasks relationship

        $input = $request->all();
        $data = AttributeValues::findOrFail($id);
        // $data->attributes_lang_id = $input['attributes_lang_id'];

        if ($data->save()) {

            if ($request->input('old_attributes')) {
                $old_attributes_ids = [];
                $i = 0;

                foreach($request->input("old_attributes")['en'] as $key => $oldAttributesName) {
                    $old_attributes_ids[] = $key;

                    if ($oldAttributesName) {
                        $dataLang = AttributeValueLang::where(['id'=>$key])->first();
                        $dataLang->attribute_value_id = $data->id;
                        $dataLang->name = $oldAttributesName[0];
                        $dataLang->lang = $request->input("old_attributes")['ar'][$key][0];

                        if (isset($request->input("old_attributes")['color_code'])) {
                            $dataLang->color_code = $request->input("old_attributes")['color_code'][$key][0];

                        } else {
                            $dataLang->color_code = null;
                        }
                        $dataLang->save();
                    }   
                    $i++;
                }

                if ($old_attributes_ids) {
                    AttributeValueLang::where('attribute_value_id', $data->id)->whereNotIn('id', $old_attributes_ids)->delete();
                }
            }

            if ($request->input('attributes')) {

                foreach($request->input("attributes")['en'] as $key => $attributesName) {

                    if ($attributesName) {
                        $dataLang = new AttributeValueLang();
                        $dataLang->attribute_value_id = $data->id;
                        $dataLang->name = $attributesName;
                        $dataLang->lang = $request->input("attributes")['ar'][$key];

                        if (isset($request->input("attributes")['color_code'])) {
                            $dataLang->color_code = $request->input("attributes")['color_code'][$key];

                        } else {
                            $dataLang->color_code = null;
                        }
                        $dataLang->save();
                    }          
                }
            }

            $result['message'] = 'Attribute values updated';
            $result['status'] = 1;

        } else {
            $result['message'] = 'Attribute values can`t updated';
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
        $details = AttributeValues::find($id); 
        if (!empty($details)) {

            if ($status == 'active') {
                $inp = ['status' => 1];
            } else {
                $inp = ['status' => 0];
            }

            $attribute = AttributeValues::where('id',$id)->update($inp);

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

    public function productImagesDelete(Request $request)
    {
        Gate::authorize('Product-section');
        $id = $request->segment(3);
        $details = ProductImages::find($id); 
        if(!empty($details)){ 
            if(ProductImages::findOrFail($id)->delete()){
                $result['message'] = 'Product Image is deleted successfully';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Product Image can`t be deleted!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild Images!!';
            $result['status'] = 0;
        }
        return response()->json($result);;
    }
    public function addMoreImages(Request $request, $id){
        Gate::authorize('Product-section');
        $data['products'] = Products::findOrFail($id);
        if ($request->file('addMoremultipalImage')) {
            $i = 1;
            foreach ($request->file("addMoremultipalImage") as $file) {
                $modelProductImages = new ProductImages();
                $extension  = $file->getClientOriginalExtension();
                $newFolder  = strtoupper(date('M') . date('Y')) . '/';
                $folderPath	=	public_path().'/uploads/product/'.$newFolder; 
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777, true);
                }
                $productImageName = time() . $i . '-product.' . $extension;
                $image = $newFolder . $productImageName;
                if ($file->move($folderPath, $productImageName)) {
                    $modelProductImages->image = $image;
                }
                $i++;
                $modelProductImages->product_id = $data['products']->id;
                $modelProductImages->save();
            }   
             
            $data['productImage'] = ProductImages::select('image','id')->where('product_id',$data['products']->id)->get();
            if($data['products']->id){
                $result['message'] = 'Product Images added successfully';
                $result['status'] = 1;

                $result['data'] = $data;
            }else{
                $result['message'] = 'Product can`t be Added!!';
                $result['status'] = 0;
            } 
        }else{
            $result['message'] = 'At least One Image is  required!!';
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

    public function import(){
        $data['restaurant'] = Restaurant::select('id','name')->get();
        $data['category'] = Category::select('id','name')->where(['status'=>1, 'type'=>1])->get();
        //dd($data['category']);
        return view('product.import',$data);
    }

    public function importData(Request $request) 
    {
        $input = $request->all();
        $catgory_id = '';
        $lang = Language::pluck('lang')->toArray();

        if (!empty($input['file']) && !empty($input['restaurant_id'])) {
            $imgRslt = file_upload($request->file('file'), 'product');
            $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];

            if (!empty($excelData)) {
                $n = 8;
                foreach ($excelData as $key => $product) {
                    $lang = Language::pluck('lang')->toArray();

                    if ($product['category_name'] && !empty($product['category_name'])) {
                        $cat = Category::select('id')->where(['name'=>$product['category_name']])->first();

                        if ($cat) {
                            $catgory_id = $cat->id;
                        } else {
                            foreach($lang as $lang) {

                                if ($lang=='en') {
                                    $categoryData = new Category;
                                    $categoryData->name = $product['category_name'];
                                    $categoryData->description = $product['category_name'];
                                    $categoryData->type = 1;
                                    $categoryData->status= 1;
                                    $categoryData->save();
                                    $catgory_id = $categoryData->id;
                                }
                                $dataLang = new CategoryLang;
                                $dataLang->category_id = $categoryData->id;
                                $dataLang->name = $product['category_name'];
                                $dataLang->description = $product['category_name'];
                                $dataLang->lang = $lang;
                                $dataLang->save();
                            }
                        }
                    }
                    $data = new  Products;

                    $data->name = $product['nameen'] ?? null;
                    $data->long_description = $product['descriptionen'] ?? null;
                    //$data->recipe_description = $product['recipe_descriptionen'] ?? null;
                    $data->products_type=$product['type'] ?? null;
                    //$data->serve=$product['serve'] ?? null;
                    $data->restaurant_id=$input['restaurant_id'] ?? null;

                    $data->admin_amount=$product['admin_price'];
                    $data->total_amount=$product['price'];
                    $data->video=$product['video_url'];
                    // $data->celebrity_id=$input['celebrity_id'];
                    
                    
                    $data->points=$product['kilo_points'];
                    $data->price=$product['price'];
                    $data->category_id=$catgory_id;
                    $data->is_active= 1;

                    if ($data->save()) {
                        //English
                        $dataLang = new  ProductLang;
                        $dataLang->product_id = $data->id;
                        $dataLang->name = $product['nameen'];
                        //$dataLang->recipe_description = $product['recipe_descriptionen'];
                        $dataLang->long_description = $product['descriptionen'];
                        $dataLang->lang = 'en';
                        $dataLang->save();

                        //Arabic
                        $dataLang = new  ProductLang;
                        $dataLang->product_id = $data->id;
                        $dataLang->name = $product['namear'];
                        //$dataLang->recipe_description = $product['recipe_descriptionar'];
                        $dataLang->long_description = $product['descriptionar'];
                        $dataLang->lang = 'ar';
                        $dataLang->save();
                    }

                    /*foreach($lang as $lang) {

                        if ($lang=='en') {

                            if ($request->file('image')) {
                                $file = $request->file('image');
                                $result = image_upload($file,'product','image');
                                if($result[0]==true){
                                    $data->main_image = $result[1];
                                }
                            }
                            
                        }
                        
                    }*/
                }
                return back();
            }
        } else {
            return back();
        }
        /*Excel::import(new BulkImport,request()->file('file'));
        return back();*/
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



}
