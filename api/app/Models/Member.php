<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
//    use SoftDeletes;

    protected $table = 'members';

    protected $fillable = [
        'user_id', 'cabinet_id', 'role_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    public function cabinet()
    {
        return $this->belongsTo('App\Models\Cabinet', 'cabinet_id');
    }

//    public function cabinets()
//    {
//        return $this->belongsToMany('App\Models\Cabinet', 'user_cabinet', 'cabinet_id', 'user_id');
//    }
//
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
