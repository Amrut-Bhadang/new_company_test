<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;
use Illuminate\Support\Facades\App;

class PanelNotificationLang extends Model
{   
    protected $table = 'panel_notification_lang';
    protected $fillable = [
        'id','panel_notification_id','title','message',
    ];
    protected $hidden = [
        'updated_at'
    ];
}
