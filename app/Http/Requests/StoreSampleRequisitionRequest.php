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
            'sub_category'        => 'required|string|in:Packaging,Finished Good,Special Order',
            'customer_id'         => 'required|exists:customers,id',
            'no_srs'              => 'required|string|max:255|unique:requisitions,no_srs',
            'account'             => 'required|string|max:255',
            'cost_center'         => 'nullable|string|max:255',
            'request_date'        => 'required|date',
            'items'                     => 'required|array|min:1',
            'items.*.item_master_id'    => 'required|exists:item_masters,id',
            'items.*.quantity_required'   => 'required|integer|min:1',
            'items.*.quantity_issued'     => 'required|integer|min:0',
            'objectives'          => 'required|string',
            'estimated_potential' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer wajib dipilih.',
            'items.required' => 'Minimal harus ada 1 item yang ditambahkan.',
            'items.*.item_master_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity_required.required' => 'Qty Required wajib diisi.',
            'objectives.required' => 'Objectives wajib diisi.',
            'estimated_potential.required' => 'Estimasi Potensi wajib diisi.',
        ];
    }
}
