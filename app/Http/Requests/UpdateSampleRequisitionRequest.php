<?php

namespace App\Http\Requests;

use App\Models\Master\ItemDetail;
use App\Models\Master\ItemMaster;
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
    public function rules()
    {
        $isQmSubmission = $this->has('source');

        if ($isQmSubmission) {
            return [
                'source'             => 'nullable|string|max:255',
                'description'        => 'nullable|string|max:255',
                'production_date'    => 'nullable|date',
                'preparation_method' => 'nullable|string|max:255',
                'sample_notes'       => 'nullable|string|max:255',
            ];
        } else {
            $rules = [
                'customer_id'         => 'required|exists:customers,id',
                'request_date'        => 'required|date',
                'sub_category'        => 'required|string',
                'cost_center'         => 'nullable|string',
                'objectives'          => 'required|string',
                'estimated_potential' => 'required|string',
                'print_batch'         => 'nullable|boolean',
                'items'               => 'required|array|min:1',
                'items.*.quantity_required' => 'required|integer|min:1',
            ];

            // Tambahkan validasi khusus jika sub-category adalah 'Special Order'
            if ($this->input('sub_category') === 'Special Order') {
                $rules = array_merge($rules, [
                    'end_date'            => 'required|date|after_or_equal:request_date',
                    'weight_selection'    => 'required|string',
                    'packaging_selection' => 'required|string',
                    'sample_count'        => 'required|string',
                    'purpose'             => 'required|string',
                    'coa_required'        => 'required|boolean',
                    'shipment_method'     => 'required|string',
                ]);
            }

            return $rules;
        }
    }

    public function attributes(): array
    {
        $attributes = [];
        $items = $this->input('items', []);
        $subCategory = $this->input('sub_category');

        foreach ($items as $id => $itemData) {
            $itemName = "Item with ID {$id}"; // Nama default jika item tidak ditemukan

            if ($subCategory === 'Packaging') {
                // Cari di ItemDetail jika sub-kategori adalah Packaging
                $itemDetail = ItemDetail::find($id);
                if ($itemDetail) {
                    $itemName = "[{$itemDetail->item_detail_code}] {$itemDetail->item_detail_name}";
                }
            } else {
                // Cari di ItemMaster untuk Finished Goods & Special Order
                $itemMaster = ItemMaster::find($id);
                if ($itemMaster) {
                    $itemName = "[{$itemMaster->item_master_code}] {$itemMaster->item_master_name}";
                }
            }

            // Definisikan "nama panggilan" untuk setiap atribut item
            $attributes["items.{$id}.quantity_required"] = "Qty Required untuk item {$itemName}";
            $attributes["items.{$id}.quantity_issued"] = "Qty Issued untuk item {$itemName}";
        }

        return $attributes;
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
