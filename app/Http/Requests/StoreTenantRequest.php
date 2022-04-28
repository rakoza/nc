<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'domain' => 'required|string',
            'notes' => 'nullable|string',
            'is_active' => 'required|boolean',
            'trial_period_end_date' => 'nullable|date',
            'src' => 'required|string',
            'db_host' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
            'redis_host' => 'required|string',
            'timezone' => 'required|string',
            // 'user_uid' => 'nullable|integer|min:1000',
            // 'user_gid' => 'nullable|integer|min:1000',
        ];
    }
}
