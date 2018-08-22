<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
    	'bc_id_from', 
    	'bc_id_to', 
    	'bc_id_exchange', 
    	'rate_from', 
    	'rate_to'
    ];	
    public $timestamps = false;

    public function exchange()
    {
        return $this->belongsTo('App\Exchange', 'bc_id_exchange', 'bc_id');
    }    
}
