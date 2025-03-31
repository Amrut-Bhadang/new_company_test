<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\GiftLang;
use App\Models\GiftImage;
use App\Models\GiftBrand;
use App\Models\GiftBrandLang;
use App\Models\GiftCategory;
use App\Models\GiftSubCategory;
use App\Models\GiftCategoryLang;
use App\Models\GiftSubCategoryLang;
use App\Models\Brand;
use App\Models\GiftZipImages;
use App\Models\GiftVarient;
use App\Models\GiftVarientLang;
use App\Models\GiftTopping;
use App\Models\GiftProductAttributes;
use App\Models\GiftProductAttributeValues;
use App\Models\GiftAttributes;
use App\Models\GiftAttributesLang;
use App\Models\GiftAttributeValues;
use App\Models\GiftAttributeValueLang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use File;
use DB;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftExport;
use App\Imports\BulkImport;

class GiftController extends Controller
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
        Gate::authorize('Gift-section');
        $columns = ['gifts.name'];

        $Gift=Gift::select('name','created_at','id','sku_code','is_active')->where('is_deleted',0);
        return Datatables::of($Gift)->editColumn('created_at', function ($Gift) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($Gift->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

    			if(!empty($request->from_date) && !empty($request->to_date)) {
    				$query->whereBetween(DB::raw('DATE(gifts.created_at)'), array($request->from_date, $request->to_date));
    			}

          if ($request->has('brand_id')) {
              $brand_id = array_filter($request->brand_id);

              if(count($brand_id) > 0) {
                  $query->whereIn('gifts.brand_id', $request->get('brand_id'));
              }
          }

          if ($request->has('category_id')) {
              $category_id = array_filter($request->category_id);

              if(count($category_id) > 0) {
                  $query->whereIn('gifts.category_id', $request->get('category_id'));
              }
          }

          if ($request->has('sub_category_id')) {
              $sub_category_id = array_filter($request->sub_category_id);

              if(count($sub_category_id) > 0) {
                  $query->whereIn('gifts.sub_category_id', $request->get('sub_category_id'));
              }
          }

          if (!empty($request->get('search'))) {
             $search = $request->get('search');
             $query->having('gifts.name', 'like', "%{$search['value']}%");
          }
    	})->addIndexColumn()->make(true);
    }


    public function frontend()
    {
        Gate::authorize('Gift-section');
        $data['brands']  = GiftBrand::select('name','id')->get();
        $data['category']=GiftCategory::select('name','id')->where('type',2)->get();
        $data['subcategory']=array();
        $category=GiftSubCategory::select('name','id')->get();
        $data['record'] = $category;
        return view('gift.listing', $data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Gift-edit');
        $data['gift'] = Gift::findOrFail($id);
        $data['brands']  = GiftBrand::select('name','id')->get();
        $data['category']=GiftCategory::select('name','id')->where('type',2)->get();
        $data['lang']=Language::pluck('lang')->toArray();
        $data['varient']=GiftVarient::select('name','id')->where('gift_id',$id)->get();
        $data['selected_attributes_lang']=GiftProductAttributes::where(['gift_id' => $id])->pluck('attributes_lang_id')->toArray();
        return view('gift.edit',$data);
    }


    public function imageView(Request $request)
    {
        Gate::authorize('Gift-section');
        $id = $request->segment(3);
        $data['gift'] = Gift::findOrFail($id);
        $data['giftImage'] = GiftImage::select('image','id')->where('gift_id',$data['gift']->id)->get();
        return response()->json($data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('Gift-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Gift-create');
        // create a new task based on Content tasks relationship
        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'name.required'=>'The Category field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The nameEnglish may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'description.en'=>'required',
               'description.ar'=>'required',
               'category_id' => 'required',
               'points' =>'required',
               'brand' =>'required',
               'weight' =>'required',
               'quantity' =>'required',
               // 'gift_discount' =>'required',
               'sku_code' =>'required',
               'category_id' =>'required',
               'is_ready' =>'required',
               'delivery_hours' =>'required',
               'image' => 'image|required|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
          $input = $request->all();

          $fail = false;
          $message = '';

          if (isset($input['customization'])) {

            if (!isset($input['customize_option'])) {

                if (empty($input['customize_option'])) {
                    $fail = true;
                    $message = 'The customize option field is required';
                }
            }
          }

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

          if(!$fail){

            try{
              $lang = Language::pluck('lang')->toArray();
              $category = Gift::where(['brand_id'=>$input['brand'], 'category_id'=>$input['category_id'], 'sub_category_id'=>$input['sub_category_id'], 'sku_code'=>$input['sku_code']])->where('name', $input['name']['en'])->first();

              if (!$category) {
                $checkSKU = Gift::where(['sku_code'=>$input['sku_code']])->first();

                if ($checkSKU) {
                  $result['message'] = 'SKU already exist on the server.';
                  $result['status'] = 0;
                } else {
                  $data = new Gift;

                  foreach($lang as $lng){
                      if($lng=='en')
                      {

                          if ($request->file('image')) {
                              $file = $request->file('image');
                              $result = image_upload($file,'gift','image');
                              if($result[0]==true){
                                  $data->main_image = $result[1];
                              }
                          }
                          $data->name = $input['name'][$lng];
                          $data->description = $input['description'][$lng];
                          // $data->discount=$input['gift_discount'];
                          $data->sku_code=$input['sku_code'];
                          $data->points=$input['points'];
                          $data->brand_id=$input['brand'];
                          $data->amount= 1;
                          $data->category_id=$input['category_id'];
                          $data->weight=$input['weight'];
                          $data->quantity=$input['quantity'];
                          $data->is_featured=$input['gift_feature'];
                          $data->sub_category_id=$input['sub_category_id'];
                          $data->is_ready=$input['is_ready'];
                          $data->delivery_hours=$input['delivery_hours'];
                          $data->is_active= 1;
                          $data->video = $input['video'] ?? null;
                          /*if(empty($input['gift_discount'])){
                              $data->points_after_discount = $input['points'];
                          } else {
                              $pad = $input['points'] - ($input['points']/100)*$input['gift_discount'];
                              $data->points_after_discount = $pad;
                          }*/

                          if (isset($input['customization'])) {
                              $data->customization = 'Yes';
                              $data->customize_option = $input['customize_option'] ?? null;

                          } else {
                              $data->customization = 'No';
                          }
                          $data->save();
                      }
                      $dataLang = new  GiftLang;
                      $dataLang->gift_id = $data->id;
                      $dataLang->name = $input['name'][$lng];
                      $dataLang->description = $input['description'][$lng];
                      $dataLang->lang = $lng;
                      $dataLang->save();

                  }
                  /*New One*/
                  $toppingData = new GiftTopping;
                  $toppingData->category_id = $input['category_id'];
                  $toppingData->sub_category_id = $input['sub_category_id'];
                  $toppingData->gift_id = $data->id;
                  $toppingData->save();

                  if ($toppingData->id) {

                      if (isset($input['attribute']) && !empty($input['attribute'])) {

                          foreach ($input['attribute'] as $key => $value) {
                              $dataAttr = new GiftProductAttributes;
                              $dataAttr->gift_id = $data->id;
                              $dataAttr->gift_topping_id = $toppingData->id;
                              $dataAttr->is_mandatory = $value['is_mandatory'];
                              $dataAttr->is_free = $value['is_free'] ?? null;

                              /*if ($value['is_free'] == 1) {
                                $dataAttr->price = null;
                                $dataAttr->discount_price = null;

                              } else {
                                $dataAttr->price = $value['price'] ?? null;
                                $dataAttr->discount_price = $value['discount_price'] ?? null;
                              }*/
                              $dataAttr->points = $value['points'] ?? null;

                              if($dataAttr->save()) {

                                if (isset($value['attribute_value_lang_id'])) {

                                  foreach ($value['attribute_value_lang_id'] as $k => $v) {
                                    $dataAttrValue = new GiftProductAttributeValues;
                                    $dataAttrValue->gift_attributes_id = $dataAttr->id;
                                    $dataAttrValue->attributes_lang_id = $k;
                                    $dataAttrValue->attribute_value_lang_id = $v;
                                    $dataAttrValue->save();
                                  }
                                }
                              }
                          }
                      }
                  }
                  /*End New One*/

                  /*Last One*/
                  /*if(isset($input['variant'])){
                      foreach ($input['variant'] as $key => $value) {
                           if ($value['en']) {
                              foreach($lang as $lng1){

                                  if($lng1=='en'){

                                      if(isset($value['en'])){
                                          $variantData = new GiftVarient();
                                          $variantData->gift_id = $data->id;
                                          $variantData->name = $value['en'];
                                          $variantData->status = 1;
                                          $variantData->save();
                                      }
                                  }

                                  $variantLangData = new GiftVarientLang();
                                  $variantLangData->gift_varient_id = $variantData->id;
                                  $variantLangData->name = $value[$lng1];
                                  $variantLangData->lang = $lng1;
                                  $variantLangData->status = 1;
                                  $variantLangData->save();
                              }
                          }
                      }
                  }*/

                  /*Old One*/
                  /*if($input['variant']) {
                      foreach($input['variant'] as $key => $value) {
                          if ($value && !empty($value)) {
                                 if(isset($value['en'])){
                                      $variantData = new GiftVarient();
                                      $variantData->gift_id = $data->id;
                                      $variantData->name = $value['en'];
                                      $variantData->status = 1;
                                      $variantData->save();
                                  }

                              if (!empty($value)) {
                                  $variantLangData = new GiftVarientLang();
                                  $variantLangData->gift_varient_id = $variantData->id;
                                  $variantLangData->name = $value[$lang];
                                  $variantLangData->lang = $lang;
                                  $variantLangData->status = 1;
                                  $variantLangData->save();
                              }
                          }
                      }
                  }*/
                  /*Old One End*/

                  if ($request->file('multipalImage')) {
                    $i=1;
                    foreach ($request->file("multipalImage") as $file) {
                      $modelProductImages = new GiftImage();
                      $extension  = $file->getClientOriginalExtension();
                      $newFolder  = strtoupper(date('M') . date('Y')) . '/';
                      $folderPath	=	public_path().'/uploads/gift/'.$newFolder;
                      if (!File::exists($folderPath)) {
                          File::makeDirectory($folderPath, $mode = 0777, true);
                      }
                      $productImageName = time() . $i . '-gift-products.' . $extension;
                      $image = $newFolder . $productImageName;
                      if ($file->move($folderPath, $productImageName)) {
                          $modelProductImages->image = $image;
                      }
                      $i++;
                      $modelProductImages->gift_id = $data->id;
                      $modelProductImages->save();
                    }
                  }
                  $result['message'] = 'Gift has been created';
                  $result['status'] = 1;
                }

                return response()->json($result);

              } else {
                  $result['message'] = 'Gift name already exist.';
                  $result['status'] = 0;
                  return response()->json($result);
              }
            } catch (Exception $e){
              $result['message'] = 'Gift Can`t created';
              $result['status'] = 0;
              return response()->json($result);
            }

          } else {
            $result['message'] = $message;
            $result['status'] = 0;
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
        Gate::authorize('Gift-section');

        $gift = Gift::select('gifts.*','gift_categories.name as cat_name')
                        ->join('gift_categories','gifts.category_id','=','gift_categories.id')
                        ->where('gifts.id',$id)
                        ->first();
        $data['gift'] = $gift;
        $data['gift_images'] = GiftImage::select('image','id')->where('gift_id',$gift->id)->get();
        return view('gift.view',$data);
    }

    

    public function show_subcategory($category_id, $sub_category_id='')
    {
        Gate::authorize('Gift-section');
        $category=GiftSubCategory::select('name','id')->where('category_id', $category_id)->get();
        $data['record'] = $category;

        if ($sub_category_id) {
        	$data['sub_category_id'] = $sub_category_id;

        } else {
        	$data['sub_category_id'] = '';
        }
        return view('gift.sub_category_list',$data); 
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
        Gate::authorize('Gift-edit');
        $Gift = Gift::findOrFail($id);
		return response()->json([
            'gift' => $Gift
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
        Gate::authorize('Gift-edit');
        // create a new task based on Content tasks relationship
        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'description.ar.required'=>'The description(Arabic) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'image.size'  => 'The file size is less than 5MB',
            'amount' => 'The price should be greater than zero',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'description.en'=>'required',
               'description.ar'=>'required',
               'category_id' => 'required',
               'points' =>'required',
               'brand' =>'required',
               'weight' =>'required',
               'quantity' =>'required',
               'sku_code' =>'required',
               // 'gift_discount' =>'required',
               'gift_feature' =>'required',
               'is_ready' =>'required',
               'delivery_hours' =>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
               //'amount' => 'required|numeric|min:0|not_in:0',
          ],$mesasge);
        $input = $request->all();
        $cate_id = $id;
        $file = $request->file('image');

        $fail = false;
        $message = '';
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

        if (!$fail) {

          if ($is_delete_attr_data) {
              //delete old gift attributes
              $productAttrs = GiftProductAttributes::where('gift_id',$cate_id)->get();

              if ($productAttrs) {

                  foreach ($productAttrs as $key => $value) {

                      if ($value) {
                          GiftProductAttributeValues::where('gift_attributes_id',$value->id)->delete();
                      }
                  }
                  GiftProductAttributes::where('gift_id',$cate_id)->delete();
              }
          }

          try{
              /*if(empty($input['gift_discount'])){
                  $points_after_discount = $input['points'];
              } else {
                  $pad = $input['points'] - ($input['points']/100)*$input['gift_discount'];
                  $points_after_discount = $pad;
              }*/
              $lang = Language::pluck('lang')->toArray();
              $category = Gift::where('id','!=',$cate_id)->where(['brand_id'=>$input['brand'], 'category_id'=>$input['category_id'], 'sub_category_id'=>$input['sub_category_id'], 'sku_code'=>$input['sku_code']])->where('name', $input['name']['en'])->first();

              if (!$category) {
                $checkSKU = Gift::where('id','!=',$cate_id)->where(['sku_code'=>$input['sku_code']])->first();

                if ($checkSKU) {
                  $result['message'] = 'SKU already exist on the server.';
                  $result['status'] = 0;

                } else {
                  $customize_option = $input['customize_option'] ?? null;
                  $video = $input['video'] ?? null;

                  if (isset($input['customization'])) {
                    $customization = 'Yes';

                  } else {
                    $customization = 'No';
                  }

                  foreach($lang as $lng)
                  {
                      if($lng=='en')
                      {
                          if(isset($file)) {
                            $result = image_upload($file,'gift','image');
                            $data = Gift::where('id',$cate_id)->update(['name'=>$input['name'][$lng],'description'=>$input['description'][$lng],'category_id'=>$input['category_id'],'sub_category_id'=>$input['sub_category_id'], 'weight' => $input['weight'], 'brand_id' => $input['brand'],'points'=>$input['points'],'sku_code'=>$input['sku_code'],'quantity'=>$input['quantity'],'is_ready'=>$input['is_ready'],'delivery_hours'=>$input['delivery_hours'],'is_featured'=>$input['gift_feature'],'customization'=>$customization,'customize_option'=>$customize_option,'video'=>$video,'main_image'=>$result[1]]);

                          } else {
                            $data = Gift::where('id',$cate_id)->update(['name'=>$input['name'][$lng],'description'=>$input['description'][$lng],'category_id'=>$input['category_id'],'sub_category_id'=>$input['sub_category_id'], 'weight' => $input['weight'], 'brand_id' => $input['brand'],'points'=>$input['points'],'sku_code'=>$input['sku_code'],'quantity'=>$input['quantity'],'is_ready'=>$input['is_ready'],'delivery_hours'=>$input['delivery_hours'],'is_featured'=>$input['gift_feature'],'customization'=>$customization,'customize_option'=>$customize_option,'video'=>$video]);
                          }
                      }
                      $dataLang = GiftLang::where(['gift_id'=>$cate_id,'lang'=>$lng])->first();

                      if (isset($dataLang)) {
                         $dataLang = GiftLang::where(['gift_id'=>$cate_id,'lang'=>$lng])->update(['name'=>$input['name'][$lng],'description'=>$input['description'][$lng]]);

                      } else {
                          $dataLang = new  GiftLang;
                          $dataLang->gift_id = $cate_id;
                          $dataLang->name = $input['name'][$lng];
                          $dataLang->description = $input['description'][$lng];
                          $dataLang->lang = $lng;
                          $dataLang->save();
                      }
                  }

                  /*New One*/
                  $giftToppingData = GiftTopping::where(['category_id'=>$input['category_id'],'sub_category_id'=>$input['sub_category_id'],'gift_id'=>$cate_id])->first();
                  $toppingUpdateId = '';

                  if (!$giftToppingData) {
                     /*New One*/
                      $toppingData = new GiftTopping;
                      $toppingData->category_id = $input['category_id'];
                      $toppingData->sub_category_id = $input['sub_category_id'];
                      $toppingData->gift_id = $cate_id;
                      $toppingData->save();
                      $toppingUpdateId = $toppingData->id;

                  } else {
                    $toppingUpdateId = $giftToppingData->id;
                  }

                  if($toppingUpdateId) {
                    
                    //delete old product attributes
                    $productAttrs = GiftProductAttributes::where('gift_id',$cate_id)->get();

                    if ($productAttrs) {

                        foreach ($productAttrs as $key => $value) {

                            if ($value) {
                                GiftProductAttributeValues::where('gift_attributes_id',$value->id)->delete();
                            }
                        }
                        GiftProductAttributes::where('gift_id',$cate_id)->delete();
                    }

                    if (isset($input['attribute']) && !empty($input['attribute'])) {

                        foreach ($input['attribute'] as $key => $value) {
                            $dataAttr = new GiftProductAttributes;
                            $dataAttr->gift_id = $cate_id;
                            $dataAttr->gift_topping_id = $toppingUpdateId;
                            $dataAttr->is_mandatory = $value['is_mandatory'];
                            $dataAttr->is_free = $value['is_free'] ?? null;

                            /*if ($value['is_free'] == 1) {
                              $dataAttr->price = null;
                              $dataAttr->discount_price = null;

                            } else {
                              $dataAttr->price = $value['price'] ?? null;
                              $dataAttr->discount_price = $value['discount_price'] ?? null;
                            }*/
                            $dataAttr->points = $value['points'] ?? null;

                            if($dataAttr->save()) {

                              if (isset($value['attribute_value_lang_id'])) {

                                foreach ($value['attribute_value_lang_id'] as $k => $v) {
                                  $dataAttrValue = new GiftProductAttributeValues;
                                  $dataAttrValue->gift_attributes_id = $dataAttr->id;
                                  $dataAttrValue->attributes_lang_id = $k;
                                  $dataAttrValue->attribute_value_lang_id = $v;
                                  $dataAttrValue->save();
                                }
                              }
                            }
                        }
                    }
                    
                  }
                  /*End New One*/

                  /*Last One*/
                  /*if(isset($input['variant']) && !empty($input['variant'])) {

                      GiftVarient::where('gift_id',$cate_id)->delete();
                      GiftVarientLang::where('gift_id',$cate_id)->delete();

                      foreach ($input['variant'] as $key => $value) {

                          if ($value['en']) {

                              foreach($lang as $lng1){

                                  if($lng1=='en'){

                                      if(isset($value['en'])){
                                          $variantData = new GiftVarient();
                                          $variantData->gift_id = $cate_id;
                                          $variantData->name = $value['en'];
                                          $variantData->status = 1;
                                          $variantData->save();
                                      }
                                  }

                                  $variantLangData = new GiftVarientLang();
                                  $variantLangData->gift_varient_id = $variantData->id;
                                  $variantLangData->name = $value[$lng1];
                                  $variantLangData->lang = $lng1;
                                  $variantLangData->status = 1;
                                  $variantLangData->save();
                              }
                          }

                      }
                  }*/
                  /*End Last One*/

                  $result['message'] = 'Gift updated successfully.';
                  $result['status'] = 1;
                }
                return response()->json($result);

              } else {
                  $result['message'] = 'Gift name already exist.';
                  $result['status'] = 0;
                  return response()->json($result);
              }
          }
          catch (Exception $e)
          {
              $result['message'] = 'Gift Can`t be updated.';
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
        Gate::authorize('Gift-delete');
        if($id){
            $eventModel	=	Gift::where('id',$id)->update(array('is_deleted'=>1,'deleted_at'=>date("Y-m-d h:i:s")));
            if($eventModel){
                $result['message'] = 'Gift is deleted successfully';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Gift can`t be deleted!!';
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
        $details = Gift::find($id);
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['is_active' => 1];
            }else{
                $inp = ['is_active' => 0];
            }
            $Gift = Gift::findOrFail($id);
            if($Gift->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Gift is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Gift is deactivate successfully';
                    $result['status'] = 1;
                }
            }else{
                $result['message'] = 'Gift status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }

         return response()->json($result);
    }

    public function giftImagesDelete(Request $request)
    {
        Gate::authorize('Gift-section');
        $id = $request->segment(3);
        $details = GiftImage::find($id);
        if(!empty($details)){
            if(GiftImage::findOrFail($id)->delete()){
                $result['message'] = 'Gift Image has been deleted successfully';
                $result['status'] = 1;
            }else{
                $result['message'] = 'Gift Image can`t be deleted!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild Images!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function addMoreImages(Request $request, $id){
        Gate::authorize('Gift-section');
        $data['gift'] = Gift::findOrFail($id);
        if ($request->file('addMoremultipalImage')) {
            $i=1;
            foreach ($request->file("addMoremultipalImage") as $file) {
                $modelProductImages = new GiftImage();
                $extension  = $file->getClientOriginalExtension();
                $newFolder  = strtoupper(date('M') . date('Y')) . '/';
                $folderPath	=	public_path().'/uploads/gift/'.$newFolder;
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777, true);
                }
                $productImageName = time() . $i . '-gift-products.' . $extension;
                $image = $newFolder . $productImageName;
                if ($file->move($folderPath, $productImageName)) {
                    $modelProductImages->image = $image;
                }
                $i++;
                $modelProductImages->gift_id = $data['gift']->id;
                $modelProductImages->save();
            }
            $data['giftImage'] = GiftImage::select('image','id')->where('gift_id',$data['gift']->id)->get();
            if($data['gift']->id){
                $result['message'] = 'Gift Images has been added successfully';
                $result['status'] = 1;

                $result['data'] = $data;
            }else{
                $result['message'] = 'Gift can`t be Added!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'At least One Image is  required!!';
            $result['status'] = 0;
        }

        return response()->json($result);
    }

    public function addSKU(Request $request, $id){
        Gate::authorize('Gift-section');
        $mesasge = [
            'sku_code.required'=>'The SKU Code field is required.',
          ];
          $this->validate($request, [
               'sku_code'  => 'required',
          ],$mesasge);
            
        $input = $request->all();
        $gift_id = $id;

        $inp=[
            'sku_code' => $request->sku_code,
        ];
      
        $gift_sku = Gift::findOrFail($gift_id);
        
        if($gift_sku->update($inp)){
            $result['message'] = 'Gift SKU Code updated successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Gift SKU Code Can`t updated';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new GiftExport, 'Gift.csv');
    }

    public function giftAdd(){
        $data['category'] = GiftCategory::where('status', 1)->where('type','2')->get();
        $data['brands']  = GiftBrand::select('name','id')->get();
        return view('gift.add', $data);
    }

    public function importData(Request $request) 
    {
        $input = $request->all();
        $login_user_data = auth()->user();
        $added_by = $login_user_data->id;
        $lang = Language::pluck('lang')->toArray();

        if (!empty($input['file']) && !empty($input['images_zip'])) {
            $zipImgRslt = file_upload($request->file('images_zip'), 'gift');

            if ($zipImgRslt[0] == true) {

                $imageZip = new \ZipArchive();
                $extractedPath = public_path('uploads/gift/');

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
                        $zipImageDataSave = new GiftZipImages;
                        $zipImageDataSave->name = $v['old_name'];
                        $zipImageDataSave->new_name = $v['new_name'];
                        $zipImageDataSave->save();
                    }
                }
            }
        }

        if (!empty($input['file'])) {
            $imgRslt = file_upload($request->file('file'), 'product');
            $excelData = (new BulkImport)->toArray(public_path($imgRslt[1]))[0];

            if (!empty($excelData)) {
                $n = 8;
                foreach ($excelData as $key => $gift) {
                    // dd($gift);
                    $brand_id = '';
                    $category_id = '';
                    $sub_category_id = '';
                    $attribute_id = '';
                    $attribute_lang_id = '';
                    $attribute_value_lang_id = '';
                    $toppingId = '';
                    $langs = Language::pluck('lang')->toArray();

                    //For Brand
                    if ($gift['brand_name_en'] && !empty($gift['brand_name_en'])) {
                        $brand = GiftBrand::select('id')->where(['name'=>$gift['brand_name_en']])->first();

                        if ($brand) {
                            $brand_id = $brand->id;

                        } else {
                            foreach($langs as $lang) {

                                if ($lang=='en') {
                                    $brandData = new GiftBrand;
                                    $brandData->name = $gift['brand_name_en'];
                                    $brandData->status= 1;
                                    $brandData->save();
                                    $brand_id = $brandData->id;
                                }
                                $dataLang = new GiftBrandLang;
                                $dataLang->brand_id = $brandData->id;
                                $dataLang->name = $gift['brand_name_'.$lang];
                                $dataLang->lang = $lang;
                                $dataLang->save();
                            }
                        }
                    }
                    //For Category
                    if ($gift['category_en'] && !empty($gift['category_en'])) {
                        $category = GiftCategory::select('id')->where(['name'=>$gift['category_en']])->first();

                        if ($category) {
                            $category_id = $category->id;

                        } else {
                            foreach($langs as $lang1) {

                                if ($lang1=='en') {
                                    $categoryData = new GiftCategory;
                                    $categoryData->name = $gift['category_en'];
                                    $categoryData->status= 1;
                                    $categoryData->type= 2;
                                    $categoryData->save();

                                    $category_id = $categoryData->id;
                                }
                                $dataLang = new GiftCategoryLang;
                                $dataLang->gift_category_id = $categoryData->id;
                                $dataLang->name = $gift['category_'.$lang1];
                                $dataLang->lang = $lang1;
                                $dataLang->save();
                            }
                        }
                        //For SubCategory
                        if ($gift['sub_category_en'] && !empty($gift['sub_category_en'])) {
                            $subcategory = GiftSubCategory::select('id')->where(['name'=>$gift['sub_category_en'], 'category_id'=>$category_id])->first();

                            if ($subcategory) {
                                $sub_category_id = $subcategory->id;

                            } else {
                                foreach($langs as $lang2) {

                                    if ($lang2=='en') {
                                        $subcategoryData = new GiftSubCategory;
                                        $subcategoryData->category_id = $category_id;
                                        $subcategoryData->name = $gift['sub_category_en'];
                                        $subcategoryData->description = $gift['sub_category_en'];
                                        $subcategoryData->status= 1;
                                        $subcategoryData->save();
                                        $sub_category_id = $subcategoryData->id;
                                    }
                                    $dataLang = new GiftSubCategoryLang;
                                    $dataLang->gift_sub_category_id = $subcategoryData->id;
                                    $dataLang->name = $gift['sub_category_'.$lang2];
                                    $dataLang->description = $gift['sub_category_'.$lang2];
                                    $dataLang->lang = $lang2;
                                    $dataLang->save();
                                }
                            }
                        }
                    }

                    //Insert Gift From Here

                    if (!empty($brand_id) && !empty($category_id) && !empty($sub_category_id) && !empty($gift['name_en']) && !empty($gift['sku_code'])) {
                      $checkSKU = Gift::where(['sku_code'=>$gift['sku_code']])->first();

                      if (!$checkSKU) {
                          $delivery_days = str_replace('Days', '', $gift['delivery_days']);

                          $data = new Gift;
                          $data->brand_id = $brand_id;
                          $data->category_id = $category_id;
                          $data->sub_category_id = $sub_category_id;
                          $data->name = $gift['name_en'] ?? null;
                          $data->description = $gift['description_en'] ?? null;
                          $data->sku_code = $gift['sku_code'] ?? null;
                          $data->points = $gift['kp'];
                          $data->weight = $gift['weight'];
                          $data->quantity = $gift['quantity'];
                          $data->is_featured = $gift['gift_feature'];
                          $data->is_ready = $gift['is_ready_product'];
                          $data->delivery_hours = trim($delivery_days);

                          if (isset($gift['customization']) && $gift['customization'] == "Yes") {
                              $data->customization = 'Yes';
                              $data->customize_option = $gift['customize_option'];

                          } else {
                              $data->customization = 'No';
                          }
                          $data->is_active= 1;

                          if ($data->save()) {
                              //English
                              $dataLang = new GiftLang;
                              $dataLang->gift_id = $data->id;
                              $dataLang->name = $gift['name_en'];
                              $dataLang->description = $gift['description_en'];
                              $dataLang->lang = 'en';
                              $dataLang->save();
                              //Arabic
                              $dataLang = new GiftLang;
                              $dataLang->gift_id = $data->id;
                              $dataLang->name = $gift['name_ar'];
                              $dataLang->description = $gift['description_ar'];
                              $dataLang->lang = 'ar';
                              $dataLang->save();

                              //Gift Images
                              if ($gift['gift_image_csv'] && !empty($gift['gift_image_csv'])) {
                                $productImgUrl = explode(',', $gift['gift_image_csv']);

                                foreach ($productImgUrl as $k_pImg => $v_pImg) {
                                    $checkImageUploaded = GiftZipImages::where(['name'=>$v_pImg])->orderBy('id', 'desc')->first();

                                    if ($checkImageUploaded) {

                                        if ($k_pImg == 0) {
                                            $productImage = $checkImageUploaded->new_name;
                                            $inputs['main_image'] = $productImage;
                                            Gift::where('id',$data->id)->update($inputs);

                                        } else {
                                            $modelProductImages = new GiftImage();
                                            $modelProductImages->image = $checkImageUploaded->new_name;
                                            $modelProductImages->gift_id = $data->id;
                                            $modelProductImages->save();
                                        }
                                    }
                                }
                              }

                              //Attribute Section goes here.....
                              for ($x = 1; $x <= 4; $x++) {

                                  if (isset($gift['attribute_'.$x])) {
                                      $checkAttributeExist = GiftAttributesLang::select('attributes_lang.*')->join('attributes', 'attributes.id', '=', 'attributes_lang.attribute_id')->where(['category_id'=>$category_id, 'sub_category_id'=>$sub_category_id, 'name' => $gift['attribute_'.$x]])->first();

                                      if ($checkAttributeExist) {
                                          $attribute_id = $checkAttributeExist->attribute_id;
                                          $attribute_lang_id = $checkAttributeExist->id;

                                      } else {
                                          $attributeData = new GiftAttributes;
                                          $attributeData->category_id = $category_id;
                                          $attributeData->sub_category_id = $sub_category_id;
                                          $attributeData->added_by = $added_by;
                                          
                                          if ($attributeData->save()) {
                                              $attribute_id = $attributeData->id;
                                              
                                              //English & Arabic
                                              $dataAttributeLang = new GiftAttributesLang();
                                              $dataAttributeLang->attribute_id = $attributeData->id;
                                              $dataAttributeLang->topping_choose = ($gift['type_'.$x] == 'Single') ? 0 : 1;
                                              $dataAttributeLang->is_color = ($gift['is_color_'.$x] == 'No') ? 0 : 1;
                                              $dataAttributeLang->name = $gift['attribute_'.$x];
                                              $dataAttributeLang->lang = $gift['attribute_'.$x];
                                              $dataAttributeLang->save();

                                              $attribute_lang_id = $dataAttributeLang->id;
                                          }
                                      }

                                      if ($attribute_id) {
                                          $checkAttributeValueExist = GiftAttributeValueLang::select('attribute_value_lang.*')->join('attribute_values', 'attribute_values.id', '=', 'attribute_value_lang.attribute_value_id')->where(['category_id'=>$category_id, 'sub_category_id'=>$sub_category_id, 'name' => $gift['attribute_value_'.$x]])->first();

                                          if ($checkAttributeValueExist) {
                                              $attribute_value_id = $checkAttributeValueExist->attribute_value_id;
                                              $attribute_value_lang_id = $checkAttributeValueExist->id;

                                          } else {
                                              $attributeValueData = new GiftAttributeValues;
                                              $attributeValueData->category_id = $category_id;
                                              $attributeValueData->sub_category_id = $sub_category_id;
                                              $attributeValueData->attributes_lang_id = $attribute_id;
                                              $attributeValueData->added_by = $added_by;

                                              if ($attributeValueData->save()) {
                                                  $attribute_value_id = $attributeValueData->id;

                                                  //English & Arabic
                                                  $attributeValuedataLang = new GiftAttributeValueLang();
                                                  $attributeValuedataLang->attribute_value_id = $attribute_value_id;
                                                  $attributeValuedataLang->name = $gift['attribute_value_'.$x];
                                                  $attributeValuedataLang->lang = $gift['attribute_value_'.$x];

                                                  if (isset($gift['color_code_'.$x]) && !empty($gift['color_code_'.$x])) {
                                                      $attributeValuedataLang->color_code = $gift['color_code_'.$x];

                                                  } else {
                                                      $attributeValuedataLang->color_code = null;
                                                  }
                                                  $attributeValuedataLang->save();

                                                  $attribute_value_lang_id = $attributeValuedataLang->id;
                                              }
                                          }
                                      }

                                      //Gift Attribute Insert
                                      $checkToppingExist = GiftTopping::where(['category_id'=>$category_id,'sub_category_id'=>$sub_category_id,'gift_id'=>$data->id])->first();

                                      if ($checkToppingExist) {
                                          $toppingId = $checkToppingExist->id;

                                      } else {
                                          $toppingData = new GiftTopping;
                                          $toppingData->category_id = $category_id;
                                          $toppingData->sub_category_id = $sub_category_id;
                                          $toppingData->gift_id = $data->id;
                                          $toppingData->save();

                                          $toppingId = $toppingData->id;
                                      }

                                      if ($toppingId) {
                                          $dataAttr = new GiftProductAttributes;
                                          $dataAttr->gift_id = $data->id;
                                          $dataAttr->gift_topping_id = $toppingId;
                                          $dataAttr->is_mandatory = 1;
                                          $dataAttr->is_free = 0;
                                          $dataAttr->points = $gift['attribute_points_'.$x] ?? null;

                                          if ($dataAttr->save()) {
                                              $dataAttrValue = new GiftProductAttributeValues;
                                              $dataAttrValue->gift_attributes_id = $dataAttr->id;
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
                }
                $result['message'] = 'Excel imported successfully';
                $result['status'] = 1;
                return response()->json($result);
              // return back();
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
}
