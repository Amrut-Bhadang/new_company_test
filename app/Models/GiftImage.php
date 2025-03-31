<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftImage extends Model
{
	protected $connection = 'mysql2';
    protected $table = 'gift_images';

    protected $fillable = [
        'gift_id','image',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            return url('uploads/gift/'.$value);

        } else {
            return url('images/image.png');
        }
    }   
}
