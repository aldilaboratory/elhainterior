<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Pakai $this->route('data-admin') kalau parameter route bernama {data-admin}
        // Karena kamu ingin pakai $id di controller, kita ambil dari input tersembunyi atau segment URL.
        // Cara aman: gunakan $this->route('id') jika route-mu menamai param {id}.
        $id = $this->route('data_admin') ?? $this->route('id') ?? $this->route('data-admin') ?? null;

        return [
            'name'     => ['required','string','max:255'],
            'email'    => [
                'required','email','max:255',
                Rule::unique('users','email')->ignore($id),
            ],
            'password' => ['nullable','string','min:8'],
            'role'     => ['required', Rule::in(['admin','kepala sekolah'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan.',
            'password.min'   => 'Password minimal 8 karakter.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower($this->input('email'))]);
        }
    }
}
