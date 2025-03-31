<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;
use Illuminate\Support\Facades\App;

class AdminNotification extends Model
{   
    protected $table = 'admin_notification';

    protected $fillable = [
        'notification_type','title','message','user_id','is_read','status','created_at','notification_for'
    ];
    protected $hidden = [
        'updated_at'
    ];

    public function getTitleAttribute($value)
    {
      $locale = App::getLocale();
      $data =  AdminNotificationLang::select('title')->where(['admin_notification_id' => $this->id, 'lang' => $locale])->first();
      return $data->title ?? $value;
    }
    public function getMessageAttribute($value)
    {
      $locale = App::getLocale();
      $data =  AdminNotificationLang::select('message')->where(['admin_notification_id' => $this->id, 'lang' => $locale])->first();
      return $data->message ?? $value;
    }
   
}
