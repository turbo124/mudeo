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

        return redirect('https://play.google.com/store/apps/details?id=app.mudeo.mudeo');

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
