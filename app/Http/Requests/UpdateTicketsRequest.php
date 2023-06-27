<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'uuid', 'exists:clients,id'],
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'status' => ['required', 'string', 'max:20', Rule::in(["Open", "In progress", "Closed"])],
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'client_id.required' => 'Client is required!',
            'user_id.required' => 'User is required!',
            'status.required' => 'Status is required!',
            'title.required' => 'Title is required!',
            'description.required' => 'Description is required!',
        ];
    }
}
