<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;

class Rating extends Model
{    
    protected $table = 'rating';
    protected $fillable = [
        'order_id','restaurant_id','reveiw','user_id','rating'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')->select(['id','name','image']);
    }
   
}
