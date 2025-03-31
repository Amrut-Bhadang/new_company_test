<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Country extends Model
{    
    protected $table = 'countries';
    protected $fillable = [
        'sortname','name','phonecode'
    ];

   
}
