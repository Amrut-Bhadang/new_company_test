<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftCartDetail extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'gift_cart_details';
    protected $fillable = [
        'gift_cart_id','gift_id','user_id','qty','points'
    ];

    protected $hidden = [
        'updated_at',
    ];    
}
