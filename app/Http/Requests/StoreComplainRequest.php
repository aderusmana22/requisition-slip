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

            // Validasi untuk array produk
            'requisition_items'   => 'required|array',
            'requisition_items.*' => 'required|integer|exists:item_masters,id',

            // Validasi untuk QTY Required
            'qty_required'   => 'required|array',
            'qty_required.*' => 'required|integer|min:0',

            // Validasi untuk QTY Issued
            'qty_issued'   => 'required|array',
            'qty_issued.*' => 'required|integer|min:0|lte:qty_required.*',

            'material_type' => 'required|array',
            'material_type.*' => 'required|string|in:Raw,Semi-Finished,Finished',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Nama customer wajib diisi.',
            'customer_address.required' => 'Alamat customer wajib diisi.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'cost_center.required' => 'Cost center wajib diisi.',
            'cost_center.max' => 'Cost center maksimal 100 karakter.',

            'material_type.required' => 'Anda harus memilih minimal satu tipe material.',
            'material_type.*.in' => 'Tipe material tidak valid. Pilihan yang tersedia: Raw, Semi-Finished, Finished.',

            'requisition_items.required' => 'Anda harus memilih minimal satu produk.',
            'qty_required.required' => 'Harus ada quantity Required.',
            'qty_issued.required' => 'Harus ada quantity Issued.',
            'qty_issued.*.lte' => 'quantity tidak boleh melebihi jumlah Required.',
            'qty_required.*.required' => 'quantity Required wajib diisi.',
            'qty_issued.*.required' => 'quantity Issued wajib diisi.',
            '*.integer' => 'Input harus berupa angka.',
            '*.min' => 'Input tidak boleh bernilai negatif.',
        ];
    }
}
