<?php

namespace App\DTO;

/**
 * @property string $id
 * @property string $account_id
 * @property string $description
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property bool $is_deleted
 * @property string $lead_source
 * @property string $title
 * @property string $phone
 */
class SalesforceContact
{
    const FIELDS = [
        'id',
        'account_id',
        'description',
        'email',
        'first_name',
        'last_name',
        'is_deleted',
        'lead_source',
        'title',
        'phone',
    ];
    public function __construct(Array $properties = []){
        foreach(self::FIELDS as $key){
            $this->{$key} = $properties[$key] ?? null;
        }
    }
}
