<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\User;

class Commission extends Model
{    
	// protected $appends = ['customer_name'];
    protected $table = 'commissions';
    protected $fillable = [
        'amount','court_id','status'
    ];

   
}
