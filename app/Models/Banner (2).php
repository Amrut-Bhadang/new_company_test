<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class Banner extends Model
{    
	
    protected $table = 'banners';
    protected $fillable = [
        'title','description','image','status','type','type_id'
    ];

    public function getImageAttribute($value)
	{
		if ($value) {
			return url('uploads/banner/' . $value);
		} else {
			return url('images/no-image-available.png');
		}
	}
   
}
