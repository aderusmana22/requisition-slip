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
    public function rules(): array
    {
        // Pengecekan apakah ini submit dari form QM, ditandai dengan adanya field 'source'.
        $isQmSubmission = $this->has('source');

        // Aturan validasi dasar yang berlaku untuk semua
        $rules = [
            'sub_category'          => 'required|string',
            'customer_id'           => 'required|exists:customers,id',
            'account'               => 'required|string|max:255',
            'cost_center'           => 'nullable|string|max:255',
            'request_date'          => 'required|date',
            'objectives'            => 'required|string',
            'estimated_potential'   => 'required|string',


            // Aturan untuk field QA/QM (selalu ada, tapi nullable)
            'source'                => 'required|string|max:255',
            'description'           => 'required|string|max:255',
            'production_date'       => 'required|date',
            'preparation_method'    => 'required|string|max:255',
            'sample_notes'          => 'required|string',
        ];

        if ($isQmSubmission) {
            $rules['items'] = 'nullable|array';

        } else {
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.quantity_required'] = 'required|integer|min:1';
            $rules['items.*.quantity_issued'] = 'required|integer|min:0';
            $rules['print_batch']           = 'required_if:sub_category,Packaging|boolean';

            $rules['end_date']              = 'required_if:sub_category,Special Order|date';
            $rules['weight_selection']      = 'required_if:sub_category,Special Order|string|max:255';
            $rules['packaging_selection']   = 'required_if:sub_category,Special Order|string|max:255';
            $rules['sample_count']          = 'required_if:sub_category,Special Order|string|max:255';
            $rules['purpose']               = 'required_if:sub_category,Special Order|string';
            $rules['shipment_method']       = 'required_if:sub_category,Special Order|string|max:255';
            $rules['coa_required']          = 'required_if:sub_category,Special Order|boolean';
        }

        return $rules;
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
