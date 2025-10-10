<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Requisition Slip - {{ $requisition->no_srs }}</title>
    <style>
        @page { margin: 0.5cm; }
        /* [DIUBAH] Ukuran font dasar dikecilkan lagi */
        body { font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size: 10pt; margin: 0; }
        .page { width: 93%; padding: 1cm; }
        .page-break { page-break-before: always; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th {
            /* [DIUBAH] Padding dikecilkan lagi */
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .bordered, .bordered th, .bordered td { border: 1px solid black; }
        .no-border, .no-border tr, .no-border td { border: none; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        tfoot th,
        tfoot td {
            text-align: center;
            vertical-align: top;
            padding: 5px 3px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
        }

        /* Garis atas judul tanda tangan */
        tfoot tr.signature-box th {
            border-top: 1px solid #000;
            border-bottom: none;
            padding: 3px 0;
        }

        /* Baris tanda tangan */
        tfoot tr.signature-row td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding-top: 10px;
            padding-bottom: 0;
        }

        /* khusus kolom Requester dan Issued (biar turun ke bawah) */
        tfoot tr.signature-row td:first-child,
        tfoot tr.signature-row td:nth-child(5) {
            vertical-align: bottom; /* turunkan posisi teks ke bawah */
            padding-top: 60px;       /* tambahkan jarak agar sejajar dengan Approved */
        }

        /* Nama tanda tangan */
        tfoot tr.signature-row td .name-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 12px;
            padding-bottom: 1px;
        }

        /* Baris jabatan */
        tfoot tr.title-row td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding-top: 0;
            padding-bottom: 2px;
            font-size: 12px;
        }

        /* Table Approved (tanpa garis tengah) */
        .approver-table {
            width: 100%;
            border-collapse: collapse;
        }
        .approver-table td {
            border: none !important;
            padding: 0;
            text-align: center;
        }

        .header-info { font-size: 8pt; }
        .main-title { font-size: 16pt; }
        .sub-title { font-size: 12pt; }
        .notes-column {
            vertical-align: top;
        }

        table.outer {
            font-family: 'Times New Roman', Times, serif;
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 12px;
            margin: 0 auto;
        }

        table.outer td,
        table.outer th {
            border: 1px solid black;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* PERUBAHAN DI SINI: Menambahkan beberapa class baru */
        .section-header td {
            border-left: none;
            border-right: none;
            border-top: none;
            background-color: #c9c9c9ff; /* Menambahkan background abu-abu seperti template */
        }

        .signature-title-row td {
            border-bottom: none;
            border-left: none;
            border-right: none;
        }

        .signature-name-row td {
            border: none;
        }

        /* [FINAL] Penyempurnaan CSS untuk Status & Word Wrapping */
        .status-indicator {
            padding: 3px 10px; /* Padding diatur untuk bentuk pil */
            border-radius: 12px; /* Dibuat lebih bulat */
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            border-width: 1.5px;
            border-style: solid;
            background-color: transparent !important; /* Latar belakang dihilangkan */
        }

        .status-approved {
            border-color: #28a745; /* Warna border hijau */
            color: #28a745;       /* Warna teks hijau */
        }

        .status-review {
            border-color: #E8A903; /* Warna border kuning/oranye */
            color: #E8A903;       /* Warna teks kuning/oranye */
        }

        .status-rejected {
            border-color: #dc3545; /* Warna border merah */
            color: #dc3545;       /* Warna teks merah */
        }

        .status-pending {
            border-color: #6c757d; /* Warna border abu-abu */
            color: #6c757d;       /* Warna teks abu-abu */
        }

        /* Style untuk membuat tabel lebih menarik */
        .striped-table thead th {
            background-color: #ffffffff;
            border-bottom: 1px solid #333;
            font-size: 10pt; /* Menambah ukuran font header */
            padding: 5px 3px; /* Menambah sedikit padding vertikal */
            text-transform: uppercase; /* Membuat teks menjadi kapital semua */
        }

        .striped-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* [DIUBAH] Menambahkan word-wrap & vertical-align */
        .striped-table td {
            padding: 4px 6px;
            word-wrap: break-word; /* INI SOLUSINYA: Memaksa teks untuk wrap */
            vertical-align: middle; /* Membuat konten di tengah secara vertikal */
        }
    </style>
</head>

<body>
    {{-- ======================================================= --}}
    {{-- ========= HALAMAN 1: TEMPLATE UTAMA (SEMUA TIPE) ========= --}}
    {{-- ======================================================= --}}

    <div class="page">
        <table class="bordered">
            <tr>
                <td style="padding: 10px;">
                    {{-- HEADER --}}
                    <table class="no-border">
                        <tr>
                            <td style="width: 25%;">
                                <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="max-height: 40px; vertical-align: middle;">
                                <span style="font-family: 'Times New Roman', Times, serif; font-size: 15pt; font-weight: bold; vertical-align: middle;">
                                    SINAR MEADOW
                                </span>
                            </td>
                            <td style="width: 60%;" class="text-center">
                                <div class="font-bold main-title" style="margin-bottom: 4px;">REQUISITION SLIP</div>
                                <div class="sub-title" style="margin-bottom: 1px;">SALES & MARKETING</div>
                                <div class="sub-title">SAMPLE PRODUCT</div>
                            </td>
                            <td style="width: 25%;" class="header-info">
                                <table class="bordered" style="width: 100%;">
                                    <tr>
                                        <td><strong>FORM NO.:</strong></td>
                                        <td>FA-INV-05</td>
                                    </tr>
                                    <tr>
                                        <td><strong>REVISION:</strong></td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td><strong>DATE:</strong></td>
                                        <td>19 FEBRUARY 2021</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div style="height: 20px;"></div>

                    {{-- CUSTOMER & RS INFO --}}
                    <table class="no-border" style="vertical-align: top;">
                        <tr>
                            <td style="width: 16%; vertical-align: top; padding-right: 30px;">
                                {{-- Tabel untuk info customer --}}
                                <table class="no-border">
                                    <tr>
                                        <td style="text-align: left; padding-right: 5px;"><strong>CUSTOMER NAME</strong></td>
                                        <td style="vertical-align: top; white-space: nowrap;">: {{ $requisition->customer->name ?? '-' }}</td>
                                    </tr>
                                    <br>
                                    <tr>
                                        <td style="vertical-align: top; text-align: left; padding-right: 5px;"><strong>ADDRESS</strong></td>
                                        <td style="vertical-align: top; white-space: nowrap; width: 100%;">: {{ $requisition->customer->address ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 40%; vertical-align: top; padding-left: 483px;">
                                {{-- Tabel untuk info RS --}}
                                <table class="no-border">
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Account</strong></td>
                                        <td>: {{ $requisition->account ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Cost Center</strong></td>
                                        <td>: {{ $requisition->cost_center ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</td>
                                    </tr>
                                    <br>
                                    <tr>
                                        <td style="text-align: right; padding-right: 5px;"><strong>Nomor RS</strong></td>
                                        <td>: <strong style="font-size: 14pt;">{{ $requisition->no_srs }}</strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- PRODUCT ITEM -->
                    <table class="bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">PRODUCT CODE</th>
                                <th>PRODUCT NAME</th>
                                <th style="width: 8%;">UNIT</th>
                                <th style="width: 8%;">QTY REQUIRED</th>
                                <th style="width: 8%;">QTY ISSUED</th>
                                <th style="width: 15%;">OBJECTIVES</th>
                                <th style="width: 15%;">Estimasi Potensi (Remarks in Carton)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $itemCount = $requisition->requisitionItems->count();
                                $minRows = 10;
                                $totalRows = max($itemCount, $minRows);
                            @endphp
                            @foreach($requisition->requisitionItems as $item)
                            <tr>
                                <td class="text-center">{{ $item->itemMaster->item_master_code ?? ($item->itemDetail->item_detail_code ?? '-') }}</td>
                                <td class="text-center">{{ $item->itemMaster->item_master_name ?? ($item->itemDetail->item_detail_name ?? '-') }}</td>
                                <td class="text-center">{{ $item->itemMaster->unit ?? ($item->itemDetail->unit ?? '-') }}</td>
                                <td class="text-center">{{ $item->quantity_required }}</td>
                                <td class="text-center">{{ $item->quantity_issued }}</td>
                                @if($loop->first)
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisition->objectives }}</td>
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisition->estimated_potential }}</td>
                                @endif
                            </tr>
                            @endforeach
                            @for ($i = $itemCount; $i < $minRows; $i++)
                            <tr>
                                <td>&nbsp;</td> <td></td> <td></td> <td></td> <td></td>
                                @if($i == 0)
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisition->objectives }}</td>
                                    <td class="notes-column text-center" rowspan="{{ $totalRows }}">{{ $requisition->estimated_potential }}</td>
                                @endif
                            </tr>
                            @endfor
                        </tbody>

                        <!-- <tfoot>
                            <tr class="signature-box">
                                <th colspan="1">Requested by</th>
                                <th colspan="3">Approved by</th>
                                <th colspan="2">Issued by</th>
                                <th colspan="1">Received by</th>
                            </tr>

                            <tr class="signature-row">
                                <td style="text-align:center; vertical-align:bottom; padding-top:70px;"><span class="name-line">{{ $requester->name ?? 'Ziddan Azzahra' }}</span></td>
                                <td colspan="3">
                                    <table class="approver-table">
                                        <tr>
                                            <td style="text-align:center; vertical-align:bottom; padding-top:70px;"><span class="name-line">{{ $firstApprover->name ?? 'User Requisition' }}</span></td>
                                            <td style="text-align:center; vertical-align:bottom; padding-top:70px;"><span class="name-line">{{ $lastApprover->name ?? 'Head HCD' }}</span></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align:center; vertical-align:bottom; padding-top:70px;" colspan="2"><span class="name-line"></span></td>
                                <td colspan="1"></td>
                            </tr>

                            <tr class="title-row">
                                <td>{{ $requester->department->name }}</td>
                                <td colspan="3">
                                    <table class="approver-table">
                                        <tr>
                                            <td>Business Controller Mgr.</td>
                                            <td>Atasan Dept</td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="2">Warehouse Supervisor</td>
                                <td colspan="1">(.............................)</td>
                            </tr>
                        </tfoot> -->
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- =================================================================== --}}
    {{-- ========= HALAMAN 2: KHUSUS JIKA TIPE SPECIAL ORDER ========= --}}
    {{-- =================================================================== --}}

    @if($requisition->sub_category === 'Special Order' && $requisition->requisitionSpecial)
        @php $special = $requisition->requisitionSpecial; @endphp
        <div class="page page-break">
            <table class="outer">
                {{-- HEADER --}}
                <tr>
                    <td colspan="6" style="text-align: center; font-weight: bold; font-size: 14px; padding: 2px 8px;">
                        PT. Sinar Meadow International Indonesia
                    </td>
                    <td rowspan="2" style="width: 25%; padding: 0;">
                        <table style="width: 100%; font-size: 10px;">
                            <tr>
                                <td style="border: none; padding: 3px 8px;">No</td>
                                <td style="border: none; padding: 3px 8px;">: F/F 08-01</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 3px 8px;">Revision</td>
                                <td style="border: none; padding: 3px 8px;">: 0</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 3px 8px;">Date</td>
                                <td style="border: none; padding: 3px 8px;">: 23 Jan 20</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 3px 8px;">Page</td>
                                <td style="border: none; padding: 3px 8px;">: 1 of 1</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="section-title"
                        style="text-align: center; font-weight: bold; font-size: 16px; padding: 2px 8px;">
                        PERMINTAAN SAMPLE
                    </td>
                </tr>
                <tr class="section-header">
                    <td colspan="7" class="text-center" style="font-style: italic; font-weight: bold;">Diisi oleh
                        Marketing</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Tanggal Permintaan</td>
                    <td style="white-space: nowrap; border: none;" colspan="5">: {{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</td>
                    <td style="white-space: nowrap; border: none;">No. SRS : <strong style="font-size: 14px;">{{ $requisition->no_srs }}</strong></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Tanggal Selesai Sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">: {{ \Carbon\Carbon::parse($special->end_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Produk</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">: <strong>{{ $special->products }}</strong></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Berat sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->weight_selection == '25 Kg')<b><u>a. 25 Kg</u></b>@else a. 25 Kg @endif
                        @if($special->weight_selection == '15 Kg')<b><u>b. 15 Kg</u></b>@else b. 15 Kg @endif
                        @if($special->weight_selection == '250 gr')<b><u>c. 250 gr</u></b>@else c. 250 gr @endif
                        @if($special->weight_selection == '500g')<b><u>d. 500 gr</u></b>@else d. 500 gr @endif
                        @if($special->weight_selection == '1 Kg')<b><u>e. 1 Kg</u></b>@else e. 1 Kg @endif
                        @if($special->weight_selection == '500 ml')<b><u>f. 500 ml</u></b>@else f. 500 ml @endif
                        @if($special->weight_selection == '1 lt')<b><u>g. 1 lt</u></b>@else g. 1 lt @endif
                        @if($special->weight_selection == '5 lt')<b><u>h. 5 lt</u></b>@else h. 5 lt @endif
                        i. Lainnya: .............
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Kemasan sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->packaging_selection == 'Tub')<b><u>a. Tub</u></b>@else a. Tub @endif
                        @if($special->packaging_selection == 'Karton')<b><u>b. Karton</u></b>@else b. Karton @endif
                        @if($special->packaging_selection == 'Botol')<b><u>c. Botol</u></b>@else c. Botol @endif
                        @if($special->packaging_selection == 'Jerrycan')<b><u>d. Jerrycan</u></b>@else d. Jerrycan @endif
                        e. Lainnya: .............
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Jumlah sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">: <strong>{{ $special->sample_count }}</strong></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Tujuan sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">: <strong>{{ $special->purpose }}</strong></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Certificate of Analysis</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->coa_required)<b><u>a. Ya</u></b>@else a. Ya @endif
                        @if(!$special->coa_required)<b><u>b. Tidak</u></b>@else b. Tidak @endif
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Dikirim melalui</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->shipment_method == 'Sales')<b><u>a. Sales</u></b>@else a. Sales @endif
                        @if($special->shipment_method == 'Delivery (DHL)')<b><u>b. Delivery (DHL)</u></b>@else b. Delivery (DHL) @endif
                        @if($special->shipment_method == 'Container')<b><u>c. Container</u></b>@else c. Container @endif
                        @if($special->shipment_method == 'Kurir')<b><u>d. Kurir</u></b>@else d. Kurir @endif
                        e. Lainnya: .............
                    </td>
                </tr>

                {{-- QA SECTION --}}
                <tr class="section-header">
                    <td colspan="7" class="text-center" style="font-style: italic; font-weight: bold;">Diisi oleh QA
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Asal sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->source == 'WH')<b><u>a. WH</u></b>@else a. WH @endif
                        @if($special->source == 'Reference Sample')<b><u>b. Reference Sample</u></b>@else b. Reference Sample @endif
                        @if($special->source == 'Batch Refinery')<b><u>c. Batch Refinery</u></b>@else c. Batch Refinery @endif
                        @if($special->source == 'Packing Room')<b><u>d. Packing Room</u></b>@else d. Packing Room @endif
                        e. Lainnya: .............
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Keterangan sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        : a. Batch / Pallet No: ........ P ........ b. WB/DEO No: ........ c. Tank No: ........
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Tgl Produksi</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        {{ $special->production_date ? \Carbon\Carbon::parse($special->production_date)->format('d M Y') : '...........................' }}
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Persiapan sample</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->preparation_method == 'Tidak berubah')<b><u>a. Tidak berubah</u></b>@else a. Tidak berubah @endif
                        @if($special->preparation_method == 'Rework Karton')<b><u>b. Rework Karton</u></b>@else b. Rework Karton @endif
                        @if($special->preparation_method == 'Rework Stencil')<b><u>c. Rework Stencil</u></b>@else c. Rework Stencil @endif
                        @if($special->preparation_method == 'Rework Label')<b><u>d. Rework Label</u></b>@else d. Rework Label @endif
                        e. Lainnya: .............
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; border: none;">Keterangan</td>
                    <td style="white-space: nowrap; border: none;" colspan="6">
                        :
                        @if($special->sample_notes == 'Tempel sticker')<b><u>a. Tempel sticker</u></b>@else a. Tempel sticker @endif
                        b. Lainnya: .............
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- ======================================================= --}}
    {{-- ============== HALAMAN 3: APPROVAL STATUS ============= --}}
    {{-- ======================================================= --}}
    <div class="page page-break">
        {{-- [DIUBAH] Menambahkan table luar agar konsisten dengan halaman 1 --}}
        <table class="bordered">
            <tr>
                <td style="padding: 10px;">
                    {{-- [DIUBAH] Header baru yang lebih profesional --}}
                    <table class="no-border" style="margin-bottom: 20px;">
                        <tr>
                            <td style="width: 25%;">
                                <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="max-height: 40px; vertical-align: middle;">
                                <span style="font-family: 'Times New Roman', Times, serif; font-size: 15pt; font-weight: bold; vertical-align: middle;">
                                    SINAR MEADOW
                                </span>
                            </td>
                            <td style="width: 60%;" class="text-center">
                                <div class="font-bold main-title" style="margin-bottom: 4px;">REQUISITION SLIP STATUS</div>
                                <div class="sub-title">SAMPLE PRODUCT: <strong>{{ $requisition->no_srs }}</strong></div>
                            </td>
                            <td style="width: 25%;">
                                <table class="no-border" style="table-layout: auto; width: auto;">
                                    <tr>
                                        {{-- KOLOM JUDUL: Dibuat agar lebarnya pas dengan teksnya --}}
                                        <td style="white-space: nowrap; padding-right: 3px;"><strong>Requester</strong></td>

                                        {{-- KOLOM TITIK DUA: Dibuat sempit --}}
                                        <td style="width: 5px;">:</td>

                                        {{-- KOLOM ISI --}}
                                        <td>{{ $requester->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="white-space: nowrap; padding-right: 3px;"><strong>Dept</strong></td>
                                        <td style="width: 5px;">:</td>
                                        <td>{{ $requester->department->name }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- [DIUBAH] Menambahkan class 'striped-table' untuk efek belang --}}
                    <table class="bordered striped-table">
                        <thead>
                            <tr>
                                <th style="width: 17%;">PCC Name</th>
                                <th style="width: 15%;">PCC Position</th>
                                <th style="width: 13%;">Status</th>
                                <th style="width: 13%;">Tanggal Approved</th>
                                <th>Notes / Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($approvals) && $approvals->count() > 0)
                                @foreach($approvals as $approval)
                                <tr>
                                    <td>{{ $approval->name }}</td>
                                    <td>{{ $approval->position }}</td>
                                    <td class="text-center">
                                        @if($approval->status == 'APPROVED NOT REVIEW')
                                            <span class="status-indicator status-approved">APPROVED NOT REVIEW</span>
                                        @elseif($approval->status == 'APPROVED WITH REVIEW')
                                            <span class="status-indicator status-review">APPROVED WITH REVIEW</span>
                                        @elseif($approval->status == 'NOT APPROVED')
                                            <span class="status-indicator status-rejected">NOT APPROVED</span>
                                        @else
                                            <span class="status-indicator status-pending">NOT REVIEWED</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($approval->approved_at)
                                            {{ \Carbon\Carbon::parse($approval->approved_at)->format('d M Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $approval->status == 'APPROVED NOT REVIEW' ? '' : $approval->notes ?? 'Tidak ada komentar' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center" style="padding: 20px;">No approval history found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </td>
            </tr>
        </table>
    </div>
</body>

</html>
