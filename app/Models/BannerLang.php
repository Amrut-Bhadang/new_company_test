<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class BannerLang extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'banners_lang';
    protected $fillable = [
        'banner_id','title','lang'
    ];

   
}
