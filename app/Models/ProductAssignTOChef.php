<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\Products;

class ProductAssignTOChef extends Model
{    
    protected $table = 'product_assign_to_chef';
    protected $fillable = [
        'chef_id','product_id',
    ];

    protected $hidden = [
        'updated_at'
    ];
    
   
    
}