<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;

class Inventory extends Model
{
	protected $connection = 'mysql2';
  	protected  $table = 'inventory';
  	protected $fillable = [
        'quantity','price','status','gift_category_id','gift_id'
    ];
}
