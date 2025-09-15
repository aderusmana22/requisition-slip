<x-app-layout>
    @section('title')
        Sample Requisition
    @endsection

    @push('css')
    <link href="{{ asset('assets/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
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

    <div class="modal fade" id="sampleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">

                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logohitam.png') }}" alt="Logo" style="height: 24px;" class="me-3">
                        <h5 class="modal-title text-white mb-0" id="sampleModalLabel">Create Sample Requisition</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sampleForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 mb-3 align-items-end">
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
                                <div class="invalid-feedback" id="sub_category_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category<i class="text-danger">*</i></label>
                                <input type="text" class="form-control" value="SAMPLE" readonly>
                            </div>
                        </div>

                        <div id="requisition-form-details" style="display: none;">
                            <hr>
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
                                    <div class="invalid-feedback" id="customer_id_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_address" class="form-label">Address<i
                                            class="text-danger">*</i></label>
                                    <textarea class="form-control" id="customer_address" rows="2" readonly></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="no_srs" class="form-label">No. SRS<i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" id="no_srs" name="no_srs"
                                        value="{{ $generatedSrs }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="account" class="form-label">Account<i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" id="account" name="account"
                                        value="{{ $userAccount }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="cost_center" class="form-label">Cost Center</label>
                                    <input type="text" class="form-control" id="cost_center" name="cost_center">
                                </div>
                                <div class="col-md-3">
                                    <label for="request_date" class="form-label">Request Date<i
                                            class="text-danger">*</i></label>
                                    <input type="date" class="form-control" id="request_date" name="request_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="objectives" class="form-label">Objectives<i
                                            class="text-danger">*</i></label>
                                    <textarea class="form-control" id="objectives" name="objectives"
                                        rows="2"></textarea>
                                    <div class="invalid-feedback" id="objectives_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="estimated_potential" class="form-label">Estimated Potential<i
                                            class="text-danger">*</i></label>
                                    <textarea class="form-control" id="estimated_potential" name="estimated_potential"
                                        rows="2"></textarea>
                                    <div class="invalid-feedback" id="estimated_potential_error"></div>
                                </div>
                            </div>

                            <div id="special-order-fields" style="display: none;">
                                <hr class="mt-4">
                                <h5 class="text-primary fw-bold">Special Order Details</h5>
                                <h6 class="text-danger fw-bold">
                                    <center><i>Diisi oleh Marketing</i></center>
                                </h6>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-4">
                                        <label for="sample_completion_date" class="form-label">Tgl. Selesai Sample<i
                                                class="text-danger">*</i></label>
                                        <input type="date" class="form-control" name="sample_completion_date">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Berat Sample<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="sample_weight"
                                            placeholder="Contoh: 25 Kg, 15 Kg, Lainnya: 10 Pcs">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kemasan Sample<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="sample_packaging"
                                            placeholder="Contoh: Karton, Lainnya: Plastik Wrap">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="sample_quantity_details" class="form-label">Jumlah Sample<i
                                                class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="sample_quantity_details"
                                            placeholder="Contoh: 2x1kg (setiap produk)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Certificate of Analysis<i
                                                class="text-danger">*</i></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="coa_required"
                                                    id="coa_yes" value="1">
                                                <label class="form-check-label" for="coa_yes">Ya<i
                                                        class="text-danger">*</i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="coa_required"
                                                    id="coa_no" value="0">
                                                <label class="form-check-label" for="coa_no">Tidak<i
                                                        class="text-danger">*</i></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Dikirim Melalui<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" name="delivery_method"
                                            placeholder="Contoh: Delivery (DHL), Lainnya: Gojek">
                                    </div>
                                </div>

                                <div class="qa-fields-section mt-4">
                                    <hr>
                                    <h6 class="text-danger fw-bold">
                                        <center><i>Diisi oleh QA/QM</i></center>
                                    </h6>
                                    <div class="row g-3 mt-1">
                                        <div class="col-12">
                                            <label class="form-label">Asal Sample<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_origin" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Keterangan Sample: Batch/Pallet No<i
                                                    class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_description_batch"
                                                disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">WB/DEO No<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_description_wb"
                                                disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tank No<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_description_tank"
                                                disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tgl Produksi<i class="text-danger">*</i></label>
                                            <input type="date" class="form-control" name="production_date" disabled>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Persiapan Sample<i
                                                    class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="sample_preparation" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Keterangan<i class="text-danger">*</i></label>
                                            <input type="text" class="form-control" name="qa_notes" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-4">
                            <div class="alert alert-primary" id="items_error" style="display: none;"></div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">2. Select Material Type(s)<i
                                        class="text-danger">*</i></label>
                                <div>
                                    @foreach ($materialTypes as $type)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input material-type-checkbox" type="checkbox"
                                            id="type_{{ Str::slug($type) }}" value="{{ $type }}">
                                        <label class="form-check-label" for="type_{{ Str::slug($type) }}">{{ $type }}<i
                                                class="text-danger">*</i></label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3" id="product-selection-container" style="display:none;">
                                <label for="product_select" class="form-label fw-bold">3. Select Product Name(s)<i
                                        class="text-danger">*</i></label>
                                <select class="form-select select2-styled" id="product_select" multiple="multiple"
                                    style="width: 100%;"></select>
                                <button type="button" class="btn btn-success btn-sm mt-2" id="btn-add-items">
                                    <i class="ph-bold ph-plus"></i> Add Selected Items to Request
                                </button>
                            </div>
                            <div class="table-responsive mt-4">
                                <h6 class="fw-bold">4. Requested Items List</h6>
                                <table class="table table-bordered w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item Detail Code</th>
                                            <th>Item Detail Name</th>
                                            <th>Unit</th>
                                            <th style="width: 15%;">Qty Required</th>
                                            <th style="width: 15%;">Qty Issued</th>
                                            <th style="width: 5%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="requisition-items-tbody">
                                        <tr id="no-items-row">
                                            <td colspan="6" class="text-center">No items added yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" id="saveSampleBtn">Save changes</button>
                        <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
        // === SweetAlert2 Reusable Functions ===
        function successMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function errorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
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
            const userDepartment = "{{ $userDepartmentName ?? '' }}";
            // === Initialize Select2 with custom style ===
            function initSelect2() {
                function formatSubCategory(option) {
                    if (!option.id) return '<span class="text-muted">Pilih Sub Category</span>';
                    let icon = '';
                    if (option.text.includes('Packaging')) icon =
                        '<i class="ph ph-package me-2 text-primary"></i>';
                    if (option.text.includes('Finished')) icon = '<i class="ph ph-cube me-2 text-success"></i>';
                    if (option.text.includes('Special')) icon =
                        '<i class="ph ph-star-four me-2 text-warning"></i>';
                    return `<span style='font-weight:500;'>${icon}${option.text}</span>`;
                }
                $('#sub_category').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Pilih Sub Category',
                    allowClear: true,
                    templateResult: function (option) {
                        return $(formatSubCategory(option));
                    },
                    templateSelection: function (option) {
                        return $(formatSubCategory(option));
                    }
                });

                // Custom template for customer
                function formatCustomer(option) {
                    if (!option.id) return '<span class="text-muted">Pilih Customer</span>';
                    return `<i class='ph ph-user-circle me-2 text-info'></i> <span style='font-weight:500;'>${option.text}</span>`;
                }
                $('#customer_id').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Pilih Customer',
                    allowClear: true,
                    templateResult: function (option) {
                        return $(formatCustomer(option));
                    },
                    templateSelection: function (option) {
                        return $(formatCustomer(option));
                    }
                });

                // Select2 untuk produk tetap default
                $('#product_select').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Select products',
                    allowClear: true
                });
            }
            initSelect2();

            // === DataTable ===
            const table = $('#sample-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sample.data') }}",
                columns: [{
                        data: 'requester_info',
                        name: 'users.name'
                    }, // Sudah diperbaiki
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

            // === Form Reset & Cleanup Functions ===
            function clearValidationErrors() {
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#items_error').hide().text('');
            }

            function resetForm() {
                $('#sampleForm')[0].reset();
                $('#sub_category, #customer_id, #product_select').val(null).trigger('change');

                // TAMBAHKAN BARIS INI
                $('.material-type-checkbox').prop('checked', false);

                $('#requisition-items-tbody').html(
                    '<tr id="no-items-row"><td colspan="6" class="text-center">No items added yet.</td></tr>'
                );
                $('#requisition-form-details, #product-selection-container, #special-order-fields').hide();
                clearValidationErrors();
            }

            $('#btn-create-sample').on('click', function () {
                resetForm();
                $('#sampleModalLabel').text('Create Sample Requisition');
                // Pastikan form disetel ke mode 'create'
                $('#sampleForm').attr('data-mode', 'create').removeAttr('data-id');
            });

            // === DYNA MIC FORM LOGIC (BARU) ===

            // 1. Tampilkan form utama setelah Sub Kategori dipilih
            $('#sub_category').on('change', function () {
                if ($(this).val()) {
                    $('#requisition-form-details').slideDown();
                } else {
                    $('#requisition-form-details').slideUp();
                }

                // TAMBAHKAN LOGIKA INI
                if ($(this).val() === 'Special Order') {
                    $('#special-order-fields').slideDown();
                } else {
                    $('#special-order-fields').slideUp();
                }
            });

            // 2. Auto-fill address when customer is selected
            $('#customer_id').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address') || '';
                $('#customer_address').val(address);
            });

            // 2. Ambil data produk (Item Master) saat Material Type di-check
            $('.material-type-checkbox').on('change', function () {
                const selectedTypes = [];
                $('.material-type-checkbox:checked').each(function () {
                    selectedTypes.push($(this).val());
                });

                const productContainer = $('#product-selection-container');
                const productSelect = $('#product_select');

                if (selectedTypes.length > 0) {
                    $.ajax({
                        url: "{{ route('sample.getProductsByMaterialTypes') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            material_types: selectedTypes
                        },
                        success: function (products) {
                            productSelect.empty();
                            products.forEach(p => {
                                productSelect.append(new Option(p.item_master_name,
                                    p.id, false, false));
                            });
                            productSelect.trigger('change');
                            productContainer.slideDown();
                        },
                        error: function () {
                            errorMessage('Failed to load products.');
                        }
                    });
                } else {
                    productContainer.slideUp();
                    productSelect.empty().trigger('change');
                }
            });

            // 3. Tambahkan Item Detail ke tabel setelah produk dipilih dan tombol diklik
            $('#btn-add-items').on('click', function () {
                const selectedProductIds = $('#product_select').val();
                if (!selectedProductIds || selectedProductIds.length === 0) {
                    warningMessage('Please select at least one product to add.');
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
                            // Cek duplikat sebelum menambahkan
                            if ($(`#item-row-${detail.id}`).length === 0) {
                                const newRow = `
                                        <tr id="item-row-${detail.id}">
                                            <td>${detail.item_detail_code}</td>
                                            <td>${detail.item_detail_name}</td>
                                            <td>${detail.unit}</td>
                                            <td><input type="number" class="form-control" name="items[${detail.id}][quantity_required]" min="1"></td>
                                            <td><input type="number" class="form-control" name="items[${detail.id}][quantity_issued]" min="0"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm btn-remove-item"><i class="fas fa-trash-alt"></i></button></td>
                                        </tr>
                                    `;
                                $('#requisition-items-tbody').append(newRow);
                            }
                        });
                        // Kosongkan pilihan setelah ditambahkan
                        $('#product_select').val(null).trigger('change');
                    },
                    error: function () {
                        errorMessage('Failed to fetch item details.');
                    }
                });
            });

            // 5. Remove an item from the request table
            $(document).on('click', '.btn-remove-item', function () {
                $(this).closest('tr').remove();
                if ($('#requisition-items-tbody tr').length === 0) {
                    $('#requisition-items-tbody').html(
                        '<tr id="no-items-row"><td colspan="6" class="text-center">No items added yet.</td></tr>'
                    );
                }
            });

            // === FORM SUBMISSION (CREATE & UPDATE) ===
            $('#sampleForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors();

                const mode = $(this).attr('data-mode');
                const id = $(this).attr('data-id');
                let url = "{{ route('sample-form.store') }}";
                let method = 'POST';

                if (mode === 'edit') {
                    url = `/sample-form/${id}`;
                }

                let formData = new FormData(this);
                if (mode === 'edit') {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    method: method,
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
                                    const parts = key.split('.');
                                    const itemId = parts[1];
                                    const field = parts[2];
                                    $(`input[name="items[${itemId}][${field}]"]`).addClass(
                                        'is-invalid').closest('td').find(
                                        '.invalid-feedback').text(errorMsg);
                                } else if (key === 'items') {
                                    $('#items_error').show().text(errorMsg);
                                } else {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}_error`).text(errorMsg);
                                }
                            }
                            errorMessage('Pastikan kembali data yang anda masukkan sudah terisi dengan benar');
                        } else {
                            errorMessage(xhr.responseJSON?.message ||
                                'Something went wrong.');
                        }
                    }
                });
            });

            // ==========================================================
            // === BLOK BARU: FUNGSI UNTUK EDIT, DAN DELETE ===
            // ==========================================================

            // Ganti seluruh fungsi populateForm Anda dengan yang ini
            function populateForm(data) {
                // 1. Reset & nonaktifkan semua field dulu untuk persiapan
                $('#sampleForm').find('input, select, textarea').prop('disabled', true);
                $('#saveSampleBtn').hide();

                // 2. Isi semua data yang ada dari server
                // Bagian Marketing & Umum
                $('#sub_category').val(data.sub_category).trigger('change');
                $('#customer_id').val(data.customer_id).trigger('change');
                $('#no_srs').val(data.no_srs);
                $('#account').val(data.account);
                $('#cost_center').val(data.cost_center);
                $('#request_date').val(data.request_date);
                $('#objectives').val(data.objectives);
                $('#estimated_potential').val(data.estimated_potential);

                // Tampilkan detail form utama
                $('#requisition-form-details').show();

                // Bagian Special Order (jika ada)
                if (data.sub_category === 'Special Order') {
                    $('#special-order-fields').show();
                    $('input[name="sample_completion_date"]').val(data.sample_completion_date);
                    $('input[name="sample_weight"]').val(data.sample_weight);
                    $('input[name="sample_packaging"]').val(data.sample_packaging);
                    $('input[name="sample_quantity_details"]').val(data.sample_quantity_details);
                    $('input[name="delivery_method"]').val(data.delivery_method);
                    if (data.coa_required == 1) {
                        $('#coa_yes').prop('checked', true);
                    } else if (data.coa_required == 0) {
                        $('#coa_no').prop('checked', true);
                    }

                    // Isi Bagian QA (jika ada)
                    $('input[name="sample_origin"]').val(data.sample_origin);
                    $('input[name="production_date"]').val(data.production_date);
                    $('input[name="sample_description_batch"]').val(data.sample_description_batch);
                    $('input[name="sample_description_wb"]').val(data.sample_description_wb);
                    $('input[name="sample_description_tank"]').val(data.sample_description_tank);
                    $('input[name="sample_preparation"]').val(data.sample_preparation);
                    $('input[name="qa_notes"]').val(data.qa_notes);
                } else {
                    $('#special-order-fields').hide();
                }

                // 3. Logika untuk mengaktifkan field berdasarkan alur kerja
                if (userDepartment === 'QA/QM' && data.route_to === 'QA/QM') {
                    $('.qa-fields-section').find('input, select, textarea').prop('disabled', false);
                    $('#saveSampleBtn').text('Save QA Data').show();
                } else if (data.status === 'Pending' || data.status === 'Draft') { // Asumsi requester bisa edit saat status Pending/Draft
                    // Aktifkan field yang boleh diedit oleh requester
                    $('#requisition-form-details').find('input, select, textarea').not('[readonly]').prop('disabled', false);
                    // Pastikan field QA tetap disabled
                    $('.qa-fields-section').find('input, select, textarea').prop('disabled', true);
                    $('#saveSampleBtn').text('Save Changes').show();
                }
                // Jika tidak ada kondisi di atas (mode view), semua tetap disabled dan tombol save tersembunyi.

                // 4. Proses dan tampilkan item di tabel
                const itemTbody = $('#requisition-items-tbody');
                itemTbody.empty();
                const materialTypesInUse = new Set();

                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        const detail = item.item_detail;
                        if (detail) {
                            // Kumpulkan material type yang ada di data
                            materialTypesInUse.add(detail.material_type);
                            // (kode untuk membuat baris tabel tidak berubah)
                            const newRow = `
                                <tr id="item-row-${detail.id}">
                                    <td>${detail.item_detail_code}</td>
                                    <td>${detail.item_detail_name}</td>
                                    <td>${detail.unit}</td>
                                    <td><input type="number" class="form-control" name="items[${detail.id}][quantity_required]" min="1" value="${item.quantity_required}"></td>
                                    <td><input type="number" class="form-control" name="items[${detail.id}][quantity_issued]" min="0" value="${item.quantity_issued || ''}"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm btn-remove-item"><i class="fas fa-trash-alt"></i></button></td>
                                </tr>
                            `;
                            itemTbody.append(newRow);
                        }
                    });

                    // 5. Atur status checkbox berdasarkan data yang sudah dikumpulkan
                    $('.material-type-checkbox').each(function() {
                        const isChecked = materialTypesInUse.has($(this).val());
                        $(this).prop('checked', isChecked);
                    });

                } else {
                    itemTbody.html('<tr id="no-items-row"><td colspan="6" class="text-center">No items found.</td></tr>');
                }
}

            // Event handler untuk tombol EDIT
            $(document).on('click', '.btn-edit-requisition', function () {
                const id = $(this).data('id');
                resetForm();

                $.ajax({
                    url: `/sample-form/${id}/edit`,
                    type: 'GET',
                    success: function (response) {
                        $('#sampleModalLabel').text('Edit Sample Requisition');
                        $('#sampleForm').attr('data-mode', 'edit').attr('data-id', id);
                        populateForm(response);
                        $('#sampleModal').modal('show');
                    },
                    error: function () {
                        errorMessage('Failed to fetch data for editing.');
                    }
                });
            });

            // Event handler untuk tombol DELETE
            $(document).on('click', '.btn-delete-requisition', function () {
                const requisitionId = $(this).data('id');
                const url = `/sample-form/${requisitionId}`;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
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
                                Swal.fire('Error!', 'Something went wrong.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Event untuk membersihkan modal SETELAH ditutup
            $('#sampleModal').on('hidden.bs.modal', function () {
                $('#sampleForm').find('input, select, textarea, button').prop('disabled', false);
                $('#saveSampleBtn').show();
                $('#sampleForm').attr('data-mode', 'create').removeAttr('data-id');
            });
        });

    </script>
    @endpush
</x-app-layout>


