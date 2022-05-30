<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class AdjustmentContacts extends Model
{
    protected $table = 'adjustment_contacts';

    protected $fillable = [
        'data', 'cabinet_id'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
