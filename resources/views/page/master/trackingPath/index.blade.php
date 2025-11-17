<x-app-layout>
    @section('title')
        Tracking Path Management
    @endsection

    {{-- Include Complaint Table Styles Component --}}
    @include('components.complaint-table-styles')

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

    <!--  -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar with Enhanced Styling -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="table-title">Tracking Path List</h5>
                    <p class="table-description">A list of all tracking paths including their category, sub-category, and
                        approvers.</p>
                </div>
                <div>
                    <button id="btn-create-approver" class="btn btn-primary-custom">
                        <i class="ph ph-plus"></i>
                        <span class="d-none d-sm-inline">Create New Path</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center">
                                <p class="table-label">Tracking Paths</p>
                                <span class="table-info-badge">{{ \App\Models\Master\TrackingPath::count() }}
                                    Paths</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <table id="trackingpathtable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="no-sort"></th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Approvers</th>
                            <th>Print Batch</th>
                            <th>Created At</th>
                            <th class="no-sort text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- modal create approver -->
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
                        <div class="form-group">
                            <label for="approvers" class="form-label">Approvers</label>
                            <select id="approvers" name="approvers[]" class="form-control" multiple="multiple"
                                required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="print_batch" class="form-label">Print Batch</label>
                            <input type="text" name="print_batch" id="print_batch" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="btn-save" form="ApproverForm">Save changes</button>
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
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: cancelButtonColor,
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: cancelButtonText,
                    reverseButtons
                });
            }

            $(document).ready(function() {
                let allSubCategories = [];
                let existingPaths = [];

                // === Initialize Select2 ===
                $('#approvers, #category_id, #sub_category_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#ApproverModal')
                });
                // Atur placeholder secara spesifik
                $('#category_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select a category',
                    dropdownParent: $('#ApproverModal')
                });
                $('#sub_category_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select a sub-category',
                    dropdownParent: $('#ApproverModal')
                });
                $('#approvers').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select approvers in order',
                    dropdownParent: $('#ApproverModal')
                });


                // atur urutan selected approvers
                $('#approvers').on('select2:select', function(e) {
                    var option = $(e.params.data.element);
                    option.detach();
                    $(this).append(option).trigger('change');
                });

                // === Dynamic Sub-Category Handling ===
                $('#category_id').on('change', function() {
                    const selectedCategory = $(this).val();
                    const selectedSubCategory = $('#sub_category_id').val();
                    updatePathWarning(selectedCategory, selectedSubCategory);

                    // Disable sub-category if category is not 'Sample'
                    if (selectedCategory && selectedCategory.toLowerCase() !== 'sample') {
                        $('#sub_category_id').val(null).trigger('change').prop('disabled', true);
                    } else {
                        $('#sub_category_id').prop('disabled', false);
                    }
                });

                $('#sub_category_id').on('change', function() {
                    const selectedCategory = $('#category_id').val();
                    const selectedSubCategory = $(this).val();
                    updatePathWarning(selectedCategory, selectedSubCategory);
                });

                function updatePathWarning(category, subCategory) {
                    // Hapus warning yang ada
                    $('#path-exists-warning').remove();

                    if (!category) return;

                    const pathExists = existingPaths.some(path =>
                        path.category === category &&
                        (path.sub_category === subCategory || (subCategory === '' && path.sub_category ===
                            null))
                    );

                    if (pathExists) {
                        const warningHtml =
                            '<div id="path-exists-warning" class="alert alert-warning mt-2">An approval path for this category and sub-category already exists.</div>';
                        $('#ApproverForm').prepend(warningHtml);
                    }
                }

                // === Load ALL Dropdown Data via AJAX ===
                function loadDropdownData() {
                    $.when(
                        $.ajax({
                            url: "{{ route('tracking-path.categories') }}",
                            type: 'GET'
                        }),
                        $.ajax({
                            url: "{{ route('tracking-path.approverName') }}",
                            type: 'GET'
                        })
                    ).done(function(categoriesResponse, approversResponse) {
                        // Handle Categories & Sub-Categories
                        const categoriesData = categoriesResponse[0];
                        const categories = categoriesData.categories;
                        allSubCategories = categoriesData.subCategories;
                        existingPaths = categoriesData.existingPaths;

                        const categorySelect = $('#category_id');
                        categorySelect.empty().append(new Option('Select a category', ''));
                        categories.forEach(function(category) {
                            categorySelect.append(new Option(category, category));
                        });

                        const subCategorySelect = $('#sub_category_id');
                        subCategorySelect.empty().append(new Option('Select a sub-category (optional)',
                            ''));
                        allSubCategories.forEach(function(subCategory) {
                            subCategorySelect.append(new Option(subCategory, subCategory));
                        });

                        // Handle Approvers
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

                // === DataTable Initialization ===
                let table = $('#trackingpathtable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('tracking-path.list') }}",
                        "type": "GET",
                        "data": function(d) {
                            // Mengirim informasi sorting ke server
                            d.order = [{
                                column: d.order[0].column,
                                dir: d.order[0].dir
                            }];
                            d.columns = d.columns.map(col => ({
                                data: col.data,
                                name: col.name,
                                searchable: col.searchable,
                                orderable: col.orderable,
                                search: col.search
                            }));
                        }
                    },
                    "columns": [{
                            "data": null,
                            "className": 'details-control',
                            "orderable": false,
                            "defaultContent": '<i class="ph ph-caret-right"></i>',
                            "width": "5%"
                        },
                        {
                            "data": "category",
                            "name": "category"
                        },
                        {
                            "data": "sub_category",
                            "name": "sub_category"
                        },
                        {
                            "data": "sequence_approvers",
                            "name": "sequence_approvers",
                            "orderable": false,
                            "render": function(data, type, row) {
                                if (!data) return '';
                                return data.map(approver =>
                                    `<span class="badge bg-secondary-custom">${approver}</span>`
                                ).join(' ');
                            }
                        },
                        {
                            "data": "print_batch",
                            "name": "print_batch"
                        },
                        {
                            "data": "created_at",
                            "name": "created_at",
                            "visible": false
                        }, // Sembunyikan kolom ini
                        {
                            "data": "id",
                            "orderable": false,
                            "className": "text-center",
                            "render": function(data, type, row) {
                                return `
                                <div class="d-flex justify-content-center">
                                    <button class="action-btn-hover me-2" data-id="${data}" data-action="edit" title="Edit Path">
                                        <i class="ph ph-pencil-simple"></i>
                                    </button>
                                    <button class="action-btn-hover text-danger" data-id="${data}" data-action="delete" title="Delete Path">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>`;
                            }
                        }
                    ],
                    "order": [
                        [5, 'desc']
                    ], // Default sort by 'created_at' descending
                    "responsive": true,
                    "autoWidth": false,
                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    "pageLength": 10,
                    "language": {
                        "search": "_INPUT_",
                        "searchPlaceholder": "Search...",
                        "lengthMenu": "Show _MENU_ entries",
                        "paginate": {
                            "first": "<i class='ph ph-caret-double-left'></i>",
                            "last": "<i class='ph ph-caret-double-right'></i>",
                            "next": "<i class='ph ph-caret-right'></i>",
                            "previous": "<i class='ph ph-caret-left'></i>"
                        }
                    },
                    "columnDefs": [{
                        "targets": 'no-sort',
                        "orderable": false,
                    }],
                    "dom": '<"top"l<"custom-search"f>>rt<"bottom"ip><"clear">'
                });

                // === DataTable Search Debounce ===
                let searchInput = $('#trackingpathtable_filter input');
                searchInput.unbind();
                let debounceTimer;
                searchInput.bind('keyup', function(e) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        table.search(this.value).draw();
                    }, 500);
                });

                // === Modal: Show for Create ===
                $('#btn-create-approver').on('click', function() {
                    resetFormState();
                    $('#modal-title').text('Create New Tracking Path');
                    $('#approver_id').val('');
                    $('#category_id, #sub_category_id').prop('disabled', false);
                    $('#ApproverModal').modal('show');
                });

                // === Modal: Show for Edit ===
                $('#trackingpathtable').on('click', '.action-btn-hover[data-action="edit"]', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    resetFormState();

                    $.get("{{ url('master/tracking-path') }}/" + id + "/edit", function(data) {
                        $('#modal-title').text('Edit Tracking Path');
                        $('#btn-save').text('Save Changes');
                        $('#approver_id').val(id);

                        // Set category and sub-category, then disable them
                        $('#category_id').val(data.category_id).trigger('change').prop('disabled',
                            true);
                        $('#sub_category_id').val(data.sub_category_id).trigger('change').prop(
                            'disabled', true);

                        // Set approvers
                        $('#approvers').val(data.approver_user_ids).trigger('change');

                        // Set print batch
                        $('#print_batch').val(data.print_batch).trigger('change');

                        $('#ApproverModal').modal('show');
                    }).fail(function() {
                        errorMessage('Could not load data for editing.');
                    });
                });

                function resetFormState() {
                    $('#ApproverForm').trigger("reset");
                    $('#approvers, #category_id, #sub_category_id, #print_batch').val(null).trigger('change');
                    $('#ApproverForm .is-invalid').removeClass('is-invalid');
                    $('#ApproverForm .invalid-feedback').remove();
                    $('#path-exists-warning').remove();
                    $('#btn-save').text('Save changes');
                    $('#category_id, #sub_category_id').prop('disabled', false);
                }

                // === Form Submit Handler (Create & Edit) ===
                $('#ApproverForm').on('submit', function(e) {
                    e.preventDefault();
                    $('#btn-save').html('Sending..').prop('disabled', true);

                    const approverId = $('#approver_id').val();
                    const url = approverId ?
                        "{{ url('master/tracking-path') }}/" + approverId :
                        "{{ route('tracking-path.store') }}";
                    const method = approverId ? 'PUT' : 'POST';

                    const formData = {
                        category_id: $('#category_id').val(),
                        sub_category_id: $('#sub_category_id').val(),
                        approvers: $('#approvers').val(),
                        print_batch: $('#print_batch').val(),
                        _token: '{{ csrf_token() }}',
                        _method: method
                    };

                    $.ajax({
                        url: url,
                        type: 'POST', // Always POST, method override handles PUT
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            successMessage(response.message);
                            $('#ApproverModal').modal('hide');
                            table.draw();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            let message = 'An error occurred.';
                            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                                message = jqXHR.responseJSON.message;
                            }
                            errorMessage(message);
                        },
                        complete: function() {
                            $('#btn-save').html('Save changes').prop('disabled', false);
                        }
                    });
                });


                // === Delete Handler ===
                $('#trackingpathtable').on('click', '.action-btn-hover[data-action="delete"]', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');

                    confirmDialog({
                        text: "You won't be able to revert this!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('master/tracking-path') }}/" + id,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    successMessage(response.message);
                                    table.draw();
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    errorMessage(jqXHR.responseJSON.message ||
                                        'Could not delete the item.');
                                }
                            });
                        }
                    });
                });


                // Custom Tooltip Handler for Action Buttons
                function initActionTooltips() {
                    // Hapus tooltip yang ada untuk menghindari duplikasi
                    $('.action-btn-hover').tooltip('dispose');

                    // Inisialisasi tooltip baru
                    $('.action-btn-hover').each(function() {
                        let title = $(this).attr('title');
                        if (title) {
                            $(this).tooltip({
                                title: title,
                                placement: 'top',
                                trigger: 'hover'
                            });
                        }
                    });
                }

                // Initialize tooltips after DataTable is ready
                table.on('draw', function() {
                    initActionTooltips();
                });

                // Initialize tooltips for the first load
                initActionTooltips();
            });
        </script>
    @endpush
</x-app-layout>
