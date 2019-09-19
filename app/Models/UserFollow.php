<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    const UPDATED_AT = null;

    protected $primaryKey = ['user_id', 'follow_id'];

    protected $guarded = [];

    public $incrementing = false;
}
