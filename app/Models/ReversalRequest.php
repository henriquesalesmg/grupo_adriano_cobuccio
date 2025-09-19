<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReversalRequest extends Model
{
    protected $fillable = [
        'transaction_id',
        'requester_id',
        'receiver_id',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
