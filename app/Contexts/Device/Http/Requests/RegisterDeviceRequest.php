<?php

namespace App\Contexts\Device\Http\Requests;

use App\Contexts\Device\Domain\Enums\DeviceType;
use App\Contexts\Device\Domain\Enums\SensorType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según tu lógica de autorización
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(DeviceType::values())],
            'sensor_type' => [
                'nullable',
                Rule::requiredIf($this->type === 'sensor'),
                Rule::in(SensorType::values())
            ],
            'mac_address' => ['required', 'string', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', 'unique:devices,mac_address'],
            'ip_address' => ['nullable', 'ip'],
            'parent_id' => ['nullable', 'string', 'exists:devices,uuid'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del dispositivo es requerido',
            'type.required' => 'El tipo de dispositivo es requerido',
            'mac_address.required' => 'La dirección MAC es requerida',
            'mac_address.regex' => 'El formato de la dirección MAC no es válido',
            'mac_address.unique' => 'Ya existe un dispositivo con esta dirección MAC',
            'ip_address.ip' => 'La dirección IP no es válida',
            'parent_id.exists' => 'El dispositivo padre no existe',
            'sensor_type.required_if' => 'El tipo de sensor es requerido para dispositivos de tipo sensor',
        ];
    }
}
