<?php
include('db.php');
include('assets/include/sidebar.php');

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']); // sanitize input
    $res = mysqli_query($con, "SELECT * FROM contact_messages WHERE token='$token'");

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>View Reply</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background-color: #f5f6f8;
                    margin: 0;
                }
                .container {
                    margin-left: 240px; /* Sidebar offset */
                    padding: 40px 20px;
                }
                .reply-card {
                    max-width: 700px;
                    margin: 0 auto;
                    background: #fff;
                    padding: 30px;
                    border-radius: 15px;
                    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
                }
                h3 {
                    color: #0d6efd;
                    font-weight: 600;
                    margin-bottom: 25px;
                    border-bottom: 2px solid #e9ecef;
                    padding-bottom: 10px;
                }
                .message, .admin-reply {
                    background: #f8f9fa;
                    padding: 15px;
                    border-left: 4px solid #0d6efd;
                    border-radius: 10px;
                    margin-bottom: 20px;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
                }
                .admin-reply {
                    border-left-color: #6610f2;
                    background: #f3f0ff;
                }
                .status {
                    font-style: italic;
                    color: #888;
                }
                .btn-back {
                    background-color: #0d6efd;
                    color: #fff;
                    border-radius: 50px;
                    padding: 8px 20px;
                    text-decoration: none;
                    transition: 0.3s;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }
                .btn-back:hover {
                    background-color: #6610f2;
                    color: #fff;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="reply-card">
                    <h3><i class="bi bi-envelope-paper"></i> Subject: <?= htmlspecialchars($row['subject']) ?></h3>

                    <div class="message">
                        <strong>📩 User Message:</strong>
                        <p class="mt-1"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                    </div>

                    <?php if ($row['status'] == 'replied' && !empty($row['admin_reply'])): ?>
                        <div class="admin-reply">
                            <strong>💬 Admin Reply:</strong>
                            <p class="mt-1"><?= nl2br(htmlspecialchars($row['admin_reply'])) ?></p>
                        </div>
                    <?php else: ?>
                        <p class="status">⏳ Admin has not replied yet.</p>
                    <?php endif; ?>

                    <a href="admin_contact.php" class="btn-back mt-3"><i class="bi bi-arrow-left"></i> Back to Messages</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<div class='alert alert-danger text-center mt-5'>Invalid link!</div>";
    }
} else {
    echo "<div class='alert alert-warning text-center mt-5'>No token provided!</div>";
}
?>
