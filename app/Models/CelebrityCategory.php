<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\Products;
class CelebrityCategory extends Model
{    
    protected $fillable = [
        'id','name','image','description'
    ];

    protected $hidden = [
        'updated_at',
    ];
   

}
