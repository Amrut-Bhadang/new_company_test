<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;
use Illuminate\Support\Facades\App;

class CourtCategory extends Model
{
	// protected $appends = ['facility_amenities'];
	protected $table = 'court_categories';
	protected $fillable = [
		'name', 'image', 'status'
	];

	public function getImageAttribute($value)
	{
		if ($value) {
			return url('uploads/court_category/' . $value);
		} else {
			return url('images/no-image-available.png');
		}
	}
	public function getNameAttribute($value)
	{
		$locale = App::getLocale();
		$data =  CourtCategoryLang::select('name')->where(['court_category_id' => $this->id, 'lang' => $locale])->first();
		return $data->name ?? $value;
	}
}
