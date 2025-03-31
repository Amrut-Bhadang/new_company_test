<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\GiftUserPlatforms;
// use App;

class GiftAuthorization
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
       
        if($request->header('GIFT-ACCESS-KEY') && $request->header('GIFT-SECRET-KEY'))
        {
            $access_key = explode('|~@#|',decryptPass($request->header('GIFT-ACCESS-KEY')));
            $secret_key = explode('|~@#|',decryptPass($request->header('GIFT-SECRET-KEY')));

            if(!empty($secret_key[0])  && !empty($access_key[0]))
            {
                if($secret_key[0]==$access_key[0] && $secret_key[1]==$access_key[1]  && $secret_key[2]==$access_key[2])
                {
                    // $data = GiftUserPlatforms::where(['user_id'=>$secret_key[0],'platform'=>$secret_key[2],'uuid'=>$secret_key[3],'platform_token'=>$request->header('GIFT-SECRET-KEY')])->first();
                    $data = GiftUserPlatforms::where(['user_id'=>$secret_key[0],'platform'=>$secret_key[2],'uuid'=>$secret_key[3]])->first();

                    if(isset($data))
                    {
                        return $next($request);
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
