<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    
    /**
     * Show terms and conditionals page
     * 
     * @return \Illuminate\Http\Response
     */
    public function termsAndConditions(){
        return view('terms');
    }


    /**
     * Show privacy and policy page
     * 
     * @return \Illuminate\Http\Response 
     */
    public function privacyAndPolicy(){
        return view('policy');
    }
}
