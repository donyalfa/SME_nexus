<?php

namespace App\Modules\UMKM\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessProfileRequest extends FormRequest
{
    /**
     * Otorisasi: profil usaha milik user yang sedang login.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Aturan validasi form onboarding profil usaha.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id_category' => ['required', 'integer', Rule::exists('business_categories', 'id_category')],
            'business_name' => ['required', 'string', 'max:100'],
            'legal_type' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'business_email' => ['nullable', 'email', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'nib' => ['nullable', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'employee_count' => ['nullable', 'integer', 'min:0'],
            'established_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute terlalu kecil.',
            'max' => ':attribute terlalu panjang.',
            'email' => 'Format email tidak valid.',
            'id_category.exists' => 'Kategori usaha tidak valid.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, dan karakter + - ( ).',
            'established_year.min' => 'Tahun berdiri minimal 1900.',
            'established_year.max' => 'Tahun berdiri tidak boleh melebihi tahun ini.',
            'employee_count.min' => 'Jumlah karyawan tidak boleh negatif.',
        ];
    }

    /**
     * Nama tampilan tiap kolom untuk pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id_category' => 'Kategori usaha',
            'business_name' => 'Nama usaha',
            'legal_type' => 'Badan hukum',
            'city' => 'Kota',
            'province' => 'Provinsi',
            'postal_code' => 'Kode pos',
            'address' => 'Alamat',
            'phone' => 'Nomor telepon/WA',
            'business_email' => 'Email usaha',
            'website' => 'Website/media sosial',
            'nib' => 'NIB',
            'npwp' => 'NPWP',
            'employee_count' => 'Jumlah karyawan',
            'established_year' => 'Tahun berdiri',
            'description' => 'Deskripsi usaha',
        ];
    }
}
