<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CronJob extends Model
{    
    protected $table = 'cron_jobs';
    protected $fillable = [
        'value'
    ];

   
}
