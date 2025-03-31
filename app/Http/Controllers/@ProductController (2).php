<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use File;
use App\User;
use App\Models\Restaurant;
use App\Models\RestaurantLang;
use App\Models\RestaurantMode;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Models\Products;
use App\Models\Brand;
use App\Models\BrandLang;
use App\Models\Language;
use App\Models\ProductLang;
use App\Models\ProductImages;
use App\Models\ProductIngredients;
use App\Models\ProductAssignTOChef;
use App\Models\ProductIngredientLang;
use App\Models\PanelNotifications;
use App\Models\MainCategory;
use App\Models\ProductTags;
use App\Models\Attributes;
use App\Models\ZipImages;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DishExport;
use App\Exports\ImportDataToBeExport;
use App\Imports\BulkImport;
use App\Models\AttributeValues;
use App\Models\AttributeValueLang;
use App\Models\AttributesLang;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
use App\Models\Topping;
use App\Models\Rating;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function __construct() {
		$this->middleware('auth');
        changeRestaurantStatus();
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        // return $request['from_date'];

        Gate::authorize('Product-section');
        $login_user_data = auth()->user();
        $columns = ['products.name','categories.name','products.products_type'];

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $Product=Products::select('products.*','categories.name as category_name','main_category.name as main_category_name')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->leftJoin('main_category', 'main_category.id', '=', 'categories.main_category_id')
                    ->where('products.restaurant_id', $restaurant_detail->id)
                    ->where('products.is_deleted',0)
                    ->groupBy('products.id');
            // dd($Product->toArray());

        } else {
            $Product=Products::select('products.*','categories.name as category_name','main_category.name as main_category_name')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->leftJoin('main_category', 'main_category.id', '=', 'categories.main_category_id')
                    ->where('products.is_deleted',0)
                    ->groupBy('products.id');
                    // dd($Product->toArray());
        }
        
        return Datatables::of($Product)->editColumn('created_at', function ($Product) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($Product->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

            if(!empty($request->from_date) && !empty($request->to_date))
            {
                $query->whereBetween(DB::raw('DATE(products.created_at)'), array($request->from_date, $request->to_date));
            }

            if ($request->has('main_category_id')) {

                if ($request->main_category_id) {
                    $main_category_id = array_filter($request->main_category_id);
                    if(count($main_category_id) > 0) {
                        $query->whereIn('main_category.id', $request->get('main_category_id'));
                    }
                }
            }

            if ($request->has('category_id')) {

                if ($request->category_id) {
                    $category_id = array_filter($request->category_id);
                    if(count($category_id) > 0) {
                        $query->whereIn('products.category_id', $request->get('category_id'));
                    }
                }
            }

            if ($request->has('restaurant_id')) {

                if($request->get('restaurant_id')) {
                    $query->where('products.restaurant_id', $request->get('restaurant_id'));
                }
            }
           
            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('products.name', 'like', "%{$search['value']}%");
               $query->orHaving('categories.name', 'like', "%{$search['value']}%");
            }

            
        })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Product-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['category']=Category::select('name','id')->where(['type'=>1, 'status'=>1])->get();
        $data['restaurants'] = Restaurant::select('name','id','user_id')->where(['status'=>1])->get();
        // $data['chef']=User::select('name','id')->where(['type'=>2,'status'=>1])->get();
        // $data['celebrity']=User::select('name','id')->where(['type'=>3,'status'=>1])->get();
        return view('product.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Product-edit');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['restaurants'] = Restaurant::select('name','id','user_id')->where(['status'=>1])->get();
        $data['product'] = Products::findOrFail($id);
        $data['category']=Category::select('name','id')->where(['type'=>1, 'status'=>1])->get();
        $data['chef']=User::select('name','id')->where(['type'=>2,'status'=>1])->get();
        $data['celebrity']=User::select('name','id')->where(['type'=>3,'status'=>1])->get();
        $productAssign= ProductAssignTOChef::select('chef_id','id')->where('product_id',$data['product']->id)->get();
        $data['productIngredients'] = ProductIngredients::select('name','id')->where('product_id',$data['product']->id)->get();
        $data['product_attributes'] = ProductAttributes::select('product_attributes.*', 'attributes_lang.name as attribute_name')->where(['product_id' => $id])->join('attributes_lang', 'attributes_lang.id', '=', 'product_attributes.attributes_lang_id')->with('AttributeValues')->get();
        $data['attributes_lang']=AttributesLang::select('attributes_lang.*')->join('attributes', 'attributes.id', '=', 'attributes_lang.attribute_id')->where(['status'=>1])->get();
        $data['selected_attributes_lang']=ProductAttributes::where(['product_id' => $id])->pluck('attributes_lang_id')->toArray();

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','main_category_id','brand_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['restaurant_id'] = $restaurant_detail->id;
            $data['brand_id'] = $restaurant_detail->brand_id;
            $data['main_category_id'] = $restaurant_detail->main_category_id;

        } else {
            $data['restaurant_id'] = '';
            $data['brand_id'] = '';
            $data['main_category_id'] = '';
        }

        if ($data['product_attributes']) {

            foreach ($data['product_attributes'] as $key => $value) {
                $value->selected_attr_values = ProductAttributeValues::where(['product_attributes_id' => $value->id])->pluck('attribute_value_lang_id')->toArray();
                $value->attributeOption = AttributeValueLang::select('attribute_value_lang.*', 'attributes_lang.name as attribute_name')->join('attribute_values', 'attribute_values.id', '=', 'attribute_value_lang.attribute_value_id')->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')->where(['attribute_values.status'=>1, 'attribute_values.attributes_lang_id' => $value->attributes_lang_id])->get()->toArray();
            }
        }
        // echo "<pre>"; print_r($data['product_attributes']->toArray());die;

        $productAssignTOChef = array();
        foreach ($productAssign as $category) {
            $productAssignTOChef[] = $category->chef_id;
        }

        
        $data['productAssignTOChef'] =$productAssignTOChef;
  
        return view('product.edit',$data);
    }

    public function ratingList($id)
    {
        Gate::authorize('Product-edit');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['records'] = Rating::select('rating.*', 'users.name', 'users.country_code', 'users.mobile', 'users.image')->where(['product_id'=>$id])->join('users', 'users.id', '=', 'rating.user_id')->get();
  
        return view('product.rating',$data);
    }

    public function imageView(Request $request)
    {
        Gate::authorize('Product-section');
        $id = $request->segment(3);
        $data['product'] = Products::findOrFail($id);
        $data['productImage'] = ProductImages::select('image','id')->where('product_id',$data['product']->id)->get();
        
        return response()->json($data);;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function create()
    {
        Gate::authorize('Product-create');
        $login_user_data = auth()->user();
        $data['category']=Category::select('name','id')->where(['type'=>1, 'status'=>1])->get();
        $data['chef']=User::select('name','id')->where(['type'=>2,'status'=>1])->get();
        $data['celebrity']=User::select('name','id')->where(['type'=>3,'status'=>1])->get();
        $data['restaurant']=Restaurant::select('name','id','user_id')->where(['status'=>1])->get();
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['user_type'] = $login_user_data->type;
        $data['attributes_lang']=AttributesLang::select('attributes_lang.*')->join('attributes', 'attributes.id', '=', 'attributes_lang.attribute_id')->where(['status'=>1])->get();
        $tags=ProductTags::select('id','tag')->where(['status'=>1, 'lang'=>'en'])->groupBy('tag')->get()->toArray();
        $data['tags'] = json_encode($tags);
        // echo "<pre>"; print_r($data['tags']);die;

        // dd($data['tags']);

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id','brand_id','main_category_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            $data['main_category']=MainCategory::select('name','id')->where(['status'=>1, 'id'=>$restaurant_detail->main_category_id])->get();
            $data['restaurant_id'] = $restaurant_detail->id;
            $data['brand_id'] = $restaurant_detail->brand_id;
            $data['main_category_id'] = $restaurant_detail->main_category_id;

        } else {
            $data['restaurant_id'] = '';
            $data['brand_id'] = '';
            $data['main_category_id'] = '';
        }
        return view('product.add', $data);
    }

    public function show_brands($main_category_id, $brand_id = '') {

      if ($main_category_id) {
        $brands = Brand::where(['main_category_id'=>$main_category_id])->get();
        $data['records'] = $brands;
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      // echo "<pre>"; print_r($data['records']); die;
      $data['brand_id'] = $brand_id;
      return view('product.showBrands',$data);

    }

    public function show_restro_category($main_category_id, $category_id='') {

      if ($main_category_id) {
        // $restroDetail = Restaurant::select('id','main_category_id')->where(['id'=>$restaurant_id])->first();
        $data['records'] = Category::select('id','name')->where(['status'=>1, 'type'=>1, 'main_category_id'=>$main_category_id])->get();
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      // echo "<pre>"; print_r($data['records']); die;
      $data['category_id'] = $category_id;
      return view('product.category',$data);

    }

    public function show_restro($main_category_id, $brand_id, $restaurant_id='') {

      if ($main_category_id) {
        $data['records'] = Restaurant::select('*')->where(['main_category_id'=>$main_category_id, 'brand_id'=>$brand_id])->get();
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      // echo "<pre>"; print_r($data['records']); die;
      $data['restaurant_id'] = $restaurant_id;
      return view('product.restro',$data);

    }

    public function show_restro_byMainCatIds($main_category_id) {

        $main_category_ids = explode(",",$main_category_id);

        if ($main_category_id) {
            $data['records'] = Restaurant::select('*')->whereIn('main_category_id', $main_category_ids)->get();
            $data['main_category_id'] = $main_category_id;

        } else {
            $data['records'] = array();
            $data['main_category_id'] = '';
        }

        return view('product.restroFilter',$data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        Gate::authorize('Product-create');
         // validate
        $input = $request->all();

        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',
            'restaurant_id.required'  => 'The Restaurant field is required.',
        ];

        if ($input['product_for'] == 'dish') {
            $this->validate($request, [
                'product_for'  => 'required',
                'main_category_id'  => 'required',
                'brand_id'  => 'required',
                'name.en'  => 'required|max:255|unique:products,name',
                'name.ar'  => 'required|max:255',
                'sku_code'=>'required',
                'description.en'=>'required',
                'description.ar'=>'required',
                'product_type'=> 'required',
                // 'video'=> 'required',
                'discount_price'=> 'nullable|numeric|not_in:0',
                'prepration_time'=> 'required|numeric|min:0|not_in:0',
                // 'admin_price'=> 'required|numeric|min:0|not_in:0',
                'price'=> 'required|numeric|min:1|not_in:0',
                'category_id' => 'required',
                'points' =>'required',
                //'serve' => 'required',
                'restaurant_id'=> 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ],$mesasge);

        } else {
            $this->validate($request, [
                'product_for'  => 'required',
                'main_category_id'  => 'required',
                'brand_id'  => 'required',
                'name.en'  => 'required|max:255|unique:products,name',
                'name.ar'  => 'required|max:255',
                'sku_code'=>'required',
                'description.en'=>'required',
                'description.ar'=>'required',
                'category_id' => 'required',
                'restaurant_id'=> 'required',
                'shop_type'=> 'required',
                'delivery_time'=> 'required',
                'discount_price'=> 'nullable|numeric|not_in:0',
                'price'=> 'required|numeric|min:1|not_in:0',
                'points' =>'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ],$mesasge);
        }
        // create a new task based on Content tasks relationship

        $fail = false;
        $message = '';

        if ($input['product_for'] == 'other' && $input['delivery_time'] == 'manual') {

            if (empty($input['delivery_hours'])) {
                $fail = true;
                $message = 'The delivery hours field is required';
            }
        }

        if (isset($input['customization'])) {

            if (!isset($input['customize_option'])) {

                if (empty($input['customize_option'])) {
                    $fail = true;
                    $message = 'The customize option field is required';
                }
            }
        }

        if (isset($input['discount_price'])) {

            if ($input['discount_price'] > $input['price']) {
                $fail = true;
                $message = 'Discount price should be less than original price';
            }
        }

        // dd($input['attribute']);

        if (isset($input['attribute']) && !empty($input['attribute'])) {
            $attributeLangIdsArray = [];

            if (isset($input['customization'])) {
                $customize_option = $input['customize_option'];

                if ($customize_option == 'normal') {

                    foreach ($input['attribute'] as $key => $value) {

                        if (isset($value['attribute_value_lang_id'])) {

                            foreach ($value['attribute_value_lang_id'] as $k => $v) {

                                if (in_array($v, $attributeLangIdsArray)) {
                                    $fail = true;
                                    $message = 'Attributes are repeated, please check.';
                                } else {
                                    $attributeLangIdsArray[] = $v;
                                }
                            }
                        }
                    }
                }
            }
        }

        $langData = Language::pluck('lang')->toArray();
        // echo "<pre>"; print_r(count($input['inputTag']['ar'])); die;

        if (isset($input['inputTag']) && !empty($input['inputTag'])) {

            foreach ($langData as $lang) {
                // $tagsArray = explode(",",$input['inputTag'][$lang]);
                $tagsArray = $input['inputTag'][$lang];

                if (count($tagsArray) > 10) {
                    $fail = true;
                    $message = 'Tag should not be more then 10.';
                }
            }
        }

        if(!$fail){
            $lang = Language::pluck('lang')->toArray();
            $data = new  Products;

            if ($input['product_for'] == 'dish') {
                foreach($lang as $lang){
                    if($lang=='en')
                    {
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file,'product','image');
                            if($result[0]==true){
                                $data->main_image = $result[1];
                            }
                        }
                        $data->name = $input['name'][$lang];
                        $data->sku_code = $input['sku_code'];
                        $data->long_description = $input['description'][$lang];
                        /*$data->recipe_description = $input['recipe_description'][$lang];*/
                        $data->product_for = $input['product_for'];
                        $data->main_category_id = $input['main_category_id'];
                        $data->brand_id = $input['brand_id'];
                        $data->products_type=$input['product_type'];
                        //$data->serve=$input['serve'];
                        $data->restaurant_id=$input['restaurant_id'];
                        
                        $data->prepration_time = $input['prepration_time'];
                        // $data->celebrity_amount=$input['celebrity_price'];
                        //$data->admin_amount=$input['admin_price'];
                        $data->discount_price=$input['discount_price'];
                        $data->total_amount=$input['price'];
                        $data->video=$input['video'];
                        // $data->celebrity_id=$input['celebrity_id'];

                        if ($input['discount_price'] && !empty($input['discount_price'])) {
                            $data->points = ($input['points'] / 100) * $input['discount_price'];

                        } else {
                            $data->points = ($input['points'] / 100) * $input['price'];
                        }

                        if ($input['discount_price'] && !empty($input['discount_price'])) {
                            $data->extra_kilopoints = ($input['extra_kilopoints'] / 100) * $input['discount_price'];

                        } else {
                            $data->extra_kilopoints = ($input['extra_kilopoints'] / 100) * $input['price'];
                        }

                        $data->points_percent=$input['points'];
                        $data->extra_kilopoints_percent=$input['extra_kilopoints'];
                        $data->price=$input['price'];
                        $data->category_id=$input['category_id'];
                        $data->sub_category_id = $input['parent_id'] ?? null;

                        if (isset($input['customization'])) {
                            $data->customization = 'Yes';
                            $data->customize_option = $input['customize_option'];

                        } else {
                            $data->customization='No';
                        }
                        $data->is_active= 1;
                        $data->save();
                    }
                    $dataLang = new  ProductLang;
                    $dataLang->product_id = $data->id;
                    $dataLang->name = $input['name'][$lang];
                    /*$dataLang->recipe_description = $input['recipe_description'][$lang];*/
                    $dataLang->long_description = $input['description'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }

                if ($request->file('multipalImage')) {
                    $i = 1;
                    foreach ($request->file("multipalImage") as $file) {
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
                        $modelProductImages->product_id = $data->id;
                        $modelProductImages->save();
                    }   
                   
                }

                /*if ($request->input('ingredients')) {

                    foreach($request->input("ingredients")['en'] as $key => $ingredientsName) {

                        if ($ingredientsName) {
                            $modelProductIngredients = new ProductIngredients();
                            $modelProductIngredients->product_id = $data->id;
                            $modelProductIngredients->name = $ingredientsName;
                            $modelProductIngredients->save();

                            
                            $modelProductIngredientsEn = new ProductIngredientLang();
                            $modelProductIngredientsEn->product_ingredients_id = $modelProductIngredients->id;
                            $modelProductIngredientsEn->name = $ingredientsName;
                            $modelProductIngredientsEn->lang = 'en';
                            $modelProductIngredientsEn->save();

                            $modelProductIngredientsAr = new ProductIngredientLang();
                            $modelProductIngredientsAr->product_ingredients_id = $modelProductIngredients->id;
                            $modelProductIngredientsAr->name = $request->input("ingredients")['ar'][$key];
                            $modelProductIngredientsAr->lang = 'ar';
                            
                            $modelProductIngredientsAr->save();
                        }              
                    }
                }*/

                /*if ($request->input('chef_id')) {
                    foreach ($request->input("chef_id") as $chefAssign) {
                        $modelProductAssigntoChef= new ProductAssignTOChef();
                        $modelProductAssigntoChef->product_id = $data->id;
                        $modelProductAssigntoChef->chef_id = $chefAssign;
                        $modelProductAssigntoChef->save();
                    }
                }*/

                if($data->id){

                    $toppingData = new Topping;
                    $toppingData->main_category_id = $input['main_category_id'];
                    $toppingData->category_id = $input['category_id'];
                    $toppingData->dish_id = $data->id;
                    $toppingData->save();

                    if ($toppingData->id) {

                        if (isset($input['attribute']) && !empty($input['attribute'])) {

                            foreach ($input['attribute'] as $key => $value) {
                                $dataAttr = new ProductAttributes;
                                $dataAttr->product_id = $data->id;
                                $dataAttr->dish_topping_id = $toppingData->id;
                                // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                                // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                                $dataAttr->is_mandatory = $value['is_mandatory'];
                                $dataAttr->is_free = $value['is_free'];

                                if ($value['is_free'] == 1) {
                                  $dataAttr->price = null;
                                  $dataAttr->discount_price = null;

                                } else {
                                  $dataAttr->price = $value['price'] ?? null;
                                  $dataAttr->discount_price = $value['discount_price'] ?? null;
                                }

                                if (isset($value['discount_price']) && !empty($value['discount_price'])) {
                                    $dataAttr->points = ($input['points'] / 100) * $value['discount_price'];

                                } else if($value['price'] && !empty($value['price'])) {
                                    $dataAttr->points = ($input['points'] / 100) * $value['price'];

                                } else {
                                    $dataAttr->points = null;
                                }
                                // $dataAttr->points = $value['points'] ?? null;

                                if($dataAttr->save()) {

                                  if (isset($value['attribute_value_lang_id'])) {
                                    foreach ($value['attribute_value_lang_id'] as $k => $v) {
                                      $dataAttrValue = new ProductAttributeValues;
                                      $dataAttrValue->product_attributes_id = $dataAttr->id;
                                      $dataAttrValue->attributes_lang_id = $k;
                                      $dataAttrValue->attribute_value_lang_id = $v;
                                      $dataAttrValue->save();
                                    }
                                  }
                                }
                            }
                        }
                    }

                    if (isset($input['inputTag']) && !empty($input['inputTag'])) {

                        foreach ($langData as $lang) {
                            // $tagsArray = explode(",",$input['inputTag'][$lang]);
                            $tagsArray = $input['inputTag'][$lang];

                            foreach ($tagsArray as $k_tag => $v_tag) {

                                if ($v_tag && !empty($v_tag)) {
                                    $dataTagValue = new ProductTags;
                                    $dataTagValue->product_id = $data->id;
                                    $dataTagValue->tag = $v_tag;
                                    $dataTagValue->lang = $lang;
                                    $dataTagValue->save();
                                }
                            }
                        }
                    }
                    $result['message'] = 'Dish '.$data->name.' has been created';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Dish Can`t created';
                    $result['status'] = 0;
                }

            } else {
                // foreach ($input['attribute'] as $key => $value) {

                //     foreach ($value['attribute_value_ids'] as $k => $v) {
                //         echo "<pre>";print_r($v);die;      
                //     }
                // } die;
                foreach($lang as $lang){
                    if($lang=='en')
                    {
                        if ($request->file('image')) {
                            $file = $request->file('image');
                            $result = image_upload($file,'product','image');
                            if($result[0]==true){
                                $data->main_image = $result[1];
                            }
                        }
                        $data->name = $input['name'][$lang];
                        $data->sku_code = $input['sku_code'];
                        $data->long_description = $input['description'][$lang];
                        $data->product_for = $input['product_for'];
                        $data->main_category_id = $input['main_category_id'];
                        $data->brand_id = $input['brand_id'];
                        $data->restaurant_id=$input['restaurant_id'];
                        $data->shop_type = $input['shop_type'];
                        $data->delivery_time = $input['delivery_time'];
                        $data->delivery_hours = $input['delivery_hours'];
                        $data->category_id=$input['category_id'];
                        $data->sub_category_id = $input['parent_id'] ?? null;
                        $data->discount_price=$input['discount_price'];
                        $data->total_amount=$input['price'];
                        // $data->points=$input['points'];
                        $data->is_active= 1;

                        if ($input['discount_price'] && !empty($input['discount_price'])) {
                            $data->points = ($input['points'] / 100) * $input['discount_price'];

                        } else {
                            $data->points = ($input['points'] / 100) * $input['price'];
                        }

                        if ($input['discount_price'] && !empty($input['discount_price'])) {
                            $data->extra_kilopoints = ($input['extra_kilopoints'] / 100) * $input['discount_price'];

                        } else {
                            $data->extra_kilopoints = ($input['extra_kilopoints'] / 100) * $input['price'];
                        }

                        $data->points_percent=$input['points'];
                        $data->extra_kilopoints_percent=$input['extra_kilopoints'];

                        if (isset($input['customization'])) {
                            $data->customization = 'Yes';
                            $data->customize_option = $input['customize_option'];

                        } else {
                            $data->customization='No';
                        }

                        $data->save();
                    }
                    $dataLang = new  ProductLang;
                    $dataLang->product_id = $data->id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->long_description = $input['description'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }

                if ($data->id) {

                    /*if (isset($input['attribute']) && !empty($input['attribute'])) {

                        foreach ($input['attribute'] as $key => $value) {
                            $dataAttr = new ProductAttributes;
                            $dataAttr->product_id = $data->id;
                            $dataAttr->attributes_lang_id = $value['attribute_id'];
                            $dataAttr->price = $value['price'];
                            $dataAttr->video = $value['video'];
                            $dataAttr->points = $value['points'];
                            $dataAttr->discount_price = $value['discount_price'];
                            
                            if ($dataAttr->save()) {

                                if ($value['attribute_value_ids'] && !empty($value['attribute_value_ids'])) {

                                    foreach ($value['attribute_value_ids'] as $k => $v) {

                                        if ($v) {
                                            $dataAttrValues = new ProductAttributeValues;
                                            $dataAttrValues->product_attributes_id = $dataAttr->id;
                                            $dataAttrValues->attribute_value_lang_id = $v;
                                            $dataAttrValues->save();
                                        }     
                                    }
                                }
                            }
                        }
                    }*/
                    $toppingData = new Topping;
                    $toppingData->main_category_id = $input['main_category_id'];
                    $toppingData->category_id = $input['category_id'];
                    $toppingData->dish_id = $data->id;
                    $toppingData->save();

                    if ($toppingData->id) {

                        if (isset($input['attribute']) && !empty($input['attribute'])) {

                            foreach ($input['attribute'] as $key => $value) {
                                $dataAttr = new ProductAttributes;
                                $dataAttr->product_id = $data->id;
                                $dataAttr->dish_topping_id = $toppingData->id;
                                $dataAttr->is_mandatory = $value['is_mandatory'];
                                $dataAttr->is_free = $value['is_free'];

                                if ($value['is_free'] == 1) {
                                  $dataAttr->price = null;
                                  $dataAttr->discount_price = null;

                                } else {
                                  $dataAttr->price = $value['price'];
                                  $dataAttr->discount_price = $value['discount_price'] ?? $value['price'];
                                }

                                if (isset($value['discount_price']) && !empty($value['discount_price'])) {
                                    $dataAttr->points = ($input['points'] / 100) * $value['discount_price'];

                                } else if($value['price'] && !empty($value['price'])) {
                                    $dataAttr->points = ($input['points'] / 100) * $value['price'];

                                } else {
                                    $dataAttr->points = null;
                                }
                                // $dataAttr->points = $value['points'] ?? null;

                                if($dataAttr->save()) {

                                  if (isset($value['attribute_value_lang_id'])) {
                                    foreach ($value['attribute_value_lang_id'] as $k => $v) {
                                      $dataAttrValue = new ProductAttributeValues;
                                      $dataAttrValue->product_attributes_id = $dataAttr->id;
                                      $dataAttrValue->attributes_lang_id = $k;
                                      $dataAttrValue->attribute_value_lang_id = $v;
                                      $dataAttrValue->save();
                                    }
                                  }
                                }
                            }
                        }
                    }

                    if (isset($input['inputTag']) && !empty($input['inputTag'])) {

                        foreach ($langData as $lang) {
                            // $tagsArray = explode(",",$input['inputTag'][$lang]);
                            $tagsArray = $input['inputTag'][$lang];

                            foreach ($tagsArray as $k_tag => $v_tag) {

                                if ($v_tag && !empty($v_tag)) {
                                    $dataTagValue = new ProductTags;
                                    $dataTagValue->product_id = $data->id;
                                    $dataTagValue->tag = $v_tag;
                                    $dataTagValue->lang = $lang;
                                    $dataTagValue->save();
                                }
                            }
                        }
                    }
                    $result['message'] = 'Product '.$data->name.' has been created';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Product Can`t created';
                    $result['status'] = 0;
                }
            }

        } else {
            $result['message'] = $message;
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
        Gate::authorize('Product-section');
        $products=Products::select('products.*','products.points as kp','categories.name as category_name','main_category.name as main_category_name','brands.name as brand_name','restaurants.name as restaurant_name')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->leftJoin('main_category', 'main_category.id', '=', 'categories.main_category_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                    ->leftJoin('restaurants', 'restaurants.id', '=', 'products.restaurant_id')
                    ->where('products.id', $id)
                    ->first();

        /*$products = Products::select('products.*','categories.name as cat_name')
                ->join('categories','products.category_id','=','categories.id')
                ->where('products.id',$id)
                ->first();*/
        $data['products'] = $products;
        if(isset($products->celebrity_id)){
            $data['celebrity_name'] = User::select('name')->where('id',$products->celebrity_id)->first();
        }
        $data['products_images'] = ProductImages::select('image','id')->where('product_id',$products->id)->get();
        $data['product_assign_to_chef'] = ProductAssignTOChef::select('product_assign_to_chef.chef_id','product_assign_to_chef.id','users.name')
                                        ->join('users','users.id','=','product_assign_to_chef.chef_id')
                                        ->where('product_id',$products->id)
                                        ->get();

        $data['product_ingredients'] = ProductIngredients::select('name')
                                    ->where('product_id',$products->id)
                                    ->get();

        // dd($data['products']->product_attributes);
        return view('product.view',$data);
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
    public function update(Request $request, $id)
    {
        Gate::authorize('Product-edit');
        // validate
        $input = $request->all();

        $message = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'name.en.unique'=>'The product is already exist with the same name.',
            'image.size'  => 'the file size is less than 5MB',
            'restaurant_id.required'  => 'The Restaurant field is required.',
        ];
		if ($input['product_for'] == 'dish') {
            $this->validate($request, [
                'product_for'  => 'required',         
                'main_category_id'  => 'required',       
                'brand_id'  => 'required',       
                'name.en'=>'required|max:255|unique:products,name, '. $id .',id',
                'name.ar'=>'required|max:255',
                'sku_code'=>'required',
                'description.en'=>'required',
                'description.ar'=>'required',
                'product_type'=> 'required',
                // 'video'=> 'required',
                'discount_price'=> 'nullable|numeric|not_in:0',
                'prepration_time'=> 'required|numeric|min:0|not_in:0',
                //'admin_price'=> 'required|numeric|min:0|not_in:0',
                'price'=> 'required|numeric|min:1|not_in:0',
                'category_id' => 'required',
                'points' =>'required',
                'buy_one_get_one' =>'required',
                //'serve' =>'required',
                'restaurant_id'=> 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ],[
                'image.size'  => 'the file size is less than 5MB',
            ],$message);

        } else {
            $this->validate($request, [
                'product_for'  => 'required',
                'main_category_id'  => 'required',
                'brand_id'  => 'required',
                'name.en'  => 'required|max:255|unique:products,name, '. $id .',id',
                'name.ar'  => 'required|max:255',
                'sku_code'=>'required',
                'description.en'=>'required',
                'description.ar'=>'required',
                'category_id' => 'required',
                'restaurant_id'=> 'required',
                'shop_type'=> 'required',
                'delivery_time'=> 'required',
                'buy_one_get_one'=> 'required',
                'discount_price'=> 'nullable|numeric|not_in:0',
                'price'=> 'required|numeric|min:1|not_in:0',
                'points' =>'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ],[
                'image.size'  => 'the file size is less than 5MB',
            ],$message);
        }
        // create a new task based on Content tasks relationship

        $cate_id = $id;
        $getProductDetail = Products::where('products.id', $cate_id)->first();
        $file = $request->file('image');

        $fail = false;
        $message = '';
        /*$encryptedPrice = encryptPass($input['price']);
        $decryptedPrice = decryptPass($encryptedPrice);*/

        if ($input['product_for'] == 'other' && $input['delivery_time'] == 'manual') {

            if (empty($input['delivery_hours'])) {
                $fail = true;
                $message = 'The delivery hours field is required';
            }
        }
        $is_delete_attr_data = false;

        if (isset($input['customization'])) {

            if (!isset($input['customize_option'])) {

                if (empty($input['customize_option'])) {
                    $fail = true;
                    $message = 'The customize option field is required';
                }
            }

        } else {
            $is_delete_attr_data = true;
        }

        if (isset($input['discount_price'])) {

            if ($input['discount_price'] > $input['price']) {
                $fail = true;
                $message = 'Discount price should be less than original price';
            }
        }

        // dd($input['attribute']);

        if (isset($input['attribute']) && !empty($input['attribute'])) {
            $attributeLangIdsArray = [];

            if (isset($input['customization'])) {
                $customize_option = $input['customize_option'];

                if ($customize_option == 'normal') {

                    foreach ($input['attribute'] as $key => $value) {

                        if (isset($value['attribute_value_lang_id'])) {

                            foreach ($value['attribute_value_lang_id'] as $k => $v) {

                                if (in_array($v, $attributeLangIdsArray)) {
                                    $fail = true;
                                    $message = 'Attributes are repeated, please check.';
                                } else {
                                    $attributeLangIdsArray[] = $v;
                                }
                            }
                        }
                    }
                }
            }

        } else {
            $is_delete_attr_data = true;
        }

        if (isset($input['inputTag']) && !empty($input['inputTag'])) {
            $langData = Language::pluck('lang')->toArray();

            foreach ($langData as $lang) {
                // $tagsArray = explode(",",$input['inputTag'][$lang]);
                $tagsArray = $input['inputTag'][$lang];

                if (count($tagsArray) > 10) {
                    $fail = true;
                    $message = 'Tag should not be more then 10.';
                }
            }
        }

        //Delete Tags
        ProductTags::where('product_id',$cate_id)->delete();

        if (!$fail) {

            if ($is_delete_attr_data) {
                //delete old product attributes
                $productAttrs = ProductAttributes::where('product_id',$cate_id)->get();

                if ($productAttrs) {

                    foreach ($productAttrs as $key => $value) {

                        if ($value) {
                            ProductAttributeValues::where('product_attributes_id',$value->id)->delete();
                        }
                    }
                    ProductAttributes::where('product_id',$cate_id)->delete();
                }
            }

            try{
                $langData = Language::pluck('lang')->toArray();

                if ($input['product_for'] == 'dish') {
                    foreach ($langData as $lang) {
                        $inputs = [
                            'name' => $input['name'][$lang],
                            'sku_code' => $input['sku_code'],
                            'products_type' =>$request->product_type,
                            'long_description' => $input['description'][$lang],
                            /*'recipe_description' => $input['recipe_description'][$lang],*/
                            'category_id' => $request->category_id,
                            'sub_category_id' => $request->parent_id ?? null,
                            'main_category_id' => $request->main_category_id,
                            'brand_id' => $request->brand_id,
                            'restaurant_id' => $request->restaurant_id,
                            // 'chef_amount'=> $request->chef_price,
                            // 'celebrity_amount'=> $request->celebrity_price,
                            //'admin_amount'=> $request->admin_price,
                            'discount_price'=> $request->discount_price ?? null,
                            'prepration_time'=> $request->prepration_time,
                            'total_amount' => $request->price,
                            'price' => $request->price,
                            'buy_one_get_one' => $request->buy_one_get_one,
                            //'serve' => $request->serve,
                            // 'points'=>$request->points,
                            'video'=>$request->video,
                            // 'celebrity_id' => $request->celebrity_id,
                        ];

                        if ($request->discount_price && !empty($request->discount_price)) {
                            $inputs['points'] = ($request->points / 100) * $request->discount_price;

                        } else {
                            $inputs['points'] = ($request->points / 100) * $request->price;
                        }
                        $inputs['points_percent'] = $request->points;

                        if ($request->discount_price && !empty($request->discount_price)) {
                            $inputs['extra_kilopoints'] = ($request->extra_kilopoints / 100) * $request->discount_price;

                        } else {
                            $inputs['extra_kilopoints'] = ($request->extra_kilopoints / 100) * $request->price;
                        }
                        $inputs['extra_kilopoints_percent'] = $request->extra_kilopoints;

                        if($lang=='en')
                        {
                            if (isset($input['customization'])) {
                                $inputs['customization'] = 'Yes';
                                $inputs['customize_option'] = $request->customize_option;

                            } else {
                                $inputs['customization'] = 'No';
                            }

                            if(isset($file))
                            {
                                $result = image_upload($file,'product','image');
                                $inputs['main_image'] = $result[1];
                                $data = Products::where('id',$cate_id)->update($inputs);
                            }
                            else
                            {
                                $data = Products::where('id',$cate_id)->update($inputs);
                            }                    
                        }

                        $dataLang = ProductLang::where(['product_id'=>$cate_id,'lang'=>$lang])->first();

                        if(isset($dataLang))
                        {
                           $dataLang = ProductLang::where(['product_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang],'long_description'=>$input['description'][$lang]]);                                   
                        }
                        else
                        {
                            $dataLang = new  ProductLang;
                            $dataLang->product_id = $cate_id;
                            $dataLang->name = $input['name'][$lang];
                            /*$dataLang->recipe_description = $input['recipe_description'][$lang];*/
                            $dataLang->long_description = $input['description'][$lang];
                            $dataLang->lang = $lang;
                            $dataLang->save();
                        }
                    }

                    /*if ($request->input('chef_id')) {
                        $chef = ProductAssignTOChef::where('product_id',$cate_id)->get();
                        if(count($chef) < 0){
                            ProductAssignTOChef::where('product_id',$cate_id)->delete();
                            foreach ($request->input("chef_id") as $chefAssign) {
                                if(isset($chefAssign)){
                                    ProductAssignTOChef::create(['product_id'=>$cate_id,'chef_id'=>$chefAssign]);
                                }
                            }
                        }else{
                            foreach ($request->input("chef_id") as $chefAssign) {
                                ProductAssignTOChef::create(['product_id'=>$cate_id,'chef_id'=>$chefAssign]);
                            }
                        }
                    }*/
                   

                    /*if ($request->input('ingredients')) {
                        $ingredient = ProductIngredients::where('product_id',$cate_id)->get();
                        if(count($ingredient)){
                            foreach ($ingredient as $dataIns) {
                                ProductIngredientLang::where('product_ingredients_id',$dataIns->id)->delete();
                            }
                            ProductIngredients::where('product_id',$cate_id)->delete();
                            foreach($request->input("ingredients")['en'] as $key => $ingredientsName) {
                                
                                if ($ingredientsName) {
                                    $modelProductIngredients = new ProductIngredients();
                                    $modelProductIngredients->product_id = $cate_id;
                                    $modelProductIngredients->name = $ingredientsName;
                                    $modelProductIngredients->save();

                                    
                                    $modelProductIngredientsEn = new ProductIngredientLang();
                                    $modelProductIngredientsEn->product_ingredients_id = $modelProductIngredients->id;
                                    $modelProductIngredientsEn->name = $ingredientsName;
                                    $modelProductIngredientsEn->lang = 'en';
                                    $modelProductIngredientsEn->save();

                                    $modelProductIngredientsAr = new ProductIngredientLang();
                                    $modelProductIngredientsAr->product_ingredients_id = $modelProductIngredients->id;
                                    $modelProductIngredientsAr->name = $request->input("ingredients")['ar'][$key];
                                    $modelProductIngredientsAr->lang = 'ar';
                                    
                                    $modelProductIngredientsAr->save();
                                }
                            }
                        }else{
                           
                            foreach($request->input("ingredients")['en'] as $key => $ingredientsName) {

                                if ($ingredientsName) {
                                    $modelProductIngredients = new ProductIngredients();
                                    $modelProductIngredients->product_id = $cate_id;
                                    $modelProductIngredients->name = $ingredientsName;
                                    $modelProductIngredients->save();

                                    
                                    $modelProductIngredientsEn = new ProductIngredientLang();
                                    $modelProductIngredientsEn->product_ingredients_id = $modelProductIngredients->id;
                                    $modelProductIngredientsEn->name = $ingredientsName;
                                    $modelProductIngredientsEn->lang = 'en';
                                    $modelProductIngredientsEn->save();

                                    $modelProductIngredientsAr = new ProductIngredientLang();
                                    $modelProductIngredientsAr->product_ingredients_id = $modelProductIngredients->id;
                                    $modelProductIngredientsAr->name = $request->input("ingredients")['ar'][$key];
                                    $modelProductIngredientsAr->lang = 'ar';
                                    
                                    $modelProductIngredientsAr->save();
                                }
                            }
                        }
                    }*/

                    $dishToppingData = Topping::where(['main_category_id'=>$input['main_category_id'],'category_id'=>$input['category_id'],'dish_id'=>$cate_id])->first();

                    if($dishToppingData) {

                        //delete old product attributes
                        $productAttrs = ProductAttributes::where('product_id',$cate_id)->get();

                        if ($productAttrs) {

                            foreach ($productAttrs as $key => $value) {

                                if ($value) {
                                    ProductAttributeValues::where('product_attributes_id',$value->id)->delete();
                                }
                            }
                            ProductAttributes::where('product_id',$cate_id)->delete();
                        }

                        if (isset($input['attribute']) && !empty($input['attribute'])) {

                            foreach ($input['attribute'] as $key => $value) {
                                $dataAttr = new ProductAttributes;
                                $dataAttr->product_id = $cate_id;
                                $dataAttr->dish_topping_id = $dishToppingData->id;
                                // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                                // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                                $dataAttr->is_mandatory = $value['is_mandatory'];
                                $dataAttr->is_free = $value['is_free'];

                                if ($value['is_free'] == 1) {
                                    $dataAttr->price = null;
                                    $dataAttr->discount_price = null;

                                } else {
                                    // $dataAttr->is_free = 0;
                                    $dataAttr->price = $value['price'] ?? null;
                                    $dataAttr->discount_price = $value['discount_price'] ?? null;
                                }

                                if (isset($value['discount_price']) && !empty($value['discount_price'])) {
                                    $dataAttr->points = ($input['points'] / 100) * $value['discount_price'];

                                } else if(isset($value['price']) && !empty($value['price'])) {
                                    $dataAttr->points = ($input['points'] / 100) * $value['price'];

                                } else {
                                    $dataAttr->points = null;
                                }
                                // $dataAttr->points = $value['points'] ?? null;
                                  // dd($dataAttr);

                                if($dataAttr->save()) {

                                    if (isset($value['attribute_value_lang_id'])) {

                                        foreach ($value['attribute_value_lang_id'] as $k => $v) {

                                            if ($v && !empty($v)) {
                                                $dataAttrValue = new ProductAttributeValues;
                                                $dataAttrValue->product_attributes_id = $dataAttr->id;
                                                $dataAttrValue->attributes_lang_id = $k;
                                                $dataAttrValue->attribute_value_lang_id = $v;
                                                $dataAttrValue->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    } else {

                        $toppingData = new Topping;
                        $toppingData->main_category_id = $input['main_category_id'];
                        $toppingData->category_id = $input['category_id'];
                        $toppingData->dish_id = $cate_id;
                        $toppingData->save();

                        if ($toppingData->id) {

                            if (isset($input['attribute']) && !empty($input['attribute'])) {

                                foreach ($input['attribute'] as $key => $value) {
                                    $dataAttr = new ProductAttributes;
                                    $dataAttr->product_id = $cate_id;
                                    $dataAttr->dish_topping_id = $toppingData->id;
                                    // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                                    // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                                    $dataAttr->is_mandatory = $value['is_mandatory'];
                                    $dataAttr->is_free = $value['is_free'];

                                    if ($value['is_free'] == 1) {
                                      $dataAttr->price = null;
                                      $dataAttr->discount_price = null;

                                    } else {
                                      $dataAttr->price = $value['price'] ?? null;
                                      $dataAttr->discount_price = $value['discount_price'] ?? $value['price'];
                                    }

                                    if (isset($value['discount_price']) && !empty($value['discount_price'])) {
                                        $dataAttr->points = ($input['points'] / 100) * $value['discount_price'];

                                    } else if($value['price'] && !empty($value['price'])) {
                                        $dataAttr->points = ($input['points'] / 100) * $value['price'];

                                    } else {
                                        $dataAttr->points = null;
                                    }
                                    // $dataAttr->points = $value['points'] ?? null;

                                    if($dataAttr->save()) {

                                      if (isset($value['attribute_value_lang_id'])) {
                                        foreach ($value['attribute_value_lang_id'] as $k => $v) {
                                          $dataAttrValue = new ProductAttributeValues;
                                          $dataAttrValue->product_attributes_id = $dataAttr->id;
                                          $dataAttrValue->attributes_lang_id = $k;
                                          $dataAttrValue->attribute_value_lang_id = $v;
                                          $dataAttrValue->save();
                                        }
                                      }
                                    }
                                }
                            }
                        }
                    }

                    if (isset($input['inputTag']) && !empty($input['inputTag'])) {

                        foreach ($langData as $lang) {
                            // $tagsArray = explode(",",$input['inputTag'][$lang]);
                            $tagsArray = $input['inputTag'][$lang];

                            foreach ($tagsArray as $k_tag => $v_tag) {

                                if ($v_tag && !empty($v_tag)) {
                                    $dataTagValue = new ProductTags;
                                    $dataTagValue->product_id = $cate_id;
                                    $dataTagValue->tag = $v_tag;
                                    $dataTagValue->lang = $lang;
                                    $dataTagValue->save();
                                }
                            }
                        }
                    }

                    if ($getProductDetail->extra_kilopoints_percent != $request->extra_kilopoints) {
                        //Panel Notification data
                        $panelNotificationData = new PanelNotifications;
                        $panelNotificationData->user_id = $request->restaurant_id;
                        $panelNotificationData->product_id = $cate_id;
                        $panelNotificationData->user_type = 4;
                        $panelNotificationData->notification_for = 'Kilopoints-Changed';
                        $panelNotificationData->notification_type = 3;
                        $panelNotificationData->title = 'Kilopoints Changed';
                        $panelNotificationData->message = $input['name']['en'].' changed extra kilopoints';
                        
                        if ($panelNotificationData->save()) {
                            $panelData = PanelNotifications::select('panel_notifications.*');
                            $adminCount = 0;
                            $restroCount = 0;

                            if ($request->restaurant_id) {
                                $panelData->where('panel_notifications.user_id', $request->restaurant_id);
                                $restroCount = $panelData->where('panel_notifications.is_read', 0)->count();
                            }
                            $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                              CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$request->restaurant_id."/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 30,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "POST",
                              CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount,\n  \"restroCount\":$restroCount\n}\n",
                              CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache",
                                "content-type: application/json",
                                "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$request->restaurant_id."/0",
                                "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                              ),
                            ));

                            $responseNew = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);

                            if ($err) {
                              // echo "cURL Error #:" . $err;
                            } else {
                              // echo $responseNew;
                            }

                            /*Admin Notification*/
                            $curl_admin = curl_init();

                            curl_setopt_array($curl_admin, array(
                              CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 30,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "POST",
                              CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount,\n  \"restroCount\":$restroCount\n}\n",
                              CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache",
                                "content-type: application/json",
                                "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/0",
                                "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                              ),
                            ));

                            $responseNew = curl_exec($curl_admin);
                            $err = curl_error($curl_admin);

                            curl_close($curl_admin);

                            if ($err) {
                              // echo "cURL Error #:" . $err;
                            } else {
                              // echo $responseNew;
                            }
                            /*Admin Notification End*/
                        }
                    }

                    $result['message'] = 'Dish updated successfully.';
                    $result['status'] = 1;
                    return response()->json($result);

                } else {
                    foreach ($langData as $lang) {
                        $inputs = [
                            'name' => $input['name'][$lang],
                            'sku_code' => $input['sku_code'],
                            'long_description' => $input['description'][$lang],
                            'products_type' =>null,
                            'product_for' =>$request->product_for,
                            'main_category_id' =>$request->main_category_id,
                            'brand_id' =>$request->brand_id,
                            'category_id' => $request->category_id,
                            'sub_category_id' => $request->parent_id ?? null,
                            'restaurant_id' => $request->restaurant_id,
                            'shop_type' => $request->shop_type,
                            'delivery_time' => $request->delivery_time,
                            'delivery_hours' => $request->delivery_hours,
                            'buy_one_get_one' => $request->buy_one_get_one,
                            'prepration_time'=> null,
                            'discount_price'=> $request->discount_price ?? null,
                            'total_amount' => $request->price,
                            'price' => $request->price,
                            'video'=>null,
                        ];

                        if ($request->delivery_time != 'manual') {
                            $inputs['delivery_hours'] = null;
                        }

                        if ($request->discount_price && !empty($request->discount_price)) {
                            $inputs['points'] = ($request->points / 100) * $request->discount_price;

                        } else {
                            $inputs['points'] = ($request->points / 100) * $request->price;
                        }
                        $inputs['points_percent'] = $request->points;

                        //start
                        if ($request->discount_price && !empty($request->discount_price)) {
                            $inputs['extra_kilopoints'] = ($request->extra_kilopoints / 100) * $request->discount_price;

                        } else {
                            $inputs['extra_kilopoints'] = ($request->extra_kilopoints / 100) * $request->price;
                        }
                        $inputs['extra_kilopoints_percent'] = $request->extra_kilopoints;
                        //end

                        if ($lang=='en') {

                            if (isset($input['customization'])) {
                                $inputs['customization'] = 'Yes';
                                $inputs['customize_option'] = $request->customize_option;

                            } else {
                                $inputs['customization'] = 'No';
                                $inputs['customize_option'] = null;
                            }

                            if (isset($file)) {
                                $result = image_upload($file,'product','image');
                                $inputs['main_image'] = $result[1];
                                $data = Products::where('id',$cate_id)->update($inputs);
                            } else {
                                $data = Products::where('id',$cate_id)->update($inputs);
                            }                    
                        }
                        $dataLang = ProductLang::where(['product_id'=>$cate_id,'lang'=>$lang])->first();

                        if (isset($dataLang)) {
                           $dataLang = ProductLang::where(['product_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang],'long_description'=>$input['description'][$lang]]);                                   
                        } else {
                            $dataLang = new ProductLang;
                            $dataLang->product_id = $cate_id;
                            $dataLang->name = $input['name'][$lang];
                            $dataLang->long_description = $input['description'][$lang];
                            $dataLang->lang = $lang;
                            $dataLang->save();
                        }
                    }
                    //delete old product attributes
                    /*ProductAttributes::where('product_id',$cate_id)->delete();

                    if (isset($input['old_attribute']) && !empty($input['old_attribute'])) {

                        foreach ($input['old_attribute'] as $key => $value) {
                            $dataAttr = new ProductAttributes;
                            $dataAttr->product_id = $cate_id;
                            $dataAttr->attributes_lang_id = $value['attribute_id'];
                            $dataAttr->price = $value['price'];
                            $dataAttr->video = $value['video'];
                            $dataAttr->points = $value['points'];
                            $dataAttr->discount_price = $value['discount_price'];
                            
                            if ($dataAttr->save()) {

                                if ($value['attribute_value_ids'] && !empty($value['attribute_value_ids'])) {

                                    foreach ($value['attribute_value_ids'] as $k => $v) {

                                        if ($v) {
                                            $dataAttrValues = new ProductAttributeValues;
                                            $dataAttrValues->product_attributes_id = $dataAttr->id;
                                            $dataAttrValues->attribute_value_lang_id = $v;
                                            $dataAttrValues->save();
                                        }     
                                    }
                                }
                            }
                        }
                    }                    

                    if (isset($input['attribute']) && !empty($input['attribute'])) {

                        foreach ($input['attribute'] as $key => $value) {
                            $dataAttr = new ProductAttributes;
                            $dataAttr->product_id = $cate_id;
                            $dataAttr->attributes_lang_id = $value['attribute_id'];
                            $dataAttr->price = $value['price'];
                            $dataAttr->video = $value['video'];
                            $dataAttr->points = $value['points'];
                            $dataAttr->discount_price = $value['discount_price'];
                            
                            if ($dataAttr->save()) {

                                if ($value['attribute_value_ids'] && !empty($value['attribute_value_ids'])) {

                                    foreach ($value['attribute_value_ids'] as $k => $v) {

                                        if ($v) {
                                            $dataAttrValues = new ProductAttributeValues;
                                            $dataAttrValues->product_attributes_id = $dataAttr->id;
                                            $dataAttrValues->attribute_value_lang_id = $v;
                                            $dataAttrValues->save();
                                        }     
                                    }
                                }
                            }
                        }
                    }*/

                    $dishToppingData = Topping::where(['main_category_id'=>$input['main_category_id'],'category_id'=>$input['category_id'],'dish_id'=>$cate_id])->first();

                    if($dishToppingData) {

                        //delete old product attributes
                        $productAttrs = ProductAttributes::where('product_id',$cate_id)->get();

                        if ($productAttrs) {

                            foreach ($productAttrs as $key => $value) {

                                if ($value) {
                                    ProductAttributeValues::where('product_attributes_id',$value->id)->delete();
                                }
                            }
                            ProductAttributes::where('product_id',$cate_id)->delete();
                        }
                        // ProductAttributes::where('product_id',$cate_id)->delete();

                        if (isset($input['attribute']) && !empty($input['attribute'])) {

                            foreach ($input['attribute'] as $key => $value) {
                                  $dataAttr = new ProductAttributes;
                                  $dataAttr->product_id = $cate_id;
                                  $dataAttr->dish_topping_id = $dishToppingData->id;
                                  // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                                  // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                                  $dataAttr->is_mandatory = $value['is_mandatory'];
                                  $dataAttr->is_free = $value['is_free'];

                                  if ($value['is_free'] == 1) {
                                    $dataAttr->price = null;
                                    $dataAttr->discount_price = null;

                                  } else {
                                    $dataAttr->price = $value['price'] ?? null;
                                    $dataAttr->discount_price = $value['discount_price'] ?? null;
                                  }

                                    if (isset($value['discount_price']) && !empty($value['discount_price'])) {
                                        $dataAttr->points = ($input['points'] / 100) * $value['discount_price'];

                                    } else if($value['price'] && !empty($value['price'])) {
                                        $dataAttr->points = ($input['points'] / 100) * $value['price'];

                                    } else {
                                        $dataAttr->points = null;
                                    }
                                    // $dataAttr->points = $value['points'];

                                  if($dataAttr->save()) {

                                    if (isset($value['attribute_value_lang_id'])) {

                                      foreach ($value['attribute_value_lang_id'] as $k => $v) {

                                        if ($v && !empty($v)) {
                                            $dataAttrValue = new ProductAttributeValues;
                                            $dataAttrValue->product_attributes_id = $dataAttr->id;
                                            $dataAttrValue->attributes_lang_id = $k;
                                            $dataAttrValue->attribute_value_lang_id = $v;
                                            $dataAttrValue->save();
                                        }
                                      }
                                    }
                                  }
                            }
                        }
                    }  else {

                        $toppingData = new Topping;
                        $toppingData->main_category_id = $input['main_category_id'];
                        $toppingData->category_id = $input['category_id'];
                        $toppingData->dish_id = $cate_id;
                        $toppingData->save();

                        if ($toppingData->id) {

                            if (isset($input['attribute']) && !empty($input['attribute'])) {

                                foreach ($input['attribute'] as $key => $value) {
                                    $dataAttr = new ProductAttributes;
                                    $dataAttr->product_id = $cate_id;
                                    $dataAttr->dish_topping_id = $toppingData->id;
                                    // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                                    // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                                    $dataAttr->is_mandatory = $value['is_mandatory'];
                                    $dataAttr->is_free = $value['is_free'];

                                    if ($value['is_free'] == 1) {
                                      $dataAttr->price = null;
                                      $dataAttr->discount_price = null;

                                    } else {
                                      $dataAttr->price = $value['price'] ?? null;
                                      $dataAttr->discount_price = $value['discount_price'] ?? $value['price'];
                                    }

                                    if (isset($value['discount_price']) && !empty($value['discount_price'])) {
                                        $dataAttr->points = ($input['points'] / 100) * $value['discount_price'];

                                    } else if(isset($value['price']) && !empty($value['price'])) {
                                        $dataAttr->points = ($input['points'] / 100) * $value['price'];

                                    } else {
                                        $dataAttr->points = null;
                                    }
                                    // $dataAttr->points = $value['points'] ?? null;

                                    if($dataAttr->save()) {

                                      if (isset($value['attribute_value_lang_id'])) {
                                        foreach ($value['attribute_value_lang_id'] as $k => $v) {
                                          $dataAttrValue = new ProductAttributeValues;
                                          $dataAttrValue->product_attributes_id = $dataAttr->id;
                                          $dataAttrValue->attributes_lang_id = $k;
                                          $dataAttrValue->attribute_value_lang_id = $v;
                                          $dataAttrValue->save();
                                        }
                                      }
                                    }
                                }
                            }
                        }
                    }

                    if (isset($input['inputTag']) && !empty($input['inputTag'])) {

                        foreach ($langData as $lang) {
                            // $tagsArray = explode(",",$input['inputTag'][$lang]);
                            $tagsArray = $input['inputTag'][$lang];

                            foreach ($tagsArray as $k_tag => $v_tag) {

                                if ($v_tag && !empty($v_tag)) {
                                    $dataTagValue = new ProductTags;
                                    $dataTagValue->product_id = $cate_id;
                                    $dataTagValue->tag = $v_tag;
                                    $dataTagValue->lang = $lang;
                                    $dataTagValue->save();
                                }
                            }
                        }
                    }

                    if ($getProductDetail->extra_kilopoints_percent != $request->extra_kilopoints) {
                        //Panel Notification data
                        $panelNotificationData = new PanelNotifications;
                        $panelNotificationData->user_id = $request->restaurant_id;
                        $panelNotificationData->product_id = $cate_id;
                        $panelNotificationData->user_type = 4;
                        $panelNotificationData->notification_for = 'Kilopoints-Changed';
                        $panelNotificationData->notification_type = 3;
                        $panelNotificationData->title = 'Kilopoints Changed';
                        $panelNotificationData->message = $input['name']['en'].' changed extra kilopoints';
                        
                        if ($panelNotificationData->save()) {
                            $panelData = PanelNotifications::select('panel_notifications.*');
                            $adminCount = 0;
                            $restroCount = 0;

                            if ($request->restaurant_id) {
                                $panelData->where('panel_notifications.user_id', $request->restaurant_id);
                                $restroCount = $panelData->where('panel_notifications.is_read', 0)->count();
                            }
                            $adminCount = $panelData->where('panel_notifications.is_read', 0)->count();

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                              CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$request->restaurant_id."/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 30,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "POST",
                              CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount,\n  \"restroCount\":$restroCount\n}\n",
                              CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache",
                                "content-type: application/json",
                                "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_".$request->restaurant_id."/0",
                                "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                              ),
                            ));

                            $responseNew = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);

                            if ($err) {
                              // echo "cURL Error #:" . $err;
                            } else {
                              // echo $responseNew;
                            }

                            /*Admin Notification*/
                            $curl_admin = curl_init();

                            curl_setopt_array($curl_admin, array(
                              CURLOPT_URL => "https://ps.pndsn.com/publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 30,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "POST",
                              CURLOPT_POSTFIELDS => "{\n  \"message\": \"$panelNotificationData->message\",\n  \"adminCount\":$adminCount,\n  \"restroCount\":$restroCount\n}\n",
                              CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache",
                                "content-type: application/json",
                                "location: /publish/pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e/sub-c-560305a8-8b03-11eb-83e5-b62f35940104/0/pubnub_onboarding_channel_admin_1/0",
                                "postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
                              ),
                            ));

                            $responseNew = curl_exec($curl_admin);
                            $err = curl_error($curl_admin);

                            curl_close($curl_admin);

                            if ($err) {
                              // echo "cURL Error #:" . $err;
                            } else {
                              // echo $responseNew;
                            }
                            /*Admin Notification End*/
                        }
                    }
                    $result['message'] = 'Product updated successfully.';
                    $result['status'] = 1;
                    return response()->json($result);
                }
            }
            catch (Exception $e)
            {
                $result['message'] = 'Proudct Can`t be updated.';
                $result['status'] = 0;
                return response()->json($result);           
            }
        } else {
            $result['message'] = $message;
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
        $details = Products::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['is_active' => 1];
            }else{
                $inp = ['is_active' => 0];
            }
            $Product = Products::findOrFail($id);
            if($Product->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Product is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Product is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Product status can`t be updated!!';
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

    public function exportSampleFileForImport($main_category_id, $brand_id)
    {
        return Excel::download(new ImportDataToBeExport($main_category_id, $brand_id), 'Dish.xlsx');
        /*Excel::create('file', function($excel) {
            require_once("/apppath//vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php");
            require_once("/apppath/vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php");

            $excel->sheet('New sheet', function($sheet) {

                $sheet->SetCellValue("A1", "UK");
                $sheet->SetCellValue("A2", "USA");

                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'countries', $sheet, 'A1:A2'
                        )
                );


                $sheet->SetCellValue("B1", "London");
                $sheet->SetCellValue("B2", "Birmingham");
                $sheet->SetCellValue("B3", "Leeds");
                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'UK', $sheet, 'B1:B3'
                        )
                );

                $sheet->SetCellValue("C1", "Atlanta");
                $sheet->SetCellValue("C2", "New York");
                $sheet->SetCellValue("C3", "Los Angeles");
                $sheet->_parent->addNamedRange(
                        new \PHPExcel_NamedRange(
                        'USA', $sheet, 'C1:C3'
                        )
                );
                $objValidation = $sheet->getCell('D1')->getDataValidation();
                $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('countries'); //note this!
            });
        })->download("xlsx");*/
        // return view('home');
    }

    public function import(){
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['restaurant'] = Restaurant::select('id','name')->get();
        $data['category'] = Category::select('id','name')->where(['status'=>1, 'type'=>1])->get();
        //dd($data['category']);
        return view('product.import',$data);
    }

    public function import_new($main_category_id){
        $data['main_category_id']=$main_category_id;
        $data['brandData'] = Brand::where(['main_category_id'=>$main_category_id])->get();
        return view('product.import_new',$data);
    }

    /*public function importData(Request $request) 
    {
        $input = $request->all();
        $catgory_id = '';
        $lang = Language::pluck('lang')->toArray();

        if (!empty($input['file']) && !empty($input['main_category_id']) && !empty($input['restaurant_id']) && !empty($input['product_for'])) {
            $imgRslt = file_upload($request->file('file'), 'product');
            $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];

            if (!empty($excelData)) {
                $n = 8;
                foreach ($excelData as $key => $product) {
                    $lang = Language::pluck('lang')->toArray();

                    if ($product['category_name'] && !empty($product['category_name'])) {
                        $cat = Category::select('id')->where(['name'=>$product['category_name'], 'main_category_id'=>$input['main_category_id']])->first();

                        if ($cat) {
                            $catgory_id = $cat->id;
                        } else {
                            foreach($lang as $lang) {

                                if ($lang=='en') {
                                    $categoryData = new Category;
                                    $categoryData->main_category_id = $input['main_category_id'];
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

                    if ($input['product_for'] == 'dish') {
                        //Food Restros.....

                        if ($product['nameen']) {
                            $data = new  Products;
                            $data->name = $product['nameen'] ?? null;
                            $data->sku_code = $product['sku_code'] ?? null;
                            $data->long_description = $product['descriptionen'] ?? null;
                            $data->product_for = $input['product_for'];
                            $data->main_category_id = $input['main_category_id'];
                            $data->restaurant_id = $input['restaurant_id'] ?? null;
                            $data->products_type = $product['type'] ?? null;
                            $data->prepration_time = $product['prepration_time'] ?? null;
                            $data->discount_price = $product['discount_price'] ?? null;
                            $data->total_amount = $product['price'];
                            $data->video = $product['video_url'];
                            $data->points=$product['kilo_points'];
                            $data->price = $product['price'];
                            $data->category_id = $catgory_id;
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
                        }

                    } else {
                        //Other Store

                        if ($product['nameen']) {
                            $data = new  Products;
                            $data->name = $product['nameen'] ?? null;
                            $data->sku_code = $product['sku_code'] ?? null;
                            $data->long_description = $product['descriptionen'] ?? null;
                            $data->product_for = $input['product_for'];
                            $data->main_category_id = $input['main_category_id'];
                            $data->restaurant_id = $input['restaurant_id'] ?? null;
                            $data->shop_type = $product['shop_type'] ?? null;
                            $data->delivery_time = $product['delivery_time'] ?? null;
                            $data->delivery_hours = $product['delivery_hours'] ?? null;
                            $data->category_id = $catgory_id;
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
                        }
                    }
                }
                return back();
            }
        } else {
            return back();
        }
        // Excel::import(new BulkImport,request()->file('file'));
        // return back();
    }*/

    public function importData_old(Request $request) 
    {
        $input = $request->all();
        $langData = Language::pluck('lang')->toArray();

        if (!empty($input['file']) && !empty($input['main_category_id'])) {
            $imgRslt = file_upload($request->file('file'), 'product');

            if ($imgRslt[0] == true) {
                $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];

                if (!empty($excelData)) {
                    $n = 8;

                    foreach ($excelData as $key => $product) {
                        $catgory_id = '';
                        $brand_id = '';
                        $restaurant_id = '';
                        $modeIds = array();
                        $payment_mode = array();
                        $pickup_mode = array();
                        $productImage = '';

                        if ($product['category'] && !empty($product['category'])) {
                            $cat = Category::select('id')->where(['name'=>$product['category'], 'main_category_id'=>$input['main_category_id']])->first();

                            if ($cat) {
                                $catgory_id = $cat->id;
                            } else {
                                foreach($langData as $lang) {

                                    if ($lang=='en') {
                                        $categoryData = new Category;
                                        $categoryData->main_category_id = $input['main_category_id'];
                                        $categoryData->name = $product['category'];
                                        $categoryData->description = $product['category'];
                                        $categoryData->type = 1;
                                        $categoryData->status= 1;
                                        $categoryData->save();

                                        $catgory_id = $categoryData->id;
                                    }
                                    $dataLang = new CategoryLang;
                                    $dataLang->category_id = $categoryData->id;
                                    $dataLang->name = $product['category'];
                                    $dataLang->description = $product['category'];
                                    $dataLang->lang = $lang;
                                    $dataLang->save();
                                }
                            }
                        }

                        if ($product['brand_name_en'] && !empty($product['brand_name_en'])) {
                            $brand = Brand::select('id')->where(['name'=>$product['brand_name_en'], 'main_category_id'=>$input['main_category_id']])->first();

                            if ($brand) {
                                $brand_id = $brand->id;

                            } else {
                                $brandData = new Brand;

                                if ($product['brand_image_path'] && !empty($product['brand_image_path'])) {
                                    $brandImgUrl = $product['brand_image_path'];
                                    $path      = parse_url($brandImgUrl, PHP_URL_PATH);       // get path from url
                                    $extension = pathinfo($path, PATHINFO_EXTENSION); // get ext from path
                                    $filename  = pathinfo($path, PATHINFO_FILENAME);  // get name from path
                                    $filenameWithExtension = $filename.time().'.'.$extension;

                                    $brandPath = public_path().'/uploads/brand/'.$filenameWithExtension;
                                    file_put_contents($brandPath, file_get_contents($brandImgUrl));
                                    chmod($brandPath,0777);

                                    $brandData->file_path = $filenameWithExtension;
                                    $brandData->file_name = $filenameWithExtension;
                                }

                                $brandData->main_category_id = $input['main_category_id'];
                                $brandData->name = $product['brand_name_en'];
                                $brandData->brand_type = 'Restaurant';
                                $brandData->type = 'Image';
                                $brandData->save();

                                $brand_id = $brandData->id;

                                //English Data
                                $brandLang = new BrandLang;
                                $brandLang->brand_id = $brandData->id;
                                $brandLang->name = $product['brand_name_en'];
                                $brandLang->lang = 'en';
                                $brandLang->save();

                                //Arabic Data
                                $brandLang = new BrandLang;
                                $brandLang->brand_id = $brandData->id;
                                $brandLang->name = $product['brand_name_ar'];
                                $brandLang->lang = 'ar';
                                $brandLang->save();
                            }
                        }

                        if ($product['email_unique'] && !empty($product['email_unique'])) {
                            $restaurant = Restaurant::select('id')->where(['email'=>$product['email_unique']])->first();

                            if ($restaurant) {
                                $restaurant_id = $restaurant->id;

                            } else {
                                $mobileWithCode = explode(')', $product['mobile_number_with_country_code_unique']);

                                if (isset($mobileWithCode[0]) && isset($mobileWithCode[1])) {
                                    $country_code = str_replace("(","",$mobileWithCode[0]);
                                    $country_code = str_replace("+","",$country_code);
                                    $mobile = trim($mobileWithCode[1]);

                                } else {
                                    $country_code = null;
                                    $mobile = null;
                                }
                                $password = 'Secure#445';

                                // create a user in users table
                                $user = User::create([
                                    'name' => $product['store_name_en'],
                                    'first_name' => $product['store_name_en'],
                                    'last_name' => '',
                                    'mobile' => $mobile,                    
                                    'email' => $product['email_unique'],
                                    'country_code' => $country_code,
                                    'password' => Hash::make($password),
                                    /*'address' => $product['address'],
                                    'latitude'=> $product['latitude'],
                                    'longitude'=> $product['longitude'],*/
                                    'email_verified_at'=> time(),
                                    'type' => '4',
                                    'status' => 1,
                                ]);

                                $restaurantData = new Restaurant;
                                $restaurantData->user_id = $user->id;
                                $restaurantData->name = $product['store_name_en'];
                                $restaurantData->brand_id = $brand_id;
                                $restaurantData->main_category_id = $input['main_category_id'];
                                $restaurantData->email = $product['email_unique'];
                                /*$restaurantData->address = $input['address'];
                                $restaurantData->latitude = $input['latitude'];
                                $restaurantData->longitude = $input['longitude'];*/
                                $restaurantData->password = Hash::make($password);
                                $restaurantData->phone_number = $mobile;
                                $restaurantData->country_code = $country_code;
                                $restaurantData->landline = $product['landline'] ?? null;
                                $restaurantData->admin_comission = $product['admin_commission'];
                                $restaurantData->is_featured = $product['is_featured'] == 'Yes' ? 1 : 0;
                                $restaurantData->cost_for_two_price = $product['price_for_two'] ?? null;
                                $restaurantData->min_order_amount = $product['minimum_order_amount'];
                                $restaurantData->is_kilo_points_promotor = 0;
                                $restaurantData->buy_one_get_one = 0;

                                if ($restaurantData->save()) {
                                    //Restro Id
                                    $restaurant_id = $restaurantData->id;

                                    //Restro Modes
                                    $modes = explode(',', $product['modes_csv']);

                                    if ($modes && !empty($modes)) {

                                        foreach ($modes as $key => $value) {
                                            $modeIdOnly = trim($value);
                                            // dd($modeIdOnly);

                                            if ($modeIdOnly == '011' || $modeIdOnly == '012') {
                                                $modeIds[] = 1;

                                                if ($modeIdOnly == '011') {
                                                    $payment_mode[] = 'pay_in_advance';
                                                }

                                                if ($modeIdOnly == '012') {
                                                    $payment_mode[] = 'pay_on_finish';
                                                }
                                            }

                                            if ($modeIdOnly == '021' || $modeIdOnly == '022') {
                                                $modeIds[] = 2;

                                                if ($modeIdOnly == '021') {
                                                    $pickup_mode[] = 'In_car';
                                                }

                                                if ($modeIdOnly == '022') {
                                                    $pickup_mode[] = 'In_restaurant';
                                                }
                                            }
                                        }
                                        $modeIds = array_unique($modeIds);
                                        $payment_mode = array_unique($payment_mode);
                                        $pickup_mode = array_unique($pickup_mode);

                                        foreach ($modeIds as $key => $value) {
                                            $modeAssign = new RestaurantMode;
                                            $modeAssign->restaurant_id = $restaurant_id;
                                            $modeAssign->mode_id = $value;
                                            
                                            if ($value==1) {
                                                $modeAssign->mode_type = json_encode($payment_mode);

                                            } else {
                                                $modeAssign->mode_type = json_encode($pickup_mode);                                
                                            }
                                            $modeAssign->save();
                                        }
                                    }

                                    //English
                                    $restroLangData = new RestaurantLang;
                                    $restroLangData->restaurant_id = $restaurant_id;
                                    $restroLangData->name = $product['store_name_en'];
                                    $restroLangData->lang = 'en';
                                    $restroLangData->save();

                                    //Arabic
                                    $restroLangData = new RestaurantLang;
                                    $restroLangData->restaurant_id = $restaurant_id;
                                    $restroLangData->name = $product['store_name_ar'];
                                    $restroLangData->lang = 'ar';
                                    $restroLangData->save();
                                }
                            }
                        }

                        if ($input['main_category_id'] == 2) {
                            //Food Restros.....

                            /*if ($product['product_image_csv'] && !empty($product['product_image_csv'])) {
                                $productImgUrl = explode(',', $product['product_image_csv']);

                                foreach ($productImgUrl as $k_pImg => $v_pImg) {
                                    $path      = parse_url($v_pImg, PHP_URL_PATH);       // get path from url
                                    $extension = pathinfo($path, PATHINFO_EXTENSION); // get ext from path
                                    $filename  = pathinfo($path, PATHINFO_FILENAME);  // get name from path
                                    $filenameWithExtension = $filename.time().'.'.$extension;
                                    $productPath = public_path().'/uploads/product/'.$filenameWithExtension;
                                    file_put_contents($productPath, file_get_contents($v_pImg));
                                    chmod($productPath,0777);

                                    if ($k_pImg == 0) {
                                        $productImage = $filenameWithExtension;

                                    } else {
                                        $modelProductImages = new ProductImages();
                                        $modelProductImages->image = $filenameWithExtension;
                                        $modelProductImages->product_id = 999;
                                        $modelProductImages->save();
                                    }
                                }
                            }*/

                            if ($product['dish_name_en']) {
                                $product = Products::select('id')->where(['name'=>$product['dish_name_en'], 'restaurant_id'=>$restaurant_id])->first();

                                if (!$product) {
                                    $productData = new  Products;
                                    $productData->name = $product['dish_name_en'] ?? null;
                                    $productData->sku_code = $product['sku_code'] ?? null;
                                    $productData->long_description = $product['description_en'];
                                    $productData->product_for = 'dish';

                                    $productData->main_category_id = $input['main_category_id'];
                                    $productData->brand_id = $input['brand_id'];
                                    $productData->products_type='dish';
                                    $productData->restaurant_id=$input['restaurant_id'];
                                    
                                    $productData->prepration_time = $input['prepration_time'];
                                    $productData->discount_price=$input['discount_price'];
                                    $productData->total_amount=$input['price'];
                                    $productData->video=$input['video'];

                                    if ($input['discount_price'] && !empty($input['discount_price'])) {
                                        $productData->points = ($input['points'] / 100) * $input['discount_price'];

                                    } else {
                                        $productData->points = ($input['points'] / 100) * $input['price'];
                                    }

                                    if ($input['discount_price'] && !empty($input['discount_price'])) {
                                        $productData->extra_kilopoints = ($input['extra_kilopoints'] / 100) * $input['discount_price'];

                                    } else {
                                        $productData->extra_kilopoints = ($input['extra_kilopoints'] / 100) * $input['price'];
                                    }

                                    $productData->points_percent=$input['points'];
                                    $productData->extra_kilopoints_percent=$input['extra_kilopoints'];
                                    $productData->price=$input['price'];
                                    $productData->category_id=$input['category_id'];

                                    if (isset($input['customization'])) {
                                        $productData->customization = 'Yes';
                                        $productData->customize_option = $input['customize_option'];

                                    } else {
                                        $productData->customization='No';
                                    }
                                    $productData->is_active= 1;
                                    $productData->save();


                                    $data->name = $product['dish_name_en'] ?? null;
                                    $data->sku_code = $product['sku_code'] ?? null;
                                    $data->long_description = $product['descriptionen'] ?? null;
                                    $data->product_for = $input['product_for'];
                                    $data->main_category_id = $input['main_category_id'];
                                    $data->restaurant_id = $input['restaurant_id'] ?? null;
                                    $data->products_type = $product['type'] ?? null;
                                    $data->prepration_time = $product['prepration_time'] ?? null;
                                    $data->discount_price = $product['discount_price'] ?? null;
                                    $data->total_amount = $product['price'];
                                    $data->video = $product['video_url'];
                                    $data->points=$product['kilo_points'];
                                    $data->price = $product['price'];
                                    $data->category_id = $catgory_id;
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
                                }
                            }

                        } else {
                            //Other Store

                            /*if ($product['nameen']) {
                                $data = new  Products;
                                $data->name = $product['nameen'] ?? null;
                                $data->sku_code = $product['sku_code'] ?? null;
                                $data->long_description = $product['descriptionen'] ?? null;
                                $data->product_for = $input['product_for'];
                                $data->main_category_id = $input['main_category_id'];
                                $data->restaurant_id = $input['restaurant_id'] ?? null;
                                $data->shop_type = $product['shop_type'] ?? null;
                                $data->delivery_time = $product['delivery_time'] ?? null;
                                $data->delivery_hours = $product['delivery_hours'] ?? null;
                                $data->category_id = $catgory_id;
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
                            }*/
                        }
                    }
                    return back();
                }
            }
        } else {
            return back();
        }
        /*Excel::import(new BulkImport,request()->file('file'));
        return back();*/
    }

    public function importData(Request $request) 
    {
        $input = $request->all();
        $langData = Language::pluck('lang')->toArray();
        $login_user_data = auth()->user();
        $added_by = $login_user_data->id;
        $imageZipName = [];
        $imageUploadedNames = [];

        if (!empty($input['file']) && !empty($input['images_zip'])) {
            $zipImgRslt = file_upload($request->file('images_zip'), 'product');

            if ($zipImgRslt[0] == true) {

                $imageZip = new \ZipArchive();
                $extractedPath = public_path('uploads/product/');

                if ($imageZip->open(public_path($zipImgRslt[1]))) {

                    for ($i = 0; $i < $imageZip->numFiles; $i++) {
                        $filename = $imageZip->getNameIndex($i);
                        $imageZipName[] = pathinfo($filename);
                    }

                    $imageZip->extractTo($extractedPath);
                    $imageZip->close();
                }

                foreach ($imageZipName as $key => $value) {
                    $newImageName = time().'-'.$value['basename'];
                    $imageUploadedNames[$key]['old_name'] = $value['basename'];
                    $imageUploadedNames[$key]['new_name'] = $newImageName;
                    rename($extractedPath.'/'.$value['basename'], $extractedPath.'/'.$newImageName);
                }

                if ($imageUploadedNames && !empty($imageUploadedNames)) {

                    foreach ($imageUploadedNames as $k => $v) {
                        $zipImageDataSave = new ZipImages;
                        $zipImageDataSave->name = $v['old_name'];
                        $zipImageDataSave->new_name = $v['new_name'];
                        $zipImageDataSave->save();
                    }
                }
            }
        }

        if (!empty($input['file']) && !empty($input['main_category_id']) && !empty($input['brand_id']) && !empty($input['restaurant_id'])) {
            $imgRslt = file_upload($request->file('file'), 'product');

            if ($imgRslt[0] == true) {
                $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];

                if (!empty($excelData)) {
                    $n = 8;
                    $brand_id = $input['brand_id'];
                    $restaurant_id = $input['restaurant_id'];

                    foreach ($excelData as $key => $product) {
                        // dd($product);
                        $catgory_id = '';
                        $attribute_value_id = '';
                        $modeIds = array();
                        $payment_mode = array();
                        $pickup_mode = array();
                        $productImage = '';
                        $attribute_id = '';
                        $attribute_lang_id = '';
                        $attribute_value_lang_id = '';
                        $toppingId = '';

                        if ($product['category_of_item'] && !empty($product['category_of_item'])) {
                            $cat = Category::select('id')->where(['name'=>$product['category_of_item'], 'main_category_id'=>$input['main_category_id']])->first();

                            if ($cat) {
                                $catgory_id = $cat->id;
                            } else {
                                foreach($langData as $lang) {

                                    if ($lang=='en') {
                                        $categoryData = new Category;
                                        $categoryData->main_category_id = $input['main_category_id'];
                                        $categoryData->name = $product['category_of_item'];
                                        $categoryData->description = $product['category_of_item'];
                                        $categoryData->type = 1;
                                        $categoryData->status= 1;
                                        $categoryData->save();

                                        $catgory_id = $categoryData->id;
                                    }
                                    $dataLang = new CategoryLang;
                                    $dataLang->category_id = $categoryData->id;
                                    $dataLang->name = $product['category_of_item'];
                                    $dataLang->description = $product['category_of_item'];
                                    $dataLang->lang = $lang;
                                    $dataLang->save();
                                }
                            }
                        }

                        if ($input['main_category_id'] == 2) {
                            //Food Restros.....

                            if ($product['dish_name_en']) {
                                // $alreadyProduct = Products::select('id')->where(['name'=>$product['dish_name_en'], 'restaurant_id'=>$restaurant_id])->first();
                                $alreadyProduct = Products::select('id')->where(['sku_code'=>$product['sku_code']])->first();

                                if (!$alreadyProduct) {
                                    $makingTime = str_replace('min', '', $product['preparation_time']);

                                    $productData = new  Products;
                                    $productData->name = $product['dish_name_en'] ?? null;
                                    $productData->sku_code = $product['sku_code'] ?? null;
                                    $productData->long_description = $product['description_en'];
                                    $productData->product_for = 'dish';
                                    $productData->main_category_id = $input['main_category_id'];
                                    $productData->brand_id = $input['brand_id'];
                                    $productData->products_type = $product['vegnon_veg'];
                                    $productData->restaurant_id = $input['restaurant_id'];
                                    $productData->prepration_time = trim($makingTime);
                                    $productData->price = $product['original_price_qar'];
                                    $productData->discount_price = $product['discounted_price_qar'];
                                    $productData->total_amount = $product['original_price_qar'];
                                    $productData->video = $product['video_url'];
                                    $productData->points_percent = $product['kp'];
                                    $productData->extra_kilopoints_percent = $product['extra_kp'];
                                    $productData->category_id = $catgory_id;

                                    if ($product['discounted_price_qar'] && !empty($product['discounted_price_qar'])) {
                                        $productData->points = ($product['kp'] / 100) * $product['discounted_price_qar'];

                                    } else {
                                        $productData->points = ($product['kp'] / 100) * $product['original_price_qar'];
                                    }

                                    if ($product['discounted_price_qar'] && !empty($product['discounted_price_qar'])) {
                                        $productData->extra_kilopoints = ($product['extra_kp'] / 100) * $product['discounted_price_qar'];

                                    } else {
                                        $productData->extra_kilopoints = ($product['extra_kp'] / 100) * $product['original_price_qar'];
                                    }

                                    if (isset($product['customization']) && $product['customization'] == "Yes") {
                                        $productData->customization = 'Yes';
                                        $productData->customize_option = $product['customize_option'];

                                    } else {
                                        $productData->customization='No';
                                    }
                                    $productData->is_active= 1;
                                    
                                    if ($productData->save()) {
                                        //English
                                        $dataLang = new ProductLang;
                                        $dataLang->product_id = $productData->id;
                                        $dataLang->name = $product['dish_name_en'];
                                        $dataLang->long_description = $product['description_en'];
                                        $dataLang->lang = 'en';
                                        $dataLang->save();

                                        //Arabic
                                        $dataLang = new ProductLang;
                                        $dataLang->product_id = $productData->id;
                                        $dataLang->name = $product['dish_name_ar'];
                                        $dataLang->long_description = $product['description_ar'];
                                        $dataLang->lang = 'ar';
                                        $dataLang->save();

                                        //Product Images
                                        if ($product['product_image_csv'] && !empty($product['product_image_csv'])) {

                                            $productImgUrl = explode(',', $product['product_image_csv']);

                                            foreach ($productImgUrl as $k_pImg => $v_pImg) {
                                                $checkImageUploaded = ZipImages::where(['name'=>$v_pImg])->orderBy('id', 'desc')->first();

                                                if ($checkImageUploaded) {

                                                    if ($k_pImg == 0) {
                                                        $productImage = $checkImageUploaded->new_name;
                                                        $inputs['main_image'] = $productImage;
                                                        Products::where('id',$productData->id)->update($inputs);

                                                    } else {
                                                        $modelProductImages = new ProductImages();
                                                        $modelProductImages->image = $checkImageUploaded->new_name;
                                                        $modelProductImages->product_id = $productData->id;
                                                        $modelProductImages->save();
                                                    }
                                                }
                                            }
                                            /*$productImgUrl = explode(',', $product['product_image_csv']);

                                            foreach ($productImgUrl as $k_pImg => $v_pImg) {
                                                $path      = parse_url($v_pImg, PHP_URL_PATH);       // get path from url
                                                $extension = pathinfo($path, PATHINFO_EXTENSION); // get ext from path
                                                $filename  = pathinfo($path, PATHINFO_FILENAME);  // get name from path
                                                $filenameWithExtension = $filename.time().'.'.$extension;
                                                $productPath = public_path().'/uploads/product/'.$filenameWithExtension;
                                                file_put_contents($productPath, file_get_contents($v_pImg));
                                                chmod($productPath,0777);

                                                if ($k_pImg == 0) {
                                                    $productImage = $filenameWithExtension;
                                                    $inputs['main_image'] = $productImage;
                                                    Products::where('id',$productData->id)->update($inputs);

                                                } else {
                                                    $modelProductImages = new ProductImages();
                                                    $modelProductImages->image = $filenameWithExtension;
                                                    $modelProductImages->product_id = $productData->id;
                                                    $modelProductImages->save();
                                                }
                                            }*/
                                        }

                                        //Product Tags
                                        if (isset($product['tag_en_csv']) && !empty($product['tag_en_csv'])) {

                                            foreach ($langData as $lang) {
                                                $tagsArray = explode(",",$product['tag_'.$lang.'_csv']);

                                                foreach ($tagsArray as $k_tag => $v_tag) {

                                                    if ($v_tag && !empty($v_tag)) {
                                                        $dataTagValue = new ProductTags;
                                                        $dataTagValue->product_id = $productData->id;
                                                        $dataTagValue->tag = $v_tag;
                                                        $dataTagValue->lang = $lang;
                                                        $dataTagValue->save();
                                                    }
                                                }
                                            }
                                        }

                                        //Attribute Section goes here.....
                                        for ($x = 1; $x <= 4; $x++) {

                                            if (isset($product['attribute_'.$x])) {
                                                $checkAttributeExist = AttributesLang::select('attributes_lang.*')->join('attributes', 'attributes.id', '=', 'attributes_lang.attribute_id')->where(['main_category_id'=>$input['main_category_id'], 'category_id'=>$catgory_id, 'name' => $product['attribute_'.$x]])->first();

                                                if ($checkAttributeExist) {
                                                    $attribute_id = $checkAttributeExist->attribute_id;
                                                    $attribute_lang_id = $checkAttributeExist->id;

                                                } else {
                                                    $attributeData = new Attributes;
                                                    $attributeData->main_category_id = $input['main_category_id'];
                                                    $attributeData->category_id = $catgory_id;
                                                    $attributeData->added_by = $added_by;
                                                    
                                                    if ($attributeData->save()) {
                                                        $attribute_id = $attributeData->id;
                                                        
                                                        //English & Arabic
                                                        $dataAttributeLang = new AttributesLang();
                                                        $dataAttributeLang->attribute_id = $attributeData->id;
                                                        $dataAttributeLang->topping_choose = ($product['type_'.$x] == 'Single') ? 0 : 1;
                                                        $dataAttributeLang->is_color = ($product['is_color_'.$x] == 'No') ? 0 : 1;
                                                        $dataAttributeLang->name = $product['attribute_'.$x];
                                                        $dataAttributeLang->lang = $product['attribute_'.$x];
                                                        $dataAttributeLang->save();

                                                        $attribute_lang_id = $dataAttributeLang->id;
                                                    }
                                                }

                                                if ($attribute_id) {
                                                    $checkAttributeValueExist = AttributeValueLang::select('attribute_value_lang.*')->join('attribute_values', 'attribute_values.id', '=', 'attribute_value_lang.attribute_value_id')->where(['main_category_id'=>$input['main_category_id'], 'category_id'=>$catgory_id, 'name' => $product['attribute_value_'.$x]])->first();

                                                    if ($checkAttributeValueExist) {
                                                        $attribute_value_id = $checkAttributeValueExist->attribute_value_id;
                                                        $attribute_value_lang_id = $checkAttributeValueExist->id;

                                                    } else {
                                                        $attributeValueData = new AttributeValues;
                                                        $attributeValueData->main_category_id = $input['main_category_id'];
                                                        $attributeValueData->category_id = $catgory_id;
                                                        $attributeValueData->attributes_lang_id = $attribute_id;
                                                        $attributeValueData->added_by = $added_by;

                                                        if ($attributeValueData->save()) {
                                                            $attribute_value_id = $attributeValueData->id;

                                                            //English & Arabic
                                                            $attributeValuedataLang = new AttributeValueLang();
                                                            $attributeValuedataLang->attribute_value_id = $attribute_value_id;
                                                            $attributeValuedataLang->name = $product['attribute_value_'.$x];
                                                            $attributeValuedataLang->lang = $product['attribute_value_'.$x];

                                                            if (isset($product['color_code_'.$x]) && !empty($product['color_code_'.$x])) {
                                                                $attributeValuedataLang->color_code = $product['color_code_'.$x];

                                                            } else {
                                                                $attributeValuedataLang->color_code = null;
                                                            }
                                                            $attributeValuedataLang->save();

                                                            $attribute_value_lang_id = $attributeValuedataLang->id;
                                                        }
                                                    }
                                                }

                                                //Attribute Insert
                                                $checkToppingExist = Topping::where(['main_category_id'=>$input['main_category_id'],'category_id'=>$catgory_id,'dish_id'=>$productData->id])->first();

                                                if ($checkToppingExist) {
                                                    $toppingId = $checkToppingExist->id;

                                                } else {
                                                    $toppingData = new Topping;
                                                    $toppingData->main_category_id = $input['main_category_id'];
                                                    $toppingData->category_id = $catgory_id;
                                                    $toppingData->dish_id = $productData->id;
                                                    $toppingData->save();
                                                    $toppingId = $toppingData->id;
                                                }

                                                if ($toppingId) {
                                                    $dataAttr = new ProductAttributes;
                                                    $dataAttr->product_id = $productData->id;
                                                    $dataAttr->dish_topping_id = $toppingId;
                                                    $dataAttr->is_mandatory = 1;
                                                    $dataAttr->is_free = 0;
                                                    $dataAttr->price = $product['attribute_price_'.$x] ?? null;

                                                    if($dataAttr->save()) {
                                                        $dataAttrValue = new ProductAttributeValues;
                                                        $dataAttrValue->product_attributes_id = $dataAttr->id;
                                                        $dataAttrValue->attributes_lang_id = $attribute_lang_id;
                                                        $dataAttrValue->attribute_value_lang_id = $attribute_value_lang_id;
                                                        $dataAttrValue->save();
                                                    }
                                                }
                                                //End Attribute Section
                                            }
                                        }
                                    }
                                }
                            }

                        } else {
                            //Other Store

                            /*if ($product['nameen']) {
                                $data = new  Products;
                                $data->name = $product['nameen'] ?? null;
                                $data->sku_code = $product['sku_code'] ?? null;
                                $data->long_description = $product['descriptionen'] ?? null;
                                $data->product_for = $input['product_for'];
                                $data->main_category_id = $input['main_category_id'];
                                $data->restaurant_id = $input['restaurant_id'] ?? null;
                                $data->shop_type = $product['shop_type'] ?? null;
                                $data->delivery_time = $product['delivery_time'] ?? null;
                                $data->delivery_hours = $product['delivery_hours'] ?? null;
                                $data->category_id = $catgory_id;
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
                            }*/
                        }
                    }
                    $result['message'] = 'Excel imported successfully';
                    $result['status'] = 1;
                    return response()->json($result);
                    // return back();
                }
            }
        } else {
            $result['message'] = 'Please fill all mandatory field.';
            $result['status'] = 0;
            return response()->json($result);
            // return back();
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

    public function show_attribute_value($attribute_id)
    {
        Gate::authorize('Product-section');

        if ($attribute_id) {
            $data['attribute_id'] = $attribute_id;
            $data['attribute_values']=AttributeValueLang::select('attribute_value_lang.*', 'attributes_lang.name as attribute_name')->join('attribute_values', 'attribute_values.id', '=', 'attribute_value_lang.attribute_value_id')->join('attributes_lang', 'attributes_lang.id', '=', 'attribute_values.attributes_lang_id')->where(['attribute_values.status'=>1, 'attribute_values.attributes_lang_id' => $attribute_id])->get()->toArray();

        } else {
            $data['attribute_id'] = '';
        }
        return view('product.attribute_values',$data);
    }

    public function showTags()
    {
        $tags=ProductTags::select('id','tag')->where(['status'=>1, 'lang'=>'en'])->groupBy('tag')->get()->toArray();
        $result = json_encode($tags);
        return $result;
    }

    public function showTagsAr()
    {
        $tags=ProductTags::select('id','tag')->where(['status'=>1, 'lang'=>'ar'])->groupBy('tag')->get()->toArray();
        $result = json_encode($tags);
        return $result;
    }
}
