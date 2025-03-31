<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;

class GiftAttributeValues extends Model
{    
	protected $connection = 'mysql2';
    protected  $table = 'attribute_values';

    protected $fillable = [
        'attributes_lang_id',
    ];

    protected $hidden = [
        'updated_at',
    ];
   
}
