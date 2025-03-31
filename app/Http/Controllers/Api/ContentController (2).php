<?php

/****************************************************/
// Developer By @Inventcolabs.com
/****************************************************/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use DB;
use Validator;
use App\Models\ContentLang;
use App\Models\Language;

class ContentController extends Controller
{

    public function getContant(Request $request)
    {
        $this->code = 200;
        $input =  $request->all();
        $this->requestdata = $input;
        $message = [
            'slug.required' => __("api.slug_required"),
        ];
        $validator = Validator::make($input, [
            'slug'   => 'required',
        ], $message);
        if ($validator->fails()) {
            $this->errorValidation($validator);
        } else {
            $content = Content::where('slug', $input['slug'])->where('status', 1)->first();
            if (isset($content)) {
                $response['status'] = true;
                $response['data'] = $content->description;
                return response()->json($response, 200);
            } else {
                $response['status'] = false;
                $response['message'] = __("api.something_worng");
                return response()->json($response, 200);
            }
        }
        return $this->jsonResponse();
    }
}
