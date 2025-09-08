<x-app-layout>
    @section('title')
        Dashboard Requisition Slip
    @endsection
    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="mb-0 fw-bold">Requisition Dashboard</h3>
            <small class="text-muted">Selamat datang kembali! Berikut adalah ringkasan status
                permintaan barang saat ini.</small>
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
            <div class="h-100 warning animate__animated animate__fadeInUp card hover-effect b-t-4-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon"><i class="ti ti-file-invoice"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Awaiting Approval</div>
                        <div class="metric-value animate-count" data-target="5">0</div>
                        <div class="metric-change text-warning small">1 new since last login</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 info animate__animated animate__fadeInUp hover-effect b-t-4-success"
                style="animation-delay: 0.1s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon"><i class="ti ti-loader-2"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">In Process</div>
                        <div class="metric-value animate-count" data-target="28">0</div>
                        <div class="metric-change text-muted small">Across all departments</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 success animate__animated animate__fadeInUp hover-effect b-t-4-warning"
                style="animation-delay: 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon"><i class="ti ti-circle-check"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Completed</div>
                        <div class="metric-value animate-count" data-target="210">0</div>
                        <div class="metric-change text-success small">+10% compared to last month</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 danger animate__animated animate__fadeInUp hover-effect b-t-4-danger"
                style="animation-delay: 0.3s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon"><i class="ti ti-ban"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Rejected</div>
                        <div class="metric-value">3<span class="fs-5"> slips</span></div>
                        <div class="metric-change text-danger small">Down 1 slip</div>
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
                                <td><span class="status-badge status-pending">Menunggu Persetujuan BC</span></td>
                                <td>15 menit lalu</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00820</a></td>
                                <td>Rina Hartono (RnD)</td>
                                <td>Sample Packaging</td>
                                <td><span class="status-badge status-processing">Diproses di Gudang</span></td>
                                <td>1 jam lalu</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00819</a></td>
                                <td>Budi Santoso (SnM)</td>
                                <td>Packaging Replacement</td>
                                <td><span class="status-badge status-rejected">Ditolak oleh QA</span></td>
                                <td>2 jam lalu</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00818</a></td>
                                <td>Sales Team C</td>
                                <td>Free Goods</td>
                                <td><span class="status-badge status-approved">Selesai</span></td>
                                <td>Kemarin</td>
                            </tr>
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#REQ-00817</a></td>
                                <td>Citra Lestari (QA)</td>
                                <td>Sample FG (Special)</td>
                                <td><span class="status-badge status-processing">Menunggu Tracking QA</span></td>
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
                        <li class="action-list-item">
                            <div class="action-icon bg-light-warning text-warning"><i class="ti ti-file-check fs-4"></i></div>
                            <div>
                                <div class="fw-bold">Approve Request #REQ-00821</div>
                                <div class="text-muted small">Dari: Andi Wijaya | Kategori: Sample Finished Goods</div>
                            </div>
                        </li>
                        <li class="action-list-item">
                            <div class="action-icon bg-light-danger text-danger"><i class="ti ti-alert-triangle fs-4"></i></div>
                            <div>
                                <div class="fw-bold">Upload Bukti Bayar #REQ-00819</div>
                                <div class="text-muted small">Status: Ditolak oleh QA. Upload bukti pembayaran untuk melanjutkan.</div>
                            </div>
                        </li>
                        <li class="action-list-item">
                            <div class="action-icon bg-light-info text-info"><i class="ti ti-truck-delivery fs-4"></i></div>
                            <div>
                                <div class="fw-bold">Lacak Pengiriman #REQ-00818</div>
                                <div class="text-muted small">Untuk Request #REQ-00818 | Status: Dikirim</div>
                            </div>
                        </li>
                        <li class="action-list-item">
                            <div class="action-icon bg-light-warning text-warning"><i class="ti ti-file-check fs-4"></i></div>
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
</x-app-layout>
