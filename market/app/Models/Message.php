<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    public const UPDATED_AT = null;

    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id', 'product_id', 'body', 'read_at'];
}
