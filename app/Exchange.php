<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $fillable = [
        'bc_id',
        'title',
        'slug',
    ];
    public $timestamps = false;
}
