<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'user_type' => 'required|string|in:law,economy',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'given_name' => 'required|string|max:50',
            'family_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'preferred_name' => 'nullable|string|max:50',
            'affilation' => 'required|string|max:100',
            'country' => 'required|string|max:50',
            'img_url' => 'nullable|string|max:50',
            'homepage_url' => 'nullable|string|max:50',
            'orchid_id' => 'nullable|string|max:19',
            'mailing_address' => 'nullable|string',
            'bio_statement' => 'nullable|string',
        ];
    }
}
