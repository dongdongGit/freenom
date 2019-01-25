<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    public function webhooks(Request $request)
    {
        dd($request->all());
    }
}
