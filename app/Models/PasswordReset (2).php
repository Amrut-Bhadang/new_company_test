<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class PasswordReset extends Model
{    
    protected $table = 'password_resets';
	public $timestamps = false;
    protected $fillable = [
        'email','token'
    ];
   

   
}
