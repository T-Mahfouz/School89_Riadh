<?php

namespace App\Http\Controllers;

use App\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	//$this->middleware('auth')/*->except('welcome')*/;
    }

    public function index()
    {
    	if(Auth::user())
        	return redirect()->route('home');
        $urls = Url::first();    
    	return view('welcome',compact('urls'));
    }
}
