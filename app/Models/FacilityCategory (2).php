<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class FacilityCategory extends Model
{
	// protected $appends = ['customer_name'];
	protected $table = 'facility_categories';
	protected $fillable = [
		'facility_id', 'category_id'
	];

	public function getImageAttribute($value)
	{
		if ($value) {
			return url('uploads/court_category/' . $value);
		} else {
			return url('images/no-image-available.png');
		}
	}
	public function categoryDetails()
	{
		return $this->hasOne(CourtCategory::class, 'id', 'category_id');
	}
}
