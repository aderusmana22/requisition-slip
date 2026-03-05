<x-app-layout>
    @section('title')
        Users List
    @endsection

    @include('components.master-table-styles')

    @push('css')
        <!-- Select2 CSS -->
        <link href="{{ asset('assets/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
    @endpush

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Users List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-address-book f-s-16"></i> Master
                        Data</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Users List</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div>
                    <button class="btn btn-primary-custom" type="button" data-bs-toggle="modal"
                        data-bs-target="#userModal" id="btn-create-user">
                        <i class="ph-bold ph-plus"></i>
                        <span>Add User</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-users"></i> Registered Users</h4>
                    <p class="table-subtitle">Manage system users, roles, and access rights</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="users-table">
                        <thead>
                            <tr>
                                <th>Nik</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header-enhanced p-4 d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, #584D3C 0%, #9F956C 100%); color: white;">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="userModalLabel"
                        style="font-size: 1.25rem;">
                        <i class="ph ph-users me-2" style="font-size: 1.5rem;"></i>
                        <span>Create User</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="userForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 text-center mb-3">
                                <div class="position-relative d-inline-block">
                                    <img id="avatarPreview" src="{{ asset('assets/images/logo/sinarmeadow.png') }}"
                                        alt="Avatar Preview"
                                        class="img-fluid rounded-circle shadow-sm border border-2 border-light"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                    <label for="avatar"
                                        class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm cursor-pointer"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="ph ph-camera text-primary"></i>
                                    </label>
                                </div>
                                <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*"
                                    onchange="previewAvatar(event)">
                                <div class="small text-muted mt-2">Click icon to upload photo</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="nik" class="form-label fw-bold text-secondary">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama" class="form-label fw-bold text-secondary">Name</label>
                                <input type="text" class="form-control" id="nama" name="name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="username" class="form-label fw-bold text-secondary">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="department_id" class="form-label fw-bold text-secondary">Department</label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">-- Select Department --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-bold text-secondary">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label fw-bold text-secondary">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    autocomplete="new-password" placeholder="Leave blank to keep current">
                            </div>

                            <div class="col-12 col-md-12">
                                <div class="select_info">
                                    <label for="atasan_nik" class="form-label">Atasan</label>
                                    <select class="form-select" style="width: 100%" id="atasan_nik"
                                        name="atasan_nik">
                                        <option value="">-- Pilih Atasan (Bisa kosong) --</option>
                                        @foreach ($atasans ?? [] as $nik => $name)
                                            <option value="{{ $nik }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="roles" class="form-label fw-bold text-secondary">Roles</label>
                                <select class="select-basic-multiple-four form-select" style="width: 100%"
                                    multiple="multiple" id="roles" name="roles[]">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6" id="row-status" style="display:none;">
                                <label for="status" class="form-label fw-bold text-secondary">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button class="btn btn-light text-secondary fw-bold px-4" data-bs-dismiss="modal"
                            type="button">Cancel</button>
                        <button class="btn btn-primary-custom px-4 shadow-sm" type="submit" id="saveUserBtn">
                            <i class="ph ph-floppy-disk me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Select2 -->
        <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
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
                // === DataTable ===
                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('users.data') }}"
                    },
                    columns: [{
                            data: 'nik',
                            name: 'nik'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'department',
                            name: 'department'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'roles',
                            name: 'roles',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                if (data === 'active') {
                                    return '<span class="badge bg-success">Active</span>';
                                } else {
                                    return '<span class="badge bg-danger">Non Active</span>';
                                }
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // === Select2 ===
                $('#roles').select2({
                    dropdownParent: $('#userModal'),
                    placeholder: "Pilih Roles"
                });

                $('#atasan_nik').select2({
                    dropdownParent: $('#userModal'),
                    placeholder: "Pilih Atasan",
                    allowClear: true
                });

                // === Avatar Preview ===
                window.previewAvatar = function(event) {
                    const input = event.target;
                    const preview = $('#avatarPreview');
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = e => preview.attr('src', e.target.result);
                        reader.readAsDataURL(input.files[0]);
                    }
                };

                // === Modal Create ===
                $('#btn-create-user').on('click', function() {
                    $('#userModalLabel').text('Create User');
                    $('#userForm')[0].reset();
                    $('#password').val('');
                    $('#avatarPreview').attr('src', '{{ asset('assets/images/logo/sinarmeadow.png') }}');
                    $('#roles').val(null).trigger('change');
                    $('#atasan_nik').val(null).trigger('change');

                    $('#userForm').attr('data-mode', 'create').removeAttr('data-id');

                    // Hide status field on create
                    $('#row-status').hide();

                    // clear error state kalau ada
                    $('#userForm .is-invalid').removeClass('is-invalid');
                    $('#userForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-user', function() {
                    const btn = $(this);
                    $('#userModalLabel').text('Edit User');

                    $('#nik').val(btn.data('nik'));
                    $('#nama').val(btn.data('name'));
                    $('#username').val(btn.data('username'));
                    $('#department_id').val(btn.data('department_id'));
                    $('#email').val(btn.data('email'));
                    $('#password').val('');
                    $('#roles').val(btn.data('roles')).trigger('change');
                     // set atasan if available
                    if (btn.data('atasan_nik')) {
                        $('#atasan_nik').val(btn.data('atasan_nik')).trigger('change');
                    } else {
                        $('#atasan_nik').val(null).trigger('change');
                    }

                    let avatar = btn.data('avatar');
                    if (avatar) {
                        $('#avatarPreview').attr('src', avatar.startsWith('http') ? avatar : avatar);
                    } else {
                        $('#avatarPreview').attr('src', '{{ asset('assets/images/logo/sinarmeadow.png') }}');
                    }

                    $('#userForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));

                    // Show status field on edit
                    $('#row-status').show();
                    // Set status value if available
                    if (btn.data('status')) {
                        $('#status').val(btn.data('status'));
                    } else {
                        $('#status').val('active');
                    }

                    new bootstrap.Modal(document.getElementById('userModal')).show();
                });

                // === Submit Form ===
                $('#userForm').on('submit', function(e) {
                    e.preventDefault();

                    let mode = $(this).attr('data-mode');
                    let userId = $(this).attr('data-id');
                    let url, method;

                    if (mode === 'create') {
                        url = "{{ route('users.store') }}";
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
                        success: function(res) {
                            $('#userModal').modal('hide');
                            $('#users-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'User created successfully' :
                                'User updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
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
                                    successMessage(res.message ||
                                        'User deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message ||
                                        'Failed to delete user');
                                }
                            });
                        } else {
                            warningMessage('User deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
