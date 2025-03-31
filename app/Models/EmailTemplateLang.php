<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class EmailTemplateLang extends Model
{
    protected $table  ='email_template_lang';    
    protected $fillable = [
        'name','slug','subject','description','email_id'
    ];

}
