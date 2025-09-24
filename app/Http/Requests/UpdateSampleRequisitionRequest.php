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
            'items.*.quantity_issued'   => 'required|integer|min:0',
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
            'sub_category.required' => 'Sub Kategori wajib dipilih.',
            'customer_id.required' => 'Customer wajib dipilih.',
            'no_srs.required' => 'Nomor SRS wajib diisi.',
            'no_srs.unique' => 'Nomor SRS sudah terdaftar.',
            'account.required' => 'Akun wajib diisi.',
            'request_date.required' => 'Tanggal Permintaan wajib diisi.',
            'objectives.required' => 'Tujuan wajib diisi.',
            'estimated_potential.required' => 'Estimasi Potensi wajib diisi.',
            'items.required' => 'Minimal harus ada 1 item yang diminta.',
            'items.min' => 'Minimal harus ada 1 item yang diminta.',
            'items.*.quantity_required.required' => 'Qty Required wajib diisi untuk setiap item.',
            'items.*.quantity_required.min' => 'Qty Required minimal 1.',
            'request_date.required_if' => 'Tanggal Penyelesaian Sampel wajib diisi untuk Special Order.',
            'weight_selection.required_if' => 'Berat Sampel wajib diisi untuk Special Order.',
            'packaging_selection.required_if' => 'Kemasan Sampel wajib diisi untuk Special Order.',
            'sample_count.required_if' => 'Rincian Jumlah Sampel wajib diisi untuk Special Order.',
            'shipment_method.required_if' => 'Metode Pengiriman wajib dipilih untuk Special Order.',
            'coa_required.required_if' => 'COA Required wajib diisi untuk Special Order.',
            'sample_origin.max' => 'Asal Sampel maksimal 255 karakter.',
            'sample_description_batch.max' => 'Deskripsi Sampel (Batch) maksimal 255 karakter.',
            'sample_description_wb.max' => 'Deskripsi Sampel (WB) maksimal 255 karakter.',
            'sample_description_tank.max' => 'Deskripsi Sampel (Tank) maksimal 255 karakter.',
            'production_date.date' => 'Tanggal Produksi tidak valid.',
            'sample_preparation.max' => 'Persiapan Sampel maksimal 255 karakter.',
            'qa_notes.string' => 'Catatan QA harus berupa teks.',
        ];
    }
}
