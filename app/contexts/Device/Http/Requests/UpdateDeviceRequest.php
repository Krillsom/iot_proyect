<?php

namespace App\Contexts\Device\Http\Requests;

use App\Contexts\Device\Domain\Enums\DeviceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'ip_address' => ['sometimes', 'nullable', 'ip'],
            'status' => ['sometimes', Rule::in(DeviceStatus::values())],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto',
            'ip_address.ip' => 'La dirección IP no es válida',
            'status.in' => 'El estado del dispositivo no es válido',
        ];
    }
}
