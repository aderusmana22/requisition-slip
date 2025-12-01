<x-app-layout>
    @section('title')
        Permission List
    @endsection

    @include('components.master-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Permission List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-address-book f-s-16"></i> Master Data</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Permission List</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
             <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div>
                    <button class="btn btn-primary-custom" type="button" data-bs-toggle="modal" data-bs-target="#permissionModal" id="btn-create-permission">
                        <i class="ph-bold ph-plus"></i>
                        <span>Add Permission</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-lock-key"></i> Access Permissions</h4>
                    <p class="table-subtitle">Granular control over system capabilities</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="permissions-table">
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

    <div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header-enhanced p-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #584D3C 0%, #9F956C 100%); color: white;">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="permissionModalLabel" style="font-size: 1.25rem;">
                        <i class="ph ph-lock-key me-2" style="font-size: 1.5rem;"></i>
                        <span>Create Permission</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="permissionForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="permission_name" class="form-label fw-bold text-secondary">Permission Name</label>
                                <input type="text" class="form-control" id="permission_name" name="name" placeholder="e.g. create_user" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button class="btn btn-light text-secondary fw-bold px-4" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary-custom px-4 shadow-sm" type="submit" id="savePermissionBtn">
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
                $('#permissions-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('permissions.index') }}"
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-permission').on('click', function() {
                    $('#permissionModalLabel').text('Create Permission');
                    $('#permissionForm')[0].reset();
                    $('#permissionForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#permissionForm .is-invalid').removeClass('is-invalid');
                    $('#permissionForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-permission', function() {
                    const btn = $(this);
                    $('#permissionModalLabel').text('Edit Permission');
                    $('#permission_name').val(btn.data('name'));
                    $('#permissionForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    new bootstrap.Modal(document.getElementById('permissionModal')).show();
                });

                // === Submit Form ===
                $('#permissionForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let permissionId = $(this).attr('data-id');
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('permissions.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('permissions') }}/" + permissionId;
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
                            $('#permissionModal').modal('hide');
                            $('#permissions-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Permission created successfully' : 'Permission updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-permission-btn', function(e) {
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
                                    $('#permissions-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Permission deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete permission');
                                }
                            });
                        } else {
                            warningMessage('Permission deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
