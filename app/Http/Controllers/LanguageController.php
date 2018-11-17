<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function changeLanguage(Request $request){
        $request->session()->put('locale', $request->locale);
        return back();
    }
}
