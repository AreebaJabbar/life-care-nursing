<?php
/**
 * LifeCare Nursing & Medical Services - Contact Us Email Handler
 * Receives form requests and dispatches emails to lifecarenursing5@gmail.com
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Extract & sanitize inputs
$name    = isset($_POST['name']) ? trim(filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS)) : '';
$phone   = isset($_POST['phone']) ? trim(filter_var($_POST['phone'], FILTER_SANITIZE_SPECIAL_CHARS)) : '';
$email   = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$service = isset($_POST['service']) ? trim(filter_var($_POST['service'], FILTER_SANITIZE_SPECIAL_CHARS)) : 'General Inquiry';
$message = isset($_POST['message']) ? trim(filter_var($_POST['message'], FILTER_SANITIZE_SPECIAL_CHARS)) : '';

// Validation
if (empty($name) || empty($phone) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Phone, Email, Message).']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Recipient email address
$to = CONTACT_EMAIL; // lifecarenursing5@gmail.com
$subject = "New Website Contact Form Submission: " . $name . " (" . $service . ")";

// HTML Email Body
$htmlBody = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background: #03357A; color: #ffffff; padding: 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; }
        .content { padding: 25px; line-height: 1.6; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 10px; border-bottom: 1px solid #eeeeee; }
        .info-table td.label { font-weight: bold; color: #029491; width: 30%; }
        .message-box { background: #FAF8F3; border-left: 4px solid #029491; padding: 15px; border-radius: 4px; font-size: 15px; }
        .footer { background: #E3EEEC; color: #5C6A66; text-align: center; padding: 15px; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>" . SITE_NAME . "</h2>
            <p style='margin:5px 0 0 0; font-size:14px; opacity:0.9;'>New Inquiry Received via Website Contact Form</p>
        </div>
        <div class='content'>
            <table class='info-table'>
                <tr>
                    <td class='label'>Sender Name:</td>
                    <td>" . htmlspecialchars($name) . "</td>
                </tr>
                <tr>
                    <td class='label'>Phone / WhatsApp:</td>
                    <td><a href='tel:" . htmlspecialchars($phone) . "' style='color:#03357A; text-decoration:none; font-weight:bold;'>" . htmlspecialchars($phone) . "</a></td>
                </tr>
                <tr>
                    <td class='label'>Email Address:</td>
                    <td><a href='mailto:" . htmlspecialchars($email) . "' style='color:#03357A; text-decoration:none;'>" . htmlspecialchars($email) . "</a></td>
                </tr>
                <tr>
                    <td class='label'>Selected Service:</td>
                    <td><strong>" . htmlspecialchars($service) . "</strong></td>
                </tr>
            </table>

            <p style='margin-bottom:8px; font-weight:bold; color:#03357A;'>Patient / Request Details:</p>
            <div class='message-box'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
        </div>
        <div class='footer'>
            This email was sent automatically from your LifeCare Nursing website contact form.<br>
            Sent on: " . date('d M Y, h:i A') . "
        </div>
    </div>
</body>
</html>
";

// Headers
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: LifeCare Website <noreply@" . ($_SERVER['HTTP_HOST'] ?? 'lifecarenursing.com') . ">" . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// Log submission locally into data/contact_messages.json as backup
$messagesLog = getData('contact_messages');
$newMessage = [
    'id' => time(),
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'service' => $service,
    'message' => $message,
    'date' => date('Y-m-d H:i:s')
];
array_unshift($messagesLog, $newMessage);
saveData('contact_messages', $messagesLog);

// Attempt mail dispatch
$mailSent = @mail($to, $subject, $htmlBody, $headers);

if ($mailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully to lifecarenursing5@gmail.com. We will contact you shortly.'
    ]);
} else {
    // Return success since we saved to database backup, noting mail dispatch notice
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been received. Our team will contact you shortly.'
    ]);
}
