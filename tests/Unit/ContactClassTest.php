<?php

namespace Tests\Unit;

use App\Models\Contact;
use Database\Factories\ContactFactory;
use Database\Factories\FactoryConstants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactClassTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIsNotSyncAnymore()
    {
        $factory = new ContactFactory();
        $factory->setSync(true)->createOne();

        /** @var Contact $contact */
        $contact = Contact::query()->firstWhere('email', FactoryConstants::CONTACT_EMAIL);

        $contact->isNotSyncAnymore();
        $contact->refresh();

        $this->assertFalse($contact->is_synced);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIsSynced()
    {
        $factory = new ContactFactory();
        $factory->setSync(false)->create();

        /** @var Contact $contact */
        $contact = Contact::query()->firstWhere('email', FactoryConstants::CONTACT_EMAIL);

        $contact->isSynced();
        $contact->refresh();

        $this->assertTrue($contact->is_synced);
    }
}
