{{--
==========================================================
FREEGOODS TABLE STYLES COMPONENT
==========================================================
Include this component to apply consistent Free Goods table styling.
Usage: @include('components.freegoods-table-styles')

==========================================================
CSS CLASSES REFERENCE & USAGE GUIDE
==========================================================

🎨 MODAL STYLING:
• .modal-header                  → Standard modal header dengan golden brown gradient
• .modal-header-enhanced         → Enhanced modal header dengan pattern texture
• .modal-title-enhanced          → Title dengan icon dan styling
• .modal-body-enhanced           → Body dengan gradient background
• .modal-footer                  → Footer dengan light gray background

📋 TABLE CONTAINER:
• .main-table-container          → Container utama tabel dengan shadow dan rounded corners
• .table-header-enhanced         → Header tabel dengan golden brown gradient dan pattern
• .table-title                   → Title tabel dengan icon
• .table-subtitle                → Subtitle tabel dengan opacity

🔘 BUTTON STYLING:
• .new-freegoods-btn             → Button "New Free Goods" (sudah di-style via CSS utama)
• .btn-primary-theme             → Button dengan golden brown gradient theme
• .btn-secondary                 → Button abu-abu (override Bootstrap)
• .btn-danger                    → Button merah dengan dark theme
• .action-btn-group              → Container untuk action buttons di tabel
• .action-btn-hover              → Button dengan custom hover tooltip (membutuhkan atribut data-tooltip)

🏷️ STATUS BADGES:
• .status-badge-lg               → Badge status dengan enhanced styling
• .bg-warning.status-badge-lg    → Badge kuning untuk "Pending" dengan golden brown gradient
• .bg-success.status-badge-lg    → Badge hijau untuk "Approved" dengan aesthetic green gradient
• .bg-danger.status-badge-lg     → Badge merah untuk "Rejected" dengan gradient

📄 DETAIL MODAL SECTIONS:
• .detail-section                → Container section untuk detail modal
• .section-header                → Header section dengan golden brown gradient
• .info-card                     → Card untuk informasi dengan left border
• .info-row                      → Row informasi dengan label dan value
• .info-label                    → Label informasi dengan icon
• .info-value                    → Value field dengan background
• .slip-header-enhanced          → Header slip dengan border
• .slip-title-enhanced           → Title slip dengan center alignment

📊 PRODUCT & TABLE DETAILS:
• .product-list-container        → Container untuk list produk
• .product-item                  → Item produk dengan hover effect
• .objectives-container          → Container untuk objectives
• .objectives-text               → Text area untuk objectives
• .detail-table-container        → Container tabel detail dengan shadow
• .detail-table                  → Tabel detail dengan enhanced styling

🎯 DATATABLES ENHANCEMENTS:
• #fgTable                       → Main table dengan golden brown & green theme
• .dataTables_wrapper            → Wrapper dengan padding dan animations
• .dataTables_filter input       → Search input dengan golden brown focus
• .dataTables_length select      → Length selector dengan theme
• .dataTables_paginate           → Pagination dengan golden brown buttons
• .dataTables_info               → Info text dengan dark color

💡 TOOLTIP SYSTEM:
• .action-tooltip                → Custom tooltip untuk action buttons
• .action-tooltip.show           → Tooltip dalam state visible
• .action-btn-hover              → Element yang akan menampilkan tooltip

🎨 COLOR THEME PALETTE:
• Primary Gold: #c0954a
• Secondary Gold: #a07c3a
• Aesthetic Green: #4a7c59
• Dark Red: #a94442
• Dark Text: #2d2d2d

==========================================================
USAGE EXAMPLES:
==========================================================

1. TABLE CONTAINER:
<div class="main-table-container">
    <div class="table-header-enhanced">
        <h4 class="table-title">
            <i class="ph-duotone ph-list"></i>
            Free Goods Requisition List
        </h4>
        <p class="table-subtitle">View, manage and track all submissions</p>
    </div>
    <table id="fgTable">...</table>
</div>

2. ACTION BUTTONS WITH TOOLTIPS:
<div class="action-btn-group">
    <a href="..." class="btn btn-info btn-sm action-btn-hover" data-tooltip="View Details">
        <i class="ph-bold ph-eye"></i>
    </a>
    <button class="btn btn-secondary btn-sm action-btn-hover" data-tooltip="Edit">
        <i class="ph-bold ph-pencil"></i>
    </button>
</div>

3. STATUS BADGE:
<span class="badge bg-success status-badge-lg">Completed</span>

==========================================================
JAVASCRIPT REQUIREMENTS:
==========================================================
• Custom tooltip system sudah include di dalam komponen ini.
• DataTables, SweetAlert2, Select2, dan Bootstrap 5 direkomendasikan untuk fungsionalitas penuh.
--}}

@push('css')
    {{-- Memuat file CSS utama untuk Free Goods dari direktori public --}}
    <link rel="stylesheet" href="{{ asset('assets/css/freegoods-table-styles.css') }}">

    {{-- Bergantung pada Select2, pastikan link ini juga ada jika belum ada di layout utama --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
<script>
// ==========================================================
// SCRIPT UNTUK CUSTOM TOOLTIP PADA ACTION BUTTONS
// ==========================================================
// Script ini akan otomatis mencari elemen dengan class .action-btn-hover
// dan atribut data-tooltip="Isi tooltip Anda" untuk menampilkan
// tooltip kustom saat di-hover.
// ==========================================================
document.addEventListener('DOMContentLoaded', function () {
    // Cek apakah tooltip sudah ada untuk menghindari duplikasi
    if (!document.querySelector('.action-tooltip')) {
        const tooltip = document.createElement('div');
        tooltip.className = 'action-tooltip';
        document.body.appendChild(tooltip);

        document.body.addEventListener('mouseenter', function(event) {
            if (event.target.matches('.action-btn-hover')) {
                const button = event.target;
                const tooltipText = button.getAttribute('data-tooltip');
                if (tooltipText) {
                    tooltip.textContent = tooltipText;
                    tooltip.classList.add('show');
                    const rect = button.getBoundingClientRect();
                    const tooltipRect = tooltip.getBoundingClientRect();

                    // Posisikan tooltip di atas tombol
                    let top = rect.top - tooltipRect.height - 8; // 8px spasi
                    let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);

                    // Cek jika tooltip keluar dari viewport atas
                    if (top < 0) {
                        top = rect.bottom + 8; // Pindahkan ke bawah jika tidak cukup ruang
                    }

                    tooltip.style.left = `${left}px`;
                    tooltip.style.top = `${top + window.scrollY}px`; // Tambahkan scrollY untuk posisi absolut
                }
            }
        }, true); // Gunakan event capturing

        document.body.addEventListener('mouseleave', function(event) {
            if (event.target.matches('.action-btn-hover')) {
                 const tooltip = document.querySelector('.action-tooltip');
                 if (tooltip) {
                    tooltip.classList.remove('show');
                 }
            }
        }, true); // Gunakan event capturing
    }
});
</script>
@endpush