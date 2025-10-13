# Warehouse Tracking Implementation

## Overview
Implementasi sistem warehouse tracking bertingkat yang akan dijalankan ketika `mailOtherLevel` tidak menemukan next approval log. Sistem ini akan mengirim email approval secara bertingkat berdasarkan data yang ada di table `trackings`.

## Flow Diagram
```
mailOtherLevel() 
    ↓
No nextApprovalLog found
    ↓
processWarehouseTracking()
    ↓
Check trackings table (order by ID)
    ↓
Find first pending/null status tracking
    ↓
Get approver by position
    ↓
Send email via sendPrintBatchMail::dispatch()
    ↓
Wait for approval response
    ↓
Process approval → Next tracking (if any)
```

## Database Schema Changes

### Trackings Table
Menggunakan field `last_updated` untuk menentukan status tracking:
- `last_updated = null`: Tracking belum diproses (siap untuk diproses)
- `last_updated = timestamp`: Tracking sedang/sudah diproses
- `token = null`: Tracking sudah selesai (approved/rejected)

Field yang digunakan:
- `requisition_id`: ID requisition
- `current_position`: Posisi warehouse (WH Supervisor First, Material Supervisor, WH Supervisor Final)
- `last_updated`: Timestamp untuk tracking status
- `notes`: Catatan dari tracking
- `token`: Token untuk approval link

**Logic Flow**:
1. Generate tracking dengan `last_updated = null`
2. Ketika email dikirim, set `last_updated = now()`
3. Ketika approved/rejected, set `token = null`

## New Functions

### 1. `processWarehouseTracking($requisitionId)`
**Purpose**: Memproses warehouse tracking berdasarkan requisition ID
**Features**:
- Mengambil semua tracking untuk requisition tertentu
- Mencari tracking pertama yang `last_updated = null`
- Menentukan approver berdasarkan position
- Mengirim email via `sendPrintBatchMail`
- Update `last_updated = now()` untuk menandai sedang diproses

### 2. `getApproverByPosition($position)`
**Purpose**: Mendapatkan user approver berdasarkan position di tracking
**Mapping**:
- `WH Supervisor First` → role: `wh-supervisor`
- `WH Supervisor Final` → role: `wh-supervisor`  
- `Material Supervisor` → role: `material-supervisor`

### 3. `processWarehouseApproval(Request $request)`
**Purpose**: Handle approval process dari email (GET/POST)
**Features**:
- Validasi token dan ID
- Cek tracking dengan `last_updated != null` (sedang diproses)
- Redirect ke direct approval atau review page
- Handle expired/invalid tokens

### 4. `showWarehouseReviewPage(Request $request)`
**Purpose**: Menampilkan halaman review untuk approval dengan notes
**Features**:
- Load requisition dengan semua relasi
- Tampilkan form approval dengan notes
- Validasi token sebelum show page

### 5. `processDirectWarehouseApproval($tracking)`
**Purpose**: Proses direct approval (langsung approve dari email)
**Features**:
- Set `token = null` untuk menandai selesai
- Update notes dengan approval info
- Check next tracking (yang `last_updated = null`) dan lanjutkan proses
- Update requisition status jika semua selesai

### 6. `processWarehouseApprovalWithValidation(Request $request, $tracking)`
**Purpose**: Proses approval dengan validasi form (approve/reject)
**Features**:
- Validasi input (status, notes)
- Handle approve: set `token = null`, lanjut ke next tracking (`last_updated = null`)
- Handle reject: set `token = null`, stop process, invalidate next trackings
- Update requisition status accordingly

## Email Template Updates

### print-batch-mail.blade.php
**Changes**:
- Updated level information dari `$approvalLog->level` menjadi `$tracking->current_position`
- Support untuk tracking-based approval links
- Improved responsive design

**Variables Available**:
- `$approver`: User object
- `$requisition`: Requisition dengan relasi
- `$tracking`: Tracking object  
- `$quickOkLink`: Direct approval URL
- `$okWithReviewLink`: Review page URL

## View Templates

### 1. warehouse-expired.blade.php
**Purpose**: Halaman untuk link expired/invalid
**Features**:
- User-friendly message
- Requisition information display
- Professional styling

### 2. warehouse-review.blade.php  
**Purpose**: Form review untuk approval
**Features**:
- Complete requisition details
- Approval form dengan notes
- AJAX submission
- Responsive design

### 3. warehouse-success.blade.php
**Purpose**: Success page setelah approval
**Features**:
- Confirmation message  
- Approval details
- Next step information
- Professional styling

## Routes Added

```php
Route::get('/complain/warehouse/approval', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.approval');
Route::get('/complain/warehouse/review', [ComplainController::class, 'showWarehouseReviewPage'])->name('complain.warehouse.review');
Route::post('/complain/warehouse/process', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.process');
Route::get('/complain/test-warehouse/{id}', [ComplainController::class, 'testWarehouseTracking'])->name('complain.test.warehouse');
```

## Testing

### Test Function: `testWarehouseTracking($requisitionId)`
**Purpose**: Test warehouse tracking system
**Usage**: `GET /complain/test-warehouse/{requisition_id}`
**Response**: JSON dengan status dan tracking details

## Usage Example

1. **Create Requisition**: Function `store()` calls `generateWarehouseTracking()`
2. **Approval Process**: Function `mailOtherLevel()` calls `processWarehouseTracking()` when no next approval log
3. **Email Sent**: `sendPrintBatchMail` job dispatched dengan tracking details
4. **User Clicks Email**: 
   - Quick OK → `processWarehouseApproval()` → Direct approval
   - Review → `showWarehouseReviewPage()` → Form approval
5. **Process Next**: Automatically process next tracking level
6. **Complete**: All trackings approved → Requisition status = 'Approved'

## Error Handling

- Token validation dan expiry check
- Database transaction untuk consistency  
- Comprehensive logging
- User-friendly error messages
- Graceful fallbacks

## Security Features

- Token-based authentication
- Request validation
- CSRF protection
- SQL injection prevention
- XSS protection dalam views

## Performance Considerations

- Efficient queries dengan proper indexing
- Background job processing untuk email
- Minimal data loading dalam loops
- Proper caching where applicable

## Maintenance

- Comprehensive logging untuk debugging
- Clear error messages
- Modular function design
- Easy configuration via role mapping
- Database migration included