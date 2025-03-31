<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class AmenityLang extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'amenities_lang';
    protected $fillable = [
        'amenity_id','name','lang'
    ];

   
}
