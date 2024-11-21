<?php
// Sender's email and name
$from = "admin@example.com";
$from_name = "Your Name";

// Receiver's email
$to = "mark@example.com";

// Subject of the email
$subject = "Here is the image you requested";

// Message body (text part)
$message = "Hello, please find the attached image.";

// Image file to attach
$file = "qr_image/66f4cf18a67bf.PNG"; // Path to the image file

// Read the file content
$file_size = filesize($file);
$handle = fopen($file, "r");
$content = fread($handle, $file_size);
fclose($handle);

// Encode the content in base64
$content = chunk_split(base64_encode($content));

// Generate a boundary string
$boundary = md5(time());

// Headers for the email
$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: $from_name <$from>\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

// Multipart message body
$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $message . "\r\n";
$body .= "--$boundary\r\n";
$body .= "Content-Type: image/jpeg; name=\"image.jpg\"\r\n"; // Change to your image MIME type
$body .= "Content-Disposition: attachment; filename=\"image.jpg\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n";
$body .= $content . "\r\n";
$body .= "--$boundary--";

// Send the email
if (mail($to, $subject, $body, $headers)) {
    echo "Email sent successfully with the image.";
} else {
    echo "Failed to send the email.";
}
?>
