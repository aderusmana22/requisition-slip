<x-app-layout>
    @section('title')
        Free Goods Activity Log
    @endsection

    {{-- Memuat file CSS khusus untuk tabel Free Goods --}}
    @include('components.freegoods-table-styles')

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
                                <th>No.</th>
                                <th>Log Name</th>
                                <th>Causer</th>
                                <th>Description</th>
                                <th>Event</th>
                                <th>Subject (FG No.)</th>
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
                $('#fgLogTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('freegoods.log.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%', className: 'text-center' },
                        { data: 'log_name', name: 'log_name', width: '15%', className: 'dt-no-wrap' }, // Penyesuaian lebar kolom
                        { data: 'causer_info', name: 'causer.name', width: '10%', orderable: false },
                        { data: 'description', name: 'description', width: '31%' },
                        { data: 'event', name: 'event', width: '10%', className: 'text-center' },
                        { data: 'subject_info', name: 'subject.no_srs', width: '9%', className: 'dt-no-wrap', orderable: false },
                        { data: 'subject_id', name: 'subject_id', width: '5%', className: 'text-center', orderable: false },
                        { data: 'created_at', name: 'created_at', width: '10%' }
                    ],
                    order: [[ 7, 'desc' ]] // Default sort by timestamp descending (kolom ke-8)
                });

                $('#fgLogTable_filter input').attr({
                    'placeholder': '🔍 Search logs...',
                    'class': 'form-control'
                });
            });
        </script>
    @endpush
</x-app-layout>