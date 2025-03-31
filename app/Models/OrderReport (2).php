<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderReport extends Model
{    
    protected $table = 'order_reports';
    protected $fillable = [
        'order_id','message','is_reply','replied_message','created_at'
    ];  
}
