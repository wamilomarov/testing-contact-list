<?php

namespace App\Listeners;

use App\Events\ContactDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncContactDeletion
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ContactDeleted $event
     * @return void
     */
    public function handle(ContactDeleted $event)
    {
        $event->contact->isSynced();
        $event->contact->delete();
    }
}
