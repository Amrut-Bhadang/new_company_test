<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Topping;
use App\Models\Products;
use App\Models\Category;
use App\Models\MainCategory;
use App\Models\ToppingCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File,DB;
use App\Models\ToppingLang;
use App\Models\Restaurant;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DishToppingExport;
use App\Exports\ItemSpecificsExport;
use App\Imports\BulkImport;
use App\Models\Attributes;
use App\Models\AttributesLang;
use App\Models\AttributeValueLang;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
class ToppingController extends Controller
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
        Gate::authorize('Toppings-section');
        $login_user_data = auth()->user();
        $columns = ['dish_toppings.topping_name', 'products.name'];

        if ($login_user_data->type == 4) {
            $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
            
            $category=Topping::select('dish_toppings.topping_name','dish_toppings.price','dish_toppings.created_at','dish_toppings.id','dish_toppings.status','products.name as dish')->join('products','products.id','=','dish_toppings.dish_id')->where('products.restaurant_id', $restaurant_detail->id);
         
        } else {
            $category=Topping::select('dish_toppings.*','products.name as dish')->join('products','products.id','=','dish_toppings.dish_id');
        }

        /*foreach ($category as $key => $value) {
          $value->productAttr = ProductAttributeValues::select('product_attribute_values.attribute_value_lang_id','attribute_value_lang.name')->join('product_attributes','product_attributes.id','=','product_attribute_values.product_attributes_id')->join('attribute_value_lang','attribute_value_lang.id','=','product_attribute_values.attribute_value_lang_id')->where(['dish_topping_id' => $value->id])->pluck('name')->toArray();
        }*/

        // dd($category->get());
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
    				$query->whereBetween(DB::raw('DATE(dish_toppings.created_at)'), array($request->from_date, $request->to_date));
    			}

          if ($request->has('main_category_id')) {
              $main_category_id = array_filter($request->main_category_id);
              if(count($main_category_id) > 0) {
                  $query->whereIn('dish_toppings.main_category_id', $request->get('main_category_id'));
              }
          }

          if ($request->has('category_id')) {
              $category_id = array_filter($request->category_id);
              if(count($category_id) > 0) {
                  $query->whereIn('dish_toppings.category_id', $request->get('category_id'));
              }
          }

          if ($request->has('product_id')) {
              $product_id = array_filter($request->product_id);
              if(count($product_id) > 0) {
                  $query->whereIn('dish_toppings.dish_id', $request->get('product_id'));
              }
          }

          if (!empty($request->get('search'))) {
             $search = $request->get('search');
             $query->having('products.name', 'like', "%{$search['value']}%");
          }
		    })->addIndexColumn()->make(true);
        
    }
    
    public function frontend()
    {
        Gate::authorize('Toppings-section');
        $login_user_data = auth()->user();

        if($login_user_data->type == 4) {
          $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
          $data['products'] = Products::where('restaurant_id',$restaurant_detail->id)->get();
        } else {
          $data['products'] = Products::all();
        }
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['category']=Category::select('name','id')->where(['type'=>1, 'status'=>1])->get();
        $data['products']=Products::select('name','id')->where(['is_deleted'=>0, 'is_active'=>1])->get();

        $data['topping_category'] = ToppingCategory::all();
        return view('topping.listing',$data);
    }

    public function show_category($main_category_id, $category_id='') {

      if ($main_category_id) {
        $data['records'] = Category::select('id','name')->where(['status'=>1, 'type'=>1, 'main_category_id'=>$main_category_id])->get();
        // $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where('attributes.category_id', $main_category_id)->get();
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      $data['category_id'] = $category_id;
      return view('topping.category',$data);

    }

    public function show_category_popup($main_category_id, $category_id='') {

      if ($main_category_id) {
        $data['records'] = Category::select('id','name')->where(['status'=>1, 'type'=>1, 'main_category_id'=>$main_category_id])->get();
        // $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where('attributes.category_id', $main_category_id)->get();
        $data['main_category_id'] = $main_category_id;

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
      }
      $data['category_id'] = $category_id;
      return view('topping.category_popup',$data);

    }

    public function show_attributes($main_category_id, $category_id, $dish_id = '') {

      if ($main_category_id) {
        $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.main_category_id'=>$main_category_id, 'attributes.category_id'=>$category_id])->get();
        $data['main_category_id'] = $main_category_id;
        $data['category_id'] = $main_category_id;

        foreach ($data['records'] as $key => $value) {
          $value->attribute_values = AttributeValueLang::select('attribute_value_lang.name','attribute_value_lang.id')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where('attribute_values.attributes_lang_id', $value->id)->get();
        }

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
        $data['category_id'] = '';
      }

      if ($dish_id) {
        $data['product_attributes'] = ProductAttributes::select('product_attributes.*')->where(['product_id' => $dish_id])->get();

        if ($data['product_attributes']) {

          foreach ($data['product_attributes'] as $k => $v) {
            $v->selected_attr_values = ProductAttributeValues::where(['product_attributes_id' => $v->id])->pluck('attribute_value_lang_id')->toArray();
          }
        }
      } else {
        $data['product_attributes'] = array();
      }
      // echo "<pre>"; print_r($data['product_attributes']);die;

      if ($dish_id) {

        if (count($data['product_attributes'])) {
          return view('topping.attribute_edit_list',$data);

        } else {
          return view('topping.attribute_list',$data);
        }

      } else {
        return view('topping.attribute_list',$data);
      }

    }

    public function show_single_attributes($main_category_id, $category_id, $option_type, $dish_id = '') {

      if ($main_category_id) {
        $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where(['attributes.main_category_id'=>$main_category_id, 'attributes.category_id'=>$category_id])->get();
        $data['main_category_id'] = $main_category_id;
        $data['category_id'] = $main_category_id;

        foreach ($data['records'] as $key => $value) {
          $attribute_values = AttributeValueLang::select('attribute_value_lang.name','attribute_value_lang.id')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where('attribute_values.attributes_lang_id', $value->id);

          $value->attribute_values = $attribute_values->get();

          $attribute_valuesIds = $attribute_values->pluck('id')->toArray();

          if ($dish_id) {

            foreach ($value->attribute_values as $k => $v) {
              $value->product_attributes = ProductAttributes::select('product_attributes.*')->join('product_attribute_values', 'product_attribute_values.product_attributes_id', 'product_attributes.id')->where(['product_id' => $dish_id])->where('product_attribute_values.attributes_lang_id', $value->id)->get();

              if ($value->product_attributes && isset($value->product_attributes[0])) {

                foreach ($value->product_attributes as $k_1 => $v_1) {
                  $v_1->selected_attr_values = ProductAttributeValues::where(['product_attributes_id' => $v_1->id])->pluck('attribute_value_lang_id')->toArray();
                }

              } else {
                $value->product_attributes = $value->attribute_values;

                foreach ($value->product_attributes as $k_2 => $v_2) {
                  $v_2->selected_attr_values = ProductAttributeValues::where(['product_attributes_id' => $v_2['id']])->pluck('attribute_value_lang_id')->toArray();
                }
              }
            }
          }
        }

      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
        $data['category_id'] = '';
      }

      if ($dish_id) {
        $data['product_attributes'] = ProductAttributes::select('product_attributes.*')->where(['product_id' => $dish_id])->get();

        if ($data['product_attributes']) {

          foreach ($data['product_attributes'] as $k => $v) {
            $v->selected_attr_values = ProductAttributeValues::where(['product_attributes_id' => $v->id])->pluck('attribute_value_lang_id')->toArray();
          }
        }
      } else {
        $data['product_attributes'] = array();
      }
      // echo "<pre>"; print_r($data['records']->toArray());
      // echo "<pre>"; print_r($data['product_attributes']);
      // die;

      if ($dish_id) {

        if ($option_type != 'single') {
          return view('topping.attribute_edit_list',$data);

        } else {
          return view('topping.single_attribute_edit_list',$data);
        }

      } else {
        return view('topping.single_attribute_list',$data);
      }

    }

    public function show_attribute_values($main_category_id, $category_id, $count, $customizeType='') {

      if ($main_category_id) {

        if ($customizeType) {
          $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where('attributes.main_category_id', $main_category_id)->where('attributes.category_id', $category_id)->where('attributes_lang.id', $customizeType)->get();

        } else {
          $data['records'] = AttributesLang::select('attributes_lang.name','attributes_lang.id')->join('attributes','attributes.id','=','attributes_lang.attribute_id')->where('attributes.main_category_id', $main_category_id)->where('attributes.category_id', $category_id)->get();
        }
        $data['main_category_id'] = $main_category_id;
        $data['category_id'] = $category_id;

        foreach ($data['records'] as $key => $value) {
          $value->attribute_values = AttributeValueLang::select('attribute_value_lang.name','attribute_value_lang.id')->join('attribute_values','attribute_values.id','=','attribute_value_lang.attribute_value_id')->where('attribute_values.attributes_lang_id', $value->id)->get();
        // $data['main_category_id'] = $main_category_id;
        }

        $data['count'] = $count;
      } else {
        $data['records'] = array();
        $data['main_category_id'] = '';
        $data['category_id'] = '';
      }
      // echo "<pre>";print_r($data['records']->toArray()); die;
      $data['customizeType'] = $customizeType;
      // echo "<pre>"; print_r($data);die;

      if ($customizeType) {
        return view('topping.single_attribute_value_td',$data);

      } else {
        return view('topping.attribute_value_td',$data);
      }

    }

    public function show_dishes($category_id, $dish_id = '') {

      $login_user_data = auth()->user();

      if ($category_id) {

        if ($login_user_data->type == 4) {
          $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
          $data['records'] = Products::where('category_id',$category_id)->where('restaurant_id',$restaurant_detail->id)->get();

        } else {
          $data['records'] = Products::where('category_id',$category_id)->get();
        }
        $data['category_id'] = $category_id;

      } else {
        $data['records'] = array();
        $data['category_id'] = '';
      }


      $data['dish_id'] = $dish_id;
      return view('topping.dish_list',$data);

    }

    public function show_category_byIds($main_category_id) {

        $main_category_ids = explode(",",$main_category_id);

        if ($main_category_id) {
            $data['records'] = Category::select('id','name')->where(['status'=>1, 'type'=>1])->whereIn('main_category_id', $main_category_ids)->get();
            $data['main_category_id'] = $main_category_id;

        } else {
            $data['records'] = array();
            $data['main_category_id'] = '';
        }
        return view('topping.filtered_category',$data);
    }

    public function show_productsByMainCatIds($category_id) {
      $category_ids = explode(",",$category_id);

      if ($category_id) {
        $data['records'] = Products::whereIn('category_id', $category_ids)->get();
        $data['category_id'] = $category_id;

      } else {
        $data['records'] = array();
        $data['category_id'] = '';
      }
      return view('topping.dish_filter_list',$data);

    }

    public function edit_frontend($id)
    {
        Gate::authorize('Toppings-edit');
        /*$data['category'] = Topping::findOrFail($id);
        $data['products'] = Products::all();
        $data['topping_category'] = ToppingCategory::all();*/
        $data['category']=Topping::select('*')
                ->where('id',$id)
                ->first();
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        $data['product_attributes'] = ProductAttributes::select('product_attributes.*')->where(['product_id' => $data['category']->dish_id])->count();
        return view('topping.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Toppings-section');
        $login_user_data = auth()->user();

        if($login_user_data->type == 4) {
          $restaurant_detail = Restaurant::select('name','id','user_id')->where(['status'=>1, 'user_id'=>$login_user_data->id])->first();
          $data['products'] = Products::where('restaurant_id',$restaurant_detail->id)->get();
        } else {
          $data['products'] = Products::all();
        }
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();

        $data['topping_category'] = ToppingCategory::all();
        return view('topping.add',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Toppings-create');
         // validate
        $mesasge = [
            // 'name.en.required'=>'The Name(English) field is required.',
            // 'name.ar.required'=>'The Name(Arabic) field is required.',
            'main_category_id'=>'The Main Category field is required.',
            'category_id'=>'The Category field is required.',
            'dish_id'=>'The Dish field is required.',
            // 'is_mandatory'=>'The Mandatory field is required.',
          ];
          $this->validate($request, [
               // 'name.en'  => 'required|max:255',
               // 'name.ar'  => 'required|max:255',
               'main_category_id'  => 'required',
               'category_id'  => 'required',
               'dish_id'  => 'required',
               // 'is_mandatory'  => 'required',
          ],$mesasge);
        $input = $request->all();

        $fail = false;
        $message = '';

        /*if ($input['is_mandatory'] == '0') {
          if (empty($input['price'])) {
                $fail = true;
                $message = 'Price field is required.';
          }  
        }*/

        // echo "<pre>"; print_r($input['attribute']);die;
        // foreach ($input['attribute'] as $key => $value) {
        // }

        $checkAttributeExist = Topping::select('id')->where(['main_category_id'=>$input['main_category_id'], 'category_id'=>$input['category_id'],'dish_id'=>$input['dish_id']])->first();

        if ($checkAttributeExist) {
            $fail = true;
            $message = 'Topping already exist with same categories and dish.';
        }

        if(!$fail) {
          try{
              $lang = Language::pluck('lang')->toArray();
              // $top_choose = ToppingCategory::select('topping_choose')->where('id',$input['topping_category_id'])->first();
              foreach($lang as $lang){
                  if($lang=='en')
                  {
                      $data = new Topping;
                      // $data->topping_name = $input['name'][$lang];
                      $data->main_category_id = $input['main_category_id'];
                      $data->category_id = $input['category_id'];
                      $data->dish_id = $input['dish_id'];
                      // $data->is_mandatory = $input['is_mandatory'];
                      // $data->topping_category_choose = $top_choose->topping_choose;
                      /*if($input['is_mandatory'] == '0'){
                        $data->price = $input['price'];
                      } else {
                        $data->price = '0';
                      }*/
                      $data->save();
                  }
                  /*$dataLang = new  ToppingLang;
                  $dataLang->dish_topping_id = $data->id;
                  $dataLang->name = $input['name'][$lang];
                  $dataLang->lang = $lang;
                  $dataLang->save();*/
              }

              if ($data->id) {

                if (isset($input['attribute']) && !empty($input['attribute'])) {

                    foreach ($input['attribute'] as $key => $value) {
                        $dataAttr = new ProductAttributes;
                        $dataAttr->product_id = $input['dish_id'];
                        $dataAttr->dish_topping_id = $data->id;
                        // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                        // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                        $dataAttr->is_mandatory = $value['is_mandatory'];
                        $dataAttr->is_free = $value['is_free'];

                        if ($value['is_free'] == 1) {
                          $dataAttr->price = null;
                          $dataAttr->discount_price = null;

                        } else {
                          $dataAttr->price = $value['price'];
                          $dataAttr->discount_price = $value['discount_price'];
                        }
                        $dataAttr->points = $value['points'];

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
              $result['message'] = 'Topping has been created';
              $result['status'] = 1;
              return response()->json($result);
          } catch (Exception $e){
              $result['message'] = 'Topping Can`t created';
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        Gate::authorize('Toppings-section');
        $data['category'] = Topping::findOrFail($id);
        $data['products'] = Products::where('id',$data['category']->dish_id)->first();
        $data['topping_category'] = ToppingCategory::where('id',$data['category']->topping_category_id)->first();
        return view('topping.view',$data);
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
        Gate::authorize('Toppings-edit');
        $Category = Topping::findOrFail($id);
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
        Gate::authorize('Toppings-edit');
        // validate
		$mesasge = [
            // 'name.en.required'=>'The Name(English) field is required.',
            // 'name.ar.required'=>'The Name(Arabic) field is required.',
            'main_category_id'=>'The Main Category field is required.',
            'category_id'=>'The Category field is required.',
            'dish_id'=>'The Dish field is required.',
            // 'is_mandatory'=>'The Mandatory field is required.',
          ];
          $this->validate($request, [
               // 'name.en'  => 'required|max:255',
               // 'name.ar'  => 'required|max:255',
               'main_category_id'  => 'required',
               'category_id'  => 'required',
               'dish_id'  => 'required',
               // 'is_mandatory'  => 'required',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;

        $fail = false;
        $message = '';
          
        // if($input['is_mandatory'] == '0'){
        //   if (empty($input['price'])) {
        //     $fail = true;
        //     $mesasge = 'Price fiels is required.';
        //   }
        // }

        $checkAttributeExist = Topping::select('id')->where('id', '!=', $cate_id)->where(['main_category_id'=>$input['main_category_id'], 'category_id'=>$input['category_id'],'dish_id'=>$input['dish_id']])->first();

        if ($checkAttributeExist) {
            $fail = true;
            $mesasge = 'Topping already exist with same categories and dish.';
        }

        if(!$fail) {


          // if($input['is_mandatory'] == '0'){
          //   $price = $input['price'];
          // } else {
          //   $price = '0';
          // }
          try{
              $lang = Language::pluck('lang')->toArray();
              // $top_choose = ToppingCategory::select('topping_choose')->where('id',$input['topping_category_id'])->first();
              foreach($lang as $lang)
              {
                  if($lang=='en')
                  {
                      $data = Topping::where('id',$cate_id)->update(['main_category_id'=>$input['main_category_id'],'category_id'=>$input['category_id'],'dish_id'=>$input['dish_id']]);                    
                  }
                  /*$dataLang = ToppingLang::where(['dish_topping_id'=>$cate_id,'lang'=>$lang])->first();
                  if(isset($dataLang))
                  {
                     $dataLang = ToppingLang::where(['dish_topping_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);                                   
                  }
                  else
                  {
                      $dataLang = new  ToppingLang;
                      $dataLang->dish_topping_id = $cate_id;
                      $dataLang->name = $input['name'][$lang];
                      $dataLang->lang = $lang;
                      $dataLang->save();
                  }*/
              }

              //delete old product attributes
              ProductAttributes::where('product_id',$input['dish_id'])->delete();

              if (isset($input['attribute']) && !empty($input['attribute'])) {

                  foreach ($input['attribute'] as $key => $value) {
                      $dataAttr = new ProductAttributes;
                      $dataAttr->product_id = $input['dish_id'];
                      $dataAttr->dish_topping_id = $cate_id;
                      // $dataAttr->attributes_lang_id = $value['attributes_lang_id'];
                      // $dataAttr->attribute_value_lang_id = $value['attribute_value_lang_id'];
                      $dataAttr->is_mandatory = $value['is_mandatory'];
                      $dataAttr->is_free = $value['is_free'];

                      if ($value['is_free'] == 1) {
                        $dataAttr->price = null;
                        $dataAttr->discount_price = null;

                      } else {
                        $dataAttr->price = $value['price'];
                        $dataAttr->discount_price = $value['discount_price'];
                      }
                      $dataAttr->points = $value['points'];

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
              $result['message'] = 'Topping updated successfully.';
              $result['status'] = 1;
              return response()->json($result);
          }
          catch (Exception $e)
          {
              $result['message'] = 'Topping Can`t be updated.';
              $result['status'] = 0;
              return response()->json($result);           
          }
        } else {
          $result['message'] = $mesasge;
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
        Gate::authorize('Toppings-delete');
        if(Topping::findOrFail($id)->delete()){
            $result['message'] = 'Topping  deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Topping  Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {
        $details = Topping::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = Topping::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Topping is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Topping is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Topping status can`t be updated!!';
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
        return Excel::download(new DishToppingExport, 'Dish_Topping.csv');
    }

    public function exportSampleFileForSpecifics($main_category_id, $category_id)
    {
        Gate::authorize('Users-section');
        return Excel::download(new ItemSpecificsExport, 'ItemSpecifics.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }

    public function import(){
        $data['main_category']=MainCategory::select('name','id')->where(['status'=>1])->get();
        return view('topping.import',$data);
    }
}
