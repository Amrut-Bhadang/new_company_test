<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class RestaurantImages extends Model
{    
    protected $table = 'restaurant_images';
    protected $fillable = [
        'restaurant_id','image',
    ];

    public function getImageAttribute($value)
    {
      if ($value) {
          return url('uploads/user/'.$value);

      } else {
          return url('images/no-image-available.png');
      }
    }

    protected $hidden = [
        'updated_at'
    ];
   
}