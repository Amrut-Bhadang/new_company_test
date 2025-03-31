<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftVarientLang extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gift_varient_lang';       
    protected $tab  ='gift_varients';    
}
