<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\BrandLang;
use App\Models\Orders;

class RestaurantTables  extends Model
{
  protected $hidden = [
      'updated_at',
  ];

  protected  $table = 'restaurant_tables';

}
