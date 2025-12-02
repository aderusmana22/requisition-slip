<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} : {{ $requisition->no_srs }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
        }

        .card-header.main-header {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0 !important;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #b8871a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eef2f9;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .info-label {
            color: #8a96a3;
            font-size: .85em;
            margin-bottom: 2px;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }

        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, .9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }

        .text-primary {
            color: #b8871a !important;
        }

        .btn-primary {
            background-color: #cc982f;
            border-color: #cc982f;
        }

        .btn-primary:hover {
            background-color: #b8871a;
            border-color: #b8871a;
        }

        .processing-overlay .spinner-border {
            color: #cc982f !important;
        }

        .main-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .action-card {
            position: sticky;
            top: 40px;
        }

        .left-column>.card {
            margin-bottom: 30px;
        }

        .radio-group-horizontal .form-check {
            margin-right: 15px;
        }

        /* Style tambahan untuk QA Form Section di kiri */
        .qa-input-section {
            background-color: #fff8e6; /* Warna latar sedikit beda biar fokus */
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e0cda5;
        }

        @media (max-width: 992px) {
            .main-container {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 0 15px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .action-card {
                position: static;
                top: auto;
            }

            .card-body.p-md-5 {
                padding: 1.5rem !important;
            }

            .main-header h4 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    {{-- [PERBAIKAN] Form Membungkus seluruh Main Container agar input di Kiri dan Tombol di Kanan terhubung --}}
    <form id="responseForm" action="{{ route('approval-sample.process-form') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="main-container">
            <div class="left-column">
                <div class="card">
                    <div class="card-header main-header">
                        <h4 class="mb-0">Sample Requisition Approval</h4>
                        <p class="mb-0 opacity-75">SRS No: {{ $requisition->no_srs }}</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        {{-- BAGIAN 1: DETAIL REQUISITION --}}
                        <h5 class="section-title"><i class="fas fa-file-invoice"></i> Requisition Details</h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="info-label">Request Date</div>
                                <div class="info-value">
                                    {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Requester</div>
                                <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Department</div>
                                <div class="info-value">{{ $requisition->requester->department->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Customer Name</div>
                                <div class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-8">
                                <div class="info-label">Address</div>
                                <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Cost Center</div>
                                <div class="info-value">{{ $requisition->cost_center ?? '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Objectives</div>
                                <div class="info-value">{{ $requisition->objectives }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Estimated Potential</div>
                                <div class="info-value">{{ $requisition->estimated_potential }}</div>
                            </div>
                        </div>

                        {{-- BAGIAN 2: DETAIL ITEM --}}
                        @if($requisition->requisitionItems->count() > 0)
                        <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Requested Item List</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        @if($requisition->sub_category == 'Packaging')
                                        <th style="width: 100px;" class="material-type-column">Material Type</th>
                                        @endif
                                        <th style="width: 100px;">Item Code</th>
                                        <th style="width: 150px;">Item Name</th>
                                        <th style="width: 90px;">Unit</th>
                                        <th style="width: 125px;" class="text-center">Qty Required</th>

                                        {{-- [PERBAIKAN] Header Tabel dinamis: QA Form juga boleh edit --}}
                                        <th class="text-center" style="width: 125px;">
                                            @if($action === 'update_qty' || $isQaForm)
                                                Qty Issued <span class="text-danger">*</span>
                                            @else
                                                Qty Issued <span class="text-danger">*</span>
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisition->requisitionItems as $item)
                                    <tr>
                                        @if($requisition->sub_category == 'Packaging')
                                        <td>{{ $item->material_type ?? '-' }}</td>
                                        <td>{{ $item->itemDetail->item_detail_code ?? '-' }}</td>
                                        <td>{{ $item->itemDetail->item_detail_name ?? '-' }}</td>
                                        <td>{{ $item->itemDetail->unit ?? '-' }}</td>
                                        @else
                                        <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                        <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                        <td>{{ $item->itemMaster->unit ?? '-' }}</td>
                                        @endif
                                        <td class="text-center">{{ $item->quantity_required }}</td>
                                        <td class="text-center">
                                            {{-- [PERBAIKAN] Input Enabled untuk Update Qty ATAU QA Form --}}
                                            @if($action === 'update_qty' || $isQaForm)
                                                <input type="number"
                                                    class="form-control form-control-sm text-center fw-bold"
                                                    style="min-width: 80px;"
                                                    name="items[{{ $item->id }}]"
                                                    value="{{ $item->quantity_issued > 0 ? $item->quantity_issued : '' }}"
                                                    placeholder="0"
                                                    min="0"
                                                    {{-- Note: required dimatikan agar JS validasi manual bisa handle atau 0 dianggap valid --}}
                                                >
                                            @else
                                                {{ $item->quantity_issued ?? '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- BAGIAN 3 (KONDISIONAL): DATA MARKETING --}}
                        @if($isQaForm)
                            <h5 class="section-title mt-5"><i class="fas fa-tags"></i> Marketing Data (Read-Only)</h5>
                            <div class="table-responsive mb-5">
                                <table class="table table-bordered align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 180px;">Tanggal Selesai Sample</th>
                                            <td>
                                                <input type="date" class="form-control"
                                                    value="{{ $requisition->requisitionSpecial->end_date ?? '' }}" readonly>
                                            </td>
                                            <th style="width: 180px;">Kemasan Sample</th>
                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $requisition->requisitionSpecial->packaging_selection ?? '' }}"
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Sample</th>
                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $requisition->requisitionSpecial->sample_count ?? '' }}" readonly>
                                            </td>
                                            <th>Tujuan Sample</th>
                                            <td>
                                                <textarea class="form-control" rows="2"
                                                    readonly>{{ $requisition->requisitionSpecial->purpose ?? '' }}</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Certificate of Analysis</th>
                                            <td>
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            @if($requisition->requisitionSpecial->coa_required == 1) checked
                                                        @endif disabled>
                                                        <label class="form-check-label">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            @if($requisition->requisitionSpecial->coa_required == 0) checked
                                                        @endif disabled>
                                                        <label class="form-check-label">No</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>Shipment Method</th>
                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $requisition->requisitionSpecial->shipment_method ?? '' }}"
                                                    readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- [PERBAIKAN LAYOUT] Form QA Super Compact --}}
                            <div class="qa-input-section mt-3 p-3" style="background-color: #fff8e6; border: 1px solid #e0cda5; border-radius: 8px;">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-pencil-alt me-2"></i>Complete QA Form</h6>

                                <input type="hidden" name="action" value="qa_submit">

                                {{-- 1. ASAL SAMPLE --}}
                                <div class="row mb-2">
                                    <label class="col-md-3 col-form-label col-form-label-sm fw-bold d-flex justify-content-between">
                                        <span>Asal sample <span class="text-danger">*</span></span>
                                        <span>:</span>
                                    </label>
                                    <div class="col-md-9">
                                        {{-- Radio Buttons Baris --}}
                                        <div class="d-flex flex-wrap gap-3 align-items-center pt-1">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="source_option" value="WH" id="source_wh">
                                                <label class="form-check-label small" for="source_wh">WH</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="source_option" value="Reference Sample" id="source_ref">
                                                <label class="form-check-label small" for="source_ref">Reference Sample</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="source_option" value="Batch Refinery" id="source_batch">
                                                <label class="form-check-label small" for="source_batch">Batch Refinery</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="source_option" value="Packing Room" id="source_packing">
                                                <label class="form-check-label small" for="source_packing">Packing Room</label>
                                            </div>
                                            {{-- Opsi Lainnya sejajar di sini --}}
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" id="source_other_radio" name="source_option" value="Lainnya">
                                                <label class="form-check-label small" for="source_other_radio">Lainnya...</label>
                                            </div>
                                        </div>
                                        {{-- Input Text muncul di bawahnya --}}
                                        <input type="text" class="form-control form-control-sm mt-1" id="source_other_input" style="display: none; max-width: 100%;" placeholder="Sebutkan asal sample...">
                                        <input type="hidden" name="source" id="source" required>
                                    </div>
                                </div>

                                {{-- 2. KETERANGAN SAMPLE --}}
                                <div class="row mb-2">
                                    <label class="col-md-3 col-form-label col-form-label-sm fw-bold d-flex justify-content-between">
                                        <span>Keterangan sample <span class="text-danger">*</span></span>
                                        <span>:</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-wrap gap-3 align-items-center pt-1">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="description_option" value="batch" id="desc_batch">
                                                <label class="form-check-label small" for="desc_batch">Batch/Pallet No</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="description_option" value="wb" id="desc_wb">
                                                <label class="form-check-label small" for="desc_wb">WB/DEO No</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="description_option" value="tank" id="desc_tank">
                                                <label class="form-check-label small" for="desc_tank">Tank No</label>
                                            </div>
                                        </div>

                                        <div class="input-group input-group-sm mt-1" id="keterangan_sample_input_wrapper" style="display: none; max-width: 350px;">
                                            <input type="text" class="form-control" id="keterangan_sample_input_1" placeholder="Nomor...">
                                            <span class="input-group-text py-0" id="batch_suffix_p" style="display: none;">P</span>
                                            <input type="text" class="form-control" id="keterangan_sample_input_2" style="display: none;" placeholder="No Pallet...">
                                        </div>
                                        <input type="hidden" name="description" id="description" required>
                                    </div>
                                </div>

                                {{-- 3. TGL PRODUKSI --}}
                                <div class="row mb-2">
                                    <label class="col-md-3 col-form-label col-form-label-sm fw-bold d-flex justify-content-between">
                                        <span>Tgl Produksi <span class="text-danger">*</span></span>
                                        <span>:</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="date" class="form-control form-control-sm" name="production_date" style="max-width: 200px;" required>
                                    </div>
                                </div>

                                {{-- 4. PERSIAPAN SAMPLE --}}
                                <div class="row mb-2">
                                    <label class="col-md-3 col-form-label col-form-label-sm fw-bold d-flex justify-content-between">
                                        <span>Persiapan sample <span class="text-danger">*</span></span>
                                        <span>:</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-wrap gap-3 align-items-center pt-1">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="preparation_method_option" value="Tidak berubah" id="prep_no_change">
                                                <label class="form-check-label small" for="prep_no_change">Tidak berubah</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="preparation_method_option" value="Rework Karton" id="prep_karton">
                                                <label class="form-check-label small" for="prep_karton">Rework Karton</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="preparation_method_option" value="Rework Stencill" id="prep_stencil">
                                                <label class="form-check-label small" for="prep_stencil">Rework Stencill</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="preparation_method_option" value="Rework Label" id="prep_label">
                                                <label class="form-check-label small" for="prep_label">Rework Label</label>
                                            </div>
                                            {{-- Lainnya sejajar --}}
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" id="preparation_method_other_radio" name="preparation_method_option" value="Lainnya">
                                                <label class="form-check-label small" for="preparation_method_other_radio">Lainnya...</label>
                                            </div>
                                        </div>
                                        {{-- Input di bawah --}}
                                        <input type="text" class="form-control form-control-sm mt-1" id="preparation_method_other_input" style="display: none; max-width: 100%;" placeholder="Sebutkan metode lain...">
                                        <input type="hidden" name="preparation_method" id="preparation_method" required>
                                    </div>
                                </div>

                                {{-- 5. KETERANGAN --}}
                                <div class="row mb-1">
                                    <label class="col-md-3 col-form-label col-form-label-sm fw-bold d-flex justify-content-between">
                                        <span>Keterangan <span class="text-danger">*</span></span>
                                        <span>:</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-wrap gap-3 align-items-center pt-1">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="sample_notes_option" value="Tempel sticker" id="note_sticker">
                                                <label class="form-check-label small" for="note_sticker">Tempel sticker</label>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" id="sample_notes_other_radio" name="sample_notes_option" value="Lainnya">
                                                <label class="form-check-label small" for="sample_notes_other_radio">Lainnya...</label>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control form-control-sm mt-1" id="sample_notes_other_input" style="display: none; max-width: 100%;" placeholder="Sebutkan keterangan lain...">
                                        <input type="hidden" name="sample_notes" id="sample_notes" required>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Tombol Submit / Form Aksi Lain --}}
            <div class="right-column">
                <div class="card action-card">
                    <div class="card-body p-4">
                        <h5 class="section-title">
                            <i class="fas fa-edit"></i>
                            @php
                                $actionName = ucwords(str_replace('_', ' ', $action));
                                if($action === 'submit' && $isWarehouseProcess) $actionName = 'Submit';
                                if($action === 'qa_submit') $actionName = 'QA Submit';
                            @endphp
                            {{ $actionName }}
                        </h5>

                        {{-- Hidden inputs yang diperlukan --}}
                        @if($action === 'approve' || ($action === 'submit' && $isWarehouseProcess))
                            <input type="hidden" name="action" value="{{ $action }}">
                        @endif

                        {{-- [PERBAIKAN] Tombol Submit QA --}}
                        @if($isQaForm)
                            <div class="alert alert-info">
                                <small>Please ensure all fields on the left (including Qty Issued) are filled correctly before submitting.</small>
                            </div>
                            <div class="d-grid mt-2">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">Submit QA Form</button>
                            </div>

                        @elseif($isWarehouseProcess)
                            <input type="hidden" name="action" value="{{ $action }}">

                            @if($action === 'submit')
                                <div class="alert alert-success">
                                    <strong>Quick Submit:</strong> You are approving this step without changes.
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">Confirm Submit</button>
                                </div>

                            @elseif ($action === 'review')
                                <div class="alert alert-primary">
                                    <strong>Submit with Notes:</strong> Please provide notes/remarks regarding this step.
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label"><strong>Notes/Reason: <span class="text-danger">*</span></strong></label>

                                    <div class="alert alert-warning d-flex align-items-start p-2 mb-2" role="alert" style="font-size: 0.8rem;">
                                        <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                        <div>
                                            <strong>Validation:</strong> Please provide a clear sentence. Inputs consisting solely of <b>numbers</b> or <b>punctuation</b> are not allowed.
                                        </div>
                                    </div>

                                    <textarea class="form-control" id="notes" name="notes" rows="5" placeholder="Provide notes..." required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit with Notes</button>
                                </div>

                            @elseif ($action === 'update_qty')
                                <div class="alert alert-warning border-warning">
                                    <strong>Update Quantity Issued:</strong><br>
                                    Please input the "Qty Issued" directly in the <b>Item List table</b> on the left, then add notes below.
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label"><strong>Notes: <span class="text-danger">*</span></strong></label>

                                    <div class="alert alert-warning d-flex align-items-start p-2 mb-2" role="alert" style="font-size: 0.8rem;">
                                        <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                        <div>
                                            <strong>Validation:</strong> Please provide a clear sentence explaining the quantity change. <b>Numbers only</b> are not accepted here.
                                        </div>
                                    </div>

                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Provide reason for quantity update..." required></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit Qty & Notes</button>
                                </div>
                            @endif

                        @else
                            {{-- Form Approval (Managerial) --}}
                            <div class="mb-3">
                                <label class="form-label"><strong>Decision:</strong></label>
                                <div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="action" id="action_review"
                                            value="review" @if($action === 'review' && $originalAction !== 'reject') checked @endif>
                                        <label class="form-check-label text-primary" for="action_review"><strong>
                                            Approve with Review</strong></label>
                                    </div>
                                    <div class="form-check me-3 mb-1">
                                        <input class="form-check-input" type="radio" name="action" id="action_reject"
                                            value="reject" @if($originalAction === 'reject') checked @endif>
                                        <label class="form-check-label text-danger"
                                            for="action_reject"><strong>Reject</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label"><strong>Notes/Reason:</strong></label>

                                <div class="alert alert-warning d-flex align-items-start p-2 mb-2" role="alert" style="font-size: 0.8rem;">
                                    <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                    <div>
                                        <strong>Validation:</strong> Please provide a clear explanation. Inputs consisting solely of <b>numbers</b> or <b>symbols</b> will be rejected by the system.
                                    </div>
                                </div>

                                <textarea class="form-control" id="notes" name="notes" rows="8"
                                    placeholder="Provide notes for your decision..."></textarea>
                                <div class="form-text text-muted">Notes are required for rejection or review.</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit Notes</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('responseForm');
            const overlay = document.getElementById('processingOverlay');
            const submitBtn = document.getElementById('submitBtn');

            const isQaForm = {{ $isQaForm ? 'true' : 'false' }};
            const isWarehouseProcess = {{ $isWarehouseProcess ? 'true' : 'false' }};
            const isQuickAction = ('{{ $action }}' === 'approve') || ('{{ $action }}' === 'submit' && isWarehouseProcess);

            // Validasi Input Angka (Qty) untuk QA Form maupun Update Qty Warehouse
            if ('{{ $action }}' === 'update_qty' || isQaForm) {
                const qtyInputs = document.querySelectorAll('input[name^="items"]');

                qtyInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        let value = this.value;
                        value = value.replace(/[^0-9]/g, '');

                        if (value.length > 1 && value.startsWith('0')) {
                            value = parseInt(value, 10).toString();
                        }
                        this.value = value;
                    });
                });
            }

            // --- FUNGSI VALIDASI ---
            const validateForm = () => {
                if (isQaForm) {
                    let allValid = true;
                    // Validasi QA Inputs
                    document.querySelectorAll('.qa-input-section [required]').forEach(input => {
                        if (!input.value.trim()) { allValid = false; }
                    });

                    if (!allValid) {
                        Swal.fire({ icon: 'warning', title: 'Form Tidak Lengkap', text: 'Mohon isi semua kolom Form QA yang wajib diisi (*).' });
                        return false;
                    }

                    // Validasi Qty Issued untuk QA (Opsional tapi disarankan)
                    let qtyFilled = true;
                    // Uncomment jika Qty Issued Wajib diisi > 0
                    /*
                    document.querySelectorAll('input[name^="items"]').forEach(input => {
                        if(input.value === '' || parseInt(input.value) < 0) qtyFilled = false;
                    });
                    if(!qtyFilled) {
                         Swal.fire({ icon: 'warning', title: 'Quantity Invalid', text: 'Mohon cek kembali Qty Issued di tabel barang.' });
                         return false;
                    }
                    */
                } else if (isWarehouseProcess) {
                    const notesTextarea = document.getElementById('notes');
                    if (notesTextarea && !(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Mohon berikan catatan yang valid.' });
                        return false;
                    }

                    if ('{{ $action }}' === 'update_qty') {
                        let qtyValid = true;
                        let invalidMessage = '';

                        const inputs = document.querySelectorAll('input[name^="items"]');

                        inputs.forEach(input => {
                            const val = input.value;
                            if (val === '') {
                                qtyValid = false; invalidMessage = 'Kolom Qty Issued tidak boleh kosong.';
                            } else if (parseInt(val) < 0) {
                                qtyValid = false; invalidMessage = 'Qty Issued tidak boleh negatif.';
                            }
                        });

                        if(!qtyValid) {
                             Swal.fire({ icon: 'warning', title: 'Quantity Invalid', text: invalidMessage });
                             return false;
                        }
                    }

                } else { // Approval
                    const reviewRadio = document.getElementById('action_review');
                    const rejectRadio = document.getElementById('action_reject');
                    const notesTextarea = document.getElementById('notes');
                    if ((reviewRadio && reviewRadio.checked || rejectRadio && rejectRadio.checked) && !(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Alasan Diperlukan', text: 'Mohon berikan alasan yang valid.' });
                        return false;
                    }
                }
                return true;
            };

            // --- AUTO SUBMIT (Quick Action) ---
            if (isQuickAction) {
                overlay.style.display = 'flex';
                form.submit();
            }
            // --- MANUAL SUBMIT ---
            else if (form && submitBtn) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    if (!validateForm()) {
                        return;
                    }

                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;

                    Swal.fire({
                        title: 'Konfirmasi Pengiriman',
                        text: "Apakah Anda yakin ingin melanjutkan?",
                        icon: 'question',
                        showRecallButton: true,
                        confirmButtonColor: '#3085d6',
                        recallButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        recallButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            overlay.style.display = 'flex';
                            form.submit();
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });
            }

            // --- LOGIKA JS UNTUK FORM QA ---
            if (isQaForm) {
                function setupQaRadioLainnya(baseName) {
                    const otherRadio = document.getElementById(`${baseName}_other_radio`);
                    const otherInput = document.getElementById(`${baseName}_other_input`);
                    const finalInput = document.getElementById(baseName);
                    document.querySelectorAll(`input[name="${baseName}_option"]`).forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (this.value === 'Lainnya') {
                                otherInput.style.display = 'block';
                                otherInput.focus();
                                finalInput.value = otherInput.value;
                            } else {
                                otherInput.style.display = 'none';
                                otherInput.value = '';
                                finalInput.value = this.value;
                            }
                        });
                    });
                    otherInput.addEventListener('input', function() {
                        otherRadio.checked = true;
                        finalInput.value = this.value;
                    });
                }
                setupQaRadioLainnya('source');
                setupQaRadioLainnya('preparation_method');
                setupQaRadioLainnya('sample_notes');
                const keteranganWrapper = document.getElementById('keterangan_sample_input_wrapper');
                const keteranganInput1 = document.getElementById('keterangan_sample_input_1');
                const keteranganInput2 = document.getElementById('keterangan_sample_input_2');
                const batchSuffix = document.getElementById('batch_suffix_p');
                const finalDescriptionInput = document.getElementById('description');

                document.querySelectorAll('input[name="description_option"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const selectedType = this.value;
                        keteranganInput1.value = '';
                        keteranganInput2.value = '';
                        finalDescriptionInput.value = '';
                        keteranganWrapper.style.display = 'flex';
                        if (selectedType === 'batch') {
                            keteranganInput1.placeholder = 'Batch No...';
                            keteranganInput2.style.display = 'block';
                            keteranganInput2.placeholder = 'Pallet No...';
                            batchSuffix.style.display = 'inline-block';
                        } else {
                            keteranganInput1.placeholder = selectedType === 'wb' ? 'WB/DEO No...' : 'Tank No...';
                            keteranganInput2.style.display = 'none';
                            batchSuffix.style.display = 'none';
                        }
                    });
                });
                function updateDescription() {
                    const selectedType = document.querySelector('input[name="description_option"]:checked')?.value;
                    if (!selectedType) return;
                    const val1 = document.getElementById('keterangan_sample_input_1').value;
                    const val2 = document.getElementById('keterangan_sample_input_2').value;
                    let finalValue = '';
                    if (selectedType === 'batch') {
                        finalValue = `${val1}P${val2}`;
                    } else if (selectedType === 'wb') {
                        finalValue = `WB:${val1}`;
                    } else if (selectedType === 'tank') {
                        finalValue = `TANK:${val1}`;
                    }
                    finalDescriptionInput.value = finalValue;
                }
                keteranganInput1.addEventListener('input', updateDescription);
                keteranganInput2.addEventListener('input', updateDescription);
            }
        });
    </script>
</body>
</html>
