<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class UserDomain extends Model
{
    protected $table = 'user_domain';
    protected $appends = array('id');
    public function getIdAttribute(){
        return $this->attributes['user_id'];
    }
    public function contacts()
    {
        return $this->hasMany('App\Models\Contact', 'user_id', 'user_id')->with('session');
    }
}
