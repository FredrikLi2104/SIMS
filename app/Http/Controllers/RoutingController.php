<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class RoutingController extends Controller
{
    public function accountsettings() {
        //return view('content.extensions.ext-component-sliders');
        return view('content.pages.page-account-settings-account');
    }
    public function component() {
        //return view('content.extensions.ext-component-sliders');
        return view('content.pages.page-faq');
    }
    public function modal() {
        //return view('content.extensions.ext-component-sliders');
        return view('content.components.component-modals');
    }
    public function selecttwo() {
        return view('content.forms.form-elements.form-select');
    }
    public function todo() {
        //return view('content.extensions.ext-component-sliders');
        return view('content.apps.todo.app-todo');
    }
    //
    public function countriesSeed()
    {
        return view('services.countries.seed');
    }
    public function home()
    {
        //var_dump(session()->get('theme'));
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

    public function themeSwitcher(Request $request)
    {
        session(['theme' => $request['theme']]);
        return response('success', 200);
    }
}
