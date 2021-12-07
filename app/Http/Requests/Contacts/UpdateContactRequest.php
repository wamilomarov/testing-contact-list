<?php

namespace App\Http\Requests\Contacts;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /** @var Contact? $contact */
        $contact = request()->route()->parameter('contact');
        $id = $contact->id;
        return [
            'first_name' => [
                'nullable',
                'sometimes',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                "unique:contacts,salesforce_id,$id"
            ],
            'phone' => [
                'nullable',
                'sometimes',
                'numeric',
            ],
        ];
    }
}
