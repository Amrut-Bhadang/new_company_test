<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Currency extends Model
{    
    protected $table = 'currency';
    protected $fillable = [
        'currency_code','currency_name'
    ];

   
}
