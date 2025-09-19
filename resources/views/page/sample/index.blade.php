<x-app-layout>
    @section('title')
    Sample Requisition
    @endsection

    @push('css')
    <link href="{{ asset('assets/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .modal-xl {
            max-width: 80vw !important;
        }
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
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

    <div class="modal fade" id="sampleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="Logo" style="height: 24px;"
                            class="me-3">
                        <h5 class="modal-title text-white mb-0" id="sampleModalLabel">Buat Sample Requisition</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="sampleForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- BAGIAN ATAS: PEMILIHAN KATEGORI --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="sub_category" class="form-label fw-bold">1. Pilih Sub Kategori<i
                                        class="text-danger">*</i></label>
                                <select class="form-select select2-styled" id="sub_category" name="sub_category"
                                    style="width: 100%;">
                                    <option></option>
                                    @foreach ($allowedSubCategories as $subCat)
                                    <option value="{{ $subCat }}">{{ $subCat }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="sub_category_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <input type="text" class="form-control" value="SAMPLE" readonly>
                            </div>
                        </div>

                        {{-- CONTAINER UTAMA: Muncul setelah sub category dipilih --}}
                        <div id="requisition-form-details" style="display: none;">
                            <hr>
                            {{-- BAGIAN REQUISITION DETAILS --}}
                            <h5 class="fw-bold text-primary mb-3">Detail Requisition</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Nama Customer<i
                                            class="text-danger">*</i></label>
                                    <select class="form-select select2-styled" id="customer_id" name="customer_id"
                                        style="width: 100%;">
                                        <option></option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">
                                            {{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="customer_id_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="customer_address" rows="2"
                                        readonly></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="no_srs" class="form-label">No. SRS<i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" id="no_srs" name="no_srs"
                                        value="{{ $generatedSrs }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="account" class="form-label">Akun<i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" id="account" name="account"
                                        value="{{ $userAccount }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="request_date" class="form-label">Tanggal Permintaan<i
                                            class="text-danger">*</i></label>
                                    <input type="date" class="form-control" id="request_date" name="request_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="cost_center" class="form-label">Cost Center</label>
                                    <input type="text" class="form-control" id="cost_center" name="cost_center">
                                </div>
                                <div class="col-md-6">
                                    <label for="objectives" class="form-label">Tujuan<i
                                            class="text-danger">*</i></label>
                                    <textarea class="form-control" id="objectives" name="objectives"
                                        rows="2"></textarea>
                                    <div class="invalid-feedback" id="objectives_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="estimated_potential" class="form-label">Estimasi Potensi<i
                                            class="text-danger">*</i></label>
                                    <textarea class="form-control" id="estimated_potential"
                                        name="estimated_potential" rows="2"></textarea>
                                    <div class="invalid-feedback" id="estimated_potential_error"></div>
                                </div>
                            </div>

                            <hr class="mt-4">

                            {{-- BAGIAN PRODUCT DETAILS --}}
                            <h5 class="fw-bold text-primary mb-3">Detail Produk</h5>

                            {{-- HANYA UNTUK PACKAGING --}}
                            <div class="mb-3" id="material-type-selection-container" style="display:none;">
                                <label class="form-label fw-bold">2. Pilih Tipe Material<i
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

                            {{-- HANYA UNTUK PACKAGING --}}
                            <div class="mb-3" id="product-selection-container" style="display:none;">
                                <label for="product_select" class="form-label fw-bold">3. Pilih Nama Produk<i
                                        class="text-danger">*</i></label>
                                <select class="form-select select2-styled" id="product_select"
                                    multiple="multiple" style="width: 100%;" disabled></select>
                                <div id="product_select_note" class="form-text text-warning fw-semibold fst-italic mt-1">
                                    <i class="ph ph-arrow-fat-up me-1"></i>
                                    Silakan pilih Tipe Material di atas untuk mengaktifkan.
                                </div>
                                <button type="button" class="btn btn-success btn-sm mt-2"
                                    id="btn-add-items-detail">
                                    <i class="ph-bold ph-plus"></i> Tambah Item ke Daftar
                                </button>
                            </div>

                            {{-- UNTUK FINISHED GOOD & SPECIAL ORDER --}}
                            <div class="mb-3" id="product-selection-container-fg" style="display:none;">
                                <label for="product_select_fg" class="form-label fw-bold">2. Pilih Nama Produk<i class="text-danger">*</i></label>
                                <select class="form-select select2-styled" id="product_select_fg"
                                    multiple="multiple" style="width: 100%;"></select>
                                <button type="button" class="btn btn-success btn-sm mt-2"
                                    id="btn-add-items-master">
                                    <i class="ph-bold ph-plus"></i> Tambah Item ke Daftar
                                </button>
                            </div>

                            <div class="alert alert-danger" id="items_error" style="display: none;"></div>

                            <div class="table-responsive mt-4">
                                <h6 class="fw-bold">3. Daftar Item yang Diminta</h6>
                                <table class="table table-bordered w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kode Item</th>
                                            <th>Nama Item</th>
                                            <th>Unit</th>
                                            <th style="width: 15%;">Qty Diminta</th>
                                            <th style="width: 15%;">Qty Diberikan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="requisition-items-tbody">
                                        <tr id="no-items-row">
                                            <td colspan="5" class="text-center">Belum ada item yang ditambahkan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- BAGIAN SPECIAL ORDER --}}
                            <div id="special-order-fields" style="display: none;">
                                <hr class="mt-4">
                                <h5 class="text-primary fw-bold">Special Order Details</h5>

                                {{-- Bagian Marketing (selalu tampil jika Special Order dipilih) --}}
                                <h6 class="text-danger fw-bold">
                                    <center><i>Diisi oleh Marketing</i></center>
                                </h6>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-4">
                                        <label for="requested_date" class="form-label">Tgl. Selesai Sample<i
                                                class="text-danger">*</i></label>
                                        <input type="date" class="form-control" name="requested_date">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Berat Sample<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="weight_selection"
                                            placeholder="Contoh: 25 Kg, 15 Kg, Lainnya: 10 Pcs">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kemasan Sample<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="packaging_selection"
                                            placeholder="Contoh: Karton, Lainnya: Plastik Wrap">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="sample_count" class="form-label">Jumlah Sample<i
                                                class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="sample_count"
                                            placeholder="Contoh: 2x1kg (setiap produk)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Certificate of Analysis<i
                                                class="text-danger">*</i></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="coa_required"
                                                    id="coa_yes" value="1">
                                                <label class="form-check-label" for="coa_yes">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="coa_required"
                                                    id="coa_no" value="0">
                                                <label class="form-check-label" for="coa_no">Tidak</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Dikirim Melalui<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="shipment_method"
                                            placeholder="Contoh: Delivery (DHL), Lainnya: Gojek">
                                    </div>
                                </div>

                                @if (isset($userDepartmentName) && $userDepartmentName == 'QA/QM')
                                <div class="qa-fields-section mt-4">
                                    <hr>
                                    <h6 class="text-danger fw-bold">
                                        <center><i>Diisi oleh QA/QM</i></center>
                                    </h6>
                                    <div class="row g-3 mt-1">
                                        <div class="col-12">
                                            <label class="form-label">Asal Sample<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_origin">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Keterangan Sample: Batch/Pallet No<i
                                                    class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_description_batch">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">WB/DEO No<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_description_wb">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tank No<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_description_tank">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tgl Produksi<i class="text-danger">*</i></label>
                                            <input type="date" class="form-control" name="production_date">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Persiapan Sample<i
                                                    class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_preparation">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Keterangan</label>
                                            <input type="text" class="form-control" name="qa_notes">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" id="saveSampleBtn">Simpan</button>
                        <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Show Detail -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="Logo" style="height: 24px;"
                            class="me-3">
                        <h5 class="modal-title text-white mb-0" id="viewModalLabel">Detail Sample Requisition</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        {{-- KOLOM KIRI: DETAIL FORM --}}
                        <div class="col-lg-8 border-end pe-4">

                            {{-- Bagian Informasi Utama --}}
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary d-flex align-items-center mb-3">
                                    <i class="ph-bold ph-identification-card me-2"></i>
                                    Informasi Utama
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted">No. SRS</small>
                                        <p class="fw-bold fs-6 mb-0" id="view_no_srs">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Sub Kategori</small>
                                        <p class="fs-6 mb-0" id="view_sub_category">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Tanggal Permintaan</small>
                                        <p class="fs-6 mb-0" id="view_request_date">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Status Saat Ini</small>
                                        <div id="view_status_badge"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bagian Informasi Customer --}}
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary d-flex align-items-center mb-3">
                                    <i class="ph-bold ph-user-circle me-2"></i>
                                    Informasi Customer
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted">Nama Customer</small>
                                        <p class="fs-6 mb-0" id="view_customer_name">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Akun</small>
                                        <p class="fs-6 mb-0" id="view_account">-</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bagian Tujuan & Potensi --}}
                            <div class="mb-4">
                                <h5 class="fw-bold text-primary d-flex align-items-center mb-3">
                                    <i class="ph-bold ph-shooting-star me-2"></i>
                                    Tujuan & Estimasi Potensi
                                </h5>
                                <div class="p-3 bg-light rounded">
                                    <p class="fw-bold mb-1">Tujuan:</p>
                                    <p class="text-dark fst-italic" id="view_objectives">-</p>
                                    <hr class="my-2">
                                    <p class="fw-bold mb-1">Estimasi Potensi:</p>
                                    <p class="text-dark fst-italic" id="view_estimated_potential">-</p>
                                </div>
                            </div>

                            <hr>

                            <h5 class="fw-bold text-primary mb-3 mt-4 d-flex align-items-center">
                                <i class="ph-bold ph-list-bullets me-2"></i>
                                Daftar Item yang Diminta
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kode Item</th>
                                            <th>Nama Item</th>
                                            <th>Unit</th>
                                            <th>Qty Diminta</th>
                                            <th>Qty Diberikan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-items-tbody">
                                        {{-- Data item akan diisi oleh JavaScript --}}
                                    </tbody>
                                </table>
                            </div>

                            <hr>
                            
                            <div id="view-special-order-section" class="mb-4" style="display: none;">
                                <h5 class="fw-bold text-primary d-flex align-items-center mb-3">
                                    <i class="ph-bold ph-star-four me-2"></i>
                                    Detail Special Order (Marketing)
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">Tgl. Selesai Sample</small>
                                        <p class="fs-6 mb-0" id="view_requested_date">-</p>
                                    </div>
                                    <div class="col-md-8">
                                        <small class="text-muted">Berat Sample</small>
                                        <p class="fs-6 mb-0" id="view_weight_selection">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Kemasan Sample</small>
                                        <p class="fs-6 mb-0" id="view_packaging_selection">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Jumlah Sample</small>
                                        <p class="fs-6 mb-0" id="view_sample_count">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">COA Diperlukan?</small>
                                        <p class="fs-6 mb-0" id="view_coa_required">-</p>
                                    </div>
                                    <div class="col-md-12">
                                        <small class="text-muted">Dikirim Melalui</small>
                                        <p class="fs-6 mb-0" id="view_shipment_method">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: TRACKING HISTORY --}}
                        <div class="col-lg-4 ps-4">
                            <h5 class="fw-bold text-primary mb-3">Jejak Status (Tracking)</h5>
                            <div id="tracking-history-container">
                                {{-- Data tracking akan diisi oleh JavaScript --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function successMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function errorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: message
            });
        }

        function warningMessage(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: message
            });
        }

        $(document).ready(function () {
            const userDepartment = "{{ $userDepartmentName ?? '' }}";

            function initSelect2() {
                function formatSubCategory(option) {
                    if (!option.id) return '<span class="text-muted">Pilih Sub Category</span>';
                    let icon = '';
                    if (option.text.includes('Packaging')) icon = '<i class="ph ph-package me-2 text-primary"></i>';
                    if (option.text.includes('Finished')) icon = '<i class="ph ph-cube me-2 text-success"></i>';
                    if (option.text.includes('Special')) icon = '<i class="ph ph-star-four me-2 text-warning"></i>';
                    return `<span style='font-weight:500;'>${icon}${option.text}</span>`;
                }

                $('#sub_category').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Pilih Sub Category',
                    allowClear: true,
                    templateResult: formatSubCategory,
                    templateSelection: formatSubCategory,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                function formatCustomer(option) {
                    if (!option.id) return '<span class="text-muted">Pilih Customer</span>';
                    return `<i class='ph ph-user-circle me-2 text-info'></i> <span style='font-weight:500;'>${option.text}</span>`;
                }
                $('#customer_id').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Pilih Customer',
                    allowClear: true,
                    templateResult: formatCustomer,
                    templateSelection: formatCustomer,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                $('#product_select').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Pilih produk',
                    allowClear: true
                });

                $('#product_select_fg').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Pilih produk Finished Good',
                    allowClear: true
                });
            }
            initSelect2();

            const table = $('#sample-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sample.data') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '20px',
                        className: 'text-center'
                    },
                    { data: 'requester_info', name: 'users.name' },
                    { data: 'customer_name', name: 'customers.name' },
                    { data: 'request_date', name: 'requisitions.request_date' },
                    { data: 'sub_category', name: 'requisitions.sub_category' },
                    { data: 'route_to', name: 'requisitions.route_to' },
                    { data: 'status', name: 'requisitions.status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
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
                $('#requisition-items-tbody').html('<tr id="no-items-row"><td colspan="5" class="text-center">Belum ada item yang ditambahkan.</td></tr>');
                $('#requisition-form-details').hide();
                clearValidationErrors();
            }

            $('#btn-create-sample').on('click', function () {
                resetForm();
                $('#sampleModalLabel').text('Buat Sample Requisition');
                $('#sampleForm').attr('data-mode', 'create').removeAttr('data-id');
            });

            $('#sub_category').on('change', function () {
                const isEditMode = $('#sampleForm').attr('data-mode') === 'edit';
                if (isEditMode) return;

                const selectedSubCategory = $(this).val();

                $('#special-order-fields, #material-type-selection-container, #product-selection-container, #product-selection-container-fg').hide();
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
                } else if (selectedSubCategory === 'Finished Good' || selectedSubCategory === 'Special Order') {
                    $.ajax({
                        url: "{{ route('sample.getAllItemMasters') }}",
                        method: 'GET',
                        success: function (masters) {
                            const productSelectFg = $('#product_select_fg');
                            productSelectFg.empty();
                            masters.forEach(m => productSelectFg.append(new Option(`[${m.item_master_code}] ${m.item_master_name}`, m.id)));
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
                $('#requisition-items-tbody').html('<tr id="no-items-row"><td colspan="5" class="text-center">Belum ada item yang ditambahkan.</td></tr>');

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
                        data: { _token: "{{ csrf_token() }}", material_types: selectedTypes },
                        success: function (products) {
                            productSelect.empty();
                            products.forEach(p => productSelect.append(new Option(p.item_master_name, p.id)));
                            productSelect.trigger('change');
                        },
                        error: function () { errorMessage('Gagal memuat produk.'); }
                    });
                } else {
                    productSelect.prop('disabled', true).empty().trigger('change');
                     productSelectNote.show();
                }
            });

            $('#btn-add-items-detail').on('click', function () {
                const selectedProductIds = $('#product_select').val();
                if (!selectedProductIds || selectedProductIds.length === 0) {
                    warningMessage('Silakan pilih produk terlebih dahulu.');
                    return;
                }
                $.ajax({
                    url: "{{ route('sample.getItemDetailsByProducts') }}",
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", product_ids: selectedProductIds },
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
                    warningMessage('Silakan pilih produk terlebih dahulu.');
                    return;
                }
                $.ajax({
                    url: "{{ route('sample.getAllItemMasters') }}",
                    method: 'GET',
                    success: function (allMasters) {
                        const selectedMasters = allMasters.filter(m => selectedMasterIds.includes(String(m.id)));
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
                    $('#requisition-items-tbody').html('<tr id="no-items-row"><td colspan="5" class="text-center">Belum ada item yang ditambahkan.</td></tr>');
                }
            });

            $('#sampleForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors();
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
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const key in errors) {
                                const errorMsg = errors[key][0];
                                if (key.startsWith('items.')) {
                                    $('#items_error').show().text('Pastikan jumlah item diisi.');
                                } else {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}_error`).text(errorMsg);
                                }
                            }
                            errorMessage('Pastikan data yang anda masukkan sudah benar.');
                        } else {
                            errorMessage(xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                        }
                    }
                });
            });

            function populateForm(data) {
                $('#sampleForm').attr('data-mode', 'edit'); // Kunci untuk mencegah reset

                // 1. Isi semua field umum
                $('#sub_category').val(data.sub_category).trigger('change');
                $('#customer_id').val(data.customer_id).trigger('change');
                $('#no_srs').val(data.no_srs);
                $('#account').val(data.account);
                $('#cost_center').val(data.cost_center);
                $('#request_date').val(data.request_date);
                $('#objectives').val(data.objectives);
                $('#estimated_potential').val(data.estimated_potential);
                $('#requisition-form-details').show();

                // 2. Tampilkan/Sembunyikan section yang relevan
                $('#special-order-fields').toggle(data.sub_category === 'Special Order');
                $('#material-type-selection-container, #product-selection-container').toggle(data.sub_category === 'Packaging');
                $('#product-selection-container-fg').toggle(data.sub_category === 'Finished Good' || data.sub_category === 'Special Order');

                // 3. Proses Product Details (TIDAK ADA AJAX LAGI DI SINI)
                if (data.sub_category === 'Packaging') {
                    $('input.material-type-checkbox').prop('checked', false);
                    if (data.attached_material_types.length > 0) {
                        data.attached_material_types.forEach(type => {
                            $(`input.material-type-checkbox[value="${type}"]`).prop('checked', true);
                        });
                    }

                    const productSelect = $('#product_select');
                    productSelect.empty();
                    data.product_options.forEach(opt => productSelect.append(new Option(opt.text, opt.id)));
                    productSelect.val(data.selected_master_ids).trigger('change.select2');
                    productSelect.prop('disabled', false);
                    $('#product_select_note').hide();

                } else if (data.sub_category === 'Finished Good' || data.sub_category === 'Special Order') {
                    const productSelectFg = $('#product_select_fg');
                    productSelectFg.empty();
                    data.product_options.forEach(opt => productSelectFg.append(new Option(opt.text, opt.id)));
                    productSelectFg.val(data.selected_master_ids).trigger('change.select2');
                }

                if (data.sub_category === 'Special Order' && data.requisition_special) {
                    $('input[name="requested_date"]').val(data.requisition_special.requested_date);
                    $('input[name="weight_selection"]').val(data.requisition_special.weight_selection);
                    $('input[name="packaging_selection"]').val(data.requisition_special.packaging_selection);
                    $('input[name="sample_count"]').val(data.requisition_special.sample_count);
                    $('input[name="shipment_method"]').val(data.requisition_special.shipment_method);
                    $('input[name=coa_required][value="' + data.requisition_special.coa_required + '"]').prop('checked', true);
                }

                const itemTbody = $('#requisition-items-tbody');
                itemTbody.empty();
                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A', id, type, masterId;
                        if (data.sub_category === 'Packaging' && item.item_detail) {
                            itemCode = item.item_detail.item_detail_code;
                            itemName = item.item_detail.item_detail_name;
                            unit = item.item_detail.unit;
                            id = item.item_detail.id;
                            masterId = item.item_master_id;
                            type = 'detail';
                        } else if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                            id = item.item_master.id;
                            masterId = item.item_master.id;
                            type = 'master';
                        }
                        if (itemCode !== 'N/A') {
                            const newRow = `
                                <tr id="item-row-${type}-${id}" data-master-id="${masterId}">
                                    <td>${itemCode}</td><td>${itemName}</td><td>${unit}</td>
                                    <td><input type="number" class="form-control" name="items[${id}][quantity_required]" value="${item.quantity_required}"></td>
                                    <td><input type="number" class="form-control" name="items[${id}][quantity_issued]" value="${item.quantity_issued || ''}"></td>
                                </tr>`;
                            itemTbody.append(newRow);
                        }
                    });
                }
                if (itemTbody.is(':empty')) {
                    itemTbody.html('<tr id="no-items-row"><td colspan="5" class="text-center">Belum ada item.</td></tr>');
                }

                if (data.status === 'Pending' || data.status === 'Draft') {
                    $('#sampleForm').find('input, select, textarea').not('[readonly]').prop('disabled', false);
                    if ($('#product_select').is(':visible') && (!data.attached_material_types || data.attached_material_types.length === 0)) {
                        $('#product_select').prop('disabled', true);
                    }
                    if (userDepartment !== 'QA/QM') {
                        $('.qa-fields-section').find('input, select, textarea').prop('disabled', true);
                    }
                    $('#saveSampleBtn').text('Simpan Perubahan').show();
                }
            }

            function populateViewForm(data) {
                // Mengisi data utama
                $('#view_no_srs').text(data.no_srs || '-');
                $('#view_sub_category').text(data.sub_category || '-');
                $('#view_request_date').text(new Date(data.request_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric'}));
                $('#view_customer_name').text(data.customer ? data.customer.name : 'N/A');
                $('#view_account').text(data.account || '-');
                $('#view_objectives').text(data.objectives || '-');
                $('#view_estimated_potential').text(data.estimated_potential || '-');

                const specialOrderSection = $('#view-special-order-section');
                    if (data.sub_category === 'Special Order' && data.requisition_special) {
                        const specialData = data.requisition_special;
                        const requestedDate = specialData.requested_date
                            ? new Date(specialData.requested_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric'})
                            : '-';

                        $('#view_requested_date').text(requestedDate);
                        $('#view_weight_selection').text(specialData.weight_selection || '-');
                        $('#view_packaging_selection').text(specialData.packaging_selection || '-');
                        $('#view_sample_count').text(specialData.sample_count || '-');
                        $('#view_shipment_method').text(specialData.shipment_method || '-');
                        $('#view_coa_required').text(specialData.coa_required == 1 ? 'Ya' : 'Tidak');

                        specialOrderSection.slideDown(); // Tampilkan section dengan animasi
                    } else {
                        specialOrderSection.slideUp(); // Sembunyikan jika bukan Special Order
                    }

                // --- Logika untuk membuat badge status ---
                const status = data.status;
                let badgeClass = 'bg-secondary';
                if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-primary';
                else if (['Approved', 'Completed'].includes(status)) badgeClass = 'bg-success';
                else if (['Rejected', 'Cancelled'].includes(status)) badgeClass = 'bg-danger';
                else if (status === 'In Progress') badgeClass = 'bg-info text-dark';
                $('#view_status_badge').html(`<span class="badge fs-6 ${badgeClass}">${status}</span>`);
                // --- Akhir logika badge ---

                // Mengisi tabel item (Tidak ada perubahan di sini)
                const viewItemTbody = $('#view-items-tbody');
                viewItemTbody.empty();
                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';
                        if (data.sub_category === 'Packaging' && item.item_detail) {
                            itemCode = item.item_detail.item_detail_code;
                            itemName = item.item_detail.item_detail_name;
                            unit = item.item_detail.unit;
                        } else if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                        }
                        const newRow = `
                            <tr>
                                <td>${itemCode}</td>
                                <td>${itemName}</td>
                                <td>${unit}</td>
                                <td>${item.quantity_required}</td>
                                <td>${item.quantity_issued || '-'}</td>
                            </tr>`;
                        viewItemTbody.append(newRow);
                    });
                } else {
                    viewItemTbody.html('<tr><td colspan="5" class="text-center">Belum ada item yang ditambahkan.</td></tr>');
                }

                // Mengisi histori tracking (Tidak ada perubahan di sini)
                const trackingContainer = $('#tracking-history-container');
                trackingContainer.empty();
                if (data.tracking_history && data.tracking_history.length > 0) {
                    let timelineHtml = '<ul class="list-unstyled d-flex flex-column gap-3">';
                    data.tracking_history.forEach(item => {
                        timelineHtml += `
                            <li class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="d-flex align-items-center justify-content-center text-white rounded-circle ${item.color}" style="width: 40px; height: 40px;">
                                        <i class="${item.icon}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">${item.status}</h6>
                                    <small class="text-muted fst-italic">${item.user} - ${item.date}</small>
                                    <p class="mb-0 small">${item.notes}</p>
                                </div>
                            </li>`;
                    });
                    timelineHtml += '</ul>';
                    trackingContainer.html(timelineHtml);
                } else {
                    trackingContainer.html('<p class="text-muted">Tidak ada histori tracking.</p>');
                }
            }

            $(document).on('click', '.btn-view-requisition', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/sample-form/${id}`, // Ini akan memanggil method show()
                    type: 'GET',
                    success: function (response) {
                        populateViewForm(response);
                        $('#viewModal').modal('show');
                    },
                    error: function () {
                        errorMessage('Gagal mengambil data detail permintaan.');
                    }
                });
            });

            $(document).on('click', '.btn-edit-requisition', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/sample-form/${id}/edit`,
                    type: 'GET',
                    success: function (response) {
                        resetForm();
                        $('#sampleModalLabel').text('Ubah Sample Requisition');
                        $('#sampleForm').attr('data-id', id);
                        populateForm(response);
                        $('#sampleModal').modal('show');
                    },
                    error: function () { errorMessage('Gagal mengambil data untuk diubah.'); }
                });
            });

            $(document).on('click', '.btn-delete-requisition', function () {
                const requisitionId = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sample-form/${requisitionId}`,
                            type: 'POST',
                            data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Terhapus!', response.message, 'success');
                                    table.ajax.reload();
                                }
                            },
                            error: function (xhr) { Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); }
                        });
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
