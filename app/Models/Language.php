<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
class Language extends Model
{    

    protected $table  ='languages';    
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
    
    protected $tab  ='Language'; 

    
}