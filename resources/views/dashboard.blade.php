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
                    <li>
                        <a class="dropdown-item btn btn-outline-info" href="{{ route('sample-form.create') }}">
                            Sample
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn btn-outline-danger" href="{{ route('complain-form.create') }}">
                            Complain Packaging
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn btn-outline-success" href="{{ route('free-goods.create') }}">
                            FreeGoods
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="h-100 card hover-effect b-t-4-primary animate__animated animate__fadeInUp">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-primary text-primary"><i class="ti ti-box"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Sample Finished Goods</div>
                        {{-- Data_target value should be populated dynamically from backend --}}
                        <div class="metric-value animate-count" data-target="152">152</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 hover-effect b-t-4-info animate__animated animate__fadeInUp"
                style="animation-delay: 0.1s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-info text-info"><i class="ti ti-package-import"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Sample Packaging</div>
                        <div class="metric-value animate-count" data-target="75">75</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 hover-effect b-t-4-warning animate__animated animate__fadeInUp"
                style="animation-delay: 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-warning text-warning"><i class="ti ti-message-report"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Complain Packaging</div>
                        <div class="metric-value animate-count" data-target="12">12</div>
                        <div class="metric-change text-warning small">2 new complaints this week</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 hover-effect b-t-4-success animate__animated animate__fadeInUp"
                style="animation-delay: 0.3s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-success text-success"><i class="ti ti-gift"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Free Goods Request</div>
                        <div class="metric-value animate-count" data-target="48">48</div>
                        <div class="metric-change text-muted small">Total outstanding requests</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Requisition Statistics per Month</h5>
                    <div class="d-flex align-items-center">
                        <select class="form-select form-select-sm" id="yearFilterSelect" style="width: auto;">
                            <option value="2025" selected>Tahun 2025</option>
                            <option value="2024">Tahun 2024</option>
                            <option value="2023">Tahun 2023</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="monthlyRequisitionChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="card h-100 ticket-card bg-light-primary">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex-center bg-white mb-3"
                                style="width: 50px; height: 50px; border-radius: 15px; flex-shrink: 0;">
                                <i class="ti ti-file-plus fs-3 text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="fs-6 text-muted mb-1">Total Created</p>
                                <h3 class="text-primary-dark mb-0" id="summaryCreated">120</h3>
                                <small class="text-muted">Current Period</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 ticket-card bg-light-success">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex-center bg-white mb-3"
                                style="width: 50px; height: 50px; border-radius: 15px; flex-shrink: 0;">
                                <i class="ti ti-check fs-3 text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="fs-6 text-muted mb-1">Approved</p>
                                <h3 class="text-success-dark mb-0" id="summaryApproved">95</h3>
                                <small class="text-muted">Current Period</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 ticket-card bg-light-warning">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex-center bg-white mb-3"
                                style="width: 50px; height: 50px; border-radius: 15px; flex-shrink: 0;">
                                <i class="ti ti-clock fs-3 text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="fs-6 text-muted mb-1">Pending Approval</p>
                                <h3 class="text-warning-dark mb-0" id="summaryPending">20</h3>
                                <small class="text-muted">Current Period</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 ticket-card bg-light-danger">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex-center bg-white mb-3"
                                style="width: 50px; height: 50px; border-radius: 15px; flex-shrink: 0;">
                                <i class="ti ti-x fs-3 text-danger"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="fs-6 text-muted mb-1">Rejected</p>
                                <h3 class="text-danger-dark mb-0" id="summaryRejected">5</h3>
                                <small class="text-muted">Current Period</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Aktivitas Requisition Terbaru</h5>
                    <a href="#" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
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
                        <tbody>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00821</a></td>
                                <td>Andi Wijaya (SnM)</td>
                                <td>Sample Finished Goods</td>
                                <td><span class="badge bg-light-warning text-warning rounded-pill">Menunggu Persetujuan
                                        BC</span></td>
                                <td>15 menit lalu</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00820</a></td>
                                <td>Rina Hartono (RnD)</td>
                                <td>Sample Packaging</td>
                                <td><span class="badge bg-light-info text-info rounded-pill">Diproses di Gudang</span>
                                </td>
                                <td>1 jam lalu</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00819</a></td>
                                <td>Budi Santoso (SnM)</td>
                                <td>Packaging Replacement</td>
                                <td><span class="badge bg-light-danger text-danger rounded-pill">Ditolak oleh QA</span>
                                </td>
                                <td>2 jam lalu</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00818</a></td>
                                <td>Sales Team C</td>
                                <td>Free Goods</td>
                                <td><span class="badge bg-light-success text-success rounded-pill">Selesai</span></td>
                                <td>Kemarin</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00817</a></td>
                                <td>Citra Lestari (QA)</td>
                                <td>Sample FG (Special)</td>
                                <td><span class="badge bg-light-info text-info rounded-pill">Menunggu Tracking
                                        QA</span></td>
                                <td>Kemarin</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Tindakan Saya (4)</h5>
                </div>
                <div class="card-body p-0 simplebar-scroll" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0">
                        {{-- Action List Item --}}
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <div class="me-3">
                                <div
                                    class="avatar-sm bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-file-check fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Approve Request #REQ-00821</div>
                                <div class="text-muted small">Dari: Andi Wijaya | Kategori: Sample Finished Goods</div>
                            </div>
                        </li>
                        {{-- Action List Item --}}
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <div class="me-3">
                                <div
                                    class="avatar-sm bg-light-danger text-danger rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-alert-triangle fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Upload Bukti Bayar #REQ-00819</div>
                                <div class="text-muted small">Status: Ditolak oleh QA. Upload bukti pembayaran untuk
                                    melanjutkan.</div>
                            </div>
                        </li>
                        {{-- Action List Item --}}
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <div class="me-3">
                                <div
                                    class="avatar-sm bg-light-info text-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-truck-delivery fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Lacak Pengiriman #REQ-00818</div>
                                <div class="text-muted small">Untuk Request #REQ-00818 | Status: Dikirim</div>
                            </div>
                        </li>
                        {{-- Action List Item --}}
                        <li class="d-flex align-items-center p-3">
                            <div class="me-3">
                                <div
                                    class="avatar-sm bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-file-check fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Approve Request #REQ-00817</div>
                                <div class="text-muted small">Dari: Citra Lestari | Kategori: Sample FG (Special)</div>
                            </div>
                        </li>
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

                    <div class="tab-pane fade" id="free-goods" role="tabpanel">
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
        // Animated counting for metric cards
        function animateCount(el, target, duration = 1200) {
            let start = 0;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                el.textContent = Math.floor(progress * (target - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.textContent = target;
                }
            };
            window.requestAnimationFrame(step);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Dummy data generator for demonstration purposes
            const generateChartData = (year) => {
                return {
                    created: [80, 90, 75, 110, 95, 130, 140, 120, 100, 115, 90, 85],
                    approved: [70, 82, 65, 90, 80, 110, 120, 100, 90, 100, 80, 75],
                    pending: [5, 3, 5, 12, 10, 15, 10, 12, 5, 10, 5, 5],
                    rejected: [5, 5, 5, 8, 5, 5, 10, 8, 5, 5, 5, 5]
                };
            };

            // ApexCharts configuration options
            const getChartOptions = (data) => ({
                series: [
                    { name: 'Created', type: 'column', data: data.created, color: '#0d6efd' },
                    { name: 'Approved', type: 'line', data: data.approved, color: '#198754' },
                    { name: 'Pending', type: 'line', data: data.pending, color: '#ffc107' },
                    { name: 'Rejected', type: 'line', data: data.rejected, color: '#dc3545' }
                ],
                chart: {
                    height: 350,
                    type: 'line',
                    stacked: false,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    },
                    animations: { enabled: true },
                    // Responsive fix: redraw on parent resize
                    events: {
                        mounted: function(chartContext, config) {
                            window.addEventListener('resize', function() {
                                setTimeout(() => chartContext.resize(), 200);
                            });
                        }
                    }
                },
                stroke: {
                    width: [0, 3, 3, 3],
                    curve: 'smooth',
                    dashArray: [0, 0, 5, 5]
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Requisition',
                        style: { fontWeight: 500 }
                    },
                },
                tooltip: { shared: true, intersect: false },
                legend: { position: 'top', horizontalAlign: 'center', offsetY: 0 },
                dataLabels: { enabled: false }
            });

            let currentChart;
            const chartElement = document.querySelector("#monthlyRequisitionChart");

            function updateMetricCards() {
                document.querySelectorAll('.metric-value.animate-count').forEach(el => {
                    const target = parseInt(el.getAttribute('data-target')) || 0;
                    animateCount(el, target, 1200);
                });
            }

            const updateDashboard = (year) => {
                const data = generateChartData(year);

                // 1. Update Chart
                if (currentChart) {
                    currentChart.updateOptions(getChartOptions(data), true, true);
                } else {
                    currentChart = new ApexCharts(chartElement, getChartOptions(data));
                    currentChart.render();
                }

                // 2. Update Summary Cards
                const sum = arr => arr.reduce((acc, val) => acc + val, 0);
                document.getElementById('summaryCreated').textContent = sum(data.created);
                document.getElementById('summaryApproved').textContent = sum(data.approved);
                document.getElementById('summaryPending').textContent = sum(data.pending);
                document.getElementById('summaryRejected').textContent = sum(data.rejected);

                // 3. Animate metric cards
                updateMetricCards();
            };

            // Initial load
            const initialYear = document.getElementById('yearFilterSelect').value;
            updateDashboard(initialYear);

            // Add event listener to filter
            document.getElementById('yearFilterSelect').addEventListener('change', function() {
                updateDashboard(this.value);
            });

            // Animate metric cards on page load
            updateMetricCards();

            // Responsive fix: redraw chart on tab shown (if inside tab)
            document.addEventListener('shown.bs.tab', function(e) {
                if (currentChart) setTimeout(() => currentChart.resize(), 200);
            });
        });
        </script>
    @endpush
</x-app-layout>
