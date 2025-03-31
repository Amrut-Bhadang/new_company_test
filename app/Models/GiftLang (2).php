<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftLang extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gifts_lang';       
    protected $tab  ='Gift';    
}
