<?php

namespace App\Jobs\Contacts;

use App\Events\ChangesCommitted;
use App\Http\Services\SalesforceService;
use App\Models\Contact;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateContactJob implements ShouldQueue
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
     */
    public function handle()
    {
        $salesforceService = resolve(SalesforceService::class);

        try {
            $result = $salesforceService->create(
                [
                    'email' => $this->contact->email,
                    'last_name' => $this->contact->last_name,
                    'first_name' => $this->contact->first_name,
                    'lead_source' => $this->contact->lead_source,
                    'phone' => $this->contact->phone,
                ]
            );
            event(new ChangesCommitted($this->contact, $result));

        } catch (Exception $exception)
        {
            Log::channel('single')->error($exception->getMessage());
            Log::channel('single')->error($exception->getTraceAsString());
        }

    }
}
