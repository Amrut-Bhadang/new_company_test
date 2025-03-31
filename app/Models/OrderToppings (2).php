<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderToppings extends Model
{    
    protected $table = 'order_toppings';
    protected $fillable = [
        'order_detail_id','dish_topping_id','topping_name','price','status','created_at'
    ];

    protected $hidden = [
        'updated_at'
    ];

}
