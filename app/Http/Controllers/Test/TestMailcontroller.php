<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestMailcontroller extends Controller
{
    public function index()
    {
        return view('test.mail.index');
    }
}
