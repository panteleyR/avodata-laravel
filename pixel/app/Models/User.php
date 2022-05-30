<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\Contact', 'user_id')->with('session');
    }

    public function domains()
    {
        return $this->hasMany('App\Models\UserDomain', 'user_id', 'id');
    }
}
