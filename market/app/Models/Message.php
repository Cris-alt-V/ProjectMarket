<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = ['sender_id', 'receiver_id', 'product_id', 'subject', 'body', 'read_at'];
}
