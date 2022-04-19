<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(['administrator']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                // method POST, create new record
                Rule::requiredIf($this->isMethod('post')),
                'string',
                'min:5',
            ],
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ];

        if($this->isMethod('put')) {
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user->id),
            ];
        }

        return $rules;
    }
}
