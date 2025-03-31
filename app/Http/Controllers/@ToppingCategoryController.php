<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\ToppingCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Media;
use File,DB;
use App\Models\ToppingCategoryLang;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomizedCategoryExport;
use App\Imports\BulkImport;

class ToppingCategoryController extends Controller
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
        Gate::authorize('Toppings_category-section');
        $columns = ['toppings_category.name'];

        $category=ToppingCategory::select('name','created_at','id','status','topping_choose');
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
        })->filter(function ($query) use ($request) {

    			if(!empty($request->from_date) && !empty($request->to_date))
    			{
    				$query->whereBetween(DB::raw('DATE(toppings_category.created_at)'), array($request->from_date, $request->to_date));
    			}

          if (!empty($request->get('search'))) {
             $search = $request->get('search');
             $query->having('toppings_category.name', 'like', "%{$search['value']}%");
          }

		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Toppings_category-section');
        return view('topping_category.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Toppings_category-edit');
        $data['category'] = ToppingCategory::findOrFail($id);
        return view('topping_category.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Toppings_category-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      Gate::authorize('Toppings_category-create');
       // validate
      $mesasge = [
          'name.en.required'=>'The Name(English) field is required.',
          'name.ar.required'=>'The Name(Arabic) field is required.',
          'topping_choose'=>'The Topping Choose field is required.',
        ];
        $this->validate($request, [
             'name.en'  => 'required|max:255',
             'name.ar'  => 'required|max:255',
             'topping_choose'  => 'required',
        ],$mesasge);
      $input = $request->all();  
      try{
          $lang = Language::pluck('lang')->toArray();
          foreach($lang as $lang){
              if($lang=='en')
              {
                  $data = new ToppingCategory;
                  $data->name = $input['name'][$lang];
                  $data->topping_choose = $input['topping_choose'];
                  $data->save();
              }
              $dataLang = new ToppingCategoryLang;
              $dataLang->topping_id = $data->id;
              $dataLang->name = $input['name'][$lang];
              $dataLang->lang = $lang;
              $dataLang->save();
          }
          $result['message'] = 'Topping Category has been created';
          $result['status'] = 1;
          return response()->json($result);
      } catch (Exception $e){
          $result['message'] = 'Topping Category Can`t created';
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
        Gate::authorize('Toppings_category-section');
        $data['category'] = ToppingCategory::findOrFail($id);
        return view('topping_category.view',$data);
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
        Gate::authorize('Toppings_category-edit');
        $Category = ToppingCategory::findOrFail($id);
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
        Gate::authorize('Toppings_category-edit');
        // validate
		$mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'topping_choose'=>'The Topping Choose field is required.',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'topping_choose'  => 'required',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang)
            {
                if($lang=='en')
                {
                    $data = ToppingCategory::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'topping_choose'=>$input['topping_choose']]);                    
                }
                $dataLang = ToppingCategoryLang::where(['topping_id'=>$cate_id,'lang'=>$lang])->first();
                if(isset($dataLang))
                {
                   $dataLang = ToppingCategoryLang::where(['topping_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang]]);                                   
                }
                else
                {
                    $dataLang = new  ToppingCategoryLang;
                    $dataLang->topping_id = $cate_id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
            }
            $result['message'] = 'Topping Category updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Topping Category Can`t be updated.';
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
        Gate::authorize('Toppings_category-delete');
        if(ToppingCategory::findOrFail($id)->delete()){
            $result['message'] = 'Topping Category deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Topping Category Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {
        $details = ToppingCategory::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = ToppingCategory::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Topping Category is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Topping Category is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Topping Category status can`t be updated!!';
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
        return Excel::download(new CustomizedCategoryExport, 'Customized_category.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
