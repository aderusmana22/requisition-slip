<x-app-layout>
    @section('title')
        Tracking Path Management
    @endsection

    @include('components.master-table-styles')

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

    <div class="row mb-3">
        <div class="col-12 text-end">
            <button id="btn-create-approver" class="btn btn-primary-custom shadow-sm">
                <i class="ph ph-plus fw-bold"></i>
                <span class="d-none d-sm-inline ms-1">Create New Path</span>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="table-title">
                                <i class="ph ph-bezier-curve"></i> Tracking Path List
                            </h5>
                            <p class="table-subtitle mb-0">
                                {{ \App\Models\Master\TrackingPath::count() }} paths available. Manage categories and approvers.
                            </p>
                        </div>
                    </div>
                </div>

                <table id="trackingpathtable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="no-sort">No.</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Approvers</th>
                            <th>Print Batch</th>
                            <th>Created At</th>
                            <th class="no-sort text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ApproverModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">

                <div class="modal-header-enhanced p-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #584D3C 0%, #9F956C 100%); color: white;">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="modal-title" style="font-size: 1.25rem;">
                        <i class="ph ph-bezier-curve me-2" style="font-size: 1.5rem;"></i>
                        <span>Create New Tracking Path</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="ApproverForm" name="ApproverForm" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="approver_id" id="approver_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-label fw-bold text-secondary">Category</label>
                                    <select id="category_id" name="category_id" class="form-control" required>
                                        <option value="">Select a category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sub_category_id" class="form-label fw-bold text-secondary">Sub Category</label>
                                    <select id="sub_category_id" name="sub_category_id" class="form-control">
                                        <option value="">Select a sub-category (optional)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="approvers" class="form-label fw-bold text-secondary">Approvers Sequence</label>
                            <div class="alert alert-light border border-secondary border-opacity-25 mb-2 p-2 rounded-3">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="ph ph-info me-2 text-primary-custom"></i>
                                    Select approvers in the order they should approve.
                                </small>
                            </div>
                            <select id="approvers" name="approvers[]" class="form-control" multiple="multiple" required>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold text-secondary">Print Batch</label>
                            <div class="d-flex gap-4 mt-2 p-3 rounded-3 bg-light text-secondary fw-bold border border-secondary border-opacity-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_batch" id="print_batch_yes" value="1">
                                    <label class="form-check-label fw-semibold" for="print_batch_yes">
                                        Yes (Batch Print)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_batch" id="print_batch_no" value="0">
                                    <label class="form-check-label fw-semibold" for="print_batch_no">
                                        No (Single Print)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 px-0 pb-0">
                            <button type="button" class="btn btn-light text-secondary fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-custom px-4 shadow-sm" id="btn-save" form="ApproverForm">
                                <i class="ph ph-floppy-disk me-2"></i> Save Changes
                            </button>
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
                confirmButtonColor = '#584D3C',
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

                $('#approvers').on('select2:selecting', function(e) {
                    var data = e.params.args.data;
                    var newOption = new Option(data.text, data.id, true, true);

                    $(this).append(newOption);
                    $(this).trigger('change');

                    e.preventDefault();
                    $(this).select2('close');
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
                    $('#path-exists-warning').remove();

                    if (!category) return;

                    const pathExists = existingPaths.some(path =>
                        path.category === category &&
                        (path.sub_category === subCategory || (subCategory === '' && path.sub_category ===
                            null))
                    );

                    if (pathExists) {
                        const warningHtml =
                            '<div id="path-exists-warning" class="alert alert-warning mt-2"><i class="ph ph-warning"></i> An approval path for this category already exists.</div>';
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
                            "name": "category",
                            "render": function(data) {
                                return `<span class="fw-bold text-dark">${data}</span>`;
                            }
                        },
                        {
                            "data": "sub_category",
                            "name": "sub_category",
                             "render": function(data) {
                                if(!data) return '<span class="text-muted">-</span>';
                                return data;
                            }
                        },
                        {
                            "data": "sequence_approvers",
                            "name": "sequence_approvers",
                            "orderable": false,
                            "render": function(data, type, row) {
                                if (!data) return '';
                                return data.map((approver, index) =>
                                    `<span class="badge bg-secondary border border-secondary text-dark bg-opacity-10 me-1 mb-1">${index + 1}. ${approver}</span>`
                                ).join(' ');
                            }
                        },
                        {
                            "data": "print_batch",
                            "name": "print_batch",
                            "render": function(data, type, row) {
                                if (data == 1 || data === true) {
                                    return '<span class="badge bg-success"><i class="ph ph-check"></i> Yes</span>';
                                } else {
                                    return '<span class="badge bg-secondary">No</span>';
                                }
                            }
                        },
                        {
                            "data": "created_at",
                            "name": "created_at",
                            "visible": false
                        },
                        {
                            "data": "id",
                            "orderable": false,
                            "className": "text-center",
                            "width": "10%",
                            "render": function(data, type, row) {
                                return `
                                <div class="action-btn-group">
                                    <button class="btn btn-secondary action-btn-hover" data-id="${data}" data-action="edit" title="Edit Path">
                                        <i class="ph ph-pencil-simple"></i>
                                    </button>
                                    <button class="btn btn-danger action-btn-hover" data-id="${data}" data-action="delete" title="Delete Path">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>`;
                            }
                        }
                    ],
                    "order": [
                        [5, 'desc']
                    ],
                    "responsive": true,
                    "autoWidth": false,
                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    "pageLength": 10,
                    "language": {
                        "search": "Search:",
                        "searchPlaceholder": "Type to search...",
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

                        $('#category_id').val(data.category_id).trigger('change').prop('disabled',
                            true);
                        $('#sub_category_id').val(data.sub_category_id).trigger('change').prop(
                            'disabled', true);

                        $('#approvers').val(data.approver_user_ids).trigger('change');

                        if (data.print_batch == 1) {
                            $('#print_batch_yes').prop('checked', true);
                        } else if (data.print_batch == 0) {
                            $('#print_batch_no').prop('checked', true);
                        } else {
                            $('#print_batch_yes').prop('checked', false);
                            $('#print_batch_no').prop('checked', false);
                        }

                        $('#ApproverModal').modal('show');
                    }).fail(function() {
                        errorMessage('Could not load data for editing.');
                    });
                });

                function resetFormState() {
                    $('#ApproverForm').trigger("reset");
                    $('#approvers, #category_id, #sub_category_id').val(null).trigger('change');
                    $('input[name="print_batch"]').prop('checked', false);
                    $('#ApproverForm .is-invalid').removeClass('is-invalid');
                    $('#ApproverForm .invalid-feedback').remove();
                    $('#path-exists-warning').remove();
                    $('#btn-save').text('Save changes');
                    $('#category_id, #sub_category_id').prop('disabled', false);
                }

                // === Form Submit Handler (Create & Edit) ===
                $('#ApproverForm').on('submit', function(e) {
                    e.preventDefault();
                    $('#btn-save').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving..').prop('disabled', true);

                    const approverId = $('#approver_id').val();
                    const url = approverId ?
                        "{{ url('master/tracking-path') }}/" + approverId :
                        "{{ route('tracking-path.store') }}";
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
                        url: url,
                        type: 'POST',
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

                function initActionTooltips() {
                    $('.action-btn-hover').tooltip('dispose');
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

                table.on('draw', function() {
                    initActionTooltips();
                });

                initActionTooltips();
            });
        </script>
    @endpush
</x-app-layout>
