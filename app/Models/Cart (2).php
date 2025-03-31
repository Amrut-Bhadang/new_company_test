<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\Products;
class Cart extends Model
{    
    protected $table = 'cart';
    protected $fillable = [
        'product_id','parent_cart_id','user_id','qty','amount','product_price','points'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function products()
    {
        return $this->hasOne(Products::class,'id','product_id');
    }
    
}
