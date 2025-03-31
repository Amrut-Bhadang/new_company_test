<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class Testimonial extends Model
{    
    protected $table = 'testimonials';
    protected $fillable = [
        'title','description','status'
    ];
}
