<x-mail::message>

@php
    $emailType = $emailType ?? 'rejection_warning';
@endphp

@if($emailType === 'rejection_warning')
# ⚠️ Payment Proof Required

## What You Need To Do:
Please upload your payment proof document through our system to continue with your requisition process.

**Important Note**: Your requisition will remain on hold until the payment proof is provided.

@elseif($emailType === 'payment_confirmation')
# ✅ Payment Proof Received

- **Status**: Completed ✓

## What Happens Next:
Your requisition has been marked as **completed** and is now ready for processing. Your payment proof document is attached to this email for your records.


**Attached**: Payment proof document for your records.

@else
# Payment Notification

We have an update regarding your requisition slip **#{{ $requisition->no_srs }}**.

<x-mail::button :url="url('/complain')">
View Details
</x-mail::button>

@endif

---
**This is an automated message from the Requisition Management System.**

Best regards,<br>
{{ config('app.name') }} Team
</x-mail::message>
