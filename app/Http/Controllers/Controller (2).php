<?php
/*
©   2021 Inventcolabs Pvt. Ltd. ,  All rights reserved.
*/

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $successStatus = 200;
    public $code = 401;
    public $status = false;
    public $data = null;
    public $requestdata = [];
    public $message = 'Failed';
    public function __construct()
    {
    }

    public function errorValidation($error)
    {
        $data = [];
        $error = $error->errors()->toArray();
        foreach ($error as $key => $err) {
            $this->message = $err[0];
            $data[$key] = $err[0];
        }
        return $data;
    }

    public function apiLog($data, $user_id = 0)
    {
        Log::insert(['user_id' => $user_id, 'log' => json_encode($data)]);
    }


    public function jsonResponse()
    {
        $data = [
            'status'    => $this->status,
            'message'   => $this->message,
            'data'      => $this->data,
            'request'   => $this->requestdata,
        ];
        if (isset($this->counter)) {
            $data['counter'] = $this->counter;
        }
        //$this->apiLog($data);
        return response()->json($data, $this->code);
    }
}
