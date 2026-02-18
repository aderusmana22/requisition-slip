<x-app-layout>
    @section('title')
        Free Goods Requisition Reports
    @endsection

    {{-- Memuat gaya tabel khusus Free Goods (Hijau) --}}
    @include('components.freegoods-table-styles')

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <style>
            /* Search Clear Icon */
            .search-container { position: relative; display: inline-block; width: 100%; }
            .search-clear-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #adb5bd; display: none; z-index: 10; transition: color 0.2s; font-size: 1rem; }
            .search-clear-icon:hover { color: #dc3545; }

            /* [CUSTOM STATUS COLORS] */
            .status-pending { background-color: #fd7e14 !important; color: #ffffff !important; border: 1px solid #fd7e14; }
            .status-processing { background-color: #8B4513 !important; color: #ffffff !important; border: 1px solid #8B4513; } /* Coklat/Bronze */
            .status-completed { background-color: #198754 !important; color: #ffffff !important; border: 1px solid #198754; }
            .status-rejected { background-color: #dc3545 !important; color: #ffffff !important; border: 1px solid #dc3545; }
            .status-default { background-color: #6c757d !important; color: #fff !important; }
        </style>
    @endpush

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Requisition Reports</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-chart-bar f-s-16"></i> Reports
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Free Goods Report</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="filter-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    {{-- Filter Date Range --}}
                    <div class="d-none d-md-flex align-items-center gap-2">
                        <label class="text-muted flex-shrink-0 fw-bold">Filter by Request Date:</label>
                        <div id="reportrange" class="form-control" style="width: 250px; cursor: pointer;">
                            <i class="ph-bold ph-calendar"></i>&nbsp;
                            <span></span> <i class="ph-bold ph-caret-down float-end mt-1"></i>
                        </div>
                        <input type="hidden" id="start_date">
                        <input type="hidden" id="end_date">
                        
                        <button id="btn-reset" class="btn btn-secondary border" data-bs-toggle="tooltip" title="Reset Filters">
                            <i class="ph-bold ph-arrow-counter-clockwise"></i>
                        </button>
                    </div>

                    {{-- Tombol Print Batch --}}
                    <div>
                        <form id="print-form" action="{{ route('freegoods.report.print.batch') }}" method="POST" target="_blank">
                            @csrf
                            <div id="hidden-ids-container"></div>
                            <button type="submit" id="print-selected-btn" class="btn btn-success" disabled>
                                <i class="ph-bold ph-printer me-1"></i>
                                <span>Print Selected (<span id="selected-count">0</span>)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list-checks"></i>
                        Free Goods List for Printing
                    </h4>
                    <p class="table-subtitle">Select requisitions to print in a batch.</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="freegoodsReportTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 20px;">
                                    <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                </th>
                                <th class="text-center">FG No.</th>
                                <th class="text-center">Requester</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Request Date</th>
                                <th class="text-center">Sub Category</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Free Goods Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">

                    {{-- CARD 1: MAIN REQUISITION DETAILS --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-success mb-3"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6"><small class="view-label">Category</small><p class="view-data">FREE GOODS</p></div>
                                <div class="col-md-6"><small class="view-label">Sub Category</small><p class="view-data" id="view_sub_category">-</p></div>
                                <div class="col-md-3"><small class="view-label">FG No.</small><p class="view-data" id="view_no_srs">-</p></div>
                                <div class="col-md-3"><small class="view-label">Request Date</small><p class="view-data" id="view_request_date">-</p></div>
                                <div class="col-md-3"><small class="view-label">Customer Name</small><p class="view-data" id="view_customer_name">-</p></div>
                                <div class="col-md-3"><small class="view-label">Address</small><p class="view-data" id="view_customer_address">-</p></div>
                                <div class="col-md-3"><small class="view-label">Account</small><p class="view-data" id="view_account">-</p></div>
                                <div class="col-md-3"><small class="view-label">Cost Center</small><p class="view-data" id="view_cost_center">-</p></div>
                                <div class="col-md-3"><small class="view-label">Objectives</small><p class="view-data" id="view_objectives">-</p></div>
                                <div class="col-md-3"><small class="view-label">Estimated Potential</small><p class="view-data" id="view_estimated_potential">-</p></div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: REQUESTED ITEM LIST --}}
                    <div class="card view-modal-card">
                         <div class="card-header">
                            <h5 class="fw-bold text-success mb-3"><i class="ph-bold  ph-list me-2"></i>Requested Item List</h5>
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
                                    <tbody id="view-items-tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 3: APPROVAL TRACKING --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-success mb-3"><i class="ph-bold ph-path me-2"></i> Approval & Process Tracking</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold me-3">Current Status:</span>
                                <div id="view_status_badge"></div>
                            </div>
                            <div class="tracker-container" id="approval-tracker-container">
                                <div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 4: HISTORY --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-success mb-3"><i class="ph-bold ph-clock-counter-clockwise me-2"></i> Requisition History</h5>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush" id="history-log-container"></ul>
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
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function errorMessage(message) {
            Swal.fire({ icon: 'error', title: 'Error', text: message });
        }

        $(document).ready(function () {
            // === Date Range Picker ===
            const initial_start = moment().subtract(29, 'days');
            const initial_end = moment();

            function cb(start, end) {
                if (!start || !end) {
                    $('#reportrange span').html('Filter by Request Date');
                    $('#start_date').val('');
                    $('#end_date').val('');
                } else {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#start_date').val(start.format('YYYY-MM-DD'));
                    $('#end_date').val(end.format('YYYY-MM-DD'));
                }
            }

            $('#reportrange').daterangepicker({
                startDate: initial_start,
                endDate: initial_end,
                ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                autoUpdateInput: false 
            }, cb);

            $('#reportrange span').html('Filter by Request Date');

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate);
                table.ajax.reload();
            });

            $('#btn-reset').on('click', function() {
                $('#reportrange span').html('Filter by Request Date');
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

            // === Data Table ===
            let table = $('#freegoodsReportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('freegoods.reports.data') }}",
                    type: 'GET',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    },
                    error: function(xhr, error, code) { console.error('DataTable Error:', error); }
                },
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle', // Center Checkbox
                        render: function (data) {
                            return `<input type="checkbox" class="requisition-checkbox form-check-input" value="${data}">`;
                        }
                    },
                    { data: 'no_srs', name: 'no_srs', className: 'align-middle text-center' },
                    { data: 'requester_info', name: 'requester.name', className: 'text-center align-middle' }, // Center Requester
                    { data: 'customer_name', name: 'customer.name', className: 'text-center align-middle' }, // Center Customer
                    { data: 'request_date', name: 'request_date', className: 'text-center align-middle' }, // Center Date
                    { data: 'sub_category', name: 'sub_category', className: 'text-center align-middle' }, // Center Category
                    { data: 'status', name: 'status', className: 'text-center align-middle' }, // Center Status
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle', // Center Actions
                        render: function (data, type, row) {
                            return `<button type="button" class="btn btn-sm btn-info btn-view-requisition" data-id="${row.id}"><i class="ph-duotone ph-eye"></i></button>`;
                        }
                    }
                ],
                order: [[4, 'desc']], 
                drawCallback: function() {
                    updateSelectedCount();
                }
            });

            // Search Logic
            const filterInput = $('#freegoodsReportTable_filter input');
            if (filterInput.parent().find('.search-container').length === 0) {
                filterInput.unbind();
                const wrapper = $('<div class="search-container"></div>');
                filterInput.wrap(wrapper);
                filterInput.attr({ 'placeholder': 'Search reports...', 'class': 'form-control ps-3 pe-5' });
                $('<i class="ph-bold ph-x search-clear-icon" title="Clear Search"></i>').insertAfter(filterInput);
            }
            const clearIcon = $('.search-clear-icon');
            let debounceTimer;
            filterInput.on('keyup input', function (e) {
                const val = $(this).val();
                if (val.length > 0) clearIcon.show(); else clearIcon.hide();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () { table.search(val).draw(); }, 500);
            });
            $(document).on('click', '.search-clear-icon', function() {
                filterInput.val('').trigger('input');
                table.search('').draw();
            });

            // Checkbox Logic
            function updateSelectedCount() {
                const selectedCheckboxes = $('.requisition-checkbox:checked');
                const selectedCount = selectedCheckboxes.length;
                $('#selected-count').text(selectedCount);
                $('#print-selected-btn').prop('disabled', selectedCount === 0);
                
                $('#hidden-ids-container').empty();
                selectedCheckboxes.each(function() {
                    $('#hidden-ids-container').append(`<input type="hidden" name="ids[]" value="${$(this).val()}">`);
                });
            }
            $('#select-all-checkbox').on('click', function() {
                $('.requisition-checkbox').prop('checked', this.checked);
                updateSelectedCount();
            });
            $('#freegoodsReportTable tbody').on('change', '.requisition-checkbox', function() {
                updateSelectedCount();
                if (!this.checked) $('#select-all-checkbox').prop('checked', false);
            });

            // Modal Logic
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

                const viewItemTbody = $('#view-items-tbody');
                viewItemTbody.empty();
                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = item.item_master ? item.item_master.item_master_code : 'N/A';
                        let itemName = item.item_master ? item.item_master.item_master_name : 'N/A';
                        let unit = item.item_master ? item.item_master.unit : 'N/A';
                        const newRow = `<tr><td>${itemCode}</td><td>${itemName}</td><td>${unit}</td><td class="text-center">${item.quantity_required}</td><td class="text-center">${item.quantity_issued || '-'}</td></tr>`;
                        viewItemTbody.append(newRow);
                    });
                } else viewItemTbody.html(`<tr><td colspan="5" class="text-center">No items found.</td></tr>`);

                const status = data.status;
                let badgeClass = 'bg-secondary';
                if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-warning';
                else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                else if (['Rejected', 'Recalled'].includes(status)) badgeClass = 'bg-danger';
                else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-info';
                $('#view_status_badge').html(`<span class="badge fs-6 rounded-pill ${badgeClass}">${status}</span>`);

                const trackerContainer = $('#approval-tracker-container');
                trackerContainer.empty();
                let steps = [
                    { id: 'submitted', label: 'Request Submit', icon: 'ph-file-arrow-up' },
                    { id: 'approver_1', label: 'Manager', icon: 'ph-user' },
                    { id: 'approver_2', label: 'Business Controller', icon: 'ph-briefcase' },
                    { id: 'outward', label: 'Warehouse Outward', icon: 'ph-package' },
                    { id: 'completed', label: 'Completed', icon: 'ph-check-circle' }
                ];
                let trackerHtml = '<div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>';
                steps.forEach(step => {
                    trackerHtml += `<div class="tracker-step" data-step-id="${step.id}"><div class="tracker-icon"><i class="ph-bold ${step.icon} fs-6"></i></div><div class="tracker-label">${step.label}</div><div class="tracker-details"></div></div>`;
                });
                trackerContainer.html(trackerHtml);

                let activeIndex = 0;
                if (status === 'Completed') activeIndex = 4;
                else if (status === 'Processing') activeIndex = 3;
                else if (status.includes('Approved')) activeIndex = 2; 
                
                if (status !== 'Rejected') {
                    let progress = (activeIndex / 4) * 100;
                    $('#tracker-progress').css('width', progress + '%');
                    $('.tracker-step').each(function(index) {
                        if (index <= activeIndex) $(this).addClass('completed');
                    });
                } else $('.tracker-step').first().addClass('rejected');

                const historyContainer = $('#history-log-container');
                historyContainer.empty();
                if (data.history && data.history.length > 0) {
                    data.history.forEach(log => {
                        let badgeClass = 'badge-created', avatarClass = 'avatar-created';
                        const action = log.action.toLowerCase();
                        if (action.includes('approved')) { badgeClass = 'badge-approved'; avatarClass = 'avatar-approved'; }
                        else if (action.includes('rejected')) { badgeClass = 'badge-rejected'; avatarClass = 'avatar-rejected'; }
                        const logDate = new Date(log.timestamp).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                        const historyItem = `<li class="list-group-item history-item"><div class="history-avatar ${avatarClass}">${log.actor.charAt(0)}</div><div class="history-content"><div class="history-actor">${log.actor}</div><div class="history-notes">${log.notes || ''}</div></div><div class="history-meta"><div class="history-badge ${badgeClass}">${log.action}</div><div class="history-timestamp">${logDate}</div></div></li>`;
                        historyContainer.append(historyItem);
                    });
                }
            }

            $(document).on('click', '.btn-view-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();
                button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

                $.ajax({
                    url: `/freegoods-form/${id}`,
                    type: 'GET',
                    success: function(response) {
                        populateViewForm(response);
                        $('#viewModal').modal('show');
                    },
                    error: function() { errorMessage('Failed to fetch requisition details.'); },
                    complete: function() { button.html(originalIcon).prop('disabled', false); }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>