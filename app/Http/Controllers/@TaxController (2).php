<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Tax;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use File,DB;
use Maatwebsite\Excel\Facades\Excel;

class TaxController extends Controller
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
        Gate::authorize('Tax-section');
        $columns = [];

        $tax=Tax::select('tax.*','countries.name','countries.phonecode','countries.sortname','currency.currency_code')->join('countries', 'countries.id', '=', 'tax.country_id')->leftJoin('currency', 'currency.id', '=', 'tax.currency_id');
        return Datatables::of($tax)->editColumn('created_at', function ($tax) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($tax->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s');
        })->filter(function ($query) use ($request,$columns) {

			if(!empty($request->from_date) && !empty($request->to_date))
			{
				$query->whereBetween(DB::raw('DATE(tax.created_at)'), array($request->from_date, $request->to_date));
			}

            if ($request->has('country_id')) {
                $country_id = array_filter($request->country_id);

                if(count($country_id) > 0) {
                    $query->whereIn('tax.country_id', $request->get('country_id'));
                }
            }

            if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('countries.name', 'like', "%{$search['value']}%");
               $query->having('countries.sortname', 'like', "%{$search['value']}%");
               $query->having('tax.tax', 'like', "%{$search['value']}%");
            }
		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Tax-section');
        $data['countriesData']=Country::select('*')->get();
        $data['currencyData']=Currency::select('*')->get();
        return view('tax.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Tax-edit');
        $data['countriesData']=Country::select('*')->get();
        $data['tax'] = Tax::select('*')->where('id', $id)->first();
        $data['currencyData']=Currency::select('*')->get();
        return view('tax.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Tax-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Tax-create');
         // validate
		$this->validate($request, [
            'country_id'=>'required', 
            'tax' => 'required',
            'currency_id' => 'required',
            'difference_amount' => 'required',
        ]);

        $input = $request->all();

	    try{
            $checkAlreadyData = Tax::where('country_id', $input['country_id'])->first();

            if (!$checkAlreadyData) {
    	        $data = new Tax;
                $data->country_id = $input['country_id'];
                $data->tax = $input['tax'];
                $data->currency_id = $input['currency_id'];
                $data->difference_amount = $input['difference_amount'];
                $data->save();

    	    	$result['message'] = 'Tax has been created';
                $result['status'] = 1;

            } else {
                $result['message'] = 'This country tax already exist.';
                $result['status'] = 0;
            }
            return response()->json($result);

	    } catch (Exception $e){
            $result['message'] = 'Tax Can`t created';
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
        Gate::authorize('Tax-section');
        $data['taxData'] = Tax::findOrFail($id);
        return view('tax.view',$data);
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
        Gate::authorize('Tax-edit');
        $taxData = Tax::findOrFail($id);
		return response()->json([
            'user' => $taxData
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
        Gate::authorize('Tax-edit');
        // validate

		$this->validate($request, [
            'country_id'=>'required', 
            'tax' => 'required',
            'currency_id' => 'required',
            'difference_amount' => 'required',
        ]);

        $input = $request->all();
        $cate_id = $id;

        try{
            $checkAlreadyData = Tax::where('country_id', $input['country_id'])->where('id','!=',$cate_id)->first();

            if (!$checkAlreadyData) {
                Tax::where('id',$cate_id)->update(['tax'=>$input['tax'],'country_id'=>$input['country_id'],'currency_id'=>$input['currency_id'],'difference_amount'=>$input['difference_amount']]);
                $result['message'] = 'Tax updated successfully.';
                $result['status'] = 1;

            } else {
                $result['message'] = 'This country tax already exist.';
                $result['status'] = 0;
            }
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'Tax Can`t be updated.';
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
        Gate::authorize('Tax-delete');
        if(Tax::findOrFail($id)->delete()){
            $result['message'] = 'Tax deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Tax Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }

    public function exportUsers($slug)
    {
        //
        Gate::authorize('Users-section');
        return Excel::download(new BrandExport, 'Tax.csv');
    }   

    public function importUsers() 
    {
        Excel::import(new BulkImport,request()->file('file'));
        return back();
    }
    
}
