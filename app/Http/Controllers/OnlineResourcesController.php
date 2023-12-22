<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnlineResourcesController extends Controller
{
    public function index(Request $request){
        return view('online-resources.index');
    }
}
