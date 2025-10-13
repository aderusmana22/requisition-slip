<x-app-layout>
    @section('title')
    Free Goods Requisition
    @endsection

    {{-- Re-use styles dari Sample --}}
    @include('components.sample-table-styles') 

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        
        <style>
            .new-freegoods-btn {
                /* WARNA UTAMA SAMPLE */
                background-color: #cc982f; 
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 8px;
                font-weight: 600;
                transition: background-color 0.3s ease;
            }
            .new-freegoods-btn:hover {
                /* WARNA SEKUNDER SAMPLE */
                background-color: #b8871a;
                color: white;
            }
            /* Ganti warna badge info menjadi warna yang lebih sesuai dengan palette coklat/emas */
            .text-info {
                color: #b8871a !important; 
            }
        </style>
    @endpush

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Free Goods Requisition List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-note-pencil f-s-16"></i> Forms
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Free Goods Requisition</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>

                <div>
                    <button class="btn new-freegoods-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#fgModal" id="btn-create-fg">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Free Goods</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list"></i>
                        Free Goods Requisition List
                    </h4>
                    <p class="table-subtitle">View, manage and track all free goods requisition submissions</p>
                </div>

                <div class="table-responsive">
                    {{-- Ganti ID table dari sampleTable ke fgTable --}}
                    <table class="w-100 display" id="fgTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Requester</th>
                                <th>Customer</th>
                                <th>Request Date</th>
                                <th>Category</th>
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

    <div class="modal fade" id="fgModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="loading-overlay" style="display: none;">
                    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mt-3 fw-bold">Processing...</h5>
                </div>
                {{-- Ganti warna header modal ke warna Sample --}}
                <div class="modal-header" style="background-color: #cc982f;"> 
                    <h5 class="modal-title text-white" id="fgModalLabel">Create New Free Goods Requisition</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                {{-- Ganti ID form dari sampleForm ke fgForm --}}
                <form id="fgForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            {{-- HILANGKAN: 1. Select Sub Category --}}
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-bold">1. Category</label>
                                <input type="text" class="form-control" value="FREE GOODS (General Request)" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Requester Department</label>
                                <input type="text" class="form-control" value="{{ $userDepartmentName }}" readonly>
                            </div>
                        </div>

                        {{-- Form Details, selalu ditampilkan --}}
                        <div id="requisition-form-details">
                            <div id="main-requisition-data">
                                <hr>
                                {{-- Ganti warna text-info menjadi text-warning (atau warna yang merepresentasikan coklat) --}}
                                <h5 class="fw-bold text-warning mb-3">Requisition Details</h5> 
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label">Customer Name<i
                                                class="text-danger">*</i></label>
                                        {{-- Ganti ID select2 agar tidak tabrakan, tapi karena select2 id sama, biarkan saja --}}
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
                                        <label for="customer_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="customer_address" rows="2" readonly></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="no_srs" class="form-label">FG No.<i class="text-danger">*</i></label>
                                        {{-- Ganti ID no_srs ke no_fg --}}
                                        <input type="text" class="form-control" id="no_fg" name="no_fg"
                                            value="{{ $generatedFg }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="account" class="form-label">Account<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="account" name="account"
                                            value="5300" readonly> {{-- Default Account 5300 --}}
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
                                        placeholder="e.g: CC1001, CC2002">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="objectives" class="form-label">Objectives<i
                                                class="text-danger">*</i></label>
                                        <textarea class="form-control" id="objectives" name="objectives"
                                            placeholder="e.g: Promotional Items, Internal Use, etc."
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

                                <h5 class="fw-bold text-warning mb-2">Product Details</h5>
                                
                                {{-- [BARU] Product Selection untuk Free Goods --}}
                                <div class="mb-3" id="product-selection-container-fg">
                                    <label for="product_select_fg" class="form-label fw-bold">2. Select Product Name<i
                                            class="text-danger">*</i></label>
                                    {{-- Re-use ID product_select_fg dari Sample --}}
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
                                        <thead class="thead-dark">
                                            <tr>
                                                {{-- Hilangkan Material Type Column --}}
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Unit</th>
                                                <th style="width: 15%;">Qty Required</th>
                                                <th style="width: 15%;">Qty Issued</th>
                                            </tr>
                                        </thead>
                                        {{-- Ganti ID tbody dari sample ke fg --}}
                                        <tbody id="requisition-items-tbody-fg"> 
                                            <tr id="no-items-row">
                                                <td colspan="5" class="text-center">No items have been added yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- HILANGKAN: special-order-fields & qa-fields-section --}}

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="submit" id="saveFgBtn">Save</button>
                        <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Modal (MODIFIED) --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                {{-- Ganti warna header modal ke warna Sample --}}
                <div class="modal-header" style="background-color: #cc982f;">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Free Goods Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">

                    {{-- CARD 1: MAIN REQUISITION DETAILS --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            {{-- Ganti warna text-info menjadi text-warning --}}
                            <h5 class="fw-bold text-warning mb-3"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5> 
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <small class="view-label">Category</small>
                                    <p class="view-data">FREE GOODS</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Sub Category</small>
                                    <p class="view-data" id="view_sub_category">General Request</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">FG No.</small>
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

                                <div class="col-md-6">
                                    <small class="view-label">Objectives</small>
                                    <p class="view-data fst-italic fw-normal" id="view_objectives">-</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Estimated Potential</small>
                                    <p class="view-data fst-italic fw-normal" id="view_estimated_potential">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: REQUESTED ITEM LIST --}}
                    <div class="card view-modal-card">
                         <div class="card-header">
                            {{-- Ganti warna text-info menjadi text-warning --}}
                            <h5 class="fw-bold text-warning mb-3"><i class="ph-bold  ph-list me-2"></i>Requested Item List</h5> 
                        </div>
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100 mb-1">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Unit</th>
                                            <th class="text-center">Qty Required</th>
                                            <th class="text-center">Qty Issued</th>
                                        </tr>
                                    </thead>
                                    {{-- Ganti ID tbody dari view-items-tbody ke view-items-tbody-fg --}}
                                    <tbody id="view-items-tbody-fg"></tbody> 
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- HILANGKAN: CARD 3: SPECIAL ORDER DETAILS --}}

                    {{-- CARD 4: APPROVAL TRACKING (MOVED TO BOTTOM) --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            {{-- Ganti warna text-info menjadi text-warning --}}
                            <h5 class="fw-bold text-warning mb-3"><i class="ph-bold ph-path me-2"></i> Approval & Process Tracking</h5> 
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold me-3">Current Status:</span>
                                <div id="view_status_badge"></div>
                            </div>
                            <div class="tracker-container" id="approval-tracker-container-fg">
                                {{-- Tracker steps akan diisi oleh JS, sesuai alur Free Goods: Requester -> Manager/HCD -> BC -> Outward WH -> Completed --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
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
        let nextFgNumber = "{{ $generatedFg }}"; // Ganti nama variabel

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
            // Department user yang login untuk logika alur
            const userDepartmentName = "{{ $userDepartmentName ?? '' }}";

            function initSelect2() {
                
                function formatCustomer(option) {
                    if (!option.id) return '<span class="text-muted">Select Customer</span>';
                    // Ganti warna icon ke text-warning (Coklat)
                    return `<i class='ph ph-user-circle me-2 text-warning'></i> <span style='font-weight:500;'>${option.text}</span>`;
                }
                $('#customer_id').select2({
                    dropdownParent: $('#fgModal'), // Ganti ID Modal
                    placeholder: 'Select Customer',
                    allowClear: true,
                    templateResult: formatCustomer,
                    templateSelection: formatCustomer,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                // Ganti product_select ke product_select_fg
                $('#product_select_fg').select2({
                    dropdownParent: $('#fgModal'), // Ganti ID Modal
                    placeholder: 'Select products',
                    allowClear: true,
                    ajax: {
                        url: "{{ route('freegoods.getAllItemMasters') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            const results = data.map(item => ({
                                id: item.id,
                                text: `[${item.item_master_code}] ${item.item_master_name}`
                            }));
                            return {
                                results: results
                            };
                        },
                        cache: true
                    }
                });
            }
            initSelect2();

            // Ganti ID table dari sampleTable ke fgTable
            const table = $('#fgTable').DataTable({
                processing: true,
                serverSide: true,
                // Ganti route ajax dari sample.data ke freegoods.data
                ajax: "{{ route('freegoods.data') }}",
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

            let searchInput = $('#fgTable_filter input'); // Ganti ID
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });

            $('#fgTable_filter input').attr({ // Ganti ID
                'placeholder': '🔍 Search Free Goods...',
                'class': 'form-control'
            });

            function clearValidationErrors() {
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#items_error').hide().text('');
                $('.select2-selection').css('border-color', '');
            }

            function resetForm() {
                $('#fgForm')[0].reset(); // Ganti ID Form
                $('#fgForm').removeAttr('data-mode data-id'); // Ganti ID Form
                $('#customer_id, #product_select_fg').val(null).trigger('change');
                // Ganti ID tbody
                $('#requisition-items-tbody-fg').html(
                    '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                );

                clearValidationErrors();
                $('#fgModalLabel').text('Create New Free Goods Requisition'); // Ganti Label
                $('#no_fg').val(nextFgNumber); // Ganti ID No SRS
                $('#saveFgBtn').text('Save'); // Ganti ID Button
            }

            $('#btn-create-fg').on('click', function () { // Ganti ID Button
                resetForm();
                $('#fgForm').attr('data-mode', 'create').removeAttr('data-id'); // Ganti ID Form
                $('#fgModal').modal('show'); // Ganti ID Modal
            });

            $('#customer_id').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address') || '';
                $('#customer_address').val(address);
            });

            $('#btn-add-items-master').on('click', function () {
                    const selectedMasterIds = $('#product_select_fg').val();
                    if (!selectedMasterIds || selectedMasterIds.length === 0) {
                        warningMessage('Please select a product first.');
                        return;
                    }
                    $.ajax({
                        // Ganti route dari sample ke freegoods
                        url: "{{ route('freegoods.getAllItemMasters') }}", 
                        method: 'GET',
                        success: function (allMasters) {
                            const tbody = $('#requisition-items-tbody-fg'); // Ganti ID tbody
                            $('#no-items-row').remove();
                            
                            // Ambil list item masters yang dipilih dari hasil AJAX
                            const selectedMasters = allMasters.filter(m => selectedMasterIds.includes(String(m.id)));

                            selectedMasters.forEach(master => {
                                // Cek apakah item sudah ada di tabel
                                if ($(`#item-row-master-${master.id}`).length === 0) {
                                    // Item Master untuk Free Goods
                                    const newRow = `
                                        <tr id="item-row-master-${master.id}" data-master-id="${master.id}">
                                            <td>${master.item_master_code}</td>
                                            <td>${master.item_master_name}</td>
                                            <td>${master.unit}</td>
                                            <td><input type="number" class="form-control" name="items[${master.id}][quantity_required]" min="1"></td>
                                            <td><input type="number" class="form-control" name="items[${master.id}][quantity_issued]" min="0"></td>
                                        </tr>`;
                                    tbody.append(newRow);
                                }
                            });
                        }
                    });
                });

            $('#product_select_fg').on('select2:unselect', function (e) {
                const unselectedMasterId = e.params.data.id;
                // Hapus item master yang di-unselect
                $(`tr[data-master-id="${unselectedMasterId}"][id^="item-row-master-"]`).remove();

                if ($('#requisition-items-tbody-fg tr').length === 0) { // Ganti ID tbody
                    $('#requisition-items-tbody-fg').html( // Ganti ID tbody
                        '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                        );
                }
            });

            $('#fgForm').on('submit', function (e) { // Ganti ID Form
                e.preventDefault(); 
                const form = this; 

                const customerName = $('#customer_id option:selected').text().trim();
                const requestDate = $('#request_date').val();
                // Ganti ID tbody
                const itemCount = $('#requisition-items-tbody-fg tr[id^="item-row-"]').length; 

                Swal.fire({
                    title: 'Konfirmasi Pengajuan',
                    html: `Anda akan mengajukan Requisition dengan ringkasan data berikut:
                        <ul class="text-start mt-3" style="list-style: none; padding-left: 0;">
                            <li style="padding: 5px 0;"><strong>Kategori:</strong> FREE GOODS (General Request)</li>
                            <li style="padding: 5px 0;"><strong>Customer:</strong> ${customerName || '<i>Belum dipilih</i>'}</li>
                            <li style="padding: 5px 0;"><strong>Tgl. Request:</strong> ${requestDate}</li>
                            <li style="padding: 5px 0;"><strong>Jumlah Item:</strong> ${itemCount} item</li>
                        </ul>
                        <hr>
                        <b class="text-danger">Pastikan semua data yang Anda masukkan sudah benar.</b>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Data Sudah Benar!',
                    cancelButtonText: 'Batal, Cek Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const mode = $(form).attr('data-mode');
                        const id = $(form).attr('data-id');

                        function submitForm(formData) {
                        const submitBtn = $('#saveFgBtn'); // Ganti ID Button
                        const overlay = $('#fgModal .loading-overlay'); // Ganti ID Modal
                        overlay.show();
                        submitBtn.prop('disabled', true);

                        // Ganti URL route dari sample-form ke freegoods-form
                        let url = (mode === 'edit') ? `/freegoods-form/${id}` : "{{ route('freegoods-form.store') }}";
                        if (mode === 'edit') {
                            formData.append('_method', 'PUT');
                        }

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.success) {
                                    $('#fgModal').modal('hide'); // Ganti ID Modal
                                    successMessage(res.message);
                                    table.ajax.reload(null, false);
                                    // Update next FG number
                                    nextFgNumber = res.next_fg_number; 
                                }
                            },

                            error: function (xhr) {
                                if (xhr.status === 422) {
                                    const errors = xhr.responseJSON.errors;
                                    let itemErrorMessages = new Set();
                                    clearValidationErrors();
                                    for (const key in errors) {
                                        const errorMsg = errors[key][0];
                                        if (key.startsWith('items.')) {
                                            itemErrorMessages.add(errorMsg);
                                        } else {
                                            const field = $(`#${key}`);
                                            const errorDiv = $(`#${key}_error`);
                                            field.addClass('is-invalid');
                                            errorDiv.text(errorMsg).show();
                                            if (field.hasClass('select2-styled')) {
                                                field.next('.select2-container').find('.select2-selection').css('border-color', '#dc3545');
                                            }
                                        }
                                    }
                                    if (itemErrorMessages.size > 0) {
                                        $('#items_error').show().html(Array.from(itemErrorMessages).join('<br>'));
                                    }
                                } else {
                                    errorMessage(xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.');
                                }
                        },
                        complete: function() {
                            overlay.hide();
                            submitBtn.prop('disabled', false);
                        }
                    });
                }
                
                let formData = new FormData(form);
                // Kita tambahkan nilai Category secara eksplisit
                formData.append('category', 'FREE GOODS');
                formData.append('sub_category', 'General Request');
                formData.append('no_srs', $('#no_fg').val()); // Ambil dari input No FG
                
                submitForm(formData);
                    }
                });
            });

            function populateForm(data) {
                $('#fgForm').attr('data-mode', 'edit').attr('data-id', data.id); // Ganti ID Form

                // Isi form
                $('#customer_id').val(data.customer_id).trigger('change.select2');
                $('#no_fg').val(data.no_srs); // Ganti ID No SRS
                $('#account').val(data.account);
                $('#cost_center').val(data.cost_center);
                $('#request_date').val(data.request_date);
                $('#objectives').val(data.objectives);
                $('#estimated_potential').val(data.estimated_potential);

                // Mengisi Dropdown Produk
                const productSelectFg = $('#product_select_fg');
                productSelectFg.empty();
                if (data.product_options && data.product_options.length > 0) {
                    data.product_options.forEach(option => {
                        productSelectFg.append(new Option(option.text, option.id, false, false));
                    });
                }
                if (data.selected_master_ids && data.selected_master_ids.length > 0) {
                    productSelectFg.val(data.selected_master_ids);
                }
                productSelectFg.trigger('change.select2');

                // Mengisi Item List
                const itemTbody = $('#requisition-items-tbody-fg'); // Ganti ID tbody
                itemTbody.empty();
                const colspan = 5;

                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';

                        if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                        }

                        const newRow = `
                            <tr id="item-row-master-${item.item_master_id}" data-master-id="${item.item_master_id}">
                                <td>${itemCode}</td>
                                <td>${itemName}</td>
                                <td>${unit}</td>
                                <td><input type="number" class="form-control" name="items[${item.item_master_id}][quantity_required]" value="${item.quantity_required || ''}" min="1"></td>
                                <td><input type="number" class="form-control" name="items[${item.item_master_id}][quantity_issued]" value="${item.quantity_issued || ''}" min="0"></td>
                            </tr>`;
                        itemTbody.append(newRow);
                    });
                } else {
                    itemTbody.html(`<tr id="no-items-row"><td colspan="${colspan}" class="text-center">No items found.</td></tr>`);
                }

                $('#saveFgBtn').text('Save Changes').prop('disabled', false); // Ganti ID Button
            }

            function populateViewForm(data) {
                $('#view_sub_category').text(data.sub_category || 'General Request');
                $('#view_customer_name').text(data.customer ? data.customer.name : '-');
                $('#view_customer_address').text(data.customer ? data.customer.address : '-');
                $('#view_no_srs').text(data.no_srs || '-');
                $('#view_account').text(data.account || '-');
                $('#view_request_date').text(new Date(data.request_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) || '-');
                $('#view_cost_center').text(data.cost_center || '-');
                $('#view_objectives').text(data.objectives || '-');
                $('#view_estimated_potential').text(data.estimated_potential || '-');

                const viewItemTbody = $('#view-items-tbody-fg'); // Ganti ID tbody
                viewItemTbody.empty();
                const colspan = 5;

                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';

                        if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                        }

                        const newRow = `
                        <tr>
                            <td>${itemCode}</td>
                            <td>${itemName}</td>
                            <td>${unit}</td>
                            <td class="text-center">${item.quantity_required}</td>
                            <td class="text-center">${item.quantity_issued || '-'}</td>
                        </tr>`;
                        viewItemTbody.append(newRow);
                    });
                } else {
                    viewItemTbody.html(`<tr><td colspan="${colspan}" class="text-center">No items have been added.</td></tr>`);
                }

                // Status Badge
                const status = data.status;
                let badgeClass = 'bg-secondary';
                if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-primary';
                else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                else if (['Rejected', 'Cancelled'].includes(status)) badgeClass = 'bg-danger';
                else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-info text-dark';

                $('#view_status_badge').html(`<span class="badge fs-6 rounded-pill ${badgeClass}">${status}</span>`);

                // Approval Tracker
                const trackerContainer = $('#approval-tracker-container-fg'); // Ganti ID container
                trackerContainer.empty();

                // 1. Definisikan langkah-langkah Free Goods
                let steps = [
                    { id: 'submitted', label: 'Request Submit', icon: 'ph-file-arrow-up' }
                ];
                
                // Alur Free Goods: Requester -> Manager/HCD -> BC -> Outward WH -> Completed
                // Kita asumsikan level approval (Manager/HCD dan BC) adalah level <= 100
                const approvalLogs = data.approval_logs ? data.approval_logs.filter(log => log.level <= 100) : [];
                
                let nextLevel = 1;
                let isSnM = data.requester.department?.name === 'SnM'; // Asumsi department user ada di data
                let approvalSteps = [
                    { label: isSnM ? 'SnM Manager' : 'HCD Dept. Head', icon: 'ph-user-plus' },
                    { label: 'Business Controller', icon: 'ph-briefcase' }
                ];

                approvalSteps.forEach((step, index) => {
                    // Cari log approval yang sudah terekam di level ini (jika ada)
                    const log = approvalLogs.find(l => l.level === (index + 1)); 
                    const approverName = log && log.approver ? log.approver.name : step.label;
                    
                    steps.push({
                        id: 'approver_' + (index + 1),
                        label: `${step.label}<br><small class="text-muted fw-normal">${log ? approverName : '...'}</small>`,
                        icon: 'ph-user-check'
                    });
                });
                
                if (status !== 'Rejected' && status !== 'Cancelled') {
                    // Langkah Tracking Warehouse
                    steps.push({ id: 'outward', label: 'Outward WH Supervisor', icon: 'ph-package' });
                    steps.push({ id: 'completed', label: 'Completed', icon: 'ph-check-circle' });
                }

                let trackerHtml = '<div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress-fg"></div></div>'; // Ganti ID progress
                steps.forEach(step => {
                    trackerHtml += `
                        <div class="tracker-step" data-step-id="${step.id}">
                            <div class="tracker-icon"><i class="ph-bold ${step.icon} fs-6"></i></div>
                            <div class="tracker-label">${step.label}</div>
                            <div class="tracker-details"></div>
                        </div>`;
                });
                trackerContainer.html(trackerHtml);

                // 4. Update status visual tracker (Logic sama seperti Sample)
                let lastCompletedIndex = -1;
                let isRejected = ['Rejected', 'Cancelled'].includes(status);

                // Submitted Step
                if (data.requester && data.created_at) {
                    const submittedStep = $(`.tracker-step[data-step-id="submitted"]`);
                    submittedStep.addClass('completed');
                    const requesterName = data.requester.name;
                    const creationDate = new Date(data.created_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                    submittedStep.find('.tracker-details').html(
                        `<div class="tracker-user text-warning">${requesterName}</div>` // WARNA TEKS DISESUAIKAN
                    );
                    lastCompletedIndex = 0;
                }

                // Approval Steps
                approvalLogs.forEach(log => {
                    const stepId = 'approver_' + log.level;
                    const stepIndex = steps.findIndex(s => s.id === stepId);
                    if (stepIndex > -1 && log.status === 'Approved') {
                        const stepElement = $(`.tracker-step[data-step-id="${stepId}"]`);
                        stepElement.addClass('completed');
                        const approvalDate = new Date(log.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                        stepElement.find('.tracker-details').html(
                            `<div class="tracker-user text-warning">${log.approver.name}</div>` // WARNA TEKS DISESUAIKAN
                        );
                        lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                    }
                });

                // Tracking Steps
                if (data.trackings && data.trackings.length > 0) {
                    const currentTrack = data.trackings[data.trackings.length - 1];
                    if (currentTrack.current_position.includes('Outward WH Supervisor')) {
                        const stepIndex = steps.findIndex(s => s.id === 'outward');
                        if(stepIndex > -1) {
                            $(`.tracker-step[data-step-id="outward"]`).addClass('active');
                            // Asumsi step Outward dimulai setelah semua approval selesai, jadi kita hanya perlu set lastCompletedIndex
                            lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex - 1); 
                        }
                    } else if (currentTrack.current_position === 'Completed') {
                         const stepIndex = steps.findIndex(s => s.id === 'completed');
                         if(stepIndex > -1) {
                             $(`.tracker-step`).addClass('completed');
                             lastCompletedIndex = stepIndex;
                         }
                    }
                }
                
                // Update Progress Line
                if (lastCompletedIndex >= 0 && !isRejected) {
                    let totalSteps = steps.length - 1;
                    // Hitung persentase langkah yang sudah selesai (contoh: 0/4, 1/4, 2/4, 3/4, 4/4)
                    let progressPercentage = (lastCompletedIndex / totalSteps) * 100;
                    // Sesuaikan jika hanya ada 1 step, progress 100%
                    if (totalSteps === 0) progressPercentage = 100; 
                    
                    $('#tracker-progress-fg').css('width', progressPercentage + '%'); // Ganti ID progress
                }


                // Final Status Marker
                if (status === 'Completed') {
                    $('.tracker-step').addClass('completed');
                } else if (isRejected) {
                    const nextStepIndex = lastCompletedIndex + 1;
                    if (nextStepIndex < steps.length) {
                        $(`.tracker-step`).eq(nextStepIndex).addClass('rejected');
                    }
                } else if (status === 'Pending' || status === 'In Progress') {
                    const nextStepIndex = lastCompletedIndex + 1;
                    if (nextStepIndex < steps.length) {
                        $(`.tracker-step`).eq(nextStepIndex).addClass('active');
                    }
                }

            }

            $(document).on('click', '.btn-view-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    // Ganti URL route dari sample-form ke freegoods-form
                    url: `/freegoods-form/${id}`, 
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

                const modal = $('#fgModal'); // Ganti ID Modal
                const overlay = modal.find('.loading-overlay');

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                overlay.show();

                $.ajax({
                    // Ganti URL route dari sample-form ke freegoods-form
                    url: `/freegoods-form/${id}/edit`, 
                    type: 'GET',
                    success: function (response) {
                        $('#fgModalLabel').text('Edit Free Goods Requisition'); // Ganti Label

                        populateForm(response);

                        modal.modal('show');
                    },
                    error: function () {
                        errorMessage('Failed to fetch data for editing.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                        overlay.hide();
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
                            // Ganti URL route dari sample-form ke freegoods-form
                            url: `/freegoods-form/${requisitionId}`,
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
            
            // HILANGKAN: btn-qa-form
            
            $('#fgModal').on('hidden.bs.modal', function () { // Ganti ID Modal
                resetForm();
                $('#fgForm').removeAttr('data-mode data-id'); // Ganti ID Form
            });

        });

    </script>
    @endpush
</x-app-layout>