<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\ToppingCategoryLang;
class Holiday extends Model
{    
    protected $fillable = [
        'id','status','created_at','holiday_reason','start_date_time','end_date_time'
    ];
    
    protected $table  ='holiday'; 
    
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
}
