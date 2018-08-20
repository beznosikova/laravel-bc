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
    		//load currency if table is empty
    		$bc->loadCurrencies();
    	}

    	return 'Hello!!!';
    }


}
