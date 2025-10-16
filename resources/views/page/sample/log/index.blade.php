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
                                <th>Description</th>
                                <th>Event</th>
                                <th>Subject (No SRS)</th>
                                <th>Causer</th>
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
                $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('sample.log.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '20px', className: 'text-center' },
                        { data: 'log_name', name: 'log_name' },
                        { data: 'description', name: 'description' },
                        { data: 'event', name: 'event', className: 'text-center' },
                        { data: 'subject_info', name: 'subject.no_srs', orderable: false },
                        { data: 'causer_info', name: 'causer.name', orderable: false },
                        { data: 'created_at', name: 'created_at' }
                    ],
                    order: [[ 6, 'desc' ]] // Default sort by timestamp descending
                });
            });
        </script>
    @endpush
</x-app-layout>
