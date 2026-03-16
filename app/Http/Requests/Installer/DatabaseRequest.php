<?php

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

class DatabaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:100'],
            'app_url'  => ['required', 'url'],
            'timezone' => ['required', 'timezone'],
            'db_host'  => ['required', 'string', 'max:255'],
            'db_port'  => ['required', 'integer', 'min:1', 'max:65535'],
            'db_name'  => ['required', 'string', 'max:255'],
            'db_user'  => ['required', 'string', 'max:255'],
            'db_pass'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'app_name.required' => 'Application name is required.',
            'app_url.required'  => 'Application URL is required.',
            'app_url.url'       => 'Application URL must be a valid URL (include http/https).',
            'timezone.required' => 'Timezone is required.',
            'timezone.timezone' => 'Please select a valid timezone.',
            'db_host.required'  => 'Database host is required.',
            'db_name.required'  => 'Database name is required.',
            'db_user.required'  => 'Database username is required.',
        ];
    }
}
