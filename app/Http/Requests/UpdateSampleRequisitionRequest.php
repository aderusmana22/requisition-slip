<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSampleRequisitionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Mendapatkan ID requisition dari route parameter
        $requisitionId = $this->route('sample_form');

        $rules = [
            'sub_category'        => 'required|string',
            'customer_id'         => 'required|exists:customers,id',
            'no_srs' => 'required|string|max:255|unique:requisitions,no_srs,' . $requisitionId,
            'account'             => 'required|string|max:255',
            'cost_center'         => 'nullable|string|max:255',
            'request_date'        => 'required|date',
            'objectives'          => 'required|string',
            'estimated_potential' => 'required|string',
            'items'               => 'required|array|min:1',
            'items.*.quantity_required' => 'required|integer|min:1',
            'items.*.quantity_issued'   => 'nullable|integer|min:0',
        ];

        // BARU tambahkan aturan kondisional ke array $rules
        if ($this->input('sub_category') === 'Special Order') {
            $rules['requested_date']      = 'nullable|date';
            $rules['weight_selection']    = 'nullable|string';
            $rules['packaging_selection'] = 'nullable|string';
            $rules['sample_count']        = 'nullable|string';
            $rules['coa_required']        = 'nullable|boolean';
            $rules['shipment_method']     = 'nullable|string';
        }

        // Terakhir, kembalikan array $rules yang sudah lengkap
        return $rules;
    }

     /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Pilih produk terlebih dahulu untuk menampilkan komponen.',
            'customer_id.required' => 'Customer wajib diisi.',
            'customer_id.exists' => 'Customer yang dipilih tidak valid.',
            'account.required' => 'Account wajib diisi.',
            'no_srs.required' => 'Nomor SRS wajib diisi.',
            'no_srs.unique' => 'Nomor SRS ini sudah pernah digunakan.',
            'items.*.component_item_master_id.required' => 'Komponen wajib dipilih.',
            'items.*.quantity_required.required' => 'Qty Required untuk setiap komponen wajib diisi.',
            'objectives.required' => 'Objectives wajib diisi.',
            'estimated_potential.required' => 'Estimasi Potensi wajib diisi.',
        ];
    }
}
