@component('mail::message')
# Persetujuan Requisition Complain

Halo **{{ $approver->name }}**,

Anda menerima permintaan persetujuan baru untuk requisition complain dengan nomor: **{{ $requisition->no_srs }}**.

Silakan tinjau detailnya dan berikan persetujuan Anda dengan menekan salah satu tombol di bawah ini.

@component('mail::button', ['url' => $approveLink, 'color' => 'success'])
Approve
@endcomponent

@component('mail::button', ['url' => $rejectLink, 'color' => 'error'])
Reject
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
