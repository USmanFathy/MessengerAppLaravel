<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'label',
        'last_message_id'
    ];

    public function participants():belongsToMany
    {
        return $this->belongsToMany(User::class,'participants')
            ->withPivot([
                'joined_at','role'
            ]);
    }
    public function messages():hasMany
    {
     return $this->hasMany(Message::class,'conversation_id','id')->latest();
    }
    public function user():belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function lastMessage()
    {
        return $this->belongsTo(Message::class,'last_message_id')->withDefault();
    }
}
