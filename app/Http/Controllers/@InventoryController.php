<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\Inventory;
use App\Models\GiftCategory;
use App\Models\Language;
use File,DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftCategoriesExport;
use App\Imports\BulkImport;

class InventoryController extends Controller
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
        Gate::authorize('Inventory-section');
        $columns = ['inventory.quantity', 'gifts.name'];

        $category=Inventory::select('inventory.price','inventory.created_at','inventory.id','inventory.status','inventory.quantity','inventory.gift_category_id','inventory.gift_id','gifts.name as gift_name','gift_categories.name as gift_cat_name')
        ->join('gifts','gifts.id','=','inventory.gift_id')
        ->join('gift_categories','gift_categories.id','=','inventory.gift_category_id')->groupBy('inventory.id');
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
    				$query->whereBetween(DB::raw('DATE(inventory.created_at)'), array($request->from_date, $request->to_date));
    			}

          if ($request->has('gift_id')) {
              $gift_id = array_filter($request->gift_id);
              if(count($gift_id) > 0) {
                  $query->whereIn('inventory.gift_id', $request->get('gift_id'));
              }
          }

          if (!empty($request->get('search'))) {
             $search = $request->get('search');
             $query->having('inventory.quantity', 'like', "%{$search['value']}%");
             $query->orHaving('gifts.name', 'like', "%{$search['value']}%");
             $query->orHaving('gift_categories.name', 'like', "%{$search['value']}%");
          }

		  })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Inventory-section');
        $data['gift_category']=GiftCategory::select('name','id')->where(['status'=>1])->get();
        $data['get_gift']=Gift::select('name','id','category_id')->where(['is_deleted'=>0,'is_active'=>1])->get();
        // $data['gifts'] = Gift::all();
        return view('inventory.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Inventory-edit');
        $data['inventory'] = Inventory::where('id',$id)->first();
        $data['gift_category']=GiftCategory::select('name','id')->where(['status'=>1])->get();
        $data['get_gift']=Gift::select('name','id','category_id')->where(['is_deleted'=>0,'is_active'=>1])->get();
        return view('inventory.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Inventory-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Inventory-create');
         // validate
          $mesasge = [
              // 'price.required'=>'The point field is required.',
              'quantity.required'=>'The Quantity field is required.',
              'gift_category_id.required'=>'The Gift Category field is required.',
              'gift_id.required'=>'The Gift field is required.',
          ];

          $this->validate($request, [
               // 'price'  => 'required|numeric|min:0|not_in:0',
               'quantity'  => 'required|numeric|min:0|not_in:0',
               'gift_category_id'  => 'required',
               'gift_id'  => 'required',
          ],$mesasge);

          //$input = $request->all();  
          //dd($request->gift_category_id);
          $inputs = [
            // 'price' => $request->price,
            'quantity' => $request->quantity,
            'gift_category_id' => $request->gift_category_id,
            'gift_id' => $request->gift_id,
            'status' => 1,
          ];

          $gift_inventory = Inventory::create($inputs);

          $gift_id = $request->gift_id;
          // $point = $request->price;
          $quantity = $request->quantity;
          $quantity = Gift::select('quantity')->where('id',$gift_id)->first();
          $newquantity = $request->quantity+$quantity->quantity;

          Gift::where(['id'=>$gift_id])->update(['quantity'=>$newquantity]);

          if($gift_inventory->id){
              $result['message'] = 'Gift Inventory has been created';
              $result['status'] = 1;
          }else{
              $result['message'] = 'Gift Inventory Can`t created';
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
        Gate::authorize('Inventory-section');
        $inventory = Inventory::findOrFail($id);
        $data['inventory'] = $inventory;
        $data['gift_category_id'] = GiftCategory::select('name')->where('id',$inventory->gift_category_id)->first();
        $data['gift_id'] = Gift::select('name')->where('id',$inventory->gift_id)->first();
        return view('inventory.view',$data);
    }

    public function get_gifts($id){
      $data['get_gift'] = Gift::select('id','name')->where('category_id',$id)->get();
      //dd($data['get_gift']);
      return view('inventory.gifts',$data);
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
        Gate::authorize('Inventory-edit');
        $Category = Inventory::findOrFail($id);
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
        Gate::authorize('Inventory-edit');
        // validate
		$mesasge = [
            // 'price.required'=>'The point field is required.',
            'quantity.required'=>'The Quantity field is required.',
            'gift_category_id.required'=>'The Gift Category field is required.',
            'gift_id.required'=>'The Gift field is required.',
          ];
          $this->validate($request, [
               // 'price'  => 'required|numeric|min:0|not_in:0',
               'quantity'  => 'required|numeric|min:0|not_in:0',
               'gift_category_id'  => 'required',
               'gift_id'  => 'required',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;

        $inp=[
            // 'price' => $request->price,
            'quantity' => $request->quantity,
            'gift_category_id' => $request->gift_category_id,
            'gift_id' => $request->gift_id,
            'status' => 1,
        ];
      
        $gift_inventory = Inventory::findOrFail($cate_id);

        $gift_id = $request->gift_id;
        // $point = $request->price;
        $editquantity = $request->quantity;
        $oldquantity = Gift::select('quantity')->where('id',$gift_id)->first();
        $addquantity = Inventory::select('quantity')->where('gift_id',$gift_id)->orderBy('id','DESC')->first();
        $newquantity = $oldquantity->quantity-$addquantity->quantity+$editquantity;
        //dd($newquantity);

        Gift::where(['id'=>$gift_id])->update(['quantity'=>$newquantity]);
        
        if($gift_inventory->update($inp)){
            $result['message'] = 'Gift Inventory updated successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Gift Inventory Can`t updated';
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
        Gate::authorize('Inventory-delete');
        if(GiftCategory::findOrFail($id)->delete()){
            $result['message'] = 'Gift Inventory deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Gift Inventory Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function changeStatus($id, $status)
    {
        $details = Inventory::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Category = Inventory::findOrFail($id);
            if($Category->update($inp)){
                if($status == 'active'){
                    $result['message'] = 'Gift Inventory is activate successfully';
                    $result['status'] = 1;
                }else{
                    $result['message'] = 'Gift Inventory is deactivate successfully';
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = 'Gift Inventory status can`t be updated!!';
                $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild Inventory!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new GiftCategoriesExport, 'giftInventory.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
}
