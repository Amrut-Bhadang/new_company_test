<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FacilityLang extends Model
{    
    protected $table = 'facilities_lang';
    protected $fillable = [
        'facility_id','name','lang'
    ];

    protected $hidden = [
        'updated_at',
    ];

    
    
}