<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "ubaidsaroyawor@gmail.com";
    $subject = "New Contact Form Submission";

    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);

    $body = "Name: $name\n";
    $body .= "Email: $email\n\n";
    $body .= "Message:\n$message\n";

    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo "<h2>Thank you! Your message has been sent.</h2>";
    } else {
        echo "<h2>Sorry, there was a problem sending your message.</h2>";
    }
}
?>
