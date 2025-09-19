<x-app-layout>
    @section('title')
    Users List
    @endsection

    @push('css')
    <style>
    .modal-header {
        background-color: #cc982f;
    }
    .modal-footer {
        background-color: #f8f8f8;
    }
    .status-badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.7em;
    font-weight: 700;
}
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Users List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Complain form</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tabel Users -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-light-danger btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#complineModal" id="btn-create-compline">
                    <i class="ph-bold ph-plus pe-2"></i> New Complain
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="complainTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Requester</th>
                                    <th>Customer</th>
                                    <th>Request Date</th>
                                    <th>Cost Center</th>
                                    <th>Route To</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit User -->
    <div class="modal fade" id="complineModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="complineModalLabel">Create complain</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('complain-form.store') }}" method="POST" data-mode="create" data-id="" id="complineForm">
                    @csrf
                        <header class="row slip-header mb-2 align-items-center">
                            <div class="col-10">
                                <img src="{{ asset('storage/logo.png') }}" alt="Sinar Meadow Logo" class="logo" style="max-height: 60px; width: auto;">
                            </div>
                            <div class="col-2">
                                <p class="form-text text-start">
                                    FORM NO: FA-INV-05<br>
                                    REVISION: 3<br>
                                    DATE: 18 FEBRUARY 2021
                                </p>
                            </div>
                        </header>

                        <!-- judul modal -->
                        <div class="row mb-4 text-center">
                            <h4><strong>REQUISITION SLIP</strong></h4>
                            <p class="">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                        </div>
                
                        <!-- data modal -->
                        <div class="row mb-4 g-2">
                            <div class="col">
                                <div class="mb-3 row align-items-center">
                                    <label for="customer_id" class="col-sm-4 col-form-label"><strong>Customer Name :</strong></label>
                                    <div class="col-sm-7">
                                        <select name="customer_id" id="customer_id" class="form-select">
                                        </select>
                                        <div data-error-for="customer_id" class="text-danger mt-1 error-message"></div>
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="customer_address" class="col-sm-4 col-form-label"><strong>Customer Address :</strong></label>
                                    <div class="col-sm-7">
                                        <textarea class="form-control" id="customer_address" name="customer_address" rows="2" readonly></textarea>
                                        <div data-error-for="customer_address" class="text-danger mt-1 error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3 row align-items-center">
                                    <label for="account" class="col-sm-3 col-form-label"><strong>Account :</strong></label>
                                    <div class="col-sm-8">
                                        <h5 id="account_display" style="font-weight: bold; text-align: center;"></h5>
                                        <input type="hidden" class="form-control" id="account" name="account">
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="cost_center" class="col-sm-3 col-form-label"><strong>Cost Center :</strong></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="cost_center" name="cost_center">
                                        <div data-error-for="cost_center" class="text-danger mt-1 error-message"></div>
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="rs_number" class="col-sm-3 col-form-label"><strong>Nomor RS :</strong></label>
                                    <div class="col-sm-8">
                                        <h5 id="rs_number_display" style="font-weight: bold; text-align: center;"></h5>
                                        <input type="hidden" class="form-control" id="rs_number" name="rs_number">
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="date" class="col-sm-3 col-form-label"><strong>Tanggal :</strong></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div data-error-for="date" class="text-danger mt-1 error-message"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-4">
                                <label for="requisition_items"><strong>List Product</strong></label>
                                <select name="requisition_items[]" id="requisition_items" multiple="multiple" class="form-control" style="display: none;">
                                </select>
                                <div data-error-for="requisition_items" class="text-danger mt-1 error-message"></div>
                            </div>
                            <div class="col-3">
                                <label for="material_type"><strong>Material Type</strong></label>
                                <div id="material_type_wrapper">
                                    <div class="form-check">
                                        <input class="form-check-input material-type-filter" type="checkbox" name="material_type[]" value="Raw" id="mt-raw">
                                        <label class="form-check-label" for="mt-raw">Raw Material</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input material-type-filter" type="checkbox" name="material_type[]" value="Semi-Finished" id="mt-semi">
                                        <label class="form-check-label" for="mt-semi">Semi Finished Material</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input material-type-filter" type="checkbox" name="material_type[]" value="Finished" id="mt-finished">
                                        <label class="form-check-label" for="mt-finished">Finished Material</label>
                                    </div>
                                </div>
                                <div data-error-for="material_type" class="text-danger mt-1 error-message"></div>
                            </div>
                            <div class="col-5">
                                <label for="objectives"><strong>Objectives</strong></label>
                                <textarea name="objectives" id="objectives" class="form-control"></textarea>
                                <div data-error-for="objectives" class="text-danger mt-1 error-message"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="productDetailsContainer"></div>
                            </div>
                        </div>
        
                        <!-- tabel produk -->
                        <!-- <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered slip-table">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT CODE</th>
                                            <th>PRODUCT NAME</th>
                                            <th>UNIT</th>
                                            <th>QTY REQUIRED</th>
                                            <th>OBJECTIVES</th>
                                            <th style="width: 5%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-list">
                                        <tr>
                                            <td><input type="text" name="codes[0]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="names[0]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="units[0]" class="form-control form-control-sm"></td>
                                            <td><input type="number" name="quantities[0]"
                                                    class="form-control form-control-sm" min="1"></td>
                                            <td><input type="text" name="objectives[0]" class="form-control form-control-sm">
                                            </td>
                                            <td class="d-flex gap-1 align-items-center" style="white-space:nowrap;">
                                                <button type="button" class="btn btn-sm btn-danger remove-row-btn" style="display:inline-flex; align-items:center;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning clear-row-btn" style="display:inline-flex; align-items:center;">
                                                    <i class="fa fa-eraser"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="add-row-btn" class="btn btn-sm btn-success mt-2">
                                    <i class="fa fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div> -->

                </div>

                <!-- footer modal -->
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="submit" id="saveUserBtn">
                        Save changes
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal detail -->
    <div class="modal fade" id="detailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="detailModalLabel">Detail Requisition Complain</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <header class="row slip-header mb-2 align-items-center">
                        <div class="col-10">
                            <img src="{{ asset('storage/logo.png') }}" alt="Sinar Meadow Logo" class="logo"
                                style="max-height: 60px; width: auto;">
                        </div>
                        <div class="col-2">
                            <p class="form-text text-start">
                                FORM NO: FA-INV-05<br>
                                REVISION: 3<br>
                                DATE: 18 FEBRUARY 2021
                            </p>
                        </div>
                    </header>

                    <div class="row mb-4 text-center">
                        <h4><strong>REQUISITION SLIP</strong></h4>
                        <p class="">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                    </div>

                    <div class="row mb-4 g-2">
                        <div class="col">
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-4 col-form-label"><strong>Customer Name :</strong></label>
                                <div class="col-sm-7">
                                    <input type="text" id="detail_customer_name" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-4 col-form-label"><strong>Customer Address :</strong></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control" id="detail_customer_address" rows="2"
                                        readonly></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-3 col-form-label"><strong>Account :</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="detail_account" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-3 col-form-label"><strong>Cost Center :</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="detail_cost_center" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-3 col-form-label"><strong>Nomor RS :</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="detail_rs_number" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-3 col-form-label"><strong>Tanggal :</strong></label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" id="detail_date" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-4">
                            <label><strong>List Product</strong></label>
                            <div id="requisition_product_list"></div>
                        </div>
                        <div class="col-8">
                            <label><strong>Objectives</strong></label>
                            <textarea id="detail_objectives" class="form-control" readonly></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5><strong>Product Details:</strong></h5>
                            <div id="detail_productDetailsContainer">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!--js-->
    <script src="{{ asset('assets') }}/js/select.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // === SweetAlert2 Reusable Functions ===
        function successMessage(message, title = 'Success', timer = 1500) {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                timer: timer,
                showConfirmButton: false
            });
        }

        function errorMessage(message, title = 'Error') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message
            });
        }

        function warningMessage(message, title = 'Warning') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: message
            });
        }

        // confirmDialog: returns a Promise, so you can use .then()
        function confirmDialog({
            title = 'Are you sure?',
            text = 'This action cannot be undone!',
            confirmButtonText = 'Yes',
            cancelButtonText = 'Cancel',
            confirmButtonColor = '#3085d6',
            cancelButtonColor = '#d33',
            icon = 'warning',
            reverseButtons = true
        } = {}) {
            return Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonColor,
                cancelButtonColor,
                confirmButtonText,
                cancelButtonText,
                reverseButtons
            });
        }

        $(document).ready(function() {
            let customerSelect = $('#customer_id');
            let addressField = $('#customer_address');
            let productselect = $('#requisition_items');
            let materialtype = $('#material_type_wrapper');
            
            // define url detail
            let detailUrlTemplate = "{{ route('get.form.detail', ['id' => ':id']) }}";

            // Cache untuk menyimpan inputan jika ddilakukan render
            let qtyCache = {};

            // === DataTable ===
            let table = $('#complainTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('get.complain.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'requester.name',
                        name: 'requester.name',
                        render: function (data, type, row) {
                            return (row.requester && row.requester.name) ? row.requester.name : '-';
                        }
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id',
                        render: function (data, type, row) {
                            return (row.customer && row.customer.name) ? row.customer.name : '-';
                        }
                    },
                    {
                        data: 'cost_center',
                        name: 'cost_center',
                        render: function (data, type, row) {
                            if (data) {
                                let formatted = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(data);
                                return formatted;
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'request_date',
                        name: 'request_date',
                        render: function (data, type, row) {
                            if (!data) return '-';
                            const d = new Date(data);
                            if (isNaN(d.getTime())) return '-';
                            // Format DD/MM/YYYY
                            return String(d.getDate()).padStart(2,'0') + '/' +
                                String(d.getMonth() + 1).padStart(2,'0') + '/' +
                                d.getFullYear();
                        }
                    },
                    {
                        data: 'route_to',
                        name: 'route_to'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            if (data == 'Pending') {
                                return '<span class="badge status-badge-lg bg-warning text-dark">Pending</span>';
                            } else if (data == 'In Progress') {
                                return '<span class="badge status-badge-lg bg-info text-white">In Progress</span>';
                            } else if (data == 'Completed' || data == 'Success') {
                                return '<span class="badge status-badge-lg bg-success">Completed</span>';
                            } else if (data == 'Rejected' || data == 'Failed') {
                                return '<span class="badge status-badge-lg bg-danger">Rejected</span>';
                            } else {
                                return '<span class="badge status-badge-lg bg-secondary">' + data + '</span>';
                            }
                        }
                    },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            console.info(data);
                            let editUrl = `/complain/${data}/edit`;
                            let status = (row.status || '').toLowerCase();
                            let editButton = (status === 'pending')
                                ? `<a href="${editUrl}" class="btn btn-secondary btn-sm" title="Edit Data"><i class="ph-duotone ph-eraser"></i></a>`
                                : '';
                            return `
                                <button type="button" class="btn btn-info btn-sm detail-button" data-id="${data}" title="Lihat Detail">
                                    <i class="ph-duotone ph-info"></i>
                                </button>
                                ${editButton}
                            `;
                        }
                    }
                ]
            });

            let searchInput = $('#complainTable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });


            // detail trigger
            $('#complainTable tbody').on('click', '.detail-button', function () {
                let complainId = $(this).data('id');
                let finalUrl = detailUrlTemplate.replace(':id', complainId);

                $('#detailModal').modal('show');

                $.ajax({
                    url: finalUrl,
                    method: 'GET',
                    success: function (data) {
                        $('#detail_customer_name').val(data.customer ? data.customer.name : '-');
                        $('#detail_customer_address').val(data.customer ? data.customer.address : '-');
                        $('#detail_account').val(data.account);
                        $('#detail_cost_center').val(data.cost_center);
                        $('#detail_rs_number').val(data.no_srs);
                        $('#detail_date').val(data.request_date);
                        $('#detail_objectives').val(data.objectives);

                        const selectedProductsDiv = $('#requisition_product_list');
                        const items = data.requisition_items;

                        selectedProductsDiv.empty(); 

                        if (items && items.length > 0) {
                            const uniqueMastersMap = new Map();

                            items.forEach(item => {
                                if (item.item_master) {
                                    uniqueMastersMap.set(item.item_master.id, item.item_master);
                                }
                            });

                            const uniqueMasters = Array.from(uniqueMastersMap.values());
                            let productListHtml = '<ul class="list-unstyled mb-0">';

                            // Gunakan .map() untuk iterasi dan ambil nama item master
                            uniqueMasters.forEach(function (master) {
                                const masterName = `${master.item_master_code} - ${master.item_master_name}`;
                                productListHtml += `<li><i class="ph-duotone ph-package text-primary me-1"></i>${masterName}</li>`;
                            });

                            productListHtml += '</ul>';
                            selectedProductsDiv.html(productListHtml);

                        } else {
                            // Jika tidak ada produk, tampilkan pesan placeholder
                            selectedProductsDiv.html('<span class="text-muted">No products selected.</span>');
                        }

                        renderDetailProductTable(data.requisition_items);
                    },
                    error: function (xhr) {
                        errorMessage(xhr.responseJSON?.message || 'Failed to load complain details.');
                        $('#detailModal').modal('hide');
                    }
                });
            });

            // modal detail
            function renderDetailProductTable(items) {
                const container = $('#detail_productDetailsContainer');
                container.empty();

                if (!items || items.length === 0) {
                    container.html('<p class="text-muted">No products found for this requisition.</p>');
                    return;
                }

                let tableRowsHTML = items.map(item => {
                    const allDetails = item.item_master ? item.item_master.item_details : [];

                    const specificDetail = allDetails.find(detail => detail.id === item.item_detail_id);

                    if (!specificDetail) {
                        return `<tr><td colspan="6" class="text-center text-danger">Product Detail with ID ${item.item_detail_id} not found.</td></tr>`;
                    }

                    return `
                        <tr>
                            <td>${specificDetail.material_type || '-'}</td>
                            <td>${specificDetail.item_detail_code || '-'}</td>
                            <td>${specificDetail.item_detail_name || '-'}</td>
                            <td>${specificDetail.unit || '-'}</td>
                            <td><input type="number" class="form-control" value="${item.quantity_required}" readonly></td>
                            <td><input type="number" class="form-control" value="${item.quantity_issued}" readonly></td>
                        </tr>
                    `;
                }).join('');

                const tableHTML = `
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Tipe Material</th>
                                <th>Kode Detail</th>
                                <th>Nama Detail</th>
                                <th>Unit</th>
                                <th>QTY Required</th>
                                <th>QTY Issued</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRowsHTML}
                        </tbody>
                    </table>
                `;
                container.html(tableHTML);
            }
            // === Modal Create ===
            $('#btn-create-compline').on('click', function() {
                $('#complineForm')[0].reset();
                $('#requisition_items').empty();
                $('#product-detail').remove();
                $('[data-error-for]').text('');
                $('#complineForm .is-invalid').removeClass('is-invalid');



                var today = new Date().toISOString().split('T')[0];
                $('#date').val(today);

                
                // menarik data costumer dari server
                $.ajax({
                    url: "{{ route('customers.list') }}",
                    method: "GET",
                    success: function(data) {

                        customerSelect.empty();
                        customerSelect.append('<option value=""></option>');
                        data.forEach(function(customer) {
                            customerSelect.append('<option value="' + customer.id + '">' + customer.name + '</option>');
                        });

                        customerSelect.select2({
                            theme: "bootstrap-5",
                            placeholder: "Pilih atau cari customer",
                            allowClear: true,
                            dropdownParent: $('#complineModal')
                        });

                        customerSelect.on('change', function() {
                            let selectedCustomer = $(this).val();
                            if (!selectedCustomer) {
                                addressField.val('');
                                return;
                            }
                            let selectedAddress = data.find(c => c.id == selectedCustomer)?.address || '';
                            addressField.val(selectedAddress);
                            
                        });
                    },
                    error: function() {
                        errorMessage('Failed to load customers');
                    }
                });

                // menarik data account dan serial number dari server
                $.ajax({
                    url: "{{ route('get.serial') }}",
                    method: "GET",
                    success: function(data) {
                        // menampilkan nomor account dan serial number di modal
                        $('#account_display').text(data.account_number);
                        $('#account').val(data.account_number);
                        $('#rs_number_display').text(data.series_number);
                        $('#rs_number').val(data.series_number);
                    },
                    error: function() {
                        $('#rs_number_display').text('serial number gagal dibuat');
                        errorMessage('Failed to generate RS number');
                    }
                });

                // menarik data product dari server
                $.ajax({
                    url: "{{ route('get.product.list') }}",
                    method: "GET",
                    success: function(data) {
                        allProductData = data;

                        productselect.empty();
                        productselect.append('<option value=""></option>');
                        data.items.forEach(function (item) {
                            productselect.append('<option value="' + item.id + '">' + item.item_master_code + ' - ' + item.item_master_name + '</option>');
                        });

                        productselect.select2({
                            theme: "bootstrap-5",
                            placeholder: "Pilih atau cari produk",
                            allowClear: true,
                            dropdownParent: $('#complineModal'),
                            width: '100%'
                        });

                        function renderProductDetails() {
                            const selectedProductIds = productselect.val();
                            const selectedTypes = $('input.material-type-filter:checked')
                                .map(function () { return this.value; }).get();
                            const detailsContainer = $('#productDetailsContainer');

                            // Simpan nilai input sebelumnya kalo ada
                            detailsContainer.find('input[name$="[qty_required]"], input[name$="[qty_issued]"]').each(function () {
                                qtyCache[$(this).attr('name')] = $(this).val();
                            });

                            detailsContainer.empty();

                            if (!selectedProductIds || selectedProductIds.length === 0) {
                                return;
                            }

                            let tableRowsHTML = '';

                            selectedProductIds.forEach(function (productId) {
                                const selectedProduct = allProductData.items.find(item => item.id == productId);
                                if (!selectedProduct || !selectedProduct.item_details.length) return;

                                // Filter data
                                const filteredDetails = selectedProduct.item_details.filter(d =>
                                    selectedTypes.length === 0 || selectedTypes.includes(d.material_type)
                                );

                                if (filteredDetails.length > 0) {
                                    tableRowsHTML += filteredDetails.map(detail => {
                                        const rqName = `items[${productId}][details][${detail.id}][qty_required]`;
                                        const isName = `items[${productId}][details][${detail.id}][qty_issued]`;
                                        return `
                                            <tr>
                                                <td>${detail.material_type}</td>
                                                <td>${detail.item_detail_code}</td>
                                                <td>${detail.item_detail_name}</td>
                                                <td>${detail.unit}</td>
                                                <td>
                                                    <input 
                                                        type="number" 
                                                        class="form-control" 
                                                        name="${rqName}" 
                                                        placeholder="0"
                                                        value="${qtyCache[rqName] ?? ''}">
                                                    <div data-error-for="${rqName}" class="text-danger mt-1 error-message"></div>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="number" 
                                                        class="form-control" 
                                                        name="${isName}" 
                                                        placeholder="0"
                                                        value="${qtyCache[isName] ?? ''}">
                                                    <div data-error-for="${isName}" class="text-danger mt-1 error-message"></div>
                                                </td>
                                            </tr>
                                        `;
                                    }).join(''); // Gabungkan semua baris menjadi satu string HTML
                                } else {
                                    // data dengan filter tidak ditemukan
                                    tableRowsHTML += `
                                        <tr>
                                            <td colspan="6" class="bg-light text-danger text-center">
                                                Tidak ada material tipe <strong>${selectedTypes.join(", ")}</strong> pada produk <strong>${selectedProduct.item_master_code}</strong>
                                            </td>
                                        </tr>
                                    `;
                                }
                            });

                            const tableHTML = `
                                <table class="table table-bordered table-striped" id="product-detail">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Tipe Material</th>
                                            <th>Kode Detail</th>
                                            <th>Nama Detail</th>
                                            <th>Unit</th>
                                            <th>QTY Required</th>
                                            <th>QTY Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRowsHTML}
                                    </tbody>
                                </table>
                            `;

                            detailsContainer.html(tableHTML);
                        }
                        productselect.on('change', renderProductDetails);

                        materialtype.on('change', renderProductDetails);

                    },
                    error: function() {
                        productselect.append('<option>produk gagal dimuat</option>');
                        errorMessage('Failed to fetch product list');
                    }
                })
            });

            // === Submit Form ===
            $('#complineForm').on('submit', function (e) {
                e.preventDefault();

                $('#complineForm .is-invalid').removeClass('is-invalid');
                $('.select2-selection.is-invalid').removeClass('is-invalid'); // Khusus untuk select2
                $('[data-error-for]').text('');

                let mode = $(this).attr('data-mode');
                let userId = $(this).attr('data-id');
                let url, method;

                if (mode === 'create') {
                    url = "{{ route('complain-form.store') }}";
                    method = "POST";
                } else {
                    url = "{{ url('users') }}/" + userId;
                    method = "POST"; // tetap POST, override pakai _method
                }

                let formData = new FormData(this);
                if (mode === 'edit') {
                    formData.append('_method', 'PUT'); // override
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $('#complineModal').modal('hide');
                        $('#complainTable').DataTable().ajax.reload(null, false);
                        qtyCache = {}
                        successMessage((mode === 'create') ? res.message :
                            'Complain updated successfully');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) { // Unprocessable Entity -> Error Validasi
                            let errors = xhr.responseJSON.errors;

                            for (let key in errors) {

                                if (key === 'items') {

                                    productselect.next('.select2-container').find('.select2-selection').addClass('is-invalid');

                                    let errorContainer = $('[data-error-for="requisition_items"]');
                                    if (errorContainer.length) {
                                        errorContainer.text(errors[key][0]);
                                    }
                                } else {
                                    let parts = key.split('.');
                                    let fieldName = parts[0] + parts.slice(1).map(part => `[${part}]`).join('');

                                    let input = $(`[name="${fieldName}"]`);
                                    let errorContainer = $(`[data-error-for="${fieldName}"]`);

                                    if (input.length) {
                                        if (input.hasClass('select2-hidden-accessible')) {
                                            input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                        } else {
                                            input.addClass('is-invalid');
                                        }
                                    }
                                    if (errorContainer.length) {
                                        errorContainer.text(errors[key][0]);
                                    }
                                }
                            }

                            errorMessage('Lengkapi data yang diperlukan');
                        } else {
                            errorMessage(xhr.responseJSON?.message || 'Terjadi kesalahan pada server.');
                        }
                    }
                });
            });

            // === SweetAlert Delete ===
            $(document).on('click', '.delete-user-btn', function(e) {
                e.preventDefault();
                const btn = $(this);
                confirmDialog({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    icon: 'warning',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: btn.closest('form').attr('action'),
                            method: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                $('#users-table').DataTable().ajax.reload(null, false);
                                successMessage(res.message || 'Complain deleted successfully!');
                            },
                            error: function(xhr) {
                                errorMessage(xhr.responseJSON?.message || 'Failed to delete complain');
                            }
                        });
                    } else {
                        warningMessage('Complain deletion canceled');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>