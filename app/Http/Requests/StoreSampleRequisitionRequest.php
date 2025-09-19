<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSampleRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sub_category'        => 'required|string|in:Packaging,"Finished Good","Special Order"',
            'customer_id'         => 'required|exists:customers,id',
            'no_srs'              => 'required|string|max:255|unique:requisitions,no_srs,' . $this->id,
            'account'             => 'required|string|max:255',
            'cost_center'         => 'nullable|string|max:255',
            'request_date'        => 'required|date',
            'objectives'          => 'required|string',
            'estimated_potential' => 'required|string',
            'items'               => 'required|array|min:1',
            'items.*.quantity_required' => 'required|integer|min:1',
            'items.*.quantity_issued'   => 'nullable|integer|min:0',
            'requested_date' => 'required_if:sub_category,Special Order|nullable|date',
            'weight_selection' => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'packaging_selection' => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'sample_count' => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'shipment_method' => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'coa_required' => 'required_if:sub_category,Special Order|nullable|boolean',
            'sample_origin' => 'nullable|string|max:255',
            'sample_description_batch' => 'nullable|string|max:255',
            'sample_description_wb' => 'nullable|string|max:255',
            'sample_description_tank' => 'nullable|string|max:255',
            'production_date' => 'nullable|date',
            'sample_preparation' => 'nullable|string|max:255',
            'qa_notes' => 'nullable|string',
        ];
    }

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
