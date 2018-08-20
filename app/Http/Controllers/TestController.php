<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BestChange;

class TestController extends Controller
{
    public function index()
    {
    	$bc = new BestChange();
    	if ($bc->loadFiles()){
    		
    	}

    	return 'Hello!!!';
    }


}
