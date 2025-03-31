<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserPlatforms;
use App\User;
// use App;

class CustomeAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('ACCESS-KEY') && $request->header('SECRET-KEY'))
        {
            $access_key = explode('|~@#|',decryptPass($request->header('ACCESS-KEY')));
            $secret_key = explode('|~@#|',decryptPass($request->header('SECRET-KEY')));

            if(!empty($secret_key[0])  && !empty($access_key[0]))
            {
                // if($secret_key[0]==$access_key[0] && $secret_key[1]==$access_key[1]  && $secret_key[2]==$access_key[2])
                if($secret_key[0]==$access_key[0] && $secret_key[2]==$access_key[2])
                {
                    // $data = UserPlatforms::where(['user_id'=>$secret_key[0],'platform'=>$secret_key[2],'uuid'=>$secret_key[3],'platform_token'=>$request->header('SECRET-KEY')])->first();
                    $data = UserPlatforms::where(['user_id'=>$secret_key[0],'platform'=>$secret_key[2],'uuid'=>$secret_key[3]])->first();

                    if(isset($data))
                    {
                        $userDetail = User::where('id', $data->user_id)->first();
                        // $password = $userDetail->country_code.'-'.$userDetail->mobile;

                        if ($userDetail) {
                            return $next($request);

                        } else {
                            return response()->json('Unauthorized',401);
                        }

                        /*if (auth()->guard('web')->attempt(array('country_code'=>$userDetail->country_code,'mobile'=>$userDetail->mobile,'password'=>$password))) {
                            // return true;
                            return $next($request);

                        } else {
                            return response()->json('Unauthorized',401);   
                        }*/
                    }
                    else
                    {
                        return response()->json('Unauthorized',401);    
                    }
                }
                else
                {
                    return response()->json('Unauthorized',401);                                      
                }

            }
            else
            {
                return response()->json('Unauthorized',401);                  
            }

        }
        else{
            return response()->json('Unauthorized',401);  
           //return $next($request);            
        }
       // return $next($request);
    }
}