<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;

class Tax  extends Model
{    
    protected $fillable = [
        'country_id','tax','status','created_at'
    ];

    protected $hidden = [
        'updated_at',
    ]; 
    
    protected  $table = 'tax';
}
