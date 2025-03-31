<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CourtPopularTime extends Model
{    
    protected $table = 'court_popular_time';
    protected $fillable = [
        'court_popular_time','day','time','status'
    ];

   
}
