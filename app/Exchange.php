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

	public function rates()
	{
		return $this->hasMany('App\Rate', 'bc_id_exchange', 'bc_id');
	}    
}
