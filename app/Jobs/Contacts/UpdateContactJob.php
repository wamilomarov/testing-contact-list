<?php

namespace App\Jobs\Contacts;

use App\Events\ChangesCommitted;
use App\Http\Services\SalesforceService;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\ValidationException;

class UpdateContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Contact $contact;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ValidationException
     */
    public function handle()
    {
        $salesforceService = resolve(SalesforceService::class);

        $result = $salesforceService->update(
            $this->contact->salesforce_id,
            [
                'email' => $this->contact->email,
                'last_name' => $this->contact->last_name,
                'first_name' => $this->contact->first_name,
                'lead_source' => $this->contact->lead_source,
                'phone' => $this->contact->phone,
            ]
        );
        if (!is_null($result))
        {
            ChangesCommitted::dispatch($this->contact, $this->contact->salesforce_id);
        }
    }
}
