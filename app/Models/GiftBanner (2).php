<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\Gift;

class GiftBanner  extends Model
{    
    protected $connection = 'mysql2';
    protected $appends = ['gift_name'];
    protected $fillable = [
        'id','gift_id','gift_category_id','file_path','created_at',
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
    
    protected  $table = 'gift_banner';

    public function getFilePathAttribute($value)
    {
        // dd($value)
    	if ($value) {
      		return url('uploads/banner/'.$value);

    	} else {
      		return url('images/no-image-available.png');
    	}
    }
    public function getGiftNameAttribute($value){
        $gift_name = Gift::select('name')->where('id',$this->gift_id)->first();
        if($gift_name){
            return $gift_name->name;
        } else {
            return '';
        }
    }

}
