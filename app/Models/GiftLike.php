<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;

class GiftLike extends Model
{
	protected $connection = 'mysql2';
  	protected  $table = 'gift_likes';
}
