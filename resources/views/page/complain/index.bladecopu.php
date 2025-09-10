<x-app-layout>
    @section('title')
        Complain Form
    @endsection

    <header class="m-4 border p-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h1 class="h3 mb-0 text-gray-800">Complain</h1>
                <p class="mb-4">List of all complains</p>
            </div>
        </div>
        <div class="mb-3">
            <a href="{{ route('complain-form.create') }}" class="btn btn-primary">Create New Complain</a>
        </div>
    </header>

    <div class="m-4">
        <table id="complainTable" class="table table-striped">
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
            <tbody>
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
        $('#complainTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get.complain.data') }}",
            columns: [
                { data: 'id', name: 'id'},
                { data: 'requester_nik' },
                { data: 'customer_id' },
                { data: 'cost_center', render: function(data, type, row) {
                    return data ? 'Rp' + data : '-';}
                },
                { data: 'category' },
                { data: 'route_to' },
                { data: 'status' }
            ]
        });
    </script>
    @endpush
</x-app-layout>