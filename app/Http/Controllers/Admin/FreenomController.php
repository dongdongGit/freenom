<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FreenomService;

class FreenomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = $this->user()->domains()->apiPaginate();

        return $this->success($domains);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $domain = $this->user()->domains()->find($id);

        return $this->success($domain);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'enabled_auto_renew' => 'boolean'
        ]);

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $domain = $this->user()->domains()->find($id);
        $domain->delete();

        return $this->success();
    }

    public function action(Request $request)
    {
        $data = $request->validate([
            'action'              => 'required|in:sync,renew',
            'domains'             => 'required_if:action,renew|array',
            'domains.*.domain'    => 'required_if:action,renew|string',
            'domains.*.domain_id' => 'required_if:action,renew|integer',
        ]);

        // TODO: job
        $freenomService = new FreenomService();
        $freenomService->sync();

        return $this->success();
    }
}
