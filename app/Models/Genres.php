<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Genres extends Model
{    
    protected $table = 'celebrity_categories';
    protected $fillable = [
        'name','description'
    ];

    protected $hidden = [
        'updated_at'
    ];

   
}
