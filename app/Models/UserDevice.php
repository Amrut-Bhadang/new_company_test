<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class UserDevice extends Model
{   
    protected $table = 'user_devices';
   protected $fillable = [
        'user_id', 'device_token', 'device_type'
    ];
   protected $hidden = [
        'updated_at', 'deleted_at',
    ];
}

