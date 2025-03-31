<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftCategoryLang extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gift_categories_lang';       
    protected $tab  ='gift_categories';    
}
