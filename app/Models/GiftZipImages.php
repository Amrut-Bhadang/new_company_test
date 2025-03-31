<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;

class GiftZipImages extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gift_zip_images';
}
