<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function sanctionsStats()
    {
        $messages = __('messages');

        return view('models/sanctions/statistics/index', compact('messages'));
    }
}
