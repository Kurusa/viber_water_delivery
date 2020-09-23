<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = 'user';
    protected $fillable = ['chat_id', 'user_name', 'status'];
    const UPDATED_AT = null;

}