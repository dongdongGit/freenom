<?php

namespace App\Observers;

use App\Models\Domain;

class DomainObserver
{
    /**
     * Handle the domain "created" event.
     *
     * @param  \App\Domain  $domain
     * @return void
     */
    public function created(Domain $domain)
    {
        //
    }

    /**
     * Handle the domain "updated" event.
     *
     * @param  \App\Domain  $domain
     * @return void
     */
    public function updated(Domain $domain)
    {
        $newData = $domain->getDirty();
        $oldData = $domain->getOriginal();

        $only_key = ['expires_date', 'renew'];
        $only_new_data = array_only($newData, $only_key);

        if (!empty($only_new_data)) {
            activity('freenom_update')
                ->causedBy($domain->user)
                ->performedOn($domain)
                ->withProperties([
                    'attributes' => $only_new_data,
                    'old'        => array_only($oldData, array_keys($only_new_data))
                ])
                ->log(':causer.name 修改 :subject.domain');
        }
    }

    /**
     * Handle the domain "deleted" event.
     *
     * @param  \App\Domain  $domain
     * @return void
     */
    public function deleted(Domain $domain)
    {
        //
    }

    /**
     * Handle the domain "restored" event.
     *
     * @param  \App\Domain  $domain
     * @return void
     */
    public function restored(Domain $domain)
    {
        //
    }

    /**
     * Handle the domain "force deleted" event.
     *
     * @param  \App\Domain  $domain
     * @return void
     */
    public function forceDeleted(Domain $domain)
    {
        //
    }
}