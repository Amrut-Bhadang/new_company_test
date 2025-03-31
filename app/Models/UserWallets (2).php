<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class UserWallets extends Model
{    
    protected $table  ='user_wallets';    
    protected $fillable = [
        'id','user_id','transaction_type','amount','comment','status'
    ];

    protected $hidden = [
        'updated_at'
    ];
    
    
}
