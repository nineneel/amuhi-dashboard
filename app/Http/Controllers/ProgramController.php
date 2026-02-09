<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        return view('programs.index');
    }
}
