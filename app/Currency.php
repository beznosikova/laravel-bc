<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'bc_id',
        'title',
    ];
    public $timestamps = false;
}
