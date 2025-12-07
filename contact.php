<?php
include('db.php'); // Database connection

$msg = '';
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);
    $token = bin2hex(random_bytes(16)); // unique token

    $query = "INSERT INTO contact_messages (name,email,subject,message,token)
              VALUES ('$name','$email','$subject','$message','$token')";
    if (mysqli_query($con, $query)) {
        $msg = "✅ Message sent successfully! You can check your reply using this link:<br>
                <a href='view_reply.php?token=$token'>View Your Reply</a>";
    } else {
        $msg = "❌ Error sending message!";
    }
}

// Fetch all contact messages
$messages = mysqli_query($con, "SELECT * FROM contact_messages ORDER BY created_at DESC");

// Helper function to mask email
function maskEmail($email) {
    $parts = explode('@', $email);
    $name = substr($parts[0], 0, 2) . str_repeat('*', max(strlen($parts[0]) - 2, 0));
    return $name . '@' . $parts[1];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f8f9fa;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
}
.contact-container {
    max-width: 600px;
    margin: 60px auto;
    padding: 25px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.contact-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #0d6efd;
}
.alert a {
    text-decoration: underline;
}

/* Message List Section */
.message-list {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    padding: 25px;
}
.message-item {
    background: #f9f9f9;
    border-left: 4px solid #0d6efd;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}
.message-item strong {
    color: #0d6efd;
}
.message-item a {
    text-decoration: none;
    color: #6610f2;
}
.message-item a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<!-- Navbar -->
<?php include('assets/include/navbar.php'); ?>

<div class="contact-container">
    <h2>Contact Us</h2>
    <?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
        </div>
        <div class="mb-3">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="Subject" required>
        </div>
        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Type your message..." required></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn-primary w-100">Send Message</button>
    </form>
</div>

<!-- All Sent Messages -->
<div class="message-list">
    <h3 class="text-center text-primary mb-4">📬 All Messages</h3>
    <?php if (mysqli_num_rows($messages) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($messages)): ?>
            <div class="message-item">
                <strong></strong> <?= htmlspecialchars($row['name']) ?><br>
                <strong>Subject:</strong> <?= htmlspecialchars($row['subject']) ?><br>
                <strong>Message:</strong> <?= nl2br(htmlspecialchars($row['message'])) ?><br>
                <strong>Reply:</strong>
                <?php if (!empty($row['admin_reply'])): ?>
                    <span><?= nl2br(htmlspecialchars($row['admin_reply'])) ?></span>
                <?php else: ?>
                    <em>Not replied yet</em>
                <?php endif; ?><br>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center text-muted">No messages found.</p>
    <?php endif; ?>
</div>

<?php include('assets/include/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
