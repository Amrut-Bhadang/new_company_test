<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class TestimonialLang extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'testimonials_lang';
    protected $fillable = [
        'testimonial_id','title','lang','description'
    ];

   
}
