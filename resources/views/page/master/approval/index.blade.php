<x-app-layout>
    @section('title')
    Approver List
    @endsection

    @push('css')
    <style>
        .modal-header {
            background-color: #cc982f;
        }

        .modal-footer {
            background-color: #f8f8f8;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Approver Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Settings
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Approver List</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-light-danger btn-md" type="button" id="btn-create-approver">
                    <i class="ph-bold ph-plus pe-2"></i> New Approver
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="approvertable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Approver</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ApproverModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="ApproverModalLabel">Create Approver</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" data-mode="create" id="ApproverForm">
                        @csrf

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                            </select>
                            <div class="invalid-feedback" data-error-for="category_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="sub_category_id" class="form-label">Sub Category</label>
                            <select class="form-select" id="sub_category_id" name="sub_category_id">
                            </select>
                            <input type="hidden" name="sub_category_id" id="hidden_sub_category_id" disabled>
                            <div class="invalid-feedback" data-error-for="sub_category_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="approvers" class="form-label">Approver Sequence</label>
                            <select class="form-select" id="approvers" name="approvers[]" multiple="multiple" required>
                            </select>
                            <div class="invalid-feedback" data-error-for="approvers"></div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="submit" id="saveApproverBtn" form="ApproverForm">
                        Save Changes
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
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

        $(document).ready(function () {
            // === Initialize Select2 ===
            $('#approvers, #category_id, #sub_category_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#ApproverModal')
            });
            // Atur placeholder secara spesifik
            $('#category_id').select2({ theme: 'bootstrap-5', placeholder: 'Select a category', dropdownParent: $('#ApproverModal') });
            $('#sub_category_id').select2({ theme: 'bootstrap-5', placeholder: 'Select a sub-category', dropdownParent: $('#ApproverModal') });
            $('#approvers').select2({ theme: 'bootstrap-5', placeholder: 'Select approvers in order', dropdownParent: $('#ApproverModal') });

            // atur urutan selected approvers
            $('#approvers').on('select2:select', function (e) {
                let id = e.params.data.id;
                let option = $(this).find('option[value="' + id + '"]');
                option.appendTo(this);
            });

            // === Dynamic Sub-Category Handling ===
            let allSubCategories = [];
            $('#category_id').on('change', function () {
                let selectedCategory = $(this).val();
                let subCategorySelect = $('#sub_category_id');
                let hiddenSubCategoryInput = $('#hidden_sub_category_id');

                if (selectedCategory === 'Complain' || selectedCategory === 'Free Goods') {
                    // Nonaktifkan select yang terlihat agar tidak bisa diubah
                    subCategorySelect.prop('disabled', true);
                    subCategorySelect.val(null).trigger('change');
                    subCategorySelect.select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#ApproverModal'),
                        placeholder: 'This category has no sub-category'
                    });

                    // Aktifkan hidden input dan beri nilai kosong
                    hiddenSubCategoryInput.prop('disabled', false);
                    hiddenSubCategoryInput.val(null);
                } else {
                    // Aktifkan kembali select yang terlihat
                    subCategorySelect.prop('disabled', false);

                    // Nonaktifkan hidden input agar nilainya tidak bentrok
                    hiddenSubCategoryInput.prop('disabled', true);

                    // Atur ulang Select2 dan isi kembali datanya
                    subCategorySelect.select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#ApproverModal'),
                        placeholder: 'Select a sub-category'
                    });

                    subCategorySelect.empty();
                    allSubCategories.forEach(subCat => {
                        subCategorySelect.append(new Option(subCat.text, subCat.value));
                    });
                    subCategorySelect.trigger('change');
                }
            });

            // === Load ALL Dropdown Data via AJAX ===
            function loadDropdownData() {
                // Fetch Categories & Sub-Categories
                $.ajax({
                    url: "{{ route('get.categories') }}",
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        let categorySelect = $('#category_id');
                        let subCategorySelect = $('#sub_category_id');

                        categorySelect.empty().append('<option selected disabled value="">Choose a category...</option>');
                        subCategorySelect.empty().append('<option selected disabled value="">Choose a sub category...</option>');

                        if (data.categories) {
                            data.categories.forEach(cat => categorySelect.append(new Option(cat, cat)));
                        }
                        if (data.subCategories) {
                            data.subCategories.forEach(subCat => { subCategorySelect.append(new Option(subCat, subCat));
                                allSubCategories.push({ value: subCat, text: subCat });
                            });
                        }
                        categorySelect.trigger('change');
                    },
                    error: function () {
                        errorMessage('Failed to load category data.');
                    }
                });

                // 2. Fetch Approver Names
                $.ajax({
                    url: "{{ route('get.approver.name') }}",
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        let approverSelect = $('#approvers');
                        approverSelect.empty();

                        console.log(data.approverName);

                        if (data.approverName) {
                            $.each(data.approverName, function (key, value) {
                                approverSelect.append(new Option(value, key, false, false));
                            });
                        }

                        approverSelect.trigger('change');
                    },
                    error: function (xhr) {
                        errorMessage('Failed to load approver data.');
                    }
                });
            }

            loadDropdownData();

            // === DataTable Initialization ===
            let table = $('#approvertable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('get.approverlist') }}",
                columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'category',
                    name: 'category'
                }, {
                    data: 'sub_category',
                    name: 'sub_category',
                    render: function (data, type, row) {
                        return data ? data  : `<span class="text-muted" style="font-style: italic; text-color: gray;"> Non Sub-category </span>`;
                    }
                }, {
                    data: 'sequence_approvers',
                    name: 'sequence_approvers',
                    render: function (data, type, row) {
                        if (!Array.isArray(data) || data.length === 0) {
                            return '<span class="text-muted">No approvers assigned</span>';
                        }
                        const listItems = data.map(approver =>
                            `<li class="d-flex align-items-center mb-1">
                                <i class="ph-duotone ph-user-check fs-5 me-2 text-success"></i>
                                <span>${approver}</span>
                            </li>`
                        ).join('');
                        return `<ul style="list-style-type: none; padding-left: 0; margin-bottom: 0;">${listItems}</ul>`;
                        }
                    }, {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                                <button type="button" class="btn btn-secondary btn-sm edit-approver-btn" data-id="${data}" title="Edit Approver">
                                    <i class="ph-duotone ph-eraser"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-approver-btn" data-id="${data}" title="Delete Approver">
                                    <i class="ph-duotone ph-trash"></i>
                                </button>
                            `;
                    }
                }]
            });

            // === DataTable Search Debounce ===
            let searchInput = $('#approvertable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    table.search(searchInput.val()).draw();
                }, 500);
            });

            // === Modal: Show for Create ===
            $('#btn-create-approver').on('click', function () {
                $('#ApproverForm')[0].reset();
                $('#ApproverForm').attr('data-mode', 'create');
                // IMPORTANT: Replace with your actual store route
                $('#ApproverForm').attr('action', '{{ route("approvers.store") }}');
                $('#ApproverForm').find('input[name="_method"]').remove();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $('#ApproverModalLabel').text('Create New Approver');
                // Reset Select2 fields
                $('#category_id, #sub_category_id, #approvers').val(null).trigger('change');

                $('#ApproverModal').modal('show');
            });

            // === Modal: Show for Edit ===
            $('#approvertable').on('click', '.edit-approver-btn', function (e) {
                e.preventDefault();
                let approverId = $(this).data('id');
                // IMPORTANT: Replace with your actual edit/update routes
                let editUrl = `/approvers/${approverId}/edit`;
                let updateUrl = `/approvers/${approverId}`;

                $.ajax({
                    url: editUrl,
                    method: 'GET',
                    success: function (data) {
                        $('#ApproverForm')[0].reset();
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        $('#ApproverForm').attr('data-mode', 'edit');
                        $('#ApproverForm').attr('action', updateUrl);

                        // Populate form fields with data from server
                        $('#category_id').val(data.category_id).trigger('change');
                        $('#sub_category_id').val(data.sub_category_id).trigger('change');
                        $('#approvers').val(data.approver_user_ids).trigger('change'); // Assuming the server returns an array of user IDs

                        $('#ApproverModalLabel').text('Edit Approver');
                        $('#ApproverModal').modal('show');
                    },
                    error: function (xhr) {
                        errorMessage(xhr.responseJSON?.message || 'Could not fetch approver data.');
                    }
                });
            });

            // === Form Submit Handler (Create & Edit) ===
            $('#ApproverForm').on('submit', function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                let form = $(this);
                let url = form.attr('action');
                let formData = new FormData(this);

                if (form.attr('data-mode') === 'edit') {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    method: 'POST', // Always POST for FormData w/ method spoofing
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $('#ApproverModal').modal('hide');
                        table.ajax.reload(null, false);
                        successMessage(res.message || 'Operation successful!');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) { // Validation Error
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                let input = $(`[name="${key}"], [name="${key}[]"]`);
                                let errorContainer = $(`[data-error-for="${key}"]`);

                                if (input.hasClass('select2-hidden-accessible')) {
                                    input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                } else {
                                    input.addClass('is-invalid');
                                }

                                if (errorContainer.length) {
                                    errorContainer.text(errors[key][0]);
                                }
                            }
                            errorMessage('Please fix the errors in the form.');
                        } else {
                            errorMessage(xhr.responseJSON?.message || 'An unexpected error occurred.');
                        }
                    }
                });
            });

            // === Delete Handler ===
            $('#approvertable').on('click', '.delete-approver-btn', function (e) {
                e.preventDefault();
                let approverId = $(this).data('id');
                // IMPORTANT: Replace with your actual destroy route
                let deleteUrl = `/approvers/${approverId}`;

                confirmDialog({
                    text: 'You won\'t be able to revert this!',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            method: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (res) {
                                table.ajax.reload(null, false);
                                successMessage(res.message || 'Approver deleted successfully!');
                            },
                            error: function (xhr) {
                                errorMessage(xhr.responseJSON?.message || 'Failed to delete approver.');
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>