<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model {

    protected $table = 'record';
    protected $fillable = ['chat_id', 'city', 'water', 'bottle', 'pomp', 'price', 'address', 'date', 'phone'];
    const UPDATED_AT = null;

}