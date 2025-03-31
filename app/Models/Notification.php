<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;
use Illuminate\Support\Facades\App;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'user_type', 'notification_type', 'title', 'message', 'user_id', 'is_read', 'status', 'created_at',
    ];
    protected $hidden = [
        'updated_at'
    ];
    public function getTitleAttribute($value)
  {
    $locale = App::getLocale();
    $data =  NotificationLang::select('title')->where(['notification_id' => $this->id, 'lang' => $locale])->first();
    return $data->title ?? $value;
  }
  public function getMessageAttribute($value)
  {
    $locale = App::getLocale();
    $data =  NotificationLang::select('message')->where(['notification_id' => $this->id, 'lang' => $locale])->first();
    return $data->message ?? $value;
  }
}
