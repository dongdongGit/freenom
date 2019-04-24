<?php

namespace App\Http\Controllers\Api;

use App\Jobs\Webhooks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UtilController extends Controller
{
    public function webhooks(Request $request)
    {
        dispatch(new Webhooks($request->all()));

        return $this->success();
    }
}
