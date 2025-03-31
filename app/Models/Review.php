<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Review extends Model
{    
    protected $table = 'reviews';
    protected $fillable = [
        'type','type_id','rating','status'
    ];

    protected $hidden = [
        'updated_at'
    ];
   
}
