<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'bc_id',
        'title',
        'slug',
    ];
    public $timestamps = false;
}
