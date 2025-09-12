<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
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
        return [
            'category_id'    => ['required','exists:categories,id'],
            'subcategory_id' => ['nullable','exists:subcategories,id'],
            'name'           => ['required','string','max:200','unique:products,name'],
            'description'    => ['nullable','string'],
            'price'          => ['required','integer','min:0'],
            'stock'          => ['required','integer','min:0'],

            // multi images
            'images'         => ['nullable','array','max:10'],
            'images.*'       => ['image','mimes:jpg,jpeg,png,webp','max:20480'],

            // opsional: index gambar utama dari array images
            'primary_index'  => ['nullable','integer','min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format yang diizinkan: jpg, jpeg, png, webp.',
            'image.max'   => 'Ukuran maksimum 2MB.',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}
