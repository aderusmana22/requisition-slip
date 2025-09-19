@component('mail::message')
# Pengajuan Berhasil Terkirim

Halo **{{ $requesterName }}**,

Terima kasih. Permintaan Sample Requisition Anda dengan detail di bawah ini telah berhasil kami terima dan sedang dalam proses peninjauan oleh atasan Anda.

@component('mail::panel')
**No. SRS:** {{ $requisition->no_srs }} <br>
**Tanggal Permintaan:** {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }} <br>
**Customer:** {{ $requisition->customer->name ?? 'N/A' }} <br>
**Status Saat Ini:** {{ $requisition->status }} <br>
**Ditujukan Kepada:** {{ $requisition->route_to }}
@endcomponent

Anda akan menerima notifikasi lebih lanjut setelah permintaan Anda ditinjau.

{{-- Jika Anda memiliki halaman detail, Anda bisa menambahkan tombol ini --}}
{{--
@component('mail::button', ['url' => $viewUrl])
Lihat Detail Permintaan
@endcomponent
--}}

Terima kasih,<br>
Sistem Otomatis {{ config('app.name') }}
@endcomponent
