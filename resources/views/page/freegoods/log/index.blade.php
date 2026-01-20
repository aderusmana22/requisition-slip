<x-app-layout>
    @section('title')
        Free Goods Activity Log
    @endsection

    {{-- Memuat file CSS khusus untuk tabel Free Goods --}}
    @include('components.freegoods-table-styles')

    @push('css')
        <style>
            /* [CUSTOM STATUS COLORS] - Matches Approval/Report */
            .status-pending { background-color: #fd7e14 !important; color: #ffffff !important; border: 1px solid #fd7e14; }
            .status-processing { background-color: #8B4513 !important; color: #ffffff !important; border: 1px solid #8B4513; } /* Coklat/Bronze */
            .status-completed { background-color: #198754 !important; color: #ffffff !important; border: 1px solid #198754; }
            .status-rejected { background-color: #dc3545 !important; color: #ffffff !important; border: 1px solid #dc3545; }
            .status-default { background-color: #6c757d !important; color: #fff !important; }

            /* [BADGE REQUESTER/CAUSER STYLE] - Dark Pill */
            .badge-requester {
                background-color: #343a40; 
                color: #ffffff;
                padding: 6px 16px;
                border-radius: 50rem; 
                display: inline-flex;
                align-items: center;
                font-weight: 500;
                font-size: 0.85em;
                box-shadow: 0 2px 4px rgba(0,0,0,0.15);
                min-width: 140px; 
                justify-content: center; /* Center content inside pill */
            }

            /* Search Clear Icon */
            .search-container { position: relative; display: inline-block; width: 100%; }
            .search-clear-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #adb5bd; display: none; z-index: 10; transition: color 0.2s; font-size: 1rem; }
            .search-clear-icon:hover { color: #dc3545; }
            
            /* Pointer on Header */
            table.dataTable thead th { cursor: pointer; }
        </style>
    @endpush

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Activity Log Monitoring</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-monitor f-s-16"></i> Monitoring</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Free Goods Activity Log</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-list-magnifying-glass"></i> System Activity Log</h4>
                    <p class="table-subtitle">A read-only log of all activities related to Free Goods Requisitions.</p>
                </div>
                <div class="table-responsive">
                    <table class="w-100 display" id="fgLogTable">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Log Name</th>
                                <th class="text-center">Causer</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Event</th>
                                <th class="text-center">Subject (FG No.)</th>
                                <th class="text-center">Subject ID</th>
                                <th class="text-center">Timestamp</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const table = $('#fgLogTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('freegoods.log.data') }}",
                    columns: [
                        { 
                            data: 'DT_RowIndex', 
                            name: 'DT_RowIndex', 
                            orderable: false, 
                            searchable: false, 
                            width: '5%', 
                            className: 'text-center align-middle' 
                        },
                        { 
                            data: 'log_name', 
                            name: 'log_name', 
                            width: '10%', 
                            className: 'text-center align-middle' 
                        }, 
                        { 
                            data: 'causer_info', 
                            name: 'causer.name', 
                            width: '15%', 
                            orderable: false,
                            className: 'text-center align-middle' 
                        },
                        { 
                            data: 'description', 
                            name: 'description', 
                            width: '25%',
                            className: 'align-middle' // Description left aligned is usually better for readability, but kept center based on request if needed, 'align-middle' handles vertical.
                        },
                        { 
                            data: 'event', 
                            name: 'event', 
                            width: '10%', 
                            className: 'text-center align-middle' 
                        },
                        { 
                            data: 'subject_info', 
                            name: 'subject.no_srs', 
                            width: '15%', 
                            className: 'dt-no-wrap text-center align-middle', 
                            orderable: false 
                        },
                        { 
                            data: 'subject_id', 
                            name: 'subject_id', 
                            width: '5%', 
                            className: 'text-center align-middle', 
                            orderable: false 
                        },
                        { 
                            data: 'created_at', 
                            name: 'created_at', 
                            width: '15%',
                            className: 'text-center align-middle'
                        }
                    ],
                    order: [[ 7, 'desc' ]] // Default sort by timestamp descending
                });

                // Search With Clear Icon Logic
                const filterInput = $('#fgLogTable_filter input');
                if (filterInput.parent().find('.search-container').length === 0) {
                    filterInput.unbind();
                    const wrapper = $('<div class="search-container"></div>');
                    filterInput.wrap(wrapper);
                    filterInput.attr({ 'placeholder': 'Search logs...', 'class': 'form-control ps-3 pe-5' });
                    $('<i class="ph-bold ph-x search-clear-icon" title="Clear Search"></i>').insertAfter(filterInput);
                }
                
                const clearIcon = $('.search-clear-icon');
                let debounceTimer;
                filterInput.on('keyup input', function (e) {
                    const val = $(this).val();
                    if (val.length > 0) clearIcon.show(); else clearIcon.hide();
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () { table.search(val).draw(); }, 500);
                });
                $(document).on('click', '.search-clear-icon', function() {
                    filterInput.val('').trigger('input');
                    table.search('').draw();
                });
            });
        </script>
    @endpush
</x-app-layout>