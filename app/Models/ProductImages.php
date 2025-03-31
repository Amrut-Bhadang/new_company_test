<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ProductImages extends Model
{    
    protected $table = 'product_images';
    protected $fillable = [
        'product_id','image',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            return url('uploads/product/'.$value);

        } else {
            return url('images/no-image-available.png');
        }
    }
   
}