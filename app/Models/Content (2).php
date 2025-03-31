<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth, App;
use App\Models\ContentLang;

class Content extends Model
{
  protected $fillable = [
    'name', 'description', 'status',
  ];

  protected $hidden = [
    'updated_at', 'deleted_at',
  ];


  public function getNameAttribute($value)
  {
    $locale = App::getLocale();
    $data =  ContentLang::select('name')->where(['content_id' => $this->id, 'lang' => $locale])->first();
    return $data->name ?? $value;
  }
  public function getDescriptionAttribute($value)
  {
    $locale = App::getLocale();
    $data =  ContentLang::select('description')->where(['content_id' => $this->id, 'lang' => $locale])->first();
    return $data->description ?? $value;
  }
}
