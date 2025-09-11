<x-app-layout>
    @section('title')
    Users List
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Users List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Complain form</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tabel Users -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-light-danger btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#complineModal" id="btn-create-compline">
                    <i class="ph-bold ph-plus pe-2"></i> New Complain
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="complainTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Requester</th>
                                    <th>Customer</th>
                                    <th>Cost Center</th>
                                    <th>Category</th>
                                    <th>Route To</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit User -->
    <div class="modal fade" id="complineModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="complineModalLabel">Create complain</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('complain-form.store') }}" method="POST" data-mode="create" data-id="" id="complineForm">
                    @csrf
                        <header class="row slip-header mb-2 align-items-center">
                            <div class="col-10">
                                <img src="{{ asset('storage/logo.png') }}" alt="Sinar Meadow Logo" class="logo" style="max-height: 60px; width: auto;">
                            </div>
                            <div class="col-2">
                                <p class="form-text text-start">
                                    FORM NO: FA-INV-05<br>
                                    REVISION: 3<br>
                                    DATE: 18 FEBRUARY 2021
                                </p>
                            </div>
                        </header>

                        <!-- judul modal -->
                        <div class="row mb-4 text-center">
                            <h4><strong>REQUISITION SLIP</strong></h4>
                            <p class="">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                        </div>
                
                        <!-- data modal -->
                        <div class="row mb-4 g-2">
                            <div class="col">
                                <div class="mb-3 row align-items-center">
                                    <label for="customer_id" class="col-sm-4 col-form-label"><strong>Customer Name :</strong></label>
                                    <div class="col-sm-7">
                                        <select name="customer_id" id="customer_id" class="form-select">
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="customer_address" class="col-sm-4 col-form-label"><strong>Customer Address :</strong></label>
                                    <div class="col-sm-7">
                                        <textarea class="form-control" id="customer_address" name="customer_address" rows="2" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3 row align-items-center">
                                    <label for="account" class="col-sm-3 col-form-label"><strong>Account :</strong></label>
                                    <div class="col-sm-8">
                                        <h5 id="account_display" style="font-weight: bold; text-align: center;"></h5>
                                        <input type="hidden" class="form-control" id="account" name="account">
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="cost_center" class="col-sm-3 col-form-label"><strong>Cost Center :</strong></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="cost_center" name="cost_center">
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="rs_number" class="col-sm-3 col-form-label"><strong>Nomor RS :</strong></label>
                                    <div class="col-sm-8">
                                        <h5 id="rs_number_display" style="font-weight: bold; text-align: center;"></h5>
                                        <input type="hidden" class="form-control" id="rs_number" name="rs_number">
                                    </div>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="date" class="col-sm-3 col-form-label"><strong>Tanggal :</strong></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <!-- tabel produk -->
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered slip-table">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT CODE</th>
                                            <th>PRODUCT NAME</th>
                                            <th>UNIT</th>
                                            <th>QTY REQUIRED</th>
                                            <th>OBJECTIVES</th>
                                            <th style="width: 5%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-list">
                                        <tr>
                                            <td><input type="text" name="codes[0]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="names[0]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="units[0]" class="form-control form-control-sm"></td>
                                            <td><input type="number" name="quantities[0]"
                                                    class="form-control form-control-sm" min="1"></td>
                                            <td><input type="text" name="objectives[0]" class="form-control form-control-sm">
                                            </td>
                                            <td class="d-flex gap-1 align-items-center" style="white-space:nowrap;">
                                                <button type="button" class="btn btn-sm btn-danger remove-row-btn" style="display:inline-flex; align-items:center;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning clear-row-btn" style="display:inline-flex; align-items:center;">
                                                    <i class="fa fa-eraser"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="add-row-btn" class="btn btn-sm btn-success mt-2">
                                    <i class="fa fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div>
                </div>

                <!-- footer modal -->
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="submit" id="saveUserBtn">
                        Save changes
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            let customerSelect = $('#customer_id');
            let addressField = $('#customer_address');
            
            // === hapus row ===
            $(document).on('click', '.remove-row-btn', function() {
                if ($('#product-list tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });

            // === clear row ===
            $(document).on('click', '.clear-row-btn', function() {
                $(this).closest('tr').find('input').val('');
            });

            // === DataTable ===
            $('#complainTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('get.complain.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'requester_nik',
                        name: 'requester_nik'
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id'
                    },
                    {
                        data: 'cost_center',
                        render: function(data, type, row) {
                            return data ? 'Rp' + data + '.00' : '-';
                        }
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'route_to',
                        name: 'route_to'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });

            $('#add-row-btn').on('click', function() {
                let rowCount = $('#product-list tr').length;
                let $lastRow = $('#product-list tr:last');
                let $newRow = $lastRow.clone();
                $newRow.find('input').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        let newName = name.replace(/\[\d+\]/, '[' + rowCount + ']');
                        $(this).attr('name', newName);
                    }
                    $(this).val('');
                });
                $('#product-list').append($newRow);
            });

            // === Modal Create ===
            $('#btn-create-compline').on('click', function() {
                $('#complineForm')[0].reset();
                $('#product-list tr:not(:first)').remove();
                $('#product-list tr:first input').val('');
                var today = new Date().toISOString().split('T')[0];
                $('#date').val(today);

                // menarik data costumer dari server
                $.ajax({
                    url: "{{ route('customers.list') }}",
                    method: "GET",
                    success: function(data) {

                        customerSelect.empty();
                        customerSelect.append('<option value=""></option>');
                        data.forEach(function(customer) {
                            customerSelect.append('<option value="' + customer.id + '">' + customer.name + '</option>');
                        });

                        customerSelect.select2({
                            theme: "bootstrap-5",
                            placeholder: "Pilih atau cari customer",
                            allowClear: true,
                            dropdownParent: $('#complineModal')
                        });

                        customerSelect.on('change', function() {
                            let selectedCustomer = $(this).val();
                            if (!selectedCustomer) {
                                addressField.val('');
                                return;
                            }
                            let selectedAddress = data.find(c => c.id == selectedCustomer)?.address || '';
                            addressField.val(selectedAddress);
                            
                        });
                    },
                    error: function() {
                        errorMessage('Failed to load customers');
                    }
                });

                $.ajax({
                    url: "{{ route('get.serial') }}",
                    method: "GET",
                    success: function(data) {
                        // menampilkan nomor account dan serial number di modal
                        $('#account_display').text(data.account_number);
                        $('#account').val(data.account_number);
                        $('#rs_number_display').text(data.series_number);
                        $('#rs_number').val(data.series_number);
                    },
                    error: function() {
                        $('#rs_number_display').text('serial number gagal dibuat');
                        errorMessage('Failed to generate RS number');
                    }
                });
            });

            // === Submit Form ===
            $('#complineForm').on('submit', function(e) {
                e.preventDefault();

                let mode = $(this).attr('data-mode');
                let userId = $(this).attr('data-id');
                let url, method;

                if (mode === 'create') {
                    url = "{{ route('complain-form.store') }}";
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
                        $('#complineModal').modal('hide');
                        $('#complainTable').DataTable().ajax.reload(null, false);
                        successMessage((mode === 'create') ? 'Complain created successfully' :
                            'Complain updated successfully');
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
                                successMessage(res.message || 'Complain deleted successfully!');
                            },
                            error: function(xhr) {
                                errorMessage(xhr.responseJSON?.message || 'Failed to delete complain');
                            }
                        });
                    } else {
                        warningMessage('Complain deletion canceled');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>