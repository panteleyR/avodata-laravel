<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderRun extends Model
{
    protected $table = 'providers_run';

    protected $fillable = [
        'provider_id', 'session_id',
    ];
}
