<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth, App;

class DiscountReadUsers  extends Model
{
    // protected $appends = ['applied_user'];

    protected $fillable = [
        'discount_id','user_id'
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected  $table = 'discount_code_read_users';

}
