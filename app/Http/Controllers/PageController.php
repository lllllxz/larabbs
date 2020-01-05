<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class PageController extends Controller
{
    public function root()
    {
        return view('pages.root');
    }

    public function permissionDenied()
    {
        if (config('administrator.permission')()){
            return redirect()->to(config('administrator.uri'), 302);
        }

        return view('pages.permission_denied');
    }
}
