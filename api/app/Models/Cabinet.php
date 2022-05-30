<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cabinet extends Model
{
    use SoftDeletes;

    protected $fillable = ["name"];

    protected $casts = [
        'name' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];


    public function members()
    {
        return $this->hasMany('App\Models\Member', 'cabinet_id');
    }

    public function domains()
    {
        return $this->hasMany('App\Models\Domain', 'cabinet_id');
    }

    public function providers()
    {
        return $this->belongsToMany('App\Models\Provider', 'cabinet_provider', 'cabinet_id', 'provider_id');
    }
}
