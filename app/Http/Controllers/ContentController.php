<?php
namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Models\ContentLang;
use App\Models\Language;
class ContentController extends Controller
{
    public function __construct() {
		$this->middleware('auth');
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('Content-section');
        $Content=Content::select('name','created_at','id','status');
        return Datatables::of($Content)->editColumn('created_at', function ($Content) {
            $timezone = 'Asia/Kolkata';

            if(isset($_COOKIE['timezone'])) {
                $timezone = $_COOKIE['timezone'];
            }
            $tz = new \DateTimeZone($timezone);
            // $tz = new \DateTimeZone('Asia/Kolkata');
            $dt = new \DateTime($Content->created_at);
            $dt->setTimezone($tz);
            return $dt->format('d-m-Y'); 
        })->addIndexColumn()->make(true);
    }
    
    public function frontend()
    {
        Gate::authorize('Content-section');
        return view('content.listing');
    }

    public function edit_frontend($id)
    {
        Gate::authorize('Content-edit');
        $data['content'] = Content::findOrFail($id);
        return view('content.edit',$data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        Gate::authorize('Content-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('Content-create');
         // validate
        $mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'description.en'=>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
        $input = $request->all(); 
		// create a new task based on Content tasks relationship
		try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang){
                if($lang=='en')
                {
                    $data = new Content;
                    $data->name = $input['name'][$lang];
                    $data->description = $input['description'][$lang];
                    $data->status= 1;
                    $data->save();
                }
                $dataLang = new  ContentLang;
                $dataLang->content_id = $data->id;
                $dataLang->name = $input['name'][$lang];
                $dataLang->description = $input['description'][$lang];
                $dataLang->lang = $lang;
                $dataLang->save();
            }
            $result['message'] = 'Content has been created';
            $result['status'] = 1;
            return response()->json($result);
        } catch (Exception $e){
            $result['message'] = 'Content Can`t created';
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
        Gate::authorize('Content-section');
        $data['content'] = Content::findOrFail($id);
        return view('content.view',$data);
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
        Gate::authorize('Content-edit');
        $Content = Content::findOrFail($id);
		return response()->json([
            'user' => $Content
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
        Gate::authorize('Content-edit');
        // validate
		$mesasge = [
            'name.en.required'=>'The Name(English) field is required.',
            'name.ar.required'=>'The Name(Arabic) field is required.',
            'description.en.required'=>'The description(English) field is required.',
            'name.en.max'=>'The name(English) may not be greater than 255 characters.',
            'name.ar.max'=>'The name(Arabic) may not be greater than 255 characters.',
            'image.size'  => 'the file size is less than 5MB',
          ];
          $this->validate($request, [
               'name.en'  => 'required|max:255',
               'name.ar'  => 'required|max:255',
               'description.en'=>'required',
               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          ],$mesasge);
            
        $input = $request->all();
        $cate_id = $id;
        try{
            $lang = Language::pluck('lang')->toArray();
            foreach($lang as $lang)
            {
                if($lang=='en')
                {
                    $data = Content::where('id',$cate_id)->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang]]);                  
                }
                $dataLang = ContentLang::where(['content_id'=>$cate_id,'lang'=>$lang])->first();
                if(isset($dataLang))
                {
                   $dataLang = ContentLang::where(['content_id'=>$cate_id,'lang'=>$lang])->update(['name'=>$input['name'][$lang],'description'=>$input['description'][$lang]]);                                   
                }
                else
                {
                    $dataLang = new  ContentLang;
                    $dataLang->content_id = $cate_id;
                    $dataLang->name = $input['name'][$lang];
                    $dataLang->description = $input['description'][$lang];
                    $dataLang->lang = $lang;
                    $dataLang->save();
                }
            }
            $result['message'] = __('backend.Content_updated_successfully');
            $result['status'] = 1;
            return response()->json($result);
        }
        catch (Exception $e)
        {
            $result['message'] = __("backend.Content_Can`t_be_updated");
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
        Gate::authorize('Content-delete');
        return Content::findOrFail($id)->delete();
    }
    public function changeStatus($id, $status)
    {
        $details = Content::find($id); 
        if(!empty($details)){
            if($status == 'active'){
                $inp = ['status' => 1];
            }else{
                $inp = ['status' => 0];
            }
            $Content = Content::findOrFail($id);
            if($Content->update($inp)){
                if($status == 'active'){
                    $result['message'] = __("backend.content_status_success");
                    $result['status'] = 1;
                }else{
                    $result['message'] = __("backend.content_status_deactivate");
                    $result['status'] = 1; 
                }
            }else{
                $result['message'] = __("backend.content_status_can`t_updated");
                $result['status'] = 0;
            }
        }else{
            $result['message'] = __("backend.Invaild_user");
            $result['status'] = 0;
        }
        return response()->json($result);
    }
}
