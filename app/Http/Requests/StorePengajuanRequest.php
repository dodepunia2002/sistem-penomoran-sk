<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPetugas();
    }

    public function rules(): array
    {
        return [
            'nama'    => ['required', 'string', 'max:255'],
            'alamat'  => ['required', 'string', 'max:500'],
            'tanggal' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'    => 'Nama wajib diisi.',
            'nama.max'         => 'Nama maksimal 255 karakter.',
            'alamat.required'  => 'Alamat wajib diisi.',
            'alamat.max'       => 'Alamat maksimal 500 karakter.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date'     => 'Format tanggal tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama'    => 'nama',
            'alamat'  => 'alamat',
            'tanggal' => 'tanggal',
        ];
    }
}
