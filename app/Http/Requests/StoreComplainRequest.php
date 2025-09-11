<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplainRequest extends FormRequest
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
            // Validasi untuk data utama
            'customer_id'    => 'required|string|max:255',
            'customer_address' => 'required|string',
            'account'          => 'required|string|max:100',
            'cost_center'      => 'required|string|max:100',
            'rs_number'        => 'required|string|max:100',
            'date'             => 'required|date',

            // Validasi untuk tabel produk (array)
            // Pastikan setidaknya ada satu produk yang diinput
            'codes'            => 'required|array|min:1',
            'names'            => 'required|array|min:1',
            'units'            => 'required|array|min:1',
            'quantities'       => 'required|array|min:1',
            'objectives'       => 'required|array|min:1',

            // Tanda '*' berarti aturan ini berlaku untuk setiap item di dalam array
            'codes.*'          => 'required|string|max:50',
            'names.*'          => 'required|string|max:255',
            'units.*'          => 'required|string|max:50',
            'quantities.*'     => 'required|integer|min:1',
            'objectives.*'     => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama customer wajib diisi.',
            'customer_address.required' => 'Alamat customer wajib diisi.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',

            // Pesan untuk array (jika array kosong)
            'codes.required' => 'Setidaknya satu produk harus ditambahkan.',
            'names.required' => 'Setidaknya satu produk harus ditambahkan.',
            'units.required' => 'Setidaknya satu produk harus ditambahkan.',
            'quantities.required' => 'Setidaknya satu produk harus ditambahkan.',

            // Pesan untuk setiap item di dalam array
            'codes.*.required' => 'Kode produk pada baris produk code wajib diisi.',
            'names.*.required' => 'Nama produk pada baris produk name wajib diisi.',
            'units.*.required' => 'Unit pada baris unit wajib diisi.',
            'quantities.*.required' => 'Kuantitas pada baris quantity required wajib diisi.',
            'quantities.*.integer' => 'Kuantitas pada baris quantity required harus berupa angka.',
            'quantities.*.min' => 'Kuantitas pada baris quantity required minimal harus 1.',
        ];
    }
}
