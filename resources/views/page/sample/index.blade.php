<x-app-layout>
    @section('title')
    Sample Requisition
    @endsection

    @push('css')
    <link href="{{ asset('assets/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .modal-xl { max-width: 80vw !important; }
        .modal-body { max-height: calc(100vh - 210px); overflow-y: auto; }
        .view-modal-card { border: none; border-radius: .75rem; box-shadow: 0 4px 12px rgba(0,0,0, .08); margin-bottom: 1.5rem !important; }
        .view-modal-card-header { background-color: transparent; border-bottom: 1px solid #e9ecef; padding: 1rem 1.25rem; }
        .view-modal-card-header h5 { color: var(--bs-primary); font-weight: 600; margin-bottom: 0; }
        .view-label { color: #6c757d; font-size: 0.85rem; margin-bottom: .25rem; display: block; }
        .view-data { font-weight: 600; color: #212529; margin-bottom: 0; }

        /* NEW: Style for clickable badge */
        #view_status_badge a.badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0, .2);
            transition: all .2s ease;
        }

        /* Tracker Styles */
        .tracker-container { display: flex; justify-content: space-between; position: relative; padding: 10px 0; }
        .tracker-line { position: absolute; top: 29px; left: 5%; right: 5%; height: 3px; background-color: #e9ecef; z-index: 1; }
        .tracker-line-progress { position: absolute; top: 0; left: 0; height: 100%; background-color: #0d6efd; z-index: 2; width: 0%; transition: width 0.5s ease-in-out; }
        .tracker-step { position: relative; z-index: 3; text-align: center; width: 16%; }
        .tracker-icon { width: 30px; height: 30px; border-radius: 50%; background-color: #ced4da; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; border: 2px solid #ced4da; transition: all 0.3s ease; }
        .tracker-step.completed .tracker-icon { background-color: #0d6efd; border-color: #0d6efd; }
        .tracker-step.active .tracker-icon { background-color: #ffc107; border-color: #ffc107; }
        .tracker-step.rejected .tracker-icon { background-color: #dc3545; border-color: #dc3545; }
        .tracker-label { font-size: 0.8rem; font-weight: 600; color: #6c757d; }
        .tracker-step.completed .tracker-label, .tracker-step.active .tracker-label { color: #212529; }
        .tracker-details { font-size: 0.75rem; margin-top: 5px; min-height: 30px; }
        .tracker-user { font-weight: 500; color: #495057; }
        .tracker-date { color: #6c757d; }

        .modal-content {
            position: relative; /* Penting agar overlay bisa diposisikan di dalamnya */
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.65);
            z-index: 10; /* Pastikan berada di atas semua elemen lain */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: .75rem; /* Samakan dengan radius modal */
            color: white;
        }
    </style>
    @endpush

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Sample Requisition List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-note-pencil f-s-16"></i> Forms
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Sample Requisition</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#sampleModal" id="btn-create-sample">
                    <i class="ph-bold ph-plus pe-2"></i> Add Sample Request
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="sample-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Requester</th>
                                    <th>Customer</th>
                                    <th>Request Date</th>
                                    <th>Sub Category</th>
                                    <th>Route To</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="sampleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="loading-overlay" style="display: none;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="text-white mt-3 fw-bold">Processing...</h5>
                </div>
                <div class="modal-header bg-primary">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="Logo" style="height: 24px;"
                            class="me-3">
                        <h5 class="modal-title text-white mb-0" id="sampleModalLabel">Create New Sample Requisition</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="sampleForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="sub_category" class="form-label fw-bold">1. Select Sub Category<i
                                        class="text-danger">*</i></label>
                                <select class="form-select select2-styled" id="sub_category" name="sub_category"
                                    style="width: 100%;">
                                    <option></option>
                                    @foreach ($allowedSubCategories as $subCat)
                                    <option value="{{ $subCat }}">{{ $subCat }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="sub_category_hidden">
                                <div class="invalid-feedback" id="sub_category_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" value="SAMPLE" readonly>
                            </div>
                        </div>

                        <div id="requisition-form-details" style="display: none;">
                            <div id="main-requisition-data">
                                <hr>
                                <h5 class="fw-bold text-primary mb-3">Requisition Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label">Customer Name<i
                                                class="text-danger">*</i></label>
                                        <select class="form-select select2-styled" id="customer_id" name="customer_id"
                                            style="width: 100%;">
                                            <option></option>
                                            @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">
                                                {{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="customer_id_hidden">
                                        <div class="invalid-feedback" id="customer_id_error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="customer_address" rows="2" readonly></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="no_srs" class="form-label">SRS No.<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="no_srs" name="no_srs"
                                            value="{{ $generatedSrs }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="account" class="form-label">Account<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="account" name="account"
                                            value="{{ $userAccount }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="request_date" class="form-label">Request Date<i
                                                class="text-danger">*</i></label>
                                        <input type="date" class="form-control" id="request_date" name="request_date"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cost_center" class="form-label">Cost Center</label>
                                        <input type="text" class="form-control" id="cost_center" name="cost_center"
                                        placeholder="e.g: CC1001, CC2002e">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="objectives" class="form-label">Objectives<i
                                                class="text-danger">*</i></label>
                                        <textarea class="form-control" id="objectives" name="objectives"
                                            placeholder="e.g: New Product Development, Quality Improvement, Others: Market Testing"
                                            rows="2"></textarea>
                                        <div class="invalid-feedback" id="objectives_error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="estimated_potential" class="form-label">Estimated Potential<i
                                                class="text-danger">*</i></label>
                                        <textarea class="form-control" id="estimated_potential" name="estimated_potential"
                                            placeholder="e.g.: High, Medium, Low, Others: Specify Here"
                                            rows="2"></textarea>
                                        <div class="invalid-feedback" id="estimated_potential_error"></div>
                                    </div>
                                </div>

                                <hr class="mt-4">

                                <h5 class="fw-bold text-primary mb-2">Product Details</h5>
                                <div class="mb-3" id="material-type-selection-container" style="display:none;">
                                    <label class="form-label fw-bold">2. Select Material Type<i
                                            class="text-danger">*</i></label>
                                    <div>
                                        @foreach ($materialTypes as $type)
                                        <div class="form-check">
                                            <input class="form-check-input material-type-checkbox" type="checkbox"
                                                id="type_{{ Str::slug($type) }}" value="{{ $type }}">
                                            <label class="form-check-label"
                                                for="type_{{ Str::slug($type) }}">{{ $type }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3" id="product-selection-container" style="display:none;">
                                    <label for="product_select" class="form-label fw-bold">3. Select Product Name<i
                                            class="text-danger">*</i></label>
                                    <select class="form-select select2-styled" id="product_select" multiple="multiple"
                                        style="width: 100%;" disabled></select>
                                    <div id="product_select_note"
                                        class="form-text text-warning fw-semibold fst-italic mt-1">
                                        <i class="ph ph-arrow-fat-up me-1"></i>
                                        Please select a Material Type above to enable this.
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm mt-2" id="btn-add-items-detail">
                                        <i class="ph-bold ph-plus"></i> Add Item to List
                                    </button>
                                </div>

                                <div class="mb-3" id="product-selection-container-fg" style="display:none;">
                                    <label for="product_select_fg" class="form-label fw-bold">2. Select Product Name<i
                                            class="text-danger">*</i></label>
                                    <select class="form-select select2-styled" id="product_select_fg" multiple="multiple"
                                        style="width: 100%;"></select>
                                    <button type="button" class="btn btn-success btn-sm mt-2" id="btn-add-items-master">
                                        <i class="ph-bold ph-plus"></i> Add Item to List
                                    </button>
                                </div>

                                <div class="alert alert-danger" id="items_error" style="display: none;"></div>

                                <div class="table-responsive mt-4">
                                    <h6 class="fw-bold">3. Requested Item List</h6>
                                    <table class="table table-bordered w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Unit</th>
                                                <th style="width: 15%;">Qty Required</th>
                                                <th style="width: 15%;">Qty Issued</th>
                                            </tr>
                                        </thead>
                                        <tbody id="requisition-items-tbody">
                                            <tr id="no-items-row">
                                                <td colspan="5" class="text-center">No items have been added yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="special-order-fields" style="display: none;">
                                <hr class="mt-4">
                                <h5 class="text-primary fw-bold">Special Order Details</h5>

                                <h6 class="text-danger fw-bold">
                                    <center><i>To be filled by Marketing</i></center>
                                </h6>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-4">
                                        <label for="requested_date" class="form-label">Sample Completion Date<i
                                                class="text-danger">*</i></label>
                                        <input type="date" class="form-control sm-field" name="requested_date">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Sample Weight<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control sm-field" name="weight_selection"
                                            placeholder="e.g.: 25 Kg, 15 Kg, Others: 10 Pcs">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Sample Packaging<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control sm-field" name="packaging_selection"
                                            placeholder="e.g.: Carton, Others: Plastic Wrap">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="sample_count" class="form-label">Number of Samples<i
                                                class="text-danger">*</i></label>
                                        <input type="text" class="form-control sm-field" name="sample_count"
                                            placeholder="e.g.: 2x1kg (each product)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Certificate of Analysis<i
                                                class="text-danger">*</i></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="coa_required"
                                                    id="coa_yes" value="1">
                                                <label class="form-check-label" for="coa_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="coa_required"
                                                    id="coa_no" value="0">
                                                <label class="form-check-label" for="coa_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Shipment Method<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control sm-field" name="shipment_method"
                                            placeholder="e.g.: Delivery (DHL), Others: Gojek">
                                    </div>
                                </div>

                                @if ((isset($userAccount) && $userAccount == '5302') ||
                                auth()->user()->roles()->where('name', 'super-admin')->exists())
                                <div class="qa-fields-section mt-4">
                                    <hr>
                                    <h6 class="text-danger fw-bold">
                                        <center><i>To be filled by QA/QM</i></center>
                                    </h6>
                                    <div class="row g-3 mt-1">
                                        <div class="col-12">
                                            <label class="form-label">Sample Origin<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control qa-field" name="sample_origin">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Sample Info: Batch/Pallet No<i
                                                    class="text-danger">*</i></label>
                                            <input type="text" class="form-control qa-field" name="sample_description_batch">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">WB/DEO No<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control qa-field" name="sample_description_wb">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tank No<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control qa-field" name="sample_description_tank">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Production Date<i
                                                    class="text-danger">*</i></label>
                                            <input type="date" class="form-control qa-field" name="production_date">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Sample Preparation<i
                                                    class="text-danger">*</i></label>
                                            <input type="text" class="form-control qa-field" name="sample_preparation">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Notes</label>
                                            <input type="text" class="form-control qa-field" name="qa_notes">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" id="saveSampleBtn">Save</button>
                        <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Modal (MODIFIED with new layout) --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: .75rem;">
                <div class="modal-header bg-primary text-white" style="border-top-left-radius: .75rem; border-top-right-radius: .75rem;">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Sample Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">

                    {{-- CARD 1: MAIN REQUISITION DETAILS --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <small class="view-label">Category</small>
                                    <p class="view-data">SAMPLE</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Sub Category</small>
                                    <p class="view-data" id="view_sub_category">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">SRS No.</small>
                                    <p class="view-data" id="view_no_srs">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Request Date</small>
                                    <p class="view-data" id="view_request_date">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Customer Name</small>
                                    <p class="view-data" id="view_customer_name">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Address</small>
                                    <p class="view-data" id="view_customer_address">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Account</small>
                                    <p class="view-data" id="view_account">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Cost Center</small>
                                    <p class="view-data" id="view_cost_center">-</p>
                                </div>

                                <div class="col-md-3">
                                    <small class="view-label">Objectives</small>
                                    <p class="view-data fst-italic fw-normal" id="view_objectives">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Estimated Potential</small>
                                    <p class="view-data fst-italic fw-normal" id="view_estimated_potential">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: REQUESTED ITEM LIST --}}
                    <div class="card view-modal-card">
                         <div class="card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold  ph-list me-2"></i>Requested Item List</h5>
                        </div>
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100 mb-1">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Unit</th>
                                            <th class="text-center">Qty Required</th>
                                            <th class="text-center">Qty Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-items-tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 3: SPECIAL ORDER DETAILS (Conditional) --}}
                    <div class="card view-modal-card" id="view-special-order-section" style="display: none;">
                        <div class="card-header">
                           <h5 class="fw-bold text-primary mb-0">Special Order Details (Marketing)</h5>
                        </div>
                        <div class="card-body">
                             <div class="row g-4">
                                <div class="col-md-4"><label class="text-muted">Sample Completion Date</label><p class="fs-6 fw-semibold" id="view_requested_date">-</p></div>
                                <div class="col-md-8"><label class="text-muted">Sample Weight</label><p class="fs-6 fw-semibold" id="view_weight_selection">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Sample Packaging</label><p class="fs-6 fw-semibold" id="view_packaging_selection">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Number of Samples</label><p class="fs-6 fw-semibold" id="view_sample_count">-</p></div>
                                <div class="col-md-4"><label class="text-muted">COA Required?</label><p class="fs-6 fw-semibold" id="view_coa_required">-</p></div>
                                <div class="col-md-12"><label class="text-muted">Shipment Method</label><p class="fs-6 fw-semibold" id="view_shipment_method">-</p></div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 4: APPROVAL TRACKING (MOVED TO BOTTOM) --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-path me-2"></i> Approval & Process Tracking</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold me-3">Current Status:</span>
                                <div id="view_status_badge"></div>
                            </div>
                            <div class="tracker-container" id="approval-tracker-container">
                                <div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>
                                <div class="tracker-step" data-step-name="Submitted"><div class="tracker-icon"><i class="ph-bold ph-file-arrow-up fs-6"></i></div><div class="tracker-label">Submitted</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Manager Approval"><div class="tracker-icon"><i class="ph-bold ph-user-plus fs-6"></i></div><div class="tracker-label">Manager</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Business Controller Approval"><div class="tracker-icon"><i class="ph-bold ph-briefcase fs-6"></i></div><div class="tracker-label">Business Controller</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Warehouse Processing"><div class="tracker-icon"><i class="ph-bold ph-package fs-6"></i></div><div class="tracker-label">Warehouse</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Ready for Dispatch"><div class="tracker-icon"><i class="ph-bold ph-truck fs-6"></i></div><div class="tracker-label">Dispatch</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Completed"><div class="tracker-icon"><i class="ph-bold ph-check-circle fs-6"></i></div><div class="tracker-label">Completed</div><div class="tracker-details"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #dee2e6;">
                    <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- Unchanged JS from original file ---
        let nextSrsNumber = "{{ $generatedSrs }}";

        function successMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 1500,
                showConfirmButton: true
            });
        }

        function errorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'An Error Occurred',
                text: message
            });
        }

        function warningMessage(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: message
            });
        }

        $(document).ready(function () {
            const userDepartmentCode = "{{ $userAccount ?? '' }}";

            function initSelect2() {
                function formatSubCategory(option) {
                    if (!option.id) return '<span class="text-muted">Select Sub Category</span>';
                    let icon = '';
                    if (option.text.includes('Packaging')) icon =
                        '<i class="ph ph-package me-2 text-primary"></i>';
                    if (option.text.includes('Finished Goods')) icon =
                        '<i class="ph ph-cube me-2 text-success"></i>';
                    if (option.text.includes('Special')) icon =
                        '<i class="ph ph-star-four me-2 text-warning"></i>';
                    return `<span style='font-weight:500;'>${icon}${option.text}</span>`;
                }

                $('#sub_category').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Select Sub Category',
                    allowClear: true,
                    templateResult: formatSubCategory,
                    templateSelection: formatSubCategory,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                function formatCustomer(option) {
                    if (!option.id) return '<span class="text-muted">Select Customer</span>';
                    return `<i class='ph ph-user-circle me-2 text-info'></i> <span style='font-weight:500;'>${option.text}</span>`;
                }
                $('#customer_id').select2({
                    dropdownParent: $('#sampleModal'),
                    placeholder: 'Select Customer',
                    allowClear: true,
                    templateResult: formatCustomer,
                    templateSelection: formatCustomer,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                $('#product_select').select2({
                    dropdownParent: $('#sampleModal'),
                    placeholder: 'Select products',
                    allowClear: true
                });

                $('#product_select_fg').select2({
                    dropdownParent: $('#sampleModal'),
                    placeholder: 'Select Finished Goods/Spesial Order products',
                    allowClear: true
                });
            }
            initSelect2();

            const table = $('#sample-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sample.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '20px',
                        className: 'text-center'
                    },
                    {
                        data: 'requester_info',
                        name: 'users.name'
                    },
                    {
                        data: 'customer_name',
                        name: 'customers.name'
                    },
                    {
                        data: 'request_date',
                        name: 'requisitions.request_date'
                    },
                    {
                        data: 'sub_category',
                        name: 'requisitions.sub_category'
                    },
                    {
                        data: 'route_to',
                        name: 'requisitions.route_to'
                    },
                    {
                        data: 'status',
                        name: 'requisitions.status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            function clearValidationErrors() {
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#items_error').hide().text('');
            }

            function resetForm() {
                $('#sampleForm')[0].reset();
                $('#sub_category, #customer_id, #product_select, #product_select_fg').val(null).trigger('change');
                $('.material-type-checkbox').prop('checked', false);
                $('#requisition-items-tbody').html(
                    '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                );
                $('#requisition-form-details').hide();
                clearValidationErrors();

                // --- UPDATED LOGIC ---
                $('#initial-category-selection').show();
                $('#main-requisition-data').show();
                
                // Kembalikan 'name' ke select yang terlihat
                $('#sub_category').attr('name', 'sub_category');
                $('#customer_id').prop('disabled', false); // Pastikan customer select aktif kembali

                // Hapus 'name' dari hidden input
                $('#sub_category_hidden').removeAttr('name');
                $('#customer_id_hidden').removeAttr('name'); // <-- BARIS BARU
            }

            $('#btn-create-sample').on('click', function () {
                resetForm();
                $('#sampleModalLabel').text('Create Sample Requisition');
                $('#sampleForm').attr('data-mode', 'create').removeAttr('data-id');
                $('#no_srs').val(nextSrsNumber);
            });

            $('#sub_category').on('change', function () {
                const isEditMode = $('#sampleForm').attr('data-mode') === 'edit';
                if (isEditMode) return;

                const selectedSubCategory = $(this).val();

                $('#special-order-fields, #material-type-selection-container, #product-selection-container, #product-selection-container-fg')
                    .hide();
                $('.material-type-checkbox').prop('checked', false);
                $('#product_select, #product_select_fg').val(null).trigger('change');
                $('#product_select').prop('disabled', true);
                $('#product_select_note').show();

                if (selectedSubCategory) {
                    $('#requisition-form-details').slideDown();
                } else {
                    $('#requisition-form-details').slideUp();
                    return;
                }

                $('#special-order-fields').toggle(selectedSubCategory === 'Special Order');

                if (selectedSubCategory === 'Packaging') {
                    $('#material-type-selection-container').slideDown();
                    $('#product-selection-container').slideDown();
                } else if (selectedSubCategory === 'Finished Goods' || selectedSubCategory ===
                    'Special Order') {
                    $.ajax({
                        url: "{{ route('sample.getAllItemMasters') }}",
                        method: 'GET',
                        success: function (masters) {
                            const productSelectFg = $('#product_select_fg');
                            productSelectFg.empty();
                            masters.forEach(m => productSelectFg.append(new Option(
                                `[${m.item_master_code}] ${m.item_master_name}`,
                                m.id)));
                            productSelectFg.trigger('change');
                            $('#product-selection-container-fg').slideDown();
                        }
                    });
                }
            });

            $('#customer_id').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address') || '';
                $('#customer_address').val(address);
            });

            $('.material-type-checkbox').on('change', function () {
                $('#product_select').val(null).trigger('change');
                $('#requisition-items-tbody').html(
                    '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                    );

                const selectedTypes = [];
                $('.material-type-checkbox:checked').each(function () {
                    selectedTypes.push($(this).val());
                });
                const productSelect = $('#product_select');
                const productSelectNote = $('#product_select_note');
                if (selectedTypes.length > 0) {
                    productSelect.prop('disabled', false);
                    productSelectNote.hide();
                    $.ajax({
                        url: "{{ route('sample.getProductsByMaterialTypes') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            material_types: selectedTypes
                        },
                        success: function (products) {
                            productSelect.empty();
                            products.forEach(p => productSelect.append(new Option(p
                                .item_master_name, p.id)));
                            productSelect.trigger('change');
                        },
                        error: function () {
                            errorMessage('Failed to load products.');
                        }
                    });
                } else {
                    productSelect.prop('disabled', true).empty().trigger('change');
                    productSelectNote.show();
                }
            });

            $('#btn-add-items-detail').on('click', function () {
                const selectedProductIds = $('#product_select').val();
                if (!selectedProductIds || selectedProductIds.length === 0) {
                    warningMessage('Please select a product first.');
                    return;
                }
                $.ajax({
                    url: "{{ route('sample.getItemDetailsByProducts') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_ids: selectedProductIds
                    },
                    success: function (itemDetails) {
                        $('#no-items-row').remove();
                        itemDetails.forEach(detail => {
                            if ($(`#item-row-detail-${detail.id}`).length === 0) {
                                const newRow = `
                                    <tr id="item-row-detail-${detail.id}" data-master-id="${detail.item_master_id}">
                                        <td>${detail.item_detail_code}</td>
                                        <td>${detail.item_detail_name}</td>
                                        <td>${detail.unit}</td>
                                        <td><input type="number" class="form-control" name="items[${detail.id}][quantity_required]" min="1"></td>
                                        <td><input type="number" class="form-control" name="items[${detail.id}][quantity_issued]" min="0"></td>
                                    </tr>`;
                                $('#requisition-items-tbody').append(newRow);
                            }
                        });
                    }
                });
            });

            $('#btn-add-items-master').on('click', function () {
                const selectedMasterIds = $('#product_select_fg').val();
                if (!selectedMasterIds || selectedMasterIds.length === 0) {
                    warningMessage('Please select a product first.');
                    return;
                }
                $.ajax({
                    url: "{{ route('sample.getAllItemMasters') }}",
                    method: 'GET',
                    success: function (allMasters) {
                        const selectedMasters = allMasters.filter(m => selectedMasterIds
                            .includes(String(m.id)));
                        $('#no-items-row').remove();
                        selectedMasters.forEach(master => {
                            if ($(`#item-row-master-${master.id}`).length === 0) {
                                const newRow = `
                                    <tr id="item-row-master-${master.id}" data-master-id="${master.id}">
                                        <td>${master.item_master_code}</td>
                                        <td>${master.item_master_name}</td>
                                        <td>${master.unit}</td>
                                        <td><input type="number" class="form-control" name="items[${master.id}][quantity_required]" min="1"></td>
                                        <td><input type="number" class="form-control" name="items[${master.id}][quantity_issued]" min="0"></td>
                                    </tr>`;
                                $('#requisition-items-tbody').append(newRow);
                            }
                        });
                    }
                });
            });

            $('#product_select, #product_select_fg').on('select2:unselect', function (e) {
                const unselectedMasterId = e.params.data.id;
                $(`tr[data-master-id="${unselectedMasterId}"]`).remove();

                if ($('#requisition-items-tbody tr').length === 0) {
                    $('#requisition-items-tbody').html(
                        '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                        );
                }
            });

            $('#sampleForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors();
                const submitBtn = $('#saveSampleBtn');
                const overlay = $('#sampleModal .loading-overlay');
                overlay.show();
                submitBtn.prop('disabled', true);
                const mode = $(this).attr('data-mode');
                const id = $(this).attr('data-id');
                let url = (mode === 'edit') ? `/sample-form/${id}` : "{{ route('sample-form.store') }}";
                let formData = new FormData(this);
                if (mode === 'edit') formData.append('_method', 'PUT');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.success) {
                            $('#sampleModal').modal('hide');
                            table.ajax.reload();
                            successMessage(res.message);
                        }
                        if (res.next_srs_number) {
                            nextSrsNumber = res.next_srs_number;
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const key in errors) {
                                const errorMsg = errors[key][0];
                                if (key.startsWith('items.')) {
                                    $('#items_error').show().text(
                                        'Please ensure item quantities are filled.');
                                } else {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}_error`).text(errorMsg);
                                }
                            }
                            errorMessage('Please ensure the data you entered is correct.');
                        } else {
                            errorMessage(xhr.responseJSON?.message ||
                                'A system error occurred.');
                        }
                    }
                });
            });

            function populateForm(data, mode = null) {
                $('#sampleForm').attr('data-mode', 'edit');

                if (mode === 'qa_mode') {
                    // --- QA MODE LOGIC ---
                    $('#initial-category-selection').hide();
                    $('#main-requisition-data').hide();
                    $('#special-order-fields').show();

                    // Pindahkan 'name' ke hidden input untuk sub_category
                    $('#sub_category_hidden').val(data.sub_category).attr('name', 'sub_category');
                    $('#sub_category').removeAttr('name');

                    // Lakukan hal yang sama untuk customer_id
                    $('#customer_id_hidden').val(data.customer_id).attr('name', 'customer_id'); // <-- BARIS BARU
                    $('#customer_id').prop('disabled', true).trigger('change'); // Tetap disable tampilan

                } else {
                    // --- NORMAL EDIT MODE LOGIC ---
                    $('#initial-category-selection').show();
                    $('#main-requisition-data').show();
                    $('#special-order-fields').toggle(data.sub_category === 'Special Order');
                    
                    // Kembalikan 'name' ke select yang terlihat
                    $('#sub_category').attr('name', 'sub_category');
                    $('#customer_id').prop('disabled', false).trigger('change'); // Aktifkan select customer
                    
                    // Hapus 'name' dari hidden input
                    $('#sub_category_hidden').val('').removeAttr('name');
                    $('#customer_id_hidden').val('').removeAttr('name'); // <-- BARIS BARU
                }

                $('#sub_category').val(data.sub_category).trigger('change');
                $('#customer_id').val(data.customer_id).trigger('change');
                $('#no_srs').val(data.no_srs);
                $('#account').val(data.account);
                $('#cost_center').val(data.cost_center);
                $('#request_date').val(data.request_date);
                $('#objectives').val(data.objectives);
                $('#estimated_potential').val(data.estimated_potential);

                $('#requisition-form-details').show();

                $('#material-type-selection-container, #product-selection-container').toggle(data.sub_category === 'Packaging');
                $('#product-selection-container-fg').toggle(data.sub_category === 'Finished Goods' || data.sub_category === 'Special Order');

                const itemTbody = $('#requisition-items-tbody');
                itemTbody.empty();
                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A', id, type, masterId;
                        if (data.sub_category === 'Packaging' && item.item_detail) {
                            itemCode = item.item_detail.item_detail_code; itemName = item.item_detail.item_detail_name; unit = item.item_detail.unit;
                            id = item.item_detail.id; masterId = item.item_master_id; type = 'detail';
                        } else if (item.item_master) {
                            itemCode = item.item_master.item_master_code; itemName = item.item_master.item_master_name; unit = item.item_master.unit;
                            id = item.item_master.id; masterId = item.item_master.id; type = 'master';
                        }
                        if (itemCode !== 'N/A') {
                            const newRow = `<tr id="item-row-${type}-${id}" data-master-id="${masterId}"><td>${itemCode}</td><td>${itemName}</td><td>${unit}</td><td><input type="number" class="form-control" name="items[${id}][quantity_required]" value="${item.quantity_required}"></td><td><input type="number" class="form-control" name="items[${id}][quantity_issued]" value="${item.quantity_issued || ''}"></td></tr>`;
                            itemTbody.append(newRow);
                        }
                    });
                } else {
                    itemTbody.html('<tr id="no-items-row"><td colspan="5" class="text-center">No items found.</td></tr>');
                }

                if (data.sub_category === 'Special Order' && data.requisition_special) {
                    $('input[name="requested_date"]').val(data.requisition_special.requested_date);
                    $('input[name="weight_selection"]').val(data.requisition_special.weight_selection);
                    $('input[name="packaging_selection"]').val(data.requisition_special.packaging_selection);
                    $('input[name="sample_count"]').val(data.requisition_special.sample_count);
                    $('input[name="shipment_method"]').val(data.requisition_special.shipment_method);
                    $('input[name=coa_required][value="' + data.requisition_special.coa_required + '"]').prop('checked', true);
                }

                if (mode === 'qa_mode') {
                    $('.qa-fields-section').show();
                    $('.sm-field').prop('disabled', true);
                    $('#customer_id').prop('disabled', true).trigger('change');
                    $('.qa-field').prop('disabled', false);
                    $('#saveSampleBtn').text('Save QM Data & Complete').prop('disabled', false);

                } else {
                    const isSuperAdmin = "{{ auth()->user()->hasRole('super-admin') }}";
                    const userDept = "{{ Auth::user()->department?->name }}";

                    if (userDept !== 'QM & HSE' && !isSuperAdmin) {
                        $('.qa-fields-section').hide();
                    } else {
                        $('.qa-fields-section').show();
                    }

                    $('.sm-field').prop('disabled', false);
                    $('#customer_id').prop('disabled', false).trigger('change');
                    $('.qa-field').prop('disabled', true);
                    $('#saveSampleBtn').text('Save Changes').prop('disabled', false);
                }
            }

            function populateViewForm(data) {
                $('#view_sub_category').text(data.sub_category || '-');
                $('#view_customer_name').text(data.customer ? data.customer.name : '-');
                $('#view_customer_address').text(data.customer ? data.customer.address : '-');
                $('#view_no_srs').text(data.no_srs || '-');
                $('#view_account').text(data.account || '-');
                $('#view_request_date').text(new Date(data.request_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) || '-');
                $('#view_cost_center').text(data.cost_center || '-');
                $('#view_objectives').text(data.objectives || '-');
                $('#view_estimated_potential').text(data.estimated_potential || '-');

                const viewItemTbody = $('#view-items-tbody');
                viewItemTbody.empty();
                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';
                        if (item.item_detail) {
                            itemCode = item.item_detail.item_detail_code; itemName = item.item_detail.item_detail_name; unit = item.item_detail.unit;
                        } else if (item.item_master) {
                            itemCode = item.item_master.item_master_code; itemName = item.item_master.item_master_name; unit = item.item_master.unit;
                        }
                        const newRow = `<tr><td>${itemCode}</td><td>${itemName}</td><td>${unit}</td><td class="text-center">${item.quantity_required}</td><td class="text-center">${item.quantity_issued || '-'}</td></tr>`;
                        viewItemTbody.append(newRow);
                    });
                } else {
                    viewItemTbody.html('<tr><td colspan="5" class="text-center">No items have been added.</td></tr>');
                }

                const specialOrderSection = $('#view-special-order-section');
                if (data.sub_category === 'Special Order' && data.requisition_special) {
                    const special = data.requisition_special;
                    $('#view_requested_date').text(special.requested_date ? new Date(special.requested_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');
                    $('#view_weight_selection').text(special.weight_selection || '-');
                    $('#view_packaging_selection').text(special.packaging_selection || '-');
                    $('#view_sample_count').text(special.sample_count || '-');
                    $('#view_shipment_method').text(special.shipment_method || '-');
                    $('#view_coa_required').text(special.coa_required == 1 ? 'Yes' : 'No');
                    specialOrderSection.show();
                } else {
                    specialOrderSection.hide();
                }

                const status = data.status;
                let badgeClass = 'bg-secondary';
                if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-primary';
                else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                else if (['Rejected', 'Cancelled'].includes(status)) badgeClass = 'bg-danger';
                else if (status === 'In Progress') badgeClass = 'bg-warning text-dark';

                let badgeHtml = `<span class="badge fs-6 rounded-pill ${badgeClass}">${status}</span>`;

                if (status === 'Rejected' && data.requester && data.requester.email) {
                    const rejectedLog = data.tracking_history.find(h => h.status.toLowerCase() === 'rejected');
                    const rejectionNotes = rejectedLog ? rejectedLog.notes : 'No reason provided.';

                    const subject = `Follow-up on Rejected Requisition: ${data.no_srs}`;
                    const body = `Hi ${data.requester.name},\n\nThis is a follow-up regarding the rejection of sample requisition ${data.no_srs}.\n\nReason for rejection: ${rejectionNotes}\n\nPlease review and advise on the next steps.\n\nThanks,`;

                    const mailtoLink = `mailto:${data.requester.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

                    badgeHtml = `<a href="${mailtoLink}" target="_blank" class="badge fs-6 rounded-pill ${badgeClass}" title="Click to send follow-up email">${status} <i class="ph-bold ph-envelope-simple ms-1"></i></a>`;
                }
                $('#view_status_badge').html(badgeHtml);

                $('.tracker-step').removeClass('completed active rejected');
                $('.tracker-details').html('');
                $('#tracker-progress').css('width', '0%');

                const history = data.tracking_history || [];
                if (history.length === 0) return;

                let lastCompletedStep = -1;
                let isRejected = false;

                history.forEach(item => {
                    if (isRejected) return;
                    let stepIndex = -1;
                    const statusLower = item.status.toLowerCase();

                    if (statusLower.includes('created')) { stepIndex = 0; }
                    else if (statusLower.includes('approved')) { stepIndex = lastCompletedStep + 1; }

                    if (stepIndex > -1) {
                        const stepEl = $('.tracker-step').eq(stepIndex);
                        stepEl.addClass('completed');
                        stepEl.find('.tracker-details').html(`<div class="tracker-user text-primary">${item.user}</div><div class="tracker-date text-dark">${item.date}</div>`);
                        lastCompletedStep = Math.max(lastCompletedStep, stepIndex);
                    }
                    else if (statusLower.includes('sent for approval')) {
                        const nextStepIndex = lastCompletedStep + 1;
                        if (nextStepIndex < 6) {
                             $('.tracker-step').eq(nextStepIndex).addClass('active')
                                .find('.tracker-details').html(`<div class="tracker-user text-warning fst-italic">Waiting for...</div><div class="tracker-date">${item.user}</div>`);
                        }
                    }
                    else if (statusLower.includes('rejected')) {
                        const rejectedStepIndex = lastCompletedStep + 1;
                         if (rejectedStepIndex < 6) {
                            const stepEl = $('.tracker-step').eq(rejectedStepIndex);
                            stepEl.removeClass('active').addClass('rejected');
                            stepEl.find('.tracker-details').html(`<div class="tracker-user text-danger">${item.user}</div><div class="tracker-date">${item.date}</div>`);
                         }
                         isRejected = true;
                    }
                });

                if (lastCompletedStep >= 0 && !isRejected) {
                    let progressPercentage = (lastCompletedStep / 5) * 100;
                    $('#tracker-progress').css('width', progressPercentage + '%');
                }

                if (data.status === 'Completed') {
                    $('.tracker-step').removeClass('active').addClass('completed');
                    $('#tracker-progress').css('width', '100%');
                }
            }

            $(document).on('click', '.btn-view-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/sample-form/${id}`,
                    type: 'GET',
                    success: function(response) {
                        populateViewForm(response);
                        $('#viewModal').modal('show');
                    },
                    error: function() {
                        errorMessage('Failed to fetch requisition details.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.btn-edit-requisition', function () {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/sample-form/${id}/edit`,
                    type: 'GET',
                    success: function (response) {
                        // resetForm(); // <<< INI BARIS YANG SALAH DAN SUDAH DIHAPUS
                        $('#sampleModalLabel').text('Edit Sample Requisition');
                        $('#sampleForm').attr('data-id', id);
                        populateForm(response); // Langsung isi form dengan data
                        $('#sampleModal').modal('show');
                    },
                    error: function () {
                        errorMessage('Failed to fetch data for editing.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.btn-delete-requisition', function () {
                const requisitionId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sample-form/${requisitionId}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message,
                                        'success');
                                    table.ajax.reload();
                                }
                            },
                            error: function (xhr) {
                                Swal.fire('Failed!', 'A system error occurred.',
                                    'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-qa-form', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/sample-form/${id}/edit`,
                    type: 'GET',
                    success: function(response) {
                        // resetForm(); // <<< INI JUGA BARIS YANG SALAH DAN SUDAH DIHAPUS
                        $('#sampleModalLabel').text('Complete QM & HSE Form');
                        $('#sampleForm').attr('data-id', id).attr('data-mode', 'edit');

                        populateForm(response, 'qa_mode'); // Langsung isi form dengan data

                        $('#sampleModal').modal('show');
                    },
                    error: function() {
                        errorMessage('Failed to fetch data for QM form.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            $('#sampleModal').on('hidden.bs.modal', function () {
                resetForm();
                $('#sampleForm').removeAttr('data-mode data-id');
            });
        });

    </script>
    @endpush
</x-app-layout>
