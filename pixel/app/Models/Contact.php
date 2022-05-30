<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'session_id', 'provider_id', 'user_id', 'domain_id', 'contacts_type_id', 'value',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function session()
    {
        return $this->hasOne('App\Models\Session', 'id', 'session_id');
    }
}
