<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;
use Illuminate\Support\Facades\App;

class Amenity extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'amenities';
    protected $fillable = [
        'name','status','image'
    ];
    public function getImageAttribute($value)
	{
		if ($value) {
			return url('uploads/amenity/' . $value);
		} else {
			return url('images/no-image-available.png');
		}
	}
	public function getNameAttribute($value)
  {
    $locale = App::getLocale();
    $data =  AmenityLang::select('name')->where(['amenity_id' => $this->id, 'lang' => $locale])->first();
    return $data->name ?? $value;
  }

   
}
