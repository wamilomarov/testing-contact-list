<?php

namespace App\Listeners;

use App\Events\ChangesCommitted;
use App\Http\Services\SalesforceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncContactRecords
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param ChangesCommitted $event
     * @return void
     */
    public function handle(ChangesCommitted $event)
    {
        $event->contact->update([
            'salesforce_id' => $event->salesforceId,
            'is_synced' => true
        ]);
    }
}
