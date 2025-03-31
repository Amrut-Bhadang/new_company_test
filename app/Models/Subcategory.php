<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth,App;
use App\Models\Products;
use App\Models\SubcategoryLang;
class Subcategory extends Model
{    
    // protected  $appends = ['parents'];
    protected $fillable = [
        'id','name','main_category_id','category_id','status','parent_id'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function getNameAttribute($value)
    {   
      $locale = App::getLocale();
      $data =  SubcategoryLang::select('name')->where(['sub_category_id'=>$this->id,'lang'=>$locale])->first();
      return $data->name ?? $value;
    }

    public function getParentsAttribute()
    {
        $parentIds = collect([]);

        $parent = $this->parent;

        while(!is_null($parent)) {
            $parentIds->push($parent->id);
            $parent = $parent->parent;
        }

        return $parentIds;
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Subcategory', 'parent_id');
    }

    public function childs() {
      return $this->hasMany('App\Models\Subcategory','parent_id','id')->select(['id','main_category_id','category_id','name','parent_id']);
    }
}
