<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class FacilityRule extends Model
{
	// protected $appends = ['customer_name'];
	protected $table = 'facility_rules';
	protected $fillable = [
		'facility_id', 'rules'
	];
}
