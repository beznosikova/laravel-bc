<?php
namespace App\Services\Helpers;

use Storage;
use App\Services\Helpers\LoadedData;

class LoadedExchange extends LoadedData
{
    protected $columnMatching = 
        [
            0 => 'bc_id', 
            1 => 'title', 
        ];
}