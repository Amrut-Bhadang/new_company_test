<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\App;

class EmailTemplate extends Model
{    
    protected $table  ='email_templates';    
    protected $fillable = [
        'name','slug','subject','description'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
    public function getNameAttribute($value)
    {
      $locale = App::getLocale();
      $data =  EmailTemplateLang::select('name')->where(['email_id' => $this->id, 'lang' => $locale])->first();
      return $data->name ?? $value;
    }
    public function getSubjectAttribute($value)
    {
      $locale = App::getLocale();
      $data =  EmailTemplateLang::select('subject')->where(['email_id' => $this->id, 'lang' => $locale])->first();
      return $data->subject ?? $value;
    }
    public function getDescriptionAttribute($value)
    {
      $locale = App::getLocale();
      $data =  EmailTemplateLang::select('description')->where(['email_id' => $this->id, 'lang' => $locale])->first();
      return $data->description ?? $value;
    }
    
    
}
