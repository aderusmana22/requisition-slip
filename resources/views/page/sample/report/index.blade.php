<x-app-layout>
    @section('title')
        Approval Report
    @endsection

    {{-- Include Complaint Table Styles Template --}}
    @include('components.sample-table-styles')

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Approval Report</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Approval Report</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar -->
            <div class="filter-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted flex-shrink-0 fw-bold">Filter by date:</label>

                        <select id="dateFilter" class="form-select select2-styled" style="width:200px;">
                            <option value="all">All time</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last_30_days">Last 30 days</option>
                            <option value="custom">Custom range</option>
                        </select>
                        <!-- Hidden inputs keep selected custom dates persistent outside the Select2 dropdown -->
                        <input type="hidden" id="startDate" name="startDate" />
                        <input type="hidden" id="endDate" name="endDate" />

                        <button id="resetDateFilter" class="btn btn-secondary border" data-bs-toggle="tooltip" title="Reset Filters">
                            <i class="ph-bold ph-arrow-counter-clockwise"></i>
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-success" type="button" onclick="printSelectedReports()" id="printSelectedBtn" disabled>
                            <i class="ph-bold ph-printer"></i>
                            <span>Print Selected (<span id="selectedCount">0</span>)</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                 <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list-checks"></i></i>
                        Requisition List for Printing
                    </h4>
                    <p class="table-subtitle">Select requisitions to print in a batch.</p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>No SRS</th>
                                <th>Requester</th>
                                <th>Request Date</th>
                                <th>Sub Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
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
                                    <p class="view-data" id="view_objectives">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Estimated Potential</small>
                                    <p class="view-data" id="view_estimated_potential">-</p>
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
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="material-type-column">Material Type</th>
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
                           <h5 class="fw-bold text-primary mb-3">Special Order Details (Marketing)</h5>
                        </div>
                        <div class="card-body">
                             <div class="row g-4">
                                <div class="col-md-4"><label class="text-muted">Sample Completion Date</label><p class="fs-6 fw-semibold" id="view_requested_date">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Sample Weight</label><p class="fs-6 fw-semibold" id="view_weight_selection">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Sample Packaging</label><p class="fs-6 fw-semibold" id="view_packaging_selection">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Number of Samples</label><p class="fs-6 fw-semibold" id="view_sample_count">-</p></div>
                                <div class="col-md-4"><label class="text-muted">COA Required?</label><p class="fs-6 fw-semibold" id="view_coa_required">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Shipment Method</label><p class="fs-6 fw-semibold" id="view_shipment_method">-</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="card view-modal-card" id="view-qa-section" style="display: none;">
                        <div class="card-header view-modal-card-header">
                           <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-test-tube me-2"></i> QA/QM Details</h5>
                        </div>
                        <div class="card-body p-4">
                             <div class="row g-4">
                                <div class="col-md-4">
                                    <small class="view-label">Asal Sample</small>
                                    <p class="view-data" id="view_source">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Tanggal Produksi</small>
                                    <p class="view-data" id="view_production_date">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Persiapan Sample</small>
                                    <p class="view-data" id="view_preparation_method">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Keterangan Sample</small>
                                    <p class="view-data" id="view_description">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Keterangan Tambahan</small>
                                    <p class="view-data fst-italic" id="view_sample_notes">-</p>
                                </div>
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
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-clock-counter-clockwise me-2"></i> Requisition History</h5>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush" id="history-log-container">
                                {{-- History akan diisi oleh JavaScript di sini --}}
                            </ul>
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
    <!-- SweetAlert -->
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

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

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function truncateText(text, maxLength = 100) {
            if (!text) return '-';
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }


        function updateSelectedCount() {
            const selectedCheckboxes = $('.row-selector:checked');
            const count = selectedCheckboxes.length;

            $('#selectedCount').text(count);
            $('#printSelectedBtn').prop('disabled', count === 0);

            const totalCheckboxes = $('.row-selector').length;
            const selectAllCheckbox = $('#selectAll');

            if (count === 0) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', false);
            } else if (count === totalCheckboxes) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true);
                selectAllCheckbox.prop('checked', false);
            }
        }


        function printSelectedReports() {
            const selectedIds = [];
            $('.row-selector:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                errorMessage('Please select at least one report to print.');
                return;
            }

            Swal.fire({
                title: 'Print Selected Reports',
                text: `Are you sure you want to print ${selectedIds.length} selected report(s)?`,
                icon: 'question',
                showRecallButton: true,
                confirmButtonColor: '#28a745',
                recallButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Print!',
                recallButtonText: 'Recall'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Preparing reports for printing...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route("report_sample.print") }}',
                        target: '_blank',
                        style: 'display: none;'
                    });

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: '{{ csrf_token() }}'
                    }));

                    selectedIds.forEach(function(id) {
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'selected_ids[]',
                            value: id
                        }));
                    });

                    console.log('Sending selected IDs:', selectedIds);
                    console.log('Form action:', '{{ route("report_sample.print") }}');

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'print_type',
                        value: 'bulk'
                    }));

                    $('body').append(form);

                    try {
                        form.submit();

                        setTimeout(() => {
                            Swal.close();
                            successMessage(`${selectedIds.length} report(s) sent for printing!`);

                            $('.row-selector').prop('checked', false);
                            $('.selected-row').removeClass('selected-row');
                            $('#selectAll').prop('checked', false).prop('indeterminate', false);
                            updateSelectedCount();
                        }, 1500);

                    } catch (error) {
                        console.error('Form submission error:', error);
                        Swal.close();
                        errorMessage('Error submitting form. Please try again.');
                    } finally {
                        form.remove();
                    }
                }
            });
        }

        function formatStepName(name) {
            if (!name) return 'Unknown';
            return name
                .replace(/-/g, ' ')
                .replace(/\b\w/g, char => char.toUpperCase());
        }

        $(document).ready(function () {
            $('#dateFilter').select2({
                width: 'style',
                placeholder: 'Select Filter',
                allowClear: true,
            });

            function renderCustomDateControls() {
                return `
                    <div class="p-2 custom-date-controls">
                        <label class="form-label mb-1 small">Start</label>
                        <input type="date" id="select2_startDate" class="form-control form-control-sm mb-2" />
                        <label class="form-label mb-1 small">End</label>
                        <input type="date" id="select2_endDate" class="form-control form-control-sm" />
                        <div class="text-end mt-2"><button type="button" id="select2_apply_dates" class="btn btn-sm btn-primary">Apply</button></div>
                    </div>
                `;
            }

            $('#dateFilter').on('select2:open', function(e) {
                const val = $(this).val();
                const $dropdown = $('.select2-container--open .select2-dropdown');
                $dropdown.find('.custom-date-controls').remove();

                if (val === 'custom') {
                    $dropdown.append(renderCustomDateControls());
                    const s = $('#startDate').val();
                    const en = $('#endDate').val();
                    if (s) $('#select2_startDate').val(s);
                    if (en) $('#select2_endDate').val(en);

                    $('#select2_apply_dates').on('click', function() {
                        const s2 = $('#select2_startDate').val();
                        const e2 = $('#select2_endDate').val();
                        if (!s2 || !e2) {
                            errorMessage('Please select both start and end dates.');
                            return;
                        }
                        $('#startDate').val(s2);
                        $('#endDate').val(e2);
                        $('#dateFilter').select2('close');
                        table.ajax.reload(null, false);
                    });
                }
            });

            $('#dateFilter').on('change', function() {
                const val = $(this).val();
                if (val === 'custom') {
                    setTimeout(function() {
                        $('#dateFilter').select2('open');
                    }, 50);
                    return;
                }

                $('#startDate, #endDate').val('');
                table.ajax.reload(null, false);
            });

            if (!$('#sampleTable').length) {
                console.error('Table element not found!');
                errorMessage('Table initialization failed: Element not found');
                return;
            }

            function getDateFilterPayload() {
                const filter = $('#dateFilter').val();
                const payload = {};
                if (filter === 'yesterday') {
                    const yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 1);
                    const iso = yesterday.toISOString().split('T')[0];
                    payload.start_date = iso;
                    payload.end_date = iso;
                } else if (filter === 'last_30_days') {
                    const end = new Date();
                    const start = new Date();
                    start.setDate(end.getDate() - 30);
                    payload.start_date = start.toISOString().split('T')[0];
                    payload.end_date = end.toISOString().split('T')[0];
                } else if (filter === 'custom') {
                    const s = $('#startDate').val();
                    const e = $('#endDate').val();
                    if (s && e) {
                        payload.start_date = s;
                        payload.end_date = e;
                    }
                }
                return payload;
            }

            let table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sample.reports.data') }}",
                    type: 'GET',
                    data: function(d) {
                        return $.extend({}, d, getDateFilterPayload());
                    },
                    error: function(xhr, error, code) {
                        if (error === 'abort') return;
                        console.error('DataTable AJAX Error:', error, code);
                        errorMessage('Failed to load data from server.');
                    }
                },
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        width: '4%',
                        render: function (data, type, row, meta) {
                            return `<input type="checkbox" class="row-selector form-check-input" value="${data}">`;
                        }
                    },
                    {
                        data: 'no_srs',
                        name: 'requisitions.no_srs',
                        width: '15%',
                        render: function (data) { return data ? `<code class="small"><strong>${data}</strong></code>` : '-'; }
                    },
                    {
                        data: 'requester',
                        name: 'users.name',
                        width: '15%',
                        render: function (data) {
                            return (data && data.name) ? `<span class="fw-medium">${data.name}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'requisitions.created_at',
                        width: '12%',
                        render: function (data) { return formatDate(data); }
                    },
                    {
                        data: 'sub_category',
                        name: 'requisitions.sub_category',
                        width: '12%',
                        render: function (data, type, row) {
                            const sub = (data || (row && row.sub_category) || '').toString().trim();
                            let badgeClass = 'bg-dark';
                            let icon = 'ph-tag';
                            let label = sub || '-';

                            switch (sub) {
                                case 'Packaging':
                                    badgeClass = 'bg-info';
                                    icon = 'ph-package';
                                    break;
                                case 'Finished Goods':
                                    badgeClass = 'bg-primary';
                                    icon = 'ph-cube';
                                    break;
                                case 'Special Order':
                                    badgeClass = 'bg-secondary';
                                    icon = 'ph-star';
                                    break;
                            }

                            return `<span class="status-badge-lg ${badgeClass}"><i class="ph-bold ${icon} me-1"></i>${label}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'requisitions.status',
                        width: '10%',
                        render: function (data, type, row) {
                            const statusRaw = data || '';
                            const status = statusRaw.toString().trim().toLowerCase();

                            let badgeClass = 'bg-secondary';
                            let icon = 'ph-question';
                            let label = statusRaw || '-';

                            switch (status) {
                                case 'pending':
                                case 'submitted':
                                    badgeClass = 'bg-primary';
                                    icon = 'ph-paper-plane-tilt';
                                    label = 'Pending';
                                    break;
                                case 'in progress':
                                case 'processing':
                                    badgeClass = 'bg-info';
                                    icon = 'ph-arrows-clockwise';
                                    label = 'In Progress';
                                    break;
                                case 'approved':
                                case 'completed':
                                    badgeClass = 'bg-success';
                                    icon = 'ph-check-circle';
                                    label = 'Approved';
                                    break;
                                case 'rejected':
                                    badgeClass = 'bg-danger';
                                    icon = 'ph-x-circle';
                                    label = 'Rejected';
                                    break;
                                case 'Recalled':
                                    badgeClass = 'bg-secondary';
                                    icon = 'ph-ban';
                                    label = 'Recalled';
                                    break;
                                default:
                                    badgeClass = 'bg-secondary';
                                    icon = 'ph-question';
                                    label = statusRaw;
                            }

                            return `<span class="badge ${badgeClass} status-badge-lg"><i class="ph-bold ${icon} me-1"></i>${label}</span>`;
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '8%',
                        render: function (data, type, row) {
                            return `
                                <div class="action-btn-group">
                                    <button type="button" class="status-badge-lg btn-info btn-view-requisition" data-id="${row.id}" title="Show Detail">
                                        <i class="ph-duotone ph-eye"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[3, 'desc']],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: "Loading data...",
                    emptyTable: "No data available",
                    zeroRecords: "No matching records found"
                },
                drawCallback: function() {
                    initActionTooltips();
                    updateSelectedCount();
                }
            });

            $('#resetDateFilter').on('click', function() {
                $('#dateFilter').val('all').trigger('change');
                $('#startDate, #endDate').val('');
                table.ajax.reload(null, false);
            });

            let searchInput = $('#sampleTable_filter input');
            searchInput.unbind();
            let debounceTimer;

            searchInput.on('keyup search input', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });

            $('#selectAll').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.row-selector').prop('checked', isChecked);

                $('#sampleTable tbody tr').each(function() {
                    if (isChecked) {
                        $(this).addClass('selected-row');
                    } else {
                        $(this).removeClass('selected-row');
                    }
                });

                updateSelectedCount();
            });

            table.on('draw', function() {
                updateSelectedCount();
            });

            $('#sampleTable tbody').on('change', '.row-selector', function() {
                const row = $(this).closest('tr');
                if ($(this).is(':checked')) {
                    row.addClass('selected-row');
                } else {
                    row.removeClass('selected-row');
                }
                updateSelectedCount();
            });

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
                const viewTable = viewItemTbody.closest('table');
                viewItemTbody.empty();
                const isPackaging = data.sub_category === 'Packaging';
                viewTable.find('th.material-type-column').toggle(isPackaging);
                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';
                        if (item.item_detail) {
                            itemCode = item.item_detail.item_detail_code;
                            itemName = item.item_detail.item_detail_name;
                            unit = item.item_detail.unit;
                        } else if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                        }
                        const materialTypeCell = isPackaging ? `<td class="material-type-column">${item.material_type}</td>` : '';
                        const newRow = `<tr>${materialTypeCell}<td>${itemCode}</td><td>${itemName}</td><td>${unit}</td><td class="text-center">${item.quantity_required}</td><td class="text-center">${item.quantity_issued || '-'}</td></tr>`;
                        viewItemTbody.append(newRow);
                    });
                } else {
                    const colspan = isPackaging ? 6 : 5;
                    viewItemTbody.html(`<tr><td colspan="${colspan}" class="text-center">No items have been added.</td></tr>`);
                }

                const specialOrderSection = $('#view-special-order-section');
                const qaSection = $('#view-qa-section');
                if (data.sub_category === 'Special Order' && data.requisition_special) {
                    const special = data.requisition_special;
                    $('#view_requested_date').text(special.requested_date ? new Date(special.requested_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');
                    $('#view_weight_selection').text(special.weight_selection || '-');
                    $('#view_packaging_selection').text(special.packaging_selection || '-');
                    $('#view_sample_count').text(special.sample_count || '-');
                    $('#view_shipment_method').text(special.shipment_method || '-');
                    $('#view_coa_required').text(special.coa_required == 1 ? 'Yes' : 'No');
                    specialOrderSection.show();
                    if (special.source) {
                        $('#view_source').text(special.source || '-');
                        $('#view_description').text(special.description || '-');
                        $('#view_production_date').text(special.production_date ? new Date(special.production_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');
                        $('#view_preparation_method').text(special.preparation_method || '-');
                        $('#view_sample_notes').text(special.sample_notes || '-');
                        qaSection.show();
                    } else {
                        qaSection.hide();
                    }
                } else {
                    specialOrderSection.hide();
                    qaSection.hide();
                }

                const status = data.status;
                let badgeClass = 'bg-secondary';
                if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-primary';
                else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                else if (['Rejected', 'Recalled'].includes(status)) badgeClass = 'bg-danger';
                else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-info';
                $('#view_status_badge').html(`<span class="badge fs-6 rounded-pill ${badgeClass}">${status}</span>`);

                const trackerContainer = $('#approval-tracker-container');
                trackerContainer.empty();

                let steps = [{ id: 'submitted', label: 'Request Submit', icon: 'ph-file-arrow-up' }];

                if (data.sequence_approvers) {
                    data.sequence_approvers.forEach((role, index) => {
                        const level = index + 1;
                        const stepTitle = (role === 'atasan') ? 'Atasan Dept' : 'Bisnis Controller';
                        steps.push({ id: `approver_${level}`, label: stepTitle, icon: 'ph-user' });
                    });
                }

                let trackingSteps = [];

                if (data.sequence_tracking && data.status !== 'Rejected' && data.status !== 'Recalled') {
                    data.sequence_tracking.forEach((stepName, index) => {
                        let icon = 'ph-package';
                        const stepNameLower = stepName.toLowerCase();

                        if (stepNameLower.includes('material')) icon = 'ph-printer';
                        if (stepNameLower.includes('outward')) icon = 'ph-truck';
                        if (stepNameLower.includes('qa') || stepNameLower.includes('qm')) icon = 'ph-clipboard-text';

                        const stepId = `tracking_${index}`;

                        steps.push({ id: stepId, label: formatStepName(stepName), icon: icon });
                        trackingSteps.push(stepId);
                    });
                }

                if (data.status !== 'Rejected' && data.status !== 'Recalled') {
                    steps.push({ id: 'completed', label: 'Completed', icon: 'ph-check-circle' });
                }

                let trackerHtml = '<div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>';
                steps.forEach(step => {
                    trackerHtml += `<div class="tracker-step" data-step-id="${step.id}"><div class="tracker-icon"><i class="ph-bold ${step.icon} fs-6"></i></div><div class="tracker-label">${step.label}</div><div class="tracker-details"></div></div>`;
                });
                trackerContainer.html(trackerHtml);

                let lastCompletedIndex = -1;
                const isRejected = ['Rejected', 'Recalled'].includes(data.status);

                if (data.requester && data.created_at) {
                    const submittedStep = $(`.tracker-step[data-step-id="submitted"]`);
                    const creationDate = new Date(data.created_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                    submittedStep.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${data.requester.name}</div><div class="tracker-date text-dark">${creationDate}</div>`);
                    lastCompletedIndex = 0;
                }

                if (data.approval_logs) {
                    data.approval_logs.forEach(log => {
                        const stepElement = $(`.tracker-step[data-step-id="approver_${log.level}"]`);
                        if (stepElement.length > 0) {
                            if (log.status === 'Approved') {
                                const approvalDate = new Date(log.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                stepElement.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${log.approver.name}</div><div class="tracker-date text-dark">${approvalDate}</div>`);
                                const stepIndex = steps.findIndex(s => s.id === `approver_${log.level}`);
                                lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                            } else if (log.status === 'Rejected') {
                                const rejectionDate = new Date(log.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                stepElement.addClass('rejected').find('.tracker-details').html(`<div class="tracker-user text-danger">${log.approver.name}</div><div class="tracker-date text-dark">${rejectionDate}</div>`);
                            }
                        }
                    });
                }

                if (data.trackings && data.trackings.length > 0) {
                    data.trackings.forEach((tracking, index) => {
                        if (tracking.last_updated && new Date(tracking.last_updated).getFullYear() > 1970) {

                            const stepId = trackingSteps[index];

                            if (stepId) {
                                const stepElement = $(`.tracker-step[data-step-id="${stepId}"]`);
                                const stepLabel = stepElement.find('.tracker-label').text();

                                let userName = tracking.current_position;
                                if (stepLabel.toLowerCase().includes('qa') || stepLabel.toLowerCase().includes('qm')) {
                                    userName = 'QA/QM HSE Team';
                                }

                                const completionDate = new Date(tracking.last_updated).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                stepElement.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${userName}</div><div class="tracker-date text-dark">${completionDate}</div>`);

                                const stepIndex = steps.findIndex(s => s.id === stepId);
                                lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                            }
                        }
                    });
                }

                if (data.status === 'Completed') {
                    const completedStep = $(`.tracker-step[data-step-id="completed"]`);
                    const completionDate = new Date(data.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                    completedStep.find('.tracker-details').html(`<div class="tracker-user text-primary">${data.requester.name}</div><div class="tracker-date text-dark">${completionDate}</div>`);
                    $('.tracker-step').addClass('completed');
                    lastCompletedIndex = steps.length - 1;
                } else if (data.status === 'Recalled') {
                    const submittedStep = $(`.tracker-step[data-step-id="submitted"]`);
                    const recallDate = new Date(data.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                    submittedStep.addClass('rejected').find('.tracker-details').html(`<div class="tracker-user text-danger">${data.requester.name}</div><div class="tracker-date text-dark">${recallDate}</div>`);
                } else if (!isRejected) {
                    const nextStepIndex = lastCompletedIndex + 1;
                    if (nextStepIndex < steps.length) {
                        const activeStepElement = trackerContainer.find('.tracker-step').eq(nextStepIndex);
                        activeStepElement.addClass('active');

                        if (data.route_to) {
                            activeStepElement.find('.tracker-details').html(
                                `<div class="tracker-user" style="color: #ffc107; font-weight: 500;">
                                    <i class="ph-bold ph-arrow-circle-right me-1"></i>Processed by
                                </div>
                                <div class="tracker-date text-dark">${data.route_to}</div>`
                            );
                        }
                    }
                }

                if (lastCompletedIndex >= 0 && !isRejected) {
                    let progressPercentage = (lastCompletedIndex / (steps.length - 1)) * 100;
                    $('#tracker-progress').css('width', progressPercentage + '%');
                }

                const historyContainer = $('#history-log-container');
                historyContainer.empty();
                if (data.history && data.history.length > 0) {
                    data.history.forEach(log => {
                        let badgeClass = 'badge-created', avatarClass = 'avatar-created';
                        const action = log.action.toLowerCase();
                        if (action.includes('approved not review')) { badgeClass = 'badge-approved'; avatarClass = 'avatar-approved'; }
                        else if (action.includes('approved with review')) { badgeClass = 'badge-review'; avatarClass = 'avatar-review'; }
                        else if (action.includes('rejected') || action.includes('recalled')) { badgeClass = 'badge-rejected'; avatarClass = 'avatar-rejected'; }
                        else if (action.includes('completed step')) { badgeClass = 'badge-process'; avatarClass = 'avatar-process'; }
                        let avatarHtml = '', actorInitial = log.actor ? log.actor.charAt(0).toUpperCase() : '?';
                        if (log.avatar) {
                            avatarClass += ' has-image';
                            avatarHtml = `<img src="${log.avatar}" alt="${actorInitial}">`;
                        } else {
                            avatarHtml = actorInitial;
                        }
                        const notesHtml = log.notes ? `<div class="history-notes">"${log.notes}"</div>` : '';
                        const logDate = new Date(log.timestamp).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                        const historyItem = `<li class="list-group-item history-item"><div class="history-avatar ${avatarClass}">${avatarHtml}</div><div class="history-content"><div class="history-actor">${log.actor}</div>${notesHtml}</div><div class="history-meta"><div class="history-badge ${badgeClass}">${log.action}</div><div class="history-timestamp">${logDate}</div></div></li>`;
                        historyContainer.append(historyItem);
                    });
                } else {
                    historyContainer.html('<li class="list-group-item">No history data available.</li>');
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

            function initActionTooltips() {
                $(document).off('mouseenter.customTooltip mouseleave.customTooltip', '.action-btn-hover');

                $(document).on('mouseenter.customTooltip', '.action-btn-hover', function (e) {
                    const tooltipText = $(this).attr('data-tooltip');
                    if (tooltipText && !$(this).data('tooltip-element')) {
                        const tooltip = $('<div class="action-tooltip">' + tooltipText + '</div>');
                        $('body').append(tooltip);

                        const button = $(this);
                        let isDestroyed = false;

                        function updateTooltipPosition() {
                            if (isDestroyed || !button.is(':visible') || !tooltip.parent().length) {
                                return;
                            }

                            const buttonOffset = button.offset();
                            if (!buttonOffset) return;

                            const buttonWidth = button.outerWidth();
                            const buttonHeight = button.outerHeight();
                            const tooltipWidth = tooltip.outerWidth();
                            const tooltipHeight = tooltip.outerHeight();
                            const windowWidth = $(window).width();
                            const scrollTop = $(window).scrollTop();

                            let left = buttonOffset.left + (buttonWidth / 2) - (tooltipWidth / 2);
                            let top = buttonOffset.top - tooltipHeight - 12;

                            if (left < 10) {
                                left = 10;
                            } else if (left + tooltipWidth > windowWidth - 10) {
                                left = windowWidth - tooltipWidth - 10;
                            }

                            if (top < scrollTop + 10) {
                                top = buttonOffset.top + buttonHeight + 12;
                                tooltip.addClass('below');
                            } else {
                                tooltip.removeClass('below');
                            }

                            tooltip.css({
                                position: 'absolute',
                                left: left + 'px',
                                top: top + 'px',
                                zIndex: 9999
                            });
                        }

                        setTimeout(() => {
                            updateTooltipPosition();
                        }, 10);

                        setTimeout(() => {
                            if (!isDestroyed) {
                                tooltip.addClass('show');
                            }
                        }, 100);

                        button.data('tooltip-element', tooltip);

                        const tooltipId = 'tooltip_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        button.data('tooltip-id', tooltipId);

                        let scrollTimeout;
                        function throttledUpdate() {
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                            scrollTimeout = setTimeout(() => {
                                if (!isDestroyed) {
                                    updateTooltipPosition();
                                }
                            }, 10);
                        }

                        $(window).on('scroll.' + tooltipId + ' resize.' + tooltipId, throttledUpdate);

                        button.data('tooltip-cleanup', function () {
                            isDestroyed = true;
                            $(window).off('.' + tooltipId);
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                        });
                    }
                });

                $(document).on('mouseleave.customTooltip', '.action-btn-hover', function (e) {
                    const button = $(this);
                    const tooltip = button.data('tooltip-element');
                    const cleanup = button.data('tooltip-cleanup');

                    if (tooltip) {
                        tooltip.removeClass('show');
                        setTimeout(() => {
                            tooltip.remove();
                        }, 200);

                        if (cleanup) {
                            cleanup();
                        }

                        button.removeData('tooltip-element');
                        button.removeData('tooltip-id');
                        button.removeData('tooltip-cleanup');
                    }
                });
            }

            table.on('draw', function () {
                initActionTooltips();
            });

            setTimeout(function() {
                initActionTooltips();
            }, 1000);

            $('#sampleTable_filter input').attr({
                'placeholder': '🔍 Search sample...',
                'class': 'form-control'
            });

            $('.dataTables_wrapper').css({
                'animation': 'fadeInUp 0.8s ease-out forwards',
                'opacity': '0'
            });

            setTimeout(function () {
                $('.dataTables_wrapper').css('opacity', '1');
            }, 200);
        });
    </script>
    @endpush
</x-app-layout>
