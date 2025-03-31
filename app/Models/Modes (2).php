<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\ModesLang;

class Modes extends Model
{    

    protected  $table = 'modes';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'updated_at',
    ];
   
}
