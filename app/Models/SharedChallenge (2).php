<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CartSplitBills;
use Auth;
use Illuminate\Support\Facades\App;

class SharedChallenge extends Model
{
    protected $table = 'shared_challenges';
    protected $fillable = [
        'court_booking_id', 'from_id', 'to_id',
    ];
    protected $hidden = [
        'updated_at'
    ];
}
