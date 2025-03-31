<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class CourtCategoryLang extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'court_categories_lang';
    protected $fillable = [
        'court_category_id','name','lang'
    ];

   
}
