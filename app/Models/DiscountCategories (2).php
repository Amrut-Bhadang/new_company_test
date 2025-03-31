<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class DiscountCategories extends Model
{    
    protected $table = 'discount_categories';
    protected $fillable = [
        'category_id','discount_id'
    ];

    protected $hidden = [
        'updated_at',
    ];

    
    
}