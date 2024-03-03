<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Pivot
{
    use HasFactory,SoftDeletes;
    public $timestamps = false;
    protected $casts = [
        'read_at' => 'datetime',
        'received_at' => 'datetime',
    ];
    public function message():belongsTo
    {
        return $this->belongsTo(Message::class);
    }
    public function user():belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
