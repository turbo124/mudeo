<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     *
     */
    public function index()
    {
	if (config('mudeo.is_dance')) {	
		return redirect('https://play.google.com/apps/testing/app.mudeo.dancelikeme');
	}

        return view('flutter');
    }

    public function terms()
    {
        return view('terms');
    }

    public function privacy()
    {
        return view('privacy');
    }
}
