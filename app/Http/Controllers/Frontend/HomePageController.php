<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Log;



class HomePageController extends Controller
{



    public function index()
    {
        $pageTitle = 'Briz Apparel Group';
        try {
            return view('frontend.index', compact('pageTitle'));
        } catch (Exception $e) {
            Log::error('Failed to open home page: ' . $e->getMessage());
            return back()->with('error', 'Unable to load home page.');
        }
    }



}