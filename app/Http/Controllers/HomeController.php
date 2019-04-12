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
        return redirect('https://itsallwidgets.com/flutter-app/mudeo');
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
