<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftOrderToppings extends Model
{    
	protected $connection = 'mysql2';
    protected $table = 'gift_order_toppings';
}
