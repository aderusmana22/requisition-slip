<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendTestEmail()
    {
        // Setup konfigurasi API
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $emailData = [
            'subject' => 'Requisition Slip Baru',
            'sender' => ['name' => 'Requisition App', 'email' => 'namaemail@domain.com'],
            'to' => [['email' => 'atasan@example.com', 'name' => 'Atasan']],
            'htmlContent' => '<p>Ada slip baru yang butuh persetujuan.</p>',
        ];

        try {
            $apiInstance->sendTransacEmail($emailData);
            return 'Email Brevo API terkirim!';
        } catch (\Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }
}
