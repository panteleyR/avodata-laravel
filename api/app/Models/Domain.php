<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Domain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'user_id',
        'token',
        'cabinet_id',
        'js_before',
        'js_after',
        'api_url',
        'geo_on',
        'script_state',
        'last_script_activity'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_script_activity' => 'datetime'
    ];

    public function cabinet()
    {
        return $this->belongsTo('App\Models\Cabinet', 'cabinet_id');
    }

    public function currentCampaign()
    {
        return $this->hasOne('App\Models\Campaign', 'domain_id')
            ->where('from', '<=', Carbon::now())
            ->where('to', '>=', Carbon::now());
    }

    public function campaigns()
    {
        return $this->hasMany('App\Models\Campaign', 'domain_id');
    }

    public function geo()
    {
        return $this->belongsToMany('App\Models\GeoCity', 'domain_geo_city', 'domain_id', 'geo_city_id');
    }
}
