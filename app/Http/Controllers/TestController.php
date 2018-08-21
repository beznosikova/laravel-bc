<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BestChange;

class TestController extends Controller
{
    public function index()
    {
    	try {
	    	$bc = new BestChange();
	    	if ($bc->loadFiles()){
	    		$bc
	    			->loadCurrencies()
	    			->loadExchanges()
	    			->loadRates()
	    			;
	    	}
		} catch(\Exception $e){
		    dd($e->getMessage());
		}
    	return 'Hello!!!';
    }


}
