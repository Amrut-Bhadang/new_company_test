<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;
use Illuminate\Support\Facades\App;

class Facility extends Model
{
	protected $appends = ['facility_amenities', 'total_rating', 'facility_categories'];
	protected $table = 'facilities';
	protected $fillable = [
		'facility_owner_id', 'position', 'name', 'image', 'address', 'status', 'commission','latitude', 'longitude', 'average_rating'
	];

	public function getImageAttribute($value)
	{
		if ($value) {
			return url('uploads/facility/' . $value);
		} else {
			return url('images/default_facility.jpg');
		}
	}
	public function getNameAttribute($value)
	{
		$locale = App::getLocale();
		$data =  FacilityLang::select('name')->where(['facility_id' => $this->id, 'lang' => $locale])->first();
		return $data->name ?? $value;
	}

	public function getAverageRatingAttribute($value)
	{
		return number_format($value, 1);
	}
	public function getTotalRatingAttribute($value)
	{

		$total_rating = Review::where(['status' => 1, 'type' => 0, 'type_id' => $this->id])->count();
		return $total_rating;
	}

	public function getFacilityAmenitiesAttribute($value)
	{
		$locale = App::getLocale();
        if($locale == null){
            $locale = 'en';
        }
		$facility_amenities = FacilityAmenity::join('facilities', 'facilities.id', 'facility_amenities.facility_id')
			->join('amenities', 'amenities.id', 'facility_amenities.amenity_id')
			->join('amenities_lang', 'amenities.id', 'amenities_lang.amenity_id')
			->where('facility_amenities.facility_id', $this->id)
			->where('amenities_lang.lang', $locale)
			->select('amenities_lang.name')
			->pluck('name')->toArray();
		return implode(", ", $facility_amenities);
	}
	public function getFacilityCategoriesAttribute($value)
	{

		$facility_categories = FacilityCategory::join('facilities', 'facilities.id', 'facility_categories.facility_id')
			->join('court_categories', 'court_categories.id', 'facility_categories.category_id')
			->where('facility_categories.facility_id', $this->id)
			->select('court_categories.name')
			->pluck('name')->toArray();
		return implode(", ", $facility_categories);
	}
	public function facilityRules()
	{
		return $this->hasMany(FacilityRule::class, 'facility_id', 'id')->select('id', 'facility_id', 'rules');
	}
	public function facilityAmenities()
	{
		return $this->hasMany(FacilityAmenity::class, 'facility_id', 'id');
	}
	public function facilityCategory()
	{
		return $this->hasMany(FacilityCategory::class, 'facility_id', 'id');
	}
}
