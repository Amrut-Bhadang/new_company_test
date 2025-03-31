<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftAttributeValueLang extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='attribute_value_lang';       
    protected $tab  ='attribute_values';    
}
