<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\ModesLang;

class RestaurantMode extends Model
{    
    protected  $table = 'restaurant_modes';

    protected $fillable = [
        'mode_id','restaurant_id'
    ];
   
}
