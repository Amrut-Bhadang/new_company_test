<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\BrandLang;

class Info  extends Model
{    
    protected $fillable = [
        'name','description','slug','status'
    ];

    protected $hidden = [
        'updated_at',
    ]; 
    
    protected  $table = 'info';

    

}
