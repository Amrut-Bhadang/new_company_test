<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftBrandLang extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gift_brand_lang';       
    protected $tab  ='GiftBrand';    
}
