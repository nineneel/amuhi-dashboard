<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(): View
    {
        return view('forum.index');
    }
}
