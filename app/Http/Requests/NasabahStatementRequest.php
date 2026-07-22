<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NasabahStatementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('nasabah')->check();
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ];
    }
}
