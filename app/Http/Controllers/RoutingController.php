<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class RoutingController extends Controller
{
    //
    public function countriesSeed() 
    {
        return view('services.countries.seed');
    }
    public function home()
    {
        return view('dashboard');
    }
    public function root()
    {
        $locale = App::currentLocale();
        return redirect()->route('home', $locale);
    }
    public function setSession(Request $request) {
        session(['theme' => $request['theme']]);
        return response('set', 200);
    }
}
