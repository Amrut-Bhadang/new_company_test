<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FaqRequest extends Model
{    
    protected $table = 'faq_request';
    protected $fillable = [
        'user_id','question','type','status'
    ];

    protected $hidden = [
        'updated_at',
    ];   
}