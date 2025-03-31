<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftCartTopping extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gift_cart_toppings';
}
