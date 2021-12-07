<?php

namespace App\Events;

use App\Models\Contact;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangesCommitted
{
    use Dispatchable, SerializesModels;

    public Contact $contact;
    public string $salesforceId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Contact $contact, $salesforceId)
    {
        $this->contact = $contact;
        $this->salesforceId = $salesforceId;
    }

}
