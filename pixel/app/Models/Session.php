<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'domain_id', 'user_id', 'domain', 'title', 'token', 'url', 'ref', 'cookie', 'api_data'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
