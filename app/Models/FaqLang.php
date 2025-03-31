<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FaqLang extends Model
{    
    protected $table = 'faq_lang';
    protected $fillable = [
        'faq_id','question','answer','lang','status'
    ];

    protected $hidden = [
        'updated_at',
    ];

    
    
}