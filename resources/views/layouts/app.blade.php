<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Website untuk form pengeluaran barang di Sinarmeadow" name="description">
    <meta content="requisition slip, sinarmeadow, pengeluaran barang, form, inventory, admin" name="keywords">
    <meta content="Sinarmeadow" name="author">
    <link href="{{ asset('assets/') }}/images/logo/favicon.png" rel="icon" type="image/x-icon">
    <link href="{{ asset('assets/') }}/images/logo/favicon.png" rel="shortcut icon" type="image/x-icon">

    <title>Requsiition Slip - @yield('title')</title>

    <!-- Fonts -->

    <!--font-awesome-css-->
    <link href="{{ asset('assets/') }}/vendor/fontawesome/css/all.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">


    <!-- iconoir icon css  -->
    <link href="{{ asset('assets/') }}/vendor/ionio-icon/css/iconoir.css" rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ asset('assets/') }}/vendor/tabler-icons/tabler-icons.css" rel="stylesheet" type="text/css">

    <!--animation-css-->
    <link href="{{ asset('assets/') }}/vendor/animation/animate.min.css" rel="stylesheet">

    <!--flag Icon css-->
    <link href="{{ asset('assets/') }}/vendor/flag-icons-master/flag-icon.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap css-->
    <link href="{{ asset('assets/') }}/vendor/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- simplebar css-->
    <link href="{{ asset('assets/') }}/vendor/simplebar/simplebar.css" rel="stylesheet" type="text/css">

    <!-- App css-->
    <link href="{{ asset('assets/') }}/css/style.css" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ asset('assets/') }}/css/responsive.css" rel="stylesheet" type="text/css">

    <style>
        /* --- Definisi Warna Status --- */
        :root {
            --bs-primary-rgb: 72, 94, 222;
            --bs-success-rgb: 26, 172, 110;
            --bs-warning-rgb: 247, 184, 75;
            --bs-danger-rgb: 239, 71, 111;
            --bs-info-rgb: 23, 162, 184;
        }

        /* --- Kartu Metrik KPI --- */
        .metric-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(var(--bs-primary-rgb), 0.08);
        }

        .metric-icon {
            padding: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: rgb(var(--bs-primary-rgb));
            font-size: 1.5rem;
            /* Ukuran icon ti */
        }

        .metric-card.success .metric-icon {
            background-color: rgba(var(--bs-success-rgb), 0.1);
            color: rgb(var(--bs-success-rgb));
        }

        .metric-card.warning .metric-icon {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
            color: rgb(var(--bs-warning-rgb));
        }

        .metric-card.danger .metric-icon {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
            color: rgb(var(--bs-danger-rgb));
        }

        .metric-card.info .metric-icon {
            background-color: rgba(var(--bs-info-rgb), 0.1);
            color: rgb(var(--bs-info-rgb));
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .metric-change {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* --- Daftar Tindakan & Log Aktivitas --- */
        .action-list-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .action-list-item:hover {
            background-color: #fcfcfc;
            cursor: pointer;
        }

        .action-list-item:last-child {
            border-bottom: none;
        }

        .action-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 1rem;
        }

        /* --- Badge Status --- */
        .status-badge {
            padding: 0.3em 0.7em;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 20px;
        }

        .status-pending {
            background-color: rgba(var(--bs-warning-rgb), 0.15);
            color: #a1741c;
        }

        .status-approved {
            background-color: rgba(var(--bs-success-rgb), 0.1);
            color: rgb(var(--bs-success-rgb));
        }

        .status-rejected {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
            color: rgb(var(--bs-danger-rgb));
        }

        .status-processing {
            background-color: rgba(var(--bs-info-rgb), 0.1);
            color: rgb(var(--bs-info-rgb));
        }

        /* --- Visualisasi Alur Kerja --- */
        .workflow-guide .nav-tabs .nav-link {
            font-weight: 600;
            color: #6c757d;
            border-bottom-width: 3px;
            border-color: transparent;
            padding: 0.75rem 1rem;
        }

        .workflow-guide .nav-tabs .nav-link.active {
            color: rgb(var(--bs-primary-rgb));
            border-color: rgb(var(--bs-primary-rgb));
        }

        .workflow-step {
            display: flex;
            align-items: center;
            position: relative;
            padding: 0.75rem 0;
        }

        .workflow-step .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eef2ff;
            color: #1e40af;
            font-weight: 600;
            flex-shrink: 0;
            margin-right: 1rem;
            z-index: 2;
        }

        .workflow-step .step-content strong {
            display: block;
            font-size: 1rem;
            color: #333;
        }

        .workflow-step .step-content small {
            color: #555;
            font-size: 0.9rem;
        }

        .workflow-connector {
            position: absolute;
            left: 20px;
            /* Tengahkan dengan ikon */
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #dbeafe;
            z-index: 1;
        }

        .workflow-step:first-child .workflow-connector {
            top: 50%;
            height: 50%;
        }

        .workflow-step:last-child .workflow-connector {
            height: 50%;
        }

        /* Styling Titik Keputusan */
        .workflow-decision {
            border-left: 3px solid rgb(var(--bs-warning-rgb));
            background-color: rgba(var(--bs-warning-rgb), 0.05);
            padding: 1rem;
            margin-left: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .workflow-decision .branch {
            margin-left: 2.5rem;
            position: relative;
            padding: 0.25rem 0;
        }

        .workflow-decision .branch::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 12px;
            width: 15px;
            height: 2px;
            background-color: rgb(var(--bs-warning-rgb));
        }

        .workflow-decision.bg-light-danger {
            border-left-color: rgb(var(--bs-danger-rgb));
            background-color: rgba(var(--bs-danger-rgb), 0.05);
        }

        .workflow-decision.bg-light-danger .branch::before {
            background-color: rgb(var(--bs-danger-rgb));
        }

        /* Penyesuaian Responsif untuk Garis Pemisah */
        @media (max-width: 991.98px) {
            .border-end-lg {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>

<body>
    <div class="app-wrapper">

        <div class="loader-wrapper">
            <div class="app-loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Menu Navigation starts -->
        @include('layouts.partials.nav')
        <!-- Menu Navigation ends -->


        <div class="app-content">
            <div class="">

                <!-- Header Section starts -->
                @include('layouts.partials.header')
                <!-- Header Section ends -->

                <!-- Body main section starts -->
                <main>
                    <div class="container-fluid mt-3">

                        <div class="row mb-3 align-items-center">
                            <div class="col-md-7">
                                <h3 class="mb-0 fw-bold">Requisition Dashboard</h3>
                                <small class="text-muted">Selamat datang kembali! Berikut adalah ringkasan status
                                    permintaan barang saat ini.</small>
                            </div>
                            <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                <button class="btn btn-primary shadow-sm">
                                    <i class="ti ti-plus me-1"></i> Buat Requisition Slip Baru
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="card metric-card h-100 warning animate__animated animate__fadeInUp">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="metric-icon"><i class="ti ti-file-invoice"></i></div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="text-muted small mb-1">Approval Tertunda Saya</div>
                                            <div class="metric-value animate-count" data-target="8">0</div>
                                            <div class="metric-change text-warning small">2 baru sejak login terakhir
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card metric-card h-100 info animate__animated animate__fadeInUp"
                                    style="animation-delay: 0.1s;">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="metric-icon"><i class="ti ti-loader-2"></i></div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="text-muted small mb-1">Total Proses Berjalan</div>
                                            <div class="metric-value animate-count" data-target="34">0</div>
                                            <div class="metric-change text-muted small">Di semua departemen</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card metric-card h-100 success animate__animated animate__fadeInUp"
                                    style="animation-delay: 0.2s;">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="metric-icon"><i class="ti ti-circle-check"></i></div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="text-muted small mb-1">Selesai Bulan Ini</div>
                                            <div class="metric-value animate-count" data-target="219">0</div>
                                            <div class="metric-change text-success small">+12% vs bulan lalu</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card metric-card h-100 danger animate__animated animate__fadeInUp"
                                    style="animation-delay: 0.3s;">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="metric-icon"><i class="ti ti-clock-hour-4"></i></div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="text-muted small mb-1">Waktu Proses Rata-rata</div>
                                            <div class="metric-value">1.8 <span class="fs-5">hari</span></div>
                                            <div class="metric-change text-danger small">Perbaikan 0.2 hari</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-lg-8">
                                <div class="card h-100 shadow-sm border-0">
                                    <div
                                        class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
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
                                                    <td><a href="#" class="fw-bold text-dark">#REQ-00821</a>
                                                    </td>
                                                    <td>Andi Wijaya (SnM)</td>
                                                    <td>Sample Finished Goods</td>
                                                    <td><span class="status-badge status-pending">Pending BC
                                                            Approval</span></td>
                                                    <td>15 menit lalu</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#" class="fw-bold text-dark">#REQ-00820</a>
                                                    </td>
                                                    <td>Rina Hartono (RnD)</td>
                                                    <td>Sample Packaging</td>
                                                    <td><span class="status-badge status-processing">Proses di
                                                            Gudang</span></td>
                                                    <td>1 jam lalu</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#" class="fw-bold text-dark">#REQ-00819</a>
                                                    </td>
                                                    <td>Budi Santoso (SnM)</td>
                                                    <td>Packaging Replacement</td>
                                                    <td><span class="status-badge status-rejected">Ditolak oleh
                                                            QA</span></td>
                                                    <td>2 jam lalu</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#" class="fw-bold text-dark">#REQ-00818</a>
                                                    </td>
                                                    <td>Sales Team C</td>
                                                    <td>Free Goods</td>
                                                    <td><span class="status-badge status-approved">Selesai</span></td>
                                                    <td>Kemarin</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#" class="fw-bold text-dark">#REQ-00817</a>
                                                    </td>
                                                    <td>Citra Lestari (QA)</td>
                                                    <td>Sample FG (Special)</td>
                                                    <td><span class="status-badge status-processing">Pending QA
                                                            Tracking</span></td>
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
                                        <h5 class="mb-0 card-title">Item Tindakan Saya (8)</h5>
                                    </div>
                                    <div class="card-body p-0 simplebar-scroll"
                                        style="max-height: 400px; overflow-y: auto;">
                                        <ul class="list-unstyled mb-0">
                                            <li class="action-list-item">
                                                <div class="action-icon bg-light-warning text-warning"><i
                                                        class="ti ti-file-check fs-4"></i></div>
                                                <div>
                                                    <div class="fw-bold">Approve Request #REQ-00821</div>
                                                    <div class="text-muted small">Dari: Andi Wijaya | Kategori: Sample
                                                        FG</div>
                                                </div>
                                            </li>
                                            <li class="action-list-item">
                                                <div class="action-icon bg-light-danger text-danger"><i
                                                        class="ti ti-alert-triangle fs-4"></i></div>
                                                <div>
                                                    <div class="fw-bold">Upload Bukti Bayar #REQ-00819</div>
                                                    <div class="text-muted small">Status: Ditolak QA. Upload bukti
                                                        untuk lanjut.</div>
                                                </div>
                                            </li>
                                            <li class="action-list-item">
                                                <div class="action-icon bg-light-info text-info"><i
                                                        class="ti ti-truck-delivery fs-4"></i></div>
                                                <div>
                                                    <div class="fw-bold">Lacak Pengiriman #SHP-00312</div>
                                                    <div class="text-muted small">Untuk Request #REQ-00815 | Status:
                                                        Dikirim</div>
                                                </div>
                                            </li>
                                            <li class="action-list-item">
                                                <div class="action-icon bg-light-warning text-warning"><i
                                                        class="ti ti-file-check fs-4"></i></div>
                                                <div>
                                                    <div class="fw-bold">Approve Request #REQ-00816</div>
                                                    <div class="text-muted small">Dari: Sales Team A | Kategori: Free
                                                        Goods</div>
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
                                                <button class="nav-link active" id="sample-fg-tab"
                                                    data-bs-toggle="tab" data-bs-target="#sample-fg" type="button"
                                                    role="tab">Sample Finished Goods</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="sample-pkg-tab" data-bs-toggle="tab"
                                                    data-bs-target="#sample-pkg" type="button" role="tab">Sample
                                                    Packaging</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pkg-replace-tab" data-bs-toggle="tab"
                                                    data-bs-target="#pkg-replace" type="button"
                                                    role="tab">Packaging Replacement</button>
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
                                                            <i
                                                                class="ti ti-help-hexagon-filled fs-4 text-warning me-2"></i>
                                                            <strong>Titik Keputusan: Print Batch Number?</strong>
                                                        </div>
                                                        <div class="branch">
                                                            <strong>YA:</strong>
                                                            <div class="workflow-step ps-3">
                                                                <div class="workflow-connector"></div>
                                                                <div class="step-icon bg-light-secondary"><i
                                                                        class="ti ti-package"></i></div>
                                                                <div class="step-content"><small>Inward WH
                                                                        Supervisor</small></div>
                                                            </div>
                                                            <div class="workflow-step ps-3">
                                                                <div class="workflow-connector"></div>
                                                                <div class="step-icon bg-light-secondary"><i
                                                                        class="ti ti-settings"></i></div>
                                                                <div class="step-content"><small>Material Support
                                                                        Supervisor</small></div>
                                                            </div>
                                                            <div class="workflow-step ps-3">
                                                                <div class="workflow-connector"></div>
                                                                <div class="step-icon bg-light-secondary"><i
                                                                        class="ti ti-package"></i></div>
                                                                <div class="step-content"><small>Inward WH Supervisor
                                                                        (Final check)</small></div>
                                                            </div>
                                                            <div class="workflow-step ps-3">
                                                                <div class="workflow-connector"></div>
                                                                <div class="step-icon bg-light-secondary"><i
                                                                        class="ti ti-truck"></i></div>
                                                                <div class="step-content"><small>Transportation</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="branch mt-2">
                                                            <strong>TIDAK:</strong>
                                                            <div class="workflow-step ps-3">
                                                                <div class="workflow-connector"></div>
                                                                <div class="step-icon bg-light-secondary"><i
                                                                        class="ti ti-package"></i></div>
                                                                <div class="step-content"><small>Inward WH
                                                                        Supervisor</small></div>
                                                            </div>
                                                            <div class="workflow-step ps-3">
                                                                <div class="workflow-connector"></div>
                                                                <div class="step-icon bg-light-secondary"><i
                                                                        class="ti ti-truck"></i></div>
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
                                                        <div class="step-icon"><i
                                                                class="ti ti-building-community"></i></div>
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

                    </div>
                </main>
                <!-- Body main section ends -->

                <!-- tap on top -->
                <div class="go-top">
                    <span class="progress-value">
                        <i class="ti ti-chevron-up"></i>
                    </span>
                </div>

                <!-- Footer Section starts-->
                @include('layouts.partials.footer')
                <!-- Footer Section ends-->

            </div>
        </div>
    </div>

    <!--customizer-->
    <div id="customizer"></div>

    <!-- latest jquery-->
    <script src="{{ asset('assets') }}/js/jquery-3.6.3.min.js"></script>

    <!-- Simple bar js-->
    <script src="{{ asset('assets') }}/vendor/simplebar/simplebar.js"></script>

    <!-- phosphor js -->
    <script src="{{ asset('assets') }}/vendor/phosphor/phosphor.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ asset('assets') }}/vendor/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- App js-->
    <script src="{{ asset('assets') }}/js/script.js"></script>

    <!-- Customizer js-->
    <script src="{{ asset('assets') }}/js/customizer.js"></script>

    // weather js
    <script>
        navigator.geolocation.getCurrentPosition(function(position) {
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;

            fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&daily=temperature_2m_max,precipitation_probability_mean&timezone=auto`
                )
                .then(res => res.json())
                .then(data => {
                    // === tampilkan suhu sekarang di header ===
                    document.getElementById("current-temp").innerHTML =
                        `${data.current_weather.temperature} <sup class="f-s-10">°C</sup>`;

                    // === tampilkan forecast harian ===
                    let forecast = data.daily;
                    let container = document.getElementById("forecast-box");
                    container.innerHTML = ""; // reset isi dulu

                    forecast.time.forEach((date, i) => {
                        let day = new Date(date).toLocaleDateString('en-US', {
                            weekday: 'short'
                        });
                        let temp = forecast.temperature_2m_max[i];
                        let rain = forecast.precipitation_probability_mean[i];

                        container.innerHTML += `
                    <div class="cloud-box bg-primary-${900 - (i*100)}">
                        <p class="mb-3">${day}</p>
                        <h6 class="mt-4 f-s-13">+${temp}°C</h6>
                        <span>
                            <i class="ph-duotone ph-sun-dim text-white f-s-25"></i>
                        </span>
                        <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> ${rain}%</p>
                    </div>
                `;
                    });
                });
        }, function(error) {
            console.error("Geolocation error:", error);
        });
    </script>
    
</body>


</html>
