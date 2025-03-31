<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class DiscountCountries extends Model
{    
    protected $table = 'discount_countries';
    protected $fillable = [
        'discount_id','country_id','status'
    ];

    protected $hidden = [
        'updated_at',
    ];

    
    
}