<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;

class AdminNotificationLang extends Model
{   
    protected $table = 'admin_notification_lang';

    protected $fillable = [
        'admin_notification_id','title','message','lang'
    ];
    protected $hidden = [
        'updated_at'
    ];
   
}
