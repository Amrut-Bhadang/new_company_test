<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\User;
use App\Models\Brand;
use App\Models\RestaurantsTiming;
use App\Models\PanelNotifications;
use App\Models\Restaurant;
use App\Models\Holiday;
use App\Models\Country;
use App\Models\Orders;
use App\Models\Tax;
use App\Models\DeliveryPrice;
use App\Models\Language;
use App\Models\Notification;
use App\Models\NotificationLang;
use App\Models\PanelNotificationLang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;


if (!function_exists('file_checker')) {
    function file_checker($file, $type = null)
    {
        if (isset($file) && !empty($file) && file_exists(base_path() . '/' . $file)) {
            $images = url($file);
        } else {
            $images = url('public/img/' . $type . '.png');
        }
        return $images;
    }
}
if (!function_exists('notificationUserType')) {

    function notificationUserType()
    {
        $data = array('All' => '1', 'User' => '2', "Celebrity" => '3');
        return $data;
    }
}

if (!function_exists('notificationType')) {

    function notificationType()
    {
        $data = array('Email' => '1', 'SMS' => '2', "PushNotification" => '3', 'All' => '4');
        return $data;
    }
}

function calculateDimensions($width, $height, $maxwidth, $maxheight)
{

    if ($width != $height) {
        if ($width > $height) {
            $t_width = $maxwidth;
            $t_height = (($t_width * $height) / $width);
            //fix height
            if ($t_height > $maxheight) {
                $t_height = $maxheight;
                $t_width = (($width * $t_height) / $height);
            }
        } else {
            $t_height = $maxheight;
            $t_width = (($width * $t_height) / $height);
            //fix width
            if ($t_width > $maxwidth) {
                $t_width = $maxwidth;
                $t_height = (($t_width * $height) / $width);
            }
        }
    } else
        $t_width = $t_height = min($maxheight, $maxwidth);

    return array('height' => (int) $t_height, 'width' => (int) $t_width);
}
if (!function_exists('image_upload')) {
    function image_upload($file, $pathName, $multipalImageName = null)
    {
        $path = public_path() . '/uploads/' . $pathName . '/';
        $newFolder = strtoupper(date('M') . date('Y')) . '/';
        $folderPath = $path . $newFolder;
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, $mode = 0777, true);
        }
        $imgsize = getimagesize($file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $imgre = calculateDimensions($width, $height, 450, 450);
        $image = $file;
        $extension = $image->getClientOriginalExtension();
        //$orignalname = $file->getClientOriginalName();       
        if ($multipalImageName == null) {
            $fileName = time() . '-' . $pathName . '.' . $extension;
        } else {
            $fileName = time() . '-' . $pathName . '-' . $multipalImageName . '.' . $extension;
        }


        if (in_array($extension, ['jpeg', 'jpg', 'JPG', 'JPEG', 'png', 'PNG', 'gif', 'GIF'])) {
            $image->move($folderPath, $fileName);
            return array(true, $newFolder . $fileName, $extension, $fileName);
        } else {
            return array(false, "file should be in jpeg, jpg,png,gif format / double extension not allow.", '');
        }
    }
}

function file_upload($file, $pathName, $multipalImageName = null)
{
    //print_r($file); die;
    $path =  public_path('storage') . '/' . $pathName . '/';
    $newFolder = strtoupper(date('M') . date('Y')) . '/';
    $folderPath         =   $path . $newFolder;

    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, $mode = 0777, true);
    }

    $image = $file;
    $extension = $image->getClientOriginalExtension();

    if ($multipalImageName == null) {
        $fileName = time() . '-' . $pathName . '.' . $extension;
    } else {
        $fileName = time() . '-' . $pathName . '-' . $multipalImageName . '.' . $extension;
    }

    $savePath  = 'storage/' . $pathName . '/' . $newFolder;

    if (in_array($extension, ['jpeg', 'jpg', 'JPG', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'pdf', 'docx', 'zip', 'xlsx', 'mp4', 'MP4'])) {
        $image->move($folderPath, $fileName);
        return array(true,  $savePath . $fileName, $extension, $fileName);
    } else {
        return array(false, "file should be in jpeg, jpg,png,gif, pdf, docx, xlsx format / double extension not allow.", '');
    }
}

function send_notification_add($form_id, $user_type = 0, $notification_type = null, $notification_for = null, $order_id = null, $title = null, $message = null)
{
    $lang = App::getLocale();
   
     App::SetLocale('en');
    $lang = App::getLocale();

    $notification_title = __('api.notification_court_book_title');
    $message_lang = __('api.'.$message);
    $title_lang = __('api.'.$title);

    $notificationData = new Notification();
    $notificationData->user_id = $form_id;
    $notificationData->user_type = $user_type;
    $notificationData->notification_type = $notification_type;
    $notificationData->notification_for = $notification_for;
    $notificationData->order_id = $order_id;
    $notificationData->title = $title_lang;
    $notificationData->message = $message_lang;
    $notificationData->lang = $lang;
    $notificationData->save();
    // insert notification lang data
    if(isset($notificationData)){
     $notificationLang = new NotificationLang();
     $notificationLang->notification_id = $notificationData->id;
     $notificationLang->title = $title_lang;
     $notificationLang->message = $message_lang;
     $notificationLang->lang = $lang;
     $notificationLang->save();
        // insert AR lang
          App::SetLocale('ar');
        $lang = App::getLocale();
        $message_lang = __('api.'.$message);
        $title_lang = __('api.'.$title);
     $notificationLang = new NotificationLang();
     $notificationLang->notification_id = $notificationData->id;
     $notificationLang->title = $title_lang;
     $notificationLang->message = $message_lang;
     $notificationLang->lang = $lang;
     $notificationLang->save();
    }
}

function add_player_notification_by_admin($form_id, $user_type = 0, $notification_type = null, $notification_for = null, $order_id = null, $title = null, $message = null){
//   dd($form_id, $user_type, $notification_type, $notification_for, $order_id, $title, $message);
  $lang = Language::pluck('lang')->toArray();
//   dd($lang,$title,$message);
  foreach ($lang as $lang) {
      if ($lang == 'en') {
        $notificationData = new Notification();
        $notificationData->user_id = $form_id;
        $notificationData->user_type = $user_type;
        $notificationData->notification_type = $notification_type;
        $notificationData->notification_for = $notification_for;
        $notificationData->order_id = $order_id;
        $notificationData->title = $title[$lang];
        $notificationData->message = $message[$lang];
        $notificationData->lang = $lang;
        $notificationData->save();
      }
      $notificationLang = new NotificationLang();
      $notificationLang->notification_id = $notificationData->id;
      $notificationLang->title = $title[$lang];
      $notificationLang->message = $message[$lang];
      $notificationLang->lang = $lang;
      $notificationLang->save();
  }
}
function addNotificationByAdminForOwner($user_type = 1, $notification_type=1, $notification_for='admin_notification', $title, $message, $user_id , $order_id = 1){
    // dd($user_type = 1, $notification_type=1, $notification_for='admin_notification', $titles, $messages, $user_id , $order_id = 1);
    $lang = Language::pluck('lang')->toArray();
  foreach ($lang as $lang) {
      if ($lang == 'en') {
        $notificationData = new PanelNotifications();
        $notificationData->user_id = $user_id;
        $notificationData->user_type = $user_type;
        $notificationData->notification_type = $notification_type;
        $notificationData->notification_for = $notification_for;
        $notificationData->order_id = $order_id;
        $notificationData->title = $title[$lang];
        $notificationData->message = $message[$lang];
        $notificationData->save();
      }
        $notificationLang = new PanelNotificationLang();
        $notificationLang->panel_notification_id = $notificationData->id;
        $notificationLang->title = $title[$lang];
        $notificationLang->message = $message[$lang];
        $notificationLang->lang = $lang;
        $notificationLang->save();
  }
}

if (!function_exists('send_notification')) {
    function send_notification($form_id, $user_id = 0, $title = null, $body = array())
    {
        $arrNotification = array();
        $arrNotification["body"]  = $body;
        $arrNotification["title"] = $title;

        if (!$form_id) {
            $arrNotification["content-available"] = 1;
            $arrNotification["slient_notification"] = 'Yes';
        } else {
            $arrNotification["content-available"] = 0;
            $arrNotification["slient_notification"] = 'No';
        }
        $arrNotification["sound"] = "default";
        $arrNotification["type"] = 1;
        $user = User::where('id', $user_id)->with('devices')->first();

        if (isset($user->devices[0])) {
            foreach ($user->devices as $device) {
                $device_type = $device->device_type;
                if ($device_type != 'Android') {
                    $arrNotification["body"]  = $body['message'];
                } else {
                    $arrNotification["body"]  = $body['message'];
                }
                $user_id = $device->user_id;
                $device_id = $device->device_token;
                $result = push_notification($device_id, $arrNotification, $device_type, $form_id);
            }
        }
    }

    if (!function_exists('push_notification')) {
        function push_notification($registatoin_ids, $notification, $device_type, $form_id)
        {
            $url = 'https://fcm.googleapis.com/fcm/send';
            if ($device_type == "Android") {
                // dd('ddd',$device_type);
                $fields = array(
                    'to' => $registatoin_ids,
                    'notification' => $notification
                );
            } else {

                if (!$form_id) {
                    $fields = array(
                        'to' => $registatoin_ids,
                        'content-available' => 1,
                        'notification' => $notification
                    );
                } else {
                    $fields = array(
                        'to' => $registatoin_ids,
                        'notification' => $notification
                    );
                }
            }
            //Firebase API Key

            if ($device_type == 'Android') {
                $headers = array('Authorization:key=AAAA_bLVrM8:APA91bH5VpXcpOltqNjwRb8tqT4Cj1C4WTWHQ5PCOMLzhggebKhjCS91YV_nwVQEVhxurMPKFaSyMMqlAQD8pcmmquPGdaKhNBkWc80co0VYFW5EvNT6wx1x70cuvwLA8R_VQ61vjSjG', 'Content-Type:application/json');
            } else {
                $headers = array('Authorization:key=AAAA_bLVrM8:APA91bH5VpXcpOltqNjwRb8tqT4Cj1C4WTWHQ5PCOMLzhggebKhjCS91YV_nwVQEVhxurMPKFaSyMMqlAQD8pcmmquPGdaKhNBkWc80co0VYFW5EvNT6wx1x70cuvwLA8R_VQ61vjSjG', 'Content-Type:application/json');
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            // dd($result);

            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }
}

function getRestroTimeStatus($restro_id)
{
    $week[0] = 'sunday';
    $week[1] = 'monday';
    $week[2] = 'tuesday';
    $week[3] = 'wednesday';
    $week[4] = 'thursday';
    $week[5] = 'friday';
    $week[6] = 'saturday';
    $date = new \DateTime();

    $tz = new \DateTimeZone('Asia/Kolkata');
    $dt = new \DateTime(date('Y-m-d H:i:s'));
    $dt->setTimezone($tz);
    $dateNew = $dt->format('Y-m-d H:i:s');


    /*$timeZone = $date->getTimezone();
  $timeZoneName = $timeZone->getName();*/
    // $dateNew = date('Y-m-d H:i:s');
    $dateNewFormat = $dt->format('d-m-Y');
    $dayofweek = date('w', strtotime($dateNew));
    $currentTime = $dt->format('H:i');
    $dayName = $week[$dayofweek];

    $restroTime = RestaurantsTiming::where(['restro_id' => $restro_id, 'day' => $dayName])->first();

    if ($restroTime) {

        if ($restroTime->is_close == 'Yes') {
            return 0;
        } else {
            // $checkHoliday = Holiday::where(['restaurant_id'=>$restro_id])->where('start_date_time', '>=', $dateNewFormat)->whereDate('end_date_time', '<=', $dateNewFormat)->first();
            $checkHoliday = Holiday::where(['restaurant_id' => $restro_id])->where(function ($query) use ($dateNewFormat) {
                $query->where('start_date_time', '<=', $dateNewFormat);
                $query->where('end_date_time', '>=', $dateNewFormat);
            })->first();

            if ($checkHoliday) {
                return 0;
            } else {

                if ($restroTime->start_time <= date('H:i', strtotime($currentTime)) && $restroTime->end_time >= date('H:i', strtotime($currentTime))) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    } else {
        return 0;
    }
}

function getNotificationList($user_id = null, $user_type = null)
{
    $notificationData = PanelNotifications::select('panel_notifications.*');
    $count = 0;

    if (isset($user_id)) {
        $count = PanelNotifications::where('panel_notifications.is_read', 0)->where('panel_notifications.user_id', $user_id)->count();
        $notificationData = $notificationData->where('panel_notifications.user_id', $user_id)->orderBy('panel_notifications.id', 'desc')->get();
        // dd($notificationData,'dd',$count);
        
    } else {
        $count = PanelNotifications::where('panel_notifications.is_read', 0)->count();
        $notificationData = $notificationData->orderBy('panel_notifications.id', 'desc')->get();
    }

    $data['count'] = $count;
    $data['notificationData'] = $notificationData;
    return $data;
}
function getNotificationPlayerList($user_id = null, $user_type = null)
{
    // dd('ddddddddddddddddddd');
    $notificationData =Notification::select('notifications.*');
    $count = 0;
   
    if ($user_id) {
        $count =Notification::where('notifications.is_read', 0)->where('notifications.user_id', $user_id)->count();
        $notificationData = $notificationData->where('notifications.user_id', $user_id)->orderBy('notifications.id', 'desc')->get();
    } else {
        $count =Notification::where('notifications.is_read', 0)->count();
        $notificationData = $notificationData->orderBy('notifications.id', 'desc')->get();
    }

    $data['count'] = $count;
    $data['notificationData'] = $notificationData;
    return $data;
}

function getKPTransferTime()
{
    $deliveryTime = DeliveryPrice::select('point_transfer_time')->first();
    $transferTime = $deliveryTime->point_transfer_time;
    $new_time = date("Y-m-d H:i:s", strtotime('+' . $transferTime . ' hours'));
    return $new_time;
}

function getShippingCharge()
{
    $deliveryTime = DeliveryPrice::select('shipping_price_per_km')->first();
    return $deliveryTime->shipping_price_per_km;
}

function getUserReferralKP($amount)
{
    $settingData = DeliveryPrice::select('referral_kp_percent')->first();
    return ($settingData->referral_kp_percent / 100) * $amount;
}

function getWareHouseAddress()
{
    $deliveryTime = DeliveryPrice::select('address_warehouse', 'latitude', 'longitude')->first();
    return $deliveryTime->address_warehouse;
}

function getSettingData($field)
{

    if ($field == 'ALL') {
        $settingData = DeliveryPrice::select('*')->first();
        return $settingData;
    } else {
        $settingData = DeliveryPrice::select($field)->first();
        return $settingData->$field;
    }
}

function changeKPTransferStatus()
{
    $currentTime = date("Y-m-d H:i:s");
    $getPendingKPOrders = Orders::select('orders.id')->where('is_kp_transfer', 'No')->where('kp_transfer_time', '<=', $currentTime)->get();

    if ($getPendingKPOrders) {

        foreach ($getPendingKPOrders as $key => $value) {
            Orders::where(['id' => $value->id])->update(['is_kp_transfer' => 'Yes']);
        }
    }
    return $getPendingKPOrders;
}

function changeRestaurantStatus()
{
    $currentTime = date("Y-m-d");
    $getRestros = Restaurant::select('id')->where('restro_valid_upto', '<=', $currentTime)->get();

    if ($getRestros) {

        foreach ($getRestros as $key => $value) {
            Restaurant::where(['id' => $value->id])->update(['status' => 0]);
        }
    }
    return $getRestros;
}

function getUserRestroId($userId)
{
    $restaurant_detail = Restaurant::where(['user_id' => $userId])->first();

    if ($restaurant_detail) {
        return $restaurant_detail->id;
    } else {
        return '';
    }
}

function getCountryTaxByLatLong($lat, $long)
{
    $geolocation = $lat . ',' . $long;
    $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $geolocation . '&sensor=false&libraries=places&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4';
    $file_contents = file_get_contents($request);
    $json_decode = json_decode($file_contents);

    // dd($json_decode->results[0]);

    if (isset($json_decode->results[0])) {
        $response = array();
        $responseShortName = array();
        foreach ($json_decode->results[0]->address_components as $addressComponet) {
            if (in_array('political', $addressComponet->types)) {
                $response[] = $addressComponet->long_name;
            }
            $responseShortName[] = $addressComponet->short_name;
        }

        if (isset($response[0])) {
            $first  =  $response[0];
        } else {
            $first  = 'null';
        }
        if (isset($response[1])) {
            $second =  $response[1];
        } else {
            $second = 'null';
        }
        if (isset($response[2])) {
            $third  =  $response[2];
        } else {
            $third  = 'null';
        }
        if (isset($response[3])) {
            $fourth =  $response[3];
        } else {
            $fourth = 'null';
        }
        if (isset($response[4])) {
            $fifth  =  $response[4];
        } else {
            $fifth  = 'null';
        }

        /*if( $first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null' ) {
            echo "<br/>Address:: ".$first;
            echo "<br/>City:: ".$second;
            echo "<br/>State:: ".$fourth;
            echo "<br/>Country:: ".$fifth;
        }
        else if ( $first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null'  ) {
            echo "<br/>Address:: ".$first;
            echo "<br/>City:: ".$second;
            echo "<br/>State:: ".$third;
            echo "<br/>Country:: ".$fourth;
        }
        else if ( $first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null' ) {
            echo "<br/>City:: ".$first;
            echo "<br/>State:: ".$second;
            echo "<br/>Country:: ".$third;
        }
        else if ( $first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null'  ) {
            echo "<br/>State:: ".$first;
            echo "<br/>Country:: ".$second;
        }
        else if ( $first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null'  ) {
            echo "<br/>Country:: ".$first;
        }*/

        // dd($responseShortName);

        if (isset($responseShortName[3]) && $responseShortName[3]) {
            $countryData = Country::where(['sortname' => $responseShortName[3]])->first();

            if ($countryData) {
                $taxData = Tax::where(['country_id' => $countryData->id])->first();

                if ($taxData) {
                    return $taxData->tax;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    } else {
        return 1;
    }
}

function getCountryIdByLatLong($lat, $long)
{
    $geolocation = $lat . ',' . $long;
    $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $geolocation . '&sensor=false&libraries=places&key=AIzaSyAplPUk9niQdlpNdKgipVXUnrd6Nev5TX4';
    $file_contents = file_get_contents($request);
    $json_decode = json_decode($file_contents);
    $country = '';

    if (isset($json_decode->results[0])) {
        $response = array();
        $responseShortName = array();
        foreach ($json_decode->results[0]->address_components as $addressComponet) {
            if (in_array('political', $addressComponet->types)) {
                $response[] = $addressComponet->long_name;
            }
            $responseShortName[] = $addressComponet->short_name;
        }

        if (isset($response[0])) {
            $first  =  $response[0];
        } else {
            $first  = 'null';
        }
        if (isset($response[1])) {
            $second =  $response[1];
        } else {
            $second = 'null';
        }
        if (isset($response[2])) {
            $third  =  $response[2];
        } else {
            $third  = 'null';
        }
        if (isset($response[3])) {
            $fourth =  $response[3];
        } else {
            $fourth = 'null';
        }
        if (isset($response[4])) {
            $fifth  =  $response[4];
        } else {
            $fifth  = 'null';
        }

        if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null') {
            $country = $fifth;
        } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null') {
            $country = $fourth;
        } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null') {
            $country = $third;
        } else if ($first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
            $country = $second;
        } else if ($first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
            $country = $first;
        }

        if (isset($country) && $country) {
            $countryData = Country::where(['name' => $country])->first();

            if ($countryData) {
                return $countryData->id;
            } else {
                return '';
            }
        } else {
            return '';
        }
    } else {
        return '';
    }
}

function checkBrandLogin()
{
    $login_user_data = auth()->user();
    $restaurant_detail = '';

    if ($login_user_data && $login_user_data->type == 4) {
        $brandData = Brand::where('user_id', $login_user_data->id)->first();

        if ($brandData) {
            auth()->logout();

            /*var_dump(Auth::user()->id); // returns 1
            Auth::logout();*/
            $restaurant_detail = Restaurant::select('name', 'id', 'user_id', 'is_open')->where(['status' => 1, 'brand_id' => $brandData->id])->first();

            if ($restaurant_detail) {
                $user = User::find($restaurant_detail->user_id);
                Auth::login($user);
            }
        } else {
            $restaurant_detail = Restaurant::select('name', 'id', 'user_id', 'is_on')->where(['status' => 1, 'user_id' => $login_user_data->id])->first();
        }

        if ($restaurant_detail) {
            return array('status' => true, 'data' => $restaurant_detail);
        } else {
            auth()->logout();
            return array('status' => false, 'data' => array());
        }
    } else {
        return array('status' => true, 'data' => $restaurant_detail);
    }
}

function getBrandRestros()
{
    $login_user_data = auth()->user();
    $restaurants = [];

    if ($login_user_data && $login_user_data->type == 4) {
        $restaurant_detail = Restaurant::select('name', 'id', 'user_id', 'brand_id')->where(['status' => 1, 'user_id' => $login_user_data->id])->first();
        // $brandData=Brand::where('user_id', $restaurant_detail->id)->first();

        if ($restaurant_detail) {
            $restaurants = Restaurant::select('name', 'id', 'user_id')->where(['status' => 1, 'brand_id' => $restaurant_detail->brand_id])->get();
        } else {
            $restaurants = Restaurant::select('name', 'id', 'user_id')->where(['status' => 1, 'user_id' => $login_user_data->id])->get();
        }
    }
    return $restaurants;
}

function switchAccount($userId)
{
    auth()->logout();
    $user = User::find($userId);
    Auth::login($user);
}

function encryptPass($password)
{
    $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
    $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
    $method = 'aes-256-cbc';

    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    $encrypted = base64_encode(openssl_encrypt($password, $method, $sSalt, OPENSSL_RAW_DATA, $iv));
    return $encrypted;
}

function decryptPass($password)
{
    $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
    $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
    $method = 'aes-256-cbc';

    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    $decrypted = openssl_decrypt(base64_decode($password), $method, $sSalt, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}

function CallbackApis($url, $type)
{

    if ($type == 'Register') {
        $header = array();
    } else {
        $header = array();
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $header,
    ));
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    // echo $response;

    if ($httpcode == 200) {
        return  json_decode($response);
    } else if ($httpcode == 401) {
        return  array('staus' => false, 'message' => 'unauthorized');
    }
}


function commonAuthUserId()
{
    $request = app('request');
    return $secret_key = explode('|~@#|', decryptPass($request->header('SECRET-KEY')));
}

function ApiCurlMethod($method, $parms, $type, $method_type = 'POST')
{
    
    $locale = App::getLocale();

    $currency = Session::get('currentCurrency') ?? 'OAR';
    if ($type == 'Normal') {
        $header = array(
            'Accept:application/json',
            'currency:' . $currency,
            'Accept-Language:' . $locale
        );
    } else {
        $userData =  Session::get('AuthUserData') ?? null;
        $access_token = $userData->token ?? null;
        $header = array(
            'Accept:application/json',
            'Authorization:Bearer ' . $access_token,
            'currency:' . $currency,
            'Accept-Language:' . $locale
        );
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => url('api/auth/') . '/' . $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 30,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method_type,
        CURLOPT_POSTFIELDS => $parms,
        CURLOPT_HTTPHEADER => $header,
    ));

    $response = curl_exec($curl);

    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);
    // echo "<pre>";
    // print_r($response);
    // die;
    // dd($response);
    if ($httpcode == 200) {

        return  json_decode($response);
    } else if ($httpcode == 401) {
        //dd($method,0401);
        Session::forget('AuthUserData');
        Session::forget('AuthUserData');
        //  dd(json_decode($response));
    } else if ($httpcode == 402) {
       // dd($method, 0402);
        // Session::forget('AuthUserData');
        // Session::forget('AuthUserData');
        //  dd(json_decode($response));

    } else if ($httpcode == 404) {
       // dd($method, 0404);
        //Session::forget('AuthUserData');
        //Session::forget('AuthUserData');
        //      dd($method);
        // dd(json_decode($response));
    } else if ($httpcode == 500) {
        //dd($response, 500);
        //Session::forget('AuthUserData');
        // Session::forget('AuthUserData');
        //  dd(json_decode($response));
    }
}
function send_admin_notification($message='',$title='',$channel_name=''){
    // dd('dddddddddd');
    //Admin Notification//
		$publishKey ='pub-c-d7274ea7-836f-4faf-b396-bc4bc9e9f99e';
		$subscribeKey= 'sub-c-560305a8-8b03-11eb-83e5-b62f35940104';
	
		$curl_admin = curl_init();
		curl_setopt_array($curl_admin, array(
		  CURLOPT_URL => "https://ps.pndsn.com/publish/$publishKey/$subscribeKey/0/$channel_name/myCallback?store=0&uuid=db9c5e39-7c95-40f5-8d71-125765b6f561",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n  \"message\": \"$message\", \"title\": \"$title\"}\n",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"location: /publish/$publishKey/$subscribeKey/0/pubnub_onboarding_channel_admin_1/0",
			"postman-token: d536d8da-8709-14cb-3c6d-ee6e19bc9fe5"
		  ),
		));

		$responseNew = curl_exec($curl_admin);
		$err = curl_error($curl_admin);

		curl_close($curl_admin);

		if ($err) {
		//   echo "cURL Error #:" . $err;
		} else {
		//   echo $responseNew;
		}
		//Admin Notification End//
}
function add_admin_notification($user_type = '', $notification_type='', $notification_for='', $title='', $message='', $user_id='', $order_id=''){
    $lang = App::getLocale();
   
     App::SetLocale('en');
    $lang = App::getLocale();

    // $notification_title = __('api.notification_court_book_title');
    $message_lang = __('backend.'.$message);
    $title_lang = __('backend.'.$title);

    $notificationData = new PanelNotifications();
    $notificationData->user_id = $user_id;
    $notificationData->user_type = $user_type;
    $notificationData->notification_type = $notification_type;
    $notificationData->notification_for = $notification_for;
    $notificationData->order_id = $order_id;
    $notificationData->title = $title_lang;
    $notificationData->message = $message_lang;
    $notificationData->save();
    // insert notification lang data
    if(isset($notificationData)){
     $notificationLang = new PanelNotificationLang();
     $notificationLang->panel_notification_id = $notificationData->id;
     $notificationLang->title = $title_lang;
     $notificationLang->message = $message_lang;
     $notificationLang->lang = $lang;
     $notificationLang->save();
        // insert AR lang
          App::SetLocale('ar');
        $lang = App::getLocale();
        $message_lang = __('backend.'.$message);
        $title_lang = __('backend.'.$title);
     $notificationLang = new PanelNotificationLang();
     $notificationLang->panel_notification_id = $notificationData->id;
     $notificationLang->title = $title_lang;
     $notificationLang->message = $message_lang;
     $notificationLang->lang = $lang;
     $notificationLang->save();
    }
    

   
}
