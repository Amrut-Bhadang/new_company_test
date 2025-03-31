<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class FacilityAmenity extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'facility_amenities';
    protected $fillable = [
        'facility_id','amenity_id'
    ];

    public function getImageAttribute($value)
	{
		if ($value) {
			return url('uploads/amenity/' . $value);
		} else {
			return url('images/no-image-available.png');
		}
	}
	public function amenityDetails()
	{
		return $this->hasOne(Amenity::class, 'id', 'amenity_id');
	}
   
}
