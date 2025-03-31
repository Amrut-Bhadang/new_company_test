<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ContactUs extends Model
{    
    protected $table = 'contact_us';
    protected $fillable = [
        'name','email','mobile','country_code','message','status'
    ];

   
}
