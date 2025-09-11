<x-app-layout>
    @section('title') Sample Requisition @endsection

    @push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .is-invalid+.select2-container .select2-selection--single {
            border-color: #dc3545;
        }

    </style>
    @endpush

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Sample Requisition List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip Form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Sample Requisition List</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-danger btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#requisitionModal">
                    <i class="ph-bold ph-plus pe-2"></i> Add Sample
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive">
                        <table class="w-100 display" id="requisitions-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Requester</th>
                                    <th>Customer</th>
                                    <th>Request Date</th>
                                    <th>Route To</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="requisitionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Create Sample Requisition</h5><button type="button"
                        class="btn-close m-0 fs-5" data-bs-dismiss="modal"></button>
                </div>
                <form id="requisitionForm" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3"><label for="sub_category" class="form-label fw-bold">1. Pilih Sub
                                Category</label><select class="form-select" id="sub_category" name="sub_category"
                                data-placeholder="-- Pilih Sub Category --"></select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div id="mainRequisitionForm" style="display: none;">
                            <h5 class="mt-4 mb-3 border-bottom pb-2">2. Requisition Details</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label">Customer<i class="text-danger">*</i><i class="text-danger">*</i></label>
                                    <select id="customer_id" name="customer_id" class="form-select"
                                        data-placeholder="-- Pilih Customer --">
                                        <option></option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="no_srs" class="form-label">No. SRS<i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" id="no_srs" name="no_srs">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="account" class="form-label">Account<i class="text-danger">*</i></label>
                                    <input type="text" class="form-control" id="account" name="account">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="cost_center" class="form-label">Cost Center<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="cost_center"
                                        name="cost_center">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="request_date" class="form-label">Request Date<i class="text-danger">*</i></label>
                                        <input type="date" class="form-control" id="request_date"
                                        name="request_date" value="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="objectives" class="form-label">Objectives<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="objectives" name="objectives">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="estimated_potential" class="form-label">Estimate Potential<i class="text-danger">*</i></label>
                                        <textarea type="text" class="form-control"
                                        id="estimated_potential" name="estimated_potential">
                                    </textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">3. Item Details</h5><button type="button"
                                    class="btn btn-sm btn-primary" id="add-item-btn"><i class="fas fa-plus pe-1"></i>
                                    Add Item</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            {{-- PERBAIKAN PADA HEADER TABEL --}}
                                            <th style="width: 30%;">Product</th>
                                            <th>Unit</th>
                                            <th>Qty Req.</th>
                                            <th>Qty Issued</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-container"></tbody>
                                </table>
                            </div>
                            <div id="item-validation-error" class="text-danger mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary" type="submit" id="saveRequisitionBtn">Save
                            changes</button><button class="btn btn-light-secondary" data-bs-dismiss="modal"
                            type="button">Close</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- Template untuk Baris Item (Diperbarui) --}}
    <table style="display: none;">
        <tr id="item-row-template">
            <td><select class="form-control select-product" name="items[__INDEX__][item_master_id]"></select>
                <div class="invalid-feedback"></div>
            </td>
            <td><input type="text" class="form-control item-unit" readonly></td>
            <td><input type="number" class="form-control" name="items[__INDEX__][quantity_required]" min="10">
                <div class="invalid-feedback"></div>
            </td>

            {{-- PERBAIKAN: Hapus readonly, value, dan class --}}
            <td><input type="number" class="form-control" name="items[__INDEX__][quantity_issued]" min="10"></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-item-btn"><i
                        class="fas fa-trash"></i></button></td>
        </tr>
    </table>

    @push('scripts')
    {{-- SCRIPT DI BAWAH INI TIDAK ADA PERUBAHAN --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            let itemIndex = 0;
            const modalElement = document.getElementById('requisitionModal');
            const requisitionModal = new bootstrap.Modal(modalElement);
            const successMessage = (msg) => Swal.fire({
                icon: 'success',
                title: 'Success',
                text: msg,
                timer: 1500,
                showConfirmButton: false
            });
            const errorMessage = (msg, title = 'Error') => Swal.fire({
                icon: 'error',
                title: title,
                text: msg
            });

            const table = $('#requisitions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sample.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'requester_name',
                        name: 'requester.name'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer.name'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'route_to',
                        name: 'route_to'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Inisialisasi Select2
            const commonSelect2Options = {
                theme: 'bootstrap-5',
                dropdownParent: $('#requisitionModal'),
                allowClear: true
            };

            // === BAGIAN YANG DIPERBAIKI ===
            // Ambil data sub category yang sudah difilter dari controller
            const allowedSubCategories = @json($allowedSubCategories);

            $('#sub_category').select2({
                ...commonSelect2Options,
                // Gunakan data dinamis tersebut, tambahkan opsi placeholder
                data: [{
                    id: '',
                    text: '-- Pilih Sub Category --'
                }].concat(allowedSubCategories)
            });

            $('#customer_id').select2({
                ...commonSelect2Options
            });

            const resetModal = () => {
                $('#requisitionForm')[0].reset();
                $('#mainRequisitionForm').hide();
                $('#items-container').empty();
                itemIndex = 0;
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback, #item-validation-error').text('');
                $('#sub_category, #customer_id').val(null).trigger('change');
            };
            modalElement.addEventListener('hidden.bs.modal', resetModal);

            $('#sub_category').on('change', function () {
                $(this).val() ? $('#mainRequisitionForm').slideDown() : $('#mainRequisitionForm')
                    .slideUp();
            });

            const initProductSelect = (element) => {
                element.select2({
                        ...commonSelect2Options,
                        placeholder: 'Cari produk...',
                        ajax: {
                            url: "{{ route('sample.searchItems') }}",
                            dataType: 'json',
                            delay: 250,
                            processResults: data => ({
                                results: data
                            }),
                            cache: true
                        }
                    })
                    .on('select2:select', e => $(e.currentTarget).closest('tr').find('.item-unit').val(e
                        .params.data.unit || 'N/A'));
            };

            $('#add-item-btn').on('click', () => {
                const newRowHtml = $('#item-row-template').html().replace(/__INDEX__/g, itemIndex);
                $('#items-container').append(`<tr>${newRowHtml}</tr>`);
                initProductSelect($(`select[name="items[${itemIndex}][item_master_id]"]`));
                itemIndex++;
            });

            $('#items-container').on('click', '.remove-item-btn', function () {
                $(this).closest('tr').remove();
            });

            $('#requisitionForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#saveRequisitionBtn');
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Saving...');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback, #item-validation-error').text('');

                $.ajax({
                    url: "{{ route('sample-form.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: (res) => {
                        requisitionModal.hide();
                        table.ajax.reload(null, false);
                        successMessage(res.message);
                    },
                    error: (xhr) => {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.items) $('#item-validation-error').text(errors.items[
                                0]);
                            $.each(errors, (key, value) => {
                                const name = key.replace(/\./g, '\\][').replace(/^/,
                                    '[') + ']';
                                const input = $(
                                    `[name="${key}"], [name^="${name.substring(0, name.indexOf('['))}"]`
                                ).last();
                                input.addClass('is-invalid').closest('td, div')
                                    .find('.invalid-feedback').text(value[0]);
                            });
                            errorMessage('Periksa kembali data yang Anda masukkan.',
                                'Validasi Gagal');
                        } else {
                            errorMessage(xhr.responseJSON.message ||
                                'Terjadi kesalahan tak terduga.');
                        }
                    },
                    complete: () => btn.prop('disabled', false).html('Save changes')
                });
            });
        });

    </script>
    @endpush
</x-app-layout>
