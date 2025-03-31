<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
class UserFavourates extends Model
{    
    // protected  $appends = ['total_dish'];
    protected $fillable = [
        'typeId','type','user_id'
    ];

    protected $table = 'user_favourates';

    protected $hidden = [
        'updated_at',
    ];
}
