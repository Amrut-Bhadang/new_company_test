<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;
use Illuminate\Support\Facades\App;

class PanelNotifications extends Model
{
    protected $table = 'panel_notifications';

    protected $fillable = [
        'id','user_type','notification_type','notification_for','title','message','user_id','order_id','is_read','status','created_at',
    ];

    protected $hidden = [
        'updated_at'
    ];   
    public function getTitleAttribute($value)
    {
      $locale = App::getLocale();
      $data =  PanelNotificationLang::select('title')->where(['panel_notification_id' => $this->id, 'lang' => $locale])->first();
      return $data->title ?? $value;
    }
    public function getMessageAttribute($value)
    {
      $locale = App::getLocale();
      $data =  PanelNotificationLang::select('message')->where(['panel_notification_id' => $this->id, 'lang' => $locale])->first();
      return $data->message ?? $value;
    }
}
