<?php

namespace App\Http\Services;

use App\DTO\SalesforceContact;
use App\Models\Contact;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SalesforceService
{
    protected string $baseUrl;
    private string $email;
    private string $password;

    public function __construct()
    {
        $this->baseUrl = config("app.salesforce_url");
        $this->email = config("app.email");
        $this->password = config("app.password");
    }

    /**
     * @return string
     * @throws ValidationException
     */
    public function getToken(): string
    {
        $url = $this->baseUrl . '/login/';

        $response = Http::asMultipart()
            ->post($url, [
                'email' => $this->email,
                'password' => $this->password
            ]);

        if ($response->failed()) {
            throw ValidationException::withMessages(['login' => trans("validation.failed_request")]);
        }

        return $response->json('token');
    }


    /**
     * @return array
     * @throws Exception
     */
    public function list(): array
    {
        $url = $this->baseUrl . '/contacts/';

        $response = Http::withHeaders([
            'authorization' => $this->getToken()
        ])->get($url);

        if ($response->failed()) {
            throw new Exception($response->body());
        }

        return $response->json('records', []);
    }

    /**
     * @param string $id
     * @return SalesforceContact
     * @throws ValidationException
     */
    public function getById(string $id): SalesforceContact
    {
        $url = $this->baseUrl . '/contacts/' . $id . '/';

        $response = Http::asMultipart()
            ->withHeaders([
                'authorization' => $this->getToken()
            ])->post($url);

        if ($response->failed()) {
            throw ValidationException::withMessages(['list' => trans("validation.failed_request")]);
        }

        $data = $response->json();

        return new SalesforceContact($data);
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function create(array $params): string
    {
        $url = $this->baseUrl . '/contacts/';

        $response = Http::asMultipart()
            ->withHeaders([
                'authorization' => $this->getToken()
            ])
            ->post($url, $params);

        if ($response->failed()) {
            throw new Exception($response->body());
        }

        return $response->json('id');
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     * @throws ValidationException
     */
    public function update(string $id, array $params): ?string
    {
        $url = $this->baseUrl . '/contacts/' . $id . '/';

        $response = Http::asMultipart()
            ->withHeaders([
                'authorization' => $this->getToken()
            ])
            ->patch($url, $params);

        if ($response->failed()) {
            throw ValidationException::withMessages(['list' => trans("validation.failed_request")]);
        }

        if ($response->json('success'))
        {
            return $response->json('id');
        }
        return null;
    }

    /**
     * @param string $id
     * @return array|mixed
     * @throws ValidationException
     */
    public function delete(string $id)
    {
        $url = $this->baseUrl . '/contacts/' . $id . '/';

        $response = Http::asMultipart()
            ->withHeaders([
                'authorization' => $this->getToken()
            ])->delete($url);

        if ($response->failed()) {
            throw ValidationException::withMessages(['list' => trans("validation.failed_request")]);
        }

        return $response->json();
    }

    public function sync(): void
    {
        try {
            $contacts = $this->list();
            foreach ($contacts as $item) {
                $salesforceContact = new SalesforceContact($item);

                if (is_null($salesforceContact->email) || is_null($salesforceContact->last_name))
                {
                    continue;
                }

                /** @var Contact $contact */
                $contact = Contact::query()
                    ->withTrashed()
                    ->updateOrCreate(
                        [
                            'email' => $salesforceContact->email,
                        ],
                        [
                            'salesforce_id' => $salesforceContact->id,
                            'first_name' => $salesforceContact->first_name,
                            'last_name' => $salesforceContact->last_name,
                            'phone' => $salesforceContact->phone,
                            'lead_source' => $salesforceContact->lead_source,
                            'is_synced' => true
                        ]);

                if ($salesforceContact->is_deleted) {
                    $contact->delete();
                } else {
                    $contact->restore();
                }

            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
