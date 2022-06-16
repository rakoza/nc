<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $tenantId = optional($this->tenant)->id;

        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'domain' => [
                'required',
                'string',
                Rule::unique('tenants')->ignore($tenantId),
            ],
            'notes' => 'nullable|string',
            'is_active' => 'required|boolean',
            'trial_period_end_date' => 'nullable|date',
            'src' => 'required|string',
            'db_host' => 'required|string',
            'db_username' => [
                'required',
                'string',
                Rule::unique('tenants')->where(function ($query) {
                    return $query
                        ->where('db_host', $this->db_host);
                        // ->where('db_username', $this->db_username);
                })
                ->ignore($tenantId),
            ],
            'db_password' => 'required|string',
            'redis_host' => 'required|string',
            'word_to_pdf_worker' => 'required|string',
            'timezone' => 'required|string',
            // 'user_uid' => 'nullable|integer|min:1000',
            // 'user_gid' => 'nullable|integer|min:1000',
        ];
    }
}
