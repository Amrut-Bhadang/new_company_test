<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class UserCourtFavourate extends Model
{    
    protected $table = 'user_court_favourates';
    protected $fillable = [
        'court_id','user_id' 
    ];
    protected $hidden = [
        'updated_at'
    ];
   
}
