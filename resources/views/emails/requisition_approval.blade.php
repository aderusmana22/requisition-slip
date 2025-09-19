@component('mail::message')
# Permintaan Persetujuan Sample Requisition

Halo **{{ $approverName }}**,

Anda menerima permintaan persetujuan baru untuk Sample Requisition dengan detail sebagai berikut:

@component('mail::panel')
**No. SRS:** {{ $requisition->no_srs }} <br>
**Requester:** {{ $requisition->requester->name ?? 'N/A' }} <br>
**Tanggal Permintaan:** {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }} <br>
**Customer:** {{ $requisition->customer->name ?? 'N/A' }} <br>
**Sub Kategori:** {{ $requisition->sub_category }}
@endcomponent

---

### **Detail Tujuan & Potensi**
**Tujuan:**
> {{ $requisition->objectives }}

**Estimasi Potensi:**
> {{ $requisition->estimated_potential }}

---

### **Item yang Diminta**
@component('mail::table')
| Kode Item | Nama Item | Qty Diminta |
|:----------|:----------|:-----------:|
@foreach($requisition->requisitionItems as $item)
| {{ $item->itemMaster->item_master_code ?? ($item->itemDetail->item_detail_code ?? 'N/A') }} | {{ $item->itemMaster->item_master_name ?? ($item->itemDetail->item_detail_name ?? 'N/A') }} | {{ $item->quantity_required }} {{ $item->itemMaster->unit ?? ($item->itemDetail->unit ?? '') }} |
@endforeach
@endcomponent

@if($requisition->sub_category == 'Special Order' && $requisition->requisitionSpecial)
### **Detail Special Order**
- **Tgl. Selesai Sample:** {{ \Carbon\Carbon::parse($requisition->requisitionSpecial->requested_date)->format('d F Y') }}
- **Berat Sample:** {{ $requisition->requisitionSpecial->weight_selection }}
- **Kemasan Sample:** {{ $requisition->requisitionSpecial->packaging_selection }}
- **Jumlah Sample:** {{ $requisition->requisitionSpecial->sample_count }}
- **COA Diperlukan:** {{ $requisition->requisitionSpecial->coa_required ? 'Ya' : 'Tidak' }}
- **Dikirim Melalui:** {{ $requisition->requisitionSpecial->shipment_method }}
@endif

---

Silakan tinjau permintaan ini dan berikan persetujuan Anda dengan mengklik salah satu tombol di bawah ini.

@component('mail::button', ['url' => $approveUrl, 'color' => 'success'])
Approve
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'error'])
Reject
@endcomponent

Terima kasih,<br>
Sistem Otomatis {{ config('app.name') }}
@endcomponent
