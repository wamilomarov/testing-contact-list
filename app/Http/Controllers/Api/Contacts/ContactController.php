<?php

namespace App\Http\Controllers\Api\Contacts;

use App\Events\SyncRequested;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\CreateContactRequest;
use App\Http\Requests\Contacts\UpdateContactRequest;
use App\Http\Resources\Contacts\ContactResource;
use App\Http\Services\SalesforceService;
use App\Jobs\Contacts\CreateContactJob;
use App\Jobs\Contacts\DeleteContactJob;
use App\Jobs\Contacts\UpdateContactJob;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    protected SalesforceService $salesforceService;

    public function __construct(SalesforceService $salesforceService)
    {
        $this->salesforceService = $salesforceService;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ContactResource::collection(Contact::query()->paginate());
    }

    /**
     * @param CreateContactRequest $request
     * @return ContactResource
     */
    public function store(CreateContactRequest $request): ContactResource
    {
        /** @var Contact $contact */
        $contact = Contact::query()->create($request->validated());
        $this->dispatch(new CreateContactJob($contact));
        return ContactResource::make($contact);
    }

    /**
     * @param Contact $contact
     * @return ContactResource
     */
    public function show(Contact $contact): ContactResource
    {
        return ContactResource::make($contact);
    }

    /**
     * @param Contact $contact
     * @param UpdateContactRequest $request
     * @return ContactResource
     */
    public function update(Contact $contact, UpdateContactRequest $request): ContactResource
    {
        $contact->update($request->validated());
        $contact->isNotSyncAnymore();
        $this->dispatch(new UpdateContactJob($contact));
        return ContactResource::make($contact->refresh());
    }

    /**
     * @param Contact $contact
     * @return JsonResponse
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        $contact->isNotSyncAnymore();
        $this->dispatch(new DeleteContactJob($contact));
        return response()->json([]);
    }

    /**
     * @return JsonResponse
     */
    public function sync(): JsonResponse
    {
        event(new SyncRequested());
        return response()->json([]);
    }
}
