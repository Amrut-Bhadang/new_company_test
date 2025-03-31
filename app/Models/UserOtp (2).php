<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class UserOtp extends Model
{    
    protected $table = 'user_otps';
    protected $fillable = [
        'user_id', 'otp'
    ];
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
   
}
