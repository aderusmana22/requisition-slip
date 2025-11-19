<x-app-layout>
    @section('title')
        Tracking Path Management
    @endsection

    {{-- Include Complaint Table Styles Component (Tetap dipertahankan sesuai asli) --}}
    @include('components.complaint-table-styles')

    {{-- Custom CSS untuk Tampilan Emas & Badge --}}
    @push('css')
    <style>
        /* Header Card Warna Emas (Gradient) */
        .card-gold-header {
            background: linear-gradient(85deg, #b07b04, #d49709);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 1.5rem;
        }
        
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
        }

        /* Styling untuk Action Button (Kotak) */
        .btn-action-box {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
            margin: 0 2px;
            border: none;
            color: white;
            transition: all 0.2s;
        }
        
        .btn-gold { background-color: #b07b04; }
        .btn-gold:hover { background-color: #8f6303; color: white; }
        
        .btn-red { background-color: #ea5455; }
        .btn-red:hover { background-color: #c0392b; color: white; }

        /* Badge Styles yang lebih mudah dibaca (Background terang, Text gelap) */
        .badge-sub-category {
            background-color: rgba(3, 195, 236, 0.15);
            color: #03c3ec;
            font-weight: 600;
            padding: 5px 10px;
        }

        .badge-print-yes {
            background-color: rgba(113, 221, 55, 0.15);
            color: #71dd37;
            padding: 5px 10px;
            font-weight: 600;
        }

        .badge-print-no {
            background-color: rgba(133, 146, 163, 0.15);
            color: #8592a3;
            padding: 5px 10px;
            font-weight: 600;
        }

        /* Styling Text List Approver */
        .approver-item {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
            color: #566a7f;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .approver-icon {
            margin-right: 8px;
            color: #b07b04; /* Gold Icon */
        }

        /* Table Rows Alignment */
        table.dataTable tbody td {
            vertical-align: middle;
        }

        /* Search Box Styling */
        .dataTables_filter input {
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #d9dee3;
        }
    </style>
    @endpush

    {{-- Header & Breadcrumbs (Tetap dipertahankan sesuai asli) --}}
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Tracking Path Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a href="{{ route('dashboard') }}">Home</a>
                </li>
                <li class="active">
                    Tracking Path Management
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-muted mb-0">A list of all tracking paths including their category, sub-category, and approvers.</p>
                </div>
                <div>
                    <button id="btn-create-approver" class="btn btn-gold fw-bold px-3 py-2" style="border-radius: 8px;">
                        <i class="ph ph-plus me-1"></i> Create New Path
                    </button>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-header card-gold-header">
                    <div class="d-flex align-items-center">
                        <i class="ph ph-list-dashes fs-4 me-2"></i>
                        <h5 class="mb-0 text-white fw-bold">Tracking Paths List</h5>
                    </div>
                    <small class="text-white opacity-75">Manage approval workflow configurations and sequences</small>
                </div>

                <div class="card-body mt-3">
                    <table id="trackingpathtable" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th class="no-sort text-center" style="width: 5%">No</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Approvers</th>
                                <th>Print Batch</th>
                                <th class="no-sort text-center" style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ApproverModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Create New Tracking Path</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ApproverForm" name="ApproverForm" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="approver_id" id="approver_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select id="category_id" name="category_id" class="form-control" required>
                                        <option value="">Select a category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_category_id" class="form-label">Sub Category</label>
                                    <select id="sub_category_id" name="sub_category_id" class="form-control">
                                        <option value="">Select a sub-category (optional)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="approvers" class="form-label">Approvers</label>
                            <select id="approvers" name="approvers[]" class="form-control" multiple="multiple" required>
                            </select>
                            <small class="text-muted">Select user roles in order of approval sequence.</small>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Print Batch</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_batch" id="print_batch_yes" value="1">
                                    <label class="form-check-label" for="print_batch_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_batch" id="print_batch_no" value="0">
                                    <label class="form-check-label" for="print_batch_no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0 mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-gold" id="btn-save" form="ApproverForm">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/@phosphor-icons/web"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // === SweetAlert2 Reusable Functions (Tetap dipertahankan) ===
            function successMessage(message, title = 'Success', timer = 1500) {
                Swal.fire({ icon: 'success', title: title, text: message, timer: timer, showConfirmButton: false });
            }

            function errorMessage(message, title = 'Error') {
                Swal.fire({ icon: 'error', title: title, text: message });
            }

            function confirmDialog({ title = 'Are you sure?', text = 'This action cannot be undone!', confirmButtonText = 'Yes', cancelButtonText = 'Cancel' } = {}) {
                return Swal.fire({
                    title: title, text: text, icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#b07b04', cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText, cancelButtonText: cancelButtonText, reverseButtons: true
                });
            }

            $(document).ready(function() {
                let allSubCategories = [];
                let existingPaths = [];

                // === Initialize Select2 (Tetap dipertahankan) ===
                $('#approvers, #category_id, #sub_category_id').select2({
                    theme: 'bootstrap-5', dropdownParent: $('#ApproverModal')
                });
                
                $('#approvers').on('select2:selecting', function(e) {
                    var data = e.params.args.data;
                    var newOption = new Option(data.text, data.id, true, true);
                    $(this).append(newOption).trigger('change');
                    e.preventDefault();
                    $(this).select2('close');
                });

                // === Dynamic Sub-Category Logic (Tetap dipertahankan) ===
                $('#category_id').on('change', function() {
                    const selectedCategory = $(this).val();
                    const selectedSubCategory = $('#sub_category_id').val();
                    updatePathWarning(selectedCategory, selectedSubCategory);

                    if (selectedCategory && selectedCategory.toLowerCase() !== 'sample') {
                        $('#sub_category_id').val(null).trigger('change').prop('disabled', true);
                    } else {
                        $('#sub_category_id').prop('disabled', false);
                    }
                });

                $('#sub_category_id').on('change', function() {
                    updatePathWarning($('#category_id').val(), $(this).val());
                });

                function updatePathWarning(category, subCategory) {
                    $('#path-exists-warning').remove();
                    if (!category) return;
                    const pathExists = existingPaths.some(path =>
                        path.category === category &&
                        (path.sub_category === subCategory || (subCategory === '' && path.sub_category === null))
                    );
                    if (pathExists) {
                        $('#ApproverForm').prepend('<div id="path-exists-warning" class="alert alert-warning mt-2">Path already exists.</div>');
                    }
                }

                // === Load Dropdown Data (Tetap dipertahankan) ===
                function loadDropdownData() {
                    $.when(
                        $.ajax({ url: "{{ route('tracking-path.categories') }}", type: 'GET' }),
                        $.ajax({ url: "{{ route('tracking-path.approverName') }}", type: 'GET' })
                    ).done(function(categoriesResponse, approversResponse) {
                        const categoriesData = categoriesResponse[0];
                        const categories = categoriesData.categories;
                        allSubCategories = categoriesData.subCategories;
                        existingPaths = categoriesData.existingPaths;

                        const categorySelect = $('#category_id');
                        categorySelect.empty().append(new Option('Select a category', ''));
                        categories.forEach(cat => categorySelect.append(new Option(cat, cat)));

                        const subCategorySelect = $('#sub_category_id');
                        subCategorySelect.empty().append(new Option('Select a sub-category (optional)', ''));
                        allSubCategories.forEach(sub => subCategorySelect.append(new Option(sub, sub)));

                        const approversData = approversResponse[0].approverName;
                        const approverSelect = $('#approvers');
                        approverSelect.empty();
                        $.each(approversData, function(key, value) {
                            approverSelect.append(new Option(value, key));
                        });
                    }).fail(function() {
                        errorMessage('Failed to load initial data. Please refresh the page.');
                    });
                }
                loadDropdownData();

                // === DataTable Initialization (UPDATE RENDER AGAR SESUAI STYLE) ===
                let table = $('#trackingpathtable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('tracking-path.list') }}",
                        type: "GET",
                        data: function(d) {
                            d.order = [{ column: d.order[0].column, dir: d.order[0].dir }];
                            d.columns = d.columns.map(col => ({
                                data: col.data, name: col.name, searchable: col.searchable, orderable: col.orderable, search: col.search
                            }));
                        }
                    },
                    columns: [
                        {
                            // Render No dengan Lingkaran
                            data: null, orderable: false, searchable: false, className: "text-center",
                            render: function (data, type, row, meta) {
                                return `<div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary fw-bold" style="width: 30px; height: 30px;">${meta.row + meta.settings._iDisplayStart + 1}</div>`;
                            }
                        },
                        {
                            // Render Category dengan Icon
                            data: "category", name: "category",
                            render: function(data) {
                                return `<span class="fw-bold text-primary"><i class="ph ph-files me-2"></i>${data}</span>`;
                            }
                        },
                        {
                            // Render Sub-Category dengan Badge Cyan
                            data: "sub_category", name: "sub_category",
                            render: function(data) {
                                if (!data || data === '-') return '<span class="badge bg-label-secondary">General</span>';
                                return `<span class="badge rounded-pill badge-sub-category"><i class="ph ph-tag me-1"></i>${data}</span>`;
                            }
                        },
                        {
                            // Render Approver List Vertikal
                            data: "sequence_approvers", name: "sequence_approvers", orderable: false,
                            render: function(data) {
                                if (!data) return '';
                                return data.map(approver =>
                                    `<div class="approver-item"><i class="ph ph-user-circle approver-icon"></i> ${approver}</div>`
                                ).join('');
                            }
                        },
                        {
                            // Render Print Batch Badge
                            data: "print_batch", name: "print_batch",
                            render: function(data) {
                                return (data == 1) 
                                    ? '<span class="badge badge-print-yes rounded-pill"><i class="ph ph-check me-1"></i>Yes</span>' 
                                    : '<span class="badge badge-print-no rounded-pill"><i class="ph ph-x me-1"></i>No</span>';
                            }
                        },
                        {
                            // Render Action Buttons (Kotak Emas & Merah)
                            data: "id", orderable: false, className: "text-center",
                            render: function(data) {
                                return `
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn-action-box btn-gold action-btn-hover" data-id="${data}" data-action="edit" title="Edit">
                                        <i class="ph ph-pencil-simple fs-5"></i>
                                    </button>
                                    <button class="btn-action-box btn-red action-btn-hover" data-id="${data}" data-action="delete" title="Delete">
                                        <i class="ph ph-trash fs-5"></i>
                                    </button>
                                </div>`;
                            }
                        }
                    ],
                    order: [[1, 'asc']],
                    responsive: true,
                    autoWidth: false,
                    language: { search: "", searchPlaceholder: "Search..." },
                    dom: '<"row mx-2"<"col-md-2"<"me-3"l>><"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"f>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
                });

                // Style Search Input
                $('.dataTables_filter input').addClass('form-control rounded-pill ps-3');

                // === Modal & CRUD Handlers (Tetap dipertahankan) ===
                $('#btn-create-approver').on('click', function() {
                    resetFormState();
                    $('#modal-title').text('Create New Tracking Path');
                    $('#approver_id').val('');
                    $('#ApproverModal').modal('show');
                });

                $('#trackingpathtable').on('click', '.action-btn-hover[data-action="edit"]', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    resetFormState();

                    $.get("{{ url('master/tracking-path') }}/" + id + "/edit", function(data) {
                        $('#modal-title').text('Edit Tracking Path');
                        $('#btn-save').text('Save Changes');
                        $('#approver_id').val(id);
                        $('#category_id').val(data.category_id).trigger('change').prop('disabled', true);
                        $('#sub_category_id').val(data.sub_category_id).trigger('change').prop('disabled', true);
                        $('#approvers').val(data.approver_user_ids).trigger('change');

                        if (data.print_batch == 1) $('#print_batch_yes').prop('checked', true);
                        else if (data.print_batch == 0) $('#print_batch_no').prop('checked', true);
                        else { $('#print_batch_yes').prop('checked', false); $('#print_batch_no').prop('checked', false); }

                        $('#ApproverModal').modal('show');
                    }).fail(function() { errorMessage('Could not load data for editing.'); });
                });

                function resetFormState() {
                    $('#ApproverForm').trigger("reset");
                    $('#approvers, #category_id, #sub_category_id').val(null).trigger('change');
                    $('input[name="print_batch"]').prop('checked', false);
                    $('#path-exists-warning').remove();
                    $('#btn-save').text('Save changes');
                    $('#category_id, #sub_category_id').prop('disabled', false);
                }

                $('#ApproverForm').on('submit', function(e) {
                    e.preventDefault();
                    $('#btn-save').html('Sending..').prop('disabled', true);
                    const approverId = $('#approver_id').val();
                    const url = approverId ? "{{ url('master/tracking-path') }}/" + approverId : "{{ route('tracking-path.store') }}";
                    const method = approverId ? 'PUT' : 'POST';
                    const formData = {
                        category_id: $('#category_id').val(),
                        sub_category_id: $('#sub_category_id').val(),
                        approvers: $('#approvers').val(),
                        print_batch: $('input[name="print_batch"]:checked').val(),
                        _token: '{{ csrf_token() }}',
                        _method: method
                    };

                    $.ajax({
                        url: url, type: 'POST', data: formData, dataType: 'json',
                        success: function(response) {
                            successMessage(response.message);
                            $('#ApproverModal').modal('hide');
                            table.draw();
                        },
                        error: function(jqXHR) {
                            errorMessage(jqXHR.responseJSON?.message || 'An error occurred.');
                        },
                        complete: function() { $('#btn-save').html('Save changes').prop('disabled', false); }
                    });
                });

                $('#trackingpathtable').on('click', '.action-btn-hover[data-action="delete"]', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    confirmDialog().then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('master/tracking-path') }}/" + id,
                                type: 'DELETE',
                                data: { _token: '{{ csrf_token() }}' },
                                success: function(response) { successMessage(response.message); table.draw(); },
                                error: function(jqXHR) { errorMessage(jqXHR.responseJSON?.message || 'Could not delete.'); }
                            });
                        }
                    });
                });

                function initActionTooltips() {
                    $('.action-btn-hover').tooltip('dispose');
                    $('.action-btn-hover').each(function() {
                        let title = $(this).attr('title');
                        if (title) { $(this).tooltip({ title: title, placement: 'top', trigger: 'hover' }); }
                    });
                }
                table.on('draw', function() { initActionTooltips(); });
                initActionTooltips();
            });
        </script>
    @endpush
</x-app-layout>