<?php
include('db.php');
include('assets/include/sidebar.php');

// Redirect if no email is provided
if (!isset($_GET['email'])) {
    header("Location: admin_contact.php");
    exit();
}

$email = mysqli_real_escape_string($con, $_GET['email']);
$messages = mysqli_query($con, "SELECT * FROM contact_messages WHERE email='$email' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages - <?= htmlspecialchars($email) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f5f6f8;
    margin: 0;
}
.container {
    margin-left: 240px;
    padding: 40px 20px;
}
h2 {
    color: #0d6efd;
    margin-bottom: 30px;
}
.card {
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}
.message-block {
    background: #fff;
    border-left: 4px solid #0d6efd;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}
textarea.form-control {
    border-radius: 10px;
    resize: none;
}
.btn-success {
    border-radius: 50px;
    padding: 6px 16px;
    font-size: 0.9rem;
    transition: background 0.3s, transform 0.3s;
}
.btn-success:hover {
    background-color: #0b5ed7;
    transform: scale(1.05);
}
.back-btn {
    text-decoration: none;
    color: #0d6efd;
    font-weight: 500;
}
.back-btn:hover {
    text-decoration: underline;
}
@media (max-width: 992px) {
    .container {
        margin-left: 0;
        padding: 20px 15px;
    }
}
</style>
</head>
<body>

<div class="container">
    <a href="admin_contact.php" class="back-btn mb-3 d-inline-block">
        <i class="bi bi-arrow-left"></i> Back to Emails
    </a>
    <h2>📧 Messages from <?= htmlspecialchars($email) ?></h2>

    <div class="card p-3">
        <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
            <div class="message-block">
                <strong>Subject:</strong> <?= htmlspecialchars($msg['subject']) ?><br>
                <strong>Name:</strong> <?= htmlspecialchars($msg['name']) ?><br>
                <strong>Message:</strong> <?= nl2br(htmlspecialchars($msg['message'])) ?><br>
                <strong>Reply:</strong> <?= htmlspecialchars($msg['admin_reply'] ?? "Not replied yet") ?><br>
                <small>Link: <a href="view_reply.php?token=<?= $msg['token'] ?>">View Reply</a></small>

                <?php if ($msg['status'] == 'pending'): ?>
                    <form action="reply_contact.php" method="POST" class="mt-2">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                        <textarea name="reply" class="form-control mb-2" placeholder="Write reply..." required></textarea>
                        <button type="submit" class="btn btn-success btn-sm">Send Reply</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
