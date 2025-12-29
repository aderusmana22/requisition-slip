<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Proof Required</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7fc; line-height: 1.6; height: 100% !important; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f4f7fc;">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"
                    style="max-width: 600px; background-color: #ffffff; border-collapse: collapse; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);"
                    class="email-wrapper">

                    <tr>
                        <td align="center" bgcolor="#ee5a24"
                            style="background: #ee5a24; background-image: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); padding: 40px 30px; border-radius: 0;">

                            <h1
                                style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); mso-line-height-alt: 35px;">
                                Payment Proof Required</h1>
                            <p style="color: rgba(255, 255, 255, 0.9); font-size: 16px; margin: 10px 0 0 0;">
                                Action Required for Your Requisition</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px;">

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse; background: #fff3cd; background-image: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%); border-left: 5px solid #ffc107; padding: 20px; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 0;">
                                        <h2
                                            style="color: #856404; font-size: 18px; font-weight: 600; margin: 0 0 10px 0;">
                                            <span style="font-size: 20px; margin-right: 10px;">⚠️</span>Important Notice
                                        </h2>
                                        <p style="color: #856404; margin: 0; font-size: 15px;">
                                            Your requisition has been reviewed and requires payment proof to proceed
                                            further.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse;">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 1px; padding: 0;">&nbsp;</td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse; background: #e3f2fd; background-image: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 10px; padding: 25px; border: 2px solid #2196f3;">
                                <tr>
                                    <td style="padding: 0;">
                                        <h3
                                            style="color: #1565c0; font-size: 16px; font-weight: 600; margin: 0 0 15px 0;">
                                            <span style="font-size: 20px; margin-right: 10px;">📋</span>Requisition
                                            Details
                                        </h3>

                                        <div style="padding: 12px 0; border-bottom: 1px solid rgba(33, 150, 243, 0.2);">
                                            <span
                                                style="font-weight: 600; color: #1565c0; min-width: 150px; font-size: 14px; display: inline-block;">Requisition
                                                No:</span>
                                            <span style="color: #0d47a1; font-size: 14px; font-weight: 500;">{{
                                                $requisition->no_srs }}</span>
                                        </div>

                                        <div style="padding: 12px 0; border-bottom: 1px solid rgba(33, 150, 243, 0.2);">
                                            <span
                                                style="font-weight: 600; color: #1565c0; min-width: 150px; font-size: 14px; display: inline-block;">Customer:</span>
                                            <span style="color: #0d47a1; font-size: 14px; font-weight: 500;">{{
                                                $requisition->customer->name ?? 'N/A' }}</span>
                                        </div>

                                        <div style="padding: 12px 0; border-bottom: 1px solid rgba(33, 150, 243, 0.2);">
                                            <span
                                                style="font-weight: 600; color: #1565c0; min-width: 150px; font-size: 14px; display: inline-block;">Request
                                                Date:</span>
                                            <span style="color: #0d47a1; font-size: 14px; font-weight: 500;">{{
                                                \Carbon\Carbon::parse($requisition->request_date)->format('d F Y')
                                                }}</span>
                                        </div>

                                        <div style="padding: 12px 0;">
                                            <span
                                                style="font-weight: 600; color: #1565c0; min-width: 150px; font-size: 14px; display: inline-block;">Current
                                                Status:</span>
                                            <span style="color: #dc3545; font-size: 14px; font-weight: 700;">Payment
                                                Proof
                                                Required</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse;">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 1px; padding: 0;">&nbsp;</td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse; background: #f8f9fa; background-image: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 10px; padding: 25px;"> <tr>
                                    <td style="padding: 0;">
                                <tr>
                                    <td style="padding: 0;">
                                        <h3
                                            style="color: #495057; font-size: 18px; font-weight: 600; margin: 0 0 15px 0;">
                                            <span style="font-size: 20px; margin-right: 10px;">✅</span>What You Need To
                                            Do
                                        </h3>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style="border-collapse: collapse;">

                                            <tr>
                                                <td width="30" valign="top" style="padding: 0 0 15px 0;">
                                                    <div
                                                        style="width: 25px; height: 25px; background: #007bff; /* Fallback */ background-image: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 50%; text-align: center; font-weight: 700; font-size: 14px; line-height: 25px;">
                                                        1</div>
                                                </td>
                                                <td valign="top"
                                                    style="padding: 0 0 15px 10px; color: #495057; font-size: 15px;">
                                                    Login to the Requisition Management System
                                                </td>
                                            </tr>

                                            <tr>
                                                <td width="30" valign="top" style="padding: 0 0 15px 0;">
                                                    <div
                                                        style="width: 25px; height: 25px; background: #007bff; background-image: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 50%; text-align: center; font-weight: 700; font-size: 14px; line-height: 25px;">
                                                        2</div>
                                                </td>
                                                <td valign="top"
                                                    style="padding: 0 0 15px 10px; color: #495057; font-size: 15px;">
                                                    Navigate to your requisition <strong>{{ $requisition->no_srs
                                                        }}</strong>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td width="30" valign="top" style="padding: 0 0 15px 0;">
                                                    <div
                                                        style="width: 25px; height: 25px; background: #007bff; background-image: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 50%; text-align: center; font-weight: 700; font-size: 14px; line-height: 25px;">
                                                        3</div>
                                                </td>
                                                <td valign="top"
                                                    style="padding: 0 0 15px 10px; color: #495057; font-size: 15px;">
                                                    Upload your payment proof document (PDF, JPG, or PNG format)
                                                </td>
                                            </tr>

                                            <tr>
                                                <td width="30" valign="top">
                                                    <div
                                                        style="width: 25px; height: 25px; background: #007bff; background-image: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 50%; text-align: center; font-weight: 700; font-size: 14px; line-height: 25px;">
                                                        4</div>
                                                </td>
                                                <td valign="top"
                                                    style="padding: 0 0 0 10px; color: #495057; font-size: 15px;">
                                                    Submit the document for review
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse;">
                                <tr>
                                    <td height="30" style="font-size: 1px; line-height: 1px; padding: 0;">&nbsp;</td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse; margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0"
                                            style="border-collapse: collapse;">
                                            <tr>
                                                <td align="center" bgcolor="#28a745"
                                                    style="border-radius: 4px; background: #28a745; /* Fallback */ background-image: linear-gradient(135deg, #28a745 0%, #218838 100%);">
                                                    <a href="{{ route('complain-form.index') }}" target="_blank"
                                                        style="display: inline-block; padding: 12px 25px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff !important; text-decoration: none; font-weight: bold; border: 1px solid #28a745; border-radius: 4px; mso-padding-alt: 12px 25px;">
                                                        Upload Payment Proof Now
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <div
                                style="height: 2px; background: #dee2e6; margin: 30px 0; max-width: 500px; width: 80%; margin-left: auto; margin-right: auto;">
                            </div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse; margin-top: 30px; /* Jarak Atas */ background: #f8f9fa; border-left: 4px solid #17a2b8; padding: 15px 20px; border-radius: 5px;">
                                <tr>
                                    <td style="padding: 0;">
                                        <p style="margin: 0; color: #495057; font-size: 14px;">
                                            <strong style="color: #0c5460;">📌 Important:</strong> Your requisition will
                                            remain on hold until the payment proof is provided and verified by our team.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#2c3e50"
                            style="background: #2c3e50; color: #ffffff; padding: 30px; border-radius: 0;">
                            <p class="company-name"
                                style="margin: 5px 0; font-size: 16px; color: #ffffff; font-weight: 700;">
                                {{ config('app.name') }}</p>
                            <p style="margin: 5px 0; font-size: 14px; color: #ecf0f1;">Requisition Management System</p>
                            <p style="margin-top: 15px; font-size: 12px; color: #95a5a6;">
                                This is an automated message. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
