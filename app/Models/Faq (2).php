<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Faq extends Model
{    
    protected $table = 'faq';
    protected $fillable = [
        'type','question','answer','status'
    ];

    protected $hidden = [
        'updated_at',
    ];

    
    
}