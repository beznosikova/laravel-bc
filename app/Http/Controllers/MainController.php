<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Rate;
use App\Exchange;
use Illuminate\Http\Request;
use App\Services\BestChange;

class MainController extends Controller
{
	public function index(Request $request)
    {
    	if ($request->isMethod('post')){
	    	$this->validate($request, [
		        'give' => 'integer',
		        'get' => 'integer',
		    ]);
    	}
		$give = $request->input('give', 92);
		$get = $request->input('get', 40);

		$rate = Rate::where('bc_id_from', $give)
               ->where('bc_id_to', $get)
               ->first();

    	$currencies = Currency::orderBy('title', 'ASC')->get();
    	return view('bc', compact('currencies','give', 'get', 'rate'));
    }

    public function load()
    {
        try {
        	$bcLoaded = (new BestChange())->run();
            return ($bcLoaded) ? "success" : "fail";
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
