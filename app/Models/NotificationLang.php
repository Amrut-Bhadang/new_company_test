<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;
use Illuminate\Support\Facades\App;

class NotificationLang extends Model
{   
    protected $table = 'notification_lang';
    protected $fillable = [
        'id','notification_id','title','message',
    ];
    protected $hidden = [
        'updated_at'
    ];
}
