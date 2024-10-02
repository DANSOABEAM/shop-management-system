<?php
// Start session
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'shop_managemen';
$user = 'root';
$pass = '';

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle support form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_support'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if (!empty($name) && !empty($email) && !empty($message)) {
        // Save support message to the database
        $stmt = $conn->prepare("INSERT INTO support_messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            $success = "Your message has been sent. We will get back to you soon!";
        } else {
            $error = "Failed to send your message. Please try again.";
        }
    } else {
        $error = "All fields are required!";
    }
}

// FAQs (these can also be stored in a database)
$faqs = [
    [
        "question" => "How do I reset my password?",
        "answer" => "To reset your password, go to your profile page, click 'Change Password' and follow the instructions."
    ],
    [
        "question" => "How do I update my profile information?",
        "answer" => "Go to the profile page and click 'Edit Profile' to update your personal information."
    ],
    [
        "question" => "Who do I contact for support?",
        "answer" => "You can use the contact form on this page to send us a message or email us at support@shopmanagement.com."
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .faq-section, .contact-section, .guides-section {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        h3 {
            margin-top: 0;
        }
        .faq {
            margin-bottom: 15px;
        }
        .faq strong {
            display: block;
            margin-bottom: 5px;
        }
        .contact-section input, .contact-section textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .contact-section button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Help & Support</h2>

    <!-- FAQs Section -->
    <div class="faq-section">
        <h3>Frequently Asked Questions</h3>
        <?php foreach ($faqs as $faq): ?>
            <div class="faq">
                <strong><?php echo $faq['question']; ?></strong>
                <p><?php echo $faq['answer']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Contact Support Section -->
    <div class="contact-section">
        <h3>Contact Support</h3>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            <button type="submit" name="contact_support">Send Message</button>
        </form>
    </div>

    <!-- User Guides Section -->
    <div class="guides-section">
        <h3>User Guides</h3>
        <ul>
            <li><a href="guide_password_reset.pdf" target="_blank">How to Reset Your Password</a></li>
            <li><a href="guide_update_profile.pdf" target="_blank">How to Update Your Profile</a></li>
            <li><a href="guide_contact_support.pdf" target="_blank">How to Contact Support</a></li>
        </ul>
    </div>
</div>

</body>
</html>
