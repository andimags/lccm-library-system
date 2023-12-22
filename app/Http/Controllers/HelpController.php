<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index(){
        $prefixes = \App\Models\Setting::where('field', 'prefix')->first()->holdingOptions()->pluck('value')->toArray();

        return view('help.index')
            ->with('prefixes', $prefixes);
    }
}
