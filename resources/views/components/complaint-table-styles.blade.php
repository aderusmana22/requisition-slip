{{-- 
==========================================================
COMPLAINT TABLE STYLES COMPONENT
==========================================================
Include this component to apply consistent complaint management table styling
Usage: @include('components.complaint-table-styles')

==========================================================
CSS CLASSES REFERENCE & USAGE GUIDE
==========================================================

🎨 MODAL STYLING:
• .modal-header                  → Standard modal header dengan gold gradient
• .modal-header-enhanced         → Enhanced modal header dengan pattern texture
• .modal-title-enhanced          → Title dengan icon dan gold styling
• .modal-body-enhanced           → Body dengan gradient background
• .modal-footer                  → Footer dengan light gray background

📋 TABLE CONTAINER:
• .main-table-container          → Container utama tabel dengan shadow dan rounded corners
• .table-header-enhanced         → Header tabel dengan gold gradient dan pattern
• .table-title                   → Title tabel dengan icon
• .table-subtitle                → Subtitle tabel dengan opacity

🔘 BUTTON STYLING:
• .new-complain-btn              → Button "New Complain" dengan gold gradient
• .btn-light-danger              → Button dengan gold gradient theme
• .btn-secondary                 → Button dengan gold gradient (override Bootstrap)
• .btn-danger                    → Button merah dengan dark theme
• .action-btn-group              → Container untuk action buttons di tabel
• .action-btn-hover              → Button dengan custom hover tooltip

🏷️ STATUS BADGES:
• .status-badge-lg               → Badge status dengan enhanced styling
• .bg-warning.status-badge-lg    → Badge kuning dengan gold gradient
• .bg-info.status-badge-lg       → Badge biru dengan gradient
• .bg-success.status-badge-lg    → Badge hijau dengan gradient
• .bg-danger.status-badge-lg     → Badge merah dengan gradient

📄 DETAIL MODAL SECTIONS:
• .detail-section                → Container section untuk detail modal
• .section-header                → Header section dengan gold gradient
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
• #complainTable                 → Main table dengan gold theme
• .dataTables_wrapper            → Wrapper dengan padding dan animations
• .dataTables_filter input       → Search input dengan gold focus
• .dataTables_length select      → Length selector dengan gold theme
• .dataTables_paginate           → Pagination dengan gold buttons
• .dataTables_info               → Info text dengan gold color

💡 TOOLTIP SYSTEM:
• .action-tooltip                → Custom tooltip untuk action buttons
• .action-tooltip.show           → Tooltip dalam state visible
• .action-btn-hover              → Element yang akan menampilkan tooltip

🎨 COLOR THEME:
• Primary Gold: rgb(192, 127, 0)
• Secondary Gold: rgb(160, 100, 0)
• Dark Gold: rgb(128, 80, 0)
• Text Dark: rgb(76, 61, 61)
• Light Blue (Edit): rgb(52, 144, 220)

📝 FORM ELEMENTS:
• .form-control:focus            → Input focus dengan gold border
• .form-select:focus             → Select focus dengan gold border
• .select2-container focus       → Select2 focus dengan gold theme
• .form-check-input:checked      → Checkbox/radio dengan gold color

⚡ ANIMATIONS:
• fadeInUp                       → Animation untuk section details
• Hover transforms               → Scale dan translateY effects
• Staggered delays               → Animation delays untuk multiple elements

==========================================================
USAGE EXAMPLES:
==========================================================

1. MODAL HEADER:
<div class="modal-header-enhanced">
    <h5 class="modal-title-enhanced">
        <i class="ph-duotone ph-file-text"></i>
        Your Title Here
    </h5>
</div>

2. TABLE CONTAINER:
<div class="main-table-container">
    <div class="table-header-enhanced">
        <h4 class="table-title">
            <i class="ph-duotone ph-list"></i>
            Table Title
        </h4>
        <p class="table-subtitle">Table description</p>
    </div>
    <table id="complainTable">...</table>
</div>

3. ACTION BUTTONS:
<div class="action-btn-group">
    <button class="btn btn-info action-btn-hover" data-tooltip="View Details">
        <i class="ph-bold ph-eye"></i>
    </button>
    <button class="btn btn-secondary action-btn-hover" data-tooltip="Edit">
        <i class="ph-bold ph-pencil"></i>
    </button>
    <button class="btn btn-danger action-btn-hover" data-tooltip="Delete">
        <i class="ph-bold ph-trash"></i>
    </button>
</div>

4. STATUS BADGE:
<span class="badge bg-warning status-badge-lg">Pending</span>

5. DETAIL SECTION:
/* Enhanced Modal Detail Styling */
.detail-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 1px solid rgba(192, 127, 0, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

/* Payment Proof Modal Styling */
#paymentProofModal .modal-content {
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    border: none;
}

#paymentProofModal .modal-header {
    background: linear-gradient(135deg, #c07f00 0%, #e8950c 100%);
    border-radius: 15px 15px 0 0;
    border: none;
    padding: 1.2rem 1.5rem;
}

#paymentProofModal .modal-body {
    padding: 2rem 1.5rem;
    background: #fafafa;
}

#paymentProofModal .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

#paymentProofModal .form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

#paymentProofModal .form-control:focus {
    border-color: #c07f00;
    box-shadow: 0 0 0 0.2rem rgba(192, 127, 0, 0.25);
    outline: 0;
}

#paymentProofModal .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

#paymentProofModal .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

#paymentProofModal .btn-secondary {
    background: #6c757d;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

#paymentProofModal .btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
}

#paymentProofModal .alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid #b8daff;
    color: #0c5460;
    border-radius: 8px;
}

.ph-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

==========================================================
JAVASCRIPT REQUIREMENTS:
==========================================================
• Custom tooltip system sudah include di component
• DataTables dengan server-side processing
• SweetAlert2 untuk confirmations
• Select2 untuk enhanced selects
• Bootstrap 5 untuk base styling

==========================================================
QUICK REFERENCE UNTUK DEVELOPER:
==========================================================

MODAL:          modal-header-enhanced, modal-title-enhanced, modal-body-enhanced
TABLE:          main-table-container, table-header-enhanced, table-title
BUTTONS:        new-complain-btn, action-btn-group, action-btn-hover
BADGES:         status-badge-lg + bg-warning/info/success/danger
DETAILS:        detail-section, section-header, info-row, info-label, info-value
COLORS:         Gold rgb(192,127,0), Dark rgb(76,61,61), Edit Blue rgb(52,144,220)
--}}

@push('css')
    <!-- Complaint Management Table Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/complaint-table-styles.css') }}">
    <!-- Select2 Integration -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush