<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\FaqLang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use File,DB;
use App\Models\Language;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Imports\BulkImport;

class FaqController extends Controller
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
        Gate::authorize('Faq-section');
        $login_user_data = auth()->user();
        $columns = ['faq.question'];
        $faq=Faq::select('faq.*');

        return Datatables::of($faq)->editColumn('created_at', function ($faq) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($faq->created_at);
            $dt->setTimezone($tz);
            return $dt->format('Y-m-d H:i:s'); 
        })->filter(function ($query) use ($request,$columns) {

    			if(!empty($request->from_date) && !empty($request->to_date))
    			{
    				$query->whereBetween(DB::raw('DATE(faq.created_at)'), array($request->from_date, $request->to_date));
    			}

          if(!empty($request->faq_type))
          {
            $query->where('faq.type', $request->faq_type);
          }

          if (!empty($request->get('search'))) {
               $search = $request->get('search');
               $query->having('faq.question', 'like', "%{$search['value']}%");
            }
    		})->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Faq-section');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        return view('faq.listing',$data);
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Faq-edit');
        $login_user_data = auth()->user();
        $data['user_type'] = $login_user_data->type;
        $data['faq'] = Faq::findOrFail($id);
        return view('faq.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Faq-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Faq-create');
         // validate
        $mesasge = [
            'question.en.required'=>'The question(English) field is required.',
            'question.ar.required'=>'The question(Arabic) field is required.',
            'answer.en.required'=>'The answer(English) field is required.',
            'answer.ar.required'=>'The answer(Arabic) field is required.',
            // 'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            // 'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            // 'image.size'  => 'the file size is less than 5MB',

          ];
          $this->validate($request, [
               'question.en'  => 'required',
               'question.ar'  => 'required',
               'answer.en'=>'required',
               'answer.ar'=>'required',
               'type'=>'required',
               // 'main_category_id'=>'required',
               // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
        $input = $request->all();
        $login_user_data = auth()->user();
        $added_by = $login_user_data->id;

        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {
                    $data = new Faq;
                    /*if ($request->file('image')) {
                        $file = $request->file('image');
                        $result = image_upload($file,'category');
                        if($result[0]==true){
                            $data->image = $result[1];
                        }
                    }*/
                    $data->question = $input['question'][$lang];
                    $data->answer = $input['answer'][$lang];
                    // $data->added_by = $added_by;
                    $data->type = $input['type'];
                    // $data->type= 1;
                    $data->status= 1;
                    $data->save();
                }
                $dataLang = new FaqLang;
                $dataLang->faq_id = $data->id;
                $dataLang->question = $input['question'][$lang];
                $dataLang->answer = $input['answer'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
            }
            $result['message'] = 'Faq has been created';
            $result['status'] = 1;
            return response()->json($result);
        } catch (Exception $e){
            $result['message'] = 'Faq Can`t created';
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
        Gate::authorize('Faq-section');
        $data['faq'] = Faq::findOrFail($id);
        return view('faq.view',$data);
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
        Gate::authorize('Faq-edit');
        $faq = Faq::findOrFail($id);
    		return response()->json([
          'user' => $faq
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
        Gate::authorize('Faq-edit');
        // validate
		$mesasge = [
            'question.en.required'=>'The question(English) field is required.',
            'question.ar.required'=>'The question(Arabic) field is required.',
            'answer.en.required'=>'The answer(English) field is required.',
            'answer.ar.required'=>'The answer(Arabic) field is required.',
            // 'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            // 'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            // 'image.size'  => 'the file size is less than 5MB',
          ];
          $this->validate($request, [
               'question.en'  => 'required|max:255',
               'question.ar'  => 'required|max:255',
               'type'=>'required',
               'answer.en'=>'required',
               'answer.ar'=>'required',
               // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        
        // $file = $request->file('image');
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang)
            {
                if($lang=='en') {               
                  $data = Faq::where('id',$cate_id)->update(['question'=>$input['question'][$lang],'answer'=>$input['answer'][$lang],'type'=>$input['type']]);
                }

                $dataLang = FaqLang::where(['faq_id'=>$cate_id,'lang'=>$lang])->first();

                if(isset($dataLang)) {
                  $dataLang = FaqLang::where(['faq_id'=>$cate_id,'lang'=>$lang])->update(['question'=>$input['question'][$lang],'answer'=>$input['answer'][$lang]]);                                   

                } else {
                    $dataLang = new FaqLang;
                    $dataLang->faq_id = $cate_id;
                    $dataLang->question = $input['question'][$lang];
                    $dataLang->answer = $input['answer'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
            }
            $result['message'] = 'FAQ updated successfully.';
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = 'FAQ Can`t be updated.';
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
        Gate::authorize('Faq-delete');
        if(Faq::findOrFail($id)->delete()){
            $result['message'] = 'Faq deleted successfully';
            $result['status'] = 1;
        }else{
            $result['message'] = 'Faq Can`t deleted';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
    public function changeStatus($id, $status)
    {
        $details = Faq::find($id);

        if(!empty($details)){

            if ($status == 'active') {
              $inp = ['status' => 1];

            } else {
              $inp = ['status' => 0];
            }
            $faq = Faq::findOrFail($id);

            if ($faq->update($inp)) {

                if($status == 'active'){
                  $result['message'] = 'FAQ is activate successfully';
                  $result['status'] = 1;

                } else {
                  $result['message'] = 'FAQ is deactivate successfully';
                  $result['status'] = 1; 
                }
            }else{
              $result['message'] = 'FAQ status can`t be updated!!';
              $result['status'] = 0;
            }
        }else{
            $result['message'] = 'Invaild user!!';
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
