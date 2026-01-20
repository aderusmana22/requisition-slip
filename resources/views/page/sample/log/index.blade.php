<x-app-layout>
    @section('title')
        Sample Requisition Activity Log
    @endsection

    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Activity Log Monitoring</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-monitor f-s-16"></i> Monitoring</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Sample Activity Log</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-list-magnifying-glass"></i> System Activity Log</h4>
                    <p class="table-subtitle">A read-only log of all activities related to Sample Requisitions.</p>
                </div>
                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Log Name</th>
                                <th>Causer</th>
                                <th>Description</th>
                                <th>Event</th>
                                <th>Subject</th>
                                <th>Subject ID</th>
                                <th>Timestamp</th>
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
                const table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[ 0, 'desc' ]],
                    ajax: "{{ route('sample.log.data') }}",
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: false,
                            width: '5%',
                            className: 'text-center',
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        { data: 'log_name', name: 'log_name', width: '15%', className: 'dt-no-wrap' },
                        { data: 'causer_info', name: 'causer.name', width: '10%', orderable: false },
                        { data: 'description', name: 'description', width: '31%' },
                        { data: 'event', name: 'event', width: '10%', className: 'text-center' },
                        { data: 'subject_info', name: 'subject_id', width: '9%', className: 'dt-no-wrap', orderable: false },
                        { data: 'subject_id', name: 'subject_id', width: '5%', className: 'text-center', orderable: false },
                        { data: 'created_at', name: 'created_at', width: '10%' }
                    ]
                });

                let searchInput = $('#sampleTable_filter input');
                searchInput.attr({
                    'placeholder': '🔍 Search log...',
                    'class': 'form-control'
                });

                searchInput.unbind();
                let debounceTimer;

                searchInput.on('keyup search input', function (e) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        let searchTerm = searchInput.val();
                        table.search(searchTerm).draw();
                    }, 500);
                });
            });
        </script>
    @endpush
</x-app-layout>
