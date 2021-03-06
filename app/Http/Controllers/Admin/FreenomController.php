<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\FreenomSync;
use App\Jobs\FreenomRenew;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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

    public function batch(Request $request)
    {
        $data = $request->validate([
            'action'              => 'required|in:sync,renew',
            'domains'             => 'required_if:action,renew|array',
            'domains.*.domain_id' => 'required_if:action,renew|integer',
            'domains.*.renew'     => 'required_if:action,renew|integer|between:1,12'
        ]);

        $user = $this->user();

        if ($data['action'] === 'sync') {
            dispatch(new FreenomSync($user));
        } elseif ($data['action'] === 'renew' && !empty(Arr::get($data, 'domains', []))) {
            $collect = collect($data['domains']);
            $unique = $collect->unique('domain_id');
            $domains = $user->domains()->whereIn('domain_id', $unique->pluck('domain_id'))->get();

            if ($domains->count() != $unique->count()) {
                return $this->abort(403, '权限不足, 无法操作'); // error
            }

            dispatch(new FreenomRenew($domains));
        }

        return $this->success();
    }
}
