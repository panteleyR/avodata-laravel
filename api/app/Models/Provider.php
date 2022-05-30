<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'on', 'description', 'price', 'ip', 'token'
    ];

    protected $table = 'providers';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function cabinets()
    {
        return $this->belongsToMany('App\Models\Cabinet', 'cabinet_provider', 'provider_id', 'cabinet_id');
    }
}
