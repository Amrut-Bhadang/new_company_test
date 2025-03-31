<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class GiftSubCategoryLang extends Model
{
	protected $connection = 'mysql2';
    protected $table  ='gift_sub_categories_lang';       
    protected $tab  ='gift_sub_categories';    
}
