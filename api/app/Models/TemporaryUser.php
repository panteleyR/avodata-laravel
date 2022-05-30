<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryUser extends Model
{
    protected $fillable = [
        'name', 'email', 'password'
    ];

    protected $table = 'temporary_users';
}
