<x-app-layout>
    @section('title')
        Customer List
    @endsection

    @include('components.master-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Customer List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-address-book f-s-16"></i> Master Data</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Customer List</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
             <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div>
                    <button class="btn btn-primary-custom" type="button" data-bs-toggle="modal" data-bs-target="#customerModal" id="btn-create-customer">
                        <i class="ph-bold ph-plus"></i>
                        <span>Add Customer</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-users-three"></i> Customers Database</h4>
                    <p class="table-subtitle">Manage registered customer information</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="customers-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header-enhanced p-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #584D3C 0%, #9F956C 100%); color: white;">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="customerModalLabel" style="font-size: 1.25rem;">
                        <i class="ph ph-users-three me-2" style="font-size: 1.5rem;"></i>
                        <span>Create Customer</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="customerForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="customer_name" class="form-label fw-bold text-secondary">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="name" required>
                            </div>
                            <div class="col-12">
                                <label for="customer_address" class="form-label fw-bold text-secondary">Address</label>
                                <textarea class="form-control" id="customer_address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button class="btn btn-light text-secondary fw-bold px-4" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary-custom px-4 shadow-sm" type="submit" id="saveCustomerBtn">
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
                $('#customers-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customers.index') }}"
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'address', name: 'address' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-customer').on('click', function() {
                    $('#customerModalLabel').text('Create customer');
                    $('#customerForm')[0].reset();
                    $('#customerForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#customerForm .is-invalid').removeClass('is-invalid');
                    $('#customerForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-customer', function() {
                    const btn = $(this);
                    $('#customerModalLabel').text('Edit customer');
                    $('#customer_id').val(btn.data('id'));
                    $('#customer_name').val(btn.data('name'));
                    $('#customer_address').val(btn.data('address'));
                    $('#customerForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    new bootstrap.Modal(document.getElementById('customerModal')).show();
                });

                // === Submit Form ===
                $('#customerForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let customerId = $(this).attr('data-id');
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('customers.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('customers') }}/" + customerId;
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
                            $('#customerModal').modal('hide');
                            $('#customers-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Customer created successfully' : 'Customer updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-customer-btn', function(e) {
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
                                    $('#customers-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Customer deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete Customer');
                                }
                            });
                        } else {
                            warningMessage('Customer deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
