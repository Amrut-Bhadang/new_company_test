<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;

class GiftUserPlatforms extends Model
{    
    protected $connection = 'mysql2';
    protected $table = 'user_platforms';

    protected $fillable = [
        'user_id', 'platform', 'password', 'platform_token', 'uuid', 'callback_url',
    ];

    protected $hidden = [
        'updated_at',
    ];   
}
