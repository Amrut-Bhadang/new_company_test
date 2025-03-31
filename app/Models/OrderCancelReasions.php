<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;

class OrderCancelReasions  extends Model
{
  protected $hidden = [
      'updated_at',
  ]; 
  
  protected  $table = 'order_cancel_reasions';
}
