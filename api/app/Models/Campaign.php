<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Campaign extends Model
{
    use SoftDeletes;

//    protected $fillable = [
//        'name'
//    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = array('name');

    public function getNameAttribute()
    {
        return 'Кампания-' . $this->id;
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PaymentDocument', 'campaign_id', 'id');
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain', 'domain_id', 'id');
    }

    public function tariff()
    {
        return $this->belongsTo('App\Models\Tariff', 'tariff_id', 'id');
    }
}
