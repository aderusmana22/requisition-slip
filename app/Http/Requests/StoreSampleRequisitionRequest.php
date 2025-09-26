<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSampleRequisitionRequest extends FormRequest
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
        // --- LOGIKA DINAMIS YANG BENAR ---
        $user = Auth::user();
        $userAccount = $user->department->code ?? null;
        $allowedSubCategories = [];

        // 1. Buat daftar sub-kategori yang diizinkan berdasarkan peran dan departemen
        if ($user->roles()->where('name', 'super-admin')->exists()) {
            $allowedSubCategories = ['Packaging', 'Finished Goods', 'Special Order'];
        } else {
            if (in_array($userAccount, ['5300', '5302'])) { // SnM, R&D, QA
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
            }
            if ($userAccount === '5300') { // Hanya SnM
                $allowedSubCategories[] = 'Special Order';
            }
        }
        $allowedSubCategories = array_unique($allowedSubCategories);
        // --- AKHIR LOGIKA DINAMIS ---

        // 2. Terapkan aturan validasi
        return [
            // Aturan Umum
            'sub_category'          => ['required', Rule::in($allowedSubCategories)],
            'customer_id'           => 'required|exists:customers,id',
            'account'               => 'required|string|max:255',
            'cost_center'           => 'nullable|string|max:255',
            'request_date'          => 'required|date',
            'objectives'            => 'required|string',
            'estimated_potential'   => 'required|string',
            
            // Aturan untuk Item
            'items'                         => 'required|array|min:1',
            'items.*.quantity_required'     => 'required|integer|min:1',
            'items.*.quantity_issued'       => 'nullable|integer|min:0', // Diubah menjadi nullable

            // Aturan Khusus untuk Special Order
            'requested_date'        => 'required_if:sub_category,Special Order|nullable|date',
            'weight_selection'      => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'packaging_selection'   => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'sample_count'          => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'shipment_method'       => 'required_if:sub_category,Special Order|nullable|string|max:255',
            'coa_required'          => 'required_if:sub_category,Special Order|nullable|boolean',

            // Aturan untuk field QA/QM (semua nullable karena diisi saat update)
            'sample_origin'             => 'nullable|string|max:255',
            'sample_description_batch'  => 'nullable|string|max:255',
            'sample_description_wb'     => 'nullable|string|max:255',
            'sample_description_tank'   => 'nullable|string|max:255',
            'production_date'           => 'nullable|date',
            'sample_preparation'        => 'nullable|string|max:255',
            'qa_notes'                  => 'nullable|string',
        ];
    }

    /**
     * Get the custom error messages for validator failures.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'sub_category.required' => 'The Sub Category field is required.',
            'sub_category.in' => 'The selected Sub Category is not valid for your department.',
            'customer_id.required' => 'The Customer field is required.',
            'account.required' => 'The Account field is required.',
            'request_date.required' => 'The Request Date field is required.',
            'objectives.required' => 'The Objectives field is required.',
            'estimated_potential.required' => 'The Estimated Potential field is required.',
            'items.required' => 'At least one item must be added.',
            'items.min' => 'At least one item must be added.',
            'items.*.quantity_required.required' => 'Qty Required must be filled for each item.',
            'items.*.quantity_required.min' => 'Qty Required must be at least 1.',
            
            // Pesan untuk Special Order
            'requested_date.required_if' => 'The Sample Completion Date is required for Special Orders.',
            'weight_selection.required_if' => 'The Sample Weight is required for Special Orders.',
            'packaging_selection.required_if' => 'The Sample Packaging is required for Special Orders.',
            'sample_count.required_if' => 'The Number of Samples is required for Special Orders.',
            'shipment_method.required_if' => 'The Shipment Method is required for Special Orders.',
            'coa_required.required_if' => 'The Certificate of Analysis option is required for Special Orders.',
        ];
    }
}