<x-app-layout>
    @section('title')
        Department List
    @endsection

    @include('components.master-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Department List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-address-book f-s-16"></i> Master Data</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Department List</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div>
                    <button class="btn btn-primary-custom" type="button" data-bs-toggle="modal" data-bs-target="#departmentModal" id="btn-create-department">
                        <i class="ph-bold ph-plus"></i>
                        <span>Add Department</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-buildings"></i> Organization Departments</h4>
                    <p class="table-subtitle">Manage company departments and codes</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="departments-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header-enhanced p-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #584D3C 0%, #9F956C 100%); color: white;">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="departmentModalLabel" style="font-size: 1.25rem;">
                        <i class="ph ph-buildings me-2" style="font-size: 1.5rem;"></i>
                        <span>Create Department</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="departmentForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="department_name" class="form-label fw-bold text-secondary">Department Name</label>
                                <input type="text" class="form-control" id="department_name" name="name" placeholder="e.g. Human Resources" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button class="btn btn-light text-secondary fw-bold px-4" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary-custom px-4 shadow-sm" type="submit" id="saveDepartmentBtn">
                            <i class="ph ph-floppy-disk me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- SweetAlert -->
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

            $(document).ready(function() {
                // === DataTable ===
                $('#departments-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('departments.index') }}"
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-department').on('click', function() {
                    $('#departmentModalLabel').text('Create Department');
                    $('#departmentForm')[0].reset();
                    $('#departmentForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#departmentForm .is-invalid').removeClass('is-invalid');
                    $('#departmentForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-department', function() {
                    const btn = $(this);
                    $('#departmentModalLabel').text('Edit Department');
                    $('#department_id').val(btn.data('id'));
                    $('#department_name').val(btn.data('name'));
                    $('#departmentForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    new bootstrap.Modal(document.getElementById('departmentModal')).show();
                });

                // === Submit Form ===
                $('#departmentForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let departmentId = $(this).attr('data-id');
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('departments.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('departments') }}/" + departmentId;
                        method = "POST";
                    }
                    let formData = $(this).serialize();
                    if (mode === 'edit') {
                        formData += '&_method=PUT';
                    }
                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        success: function(res) {
                            $('#departmentModal').modal('hide');
                            $('#departments-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Department created successfully' : 'Department updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-department-btn', function(e) {
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
                                    $('#departments-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Department deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete department');
                                }
                            });
                        } else {
                            warningMessage('Department deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
