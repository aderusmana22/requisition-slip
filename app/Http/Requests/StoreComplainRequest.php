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
            'objectives'       => 'nullable|string',

            'material_type' => 'required|array',
            'material_type.*' => 'required|string|in:Raw,Semi-Finished,Finished',

            'items'                 => 'required|array|min:1',
            'items.*'               => 'required|array',
            'items.*.details'       => 'required|array|min:1',
            'items.*.details.*'     => 'required|array',

            // Validasi untuk kuantitas
            'items.*.details.*.qty_required' => 'required|numeric|min:0',
            'items.*.details.*.qty_issued'   => 'required|numeric|min:0|lte:items.*.details.*.qty_required',

        ];
    }

    public function messages(): array
    {
        return [
            // Pesan untuk data utama
            'customer_id.required'      => 'Nama customer wajib diisi.',
            'customer_address.required' => 'Alamat customer wajib diisi.',
            'account.required'          => 'Akun wajib diisi.',
            'cost_center.required'      => 'Cost center wajib diisi.',
            'rs_number.required'        => 'Nomor RS/S wajib diisi.',
            'date.required'             => 'Tanggal wajib diisi.',
            'date.date'                 => 'Format tanggal tidak valid.',


            'material_type.required' => 'Pilih minimal satu tipe material.',
            'material_type.*.in' => 'Tipe material tidak valid. Pilihan yang tersedia: Raw, Semi-Finished, Finished.',

            // Pesan untuk validasi array 'items'
            'items.required'            => 'Anda harus memilih setidaknya satu produk.',
            'items.min'                 => 'Anda harus memilih setidaknya satu produk.',

            // Pesan untuk validasi 'details' di dalam 'items'
            // Tanda '*' akan secara otomatis digantikan oleh Laravel
            'items.*.details.required'  => 'Detail material untuk produk yang dipilih wajib ada.',
            'items.*.details.min'       => 'Setiap produk yang dipilih harus memiliki minimal satu detail material.',

            // Pesan untuk validasi kuantitas
            'items.*.details.*.qty_required'         => 'Kuantitas yang diminta harus diisi.',
            'items.*.details.*.qty_required.numeric' => 'Kuantitas yang diminta harus berupa angka.',
            'items.*.details.*.qty_required.min'     => 'Kuantitas yang diminta tidak boleh negatif.',
            'items.*.details.*.qty_issued'           => 'Kuantitas yang dikeluarkan harus diisi.',
            'items.*.details.*.qty_issued.numeric'   => 'Kuantitas yang dikeluarkan harus berupa angka.',
            'items.*.details.*.qty_issued.min'       => 'Kuantitas yang dikeluarkan tidak boleh negatif.',
            'items.*.details.*.qty_issued.lte'       => 'Kuantitas yang dikeluarkan tidak boleh lebih besar dari kuantitas yang diminta.',

        ];
    }
}
