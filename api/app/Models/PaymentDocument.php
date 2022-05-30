<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDocument extends Model
{
    use SoftDeletes;

//    protected $fillable = [
//        'name'
//    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];
    protected $table = 'payment_documents';

    public function campaign()
    {
        return $this->hasOne('App\Models\Campaign', 'id', 'campaign_id');
    }
}
