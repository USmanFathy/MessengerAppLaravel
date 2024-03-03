<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable=[
        'conversation_id',
        'user_id',
        'body',
        'type'
    ];

    public function conversation():belongsTo
    {
        return $this->belongsTo(Conversation::class,'conversation_id');
    }
    public function user():belongsTo
    {
        return $this->belongsTo(User::class,'user_id')
            ->withDefault([
            'name' =>__('user')
            ]);
    }
    public function recipients():belongsToMany
    {
        return $this->belongsToMany(User::class,'recipients')
            ->withPivot([
            'read_at','deleted_at','received_at'
            ]);
    }
}
