<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\FreenomSync;
use App\Services\FreenomService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FreenomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = $this->user()->domains()->apiPaginate(10);

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
        // TODO:
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
            'enabled_auto_renew' => 'boolean',
            'renew'              => 'integer|between:1,12',
        ]);

        $domain = $this->user()->domains()->findOrFail($id);
        $domain->update($data);

        return $this->success($domain);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $domain = $this->user()->domains()->findOrFail($id);
        $domain->delete();

        return $this->success();
    }

    public function action(Request $request)
    {
        $data = $request->validate([
            'action'              => 'required|in:sync,renew',
            'domains'             => 'required_if:action,renew|array',
            'domains.*.domain_id' => 'required_if:action,renew|integer',
            'domains.*.renew'     => 'required_if:action,renew|integer|between:1,12'
        ]);

        $user = $this->user();

        // TODO: job
        // $freenomService = new FreenomService();

        if ($data['action'] === 'sync') {
            // $freenomService->sync();
            dispatch(new FreenomSync());
        } elseif ($data['action'] === 'renew' && !empty(array_get($data, 'domains', []))) {
            $domains = $user->domains()->whereIn('domain_id', collect($data['domains'])->pluck('domain_id'))->get();

            if ($domains->count() != count($data['domains'])) {
                return $this->abort(403, '权限不足, 无法操作'); // error
            }

            dispatch(new FreenomRenew($domains));

            // $freenomService->renew($domains);
        }

        return $this->success();
    }
}
