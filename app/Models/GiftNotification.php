<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;

class GiftNotification extends Model
{   
    protected $connection = 'mysql2';
    protected $table = 'gift_notifications';
    protected $fillable = [
        'user_type','notification_type','title','message','user_id','is_read','status','created_at',
    ];

    protected $hidden = [
        'updated_at'
    ];
   
}
