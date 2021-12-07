<?php

namespace App\Listeners;

use App\Events\SyncRequested;
use App\Http\Services\SalesforceService;

class SyncContactsByRequest
{
    private SalesforceService $salesforceService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->salesforceService = resolve(SalesforceService::class);
    }

    /**
     * Handle the event.
     *
     * @param SyncRequested $event
     * @return void
     */
    public function handle(SyncRequested $event)
    {
        $this->salesforceService->sync();
    }
}
