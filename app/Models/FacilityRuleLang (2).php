<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class FacilityRuleLang extends Model
{
    protected $table = 'facility_rules_lang';
    protected $fillable = [
        'facility_rule_id', 'rules', 'lang'
    ];

    protected $hidden = [
        'updated_at',
    ];
}
