<x-app-layout>
    {{-- Set Title --}}
    @section('title')
        Dashboard Requisition Slip
    @endsection

    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="mb-0 fw-bold">Requisition Dashboard</h3>
            <small class="text-muted">Selamat datang kembali! Berikut adalah ringkasan data requisition slip.</small>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-plus me-1"></i> Buat Requisition Slip Baru
                </button>
                <ul class="dropdown-menu">
<<<<<<< HEAD
                    <li><a class="dropdown-item" href="{{ route('sample-form.index') }}">Sample</a></li>
                    <li><a class="dropdown-item" href="{{ route('get.complain.data') }}">Complain Packaging</a></li>
                    <li><a class="dropdown-item" href="{{ route('free-goods.create') }}">FreeGoods</a></li>
=======
                    <li><a class="dropdown-item" href="{{ route('sample-form.create') }}">Sample</a></li>
                    <li><a class="dropdown-item" href="{{ route('complain-form.create') }}">Complain Packaging</a></li>
                    <li><a class="dropdown-item" href="{{ route('freegoods-form.create') }}">FreeGoods</a></li>
>>>>>>> feature/free-goods
                </ul>
            </div>
        </div>
    </div>

    {{-- Kartu Metrik Dinamis --}}
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
        {{-- Kolom 1: Sample Packaging --}}
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-info animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-info text-info"><i class="ti ti-package-import"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Sample Packaging</div>
                        <div class="metric-value" id="metric_sample_pkg">0</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kolom 2: Sample Finished Goods --}}
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-primary animate__animated animate__fadeInUp">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-primary text-primary"><i class="ti ti-box"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Sample Finished Goods</div>
                        <div class="metric-value" id="metric_sample_fg">0</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kolom 3: Sample Special Order --}}
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-secondary animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-secondary text-secondary"><i class="ti ti-star"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Sample Special Order</div>
                        <div class="metric-value" id="metric_sample_so">0</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kolom 4: Complain Packaging --}}
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-warning animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-warning text-warning"><i class="ti ti-message-report"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Complain Packaging</div>
                        <div class="metric-value" id="metric_complain">0</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kolom 5: Free Goods Request --}}
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-success animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-success text-success"><i class="ti ti-gift"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Free Goods Request</div>
                        <div class="metric-value" id="metric_free_goods">0</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Chart dan Ringkasan Status --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Requisition Statistics per Month</h5>
                    <div class="d-flex align-items-center">
                        {{-- Year filter - menggunakan data tahun dari database requisition --}}
                        <select class="form-select form-select-sm" id="yearFilterSelect" style="width: auto;">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    {{-- [MODIFIKASI] style="min-height" ditambahkan sebagai fallback --}}
                    <div id="monthlyRequisitionChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            {{-- [MODIFIKASI] ID ditambahkan untuk kalkulasi tinggi chart --}}
            <div id="summaryCardsContainer" class="row row-cols-1 row-cols-sm-2 gx-3">
                <div class="col">
                    <div class="card h-100 ticket-card bg-light-primary">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-file-plus fs-3 text-primary"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Total Created</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Semua requisition yang pernah dibuat.</small>
                            <h3 class="text-primary-dark mb-0" id="summaryCreated">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 ticket-card bg-light-success">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-check fs-3 text-success"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Approved</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Selesai tahap approval, siap diproses menuju warehouse/QA.</small>
                            <h3 class="text-success-dark mb-0" id="summaryApproved">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 ticket-card bg-light-info">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-loader-2 fs-3 text-info"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">In Progress</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Sedang dalam proses approval bisnis controller.</small>
                            <h3 class="text-info-dark mb-0" id="summaryInProgress">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 ticket-card bg-light-warning">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-clock fs-3 text-warning"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Pending</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Request baru, menunggu approval pertama.</small>
                            <h3 class="text-warning-dark mb-0" id="summaryPending">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 ticket-card bg-light-danger">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-ban fs-3 text-danger"></i>
                            </div>
                            {{-- [MODIFIKASI] Judul, deskripsi, dan ID diubah --}}
                            <p class="fs-6 text-muted mb-0">Rejected / Cancelled</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Ditolak approver atau dibatalkan requester.</small>
                            <h3 class="text-danger-dark mb-0" id="summaryRejectedCancelled">0</h3>
                        </div>
                    </div>
                </div>
                {{-- [KARTU BARU] Kartu untuk Completed --}}
                <div class="col">
                    <div class="card h-100 ticket-card bg-light-dark">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-package-export fs-3 text-dark"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Completed</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Selesai proses warehouse/QA.</small>
                            <h3 class="text-dark mb-0" id="summaryCompleted">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top 5 Item dan Customer --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <h5 class="card-title mb-0">Top 5 Item Requisition</h5>
                        <div class="d-flex align-items-center flex-wrap gap-2" style="font-size: 0.8rem;">
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topItemCategoryFilter">
                                <option value="all">Semua Kategori</option>
                                <option value="sample">Sample</option>
                                <option value="complain">Complain</option>
                                <option value="freegoods">Free Goods</option>
                            </select>
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topItemMonthFilter">
                                 <option value="all">Semua Bulan</option>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                                @endforeach
                            </select>
                            {{-- Year filter - menggunakan data tahun dari database requisition --}}
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topItemYearFilter">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0 simplebar-scroll" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="topItemsList">
                        {{-- Data diisi oleh JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <h5 class="card-title mb-0">Top 5 Customer Requisition</h5>
                        <div class="d-flex align-items-center flex-wrap gap-2" style="font-size: 0.8rem;">
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topCustomerCategoryFilter">
                                <option value="all">Semua Kategori</option>
                                <option value="sample">Sample</option>
                                <option value="complain">Complain</option>
                                <option value="freegoods">Free Goods</option>
                            </select>
                             <select class="form-select form-select-sm top-filter" style="width: auto;" id="topCustomerMonthFilter">
                                 <option value="all">Semua Bulan</option>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                                @endforeach
                            </select>
                            {{-- Year filter - menggunakan data tahun dari database requisition --}}
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topCustomerYearFilter">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0 simplebar-scroll" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="topCustomersList">
                        {{-- Data diisi oleh JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru dan Tindakan Saya --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Aktivitas Requisition Terbaru</h5>
                    <a href="{{ route('sample-form.log') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Request ID</th>
                                <th scope="col">Requester</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody id="recentActivitiesTableBody">
                           {{-- Data diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Tindakan Saya (<span id="myActionsCount">0</span>)</h5>
                </div>
                <div class="card-body p-0 simplebar-scroll" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0" id="myActionsList">
                       {{-- Data diisi oleh JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card workflow-guide shadow-sm border-0">
                <div class="card-header border-bottom-0 pb-0 pt-3 bg-white">
                    <h5 class="card-title mb-0">Panduan Alur Kerja Requisition</h5>
                    <p class="text-muted small">Visualisasi proses approval berdasarkan kategori.
                    </p>
                    <ul class="nav nav-tabs mt-3" id="workflowTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sample-fg-tab" data-bs-toggle="tab"
                                data-bs-target="#sample-fg" type="button" role="tab">Sample Finished
                                Goods</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sample-pkg-tab" data-bs-toggle="tab"
                                data-bs-target="#sample-pkg" type="button" role="tab">Sample
                                Packaging</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pkg-replace-tab" data-bs-toggle="tab"
                                data-bs-target="#pkg-replace" type="button" role="tab">Packaging
                                Replacement</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="free-goods-tab" data-bs-toggle="tab"
                                data-bs-target="#free-goods" type="button" role="tab">Free
                                Goods</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content" id="workflowTabsContent">
                    <div class="tab-pane fade show active" id="sample-fg" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-4 border-end-lg">
                                <h6>Flow 1: RnD / QA (5302)</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content">
                                        <strong>Requester</strong><small>Memulai permintaan</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Atasan
                                            Requester</strong><small>Persetujuan atasan</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business
                                            Controller</strong><small>Pengecekan finansial</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-package"></i></div>
                                    <div class="step-content"><strong>Outward WH
                                            Supervisor</strong><small>Tracking & Pemenuhan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 border-end-lg">
                                <h6>Flow 2: SnM Normal (5300)</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content">
                                        <strong>Requester</strong><small>Memulai permintaan</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Atasan
                                            Requester</strong><small>Persetujuan atasan</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business
                                            Controller</strong><small>Pengecekan finansial</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-package"></i></div>
                                    <div class="step-content"><strong>Outward WH
                                            Supervisor</strong><small>Persiapan pemenuhan</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-truck"></i></div>
                                    <div class="step-content">
                                        <strong>Transportation</strong><small>Eksekusi
                                            pengiriman</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <h6>Flow 3: SnM Special Order (5300)</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content">
                                        <strong>Requester</strong><small>Mengisi detail pesanan
                                            khusus</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Atasan
                                            Requester</strong><small>Persetujuan atasan</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business
                                            Controller</strong><small>Pengecekan finansial</small>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-flask"></i></div>
                                    <div class="step-content"><strong>Quality Assurance
                                            (QA)</strong><small>Tracking, pengisian form, & submit
                                            akhir</small></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sample-pkg" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-6 border-end-lg">
                                <h6>Flow 1: SnM (5300)</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content"><strong>Requester</strong></div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Atasan Requester</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business Controller</strong>
                                    </div>
                                </div>

                                <div class="workflow-decision mt-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-help-hexagon-filled fs-4 text-warning me-2"></i>
                                        <strong>Titik Keputusan: Print Batch Number?</strong>
                                    </div>
                                    <div class="branch">
                                        <strong>YA:</strong>
                                        <div class="workflow-step ps-3">
                                            <div class="workflow-connector"></div>
                                            <div class="step-icon bg-light-secondary"><i class="ti ti-package"></i>
                                            </div>
                                            <div class="step-content"><small>Inward WH
                                                    Supervisor</small></div>
                                        </div>
                                        <div class="workflow-step ps-3">
                                            <div class="workflow-connector"></div>
                                            <div class="step-icon bg-light-secondary"><i class="ti ti-settings"></i>
                                            </div>
                                            <div class="step-content"><small>Material Support
                                                    Supervisor</small></div>
                                        </div>
                                        <div class="workflow-step ps-3">
                                            <div class="workflow-connector"></div>
                                            <div class="step-icon bg-light-secondary"><i class="ti ti-package"></i>
                                            </div>
                                            <div class="step-content"><small>Inward WH Supervisor
                                                    (Final check)</small></div>
                                        </div>
                                        <div class="workflow-step ps-3">
                                            <div class="workflow-connector"></div>
                                            <div class="step-icon bg-light-secondary"><i class="ti ti-truck"></i>
                                            </div>
                                            <div class="step-content"><small>Transportation</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="branch mt-2">
                                        <strong>TIDAK:</strong>
                                        <div class="workflow-step ps-3">
                                            <div class="workflow-connector"></div>
                                            <div class="step-icon bg-light-secondary"><i class="ti ti-package"></i>
                                            </div>
                                            <div class="step-content"><small>Inward WH
                                                    Supervisor</small></div>
                                        </div>
                                        <div class="workflow-step ps-3">
                                            <div class="workflow-connector"></div>
                                            <div class="step-icon bg-light-secondary"><i class="ti ti-truck"></i>
                                            </div>
                                            <div class="step-content"><small>Transportation</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6>Flow 2: RnD (5302)</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content"><strong>Requester</strong></div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Atasan Requester</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business Controller</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-package"></i></div>
                                    <div class="step-content"><strong>Inward WH Supervisor
                                            (Tracking)</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pkg-replace" role="tabpanel">
                        <h6>Alur Persetujuan (4914)</h6>
                        <div class="workflow-step">
                            <div class="workflow-connector"></div>
                            <div class="step-icon"><i class="ti ti-user"></i></div>
                            <div class="step-content"><strong>Requester</strong><small>Memulai
                                    permintaan penggantian</small></div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-connector"></div>
                            <div class="step-icon"><i class="ti ti-flask"></i></div>
                            <div class="step-content"><strong>Quality Assurance
                                    (QA)</strong><small>Pengecekan awal validitas</small></div>
                        </div>
                        <div class="workflow-decision mt-2 bg-light-danger">
                            <div class="d-flex align-items-center mb-2 text-danger">
                                <i class="ti ti-alert-circle-filled fs-4 me-2"></i>
                                <strong>Titik Keputusan: Penolakan QA</strong>
                            </div>
                            <div class="branch"><small>Jika ditolak, alur kembali ke
                                    <strong>Requester</strong> untuk mengunggah bukti pembayaran
                                    sebelum submit ulang.</small></div>
                        </div>
                        <div class="workflow-step mt-2">
                            <div class="workflow-connector"></div>
                            <div class="step-icon"><i class="ti ti-user-check"></i></div>
                            <div class="step-content"><strong>Atasan
                                    Requester</strong><small>Approval pasca-QA atau pasca-pembayaran
                                    </Gstrong>
                            </div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-connector"></div>
                            <div class="step-icon"><i class="ti ti-calculator"></i></div>
                            <div class="step-content"><strong>Business
                                    Controller</strong><small>Approval final finansial</small></div>
                        </div>
                        <hr class="my-3">
                        <h6>Alur Pengiriman</h6>
                        <p class="text-muted">Setelah disetujui, alur pengiriman mengikuti proses
                            yang sama dengan **Sample Packaging SnM**, termasuk titik keputusan
                            "Print Batch Number?".</p>
                    </div>

                    <div class="tab-pane fade" id="freegoods" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-6 border-end-lg">
                                <h6>Flow 1: Dari Departemen SnM</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content"><strong>Requester (SnM)</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-users"></i></div>
                                    <div class="step-content"><strong>SnM
                                            Manager</strong><small>Persetujuan kepala
                                            departemen</small></div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business Controller</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-package"></i></div>
                                    <div class="step-content"><strong>Outward WH Supervisor
                                            (Tracking)</strong></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6>Flow 2: Dari Departemen Lain</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content"><strong>Requester (Non-SnM)</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-building-community"></i></div>
                                    <div class="step-content"><strong>HCD Dept.
                                            Head</strong><small>Persetujuan kepala departemen
                                            terkait</small></div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Business Controller</strong>
                                    </div>
                                </div>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-package"></i></div>
                                    <div class="step-content"><strong>Outward WH Supervisor
                                            (Tracking)</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Push scripts to layout stack --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // Animated counting function
            function animateCount(el, target, duration = 1200) {
                let start = 0;
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const currentVal = Math.floor(progress * (target - start) + start);
                    el.textContent = currentVal.toLocaleString('id-ID'); // Format dengan pemisah ribuan
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        el.textContent = target.toLocaleString('id-ID'); // Pastikan nilai akhir tepat
                    }
                };
                window.requestAnimationFrame(step);
            }

            document.addEventListener("DOMContentLoaded", async function() {

                // Helper untuk fetch data dengan error handling
                async function fetchData(url) {
                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return await response.json();
                    } catch (error) {
                        console.error(`Could not fetch data from ${url}:`, error);
                        return null;
                    }
                }

                // === 1. METRIC CARDS INITIALIZATION ===
                async function loadMetricCounts() {
                    const data = await fetchData("{{ route('dashboard.data.metric-counts') }}");
                    if (!data) return;

                    animateCount(document.getElementById('metric_sample_fg'), data.sample_fg || 0);
                    animateCount(document.getElementById('metric_sample_pkg'), data.sample_pkg || 0);
                    animateCount(document.getElementById('metric_sample_so'), data.sample_so || 0);
                    animateCount(document.getElementById('metric_complain'), data.complain || 0);
                    animateCount(document.getElementById('metric_free_goods'), data.free_goods || 0);
                }

                // === YEAR OPTIONS DYNAMIC LOADING ===
                async function loadAvailableYears() {
                    try {
                        const years = await fetchData("{{ route('dashboard.data.available-years') }}");
                        if (!years || !Array.isArray(years)) return;

                        const currentYear = new Date().getFullYear();
                        
                        // Update semua year filter selects
                        const yearSelects = ['yearFilterSelect', 'topItemYearFilter', 'topCustomerYearFilter'];
                        
                        yearSelects.forEach(selectId => {
                            const selectElement = document.getElementById(selectId);
                            if (selectElement) {
                                const currentValue = selectElement.value;
                                selectElement.innerHTML = '';
                                
                                years.forEach(year => {
                                    const option = document.createElement('option');
                                    option.value = year;
                                    option.textContent = `Tahun ${year}`;
                                    option.selected = (year == currentYear || year == currentValue);
                                    selectElement.appendChild(option);
                                });
                            }
                        });
                    } catch (error) {
                        console.error('Failed to load available years:', error);
                    }
                }

                // === 2. CHART INITIALIZATION ===
                const getChartOptions = (data) => ({
                    series: [
                        { name: 'Created',     type: 'line', data: data.created,     color: '#0d6efd' },
                        { name: 'Approved',    type: 'line', data: data.approved,    color: '#198754' },
                        { name: 'In Progress', type: 'line', data: data.in_progress, color: '#0dcaf0' },
                        { name: 'Pending',     type: 'line', data: data.pending,     color: '#ffc107' },
                        { name: 'Rejected / Cancelled', type: 'line', data: data.rejected_cancelled, color: '#dc3545' },
                        { name: 'Completed',   type: 'line', data: data.completed,   color: '#212529' }
                    ],
                    chart: { type: 'line', stacked: false, toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }}},
                    // [MODIFIKASI] Kurangi jumlah array agar sesuai (menjadi 6)
                    stroke: { width: [3, 3, 3, 3, 3, 3], curve: 'smooth', dashArray: [0, 0, 0, 5, 0, 5] }, // DashArray disesuaikan
                    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
                    yaxis: { title: { text: 'Jumlah Requisition', style: { fontWeight: 500 }}},
                    tooltip: { shared: true, intersect: false },
                    legend: { position: 'top', horizontalAlign: 'center' },
                    dataLabels: { enabled: false }
                });

                let currentChart;
                const chartElement = document.querySelector("#monthlyRequisitionChart");
                const summaryCardsContainer = document.getElementById('summaryCardsContainer');

                // [FUNGSI BARU] Untuk menyesuaikan tinggi chart
                function adjustChartHeight() {
                    const chartCard = chartElement.closest('.card');
                    const chartCardHeader = chartCard.querySelector('.card-header');
                    const chartCardBody = chartCard.querySelector('.card-body');

                    if (chartElement && summaryCardsContainer && chartCardHeader && chartCardBody) {
                        // Get the total height of the summary cards block
                        const summaryHeight = summaryCardsContainer.offsetHeight;
                        // Get the height of the chart card's header
                        const headerHeight = chartCardHeader.offsetHeight;
                        // Get the vertical padding of the chart card's body
                        const bodyStyles = window.getComputedStyle(chartCardBody);
                        const bodyPaddingY = parseFloat(bodyStyles.paddingTop) + parseFloat(bodyStyles.paddingBottom);

                        // Calculate the target height for the chart itself
                        const newChartHeight = summaryHeight - headerHeight - bodyPaddingY;

                        // Set a minimum height to avoid errors
                        if (newChartHeight > 100) {
                            chartElement.style.height = `${newChartHeight}px`;
                            if (currentChart) {
                                currentChart.updateOptions({ chart: { height: newChartHeight } });
                            }
                        }
                    }
                }

                async function updateDashboardChart(year) {
                    const data = await fetchData(`{{ route('dashboard.data.monthly-stats') }}?year=${year}`);
                    if (!data) return;

                    if (currentChart) {
                        currentChart.updateOptions(getChartOptions(data));
                    } else if (chartElement) {
                        currentChart = new ApexCharts(chartElement, getChartOptions(data));
                        currentChart.render().then(() => {
                            setTimeout(adjustChartHeight, 100);
                        });
                    }

                    const sum = arr => arr.reduce((acc, val) => acc + val, 0);
                    
                    document.getElementById('summaryCreated').textContent = sum(data.created).toLocaleString('id-ID');
                    document.getElementById('summaryApproved').textContent = sum(data.approved).toLocaleString('id-ID');
                    document.getElementById('summaryInProgress').textContent = sum(data.in_progress).toLocaleString('id-ID');
                    document.getElementById('summaryPending').textContent = sum(data.pending).toLocaleString('id-ID');
                    document.getElementById('summaryCompleted').textContent = sum(data.completed).toLocaleString('id-ID');

                    // [MODIFIKASI] Gunakan data gabungan yang baru untuk total di kartu ringkasan
                    const totalRejectedCancelled = sum(data.rejected_cancelled || []);
                    document.getElementById('summaryRejectedCancelled').textContent = totalRejectedCancelled.toLocaleString('id-ID');
                };

                const yearFilterElement = document.getElementById('yearFilterSelect');
                yearFilterElement.addEventListener('change', function() {
                    updateDashboardChart(this.value);
                });

                window.addEventListener('resize', adjustChartHeight);

                // === 3. TOP 5 LISTS INITIALIZATION ===
                async function updateTop5List(type, filters) {
                    const listElement = document.getElementById(type === 'Items' ? 'topItemsList' : 'topCustomersList');
                    const url = new URL(type === 'Items' ? "{{ route('dashboard.data.top-items') }}" : "{{ route('dashboard.data.top-customers') }}");
                    url.search = new URLSearchParams(filters).toString();

                    const data = await fetchData(url);
                    listElement.innerHTML = ''; // Clear existing list

                    if (!data || data.length === 0) {
                        listElement.innerHTML = '<li class="list-group-item text-center text-muted">No data available.</li>';
                        return;
                    }

                    const maxTotal = Math.max(...data.map(item => item.total), 0);

                    data.forEach(item => {
                        const progress = maxTotal > 0 ? (item.total / maxTotal) * 100 : 0;
                        const progressBarColor = type === 'Items' ? 'bg-primary' : 'bg-success';
                        const skuOrCode = type === 'Items' ? item.sku : item.code;

                        const listItem = `
                            <li class="list-group-item px-0 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${item.name}</div>
                                    <small class="text-muted">${type === 'Items' ? 'SKU' : 'ID'}: ${skuOrCode}</small>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar ${progressBarColor}" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="ms-3 text-end">
                                    <span class="fw-bold fs-5">${item.total}</span>
                                    <div class="small text-muted">reqs</div>
                                </div>
                            </li>`;
                        listElement.insertAdjacentHTML('beforeend', listItem);
                    });
                }

                function getTopFilters(prefix) {
                    return {
                        category: document.getElementById(`${prefix}CategoryFilter`).value,
                        month: document.getElementById(`${prefix}MonthFilter`).value,
                        year: document.getElementById(`${prefix}YearFilter`).value,
                    };
                }

                document.querySelectorAll('.top-filter').forEach(filter => {
                    filter.addEventListener('change', () => {
                        updateTop5List('Items', getTopFilters('topItem'));
                        updateTop5List('Customers', getTopFilters('topCustomer'));
                    });
                });


                // === 4. RECENT ACTIVITIES & MY ACTIONS ===
                async function loadRecentActivities() {
                    const data = await fetchData("{{ route('dashboard.data.recent-activities') }}");
                    const tableBody = document.getElementById('recentActivitiesTableBody');
                    tableBody.innerHTML = '';

                    if (!data || data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent activities.</td></tr>';
                        return;
                    }

                    data.forEach(activity => {
                        const row = `
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#${activity.srs_number || 'N/A'}</a></td>
                                <td><span class="badge bg-dark text-light rounded-pill">${activity.requester_name}</span></td>
                                <td><span class="badge bg-primary text-light rounded-pill">${activity.category}</span></td>
                                <td><span class="badge bg-info text-light rounded-pill">${activity.status}</span></td>
                                <td>${activity.timestamp}</td>
                            </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                }

                async function loadMyActions() {
                    const data = await fetchData("{{ route('dashboard.data.my-actions') }}");
                    const listElement = document.getElementById('myActionsList');
                    document.getElementById('myActionsCount').textContent = data ? data.count : 0;
                    listElement.innerHTML = '';

                    if (!data || !data.notifications || data.notifications.length === 0) {
                        listElement.innerHTML = '<li class="p-3 text-center text-muted">No pending actions.</li>';
                        return;
                    }

                    data.notifications.forEach(notif => {
                        const item = `
                            <li class="d-flex align-items-center p-3 border-bottom">
                                <div class="me-3">
                                    <div class="avatar-sm bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti ti-file-check fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <a href="${notif.url}" class="fw-bold text-dark">${notif.message}</a>
                                    <div class="text-muted small">From: ${notif.causer_name} | ${notif.timestamp}</div>
                                </div>
                            </li>`;
                        listElement.insertAdjacentHTML('beforeend', item);
                    });
                }


                // === INITIAL DATA LOAD ===
                // Load available years first, then load other data
                await loadAvailableYears();
                
                loadMetricCounts();
                updateDashboardChart(yearFilterElement.value);
                updateTop5List('Items', getTopFilters('topItem'));
                updateTop5List('Customers', getTopFilters('topCustomer'));
                loadRecentActivities();
                loadMyActions();
            });
        </script>
    @endpush
</x-app-layout>

